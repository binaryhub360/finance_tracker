<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Invoice::where('user_id', $user->id)
            ->with(['client', 'items']);

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'overdue') {
                $query->whereNotIn('status', ['paid', 'cancelled'])
                    ->where('due_date', '<', Carbon::today());
            } else {
                $query->where('status', $status);
            }
        }

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('issue_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('issue_date', '<=', $request->date_to);
        }

        // Search by invoice number or client name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('company_name', 'like', "%{$search}%");
                    });
            });
        }

        $invoices = $query->latest('issue_date')->latest('id')->paginate(15)->withQueryString();

        // Calculate KPI summaries
        $allInvoices = Invoice::where('user_id', $user->id)->where('status', '!=', 'cancelled')->get();
        $totalInvoiced = (float) $allInvoices->sum('total');
        $totalPaid = (float) $allInvoices->sum('paid_amount');
        $totalOutstanding = max(0, $totalInvoiced - $totalPaid);

        $overdueCount = 0;
        $overdueAmount = 0;
        foreach ($allInvoices as $inv) {
            if ($inv->is_overdue && $inv->status !== 'paid') {
                $overdueCount++;
                $overdueAmount += $inv->balance_due;
            }
        }

        $clients = Client::where('user_id', $user->id)->active()->orderBy('name')->get();

        return view('invoices.index', compact(
            'invoices',
            'clients',
            'totalInvoiced',
            'totalPaid',
            'totalOutstanding',
            'overdueCount',
            'overdueAmount'
        ));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        $clients = Client::where('user_id', $user->id)->active()->orderBy('name')->get();
        $nextInvoiceNumber = Invoice::generateNextInvoiceNumber($user->id);
        $currency = currency_symbol();

        return view('invoices.create', compact('clients', 'nextInvoiceNumber', 'currency'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_number' => 'required|string|max:50|unique:invoices,invoice_number,NULL,id,user_id,' . $user->id,
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'status' => 'required|in:draft,sent',
            'currency' => 'nullable|string|max:10',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'terms' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $invoice = DB::transaction(function () use ($user, $validated) {
            $invoice = Invoice::create([
                'user_id' => $user->id,
                'client_id' => $validated['client_id'],
                'invoice_number' => $validated['invoice_number'],
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'status' => $validated['status'],
                'currency' => $validated['currency'] ?? currency_symbol(),
                'tax_rate' => $validated['tax_rate'] ?? 0,
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
            ]);

            foreach ($validated['items'] as $itemData) {
                $qty = (float)$itemData['quantity'];
                $price = (float)$itemData['unit_price'];
                $total = round($qty * $price, 2);

                $invoice->items()->create([
                    'description' => $itemData['description'],
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'total' => $total,
                ]);
            }

            $invoice->recalculateTotals();

            return $invoice;
        });

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice created successfully.');
    }

    public function show(Request $request, Invoice $invoice): View
    {
        abort_if($invoice->user_id !== $request->user()->id, 403);

        $invoice->load(['client', 'items', 'payments.account', 'payments.transaction']);
        $accounts = Account::where('user_id', $request->user()->id)->active()->ordered()->get();

        return view('invoices.show', compact('invoice', 'accounts'));
    }

    public function edit(Request $request, Invoice $invoice): View
    {
        abort_if($invoice->user_id !== $request->user()->id, 403);

        if ($invoice->status === 'paid') {
            return back()->with('error', 'Cannot edit a fully paid invoice.');
        }

        $user = $request->user();
        $clients = Client::where('user_id', $user->id)->active()->orderBy('name')->get();
        $invoice->load('items');

        return view('invoices.edit', compact('invoice', 'clients'));
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        abort_if($invoice->user_id !== $request->user()->id, 403);

        $user = $request->user();

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_number' => 'required|string|max:50|unique:invoices,invoice_number,' . $invoice->id . ',id,user_id,' . $user->id,
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'status' => 'required|in:draft,sent,cancelled',
            'currency' => 'nullable|string|max:10',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'terms' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($invoice, $validated) {
            $invoice->update([
                'client_id' => $validated['client_id'],
                'invoice_number' => $validated['invoice_number'],
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'status' => $validated['status'],
                'currency' => $validated['currency'] ?? $invoice->currency,
                'tax_rate' => $validated['tax_rate'] ?? 0,
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
            ]);

            // Replace items
            $invoice->items()->delete();
            foreach ($validated['items'] as $itemData) {
                $qty = (float)$itemData['quantity'];
                $price = (float)$itemData['unit_price'];
                $total = round($qty * $price, 2);

                $invoice->items()->create([
                    'description' => $itemData['description'],
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'total' => $total,
                ]);
            }

            $invoice->recalculateTotals();
        });

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Request $request, Invoice $invoice): RedirectResponse
    {
        abort_if($invoice->user_id !== $request->user()->id, 403);

        DB::transaction(function () use ($invoice) {
            // Delete linked transactions for payments
            foreach ($invoice->payments as $payment) {
                if ($payment->transaction) {
                    $payment->transaction->delete();
                }
            }
            $invoice->delete();
        });

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    public function markSent(Request $request, Invoice $invoice): RedirectResponse
    {
        abort_if($invoice->user_id !== $request->user()->id, 403);

        if ($invoice->status === 'draft') {
            $invoice->update(['status' => 'sent']);
        }

        return back()->with('success', 'Invoice marked as Sent.');
    }

    public function print(Request $request, Invoice $invoice): View
    {
        abort_if($invoice->user_id !== $request->user()->id, 403);

        $invoice->load(['client', 'items', 'payments.account', 'user']);

        return view('invoices.print', compact('invoice'));
    }
}

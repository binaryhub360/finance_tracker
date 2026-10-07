<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\IncomeCategory;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoicePaymentController extends Controller
{
    public function store(Request $request, Invoice $invoice): RedirectResponse
    {
        $user = $request->user();
        abort_if($invoice->user_id !== $user->id, 403);

        $maxAmount = $invoice->balance_due;
        if ($maxAmount <= 0) {
            return back()->with('error', 'This invoice is already fully paid.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $maxAmount,
            'account_id' => 'required|exists:accounts,id',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:50',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        // Verify account belongs to user
        $account = Account::where('user_id', $user->id)->findOrFail($validated['account_id']);

        DB::transaction(function () use ($user, $invoice, $validated) {
            // Find or create an Invoicing income category
            $incomeCategory = IncomeCategory::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'name' => 'Invoice Payments',
                ],
                [
                    'description' => 'Automated revenue logged from client invoices',
                    'is_active' => true,
                ]
            );

            // 1. Create linked Income Transaction
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'type' => 'income',
                'date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'account_id' => $validated['account_id'],
                'income_category_id' => $incomeCategory->id,
                'description' => "Invoice {$invoice->invoice_number} payment from {$invoice->client->name}",
                'reference' => $validated['reference'] ?: $invoice->invoice_number,
            ]);

            // 2. Create Invoice Payment record
            $invoice->payments()->create([
                'account_id' => $validated['account_id'],
                'transaction_id' => $transaction->id,
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'reference' => $validated['reference'],
                'notes' => $validated['notes'],
            ]);

            // 3. Recalculate invoice totals and status
            $invoice->recalculateTotals();
        });

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Payment recorded successfully and reconciled into account ledger.');
    }

    public function destroy(Request $request, Invoice $invoice, InvoicePayment $payment): RedirectResponse
    {
        $user = $request->user();
        abort_if($invoice->user_id !== $user->id, 403);
        abort_if($payment->invoice_id !== $invoice->id, 404);

        DB::transaction(function () use ($invoice, $payment) {
            // Delete linked income transaction
            if ($payment->transaction) {
                $payment->transaction->delete();
            }

            $payment->delete();

            $invoice->recalculateTotals();
        });

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Payment record deleted and transaction reversed.');
    }
}

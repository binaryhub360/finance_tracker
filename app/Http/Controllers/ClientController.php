<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Client::where('user_id', $user->id)
            ->withCount('invoices');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $clients = $query->orderBy('name')->paginate(15)->withQueryString();

        // Calculate overview KPIs
        $totalClients = Client::where('user_id', $user->id)->count();
        $activeClients = Client::where('user_id', $user->id)->where('is_active', true)->count();

        return view('clients.index', compact('clients', 'totalClients', 'activeClients'));
    }

    public function create(): View
    {
        return view('clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'company_name' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'tax_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['is_active'] = $request->boolean('is_active', true);

        Client::create($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Client created successfully.');
    }

    public function show(Request $request, Client $client): View
    {
        abort_if($client->user_id !== $request->user()->id, 403);

        $invoices = $client->invoices()
            ->latest('issue_date')
            ->paginate(10);

        return view('clients.show', compact('client', 'invoices'));
    }

    public function edit(Request $request, Client $client): View
    {
        abort_if($client->user_id !== $request->user()->id, 403);

        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        abort_if($client->user_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'company_name' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'tax_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Client updated successfully.');
    }

    public function destroy(Request $request, Client $client): RedirectResponse
    {
        abort_if($client->user_id !== $request->user()->id, 403);

        if ($client->invoices()->exists()) {
            return back()->with('error', 'Cannot delete client with existing invoices.');
        }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    public function toggle(Request $request, Client $client): RedirectResponse
    {
        abort_if($client->user_id !== $request->user()->id, 403);

        $client->update(['is_active' => !$client->is_active]);

        return back()->with('success', 'Client status updated.');
    }
}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }} - {{ $invoice->client->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #0f172a;
            background-color: #f8fafc;
            line-height: 1.5;
            font-size: 13px;
            -webkit-font-smoothing: antialiased;
        }
        .mono {
            font-family: 'JetBrains Mono', monospace;
            font-feature-settings: "tnum";
            font-variant-numeric: tabular-nums;
        }
        .container {
            max-width: 820px;
            margin: 24px auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .action-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 24px;
            background: #0f172a;
            color: #ffffff;
        }
        .action-bar a, .action-bar button {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #10b981;
            color: #ffffff;
            font-weight: 600;
            font-size: 12px;
            padding: 8px 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.15s ease;
        }
        .action-bar button:hover {
            background: #059669;
        }
        .action-bar .close-btn {
            background: rgba(255, 255, 255, 0.1);
            color: #94a3b8;
        }
        .action-bar .close-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }
        .invoice-body {
            padding: 48px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 32px;
            border-bottom: 2px solid #f1f5f9;
        }
        .brand-logo {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .brand-logo span {
            color: #10b981;
        }
        .invoice-title {
            text-align: right;
        }
        .invoice-title h1 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -1px;
            color: #0f172a;
            text-transform: uppercase;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            padding: 32px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .meta-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            margin-bottom: 6px;
        }
        .meta-value-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .meta-text {
            color: #475569;
            font-size: 12px;
            line-height: 1.6;
        }
        .invoice-meta-table {
            margin-left: auto;
            text-align: right;
        }
        .invoice-meta-table td {
            padding: 3px 0;
            font-size: 12px;
        }
        .invoice-meta-table td:first-child {
            color: #64748b;
            padding-right: 16px;
        }
        .invoice-meta-table td:last-child {
            font-weight: 600;
            color: #0f172a;
        }
        .status-pill {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-paid { background: #dcfce7; color: #15803d; }
        .status-sent { background: #e0f2fe; color: #0369a1; }
        .status-partially_paid { background: #fef3c7; color: #b45309; }
        .status-draft { background: #f1f5f9; color: #475569; }
        .status-overdue { background: #fee2e2; color: #b91c1c; }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 24px;
        }
        .items-table th {
            text-align: left;
            padding: 12px 10px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }
        .items-table td {
            padding: 14px 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 12px;
            color: #1e293b;
        }
        .items-table .text-center { text-align: center; }
        .items-table .text-right { text-align: right; }

        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }
        .totals-table {
            width: 280px;
        }
        .totals-table td {
            padding: 5px 0;
            font-size: 12px;
        }
        .totals-table td:first-child {
            color: #64748b;
        }
        .totals-table td:last-child {
            text-align: right;
            font-weight: 600;
            color: #0f172a;
        }
        .grand-total td {
            padding-top: 10px;
            border-top: 2px solid #0f172a;
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
        }
        .balance-due td {
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            font-weight: 700;
            color: #059669;
        }

        .notes-grid {
            margin-top: 36px;
            padding-top: 24px;
            border-top: 1px solid #f1f5f9;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }
        .footer-note {
            margin-top: 48px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            border-top: 1px dashed #e2e8f0;
            padding-top: 20px;
        }

        @media print {
            body {
                background: #ffffff;
                color: #000000;
            }
            .no-print {
                display: none !important;
            }
            .container {
                max-width: 100%;
                margin: 0;
                box-shadow: none;
                border: none;
                border-radius: 0;
            }
            .invoice-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Interactive Print Topbar --}}
        <div class="action-bar no-print">
            <span style="font-size: 12px; font-weight: 500; color: #cbd5e1;">
                Invoice {{ $invoice->invoice_number }} &bull; Print Preview
            </span>
            <div style="display: flex; gap: 8px;">
                <button type="button" onclick="window.print()">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print / Save PDF
                </button>
                <a href="{{ route('invoices.show', $invoice) }}" class="close-btn">Close Preview</a>
            </div>
        </div>

        <div class="invoice-body">
            {{-- Header --}}
            <div class="header">
                <div>
                    <div class="brand-logo">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="24" height="24" rx="6" fill="#10b981"/>
                            <path d="M12 6V18M6 12L12 6L18 12" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Finance<span>Hub</span>
                    </div>
                    <p style="color: #64748b; font-size: 11px; margin-top: 4px;">
                        {{ $invoice->user ? $invoice->user->name : 'Merchant' }}
                    </p>
                </div>
                <div class="invoice-title">
                    <h1>INVOICE</h1>
                    <p class="mono" style="font-weight: 700; color: #64748b; font-size: 13px;">{{ $invoice->invoice_number }}</p>
                    <div style="margin-top: 6px;">
                        <span class="status-pill status-{{ $invoice->is_overdue && $invoice->status !== 'paid' ? 'overdue' : $invoice->status }}">
                            {{ $invoice->is_overdue && $invoice->status !== 'paid' ? 'Overdue' : ucfirst(str_replace('_', ' ', $invoice->status)) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Meta Grid --}}
            <div class="meta-grid">
                <div>
                    <div class="meta-label">Billed To</div>
                    <div class="meta-value-title">{{ $invoice->client->name }}</div>
                    @if($invoice->client->company_name)
                        <div class="meta-text" style="font-weight: 600;">{{ $invoice->client->company_name }}</div>
                    @endif
                    @if($invoice->client->email)
                        <div class="meta-text">{{ $invoice->client->email }}</div>
                    @endif
                    @if($invoice->client->phone)
                        <div class="meta-text">{{ $invoice->client->phone }}</div>
                    @endif
                    @if($invoice->client->address)
                        <div class="meta-text" style="white-space: pre-line; margin-top: 4px;">{{ $invoice->client->address }}</div>
                    @endif
                </div>

                <div>
                    <table class="invoice-meta-table">
                        <tr>
                            <td>Invoice Date:</td>
                            <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <td>Payment Due:</td>
                            <td style="{{ $invoice->is_overdue && $invoice->status !== 'paid' ? 'color: #dc2626;' : '' }}">
                                {{ $invoice->due_date->format('M d, Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td>Currency:</td>
                            <td class="mono">{{ $invoice->currency }}</td>
                        </tr>
                        <tr>
                            <td>Payment Status:</td>
                            <td>{{ $invoice->balance_due <= 0 ? 'Fully Paid' : 'Pending Payment' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Items Table --}}
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Item / Description</th>
                        <th class="text-center" style="width: 15%;">Qty</th>
                        <th class="text-right" style="width: 17%;">Unit Price</th>
                        <th class="text-right" style="width: 18%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->description }}</strong>
                            </td>
                            <td class="text-center mono">{{ (float)$item->quantity }}</td>
                            <td class="text-right mono">{{ $invoice->currency }} {{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-right mono font-bold"><strong>{{ $invoice->currency }} {{ number_format($item->total, 2) }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Calculations --}}
            <div class="totals-section">
                <table class="totals-table">
                    <tr>
                        <td>Subtotal:</td>
                        <td class="mono">{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                    @if($invoice->tax_rate > 0)
                        <tr>
                            <td>Tax ({{ (float)$invoice->tax_rate }}%):</td>
                            <td class="mono">+{{ $invoice->currency }} {{ number_format($invoice->tax_amount, 2) }}</td>
                        </tr>
                    @endif
                    @if($invoice->discount_amount > 0)
                        <tr>
                            <td>Discount:</td>
                            <td class="mono">-{{ $invoice->currency }} {{ number_format($invoice->discount_amount, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="grand-total">
                        <td>Total Due:</td>
                        <td class="mono">{{ $invoice->currency }} {{ number_format($invoice->total_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Paid to Date:</td>
                        <td class="mono">{{ $invoice->currency }} {{ number_format($invoice->paid_amount, 2) }}</td>
                    </tr>
                    <tr class="balance-due">
                        <td>Balance Due:</td>
                        <td class="mono" style="{{ $invoice->balance_due > 0 ? 'color: #d97706;' : 'color: #059669;' }}">
                            {{ $invoice->currency }} {{ number_format($invoice->balance_due, 2) }}
                        </td>
                    </tr>
                </table>
            </div>

            {{-- Notes & Terms --}}
            @if($invoice->notes || $invoice->terms)
                <div class="notes-grid">
                    @if($invoice->notes)
                        <div>
                            <div class="meta-label">Notes</div>
                            <div class="meta-text" style="white-space: pre-line;">{{ $invoice->notes }}</div>
                        </div>
                    @endif
                    @if($invoice->terms)
                        <div>
                            <div class="meta-label">Payment Terms & Instructions</div>
                            <div class="meta-text" style="white-space: pre-line;">{{ $invoice->terms }}</div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Footer Note --}}
            <div class="footer-note">
                Thank you for your business! If you have questions about this invoice, please reach out directly.
            </div>
        </div>
    </div>
</body>
</html>

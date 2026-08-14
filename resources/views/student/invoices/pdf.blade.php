<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        /* dompdf has limited CSS support — keep this simple and self-contained,
           no Bootstrap/Vite here, unlike the rest of the app. */
        body { font-family: DejaVu Sans, sans-serif; color: #16213A; font-size: 13px; }
        .header { display: table; width: 100%; margin-bottom: 30px; }
        .header .logo-cell { display: table-cell; width: 50%; vertical-align: top; }
        .header .meta-cell { display: table-cell; width: 50%; vertical-align: top; text-align: right; }
        h1 { color: #082159; font-size: 20px; margin: 0 0 4px; }
        .muted { color: #6B7590; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #EEF1F8; text-align: left; padding: 8px; font-size: 12px; text-transform: uppercase; color: #47516E; }
        td { padding: 8px; border-bottom: 1px solid #DDE3F0; }
        .text-end { text-align: right; }
        .totals td { border-bottom: none; font-weight: bold; }
        .balance { color: #C0392B; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-cell">
            <h1>Altura Workforce Solutions</h1>
            <div class="muted">Utalii House, 3rd Floor, Rm No. 321<br>Nairobi, Kenya</div>
        </div>
        <div class="meta-cell">
            <h1>INVOICE</h1>
            <div class="muted">{{ $invoice->invoice_number }}</div>
            <div class="muted">Due: {{ $invoice->due_date?->format('d M Y') ?? '—' }}</div>
        </div>
    </div>

    <div>
        <strong>Billed To:</strong><br>
        {{ $invoice->student->name }}<br>
        {{ $invoice->student->email }}
    </div>

    <table>
        <thead>
            <tr><th>Description</th><th class="text-end">Qty</th><th class="text-end">Unit Price</th><th class="text-end">Total</th></tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="text-end">{{ $item->quantity }}</td>
                    <td class="text-end">{{ $invoice->currency }} {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end">{{ $invoice->currency }} {{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="totals">
            <tr><td colspan="3" class="text-end">Total</td><td class="text-end">{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</td></tr>
            <tr><td colspan="3" class="text-end">Paid</td><td class="text-end">{{ $invoice->currency }} {{ number_format($invoice->amount_paid, 2) }}</td></tr>
            <tr><td colspan="3" class="text-end">Balance</td><td class="text-end balance">{{ $invoice->currency }} {{ number_format($invoice->balance, 2) }}</td></tr>
        </tfoot>
    </table>
</body>
</html>

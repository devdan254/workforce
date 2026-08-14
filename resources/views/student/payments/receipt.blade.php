<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #16213A; font-size: 13px; }
        h1 { color: #082159; font-size: 20px; margin: 0 0 4px; }
        .muted { color: #6B7590; }
        .box { border: 1px solid #DDE3F0; border-radius: 8px; padding: 20px; margin-top: 24px; }
        .row { display: table; width: 100%; padding: 8px 0; border-bottom: 1px solid #EEF1F8; }
        .row .label { display: table-cell; width: 50%; color: #6B7590; }
        .row .value { display: table-cell; width: 50%; text-align: right; font-weight: bold; }
        .amount { font-size: 24px; color: #1E8E5A; font-weight: bold; text-align: center; margin: 20px 0; }
        .stamp { text-align: center; color: #1E8E5A; font-weight: bold; margin-top: 20px; border: 2px solid #1E8E5A; padding: 6px; display: inline-block; }
        .center { text-align: center; }
    </style>
</head>
<body>
    <h1>Altura Workforce Solutions</h1>
    <div class="muted">Payment Receipt</div>

    <div class="box">
        <div class="center">
            <div class="muted">Amount Paid</div>
            <div class="amount">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</div>
        </div>

        <div class="row"><div class="label">Receipt No.</div><div class="value">RCPT-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</div></div>
        <div class="row"><div class="label">Paid By</div><div class="value">{{ $payment->student->name }}</div></div>
        <div class="row"><div class="label">Invoice</div><div class="value">{{ $payment->invoice->invoice_number }}</div></div>
        <div class="row"><div class="label">Method</div><div class="value">{{ ucfirst($payment->method) }}</div></div>
        <div class="row"><div class="label">Date Paid</div><div class="value">{{ $payment->paid_at?->format('d F Y') }}</div></div>
        <div class="row"><div class="label">Confirmed By</div><div class="value">{{ $payment->confirmedBy?->name ?? '—' }}</div></div>

        <div class="center">
            <div class="stamp">✓ CONFIRMED</div>
        </div>
    </div>

    <p class="muted center" style="margin-top: 30px;">
        Utalii House, 3rd Floor, Rm No. 321, Nairobi, Kenya · alturaworkforcesolutions@gmail.com
    </p>
</body>
</html>

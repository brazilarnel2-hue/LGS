<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Receipt — Order #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            max-width: 400px;
            margin: 40px auto;
            color: #222;
            font-size: 14px;
        }
        .center { text-align: center; }
        h1 { font-size: 20px; margin-bottom: 0; }
        .subtitle { color: #666; font-size: 12px; margin-top: 4px; }
        hr { border: none; border-top: 1px dashed #999; margin: 16px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 4px 0; vertical-align: top; }
        .label { color: #666; }
        .right { text-align: right; }
        .total-row td { font-weight: bold; font-size: 16px; border-top: 1px solid #333; padding-top: 8px; }
        .paid-stamp {
            display: inline-block;
            border: 3px solid #16a34a;
            color: #16a34a;
            font-weight: bold;
            padding: 6px 16px;
            border-radius: 6px;
            transform: rotate(-6deg);
            margin: 16px 0;
            font-size: 18px;
        }
        .unpaid-stamp {
            display: inline-block;
            border: 3px solid #dc2626;
            color: #dc2626;
            font-weight: bold;
            padding: 6px 16px;
            border-radius: 6px;
            transform: rotate(-6deg);
            margin: 16px 0;
            font-size: 18px;
        }
        .print-btn {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
        }
        @media print {
            .print-btn { display: none; }
        }
    </style>
</head>
<body>

    <div class="center">
        <h1>LaundryGo</h1>
        <p class="subtitle">Pickup & Delivery Receipt</p>
    </div>

    <hr>

    <table>
        <tr>
            <td class="label">Order #</td>
            <td class="right">{{ $order->id }}</td>
        </tr>
        <tr>
            <td class="label">Customer</td>
            <td class="right">{{ $order->customer->name }}</td>
        </tr>
        <tr>
            <td class="label">Date</td>
            <td class="right">{{ $order->created_at->format('M d, Y h:i A') }}</td>
        </tr>
    </table>

    <hr>

    <table>
        @foreach ($order->items as $item)
            <tr>
                <td>{{ $item->service->name }}<br>
                    <span class="label">{{ $item->quantity }} {{ $item->service->unit }}</span>
                </td>
                <td class="right">₱{{ number_format($item->subtotal, 2) }}</td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td>TOTAL</td>
            <td class="right">₱{{ number_format($order->total_amount, 2) }}</td>
        </tr>
    </table>

    <hr>

    <div class="center">
        @if ($order->payment && $order->payment->status === 'paid')
            <div class="paid-stamp">✓ PAID</div>
            <table>
                <tr>
                    <td class="label">Method</td>
                    <td class="right">
                        {{ $order->payment->method === 'cod' ? 'Cash on Delivery' : 'GCash' }}
                    </td>
                </tr>
                @if ($order->payment->reference_number)
                    <tr>
                        <td class="label">Reference #</td>
                        <td class="right">{{ $order->payment->reference_number }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="label">Paid on</td>
                    <td class="right">{{ $order->payment->paid_at?->format('M d, Y h:i A') }}</td>
                </tr>
            </table>
        @else
            <div class="unpaid-stamp">⚠ UNPAID</div>
            <p class="label">Amount due: ₱{{ number_format($order->total_amount, 2) }}</p>
        @endif
    </div>

    <hr>

    <p class="center subtitle">Thank you for choosing LaundryGo!</p>

    <button class="print-btn" onclick="window.print()">Print Receipt</button>

</body>
</html>
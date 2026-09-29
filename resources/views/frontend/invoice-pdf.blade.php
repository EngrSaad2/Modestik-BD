<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        @page {
            size: 80mm 220mm;
            margin: 0;
        }
        @font-face {
            font-family: 'SolaimanLipi';
            font-style: normal;
            font-weight: normal;
            src: url('{{ public_path("fonts/SolaimanLipi.ttf") }}') format('truetype');
        }
        @font-face {
            font-family: 'Kalpurush';
            font-style: normal;
            font-weight: normal;
            src: url('{{ public_path("fonts/kalpurush.ttf") }}') format('truetype');
        }
        body {
            font-family: 'SolaimanLipi', 'Kalpurush', 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 10px;
            width: 70mm;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .logo { font-size: 16px; font-weight: bold; margin-bottom: 5px; }
        .info { margin-bottom: 10px; line-height: 1.4; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .items-table th { font-weight: bold; border-bottom: 1px dashed #000; padding: 4px 0; text-align: left; }
        .items-table td { padding: 5px 0; vertical-align: top; }
        .totals-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .totals-table td { padding: 4px 0; }
        .footer { margin-top: 15px; text-align: center; font-size: 10px; }
    </style>
</head>
<body>
    <div class="text-center">
        <div class="logo">Modestik</div>
        <div class="info">
            {{ \App\Models\Setting::get('site_address', 'Dhaka, Bangladesh') }}<br>
            Phone: {{ \App\Models\Setting::get('site_phone', '+8801810536303') }}<br>
            Email: {{ \App\Models\Setting::get('site_email', 'admin@modestikbd.com') }}
        </div>
    </div>

    <div class="divider"></div>

    <div class="info">
        <strong>Invoice:</strong> #{{ $order->order_number }}<br>
        <strong>Date:</strong> {{ $order->created_at->format('d M Y, h:i A') }}<br>
        @if($order->consignment_id)
            <strong>Consignment ID:</strong> {{ $order->consignment_id }}<br>
            <strong>Tracking Code:</strong> {{ $order->tracking_code }}<br>
        @endif
        <strong>Customer:</strong> {{ $order->name }}<br>
        <strong>Phone:</strong> {{ $order->phone }}<br>
        <strong>Address:</strong> {{ $order->address }}<br>
        <table style="width: 100%; border: none; margin: 2px 0 0 0; padding: 0; font-size: 11px;">
            <tr>
                <td style="padding: 0; border: none; width: 50%;"><strong>Thana/Area:</strong> {{ $order->division ?? '-' }}</td>
                <td style="padding: 0; border: none; width: 50%;"><strong>District:</strong> {{ $order->district ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="divider"></div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 50%;">Item</th>
                <th style="width: 15%; text-align: center;">Qty</th>
                <th style="width: 35%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>
                    {{ $item->name }}
                    @if($item->variant_info)
                        <br><small style="color:#555;">({{ $item->variant_info }})</small>
                    @endif
                </td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td style="text-align: right;">Tk. {{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <table class="totals-table">
        <tr>
            <td>Subtotal:</td>
            <td style="text-align: right;">Tk. {{ number_format($order->subtotal, 2) }}</td>
        </tr>
        @if($order->discount > 0)
        <tr>
            <td>Discount:</td>
            <td style="text-align: right; color: #ff0000;">-Tk. {{ number_format($order->discount, 2) }}</td>
        </tr>
        @endif
        <tr>
            <td>Shipping:</td>
            <td style="text-align: right;">Tk. {{ number_format($order->shipping_charge, 2) }}</td>
        </tr>
        <tr class="bold" style="border-top: 1px dashed #000;">
            <td style="padding-top: 5px;">Total Amount:</td>
            <td style="text-align: right; padding-top: 5px;">Tk. {{ number_format($order->total, 2) }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="info">
        <strong>Payment Method:</strong> {{ strtoupper($order->payment_method) }}<br>
        @if($order->payment_method === 'bkash')
            <strong>Sender No:</strong> {{ $order->bkash_number }}<br>
            <strong>TrxID:</strong> {{ $order->bkash_trx_id }}<br>
        @endif
        <strong>Payment Status:</strong> {{ strtoupper($order->payment_status) }}
    </div>

    <div class="divider"></div>

    <div class="footer">
        Thank you for shopping with us!<br>
        Please visit again.
    </div>
</body>
</html>

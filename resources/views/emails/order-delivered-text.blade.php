Order Delivered - #{{ $order->order_number }}

Hi {{ $order->name }},

We are happy to inform you that your order has been successfully delivered. We hope you love your purchase!

Order Number: #{{ $order->order_number }}
Delivery Date: {{ now()->format('M d, Y h:i A') }}
Payment Method: {{ strtoupper($order->payment_method) }}

Items:
@foreach($order->items as $item)
- {{ $item->name }} (Qty: {{ $item->quantity }}) - ৳{{ number_format($item->total, 2) }}
@endforeach

Subtotal: ৳{{ number_format($order->subtotal, 2) }}
Shipping: ৳{{ number_format($order->shipping_charge, 2) }}
Grand Total: ৳{{ number_format($order->total, 2) }}

Delivered To:
{{ $order->name }}
Phone: {{ $order->phone }}
Address: {{ $order->address }}, {{ $order->area ? $order->area . ', ' : '' }}{{ $order->district }}, {{ $order->division }}

Need help? Contact our Support Team: admin@modestikbd.com

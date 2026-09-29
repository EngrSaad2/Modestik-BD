Order Confirmation - #{{ $order->order_number }}

Hi {{ $order->name }},

Thank you for your purchase. We have received your order and are processing it.
Delivery Time: ঢাকা সিটির ভিতরে: ২ থেকে ৩ দিন | ঢাকা সিটির বাইরে: ২ থেকে ৫ দিন।


Order Number: #{{ $order->order_number }}
Order Date: {{ $order->created_at->format('M d, Y h:i A') }}
Payment Method: {{ strtoupper($order->payment_method) }}

Items:
@foreach($order->items as $item)
- {{ $item->name }} (Qty: {{ $item->quantity }}) - ৳{{ number_format($item->total, 2) }}
@endforeach

Subtotal: ৳{{ number_format($order->subtotal, 2) }}
Shipping: ৳{{ number_format($order->shipping_charge, 2) }}
Grand Total: ৳{{ number_format($order->total, 2) }}

Delivery Address:
{{ $order->name }}
Phone: {{ $order->phone }}
Address: {{ $order->address }}, {{ $order->area ? $order->area . ', ' : '' }}{{ $order->district }}, {{ $order->division }}

Need help? Contact our Support Team: admin@modestikbd.com

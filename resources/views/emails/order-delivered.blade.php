<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Delivered</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', 'Inter', Helvetica, Arial, sans-serif; background-color: #f3f4f6; color: #2d2325; margin: 0; padding: 0; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f3f4f6; padding: 30px 0; }
        .main-table { width: 100%; max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
        .top-stripe { height: 8px; background-color: #d97d8c; }
        .content-padding { padding: 40px; }
        .logo-row { width: 100%; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: 800; color: #d97d8c; text-decoration: none; font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; }
        .order-no { font-size: 13px; color: #7b686a; text-align: right; font-weight: 500; }
        .order-no strong { color: #2d2325; font-weight: 700; }
        .headline { font-size: 24px; font-weight: 800; color: #2d2325; margin: 0 0 15px 0; }
        .salutation { font-size: 16px; font-weight: 700; color: #2d2325; margin: 0 0 10px 0; }
        .intro-text { font-size: 14px; color: #7b686a; line-height: 1.6; margin: 0 0 35px 0; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { border-bottom: 1px solid #e2e8f0; padding: 12px 0; font-size: 12px; font-weight: 700; color: #7b686a; text-align: left; }
        .items-table td { border-bottom: 1px solid #f1f5f9; padding: 15px 0; font-size: 13px; color: #2d2325; vertical-align: middle; }
        .product-info-cell { display: flex; align-items: center; }
        .product-img { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; margin-right: 15px; border: 1px solid #e2e8f0; }
        .product-name { font-weight: 700; color: #2d2325; margin: 0 0 2px 0; font-size: 13px; }
        .product-meta { font-size: 11px; color: #7b686a; margin: 0; }
        .totals-wrapper { width: 100%; margin-bottom: 40px; }
        .totals-table { width: 250px; margin-left: auto; border-collapse: collapse; }
        .totals-table td { padding: 6px 0; font-size: 13px; color: #7b686a; }
        .totals-table td.val { text-align: right; font-weight: 600; color: #2d2325; }
        .totals-table tr.grand-total td { font-size: 15px; font-weight: 800; color: #2d2325; border-top: 1px solid #e2e8f0; padding-top: 12px; margin-top: 5px; }
        .totals-table tr.grand-total td.val { color: #d97d8c; }
        .address-section { width: 100%; margin-bottom: 40px; }
        .address-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; font-size: 13px; color: #7b686a; line-height: 1.6; }
        .address-title { font-weight: 700; color: #2d2325; margin-bottom: 10px; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        .sign-off { font-size: 14px; color: #7b686a; line-height: 1.6; margin-bottom: 30px; }
        .sign-off strong { color: #2d2325; }
        .footer { background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 40px; text-align: center; font-size: 12px; color: #7b686a; }
        .footer a { color: #d97d8c; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table class="main-table" cellpadding="0" cellspacing="0" align="center">
                    <!-- Top accent line -->
                    <tr>
                        <td class="top-stripe"></td>
                    </tr>
                    
                    <tr>
                        <td class="content-padding">
                            <!-- Logo and Order Number header row -->
                            <table class="logo-row" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <span class="logo">{{ config('app.name', 'Modestik') }}</span>
                                    </td>
                                    <td class="order-no">
                                        Order No : <strong>{{ $order->order_number }}</strong>
                                    </td>
                                </tr>
                            </table>

                            <h1 class="headline">Yay! Your Order Is Delivered</h1>
                            
                            <p class="salutation">Hi {{ $order->name }},</p>
                            
                            <p class="intro-text">
                                We are happy to inform you that your order has been successfully delivered. We hope you love your purchase! Please find below the receipt of your purchase.
                            </p>

                            <!-- Product Items list -->
                            <table class="items-table">
                                <thead>
                                    <tr>
                                        <th style="width: 70%;">Order Details</th>
                                        <th style="width: 30%; text-align: right;">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td>
                                                <div class="product-info-cell">
                                                    @php
                                                        $imageUrl = $item->product ? $item->product->primary_image_url : asset('images/placeholder.png');
                                                        if (str_contains($imageUrl, 'localhost')) {
                                                            $imageUrl = str_replace(
                                                                ['http://localhost/modestik/public', 'https://localhost/modestik/public', 'localhost/modestik/public', 'http://localhost/storage', 'https://localhost/storage'],
                                                                ['https://modestikbd.com', 'https://modestikbd.com', 'modestikbd.com', 'https://modestikbd.com/storage', 'https://modestikbd.com/storage'],
                                                                $imageUrl
                                                            );
                                                        }
                                                    @endphp
                                                    <img src="{{ $imageUrl }}" alt="{{ $item->name }}" class="product-img">
                                                    <div>
                                                        <h4 class="product-name">{{ $item->name }}</h4>
                                                        <p class="product-meta">
                                                            Quantity : {{ $item->quantity }}
                                                            @if($item->variant_info)
                                                                <br>Option : {{ $item->variant_info }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="text-align: right; font-weight: 700;">
                                                ৳{{ number_format($item->total, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Separator line -->
                            <div style="border-top: 1.5px dashed #cbd5e1; margin-bottom: 20px;"></div>

                            <!-- Totals section table -->
                            <table class="totals-wrapper" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <table class="totals-table" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td>Total :</td>
                                                <td class="val">৳{{ number_format($order->subtotal, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Shipping Charges :</td>
                                                <td class="val">৳{{ number_format($order->shipping_charge, 2) }}</td>
                                            </tr>
                                            @if($order->tax > 0)
                                                <tr>
                                                    <td>Tax :</td>
                                                    <td class="val">৳{{ number_format($order->tax, 2) }}</td>
                                                </tr>
                                            @endif
                                            @if($order->discount > 0)
                                                <tr style="color: #dc2626;">
                                                    <td>Discount :</td>
                                                    <td class="val" style="color: #dc2626;">-৳{{ number_format($order->discount, 2) }}</td>
                                                </tr>
                                            @endif
                                            <tr class="grand-total">
                                                <td>Grand Total :</td>
                                                <td class="val">৳{{ number_format($order->total, 2) }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Address Box Section -->
                            <table class="address-section" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="width: 48%; padding-right: 4%; vertical-align: top;">
                                                    <div class="address-box">
                                                        <div class="address-title">Shipping Address:</div>
                                                        <strong>{{ $order->name }}</strong><br>
                                                        Phone: {{ $order->phone }}<br>
                                                        {{ $order->address }}<br>
                                                        {{ $order->area ? $order->area . ', ' : '' }}{{ $order->district }}, {{ $order->division }}
                                                    </div>
                                                </td>
                                                <td style="width: 48%; vertical-align: top;">
                                                    <div class="address-box">
                                                        <div class="address-title">Billing Address:</div>
                                                        <strong>{{ $order->name }}</strong><br>
                                                        Phone: {{ $order->phone }}<br>
                                                        {{ $order->address }}<br>
                                                        {{ $order->area ? $order->area . ', ' : '' }}{{ $order->district }}, {{ $order->division }}
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p class="sign-off">
                                Hope to see you soon,<br>
                                <strong>{{ config('app.name', 'Modestik') }} Team</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Email Footer -->
                    <tr>
                        <td class="footer">
                            Need help? Contact our <a href="{{ route('contact') }}">Support Team</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

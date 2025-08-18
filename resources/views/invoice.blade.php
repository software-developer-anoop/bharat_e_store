<!DOCTYPE html>
<html>
<head>
    <title>Invoice - #{{ $order->order_id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 14px; 
            color: #333; 
        }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f9f9f9; text-align: left; }
        .text-right { text-align: right; }
        .address-table td { vertical-align: top; padding: 10px; }
    </style>
</head>
<body>

    {{-- Header with Logo --}}

    <div class="header text-center" style="margin-bottom: 20px; text-align:center;">
    <img src="{{ public_path('uploads/'.$web->logo) }}" 
         alt="Company Logo" 
         style="max-height:100px; width:auto; display:block; margin:0 auto 10px auto;">
    <h2 style="margin:0; font-weight:bold; color:#333;">Bharat E Store</h2>
</div>


    <h2>Order {{ ucfirst($order->order_status) }} - #{{ $order->order_id }}</h2>
    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('d-m-Y') }}</p>

    {{-- Product Table --}}
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Line Total</th>
            </tr>
        </thead>
        <tbody>
            @php $totalSelling = 0; @endphp
            @foreach($orderItems as $item)
                @php 
                    $lineTotal = $item->product_selling_price * $item->quantity;
                    $totalSelling += $lineTotal;
                @endphp
                <tr>
                    <td>
                        {{ $item->product_name }} <br>
                        <small><strong>Size:</strong> {{ $item->product_size ?? '-' }}</small>
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td class="text-right">₹{{ number_format($item->product_selling_price, 2) }}</td>
                    <td class="text-right">₹{{ number_format($lineTotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                <td class="text-right"><strong>₹{{ number_format($totalSelling, 2) }}</strong></td>
            </tr>
            <tr>
                <td colspan="3" class="text-right"><strong>Payment Method:</strong></td>
                <td class="text-right">{{ ucfirst($order->payment_mode) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Billing & Shipping Address --}}
    <table class="address-table" style="margin-top:30px; width:100%;">
        <tr>
            <td width="50%">
                <h3>Billing Address</h3>
                <p>
                    {{ $order->name }}<br>
                    {{ $order->address }}<br>
                    {{ $order->city_name }} - {{ $order->pincode }}<br>
                    {{ $order->state_name }}<br>
                    {{ $order->phone }}<br>
                    {{ $order->email }}
                </p>
            </td>
            <td width="50%">
                <h3>Shipping Address</h3>
                <p>
                    {{ $order->name }}<br>
                    {{ $order->address }}<br>
                    {{ $order->city_name }} - {{ $order->pincode }}<br>
                    {{ $order->state_name }}
                </p>
            </td>
        </tr>
    </table>
    <p class="text-center mt-4">A 325, New Panchwati, Block I, A-Block, Govindpuram, Ghaziabad, Uttar Pradesh 201013
Contact No: 9266208206</p>
    <div class="footer text-center mt-4">
    &copy; {{ date('Y') }} Bharat E Store. All rights reserved.
    </div>
</body>
</html>

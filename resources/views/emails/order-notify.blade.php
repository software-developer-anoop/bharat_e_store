<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Update - Bharat E Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f6f6f6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: #ffffff; padding: 20px; border-radius: 8px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { color: #333; }
        .content p { font-size: 15px; color: #555; line-height: 1.6; }
        .content strong { color: #333; }
        .btn { display: inline-block; background: #007bff; color: #ffffff; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin-top: 15px; }
        .btn:hover { background: #0056b3; }
        .footer { text-align: center; margin-top: 20px; font-size: 13px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Your Order Has Been {{ $order->order_status ? ucfirst($order->order_status) : 'Updated' }}!</h2>
        </div>

        <div class="content">
            <p>Hi <strong>{{ $order->customer_name ? ucwords($order->customer_name) : 'Customer' }}</strong>,</p>

            <p>
                Your order <strong>{{ $order->order_id ? '#'.$order->order_id : '' }}</strong> has been 
                @switch($order->order_status)
                    @case('shipped')
                        shipped and is on its way.
                        @break
                    @case('delivered')
                        delivered. We hope you’re loving your purchase!
                        @break
                    @default
                        {{ $order->order_status ?? 'updated' }}.
                @endswitch
            </p>

            @if($order->order_status == "shipped")
                <p>Thank you for shopping with <strong>Bharat E Store</strong>. We’ll notify you once it’s delivered.</p>
            @else
                <p>Thank you for shopping with <strong>Bharat E Store</strong>.</p>
            @endif

            <p>Best regards,<br>
            <strong>Team Bharat E Store</strong></p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Bharat E Store. All rights reserved.
        </div>
    </div>
</body>
</html>

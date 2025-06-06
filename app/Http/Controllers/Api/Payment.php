<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
class Payment extends Controller {
    public function index(Request $request)
    {
        try {
            $customer_id = $request->input('customer_id', '');
            $amount = $request->input('amount', '');
            $payment_mode = strtolower($request->input('payment_mode', ''));
            $payment_environment = $request->input('payment_environment', '');
            $address_id = $request->input('address_id', '');
            $coupon_id = $request->input('coupon_id', '');

            $product_ids = $request->input('product_id', []);
            $product_names = $request->input('product_name', []);
            $product_colors = $request->input('product_color', []);
            $product_prices = $request->input('product_price', []);
            $quantities = $request->input('quantity', []);
            $images = $request->input('image', []); // Now expecting string URLs or image paths

            // Validation
            if (empty($customer_id) || empty($amount) || empty($payment_mode) || empty($payment_environment) || empty($address_id)) {
                return response()->json(['status' => false, 'message' => 'Missing required fields']);
            }

            if (!is_numeric($amount) || $amount <= 0) {
                return response()->json(['status' => false, 'message' => 'Invalid amount']);
            }

            if (empty($product_ids) || !is_array($product_ids)) {
                return response()->json(['status' => false, 'message' => 'No products found']);
            }

            // Customer and Address Checks
            $customer = DB::table('customers')->where('id', $customer_id)->first();
            if (!$customer || $customer->profile_status === 'Inactive') {
                return response()->json(['status' => false, 'message' => 'Customer not found or inactive']);
            }

            $address = DB::table('addresses')->find($address_id);
            if (!$address) {
                return response()->json(['status' => false, 'message' => 'Address not found']);
            }

            // Create Order
            $orderId = 'BES' . date('YmdHis');
            $order_table_id = DB::table('orders')->insertGetId([
                'order_id' => $orderId,
                'customer_id' => $customer_id,
                'address_id' => $address_id,
                'amount' => $amount,
                'payment_mode' => $payment_mode,
                'status' => 'pending',
                'coupon_id' => $coupon_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert Product Details
            for ($i = 0; $i < count($product_ids); $i++) {
                DB::table('order_history')->insert([
                    'order_table_id' => $order_table_id,
                    'order_id' => $orderId,
                    'product_id' => $product_ids[$i] ?? '',
                    'product_name' => $product_names[$i] ?? '',
                    'product_color' => $product_colors[$i] ?? '',
                    'product_selling_price' => preg_replace('/[^\d.]/', '', $product_prices[$i] ?? ''),
                    'quantity' => $quantities[$i] ?? '',
                    'image' => $images[$i] ?? '', // Just use string/image URL
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Log Transaction
            DB::table('transactions')->insert([
                'order_id' => $orderId,
                'status' => $payment_mode === 'cod' ? 'cod' : 'initiated',
                'payment_gateway' => $payment_mode === 'cod' ? 'cod' : 'cashfree',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Cash on Delivery
            if ($payment_mode === 'cod') {
                return response()->json([
                    'status' => true,
                    'message' => 'Cash on Delivery order placed successfully',
                    'payment_mode' => 'cod',
                    'payment_environment' => $payment_environment,
                    'order_table_id' => $order_table_id,
                    'order_id' => $orderId,
                ]);
            }

            // Online Payment (Cashfree)
            $appId = $payment_environment === "TEST" ? env('CASHFREE_APP_ID_TEST') : env('CASHFREE_APP_ID_PROD');
            $secretKey = $payment_environment === "TEST" ? env('CASHFREE_SECRET_KEY_TEST') : env('CASHFREE_SECRET_KEY_PROD');
            $cashfreeBaseUrl = $payment_environment === "TEST" ? env('CASHFREE_BASE_URL_TEST') : env('CASHFREE_BASE_URL_PROD');
            $callbackUrl = url('/api/payment-webhook');

            $cfRequest = [
                'order_id' => $orderId,
                'order_amount' => $amount,
                'order_currency' => 'INR',
                'customer_details' => [
                    'customer_id' => $customer_id,
                    'customer_name' => $customer->customer_name ?: 'Guest User',
                    'customer_email' => $customer->customer_email ?? 'test@example.com',
                    'customer_phone' => $customer->customer_phone ?? '9999999999',
                ],
                'order_meta' => [
                    'notify_url' => $callbackUrl,
                    'return_url' => ''
                ],
            ];

            $payload = json_encode($cfRequest);
            $ch = curl_init("$cashfreeBaseUrl/pg/orders");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'x-api-version: 2023-08-01',
                "x-client-id: $appId",
                "x-client-secret: $secretKey",
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                return response()->json(['status' => false, 'message' => 'Curl error: ' . $curlError]);
            }

            $responseBody = json_decode($response, true);
            if ($httpCode === 200 && isset($responseBody['payment_session_id'])) {
                return response()->json([
                    'status' => true,
                    'message' => 'Payment initiated',
                    'payment_mode' => 'online',
                    'payment_environment' => $payment_environment,
                    'payment_session_id' => $responseBody['payment_session_id'],
                    'order_table_id' => $order_table_id,
                    'order_id' => $orderId,
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => $responseBody['message'] ?? 'Failed to initiate payment',
                'error' => $responseBody,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function handleWebhook(Request $request)
    {
        // Parse the webhook JSON payload
        $raw = file_get_contents('php://input');
        //\Log::info('Cashfree Webhook Raw: ' . $raw);

        $payload = json_decode($raw, true);

        if (!$payload || !isset($payload['data'])) {
            return response()->json(['status' => false, 'message' => 'Invalid payload'], 400);
        }

        $data = $payload['data'];

        $orderId = $data['order']['order_id'] ?? null;
        $paymentStatus = $data['payment']['payment_status'] ?? 'FAILED';
        $paymentId = $data['payment']['cf_payment_id'] ?? null;
        $customerId = $data['customer_details']['customer_id'] ?? null;

        if (!$orderId || !$customerId) {
            return response()->json(['status' => false, 'message' => 'Missing order_id or customer_id'], 400);
        }

        $transaction = DB::table('transactions')->where('order_id', $orderId)->first();

        if (!$transaction || $transaction->payment_gateway !== 'cashfree') {
            return response()->json(['status' => false, 'message' => 'Not a Cashfree transaction or transaction not found']);
        }

        $newStatus = $paymentStatus === 'SUCCESS' ? 'success' : 'failure';
        $orderStatus = $paymentStatus === 'SUCCESS' ? 'paid' : 'failed';

        // Update transaction
        DB::table('transactions')->where('order_id', $orderId)->update([
            'status' => $newStatus,
            'transaction_id' => $paymentId,
            'response' => json_encode($payload),
            'updated_at' => now(),
        ]);

        // Update order
        DB::table('orders')->where('order_id', $orderId)->update([
            'status' => $orderStatus,
            'order_status' => $orderStatus=='paid'?'placed':'pending',
            'updated_at' => now(),
        ]);

        // Empty cart only if payment succeeded
        if ($newStatus === 'success' && $orderStatus === 'paid') {
            $productIds = DB::table('order_history')->where('order_id', $orderId)->pluck('product_id');

            DB::table('cart')
                ->where('customer_id', $customerId)
                ->whereIn('product_id', $productIds)
                ->delete();
        }

        return response()->json(['status' => true, 'message' => 'Webhook handled'], 200);
    }


}

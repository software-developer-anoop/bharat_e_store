<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
class Payment extends Controller
{
    public function index(Request $request)
    {
        try {
            $post = checkPayload();

            $customer_id = trim($post['customer_id'] ?? '');
            $amount = trim($post['amount'] ?? '');

            if (empty($customer_id)) {
                return response()->json(['status' => false, 'message' => 'Customer ID is blank']);
            }

            if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
                return response()->json(['status' => false, 'message' => 'Amount is invalid']);
            }

            $customer = DB::table('customers')->where('id', $customer_id)->first();
            if (!$customer) {
                return response()->json(['status' => false, 'message' => 'Customer not found']);
            }

            if ($customer->profile_status === 'Inactive') {
                return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
            }

            $orderId = Str::uuid()->toString();

            // Save order
            $order_table_id = DB::table('orders')->insertGetId([
                'order_id' => $orderId,
                'customer_id' => $customer_id,
                'amount' => $amount,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Log transaction
            DB::table('transactions')->insert([
                'order_id' => $orderId,
                'status' => 'initiated',
                'payment_gateway' => 'cashfree',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Cashfree Config
            $appId = env('CASHFREE_APP_ID');
            $secretKey = env('CASHFREE_SECRET_KEY');
            $cashfreeBaseUrl = env('CASHFREE_BASE_URL');
            $callbackUrl = route('payment.webhook');

            $cfRequest = [
                'order_id' => $orderId,
                'order_amount' => $amount,
                'order_currency' => 'INR',
                'customer_details' => [
                    'customer_id' => $customer_id,
                    'customer_name' => $customer->customer_name??'Guest User',
                    'customer_email' => $customer->customer_email ?? 'test@example.com',
                    'customer_phone' => $customer->customer_phone ?? '9999999999',
                ],
                'order_meta' => [
                    'return_url' => $callbackUrl . '?order_id={{order_id}}',
                ]
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

            // Optional: disable SSL verification in dev (DON'T DO THIS IN PRODUCTION)
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);

            curl_close($ch);

            if ($curlError) {
                return response()->json([
                    'status' => false,
                    'message' => 'Curl error: ' . $curlError
                ]);
            }

            $responseBody = json_decode($response, true);

            if ($httpCode === 200 && isset($responseBody['payment_session_id'])) {
                $paymentLink = "https://www.cashfree.com/pg/view_payment/" . $responseBody['payment_session_id'];

                return response()->json([
                    'status' => true,
                    'message' => 'Payment initiated',
                    'url' => $paymentLink,
                    'order_table_id' => $order_table_id,
                    'order_id' => $orderId,
                ]);
            }


            return response()->json([
                'status' => false,
                'message' => 'Failed to initiate payment',
                'error' => $responseBody,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function handleWebhook(Request $request)
    {
        $data = $request->all();

        $orderId = $data['order']['order_id'] ?? null;
        $transactionStatus = $data['order']['order_status'] ?? 'FAILED';

        if (!$orderId) {
            return response()->json(['status' => false, 'message' => 'Invalid payload']);
        }

        DB::table('transactions')
            ->where('order_id', $orderId)
            ->update([
                'status' => $transactionStatus === 'PAID' ? 'success' : 'failure',
                'transaction_id' => $data['order']['cf_payment_id'] ?? null,
                'response' => json_encode($data),
                'updated_at' => now()
            ]);

        DB::table('orders')
            ->where('order_id', $orderId)
            ->update([
                'status' => $transactionStatus === 'PAID' ? 'paid' : 'failed',
                'updated_at' => now()
            ]);

        return response()->json(['status' => true, 'message' => 'Webhook handled']);
    }

    public function paymentStatus($orderId)
    {
        $order = DB::table('orders')->where('order_id', $orderId)->first();

        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found']);
        }

        return response()->json(['status' => true, 'message' => 'Order not found','order_id'=>$orderId,'status'=>$order->status]);
    }
}

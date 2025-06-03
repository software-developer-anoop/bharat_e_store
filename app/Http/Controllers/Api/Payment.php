<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
class Payment extends Controller {
    public function index(Request $request) {

        try {
            $post = checkPayload();
            $customer_id = trim($post['customer_id']??'');
            $amount = trim($post['amount']??'');
            $payment_mode = strtolower(trim($post['payment_mode']??'')); // 'online' or 'cod'
            $address_id = trim($post['address_id']??'');
            $product_id = trim($post['product_id']??'');
            $product_name = trim($post['product_name']??'');
            $product_color = trim($post['product_color']??'');
            $product_selling_price = trim($post['product_selling_price']??'');
            $quantity = trim($post['quantity']??'');
            $image = trim($post['image']??'');

            if (empty($customer_id)) {
                return response()->json(['status' => false, 'message' => 'Customer ID is blank']);
            }

            if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
                return response()->json(['status' => false, 'message' => 'Amount is invalid']);
            }

            if (empty($payment_mode)) {
                return response()->json(['status' => false, 'message' => 'Payment Mode is blank']);
            }

            if (empty($address_id)) {
                return response()->json(['status' => false, 'message' => 'Address Id is blank']);
            }

            // if (empty($product_id)) {
            //     return response()->json(['status' => false, 'message' => 'Product Id is blank']);
            // }

            // if (empty($product_name)) {
            //     return response()->json(['status' => false, 'message' => 'Product Name is blank']);
            // }

            // if (empty($product_color)) {
            //     return response()->json(['status' => false, 'message' => 'Product Color is blank']);
            // }

            // if (empty($product_selling_price)) {
            //     return response()->json(['status' => false, 'message' => 'Product Price is blank']);
            // }

            // if (empty($quantity)) {
            //     return response()->json(['status' => false, 'message' => 'Quantity is blank']);
            // }

            // if (empty($image)) {
            //     return response()->json(['status' => false, 'message' => 'Image is blank']);
            // }

            $customer = DB::table('customers')->where('id', $customer_id)->first();

            if (!$customer) {
                return response()->json(['status' => false, 'message' => 'Customer not found']);
            }

            if ($customer->profile_status === 'Inactive') {
                return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
            }

            $address = DB::table('addresses')->find($address_id);

            if (!$address) {
                return response()->json(['status' => false, 'message' => 'Address not found']);
            }

            $orderId = Str::uuid()->toString();

            // Save order
            $order_table_id = DB::table('orders')->insertGetId(
                ['order_id'    => $orderId, 
                 'customer_id' => $customer_id, 
                 'address_id' => $address_id, 
                 'amount'      => $amount, 
                 'payment_mode'      => $payment_mode, 
                 // 'product_id'      => $product_id, 
                 // 'product_name'      => $product_name, 
                 // 'product_color'      => $product_color, 
                 // 'product_selling_price'      => $product_selling_price, 
                 // 'quantity'      => $quantity, 
                 'status'      => 'pending', 
                 'created_at'  => now(), 
                 'updated_at'  => now(), ]);

            // Log transaction
            DB::table('transactions')->insert(
                ['order_id' => $orderId, 
                 'status' => $payment_mode === 'cod' ? 'cod' : 'initiated', 
                 'payment_gateway' => $payment_mode === 'cod' ? 'cod' : 'cashfree', 
                 'created_at' => now(), 
                 'updated_at' => now(), ]);

            // ✅ If COD, skip Cashfree API and return success
            if ($payment_mode === 'cod') {
                return response()->json(
                    ['status' => true, 
                     'message' => 'Cash on Delivery order placed successfully',
                     'payment_mode' => 'cod', 
                     'order_table_id' => $order_table_id, 
                     'order_id' => $orderId, ]);
            }

            // 🟡 Online payment (Cashfree)
            $appId = env('CASHFREE_APP_ID');
            $secretKey = env('CASHFREE_SECRET_KEY');
            $cashfreeBaseUrl = env('CASHFREE_BASE_URL');
            $callbackUrl = route('payment.webhook');

            $cfRequest = ['order_id' => $orderId, 
                          'order_amount' => $amount, 
                          'order_currency' => 'INR', 
                          'customer_details' => 
                             ['customer_id' => $customer_id, 
                              'customer_name' => $customer->customer_name ? : 'Guest User', 
                              'customer_email' => $customer->customer_email??'test@example.com', 
                              'customer_phone' => $customer->customer_phone??'9999999999', ], 
                          'order_meta' => ['return_url' => $callbackUrl . '?order_id={{order_id}}', ]];

            $payload = json_encode($cfRequest);
            $ch = curl_init("$cashfreeBaseUrl/pg/orders");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'x-api-version: 2023-08-01', "x-client-id: $appId", "x-client-secret: $secretKey", ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // dev only
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            if ($curlError) {
                return response()->json(['status' => false, 'message' => 'Curl error: ' . $curlError]);
            }
            $responseBody = json_decode($response, true);
            if ($httpCode === 200 && isset($responseBody['payment_session_id'])) {
                return response()->json(['status' => true, 'message' => 'Payment initiated', 'payment_mode' => 'online', 'payment_session_id' => $responseBody['payment_session_id'], 'order_table_id' => $order_table_id, 'order_id' => $orderId, ]);
            }
            return response()->json(['status' => false, 'message' => $responseBody['message']??'Failed to initiate payment', 'error' => $responseBody, ]);
        }
        catch(\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred: ' . $e->getMessage() ]);
        }
    }
    public function paymentHandler(Request $request)
    {
        if ($request->isMethod('post')) {
            // Handle Cashfree Webhook
            $data = $request->all();
            $orderId = $data['order']['order_id'] ?? null;
            $transactionStatus = $data['order']['order_status'] ?? 'FAILED';
            $paymentId = $data['order']['cf_payment_id'] ?? null;

            if (!$orderId) {
                return response()->json(['status' => false, 'message' => 'Invalid payload']);
            }

            $transaction = DB::table('transactions')->where('order_id', $orderId)->first();

            if (!$transaction || $transaction->payment_gateway !== 'cashfree') {
                return response()->json(['status' => false, 'message' => 'Not a Cashfree transaction or transaction not found']);
            }

            $newStatus = $transactionStatus === 'PAID' ? 'success' : 'failure';
            $orderStatus = $transactionStatus === 'PAID' ? 'paid' : 'failed';

            DB::table('transactions')->where('order_id', $orderId)->update([
                'status' => $newStatus,
                'transaction_id' => $paymentId,
                'response' => json_encode($data),
                'updated_at' => now(),
            ]);

            DB::table('orders')->where('order_id', $orderId)->update([
                'status' => $orderStatus,
                'updated_at' => now(),
            ]);

            return response()->json(['status' => true, 'message' => 'Webhook handled']);
        }

        // Handle payment status check via GET
        $orderId = $request->query('order_id');

        if (!$orderId) {
            return response()->json(['status' => false, 'message' => 'Missing order_id']);
        }

        $order = DB::table('orders')->where('order_id', $orderId)->first();
        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found']);
        }

        $transaction = DB::table('transactions')->where('order_id', $orderId)->first();

        return response()->json([
            'status' => true,
            'message' => 'Order status fetched successfully',
            'order_id' => $orderId,
            'order_status' => $order->status,
            'payment_method' => $transaction->payment_gateway ?? 'unknown',
            'transaction_status' => $transaction->status ?? 'unknown',
        ]);
    }

}

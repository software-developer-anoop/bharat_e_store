<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
class Cart extends Controller{

    public function index()
    {
        $post = checkPayload();
        $customer_id = trim($post['customer_id'] ?? '');
        $product_id = trim($post['product_id'] ?? '');
        $size = trim($post['size'] ?? '');
        $color = trim($post['color'] ?? '');

        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => 'Customer Id Is Blank']);
        }

        if (empty($product_id)) {
            return response()->json(['status' => false, 'message' => 'Product Id Is Blank']);
        }

        if (empty($size)) {
            return response()->json(['status' => false, 'message' => 'Product Size Is Blank']);
        }

        if (empty($color)) {
            return response()->json(['status' => false, 'message' => 'Product Color Is Blank']);
        }

        // Validate customer
        $customer = DB::table('customers')->find($customer_id);
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }

        if ($customer->profile_status === "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }

        // Validate product
        $product = DB::table('products')->find($product_id);
        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found']);
        }

        // Check if item already exists in cart
        $fetchCart = DB::table('cart')
            ->where(['customer_id' => $customer_id, 'product_id' => $product_id])
            ->first();

        if (is_null($fetchCart)) {
            DB::table('cart')->insert([
                'customer_id' => $customer_id,
                'product_id' => $product_id,
                'size' => $size,
                'color' => $color,
                'quantity' => 1,
                'created_at' => Carbon::now(),
            ]);
        } else {
            DB::table('cart')
                ->where(['customer_id' => $customer_id, 'product_id' => $product_id])
                ->increment('quantity');
        }

        return response()->json(['status' => true, 'message' => 'Added To Cart']);
    }
    public function removeFromCart()
    {
        $post = checkPayload();
        $customer_id = trim($post['customer_id'] ?? '');
        $cart_id = trim($post['cart_id'] ?? '');

        // Basic validation
        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => 'Customer Id Is Blank']);
        }

        if (empty($cart_id)) {
            return response()->json(['status' => false, 'message' => 'Cart Id is blank']);
        }

        // Check if customer exists
        $customer = DB::table('customers')->find($customer_id);
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }

        if ($customer->profile_status === "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }

        // Check if cart item exists and belongs to customer
        $cartProduct = DB::table('cart')
            ->where('id', $cart_id)
            ->where('customer_id', $customer_id)
            ->first();

        if (!$cartProduct) {
            return response()->json(['status' => false, 'message' => 'Cart item not found or does not belong to this customer']);
        }

        // Delete the item
        DB::table('cart')
            ->where('id', $cart_id)
            ->where('customer_id', $customer_id)
            ->delete();

        return response()->json(['status' => true, 'message' => 'Removed from cart']);
    }
    public function myCart()
    {
        $post = checkPayload();
        $customer_id = trim($post['customer_id'] ?? '');

        // Validate input
        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => 'Customer Id Is Blank']);
        }

        // Cooldown control (3 seconds)
        $cooldownKey = "cooldown:mycart:$customer_id";
        if (Cache::has($cooldownKey)) {
            return response()->json(['status' => false, 'message' => 'Too many requests. Please wait a few seconds.'], 429);
        }
        Cache::put($cooldownKey, true, now()->addSeconds(3));

        // Cache key for response
        $cacheKey = "cart:data:$customer_id";
        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        // Validate customer
        $customer = DB::table('customers')->find($customer_id);
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }

        if ($customer->profile_status === "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }

        $customerCurrency = getUserCurrency($customer_id) ?? '';

        // Get cart products
        $products = DB::table('cart')
            ->join('products', 'products.id', '=', 'cart.product_id')
            ->where('cart.customer_id', $customer_id)
            ->select(
                'products.id as product_id',
                'products.category_id',
                'products.subcategory_id',
                'products.product_name',
                'products.product_rating',
                'products.product_image',
                'products.product_selling_price',
                'products.product_cost_price',
                'cart.quantity',
                'cart.id as cart_id',
                'cart.color as color'
            )
            ->get();

        if ($products->isEmpty()) {
            return response()->json(['status' => false, 'message' => "No Records Found"]);
        }

        $subTotal = 0;
        $returnData = [];

        foreach ($products as $value) {
            $images = $value->product_image ? json_decode($value->product_image, true) : [];
            $firstImageUrl = !empty($images) && isset($images[0]['image']) ? url('uploads/' . $images[0]['image']) : null;

            $itemTotal = $value->product_selling_price * $value->quantity;
            $subTotal += $itemTotal;

            $returnData[] = [
                'cart_id'              => (string)$value->cart_id,
                'product_id'           => (string)$value->product_id,
                'category_id'          => (string)$value->category_id,
                'subcategory_id'       => (string)$value->subcategory_id,
                'product_name'         => (string)$value->product_name,
                'product_color'        => (string)($value->color ?? ''),
                'product_selling_price'=> $customerCurrency . ' ' . (string)$value->product_selling_price,
                'product_cost_price'   => $customerCurrency . ' ' . (string)$value->product_cost_price,
                'product_image'        => $firstImageUrl,
                'product_quantity'     => (string)$value->quantity,
            ];
        }

        $response = [
            'status'           => true,
            'data'             => $returnData,
            'subTotal'         => $customerCurrency . ' ' . (string)$subTotal,
            'coins_available'  => (string)$customer->wallet_points,
            'message'          => "API Accessed Successfully!"
        ];

        // Cache response for 2 minutes
        Cache::put($cacheKey, $response, now()->addMinutes(2));

        return response()->json($response);
    }
    public function applyCoupon()
    {
        $post = checkPayload();
        $customer_id = trim($post['customer_id'] ?? '');
        $coupon_id   = trim($post['coupon_id'] ?? '');

        // Validate input
        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => 'Customer Id Is Blank']);
        }

        if (empty($coupon_id)) {
            return response()->json(['status' => false, 'message' => 'Coupon Id is blank']);
        }

        // Fetch cart products for customer
        $products = DB::table('cart')
            ->join('products', 'products.id', '=', 'cart.product_id')
            ->where('cart.customer_id', $customer_id)
            ->select(
                'products.product_selling_price',
                'cart.quantity'
            )
            ->get();

        // Calculate subtotal
        $subTotal = 0;
        foreach ($products as $item) {
            $price = floatval($item->product_selling_price);
            $quantity = intval($item->quantity);
            $subTotal += $price * $quantity;
        }

        $subTotal = round($subTotal, 2);

        // Validate customer
        $customer = DB::table('customers')->find($customer_id);
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }

        if ($customer->profile_status === "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }

        $customerCurrency = getUserCurrency($customer_id) ?? '';

        // Validate coupon
        $coupon = DB::table('coupons')->find($coupon_id);
        if (!$coupon) {
            return response()->json(['status' => false, 'message' => 'Coupon not found']);
        }

        if ($coupon->status === "Inactive") {
            return response()->json(['status' => false, 'message' => 'This coupon is currently inactive']);
        }

        // Check if already applied
        $checkApplied = DB::table('orders')
            ->where(['customer_id' => $customer_id, 'coupon_id' => $coupon_id,'status'=>'success'])
            ->first();

        if ($checkApplied) {
            return response()->json(['status' => false, 'message' => 'Coupon already applied']);
        }

        // Apply coupon logic
        $discount = 0;
        $total = 0;

        if ($coupon->coupon_type === "Fixed") {
            $discount = (float) $coupon->coupon_value;
            $total = $subTotal - $discount;
        } else {
            $discount = ($subTotal * $coupon->coupon_value) / 100;
            $total = $subTotal - $discount;
        }

        // Optional: prevent negative totals
        $total = max($total, 0);

        // Optional: round values to 2 decimal places
        $discount = round($discount, 2);
        $total = round($total, 2);

        // Save history
        $saveData = [
            'coupon_id'  => $coupon_id,
            'customer_id'=> $customer_id,
            'subtotal'   => $subTotal,
            'total'      => $total,
            'discount'   => $discount,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $coupon_history_id = DB::table('coupon_history')->insertGetId($saveData);

        return response()->json([
            'status' => true,
            'message' => 'Coupon Applied Successfully',
            'total' => $customerCurrency .' '. (string)$total,
            'applied_coupon_id' => (string)$coupon_history_id
        ]);
    }

    public function removeCoupon()
    {
        $post = checkPayload();
        $customer_id = trim($post['customer_id'] ?? '');
        $applied_coupon_id = trim($post['applied_coupon_id'] ?? '');

        // Validate input
        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => 'Customer Id Is Blank']);
        }

        if (empty($applied_coupon_id)) {
            return response()->json(['status' => false, 'message' => 'Applied Coupon ID is blank']);
        }

        // Validate customer
        $customer = DB::table('customers')->find($customer_id);
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }

        if ($customer->profile_status === "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }

        // Fetch the coupon history record
        $applied = DB::table('coupon_history')
            ->where('id', $applied_coupon_id)
            ->where('customer_id', $customer_id)
            ->first();

        if (!$applied) {
            return response()->json(['status' => false, 'message' => 'No applied coupon found']);
        }

        // Delete the coupon history record
        DB::table('coupon_history')->where('id', $applied_coupon_id)->delete();

        $customerCurrency = getUserCurrency($customer_id) ?? '';

        return response()->json([
            'status' => true,
            'message' => 'Coupon removed successfully',
            'total' => $customerCurrency .' '. (string)round((float)$applied->subtotal, 2)
        ]);
    }
    public function increaseDecreaseQuantity()
    {
        $post = checkPayload();
        $customer_id = trim($post['customer_id'] ?? '');
        $product_id = trim($post['product_id'] ?? '');
        $condition = trim($post['condition'] ?? '');

        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => 'Customer ID is blank']);
        }

        if (empty($product_id)) {
            return response()->json(['status' => false, 'message' => 'Product ID is blank']);
        }

        if (!in_array($condition, ['increment', 'decrement'])) {
            return response()->json(['status' => false, 'message' => 'Condition must be either increment or decrement']);
        }

        // Validate customer
        $customer = DB::table('customers')->find($customer_id);
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }

        if ($customer->profile_status === "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }

        // Validate product
        $product = DB::table('products')->find($product_id);
        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found']);
        }

        // Check if item already exists in cart
        $cartItem = DB::table('cart')
            ->where(['customer_id' => $customer_id, 'product_id' => $product_id])
            ->first();

        if (!$cartItem) {
            return response()->json(['status' => false, 'message' => 'Product is not in the cart']);
        }

        if ($condition === "increment") {
            DB::table('cart')
                ->where(['customer_id' => $customer_id, 'product_id' => $product_id])
                ->increment('quantity');

            $updatedQuantity = DB::table('cart')
                ->where(['customer_id' => $customer_id, 'product_id' => $product_id])
                ->value('quantity');

            return response()->json([
                'status' => true,
                'message' => 'Quantity increased',
                'quantity' => $updatedQuantity
            ]);
        }

        if ($condition === "decrement") {
            DB::table('cart')
                ->where(['customer_id' => $customer_id, 'product_id' => $product_id])
                ->decrement('quantity');

            $updatedCartItem = DB::table('cart')
                ->where(['customer_id' => $customer_id, 'product_id' => $product_id])
                ->first();

            if ($updatedCartItem && $updatedCartItem->quantity <= 0) {
                DB::table('cart')
                    ->where(['customer_id' => $customer_id, 'product_id' => $product_id])
                    ->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Product removed from cart',
                    'quantity' => 0
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Quantity decreased',
                'quantity' => $updatedCartItem->quantity
            ]);
        }
    }
}

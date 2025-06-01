<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class Couponlist extends Controller {
    public function index() {
        $response = [];
        $post = checkPayload();
        $customer_id = trim($post['customer_id']??'');

        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => 'Customer ID is blank']);
        }

        $customer = DB::table('customers')->find($customer_id);

        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }

        if ($customer->profile_status === "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }

        $coupons = DB::table('coupons')->where('status', 'Active')->select('id', 'coupon_title', 'coupon_description', 'coupon_code', 'coupon_type', 'coupon_value')->get();

        if ($coupons->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'No records found']);
        }

        // Get all coupon_ids already used by this customer
        $usedCouponIds = DB::table('coupon_history')->where('customer_id', $customer_id)->pluck('coupon_id')->toArray();

        $returnData = [];
        foreach ($coupons as $coupon) {
            $returnData[] = ['coupon_id' => (string)$coupon->id, 
                             'coupon_title' => (string)$coupon->coupon_title, 
                             'coupon_description' => (string)$coupon->coupon_description, 
                             'coupon_code' => (string)$coupon->coupon_code, 
                             'coupon_type' => (string)$coupon->coupon_type, 
                             'coupon_value' => (string)$coupon->coupon_value, 
                             'is_applied' => in_array($coupon->id, $usedCouponIds), ];
        }
        return response()->json(['status' => true, 'data' => $returnData, 'message' => 'API accessed successfully!']);
    }
}

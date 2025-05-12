<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Mail\CustomerVerificationMail;
use Illuminate\Support\Facades\Mail;
class Authentication extends Controller {
    public function index(Request $request) {
        $post = checkPayload();
        $countryCode = trim($post['country_code']??'');
        $mobileNumber = trim($post['mobile_number']??'');
        $email = trim($post['email']??'');
        $referralCode = trim($post['referral_code']??'');
        if (empty($countryCode)) {
            return response()->json(['status' => false, 'message' => 'Please Select Country', ]);
        }
        if (strlen($referralCode) !== 10) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid referral code. It must be exactly 10 characters long.'
            ]);
        }
        $country = DB::table('country')->select('country_name', 'country_code', 'country_currency_symbol', 'flag_image')->where([['status', '=', 'Active'], ['country_code', '=', $countryCode]])->first();
        if (!$country) {
            return response()->json(['status' => false, 'message' => 'Invalid Country Selected', ]);
        }
        $isIndia = $country->country_name === 'India';
        if ($isIndia && empty($mobileNumber)) {
            return response()->json(['status' => false, 'message' => 'Please Enter Mobile Number', ]);
        }
        if (!$isIndia && empty($email)) {
            return response()->json(['status' => false, 'message' => 'Please Enter Email', ]);
        }
        $checkField = $isIndia ? ['customer_phone' => $mobileNumber] : ['customer_email' => $email];
        $duplicate = DB::table('customers')->where($checkField)->first();
        if ($duplicate) {
            return response()->json(['status' => false, 'message' => 'Duplicate Entry', ]);
        }
        //$otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $otp = 1234;
        $saveData = array_merge($checkField, ['referral_code' => $referralCode, 'referrer_code' => random_alphanumeric_string(10), 'profile_status' => 'Inactive', 'created_at' => Carbon::now(), 'otp' => $otp, 'otp_sent_at' => Carbon::now(), 'country_code' => $country->country_code, 'country_name' => $country->country_name]);
        $customer_id = DB::table('customers')->insertGetId($saveData);
        if ($isIndia) {
            //sendOtpPhone($mobileNumber, $otp);
            
        } else {
            Mail::to($email)->send(new CustomerVerificationMail(['otp' => $otp]));
        }
        return response()->json(['status' => true, 'message' => 'You are now registered. Please Verify With OTP', 'customer_id' => (string)$customer_id]);
    }
    public function verifyOtp(Request $request) {
        $post = checkPayload(); // Assuming this returns validated data
        $otp = trim($post['otp']??'');
        $customerId = trim($post['customer_id']??'');
        $deviceId = trim($post['device_id']??'');
        $fcmToken = trim($post['fcm_token']??'');
        // Basic validations
        if (!$otp) {
            return response()->json(['status' => false, 'message' => 'OTP is blank']);
        }
        if (strlen($otp) < 4) {
            return response()->json(['status' => false, 'message' => 'Please enter a four-digit OTP']);
        }
        if (!$customerId) {
            return response()->json(['status' => false, 'message' => 'Customer ID is blank']);
        }
        if (!$deviceId) {
            return response()->json(['status' => false, 'message' => 'Device ID is blank']);
        }
        if (!$fcmToken) {
            return response()->json(['status' => false, 'message' => 'FCM Token is blank']);
        }
        $customer = DB::table('customers')->where('id', $customerId)->first();
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }
        if ($customer->otp !== $otp) {
            return response()->json(['status' => false, 'message' => 'Incorrect OTP']);
        }
        // OTP expiration check using Carbon
        $otpSentAt = Carbon::parse($customer->otp_sent_at);
        if (Carbon::now()->greaterThan($otpSentAt->addMinutes(10))) {
            return response()->json(['status' => false, 'message' => 'OTP expired']);
        }
        // Handle referral if exists
        if (!empty($customer->referral_code)) {
            $referralCustomer = DB::table('customers')->where('referrer_code', $customer->referral_code)->first();
            if ($referralCustomer) {
                DB::table('referral_history')->insert(['referrer_customer_id' => $customer->id, 'referrer_code' => $customer->referrer_code, 'referral_customer_id' => $referralCustomer->id, 'referral_code' => $referralCustomer->referrer_code, 'points' => 10, ]);
                DB::table('customers')->where('id', $referralCustomer->id)->increment('wallet_points', 10);
            }
        }
        // Update customer status and device info
        DB::table('customers')->where('id', $customerId)->update(['profile_status' => 'Active', 'email_status' => 'Verified', 'otp' => '', 'device_id' => $deviceId, 'fcm_token' => $fcmToken, ]);
        // Fetch updated customer info
        $customer = DB::table('customers')->find($customerId);
        $data = ['customer_id' => (string)$customer->id, 'customer_email' => (string)$customer->customer_email, 'customer_phone' => (string)$customer->customer_phone, 'profile_status' => (string)$customer->profile_status, 'email_status' => (string)$customer->email_status, 'referrer_code' => (string)$customer->referrer_code, 'country_name' => (string)$customer->country_name, 'country_code' => (string)$customer->country_code, 'device_id' => (string)$customer->device_id, 'fcm_token' => (string)$customer->fcm_token, ];
        return response()->json(['status' => true, 'message' => 'OTP verified', 'data' => $data, ]);
    }
    public function resendOtp(Request $request) {
        $post = checkPayload();
        $customer_id = trim($post['customer_id']??'');
        $customer = DB::table('customers')->where('id', $customer_id)->first();
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'No Record Found']);
        }
        $isIndia = $customer->country_name === 'India';
        //$otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $otp = 1234;
        $updateData['otp'] = $otp;
        $updateData['otp_sent_at'] = date('Y-m-d H:i:s');
        DB::table('customers')->where('id', $customer_id)->update($updateData);
        if ($isIndia) {
            //sendOtpPhone($customer->customer_phone, $otp);
            
        } else {
            Mail::to($customer->customer_email)->send(new CustomerVerificationMail(['otp' => $otp]));
        }
        return response()->json(['status' => true, 'message' => 'OTP Resent']);
    }
    public function autoLogin() {
        $post = checkPayload();
        $device_id = trim($post['device_id']??'');
        $fcm_token = trim($post['fcm_token']??'');
        if (empty($device_id)) {
            return response()->json(['status' => false, 'message' => 'Device ID is blank']);
        }
        if (empty($fcm_token)) {
            return response()->json(['status' => false, 'message' => 'FCM Token is blank']);
        }
        $where = [];
        $where['device_id'] = $device_id;
        $where['fcm_token'] = $fcm_token;
        $customer = DB::table('customers')->where($where)->first();
        if (empty($customer)) {
            return response()->json(['status' => false, 'message' => 'No Record Found']);
        }
        if ($customer->profile_status == "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }
        $return = [];
        $return['customer_id'] = (string)$customer->id;
        $return['customer_name'] = (string)$customer->customer_name;
        $return['customer_email'] = (string)$customer->customer_email;
        $return['customer_phone'] = (string)$customer->customer_phone;
        $return['customer_address'] = (string)$customer->customer_address;
        $return['customer_gender'] = (string)$customer->customer_gender;
        $return['profile_status'] = (string)$customer->profile_status;
        $return['email_status'] = (string)$customer->email_status;
        $return['referral_code'] = (string)$customer->referral_code;
        $return['referrer_code'] = (string)$customer->referrer_code;
        $return['country_name'] = (string)$customer->country_name;
        $return['country_code'] = (string)$customer->country_code;
        $return['device_id'] = (string)$customer->device_id;
        $return['fcm_token'] = (string)$customer->fcm_token;
        return response()->json(['status' => true, 'message' => 'Login Successfully', 'data' => $return]);
    }
    public function customerLogin(Request $request) {
        $post = checkPayload();
        $countryCode = trim($post['country_code']??'');
        $mobileNumber = trim($post['mobile_number']??'');
        $email = trim($post['email']??'');

        if (empty($countryCode)) {
            return response()->json(['status' => false, 'message' => 'Please Select Country', ]);
        }
        
        $country = DB::table('country')->select('country_name', 'country_code', 'country_currency_symbol', 'flag_image')->where([['status', '=', 'Active'], ['country_code', '=', $countryCode]])->first();
        if (!$country) {
            return response()->json(['status' => false, 'message' => 'Invalid Country Selected']);
        }
        $isIndia = $country->country_name === 'India';
        if ($isIndia && empty($mobileNumber)) {
            return response()->json(['status' => false, 'message' => 'Please Enter Mobile Number']);
        }
        if (!$isIndia && empty($email)) {
            return response()->json(['status' => false, 'message' => 'Please Enter Email']);
        }
        $checkField = $isIndia ? ['customer_phone' => $mobileNumber] : ['customer_email' => $email];
        $customer = DB::table('customers')->where($checkField)->first();
        if (empty($customer)) {
            return response()->json(['status' => false, 'message' => 'No Record Found']);
        }
        if ($customer->profile_status == "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }
        //$otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $otp = 1234;
        $updateData['otp'] = $otp;
        $updateData['otp_sent_at'] = date('Y-m-d H:i:s');
        DB::table('customers')->where('id', $customer->id)->update($updateData);
        if ($isIndia) {
            //sendOtpPhone($mobileNumber, $otp);
            
        } else {
            Mail::to($email)->send(new CustomerVerificationMail(['otp' => $otp]));
        }
        return response()->json(['status' => true, 'message' => 'OTP Sent Successfully','customer_id'=>(string)$customer->id]);
    }
    public function logOut(Request $request) {
        $post = checkPayload();
        $customer_id = trim($post['customer_id']??'');
        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => 'Customer Id Is Blank']);
        }
        $customer = DB::table('customers')->where('id', $customer_id)->first();
        if (empty($customer)) {
            return response()->json(['status' => false, 'message' => 'No Record Found']);
        }
        if ($customer->profile_status == "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }
        DB::table('customers')->where('id', $customer_id)->update(['fcm_token' => '', 'device_id' => '']);
        return response()->json(['status' => true, 'message' => 'Logout Successfully']);
    }
}

<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
if (!function_exists("userData")) {
    function userData($select) {
        $userData = DB::table('users')->select($select)->first();
        // Check if data is found before returning
        if ($userData) {
            return $userData;
        }
        return null; // Return null if no data found
        
    }
}
if (!function_exists("webSetting")) {
    function webSetting($select) {
        $web = DB::table('websetting')->select($select)->first();
        // Check if data is found before returning
        if ($web) {
            return $web;
        }
        return null; // Return null if no data found
        
    }
}
if (!function_exists("removeImage")) {
    function removeImage($old_image) {
        $imagePath = public_path('uploads/' . $old_image);
        if (!empty($old_image) && file_exists($imagePath)) {
            @unlink($imagePath);
        }
        return true;
    }
}
if (!function_exists('currentUrl')) {
    function currentUrl() {
        return url()->full();
    }
}
if (!function_exists("getUserCurrency")) {
    function getUserCurrency($id = null) {
        $userCountry = null;
        if (!empty($id)) {
            $userCountry = DB::table('customers')->where('id', $id)->value('country_code');
            return DB::table('country')->where('country_code', $userCountry)->value('country_currency_symbol');
        }
        if (Auth::check()) {
            $userCountry = Auth::user()->country;
            return DB::table('country')->where('country_name', $userCountry)->value('country_currency_symbol');
        }
        return null; // fallback return
        
    }
}
if (!function_exists('checkPayload')) {
    function checkPayload() {
        $response = [];
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            $response["status"] = false;
            $response["message"] = "Bad Request";
            echo json_encode($response);
            exit();
        }
        //handle request data
        $requestData = file_get_contents("php://input");
        $post = json_decode($requestData, true);
        if (empty($post)) {
            $response["status"] = false;
            $response["message"] = "No Payload";
            echo json_encode($response);
            exit();
        }
        $checkHeaders = checkHeaders();
        if (empty($checkHeaders)) {
            return $post;
        }
    }
}
if (!function_exists('checkHeaders')) {
    function checkHeaders() {
        $response = [];
        $headersList = apache_request_headers();
        // print_r($headersList['Content-Type']);die;
        $xPid = explode(" ", $headersList['Authorization']);
        $contentType = $headersList['Content-Type'];
        if (strpos($contentType, 'application/json') !== false) {
            $contentType = 'application/json';
        }
        $allowedXPID = trim($xPid[1]);
        $MatchedHeaderList = [];
        $allowedHeaders = ['CONTENT-TYPE', 'Authorization'];
        $matchHeadersCount = 0;
        foreach ($headersList as $key => $value) {
            if (in_array(strtoupper($key), $allowedHeaders)) {
                $MatchedHeaderList[strtoupper($key) ] = $value;
                $matchHeadersCount+= 1;
            }
        }
        if ($matchHeadersCount == 0 || $matchHeadersCount < 1) {
            $response['status'] = false;
            $response['message'] = 'Headers Not Available!';
            echo json_encode($response);
            exit;
        }
        if (in_array('CONTENT-TYPE', $allowedHeaders) && ($contentType != 'application/json')) {
            $response['status'] = false;
            $response['message'] = 'Invalid Auth Token1!';
            echo json_encode($response);
            exit;
        } else if (in_array('Authorization', $allowedHeaders) && ($allowedXPID != '3d677482a0d52578ddca12375c374e24')) {
            $response['status'] = false;
            $response['message'] = 'Invalid Auth Token2!';
            echo json_encode($response);
            exit;
        }
    }
}
function curlApis($url, $method = 'GET', $postarray = null, $header = null, $time = 30) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, $time);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_MAXREDIRS, 5);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    if ($method === 'POST') {
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postarray));
    }
    if (!empty($header)) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    }
    $jsondata = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        return ['error' => true, 'message' => $err];
    }
    return json_decode($jsondata, true);
}
function random_alphanumeric_string($length) {
    $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return substr(str_shuffle($chars), 0, $length);
}
function sendSms($mobile, $otp) {
    $url = "https://www.fast2sms.com/dev/bulkV2";
    $apiKey = env('FAST2SMS_API_KEY'); // Ensure this is set in your .env
    $fields = ["variables_values" => $otp, 
               "route" => "otp", 
               "numbers" => $mobile];

    $headers = ["authorization: $apiKey", "accept: */*", "cache-control: no-cache", "content-type: application/json"];

    $response = curlApis($url, 'POST', $fields, $headers);
    header('Content-Type: application/json');
    if (is_array($response) && isset($response['return']) && $response['return'] == true) {
        echo json_encode(['status' => 'success', 'message' => "Message sent successfully to $mobile.", 'response' => $response]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send SMS.', 'response' => $response??'No response or invalid format']);
    }
}
function sendOtpPhone($mobile, $otp) {
    $message = "$otp is your verification code.";
    sendSms($mobile, $message);
}
function sendPushNotification(array $fields, string $accessToken = null)
{
    $fcmUrl = 'https://fcm.googleapis.com/v1/projects/myproject-bharat-e-store/messages:send';

    // Load access token from parameter or environment
    $token = $accessToken ?? env('FIREBASE_ACCESS_TOKEN');

    if (!$token) {
        throw new Exception('Firebase access token is not set.');
    }

    $headers = [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ];

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $fcmUrl,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_POSTFIELDS => json_encode(['message' => $fields])
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception('Curl Error: ' . curl_error($ch));
    }

    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpStatus !== 200) {
        throw new Exception("FCM request failed with status $httpStatus: $response");
    }

    return json_decode($response, true);
}

// sending push message to single user by firebase reg id
function send($to, $message, $key) {
    $fields = array('to' => $to, 'notification' => $message['data'],);
    return sendPushNotification($fields, $key);
}
// Sending message to a topic by topic name
function sendToTopic($to, $message, $key) {
    $fields = array('to' => '/topics/' . $to, 'notification' => $message['data'],);
    return sendPushNotification($fields, $key);
}
// sending push message to multiple users by firebase registration ids
function sendMultiple($registration_ids, $message, $key) {
    if (is_array($registration_ids)) {
        $fields = array('registration_ids' => $registration_ids, 'notification' => $message['data'],);
    } else {
        $fields = array('to' => $registration_ids, 'notification' => $message['data'],);
    }
    return sendPushNotification($fields, $key);
}
function getPush($arraydata) {
    $res = array();
    $res['data']['title'] = $arraydata['title'];
    $res['data']['is_background'] = !empty($arraydata['image']) ? TRUE : FALSE;
    $res['data']['body'] = $arraydata['message'];
    $res['data']['image'] = $arraydata['image'];
    $res['data']['payload'] = array('team' => 'India', 'score' => '3x1');
    $res['data']['timestamp'] = date('Y-m-d G:i:s');
    $res['data']['priority'] = 'high';
    // isset($arraydata['custom']) && !empty( $arraydata['custom'] ) ? ( $res['data']['custom'] = $arraydata['custom'] ) : '';
    $res['data']['custom'] = isset($arraydata['custom']) && !empty($arraydata['custom']) ? $arraydata['custom'] : '';
    $res['data']['manual_data'] = isset($arraydata['manual_data']) && !empty($arraydata['manual_data']) ? $arraydata['manual_data'] : array();
    return $res;
}
function pushnotifications($regids, $msgarray, $key = null) {
    $regids = rtrim($regids, ',');
    $idsinarray = explode(',', $regids);
    $idsinarray = array_unique($idsinarray);
    $countids = count($idsinarray);
    $push_type = $countids > 1 ? 'multiple' : 'individual';
    $firebaseRegids = $countids == 1 ? $regids : $idsinarray;
    $json = '';
    $response = '';
    $json = getPush($msgarray);
    if ($push_type == 'topic' && !empty($firebaseRegids)) {
        $response = sendToTopic('global', $json, $key);
    } else if ($push_type == 'individual' && !empty($firebaseRegids)) {
        $response = send($firebaseRegids, $json, $key);
    } else if ($push_type == 'multiple' && !empty($firebaseRegids)) {
        $response = sendMultiple($firebaseRegids, $json, $key);
    }
    $responsearray = json_decode($response, true);
    return !empty($responsearray['success']) ? $responsearray['success'] : '';
}
if (!function_exists('validateApiRequest')) {
    /**
     * Validates that the request is POST and contains a valid Authorization header.
     *
     * @param \Illuminate\Http\Request $request
     * @param string|null $expectedToken
     * @return \Illuminate\Http\JsonResponse|null
     */
    function validateApiRequest($request, $expectedToken = '3d677482a0d52578ddca12375c374e24') {
        if (!$request->isMethod('post')) {
            return response()->json(['status' => false, 'message' => 'Bad Request'], 400);
        }
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['status' => false, 'message' => 'Missing or malformed Auth Token!'], 401);
        }
        $token = trim(str_replace('Bearer', '', $authHeader));
        if ($token !== $expectedToken) {
            return response()->json(['status' => false, 'message' => 'Invalid Auth Token!'], 401);
        }
        // Valid request
        return null;
    }
}

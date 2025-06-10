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
if (!function_exists("validate_slug")) {
    function validate_slug($text, string $divider = '-') {
        $text = preg_replace('~[^\p{L}\d]+~u', $divider, $text);
        $text = transliterator_transliterate('Any-Latin; Latin-ASCII', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, $divider);
        $text = preg_replace('~-+~', $divider, $text);
        $text = strtolower($text);
        return empty($text) ? 'n-a' : $text;
    }
}
function random_alphanumeric_string($length) {
    $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return substr(str_shuffle($chars), 0, $length);
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
function curlApis($url, $method = null, $postarray = null, $header = null, $time = null) {
    $curl = curl_init();
    $timeout = !empty($time) ? $time : 30;
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_POST, false);
    if ($method == 'POST') {
        $jsonpostdata = json_encode($postarray);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonpostdata);
        curl_setopt($curl, CURLOPT_POST, true);
    }
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_MAXREDIRS, 5);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    if (!empty($header)) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    }
    $jsondata = curl_exec($curl);
    curl_close($curl);
    $data = json_decode($jsondata, true);
    return $data;
}
function sendsms($mobile, $message, $restype = null, $callingcode = null) {
    $str = urlencode($message);
    $mobile = $callingcode . $mobile;
    $apiurl = env('SMS_API_PATH') . "rest/services/sendSMS/sendGroupSms?AUTH_KEY=" . env('SMSKEY') . "&message=$str&senderId=" . env('SENDERID') . "&routeId=" . env('ROOTID') . "&mobileNos=$mobile&smsContentType=english";
    $data = curlApis($apiurl);
    $jsondata = json_encode($data, true);
    // echo $jsondata;die;
    $status = false;
    $datastatus = !empty($data['responseCode']) ? $data['responseCode'] : false;
    if ($datastatus == '3001') {
        $status = true;
    }
    return (!empty($restype) ? $jsondata : $status);
}
function sendOtpPhone($mobile, $otp) {
    $msg = "Your Login OTP " . $otp . " Don't Share with any one Thanks!";
    $sent = sendsms($mobile, $msg);
    return true;
}
if (!function_exists('base64url_encode')) {
    function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}

function getAccessTokenFromServiceAccount($jsonFilePath) {
    $json = json_decode(file_get_contents($jsonFilePath), true);

    $header = ['alg' => 'RS256', 'typ' => 'JWT'];
    $iat = time();
    $exp = $iat + 3600;

    $claims = [
        'iss' => $json['client_email'],
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud' => 'https://oauth2.googleapis.com/token',
        'iat' => $iat,
        'exp' => $exp
    ];

    // Use existing global base64url_encode()
    $jwtHeader = base64url_encode(json_encode($header));
    $jwtClaims = base64url_encode(json_encode($claims));
    $signatureInput = "$jwtHeader.$jwtClaims";

    openssl_sign($signatureInput, $signature, $json['private_key'], 'sha256WithRSAEncryption');
    $jwtSignature = base64url_encode($signature);

    $jwt = "$jwtHeader.$jwtClaims.$jwtSignature";

    $postFields = http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ]);

    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    $response = curl_exec($ch);
    if ($response === false) {
        die('Token request failed: ' . curl_error($ch));
    }
    curl_close($ch);

    $responseData = json_decode($response, true);
    return $responseData['access_token'] ?? null;
}


function sendPushNotification($fields, $jsonPath) {
    $fcmurl = 'https://fcm.googleapis.com/v1/projects/bharat-e-store/messages:send';
    $accessToken = getAccessTokenFromServiceAccount($jsonPath);
    $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ];
    // Properly format payload for HTTP v1 API
    $payload = [
        'message' => [
            'token' => $fields['message']['to'],
            'notification' => [
                'title' => $fields['message']['notification']['title'],
                'body'  => $fields['message']['notification']['body'],
                'image' => $fields['message']['notification']['image'] ?? null, // optional image
            ],
            'data' => array_merge($fields['message']['data'], [
                'title'             => $fields['message']['notification']['title'],
                'body'              => $fields['message']['notification']['body'],
                'notification_type' => $fields['message']['data']['notification_type'],
                'image'             => $fields['message']['notification']['image'] ?? '', // optional image
            ]),
            'android' => [
                'notification' => [
                    'click_action' => $fields['message']['android']['notification']['click_action'] ?? 'OPEN_NOTIFICATION'
                ]
            ],
            'apns' => [
                'payload' => [
                    'aps' => [
                        'category' => 'NEW_MESSAGE_CATEGORY'
                    ]
                ]
            ]
        ]
    ];



    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fcmurl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    $result = curl_exec($ch);
    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }
    curl_close($ch);
    return $result;
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

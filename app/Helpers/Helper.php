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
// if (!function_exists("homeSetting")) {
//     function homeSetting($select) {
//         $home = DB::table('homesetting')->select($select)->first();
//         // Check if data is found before returning
//         if ($home) {
//             return $home;
//         }
//         return null; // Return null if no data found
//     }
// }

if (!function_exists('convertImageToWebp')) {
    function convertImageToWebp($folderPath, $uploaded_file_name, $new_webp_file) {
        $source = $folderPath . '/' . $uploaded_file_name;
        $extension = pathinfo($source, PATHINFO_EXTENSION);
        $quality = 100;
        $image = '';
        if ($extension == 'jpeg' || $extension == 'jpg') {
            $image = imagecreatefromjpeg($source);
        } else if ($extension == 'gif') {
            $image = imagecreatefromgif($source);
        } else if ($extension == 'png') {
            $image = imagecreatefrompng($source);
            imagepalettetotruecolor($image);
        } else {
            $image = $uploaded_file_name;
        }
        $destination = $folderPath . '/' . $new_webp_file;
        $webp_upload_done = imagewebp($image, $destination, $quality);
        return $webp_upload_done ? $new_webp_file : '';
    }
}
function FetchExactBrowserName() {
    $userAgent = strtolower(request()->header('User-Agent'));
    if (strpos($userAgent, "opr/") !== false) {
        return "Opera";
    } elseif (strpos($userAgent, "chrome/") !== false) {
        return "Chrome";
    } elseif (strpos($userAgent, "edg/") !== false) {
        return "Microsoft Edge";
    } elseif (strpos($userAgent, "msie") !== false || strpos($userAgent, "trident/") !== false) {
        return "Internet Explorer";
    } elseif (strpos($userAgent, "firefox/") !== false) {
        return "Firefox";
    } elseif (strpos($userAgent, "safari/") !== false && strpos($userAgent, "chrome/") === false) {
        return "Safari";
    } else {
        return "Unknown";
    }
}
function imgExtension($image_jpg_png_gif, $image_webp = null) {
    $browserName = FetchExactBrowserName();
    if ($browserName === "Chrome" && !empty($image_webp)) {
        return $image_webp;
    } elseif ($browserName === "Safari" && !empty($image_webp)) {
        return $image_webp;
    } else {
        return $image_jpg_png_gif;
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
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@#$&!=+';
    return substr(str_shuffle($chars), 0, $length);
}
function testInput($input) {
    $input = trim($input);
    $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $input = filter_var($input, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
    return $input;
}
if (!function_exists("generateProductSchema")) {
    function generateProductSchema($name = false, $image = false, $description = false) {
        // $ratingValue = random_int(40, 50) / 10;
        $ratingValue = (int)4;
        $settingData = webSetting(['logo','site_name']);
        $brandname = $settingData->site_name;
        if (!empty($image)) {
           $imagePath = asset('uploads/' . $image);
        } else {
            $imagePath = asset('uploads/' . $settingData->logo);
        }
        $schema = '';
        $schema.= '<script type="application/ld+json">' . "\n";
        $schema.= '{' . "\n";
        $schema.= '"@context": "https://schema.org/",' . "\n";
        $schema.= '"@type": "Product", ' . "\n";
        $schema.= '"name": "' . $name . '",' . "\n";
        $schema.= '"image": "' . $imagePath . '",' . "\n";
        $schema.= '"description": "' . $description . '",' . "\n";
        $schema.= '"brand": {' . "\n";
        $schema.= '"@type": "Brand",' . "\n";
        $schema.= '"name": "' . $brandname . '"' . "\n";
        $schema.= '},' . "\n";
        $schema.= '"aggregateRating": {' . "\n";
        $schema.= '"@type": "AggregateRating",' . "\n";
        $schema.= '"ratingValue": "' . $ratingValue . '",' . "\n";
        $schema.= '"bestRating": "5",' . "\n";
        $schema.= '"worstRating": "1",' . "\n";
        $schema.= '"ratingCount": "' . random_int(700, 1500) . '"' . "\n";
        $schema.= '}' . "\n";
        $schema.= '}' . "\n";
        $schema.= '</script>' . "\n";
        return $schema;
    }
}
if (!function_exists("generateFaqSchema")) {
    function generateFaqSchema($faqData) {
        if (!empty($faqData)) {
            $count = count($faqData);
            $schema = ["@context" => "https://schema.org/", "@type" => "FAQPage", "mainEntity" => []];
            foreach ($faqData as $index => $faqItem) {
                $question = strip_tags($faqItem['question']);
                $answer = strip_tags($faqItem['answer']);
                $schema['mainEntity'][] = ["@type" => "Question", "name" => $question, "acceptedAnswer" => ["@type" => "Answer", "text" => $answer]];
            }
            $jsonSchema = json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $scriptTag = '<script type="application/ld+json">' . PHP_EOL . $jsonSchema . PHP_EOL . '</script>' . PHP_EOL;
            return $scriptTag;
        } else {
            return false;
        }
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
    function getUserCurrency() {
        if (Auth::check()) {
            $userCountry = Auth::user()->country;
            $currency = DB::table('country')->where('country_name', $userCountry)->value('country_currency_symbol');
            return $currency;
        }
    }
}


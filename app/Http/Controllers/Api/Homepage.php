<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
class Homepage extends Controller {
    public function index(Request $request) {
        checkHeaders();
        // Use IP address for basic rate-limiting (or set customer_id if available)
        $ip = $request->ip();
        $cooldownKey = "cooldown:banners:" . $ip;
        if (Cache::has($cooldownKey)) {
            return response()->json(['status' => false, 'message' => 'Too many requests. Please wait a few seconds.'], 429);
        }
        Cache::add($cooldownKey, true, now()->addSeconds(3));
        // Use cache key to store response for 2 minutes
        $cacheKey = 'banner_list:all';
        if (Cache::has($cacheKey)) {
            return response()->json(['status' => true, 'banner' => Cache::get($cacheKey), 'message' => 'API Accessed Successfully (cached)']);
        }
        $record = DB::table('banner_list')->leftJoin('categories', 'banner_list.category_id', '=', 'categories.id')->leftJoin('subcategories', 'banner_list.subcategory_id', '=', 'subcategories.id')->select('banner_list.id', 'banner_list.category_id', 'banner_list.subcategory_id', 'banner_list.image', 'categories.category_name', 'subcategories.subcategory_name')->get();
        if ($record->isEmpty()) {
            return response()->json(['status' => false, 'message' => "No Records Found"]);
        }
        $returnData = [];
        foreach ($record as $value) {
            $returnData[] = ['banner_id' => (string)$value->id, 'category_id' => (string)$value->category_id, 'category_name' => (string)$value->category_name, 'subcategory_id' => (string)$value->subcategory_id, 'subcategory_name' => (string)$value->subcategory_name, 'image' => url('uploads/' . $value->image), ];
        }
        // Cache for 2 minutes
        Cache::put($cacheKey, $returnData, now()->addMinutes(2));
        return response()->json(['status' => true, 'banner' => $returnData, 'message' => 'API Accessed Successfully']);
    }
    public function categoryList() {
        checkHeaders();
        $category = DB::table('categories')->where('status', 'Active')->select('category_name', 'category_image', 'id')->get();
        if (empty($category)) {
            $response['status'] = false;
            $response['message'] = "No Records Found";
            return response()->json($response);
        }
        $returnData = [];
        foreach ($category as $key => $value) {
            $return['category_id'] = (string)$value->id;
            $return['category_name'] = (string)$value->category_name;
            $return['category_image'] = url('uploads/' . $value->category_image);
            array_push($returnData, $return);
        }
        $response['status'] = true;
        $response['data'] = $returnData;
        $response['message'] = "API Accessed Successfully!";
        return response()->json($response);
    }
    public function subcategoryList() {
        $post = checkPayload();
        $category_id = trim($post['category_id']??'');
        $where = [];
        $where['status'] = 'Active';
        $where['category'] = $category_id;
        $subcategory = DB::table('subcategories')->where($where)->select('subcategory_name', 'subcategory_image', 'id', 'category')->get();
        if (empty($subcategory)) {
            $response['status'] = false;
            $response['message'] = "No Records Found";
            return response()->json($response);
        }
        $returnData = [];
        foreach ($subcategory as $key => $value) {
            $return['subcategory_id'] = (string)$value->id;
            $return['category_id'] = (string)$value->category;
            $return['subcategory_name'] = (string)$value->subcategory_name;
            $return['subcategory_image'] = url('uploads/' . $value->subcategory_image);
            array_push($returnData, $return);
        }
        $response['status'] = true;
        $response['data'] = $returnData;
        $response['message'] = "API Accessed Successfully!";
        return response()->json($response);
    }
    public function trendingProducts() {
        $post = checkPayload();
        $customer_id = trim($post['customer_id']??'');
        $condition = trim($post['condition']??'');
        $per_page_limit = intval($post['per_page_limit']??10); // Default to 10
        $page_no = intval($post['page_no']??1); // Default to 1
        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => "Customer Id Is Blank"]);
        }
        // Cooldown check (per user)
        $cooldownKey = "cooldown:trending_products:{$customer_id}";
        if (Cache::has($cooldownKey)) {
            return response()->json(['status' => false, 'message' => 'Too many requests. Please wait a few seconds.'], 429);
        }
        Cache::add($cooldownKey, true, now()->addSeconds(3));
        $customer = DB::table('customers')->find($customer_id);
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }
        if ($customer->profile_status === "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }
        if (!empty($condition) && $condition !== 'all') {
            return response()->json(['status' => false, 'message' => "Invalid Condition"]);
        }
        $customerCurrency = getUserCurrency($customer_id) ??'';
        // Generate cache key
        $cacheKey = "trending_products:{$customer_id}:{$condition}:{$per_page_limit}:{$page_no}";
        if (Cache::has($cacheKey)) {
            return response()->json(['status' => true, 'data' => Cache::get($cacheKey), 'message' => "API Accessed Successfully! (cached)", ]);
        }
        // Base query
        $where = ['products.status' => 'Active', 'products.is_trending' => 'yes'];
        $query = DB::table('products')->join('categories', 'categories.id', '=', 'products.category_id')->where($where)->select('products.*', 'categories.category_name');
        if (!empty($condition)) {
            $offset = ($page_no - 1) * $per_page_limit;
            $query->limit($per_page_limit)->offset($offset);
        } else {
            $query->limit(10); // Default trending products if no condition
            
        }
        $products = $query->get();
        if ($products->isEmpty()) {
            return response()->json(['status' => false, 'message' => "No Records Found"]);
        }
        $returnData = $products->map(function ($value) use ($customerCurrency) {
            $images = $value->product_image ? json_decode($value->product_image, true) : [];
            $imageUrls = [];
            foreach ($images as $imageArray) {
                if (isset($imageArray['image'])) {
                    $imageUrls[] = url('uploads/' . $imageArray['image']);
                }
            }
            return ['product_id' => (string)$value->id, 'category_id' => (string)$value->category_id, 'subcategory_id' => (string)$value->subcategory_id, 'product_name' => (string)$value->product_name, 'product_rating' => (string)$value->product_rating, 'product_selling_price' => $customerCurrency . ' ' . (string)$value->product_selling_price, 'product_cost_price' => $customerCurrency . ' ' . (string)$value->product_cost_price, 'category_name' => (string)$value->category_name, 'product_image' => $imageUrls, 'added_to_wishlist' => strtolower($value->added_to_wishlist) === 'true', ];
        });
        // Cache the response
        Cache::put($cacheKey, $returnData, now()->addMinutes(2));
        return response()->json(['status' => true, 'data' => $returnData, 'message' => "API Accessed Successfully!"]);
    }
    public function search() {
        $post = checkPayload();
        $keyword = trim($post['keyword']??'');
        if (empty($keyword)) {
            return response()->json(['status' => false, 'message' => "Keyword is required"]);
        }
        // Search Products
        $products = DB::table('products')->leftJoin('categories', 'products.category_id', '=', 'categories.id')->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')->where(function ($query) use ($keyword) {
            $query->where('products.product_name', 'like', "%{$keyword}%")->orWhere('products.product_description', 'like', "%{$keyword}%")->orWhere('categories.category_name', 'like', "%{$keyword}%")->orWhere('subcategories.subcategory_name', 'like', "%{$keyword}%");
        })->select('products.id', 'products.product_name', 'products.product_rating', 'products.product_image', 'products.category_id', 'products.subcategory_id', 'categories.category_name', 'subcategories.subcategory_name')->get();
        // Search Categories
        $categories = DB::table('categories')->where('category_name', 'like', "%{$keyword}%")->select('id', 'category_name')->get();
        // Search Subcategories
        $subcategories = DB::table('subcategories')->where('subcategory_name', 'like', "%{$keyword}%")->select('id', 'subcategory_name')->get();
        if ($products->isEmpty() && $categories->isEmpty() && $subcategories->isEmpty()) {
            return response()->json(['status' => false, 'message' => "No matching records found"]);
        }
        $productData = $products->map(function ($product) {
            return ['type' => 'product', 'product_id' => (string)$product->id, 'product_name' => (string)$product->product_name, 'product_rating' => (string)$product->product_rating, 'product_image' => url('uploads/' . $product->product_image), 'category_id' => (string)$product->category_id, 'subcategory_id' => (string)$product->subcategory_id, 'category_name' => (string)$product->category_name, 'subcategory_name' => (string)$product->subcategory_name, ];
        });
        $categoryData = $categories->map(function ($category) {
            return ['type' => 'category', 'category_id' => (string)$category->id, 'category_name' => (string)$category->category_name, ];
        });
        $subcategoryData = $subcategories->map(function ($subcategory) {
            return ['type' => 'subcategory', 'subcategory_id' => (string)$subcategory->id, 'subcategory_name' => (string)$subcategory->subcategory_name, ];
        });
        // Suggestions for autocomplete (limit to unique 10 max)
        $suggestions = collect()->merge($products->pluck('product_name'))->merge($products->pluck('category_name'))->merge($products->pluck('subcategory_name'))->merge($categories->pluck('category_name'))->merge($subcategories->pluck('subcategory_name'))->filter()->unique()->values()->take(10);
        return response()->json(['status' => true, 'message' => 'Search results found', 'data' => $productData->merge($categoryData)->merge($subcategoryData)->values(), 'suggestions' => $suggestions, ]);
    }
    public function referralHistory() {
        $post = checkPayload();
        $customer_id = trim($post['customer_id']??'');
        $per_page_limit = intval($post['per_page_limit']??10); // Default to 10
        $page_no = intval($post['page_no']??1); // Default to 1
        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => "Customer ID Is Blank", ]);
        }
        $customer = DB::table('customers')->find($customer_id);
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }
        if ($customer->profile_status === "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }
        $offset = ($page_no - 1) * $per_page_limit;
        $referralHistory = DB::table('referral_history')->join('customers', 'referral_history.referral_customer_id', '=', 'customers.id')->where('referral_history.referral_customer_id', $customer_id)->select('referral_history.id as referral_id', 'customers.customer_name', 'referral_history.points')->offset($offset)->limit($per_page_limit)->get();
        if ($referralHistory->isEmpty()) {
            return response()->json(['status' => false, 'message' => "No records found", ]);
        }
        $returnData = $referralHistory->map(function ($value) {
            return ['referral_history_id' => (string)$value->referral_id, 'customer_name' => (string)$value->customer_name, 'points' => (string)$value->points, ];
        });
        return response()->json(['status' => true, 'data' => $returnData, 'message' => "API Accessed Successfully!", ]);
    }
    public function productDetail() {
        $post = checkPayload();
        $product_id = trim($post['product_id']??'');
        $customer_id = trim($post['customer_id']??'');
        if (empty($product_id)) {
            return response()->json(['status' => false, 'message' => "Product ID is blank"]);
        }
        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => "Customer ID Is Blank"]);
        }
        // Manual Cooldown: Prevent frequent calls from the same customer
        $cooldownKey = "cooldown:product_detail:{$customer_id}";
        if (Cache::has($cooldownKey)) {
            return response()->json(['status' => false, 'message' => 'Too many requests. Please wait a few seconds.'], 429);
        }
        Cache::add($cooldownKey, true, now()->addSeconds(3)); // 3-second cooldown
        // Caching the response to reduce DB load
        $cacheKey = "product_detail_{$customer_id}_{$product_id}";
        $cachedData = Cache::get($cacheKey);
        if ($cachedData) {
            return response()->json(['status' => true, 'message' => 'API Accessed Successfully (cached)', 'data' => $cachedData]);
        }
        $customer = DB::table('customers')->find($customer_id);
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }
        if ($customer->profile_status === "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }
        $customerCurrency = getUserCurrency($customer_id);
        $where = ['products.status' => 'Active', 'products.id' => $product_id];
        $product = DB::table('products')->join('categories', 'categories.id', '=', 'products.category_id')->join('subcategories', 'subcategories.id', '=', 'products.subcategory_id')->where($where)->select('products.*', 'categories.category_name', 'subcategories.subcategory_name')->first();
        if (empty($product)) {
            return response()->json(['status' => false, 'message' => "Product Not Found"]);
        }
        $images = $product->product_image ? json_decode($product->product_image, true) : [];
        $imageUrls = [];
        if (!empty($images)) {
            foreach ($images as $imageArray) {
                if (isset($imageArray['image'])) {
                    $imageUrls[] = url('uploads/' . $imageArray['image']);
                }
            }
        }
        $returnData = ['product_id' => (string)$product->id, 'category_name' => (string)$product->category_name, 'subcategory_name' => (string)$product->subcategory_name, 'product_name' => (string)$product->product_name, 'product_description' => (string)$product->product_description, 'product_size' => !empty($product->product_size) ? array_map('trim', explode(',', $product->product_size)) : [], 'product_colors' => !empty($product->product_colors) ? array_values(array_filter(array_map(function ($color) {
            $parts = array_map('trim', explode('-', $color));
            if (count($parts) === 2) {
                return ['color_name' => $parts[0], 'color_code' => $parts[1]];
            }
            return null;
        }, explode(',', $product->product_colors)))) : [], 'product_image' => $imageUrls, 'product_selling_price' => $customerCurrency . ' ' . (string)$product->product_selling_price, 'product_cost_price' => $customerCurrency . ' ' . (string)$product->product_cost_price, 'product_quantity' => (string)$product->product_quantity, 'product_availability' => (string)$product->product_availability, 'product_rating' => (string)$product->product_rating, 'is_trending' => (string)$product->is_trending, 'product_status' => (string)$product->status, 'added_to_wishlist' => strtolower($product->added_to_wishlist) === 'true', 'product_off' => (string)$product->product_off, ];
        // Store result in cache for 2 minutes
        Cache::put($cacheKey, $returnData, now()->addMinutes(2));
        return response()->json(['status' => true, 'message' => 'API Accessed Successfully', 'data' => $returnData]);
    }
    public function hotDealsProducts() {
        $post = checkPayload();
        $customer_id = trim($post['customer_id']??'');
        $condition = trim($post['condition']??'');
        $per_page_limit = intval($post['per_page_limit']??10);
        $page_no = intval($post['page_no']??1);
        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => "Customer Id Is Blank"]);
        }
        // Manual Cooldown (3 seconds)
        $cooldownKey = "cooldown:hot_deals:{$customer_id}";
        if (Cache::has($cooldownKey)) {
            return response()->json(['status' => false, 'message' => 'Too many requests. Please wait a few seconds.'], 429);
        }
        Cache::add($cooldownKey, true, now()->addSeconds(3));
        // Cache Key for unique request
        $cacheKey = "hot_deals_{$customer_id}_{$page_no}_{$per_page_limit}_{$condition}";
        if (Cache::has($cacheKey)) {
            return response()->json(['status' => true, 'data' => Cache::get($cacheKey), 'message' => "API Accessed Successfully! (cached)"]);
        }
        $customer = DB::table('customers')->find($customer_id);
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }
        if ($customer->profile_status === "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }
        $customerCurrency = getUserCurrency($customer_id) ??'';
        if (!empty($condition) && $condition !== 'all') {
            return response()->json(['status' => false, 'message' => "Invalid Condition"]);
        }
        $where = ['products.status' => 'Active', 'products.is_hot_deal' => 'yes'];
        $query = DB::table('products')->join('categories', 'categories.id', '=', 'products.category_id')->where($where)->select('products.*', 'categories.category_name');
        if (!empty($condition)) {
            $offset = ($page_no - 1) * $per_page_limit;
            $query->limit($per_page_limit)->offset($offset);
        } else {
            $query->limit(10);
        }
        $products = $query->get();
        if ($products->isEmpty()) {
            return response()->json(['status' => false, 'message' => "No Records Found"]);
        }
        $returnData = $products->map(function ($value) use ($customerCurrency) {
            $images = $value->product_image ? json_decode($value->product_image, true) : [];
            $imageUrls = [];
            if (!empty($images)) {
                foreach ($images as $imageArray) {
                    if (isset($imageArray['image'])) {
                        $imageUrls[] = url('uploads/' . $imageArray['image']);
                    }
                }
            }
            return ['product_id' => (string)$value->id, 'category_id' => (string)$value->category_id, 'subcategory_id' => (string)$value->subcategory_id, 'product_name' => (string)$value->product_name, 'product_rating' => (string)$value->product_rating, 'product_selling_price' => $customerCurrency . ' ' . (string)$value->product_selling_price, 'product_cost_price' => $customerCurrency . ' ' . (string)$value->product_cost_price, 'category_name' => (string)$value->category_name, 'product_image' => $imageUrls, 'added_to_wishlist' => strtolower($value->added_to_wishlist) === 'true', ];
        });
        // Cache for 2 minutes
        Cache::put($cacheKey, $returnData, now()->addMinutes(2));
        return response()->json(['status' => true, 'data' => $returnData, 'message' => "API Accessed Successfully!"]);
    }
    public function categoryProducts() {
        $post = checkPayload();
        $customer_id = trim($post['customer_id']??'');
        $category_id = trim($post['category_id']??'');
        $per_page_limit = intval($post['per_page_limit']??10); // Default to 10
        $page_no = intval($post['page_no']??1); // Default to 1
        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => "Customer Id Is Blank"]);
        }
        // Validate condition
        if (empty($category_id)) {
            return response()->json(['status' => false, 'message' => "Category Id Is Blank"]);
        }
        $customer = DB::table('customers')->find($customer_id);
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }
        if ($customer->profile_status === "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }
        $customerCurrency = getUserCurrency($customer_id) ??'';
        // Base query
        $where = ['products.status' => 'Active', 'products.category_id' => $category_id];
        $query = DB::table('products')->join('categories', 'categories.id', '=', 'products.category_id')->where($where)->select('products.*', 'categories.category_name');
        // Pagination
        $offset = ($page_no - 1) * $per_page_limit;
        $query->limit($per_page_limit)->offset($offset);
        $products = $query->get();
        if ($products->isEmpty()) {
            return response()->json(['status' => false, 'message' => "No Records Found"]);
        }
        // Format data
        $returnData = $products->map(function ($value) use ($customerCurrency) {
            $images = json_decode($value->product_image, true); // decode as array
            $imageUrls = [];
            foreach ($images as $imageArray) {
                if (isset($imageArray['image'])) {
                    $imageUrls[] = url('uploads/' . $imageArray['image']);
                }
            }
            return ['product_id' => (string)$value->id, 'category_id' => (string)$value->category_id, 'subcategory_id' => (string)$value->subcategory_id, 'product_name' => (string)$value->product_name, 'product_rating' => (string)$value->product_rating, 'product_selling_price' => $customerCurrency . ' ' . (string)$value->product_selling_price, 'product_cost_price' => $customerCurrency . ' ' . (string)$value->product_cost_price, 'category_name' => (string)$value->category_name, 'product_image' => $imageUrls, 'added_to_wishlist' => strtolower($value->added_to_wishlist) === 'true', ];
        });
        return response()->json(['status' => true, 'data' => $returnData, 'message' => "API Accessed Successfully!", ]);
    }
    public function subCategoryProducts() {
        $post = checkPayload();
        $customer_id = trim($post['customer_id']??'');
        $category_id = trim($post['category_id']??'');
        $subcategory_id = trim($post['subcategory_id']??'');
        $per_page_limit = intval($post['per_page_limit']??10); // Default to 10
        $page_no = intval($post['page_no']??1); // Default to 1
        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => "Customer Id Is Blank"]);
        }
        // Validate condition
        if (empty($category_id)) {
            return response()->json(['status' => false, 'message' => "Category Id Is Blank"]);
        }
        if (empty($subcategory_id)) {
            return response()->json(['status' => false, 'message' => "Subcategory Id Is Blank"]);
        }
        $customer = DB::table('customers')->find($customer_id);
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }
        if ($customer->profile_status === "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }
        $customerCurrency = getUserCurrency($customer_id) ??'';
        // Base query
        $where = ['products.status' => 'Active', 'products.category_id' => $category_id, 'products.subcategory_id' => $subcategory_id];
        $query = DB::table('products')->join('categories', 'categories.id', '=', 'products.category_id')->where($where)->select('products.*', 'categories.category_name');
        // Pagination
        $offset = ($page_no - 1) * $per_page_limit;
        $query->limit($per_page_limit)->offset($offset);
        $products = $query->get();
        if ($products->isEmpty()) {
            return response()->json(['status' => false, 'message' => "No Records Found"]);
        }
        // Format data
        $returnData = $products->map(function ($value) use ($customerCurrency) {
            $images = json_decode($value->product_image, true); // decode as array
            $imageUrls = [];
            foreach ($images as $imageArray) {
                if (isset($imageArray['image'])) {
                    $imageUrls[] = url('uploads/' . $imageArray['image']);
                }
            }
            return ['product_id' => (string)$value->id, 'category_id' => (string)$value->category_id, 'subcategory_id' => (string)$value->subcategory_id, 'product_name' => (string)$value->product_name, 'product_rating' => (string)$value->product_rating, 'product_selling_price' => $customerCurrency . ' ' . (string)$value->product_selling_price, 'product_cost_price' => $customerCurrency . ' ' . (string)$value->product_cost_price, 'category_name' => (string)$value->category_name, 'product_image' => $imageUrls, 'added_to_wishlist' => strtolower($value->added_to_wishlist) === 'true', ];
        });
        return response()->json(['status' => true, 'data' => $returnData, 'message' => "API Accessed Successfully!", ]);
    }
    public function helpSupport() {
        checkHeaders();
        $response = [];
        $record = DB::table('websetting')->select('email', 'mobile_number', 'whatsapp_number')->first();
        if (empty($record)) {
            $response['status'] = false;
            $response['message'] = "No Records Found";
            return response()->json($response);
        }
        $returnData = [];
        $returnData['email'] = (string)$record->email;
        $returnData['mobile_number'] = (string)$record->mobile_number;
        $returnData['whatsapp_number'] = (string)$record->whatsapp_number;
        $response['status'] = true;
        $response['message'] = "API Accessed Successfully";
        $response['data'] = $returnData;
        return response()->json($response);
    }
    public function reviewProduct(Request $request) {
        // Call your custom request validator
        if ($error = validateApiRequest($request)) {
            return $error;
        }
        // Extract input
        $customer_id = $request->input('customer_id');
        $product_id = $request->input('product_id');
        $review = $request->input('review');
        $rating = $request->input('rating');
        // Basic validation
        if (!$customer_id) {
            return response()->json(['status' => false, 'message' => 'Customer ID is blank']);
        }
        if (!$product_id) {
            return response()->json(['status' => false, 'message' => 'Product ID is blank']);
        }
        if (!$review) {
            return response()->json(['status' => false, 'message' => 'Review is blank']);
        }
        if (!$rating) {
            return response()->json(['status' => false, 'message' => 'Rating is blank']);
        }
        // Validate customer existence and status
        $customer = DB::table('customers')->find($customer_id);
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }
        if ($customer->profile_status === 'Inactive') {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }
        // Validate product existence
        $product = DB::table('products')->find($product_id);
        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found']);
        }
        // Check if the customer already reviewed this product
        $existingReview = DB::table('reviews')->where('customer_id', $customer_id)->where('product_id', $product_id)->first();
        if ($existingReview) {
            return response()->json(['status' => false, 'message' => 'You have already submitted a review for this product']);
        }
        // Handle optional uploaded images
        $img_data = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file->isValid()) {
                    $filename = $file->hashName();
                    $file->move(public_path('uploads/'), $filename);
                    $img_data[] = ['image' => $filename];
                }
            }
        }
        // Insert review into database
        DB::table('reviews')->insert(['customer_id' => $customer_id, 'product_id' => $product_id, 'review' => $review, 'rating' => $rating, 'images' => json_encode($img_data), 'created_at' => Carbon::now(), ]);
        return response()->json(['status' => true, 'message' => 'Review added successfully']);
    }
    public function similiarProducts() {
        $post = checkPayload();
        $customer_id = trim($post['customer_id']??'');
        $product_id = trim($post['product_id']??'');
        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => 'Customer ID is blank']);
        }
        if (empty($product_id)) {
            return response()->json(['status' => false, 'message' => 'Product ID is blank']);
        }
        // Manual Cooldown (3 seconds)
        $cooldownKey = "cooldown:similar_products:{$customer_id}";
        if (Cache::has($cooldownKey)) {
            return response()->json(['status' => false, 'message' => 'Too many requests. Please wait a few seconds.'], 429);
        }
        Cache::add($cooldownKey, true, now()->addSeconds(3));
        // Response Cache (2 minutes)
        $cacheKey = "similar_products_{$customer_id}_{$product_id}";
        if (Cache::has($cacheKey)) {
            return response()->json(['status' => true, 'data' => Cache::get($cacheKey), 'message' => 'API Accessed Successfully! (cached)']);
        }
        $customer = DB::table('customers')->find($customer_id);
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }
        if ($customer->profile_status === 'Inactive') {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }
        $product = DB::table('products')->find($product_id);
        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found']);
        }
        $similarProducts = DB::table('products')->leftJoin('categories', 'products.category_id', '=', 'categories.id')->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')->where('products.id', '!=', $product_id)->where('products.category_id', $product->category_id)->where('products.subcategory_id', $product->subcategory_id)->where('products.status', 'Active')->select('products.*', 'categories.category_name', 'subcategories.subcategory_name')->get();
        if ($similarProducts->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'No records found']);
        }
        $customerCurrency = getUserCurrency($customer_id) ??'';
        $returnData = $similarProducts->map(function ($value) use ($customerCurrency) {
            $images = $value->product_image ? json_decode($value->product_image, true) : [];
            $firstImageUrl = !empty($images) && isset($images[0]['image']) ? url('uploads/' . $images[0]['image']) : null;
            return ['product_id' => (string)$value->id, 'category_id' => (string)$value->category_id, 'subcategory_id' => (string)$value->subcategory_id, 'product_name' => (string)$value->product_name, 'product_rating' => (string)$value->product_rating, 'product_selling_price' => $customerCurrency . ' ' . (string)$value->product_selling_price, 'product_cost_price' => $customerCurrency . ' ' . (string)$value->product_cost_price, 'category_name' => (string)$value->category_name, 'subcategory_name' => (string)$value->subcategory_name, 'product_image' => $firstImageUrl, 'added_to_wishlist' => strtolower((string)$value->added_to_wishlist) === 'true', ];
        });
        // Store to cache
        Cache::put($cacheKey, $returnData, now()->addMinutes(2));
        return response()->json(['status' => true, 'data' => $returnData, 'message' => 'API Accessed Successfully!', ]);
    }
    public function productFaqs() {
        $post = checkPayload();
        $product_id = trim($post['product_id']??'');
        if (empty($product_id)) {
            return response()->json(['status' => false, 'message' => 'Product ID is blank']);
        }
        // Cooldown key: prevents flood
        $cooldownKey = "cooldown:product_faqs:{$product_id}";
        if (Cache::has($cooldownKey)) {
            return response()->json(['status' => false, 'message' => 'Too many requests. Please wait a few seconds.'], 429);
        }
        Cache::add($cooldownKey, true, now()->addSeconds(3));
        // Cache key
        $cacheKey = "product_faqs:{$product_id}";
        if (Cache::has($cacheKey)) {
            return response()->json(['status' => true, 'message' => "API accessed successfully! (cached)", 'data' => Cache::get($cacheKey), ]);
        }
        $product = DB::table('products')->find($product_id);
        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found']);
        }
        $faqs = DB::table('faqs')->where('table_name', 'products')->where('table_id', $product_id)->get();
        if ($faqs->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'No FAQs found']);
        }
        $returnData = $faqs->map(function ($value) {
            return ['faq_id' => (string)$value->id, 'question' => (string)$value->question, 'answer' => (string)$value->answer];
        });
        // Save to cache
        Cache::put($cacheKey, $returnData, now()->addMinutes(2));
        return response()->json(['status' => true, 'message' => "API accessed successfully!", 'data' => $returnData, ]);
    }
    public function myReviews() {
        $post = checkPayload();
        $customer_id = trim($post['customer_id']??'');
        $product_id = trim($post['product_id']??'');
        $per_page_limit = intval($post['per_page_limit']??10); // Default to 10
        $page_no = intval($post['page_no']??1); // Default to 1
        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => 'Customer ID is blank']);
        }
        if (empty($product_id)) {
            return response()->json(['status' => false, 'message' => 'Product ID is blank']);
        }
        $customer = DB::table('customers')->find($customer_id);
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }
        if ($customer->profile_status === "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }
        $offset = ($page_no - 1) * $per_page_limit;
        $reviews = DB::table('reviews')->where('customer_id', $customer_id)->where('product_id', $product_id)->select('id as review_id', 'review', 'rating', 'images', 'product_id', 'customer_id')->offset($offset)->limit($per_page_limit)->get();
        if ($reviews->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'No records found']);
        }
        $returnData = [];
        foreach ($reviews as $review) {
            $images = json_decode($review->images, true) ??[];
            $imageUrls = [];
            foreach ($images as $imageArray) {
                if (!empty($imageArray['image'])) {
                    $imageUrls[] = url('uploads/' . $imageArray['image']);
                }
            }
            $returnData[] = ['review_id' => (string)$review->review_id, 'review' => (string)$review->review, 'rating' => (string)$review->rating, 'customer_id' => (string)$review->customer_id, 'product_id' => (string)$review->product_id, 'images' => $imageUrls];
        }
        return response()->json(['status' => true, 'data' => $returnData, 'message' => 'API accessed successfully!']);
    }
}

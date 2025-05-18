<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class Homepage extends Controller {
    public function index(Request $request) {
        checkHeaders();
        $record = DB::table('websetting')->select('banner')->first();
        $banner = !empty($record->banner) ? json_decode($record->banner) : [];
        if (empty($banner)) {
            $response['status'] = false;
            $response['message'] = "No Records Found";
            return response()->json($response);
        }
        $returnData = [];
        foreach ($banner as $key => $value) {
            $return['image'] = url('uploads/' . $value->image);
            array_push($returnData, $return);
        }
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
        $customerCurrency = getUserCurrency($customer_id) ??'';
        // Validate condition
        if (!empty($condition) && $condition !== 'all') {
            return response()->json(['status' => false, 'message' => "Invalid Condition"]);
        }
        // Base query
        $where = ['products.status' => 'Active', 'products.is_trending' => 'yes', ];
        $query = DB::table('products')->join('categories', 'categories.id', '=', 'products.category_id')->where($where)->select('products.*', 'categories.category_name');
        // Pagination
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
        // Format data
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

            return ['product_id' => (string)$value->id, 
                    'category_id' => (string)$value->category_id, 
                    'subcategory_id' => (string)$value->subcategory_id,
                    'product_name' => (string)$value->product_name, 
                    'product_rating' => (string)$value->product_rating, 
                    'product_selling_price' => $customerCurrency . (string)$value->product_selling_price, 
                    'product_cost_price' => $customerCurrency . (string)$value->product_cost_price, 
                    'category_name' => (string)$value->category_name, 
                    'product_image' => $imageUrls, 
                    'added_to_wishlist' => (bool)$value->added_to_wishlist];
        });
        return response()->json(['status' => true, 'data' => $returnData, 'message' => "API Accessed Successfully!", ]);
    }
    public function search() {
        $post = checkPayload();
        $keyword = trim($post['keyword']??'');
        if (empty($keyword)) {
            return response()->json(['status' => false, 'message' => "Keyword is blank"]);
        }
        $products = DB::table('products')->leftJoin('categories', 'products.category_id', '=', 'categories.id')->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')->where(function ($query) use ($keyword) {
            $query->where('products.product_name', 'like', "%{$keyword}%")->orWhere('products.product_description', 'like', "%{$keyword}%")->orWhere('categories.category_name', 'like', "%{$keyword}%")->orWhere('subcategories.subcategory_name', 'like', "%{$keyword}%");
        })->select('products.*', 'categories.category_name as category_name', 'subcategories.subcategory_name as subcategory_name')->get();
        if ($products->isEmpty()) {
            return response()->json(['status' => false, 'message' => "No record found"]);
        }
        $returnData = [];
        foreach ($products as $product) {
            $return['product_id'] = (string)$product->id;
            $return['category_id'] = (string)$product->category_id;
            $return['subcategory_id'] = (string)$product->subcategory_id;
            $return['product_name'] = (string)$product->product_name;
            $return['product_rating'] = (string)$product->product_rating;
            $return['product_image'] = url('uploads/' . $product->product_image);
            array_push($returnData, $return);
        }
        return response()->json(['status' => true, 'data' => $returnData, 'message' => "API accessed successfully!"]);
    }
    public function referralHistory() {
        $post = checkPayload();
        $customer_id = trim($post['customer_id']??'');
        $per_page_limit = intval($post['per_page_limit']??10); // Default to 10
        $page_no = intval($post['page_no']??1); // Default to 1
        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => "Customer ID is blank", ]);
        }
        $customer = DB::table('customers')->find($customer_id);
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }
        if ($customer->profile_status === "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }
        $offset = ($page_no - 1) * $per_page_limit;
        $referralHistory = DB::table('referral_history')->join('customers', 'referral_history.referral_customer_id', '=', 'customers.id')->where('referral_history.referral_customer_id', $customer_id)->select('referral_history.id as referral_id', 'customers.customer_name', 'referral_history.points')->limit($per_page_limit)->offset($offset)->get();
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
            return response()->json(['status' => false, 'message' => "Product ID is blank", ]);
        }
        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => "Customer ID is blank", ]);
        }
        $customer = DB::table('customers')->find($customer_id);
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }
        if ($customer->profile_status === "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }
        $customerCurrency = getUserCurrency($customer_id);
        $where = [];
        $where['products.status'] = 'Active';
        $where['products.id'] = $product_id;
        $product = DB::table('products')->join('categories', 'categories.id', '=', 'products.category_id')->join('subcategories', 'subcategories.id', '=', 'products.subcategory_id')->where($where)->select('products.*', 'categories.category_name', 'subcategories.subcategory_name')->first();
        if (empty($product)) {
            return response()->json(['status' => false, 'message' => "Product Not Found"]);
        }
        $returnData = [];
        $returnData['product_id'] = (string)$product->id;
        $returnData['category_name'] = (string)$product->category_name;
        $returnData['subcategory_name'] = (string)$product->subcategory_name;
        $returnData['product_name'] = (string)$product->product_name;
        $returnData['product_description'] = (string)$product->product_description;
        $returnData['product_size'] = (string)$product->product_size;
        $returnData['product_colors'] = (string)$product->product_colors;
        $returnData['product_image'] = (string)(url('uploads/' . $product->product_image));
        $returnData['product_selling_price'] = $customerCurrency . (string)$product->product_selling_price;
        $returnData['product_cost_price'] = $customerCurrency . (string)$product->product_cost_price;
        $returnData['product_quantity'] = (string)$product->product_quantity;
        $returnData['product_availability'] = (string)$product->product_availability;
        $returnData['product_rating'] = (string)$product->product_rating;
        $returnData['is_trending'] = (string)$product->is_trending;
        $returnData['product_status'] = (string)$product->status;
        return response()->json(['status' => true, 'message' => 'API Accessed Successfully', 'data' => $returnData]);
    }
    public function hotDealsProducts() {
        $post = checkPayload();
        $customer_id = trim($post['customer_id']??'');
        $condition = trim($post['condition']??'');
        $per_page_limit = intval($post['per_page_limit']??10); // Default to 10
        $page_no = intval($post['page_no']??1); // Default to 1
        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => "Customer Id Is Blank"]);
        }
        $customerCurrency = getUserCurrency($customer_id) ??'';
        // Validate condition
        if (!empty($condition) && $condition !== 'all') {
            return response()->json(['status' => false, 'message' => "Invalid Condition"]);
        }
        // Base query
        $where = ['products.status' => 'Active', 'products.is_hot_deal' => 'yes', ];
        $query = DB::table('products')->join('categories', 'categories.id', '=', 'products.category_id')->where($where)->select('products.*', 'categories.category_name');
        // Pagination
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
        // Format data
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
            return ['product_id' => (string)$value->id, 
                    'category_id' => (string)$value->category_id, 
                    'subcategory_id' => (string)$value->subcategory_id,
                    'product_name' => (string)$value->product_name, 
                    'product_rating' => (string)$value->product_rating, 
                    'product_selling_price' => $customerCurrency . (string)$value->product_selling_price, 
                    'product_cost_price' => $customerCurrency . (string)$value->product_cost_price, 
                    'category_name' => (string)$value->category_name, 
                    'product_image' => $imageUrls, 
                    'added_to_wishlist' => (bool)$value->added_to_wishlist];
        });
        return response()->json(['status' => true, 'data' => $returnData, 'message' => "API Accessed Successfully!", ]);
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
            return ['product_id' => (string)$value->id, 
                    'category_id' => (string)$value->category_id, 
                    'subcategory_id' => (string)$value->subcategory_id,
                    'product_name' => (string)$value->product_name, 
                    'product_rating' => (string)$value->product_rating, 
                    'product_selling_price' => $customerCurrency . (string)$value->product_selling_price, 
                    'product_cost_price' => $customerCurrency . (string)$value->product_cost_price, 
                    'category_name' => (string)$value->category_name, 
                    'product_image' => $imageUrls, 
                    'added_to_wishlist' => (bool)$value->added_to_wishlist];
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
        $customerCurrency = getUserCurrency($customer_id) ??'';
        
        // Base query
        $where = ['products.status' => 'Active', 'products.category_id' => $category_id,'products.subcategory_id' => $subcategory_id];
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
            return ['product_id' => (string)$value->id, 
                    'category_id' => (string)$value->category_id, 
                    'subcategory_id' => (string)$value->subcategory_id,
                    'product_name' => (string)$value->product_name, 
                    'product_rating' => (string)$value->product_rating, 
                    'product_selling_price' => $customerCurrency . (string)$value->product_selling_price, 
                    'product_cost_price' => $customerCurrency . (string)$value->product_cost_price, 
                    'category_name' => (string)$value->category_name, 
                    'product_image' => $imageUrls, 
                    'added_to_wishlist' => (bool)$value->added_to_wishlist];
        });
        return response()->json(['status' => true, 'data' => $returnData, 'message' => "API Accessed Successfully!", ]);
    }
}

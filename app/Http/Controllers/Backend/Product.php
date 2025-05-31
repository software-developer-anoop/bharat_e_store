<?php
namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class Product extends Controller {
    public function index() {
        $page_name = 'Product List';
        $data = $data = DB::table('products')->leftJoin('categories', 'products.category_id', '=', 'categories.id')->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')->select('products.*', 'categories.category_name as category_name', 'subcategories.subcategory_name as subcategory_name')->get();
        return view('backend.product-list', compact('page_name', 'data'));
    }
    public function addProduct($id = null) {
        $data = $id ? DB::table('products')->where('id', $id)->first() : '';
        $page_name = $id ? 'Edit Product' : 'Add Product';
        $categories = DB::table('categories')->get();
        $faqs = DB::table('faqs')->where(['table_id'=>$id,'table_name'=>'products'])->select('id','question','answer')->get();
        return view('backend.add-product', compact('data', 'page_name', 'categories','faqs'));
    }
    public function saveProduct(Request $request) {
        $data = $request->all();
        $id = isset($data['id']) ? trim($data['id']) : null;
        $checkData = ['category_id' => trim($data['category']??''), 'subcategory_id' => trim($data['subcategory']??''), 'product_name' => trim($data['product_name']??''), ];
        // Check for duplicate
        $duplicate = DB::table('products')->where($checkData)->first();
        if ($duplicate && (!$id || $duplicate->id != $id)) {
            return redirect()->back()->with('error', 'Duplicate Entry');
        }
        $saveData = $checkData;
        // Handle product images
        $img_data = [];
        if ($request->hasFile('product_image')) {
            foreach ($request->file('product_image') as $file) {
                if ($file->isValid()) {
                    $filename = $file->hashName();
                    $file->move(public_path('uploads'), $filename);
                    $img_data[] = ['image' => $filename];
                }
            }
            $saveData['product_image'] = !empty($img_data) ? json_encode($img_data) : null;
        }
        // Additional fields
        $saveData['product_description'] = trim($data['product_description']??'');
        $saveData['product_size'] = trim($data['product_size']??'');
        $saveData['product_colors'] = trim($data['product_colors']??'');
        $saveData['product_selling_price'] = trim($data['product_selling_price']??'');
        $saveData['product_cost_price'] = trim($data['product_cost_price']??'');
        $saveData['product_quantity'] = trim($data['product_quantity']??'');
        $saveData['product_availability'] = trim($data['product_availability']??'');
        $saveData['product_rating'] = isset($data['product_rating']) && is_numeric($data['product_rating']) ? floatval($data['product_rating']) : null;
        $saveData['is_trending'] = trim($data['is_trending']??'');
        $saveData['is_hot_deal'] = trim($data['is_hot_deal']??'');
        $saveData['product_off'] = isset($data['product_off']) && is_numeric($data['product_off']) ? floatval($data['product_off']) : null;
        if (empty($id)) {
            $saveData['created_at'] = now();
            $last_id = DB::table('products')->insertGetId($saveData);
            $msg = 'Product Added Successfully';
        } else {
            $saveData['updated_at'] = now();
            DB::table('products')->where('id', $id)->update($saveData);
            $last_id = $id;
            $msg = 'Product Updated Successfully';
        }
        // Handle FAQs
        $faq_data = [];
        $questions = $data['faq_question']??[];
        $answers = $data['faq_answer']??[];
        if (is_array($questions) && is_array($answers)) {
            for ($i = 0;$i < count($questions);$i++) {
                if (!empty($questions[$i]) && !empty($answers[$i])) {
                    $faq_data[] = ["table_name" => 'products', 
                                   "table_id" => $last_id, 
                                   "question" => $questions[$i], 
                                   "answer" => $answers[$i], 
                                   "created_at" => now() ];
                }
            }
            if (!empty($faq_data)) {
                // Delete existing FAQs for the product
                DB::table('faqs')->where(['table_id' => $last_id, 'table_name' => 'products'])->delete();
                // Insert new FAQs
                DB::table('faqs')->insert($faq_data);
            }
        }
        return redirect(route('admin.product-list'))->with('success', $msg);
    }
}

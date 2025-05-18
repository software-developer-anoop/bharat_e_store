<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class Product extends Controller
{
    public function index(){
        $page_name = 'Product List';
        $data = $data = DB::table('products')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')
                ->select(
                    'products.*',
                    'categories.category_name as category_name',
                    'subcategories.subcategory_name as subcategory_name'
                )
                ->get();
        return view('backend.product-list',compact('page_name','data'));
    }
    public function addProduct($id=null){
        $data = $id?DB::table('products')->where('id',$id)->first():'';
        $page_name = $id?'Edit Product':'Add Product';
        $categories = DB::table('categories')->get();
        return view('backend.add-product',compact('data','page_name','categories'));
    }
    public function saveProduct(Request $request){
        $data = $request->all();
        $saveData = [];
        $id = $data['id']?trim($data['id']):'';
        $checkData['category_id'] = trim($data['category']);
        $checkData['subcategory_id'] = trim($data['subcategory']);
        $checkData['product_name'] = trim($data['product_name']);
        $duplicate = DB::table('products')->where($checkData)->first();

        if (!empty($duplicate)) {
            if ($id === '' || $duplicate->id != $id) {
                return redirect()->back()->with('error', 'Duplicate Entry');
            }
        }
        $saveData = $checkData;
        $img_data = [];
        if ($request->hasFile('product_image')) {
            foreach ($request->file('product_image') as $file) {
                $filename = $file->hashName();
                $file->move(public_path('uploads'), $filename);
                $imgdata = []; // Initialize $imgdata as an empty array for each iteration
                $imgdata['image'] = $filename;
                $img_data[] = $imgdata;
            }
            $saveData['product_image'] = !empty($img_data) ? json_encode($img_data) : [];
        }
        
        $saveData['product_name'] = $data['product_name']?trim($data['product_name']):'';
        $saveData['product_description'] = $data['product_description']?trim($data['product_description']):'';
        $saveData['product_size'] = $data['product_size']?trim($data['product_size']):'';
        $saveData['product_colors'] = $data['product_colors']?trim($data['product_colors']):'';
        $saveData['product_selling_price'] = $data['product_selling_price']?trim($data['product_selling_price']):'';
        $saveData['product_cost_price'] = $data['product_cost_price']?trim($data['product_cost_price']):'';
        $saveData['product_quantity'] = $data['product_quantity']?trim($data['product_quantity']):'';
        $saveData['product_availability'] = $data['product_availability']?trim($data['product_availability']):'';
        $saveData['product_rating'] = isset($data['product_rating']) && is_numeric($data['product_rating'])
            ? floatval(trim($data['product_rating']))
            : null; // or use 0.0 if you prefer a default numeric value
        $saveData['is_trending'] = $data['is_trending']?trim($data['is_trending']):'';
        $saveData['is_hot_deal'] = $data['is_hot_deal']?trim($data['is_hot_deal']):'';
        if(empty($id)){
            $saveData['created_at'] = Carbon::now();
            DB::table('products')->insert($saveData);
            $msg = 'Product Added successfully';
        }else{
            $saveData['updated_at'] = Carbon::now();
            DB::table('products')->where('id',$id)->update($saveData);
            $msg = 'Product Updated Successfully';
        }
        return redirect(route('admin.product-list'))->with('success',$msg);
    }
}

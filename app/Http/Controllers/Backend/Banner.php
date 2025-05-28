<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class Banner extends Controller
{
    public function index(){
        $page_name = 'Banner List';
        $data = DB::table('banner_list')
                ->leftJoin('categories', 'banner_list.category_id', '=', 'categories.id')
                ->leftJoin('subcategories', 'banner_list.subcategory_id', '=', 'subcategories.id')
                ->select('banner_list.*', 'categories.category_name as category_name','subcategories.subcategory_name as subcategory_name')
                ->get();
        return view('backend.banner-list',compact('page_name','data'));
    }
    public function addBanner($id=null){
        $data = $id?DB::table('banner_list')->where('id',$id)->first():'';
        $page_name = $id?'Edit Banner':'Add Banner';
        $category = DB::table('categories')->select('category_name','id')->where('status', 'Active')->get();
        return view('backend.add-banner',compact('data','page_name','category'));
    }
    public function saveBanner(Request $request){
        $data = $request->all();
        $saveData = [];
        $id = $data['id']?trim($data['id']):'';
        $checkData['category_id'] = trim($data['category']);
        $checkData['subcategory_id'] = trim($data['subcategory']);
        $duplicate = DB::table('banner_list')->where($checkData)->first();

        if (!empty($duplicate)) {
            if ($id === '' || $duplicate->id != $id) {
                return redirect()->back()->with('error', 'Duplicate Entry');
            }
        }
        $saveData = $checkData;
        if ($file = $request->file('image')) {
            if ($file->isValid()) {
                $filename = $file->hashName();
                if (is_file(public_path('uploads/' . $data['old_image']))) {
                    @unlink(public_path('uploads/' . $data['old_image']));
                }
                $file->move(public_path('uploads/'), $filename);
                $saveData['image'] = $filename;
            }
        }
        
        if(empty($id)){
            $saveData['created_at'] = Carbon::now();
            DB::table('banner_list')->insert($saveData);
            $msg = 'Banner Added successfully';
        }else{
            $saveData['updated_at'] = Carbon::now();
            DB::table('banner_list')->where('id',$id)->update($saveData);
            $msg = 'Banner Updated Successfully';
        }
        return redirect(route('admin.banner-list'))->with('success',$msg);
    }

}

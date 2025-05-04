<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class Category extends Controller
{
    public function index(){
        $page_name = 'Category List';
        $data = DB::table('categories')->get();
        return view('backend.category-list',compact('page_name','data'));
    }
    public function addCategory($id=null){
        $data = $id?DB::table('categories')->where('id',$id)->first():'';
        $page_name = $id?'Edit Category':'Add Category';
        return view('backend.add-category',compact('data','page_name'));
    }
    public function saveCategory(Request $request){
        $data = $request->all();
        $saveData = [];
        $id = $data['id']?trim($data['id']):'';
        $checkData['category_name'] = ucwords(trim($data['category_name']));
        $duplicate = DB::table('categories')->where($checkData)->first();

        if (!empty($duplicate)) {
            if ($id === '' || $duplicate->id != $id) {
                return redirect()->back()->with('error', 'Duplicate Entry');
            }
        }

        if ($file = $request->file('category_image')) {
            if ($file->isValid()) {
                $filename = $file->hashName();
                if (is_file(public_path('uploads/' . $data['old_category_image']))) {
                    @unlink(public_path('uploads/' . $data['old_category_image']));
                }
                $file->move(public_path('uploads/'), $filename);
                $saveData['category_image'] = $filename;
            }
        }
        
        $saveData['category_name'] = $data['category_name']?trim($data['category_name']):'';
      
        if(empty($id)){
            $saveData['created_at'] = Carbon::now();
            DB::table('categories')->insert($saveData);
            $msg = 'Category Added successfully';
        }else{
            $saveData['updated_at'] = Carbon::now();
            DB::table('categories')->where('id',$id)->update($saveData);
            $msg = 'Category Updated Successfully';
        }
        return redirect(route('admin.category-list'))->with('success',$msg);
    }
}

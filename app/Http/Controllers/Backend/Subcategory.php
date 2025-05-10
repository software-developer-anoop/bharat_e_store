<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class Subcategory extends Controller
{
    public function index(){
        $page_name = 'Subcategory List';
        $data = DB::table('subcategories')
                ->join('categories', 'subcategories.category', '=', 'categories.id')
                ->select('subcategories.*', 'categories.category_name as category_name')
                ->get();
        return view('backend.subcategory-list',compact('page_name','data'));
    }
    public function addSubcategory($id=null){
        $data = $id?DB::table('subcategories')->where('id',$id)->first():'';
        $category = DB::table('categories')->select('category_name','id')->where('status', 'Active')->get();
        $page_name = $id?'Edit Subcategory':'Add Subcategory';
        return view('backend.add-subcategory',compact('data','page_name','category'));
    }
    public function saveSubcategory(Request $request){
        $data = $request->all();
        $saveData = [];
        $id = $data['id']?trim($data['id']):'';
        $checkData['category'] = trim($data['category']);
        $checkData['subcategory_name'] = ucwords(trim($data['subcategory_name']));
        $duplicate = DB::table('subcategories')->where($checkData)->first();

        if (!empty($duplicate)) {
            if ($id === '' || $duplicate->id != $id) {
                return redirect()->back()->with('error', 'Duplicate Entry');
            }
        }
        $saveData = $checkData;
        if ($file = $request->file('subcategory_image')) {
            if ($file->isValid()) {
                $filename = $file->hashName();
                if (is_file(public_path('uploads/' . $data['old_subcategory_image']))) {
                    @unlink(public_path('uploads/' . $data['old_subcategory_image']));
                }
                $file->move(public_path('uploads/'), $filename);
                $saveData['subcategory_image'] = $filename;
            }
        }
        
        if(empty($id)){
            $saveData['created_at'] = Carbon::now();
            DB::table('subcategories')->insert($saveData);
            $msg = 'Subcategory Added successfully';
        }else{
            $saveData['updated_at'] = Carbon::now();
            DB::table('subcategories')->where('id',$id)->update($saveData);
            $msg = 'Subcategory Updated Successfully';
        }
        return redirect(route('admin.subcategory-list'))->with('success',$msg);
    }
}

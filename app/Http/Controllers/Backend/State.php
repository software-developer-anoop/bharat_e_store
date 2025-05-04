<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class State extends Controller
{
    public function index(){
        $page_name = 'State List';
        $data = DB::table('states')
                ->join('country', 'states.country', '=', 'country.id')
                ->select('states.*', 'country.country_name as country_name')
                ->get();
        return view('backend.state-list',compact('page_name','data'));
    }
    public function addState($id=null){
        $data = $id?DB::table('states')->where('id',$id)->first():'';
        $page_name = $id?'Edit State':'Add State';
        $countries = DB::table('country')->select('country_name','id')->where('status', 'Active')->get();
        return view('backend.add-state',compact('data','page_name','countries'));
    }
    public function saveState(Request $request){
        $data = $request->all();
        $saveData = [];
        $id = $data['id']?trim($data['id']):'';
        $checkData['country'] = trim($data['country']);
        $checkData['state_name'] = ucwords(trim($data['state_name']));
        $duplicate = DB::table('states')->where($checkData)->first();

        if (!empty($duplicate)) {
            if ($id === '' || $duplicate->id != $id) {
                return redirect()->back()->with('error', 'Duplicate Entry');
            }
        }
        
        $saveData = $checkData;
      
        if(empty($id)){
            $saveData['created_at'] = Carbon::now();
            DB::table('states')->insert($saveData);
            $msg = 'State Added successfully';
        }else{
            $saveData['updated_at'] = Carbon::now();
            DB::table('states')->where('id',$id)->update($saveData);
            $msg = 'State Updated Successfully';
        }
        return redirect(route('admin.state-list'))->with('success',$msg);
    }
}

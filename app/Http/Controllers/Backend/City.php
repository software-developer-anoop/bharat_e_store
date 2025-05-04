<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class City extends Controller
{
    public function index(){
        $page_name = 'City List';
        $data = DB::table('cities')
                ->join('country', 'cities.country', '=', 'country.id')
                ->join('states', 'cities.state', '=', 'states.id')
                ->select('cities.*', 'country.country_name as country_name','states.state_name as state_name')
                ->get();
        return view('backend.city-list',compact('page_name','data'));
    }
    public function addCity($id=null){
        $data = $id?DB::table('cities')->where('id',$id)->first():'';
        $page_name = $id?'Edit City':'Add City';
        $countries = DB::table('country')->select('country_name','id')->where('status', 'Active')->get();
        return view('backend.add-city',compact('data','page_name','countries'));
    }
    public function saveCity(Request $request){
        $data = $request->all();
        $saveData = [];
        $id = $data['id']?trim($data['id']):'';
        $checkData['country'] = trim($data['country']);
        $checkData['state'] = trim($data['state']);
        $checkData['city_name'] = trim($data['city_name']);
        $duplicate = DB::table('cities')->where($checkData)->first();

        if (!empty($duplicate)) {
            if ($id === '' || $duplicate->id != $id) {
                return redirect()->back()->with('error', 'Duplicate Entry');
            }
        }
        
        $saveData = $checkData;
        $saveData['locality'] = $data['locality']?trim($data['locality']):'';
      
        if(empty($id)){
            $saveData['created_at'] = Carbon::now();
            DB::table('cities')->insert($saveData);
            $msg = 'City Added successfully';
        }else{
            $saveData['updated_at'] = Carbon::now();
            DB::table('cities')->where('id',$id)->update($saveData);
            $msg = 'City Updated Successfully';
        }
        return redirect(route('admin.city-list'))->with('success',$msg);
    }
}

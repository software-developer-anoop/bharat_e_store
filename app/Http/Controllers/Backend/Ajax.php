<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class Ajax extends Controller
{
    public function index(Request $request) {
        $keyword = $request->input("keyword");
        if (empty($keyword)) {
            return '';
        }
        $slug = validate_slug($keyword);
        return $slug;
    }
    public function changeStatus(Request $request){
        $status = $request->input('status')??'';
        $id = $request->input('id')??'';
        $table = $request->input('table')??'';
        if(empty($status)){
            return response()->json(['status'=>false,'msg'=>'Status is blank']);
        }
        if($table == "customers"){
          $updated = DB::table($table)->where('id', $id)->update(['profile_status' => $status,'updated_at'=>Carbon::now()]);  
        }else{
           $updated = DB::table($table)->where('id', $id)->update(['status' => $status,'updated_at'=>Carbon::now()]); 
        }
        
        if ($updated) {
            return response()->json(['status' => true, 'msg' => 'Status Changed To ' . $status, 'name' => $status]);
        } else {
            return response()->json(['status' => false, 'msg' => 'Failed to update status or record not found']);
        }
    }
    public function deleteItem(Request $request){
        $id = $request->input('id')??'';
        $table = $request->input('table')??'';
        if(empty($id)){
            return response()->json(['status'=>false,'msg'=>'Id is blank']);
        }
        $deleted = DB::table($table)->where('id', $id)->delete();
        if ($deleted) {
            return response()->json(['status' => true, 'msg' => 'Item Deleted Successfully']);
        } else {
            return response()->json(['status' => false, 'msg' => 'Failed to Delete']);
        }
    }
    public function getStates(Request $request)
    {
        $country_id = (int) $request->input('country_id');

        $states = DB::table('states')
                    ->select('state_name', 'id')
                    ->where([
                        ['country', '=', $country_id],
                        ['status', '=', 'Active']
                    ])
                    ->get();

        $html = '<option value="">Select State</option>';
        foreach ($states as $state) {
            $html .= '<option value="' . $state->id . '">' . $state->state_name . '</option>';
        }
        
        return response()->json(['html' => $html]);
    }


}

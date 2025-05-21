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
        $state_id = $request->input('state')?(int) $request->input('state'):'';
        $states = DB::table('states')
                    ->select('state_name', 'id')
                    ->where([
                        ['country', '=', $country_id],
                        ['status', '=', 'Active']
                    ])
                    ->get();

        $html = '<option value="">Select State</option>';
        foreach ($states as $state) {
            $selected = !empty($state_id) && ($state_id==$state->id)?'selected':'';
            $html .= '<option value="' . $state->id . '" '.$selected.'>' . $state->state_name . '</option>';
        }
        
        return response()->json(['html' => $html]);
    }
    public function getSubcategory(Request $request)
    {
        $category_id = (int) $request->input('category_id');
        $subcategory_id = $request->input('subcategory_id')?(int) $request->input('subcategory_id'):'';
        $subcategories = DB::table('subcategories')
                    ->select('subcategory_name', 'id')
                    ->where([
                        ['category', '=', $category_id],
                        ['status', '=', 'Active']
                    ])
                    ->get();

        $html = '<option value="">Select Subcategory</option>';
        foreach ($subcategories as $subcategory) {
            $selected = !empty($subcategory_id) && ($subcategory_id==$subcategory->id)?'selected':'';
            $html .= '<option value="' . $subcategory->id . '" '.$selected.'>' . $subcategory->subcategory_name . '</option>';

        }
        
        return response()->json(['html' => $html]);
    }
    public function manageInventory(Request $request)
    {
        $condition = trim($request->condition ?? '');
        $product_id = trim($request->product_id ?? '');

        if (empty($condition)) {
            return response()->json(['status' => false, 'msg' => 'Condition is blank']);
        }

        if (empty($product_id)) {
            return response()->json(['status' => false, 'msg' => 'Product ID is blank']);
        }

        $changeAmount = 1; // Always change by 1

        if ($condition === "increment") {
            $change = "Increased";
            DB::table('products')->where('id', $product_id)->increment('product_quantity', $changeAmount);
        } elseif ($condition === "decrement") {
            $change = "Decreased";
            DB::table('products')->where('id', $product_id)->decrement('product_quantity', $changeAmount);
        } else {
            return response()->json(['status' => false, 'msg' => 'Invalid condition']);
        }

        return response()->json(['status' => true, 'msg' => "Quantity {$change}"]);
    }
    public function setIsTrendingHotDeal(Request $request)
    {
        $product_id = (int) $request->product_id;
        $key = $request->key;
        $checked = filter_var($request->checked, FILTER_VALIDATE_BOOLEAN); // true or false

        if (!$product_id || !in_array($key, ['is_trending', 'is_hot_deal'])) {
            return response()->json(['status' => false, 'msg' => 'Invalid parameters']);
        }

        $label = $key === 'is_trending' ? 'Trending' : 'Hot Deal';
        $msg = $checked ? "Set to $label" : "Removed from $label";
        $status = $checked ? 'yes' : 'no';

        DB::table('products')->where('id', $product_id)->update([$key => $status]);

        return response()->json(['status' => true, 'msg' => $msg]);
    }
  
    public function assignMenu(Request $request)
    {
        if (!$request->isMethod('post')) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid request method.'
            ]);
        }

        $adminId = $request->input('admin');

        if (empty($adminId)) {
            return response()->json([
                'status' => false,
                'message' => 'Please choose an admin.'
            ]);
        }

        $assignedMenus = $request->input('menus', []);
        $menuString = !empty($assignedMenus) ? implode(',', $assignedMenus) : null;

        $updated = DB::table('users')
            ->where('id', $adminId)
            ->update(['assigned_menus' => $menuString]);

        if ($updated) {
            $status = $menuString ? 'assigned' : 'unassigned';
            return response()->json([
                'status' => true,
                'message' => "Menus {$status} successfully."
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Failed to update menus.'
        ]);
    }


}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class Address extends Controller
{
    public function index()
    {
        $post = checkPayload();
        $customer_id = trim($post['customer_id'] ?? '');

        if (empty($customer_id)) {
            return response()->json([
                'status' => false,
                'message' => "Customer ID Is Blank",
            ]);
        }

        $customer = DB::table('customers')->where('id', $customer_id)->first();
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'No Record Found']);
        }

        $addressList = DB::table('addresses')
            ->where('addresses.customer_id', $customer_id)
            ->leftJoin('country','addresses.country_id','=','country.id')
            ->leftJoin('states','addresses.state_id','=','states.id')
            ->leftJoin('cities','addresses.city_id','=','cities.id')
            ->select('addresses.id', 'addresses.customer_id', 'addresses.name', 'addresses.email', 'addresses.phone', 'addresses.address', 'addresses.pincode', 'addresses.address_type','addresses.country_id','addresses.state_id','addresses.city_id','country.country_name','states.state_name','cities.city_name')
            ->get();

        if ($addressList->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => "No records found",
            ]);
        }

        $returnData = [];
        foreach ($addressList as $value) {
            $returnData[] = [
                'address_id'   => (string) $value->id,
                'customer_id'  => (string) $value->customer_id,
                'name'         => (string) $value->name,
                'email'        => (string) $value->email,
                'phone'        => (string) $value->phone,
                'address'      => (string) $value->address,
                'pincode'      => (string) $value->pincode,
                'address_type' => (string) $value->address_type,
                'country_id'   => (string) $value->country_id,
                'country_name' => (string) $value->country_name,
                'state_id'     => (string) $value->state_id,
                'state_name'   => (string) $value->state_name,
                'city_id'      => (string) $value->city_id,
                'city_name'    => (string) $value->city_name,
            ];
        }

        return response()->json([
            'status'  => true,
            'message' => "API accessed successfully!",
            'data'    => $returnData,
        ]);
    }

    public function addEditAddress()
    {
        $response = [];
        $post = checkPayload();

        $customer_id = trim($post['customer_id'] ?? '');
        $address_id = trim($post['address_id'] ?? ''); // fixed typo here
        $name = trim($post['name'] ?? '');
        $phone = trim($post['phone'] ?? '');
        $email = trim($post['email'] ?? '');
        $address = trim($post['address'] ?? '');
        $pincode = trim($post['pincode'] ?? '');
        $address_type = trim($post['address_type'] ?? '');
        $country_id = trim($post['country_id'] ?? '');
        $state_id = trim($post['state_id'] ?? '');
        $city_id = trim($post['city_id'] ?? '');

        if (empty($customer_id)) {
            return response()->json([
                'status' => false,
                'message' => 'Customer ID Is Required',
            ]);
        }

        $customer = DB::table('customers')->where('id', $customer_id)->first();
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'No Record Found']);
        }
        
        $saveData = [
            'customer_id' => $customer_id,
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'address' => $address,
            'pincode' => $pincode,
            'address_type' => $address_type,
            'country_id' => $country_id,
            'state_id' => $state_id,
            'city_id' => $city_id,
        ];

        if (empty($address_id)) {
            $saveData['created_at'] = Carbon::now();
            DB::table('addresses')->insert($saveData);
            $msg = 'Address added successfully';
        } else {
            $saveData['updated_at'] = Carbon::now();
            DB::table('addresses')->where('id', $address_id)->update($saveData);
            $msg = 'Address updated successfully';
        }

        return response()->json([
            'status' => true,
            'message' => $msg,
        ]);
    }
    public function deleteAddress()
    {
        $post = checkPayload();
        $address_id = trim($post['address_id'] ?? '');

        if (empty($address_id)) {
            return response()->json([
                'status' => false,
                'message' => 'Address ID is blank',
            ]);
        }

        $check = DB::table('addresses')->where('id', $address_id)->first();

        if ($check) {
            DB::table('addresses')->where('id', $address_id)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Address deleted successfully.',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Address not found.',
            ]);
        }
    }



}

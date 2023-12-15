<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    //user address create
    public function createAddress (Request $request){
        $data = $request->validate([
            'street' => 'required|string',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'postal_code' => 'nullable',
            'phone' => 'nullable',
        ]);
        $auth_user = auth('sanctum')->user();
        $address = UserAddress::create([
            'user_id' => $auth_user->id,
            'street' => $data['street'],
            'city' => $data['city'],
            'state' => $data['state'],
            'country' => $data['country'],
            'postal_code' => $data['postal_code'],
            'phone' => $data['phone']
        ]);

        $address->load('user');

        return response()->json([
            'status' => 'success',
            'data' =>  $address,
        ], 201);
    }

    #user address update
    public function updateAddress(Request $request,$id){
        $address = UserAddress::find($id);
        if($address){
            $address->update([
                'street' => $request->street ?? $address->street,
                'city' => $request->city ?? $address->city,
                'start' => $request->start ?? $address->start,
                'country' => $request->country ?? $address->country,
                'postal_code' => $request->postal_code ?? $address->postal_code,
                'phone' => $request->phone ?? $address->phone,
            ]);

            $address->load('user');
            return response()->json([
                'status' => 'success',
                'message' =>  "Successfully Updated",
                'data' => $address
            ], 201);
        }else{
            return response()->json([
                'status' => 'fail',
                'message' =>  "Not Found"
            ], 404);
        }
    }
}

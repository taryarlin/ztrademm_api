<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserAddressResource;
use App\Models\UserAddress;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    public function index()
    {
        $auth_user = auth('sanctum')->user();

        if(is_null($auth_user)) {
            return response()->json([
                'status' => 'fail',
                'message' => "Unauthenticated"
            ], 401);
        }

        if(!is_null($auth_user->address)) {
            $auth_user->address->load('user');
        }

        return new UserAddressResource($auth_user->address);
    }

    public function save(Request $request)
    {
        $auth_user = auth('sanctum')->user();

        if(is_null($auth_user)) {
            return response()->json([
                'status' => 'fail',
                'message' => "Unauthenticated"
            ], 401);
        }

        $data = $request->validate([
            'street' => 'required|string',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'postal_code' => 'nullable',
            'phone' => 'nullable',
        ]);

        $address = UserAddress::updateOrCreate([
            'user_id' => $auth_user->id
        ], [
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
            'data' => $address,
        ], 201);
    }
}

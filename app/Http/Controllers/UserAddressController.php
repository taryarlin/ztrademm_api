<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    public function save(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required',
            'street' => 'required|string',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'postal_code' => 'nullable',
            'phone' => 'nullable',
        ]);

        $address = UserAddress::updateOrCreate([
            'user_id' => $request->user_id
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

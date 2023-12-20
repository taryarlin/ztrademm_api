<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $data = OrderResource::collection(Order::with('orderItems')->get());

        return json_decode(json_encode($data));
    }
}

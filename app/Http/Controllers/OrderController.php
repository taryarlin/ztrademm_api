<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        return OrderResource::collection(Order::with('orderItems')->paginate(20))->additional([
            'result' => 1,
            'message' => 'success',
        ]);
    }
}

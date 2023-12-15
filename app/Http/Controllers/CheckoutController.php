<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Notifications\OrderSuccessNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        DB::beginTransaction();

        try {
            $auth_user = auth('sanctum')->user();

            if(is_null($auth_user)) {
                return response()->json([
                    'status' => 'fail',
                    'message' => "Unauthenticated"
                ], 401);
            }

            $request->validate(['payment_option' => 'required|in:cod,stripe']);

            if($auth_user->cartItems()->count() <= 0) {
                return response()->json([
                    'status' => 'fail',
                    'message' => "No cart item to checkout"
                ], 404);
            }

            $order = Order::create([
                'user_id' => $auth_user->id,
                'user_address_id' => optional($auth_user->address)->id,
                'pay_status' => 'pending',
                'payment_option' => $request->payment_option,
                'address_archive' => $auth_user->address ? json_encode($auth_user->address) : null,
                'product_archive' => json_encode($auth_user->cartItems)
            ]);

            $order_number = 'ZTRADEMM-00' . $order->id;
            $order->order_number = $order_number;
            $order->update();

            foreach($auth_user->cartItems as $cart_item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'user_id' => $auth_user->id,
                    'product_id' => $cart_item->product_id,
                    'quantity' => $cart_item->quantity,
                    'single_price' => $cart_item->single_price,
                    'total_price' => $cart_item->total_price,
                ]);
            }

            $auth_user->cartItems()->delete();

            DB::commit();

            $auth_user->notify(new OrderSuccessNotification($order_number));

            return response()->json([
                'status' => 'success',
                'message' => "Customer order is successful",
                'data' => $order
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error($e);

            return response()->json([
                'status' => 'fail',
                'message' => $e->getMessage(),
            ], 201);
        }
    }
}

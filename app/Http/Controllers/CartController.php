<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartItemResource;
use App\Models\CartSession;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function cartItems()
    {
        $auth_user = auth('sanctum')->user();

        if(is_null($auth_user)) {
            return response()->json([
                'status' => 'fail',
                'message' => "Unauthenticated"
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Cart item list request is successful.",
            'data' => CartItemResource::collection($auth_user->cartItems)
        ], 201);
    }

    public function addToCart(Request $request)
    {
        Log::info($request->all());

        DB::beginTransaction();

        try {
            $auth_user = auth('sanctum')->user();

            if(is_null($auth_user)) {
                return response()->json([
                    'status' => 'fail',
                    'message' => "Unauthenticated"
                ], 401);
            }

            $request->validate(
                [
                    'product_id' => 'required',
                    'quantity' => 'required',
                ],
                [
                    'product_id.required' => 'The Product field is required.',
                ]
            );

            $product = Product::find($request->product_id);

            if(is_null($product)) {
                return response()->json([
                    'status' => 'fail',
                    'message' => "Product not found."
                ], 404);
            }

            $cart_item = CartSession::firstOrCreate([
                'user_id' => $auth_user->id,
                'product_id' => $request->product_id
            ], [
                'quantity' => 0,
                'session_key' => $this->generateSessionKey(),
                'single_price' => $product->price,
                'total_price' => 0,
            ]);

            $new_quantity = $cart_item->quantity + $request->quantity;
            $cart_item->quantity = $new_quantity;
            $cart_item->total_price = $product->price * $new_quantity;
            $cart_item->update();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "Product is successfully added to cart.",
                'data' => $cart_item
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

    public function addQuantityToCart(string $cart_session_id)
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

            $cart_session = CartSession::find($cart_session_id);

            if(is_null($cart_session)) {
                return response()->json([
                    'status' => 'fail',
                    'message' => "Cart item not found"
                ], 404);
            }

            $cart_session->quantity = $cart_session->quantity + 1;
            $cart_session->update();

            if ($cart_session->quantity < 1) {
                $cart_session->delete();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "Product quantity is successfully added.",
                'data' => $cart_session
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

    public function reduceQuantityToCart(string $cart_session_id)
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

            $cart_session = CartSession::find($cart_session_id);

            if(is_null($cart_session)) {
                return response()->json([
                    'status' => 'fail',
                    'message' => "Cart item not found"
                ], 404);
            }

            $cart_session->quantity = $cart_session->quantity - 1;
            $cart_session->update();

            if ($cart_session->quantity < 1) {
                $cart_session->delete();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "Product quantity is reduced successfully.",
                'data' => $cart_session
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

    public function removeFromCart(Request $request)
    {
        $auth_user = auth('sanctum')->user();

        if(is_null($auth_user)) {
            return response()->json([
                'status' => 'fail',
                'message' => "Unauthenticated"
            ], 401);
        }

        $request->validate(['product_id' => 'required']);

        if(!$auth_user->cartItems()->where('product_id', $request->product_id)->exists()) {
            return response()->json([
                'status' => 'fail',
                'message' => "Product Not Found!"
            ], 404);
        }

        $auth_user->cartItems()->where('product_id', $request->product_id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Cart item is successfully deleted."
        ], 201);
    }

    public function itemCount()
    {
        $auth_user = auth('sanctum')->user();

        if(is_null($auth_user)) {
            return response()->json([
                'status' => 'fail',
                'message' => "Unauthenticated"
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Product is successfully added to cart.",
            'data' => ['item_count' => $auth_user->cartItems()->count()]
        ], 201);
    }

    private function generateSessionKey()
    {
        $session_key = 'cart-items-' . Str::random(21);

        if (CartSession::where('session_key', $session_key)->exists()) {
            return self::generateSessionKey();
        }

        return $session_key;
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CartController extends Controller
{
    /**
     * Display the authenticated user's cart.
     */
    public function index(Request $request)
    {
        Gate::authorize('role:customer', Auth::user());

        $user = Auth::user();
        $cart = $user->cart()->firstOrCreate();
        $cart->load(['cartItems.product']);

        return $this->response(new CartResource($cart), 'Cart retrieved successfully');
    }

    /**
     * Store a newly created resource or update an existing resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('role:customer', Auth::user());

        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $user = Auth::user();
        $cart = $user->cart()->firstOrCreate();

        $product = Product::find($validated['product_id']);

        if ($product->stock_quantity < $validated['quantity']) {
            return $this->response(null, 'Insufficient stock available', 422);
        }

        $cartItem = $cart->cartItems()->updateOrCreate(
            ['product_id' => $validated['product_id']],
            ['quantity' => $validated['quantity']]
        );

        $cart->load(['cartItems.product']);

        return $this->response(new CartResource($cart), 'Cart updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Gate::authorize('role:customer', Auth::user());

        $user = Auth::user();
        $cart = $user->cart;

        if (! $cart) {
            return $this->response(null, 'Cart item not found', 404);
        }

        $cartItem = $cart->cartItems()->find($id);

        if (! $cartItem) {
            return $this->response(null, 'Cart item not found', 404);
        }

        $cartItem->delete();

        $cart->load(['cartItems.product']);

        return $this->response(new CartResource($cart), 'Cart item removed successfully');
    }
}

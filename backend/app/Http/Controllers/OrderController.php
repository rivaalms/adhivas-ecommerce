<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Enums\UserRoleEnum;
use App\Enums\Enum\OrderStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $orders = Order::when($user->role === UserRoleEnum::CUSTOMER, function ($query) use ($user) {
            return $query->where('user_id', $user->id);
        })
            ->with(['user'])
            ->paginate($request->per_page);

        return $this->response(new OrderCollection($orders), 'Orders retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        Gate::authorize('role:customer', $user);

        $validated = $request->validate([
            'user_address_id' => [
                'required',
                'integer',
                'exists:user_addresses,id,user_id,' . $user->id
            ],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $order = DB::transaction(function () use ($validated, $user) {
                $totalAmount = 0.0;
                $itemsData = [];

                foreach ($validated['items'] as $item) {
                    $product = Product::lockForUpdate()->find($item['product_id']);

                    if ($product->stock_quantity < $item['quantity']) {
                        throw ValidationException::withMessages([
                            'items' => "Insufficient stock for product: {$product->name}. Available: {$product->stock_quantity}."
                        ]);
                    }

                    $product->decrement('stock_quantity', $item['quantity']);

                    $totalAmount += $product->price * $item['quantity'];

                    $itemsData[] = [
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $product->price
                    ];
                }

                $order = Order::create([
                    'user_id' => $user->id,
                    'user_address_id' => $validated['user_address_id'],
                    'order_date' => now(),
                    'total_amount' => $totalAmount,
                    'status' => OrderStatusEnum::PENDING,
                ]);

                foreach ($itemsData as $itemData) {
                    $order->orderDetails()->create($itemData);
                }

                // Delete checked-out items from user's cart
                if ($cart = $user->cart) {
                    $productIds = array_column($validated['items'], 'product_id');
                    $cart->cartItems()->whereIn('product_id', $productIds)->delete();
                }

                return $order;
            });

            $order->load(['user', 'userAddress', 'orderDetails.product']);

            return $this->response(new OrderResource($order), 'Order created successfully', 201);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return $this->response(null, 'Failed to place order: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();

        $order = Order::when($user->role === UserRoleEnum::CUSTOMER, function ($query) use ($user) {
            return $query->where('user_id', $user->id);
        })->with(['user', 'userAddress', 'orderDetails.product'])->find($id);

        if (!$order) {
            return $this->response(null, 'Order not found', 404);
        }

        return $this->response(new OrderResource($order), 'Order retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)->find($id);
        if (!$order) {
            return $this->response(null, 'Order not found', 404);
        }

        if ($order->status !== OrderStatusEnum::PENDING) {
            return $this->response(null, 'Only pending orders can be updated', 422);
        }

        $validated = $request->validate([
            'user_address_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:user_addresses,id,user_id,' . ($user->role === UserRoleEnum::ADMIN ? $order->user_id : $user->id)
            ],
        ]);

        $order->update($validated);
        $order->load(['user', 'userAddress', 'orderDetails.product']);

        return $this->response(new OrderResource($order), 'Order updated successfully');
    }

    /**
     * Update the status of the specified resource.
     */
    public function updateStatus(Request $request, string $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return $this->response(null, 'Order not found', 404);
        }

        $user = Auth::user();

        if ($user->role === UserRoleEnum::CUSTOMER) {
            if ($order->user_id !== $user->id) {
                return $this->response(null, 'Forbidden', 403);
            }

            $validated = $request->validate([
                'status' => ['required', 'string', 'in:cancelled'],
            ]);

            if ($order->status !== OrderStatusEnum::PENDING) {
                return $this->response(null, 'Only pending orders can be cancelled', 422);
            }

            $newStatus = OrderStatusEnum::CANCELLED;
        } else {
            $validated = $request->validate([
                'status' => ['required', 'string', 'in:' . implode(',', array_column(OrderStatusEnum::cases(), 'value'))],
            ]);

            $newStatus = OrderStatusEnum::from($validated['status']);
        }

        if ($newStatus === OrderStatusEnum::CANCELLED && $order->status !== OrderStatusEnum::CANCELLED) {
            DB::transaction(function () use ($order, $newStatus) {
                foreach ($order->orderDetails as $detail) {
                    $detail->product()->increment('stock_quantity', $detail->quantity);
                }
                $order->update(['status' => $newStatus]);
            });
        } else {
            $order->update(['status' => $newStatus]);
        }

        $order->load(['userAddress', 'orderDetails.product']);
        return $this->response(new OrderResource($order), 'Order status updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Gate::authorize('role:admin', Auth::user());

        $order = Order::find($id);
        if (!$order) {
            return $this->response(null, 'Order not found', 404);
        }

        DB::transaction(function () use ($order) {
            if ($order->status !== OrderStatusEnum::CANCELLED) {
                foreach ($order->orderDetails as $detail) {
                    $detail->product()->increment('stock_quantity', $detail->quantity);
                }
            }
            $order->orderDetails()->delete();
            $order->delete();
        });

        return $this->response(null, 'Order deleted successfully');
    }
}

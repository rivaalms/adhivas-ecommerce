<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::with(['categories'])->paginate($request->per_page);
        return $this->response(new ProductCollection($products), 'Products retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('role:admin', Auth::user());

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'image_url' => ['nullable', 'string', 'url', 'max:255'],
        ]);

        $product = Product::create($validated);

        return $this->response(new ProductResource($product), 'Product created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return $this->response(null, 'Product not found', 404);
        }

        return $this->response(new ProductResource($product), 'Product retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Gate::authorize('role:admin', Auth::user());

        $product = Product::find($id);

        if (!$product) {
            return $this->response(null, 'Product not found', 404);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'stock_quantity' => ['sometimes', 'required', 'integer', 'min:0'],
            'image_url' => ['nullable', 'string', 'url', 'max:255'],
        ]);

        $product->update($validated);

        return $this->response(new ProductResource($product), 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Gate::authorize('role:admin', Auth::user());

        $product = Product::find($id);

        if (!$product) {
            return $this->response(null, 'Product not found', 404);
        }

        $product->delete();

        return $this->response(null, 'Product deleted successfully');
    }
}

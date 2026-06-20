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
    public function index(Request $request)
    {
        $products = Product::with(['categories'])
            ->when($request->category_id, function ($query, $categoryId) {
                return $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                });
            })
            ->when($request->search ?? $request->name, function ($query, $search) {
                return $query->where('name', 'ilike', "%{$search}%");
            })
            ->when($request->sort_by, function ($query, $sortBy) use ($request) {
                $allowedFields = ['price', 'created_at'];
                if (in_array($sortBy, $allowedFields)) {
                    $sortDir = $request->input('sort_dir', 'asc') === 'desc' ? 'desc' : 'asc';
                    return $query->orderBy($sortBy, $sortDir);
                }
                return $query;
            }, function ($query) {
                return $query->orderBy('created_at', 'desc');
            })
            ->paginate($request->per_page);

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
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['required', 'integer', 'exists:categories,id'],
        ]);

        $product = Product::create($validated);
        $product->categories()->attach($validated['categories']);

        return $this->response(new ProductResource($product), 'Product created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with(['categories'])->find($id);

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
            'categories' => ['sometimes', 'required', 'array', 'min:1'],
            'categories.*' => ['required', 'integer', 'exists:categories,id'],
        ]);

        $product->update($validated);

        if (isset($validated['categories'])) {
            $product->categories()->sync($validated['categories']);
        }

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

        $product->categories()->detach();
        $product->delete();

        return $this->response(null, 'Product deleted successfully');
    }
}

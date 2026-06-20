<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = Category::paginate($request->per_page);
        return $this->response(new CategoryCollection($categories), 'Categories retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('role:admin', Auth::user());

        $validated = $request->merge([
            'slug' => Str::slug($request->name)
        ])->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug'],
        ]);

        $category = Category::create($validated);

        return $this->response(new CategoryResource($category), 'Category created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $this->response(null, 'Category not found', 404);
        }

        return $this->response(new CategoryResource($category), 'Category retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Gate::authorize('role:admin', Auth::user());

        $category = Category::find($id);

        if (!$category) {
            return $this->response(null, 'Category not found', 404);
        }

        $validated = $request->merge([
            'slug' => Str::slug($request->name)
        ])->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'required', 'string', 'max:255', "unique:categories,slug,{$id}"],
        ]);

        $category->update($validated);

        return $this->response(new CategoryResource($category), 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Gate::authorize('role:admin', Auth::user());

        $category = Category::find($id);

        if (!$category) {
            return $this->response(null, 'Category not found', 404);
        }

        $category->delete();

        return $this->response(null, 'Category deleted successfully');
    }
}

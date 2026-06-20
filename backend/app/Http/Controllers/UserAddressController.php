<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserAddressCollection;
use App\Http\Resources\UserAddressResource;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UserAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $addresses = $request->user()->addresses()->paginate($request->per_page);
        return $this->response(new UserAddressCollection($addresses), 'Addresses retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        Gate::authorize('role:customer', $user);

        $validated = $request->validate([
            'address_line' => ['nullable', 'string', 'max:255'],
            'subdistrict_id' => ['required', 'string', 'max:255'],
            'subdistrict_name' => ['required', 'string', 'max:255'],
            'district_id' => ['required', 'string', 'max:255'],
            'district_name' => ['required', 'string', 'max:255'],
            'regency_id' => ['required', 'string', 'max:255'],
            'regency_name' => ['required', 'string', 'max:255'],
            'province_id' => ['required', 'string', 'max:255'],
            'province_name' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $isFirstAddress = !UserAddress::where('user_id', $user->id)->exists();

        $validated['is_default'] = $isFirstAddress || ($validated['is_default'] ?? false);

        if ($validated['is_default']) {
            UserAddress::where('user_id', $user->id)->update(['is_default' => false]);
        }

        $address = UserAddress::create(array_merge($validated, ['user_id' => $user->id]));

        return $this->response(new UserAddressResource($address), 'Address created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $address = UserAddress::where('user_id', Auth::user()->id)->find($id);

        if (!$address) {
            return $this->response(null, 'Address not found', 404);
        }

        return $this->response(new UserAddressResource($address), 'Address retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        Gate::authorize('role:customer', $user);

        $address = UserAddress::where('user_id', $user->id)->find($id);

        if (!$address) {
            return $this->response(null, 'Address not found', 404);
        }

        $validated = $request->validate([
            'address_line' => ['sometimes', 'nullable', 'string', 'max:255'],
            'subdistrict_id' => ['sometimes', 'required', 'string', 'max:255'],
            'subdistrict_name' => ['sometimes', 'required', 'string', 'max:255'],
            'district_id' => ['sometimes', 'required', 'string', 'max:255'],
            'district_name' => ['sometimes', 'required', 'string', 'max:255'],
            'regency_id' => ['sometimes', 'required', 'string', 'max:255'],
            'regency_name' => ['sometimes', 'required', 'string', 'max:255'],
            'province_id' => ['sometimes', 'required', 'string', 'max:255'],
            'province_name' => ['sometimes', 'required', 'string', 'max:255'],
            'postal_code' => ['sometimes', 'required', 'string', 'max:20'],
            'is_default' => ['sometimes', 'required', 'boolean'],
        ]);

        if (isset($validated['is_default']) && $validated['is_default']) {
            UserAddress::where('user_id', $user->id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return $this->response(new UserAddressResource($address), 'Address updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();

        Gate::authorize('role:customer', $user);

        $address = UserAddress::where('user_id', $user->id)->find($id);

        if (!$address) {
            return $this->response(null, 'Address not found', 404);
        }

        $wasDefault = $address->is_default;

        if ($wasDefault) {
            $nextDefault = UserAddress::where('user_id', $address->user_id)->first();
            if ($nextDefault) {
                $nextDefault->update(['is_default' => true]);
            }
        }

        $address->delete();

        return $this->response(null, 'Address deleted successfully');
    }
}

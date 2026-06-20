<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserCollection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('role:admin', Auth::user());

        $users = User::when($request->search, function ($query, $search) {
            return $query->where('full_name', 'ilike', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%");
        })
            ->when($request->role, function ($query, $role) {
                return $query->where('role', $role);
            })
            ->paginate($request->input('per_page', 10));

        return $this->response(new UserCollection($users), 'Users retrieved successfully');
    }
}

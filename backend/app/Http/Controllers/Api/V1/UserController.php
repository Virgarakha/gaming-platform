<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getAdmins()
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'You are not the administrator'
            ], 403);
        }

        $admins = User::where('role', 'admin')->get();

        return response()->json([
            'totalElements' => $admins->count(),
            'content' => $admins
        ], 200);
    }

    public function index()
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'You are not the administrator'
            ], 403);
        }

        $users = User::where('role', 'player')->get();

        return response()->json([
            'totalElements' => $users->count(),
            'content' => $users
        ], 200);
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'You are not the administrator'
            ], 403);
        }

        $request->validate([
            'username' => 'required|string|unique:users|min:4|max:60',
            'password' => 'required|string|min:5|max:10',
        ]);

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'player',
        ]);

        return response()->json([
            'status' => 'success',
            'username' => $user->username
        ], 201);
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'You are not the administrator'
            ], 403);
        }

        $request->validate([
            'username' => 'required|string|min:4|max:60|unique:users,username,'.$id,
            'password' => 'required|string|min:5|max:10',
        ]);

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'User Not found'
            ], 404);
        }

        $user->update([
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 'success',
            'username' => $user->username
        ], 201);
    }

    public function destroy($id)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'You are not the administrator'
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'User Not found'
            ], 404);
        }

        $user->delete();

        return response()->json(null, 204);
    }
}
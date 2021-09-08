<?php

namespace App\Http\Controllers\Api;

use auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'gender' => 'required',
            'age' => 'required|integer',
            'password' => 'required|string|confirmed',
        ]);
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'gender' => $fields['gender'],
            'age' => $fields['age'],
            'password' => Hash::make($fields['password'])
        ]);
        $token = $user->createToken('userToken')->plainTextToken;
        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'email|string|required',
            'password' => 'required|string',
        ]);
        $user = User::where('email', $fields['email'])->first();
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'There is an error in email or Password'
            ], 401);
        }
        $token = $user->createToken('userToken')->plainTextToken;
        $response = [
            'user' => $user, 'token' => $token
        ];
        return response($response, 201);
    }
    public function logout(User $user)
    {
        $user->tokens()->delete();
        return [
            'message' => 'Logged out'
        ];
    }
}

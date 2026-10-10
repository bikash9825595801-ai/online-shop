<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\User;
use App\Models\Wishlist;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends ApiController
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:1000'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_ACTIVE,
        ]);

        Cart::create(['user_id' => $user->id]);
        Wishlist::create(['user_id' => $user->id]);

        AuditService::log('Registered', 'Auth', "New customer account created via API: {$user->email}", $user->id);

        $token = $user->createToken('mobile')->plainTextToken;

        return api_response(true, 'Account created successfully.', [
            'user' => $this->userPayload($user),
            'token' => $token,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return api_response(false, 'Invalid email or password.', null, 401);
        }

        if ($user->isBlocked()) {
            return api_response(false, 'Your account has been blocked. Please contact support.', null, 403);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        AuditService::log('Logged in', 'Auth', "Mobile API login: {$user->email}", $user->id);

        return api_response(true, 'Login successful.', [
            'user' => $this->userPayload($user),
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return api_response(true, 'Logged out successfully.');
    }

    public function me(Request $request): JsonResponse
    {
        return api_response(true, 'Profile fetched.', [
            'user' => $this->userPayload($request->user()),
        ]);
    }
}

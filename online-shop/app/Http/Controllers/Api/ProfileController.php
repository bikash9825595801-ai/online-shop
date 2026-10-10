<?php

namespace App\Http\Controllers\Api;

use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends ApiController
{
    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();
        $user->update($data);

        AuditService::log('Profile updated', 'Profile', "{$user->email} updated their profile via API.");

        return api_response(true, 'Profile updated successfully.', [
            'user' => $this->userPayload($user),
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (! Hash::check($data['current_password'], $user->password)) {
            return api_response(false, 'Current password is incorrect.', null, 422);
        }

        $user->update(['password' => Hash::make($data['password'])]);

        AuditService::log('Password changed', 'Profile', "{$user->email} changed their password via API.");

        return api_response(true, 'Password updated successfully.');
    }
}

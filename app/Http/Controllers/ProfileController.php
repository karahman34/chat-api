<?php

namespace App\Http\Controllers;

use App\Events\UserUpdated;
use App\Helpers\Transformer;
use App\Http\Resources\ReceiverResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Update user profile.
     *
     * @param   Request  $request
     *
     * @return  JsonResponse
     */
    public function update(Request $request)
    {
        $auth = Auth::user();

        $request->validate([
            'avatar' => 'nullable|mimes:png,jpg,jpeg|max:8192',
            'username' => 'required|string|max:255|unique:users,username,' . $auth->id,
        ]);

        try {
            $payload = $request->only('username');

            if ($request->hasFile('avatar')) {
                $payload['avatar'] = $request->file('avatar')->store('avatars');
            }

            $auth->update($payload);
            
            event(new UserUpdated(new ReceiverResource(Auth::user())));

            return Transformer::success('Success to update profile.', new UserResource(Auth::user()));
        } catch (\Throwable $th) {
            return Transformer::failed('Failed to update profile.');
        }
    }

    /**
     * Change user password.
     *
     * @param   Request  $request
     *
     * @return  JsonResponse
     */
    public function changePassword(Request $request)
    {
        $payload = $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|max:255|confirmed',
        ]);

        try {
            $auth = Auth::user();
            
            if (!Hash::check($payload['old_password'], $auth->password)) {
                return Transformer::failed('Old password is wrong.', null, 401);
            }

            $auth->update([
                'password' => Hash::make($payload['new_password']),
            ]);

            return Transformer::success('Success to update profile.');
        } catch (\Throwable $th) {
            return Transformer::failed('Failed to update profile.');
        }
    }
}

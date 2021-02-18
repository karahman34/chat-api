<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Transformer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
    * Get the authenticated User.
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function me()
    {
        return Transformer::success('Success to get auth data.', auth()->user());
    }

    /**
     * Update last online.
     *
     * @return  JsonResponse
     */
    public function updateLastOnline()
    {
        try {
            Auth::user()->update([
                'last_online' => now()
            ]);

            return Transformer::success('Success to update last online.');
        } catch (\Throwable $th) {
            return Transformer::failed('Failed to update last online.');
        }
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $token = $request->authenticate();
        if (!$token) {
            return Transformer::failed('Invalid credentials.', null, 401);
        }

        return $this->respondWithToken('Success to authenthicate user.', $token);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken('Success to refresh token.', auth()->refresh());
    }

    /**
     * Destroy an authenticated session.
     *
     * @return JsonResponse
     */
    public function destroy()
    {
        Auth::logout();

        return Transformer::success('Successfully logged out.');
    }

    /**
    * Get the token array structure.
    *
    * @param  string $message
    * @param  string $token
    *
    * @return \Illuminate\Http\JsonResponse
    */
    protected function respondWithToken(string $message, string $token)
    {
        return Transformer::success($message, [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => Auth::user()
        ]);
    }
}

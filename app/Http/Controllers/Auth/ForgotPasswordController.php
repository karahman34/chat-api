<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Transformer;
use App\Http\Controllers\Controller;
use App\Jobs\SendForgotPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255'
        ]);

        $user = User::select('id', 'email', 'email_verified_at')
                        ->where('email', $request->get('email'))
                        ->firstOrFail();

        try {
            if (!$user->hasVerifiedEmail()) {
                return Transformer::failed('sorry but, this email is still unverified', null, 400);
            }

            SendForgotPasswordMail::dispatch($user);

            return Transformer::success('forgot password mail has been sent successfully.');
        } catch (\Throwable $th) {
            return Transformer::failed('Failed to send forgot password mail.');
        }
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function verify(Request $request, User $user)
    {
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            echo "Success to verify email address.";
        } else {
            echo "User is already verified.";
        }
    }
}

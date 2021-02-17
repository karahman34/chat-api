<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Transformer;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    private function removeToken(string $email)
    {
        DB::table('password_resets')
                ->where('email', $email)
                ->delete();
    }

    private function updatePassword(Request $request)
    {
        User::where('email', $request->get('email'))
            ->update([
                'password' => Hash::make($request->get('password'))
            ]);
    }

    public function update(Request $request)
    {
        $payload = $request->validate([
            'token' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|min:8|max:255|confirmed',
        ]);

        try {
            $tokenValid = DB::table('password_resets')
                                ->where('token', $payload['token'])
                                ->where('email', $payload['email'])
                                ->first();

            if (!$tokenValid || Carbon::parse($tokenValid->created_at)->addHour()->lessThan(now())) {
                return Transformer::failed('Token is not valid.', null, 400);
            }

            $this->updatePassword($request);

            $this->removeToken($payload['email']);
                
            return Transformer::success('Success to reset user password.');
        } catch (\Throwable $th) {
            return Transformer::failed('Failed to reset user password.');
        }
    }
}

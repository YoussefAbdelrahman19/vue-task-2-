<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;

class NewPasswordController extends Controller
{
    public function forgetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $status = Password::sendResetLink($request->only('email'));
        if ($status == Password::RESET_LINK_SENT) {
            return [
                'status' => __($status)
            ];
        }
        throw ValidationException::withMessages([
            'email' => [trans($status)]
        ]);
    }
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
                $user->tokens()->delete();
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response([
                'message' => 'password reset successfully',
            ]);
        }
        return response([
            'message' => __($status)
        ], 500);
    }
}

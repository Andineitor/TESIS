<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

class ResetPasswordController extends Controller
{
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
            'token' => 'required',
        ]);

        try {

            $response = $this->broker()->reset(
                $this->credentials($request),
                function ($user, $password) {
                    $this->resetPassword($user, $password);
                }
            );

            if ($response == Password::PASSWORD_RESET) {
                Log::info('Reset Password: Password reset successful for email ' . $request->input('email'));

                // Add a log before the redirection
                Log::info('Reset Password: Redirecting user after successful password reset.');

                return response(['message' => trans('passwords.reset')], 200);
            } else {
                Log::warning('Reset Password: Password reset failed for email ' . $request->input('email'));
                return response(['error' => trans($response)], 400);
            }
        } catch (\Exception $e) {
            Log::error('Reset Password: Exception - ' . $e->getMessage());

            // Add a log before returning the error response
            Log::error('Reset Password: Error occurred during password reset.');

            return response(['error' => $e->getMessage()], 500);
        }
    }
}

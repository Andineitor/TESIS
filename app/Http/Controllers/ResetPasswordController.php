<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

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

                return response(['message' => trans('passwords.reset')], 200);
            } else {
                Log::warning('Reset Password: Password reset failed for email ' . $request->input('email'));
                return response(['error' => trans($response)], 400);
            }
        } catch (\Exception $e) {
            Log::error('Reset Password: Exception - ' . $e->getMessage());
            return response(['error' => $e->getMessage()], 500);
        }
    }



    public function verifyToken($token)
    {
        try {
            $user = DB::table('password_resets')->where('token', $token)->first();

            if (!$user) {
                return response()->json(['msg' => 'Token inválido'], 400);
            }

            // Puedes personalizar la lógica según tus necesidades

            return response()->json(['msg' => 'Token confirmado'], 200);
        } catch (\Exception $e) {
            return response()->json(['msg' => 'Algo salió mal, no se puede verificar el token'], 500);
        }
    }

}

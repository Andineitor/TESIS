<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    protected function sendResetLinkResponse($response)
    {
        $resetUrl = 'https://cargod.netlify.app/reset/' . $response['token'];

        // Log información sobre la URL de restablecimiento
        Log::info('Reset URL: ' . $resetUrl);

        return response(['message' => trans($response['message']), 'reset_url' => $resetUrl], 200);
    }


    
}

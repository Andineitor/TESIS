<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForgotPasswordController;
use Illuminate\Support\Facades\Redirect;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/registro', [AuthController::class, 'registro']);
Route::post('/login', [AuthController::class, 'login']);


Route::group(['middleware' => 'auth:sanctum'], function () {
    // Otras rutas protegidas por autenticación

    Route::post('/logout', [AuthController::class, 'logout']);
});


// Cambia la ruta para enviar el correo electrónico de restablecimiento a una solicitud POST
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// Cambia la ruta para manejar el restablecimiento de contraseña a una solicitud POST
// Agrega esta línea en tu archivo de rutas o en el middleware global
Route::middleware('cors')->get('/reset-password/{token}', function ($token) {
    $resetUrl = "https://cargod.netlify.app/reset-password/$token";
    return Redirect::away($resetUrl);
})->name('password.reset');




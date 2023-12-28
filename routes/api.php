<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForgotPasswordController;
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


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::group(['middleware' => 'auth:sanctum'], function () {
    // Otras rutas protegidas por autenticación

    Route::post('/logout', [AuthController::class, 'logout']);
});


// Cambia la ruta para enviar el correo electrónico de restablecimiento a una solicitud POST
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// Cambia la ruta para manejar el restablecimiento de contraseña a una solicitud POST
Route::any('/reset-password/{token}', [ForgotPasswordController::class, 'reset'])->name('password.reset');

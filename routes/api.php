<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Response;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\ForgotPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\SolicituController;
use App\Http\Controllers\VehiculoController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

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


//obtener a los usuarios
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//registrarse y logearse
Route::post('/registro', [AuthController::class, 'registro']);
Route::post('/login', [AuthController::class, 'login']);


Route::group(['middleware' => 'auth:sanctum'], function () {

    // para editar la informacion perosonal
    //TODOS LOS ROLES
    Route::put('/update/{id}', [AuthController::class, 'update']);


    //registrta vehiculo y ver las solicitudes echas
    //PROPIETARIO
    Route::post('/vehiculos', [VehiculoController::class, 'vehiculo']);
    Route::get('/propietario/aceptado', [VehiculoController::class, 'index']);


    //para cambiar estado de la solicitud del auto y todas las solicitudes
    //ADMIN
    Route::post('/estado/{id}', [SolicituController::class, 'estado']);
    Route::get('/solicitudes/pendientes', [SolicituController::class, 'indexPendientes']);



    //contratar vehiculos y ver los contratos que tengo
    //CLIENTE
    Route::put('/contratos/{vehiculoId}/{diasContratados}', [ContratoController::class, 'contrato'])->name('contratos.contrato');
    Route::get('/contratados', [ContratoController::class, 'indexContrato']);
    //mira los vehiculos a disposicion
    Route::get('aceptados', [SolicituController::class, 'indexAceptados']);



    //para deslogearse y eliminar el token de autenticacion
    //RODOS LOS REOLES
    Route::post('/logout', [AuthController::class, 'logout']);
});



//rutas reset password

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');


Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'sendResetLinkResponse'])->name('password.reset');


Route::post('/reset/{token}', [ResetPasswordController::class, 'reset'])->name('password.reset');


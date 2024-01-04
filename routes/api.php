<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContratoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\SolicituController;
use App\Http\Controllers\VehiculoController;

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

    Route::put('/update/{id}', [AuthController::class, 'update']);


    //registrta vehiculo
    Route::post('/vehiculos', [VehiculoController::class, 'vehiculo']);
    

    //muestra los vehiculos solo cuando su estado_id es = aceptada 
    //eso se supone que solo lo hace el admin
    Route::get('/aceptados', [VehiculoController::class, 'index']);

    Route::get('/pendientes', [SolicituController::class, 'indexPendientes']);
    Route::get('/contratados', [ContratoController::class, 'indexContrato']);


    //aqui se supone el admin cambia el estado de un auto 
    //su estaado_id default es pendiente, este puede tene dos mas estado
    // [rechazado,aceptado]
    Route::put('/estado/{id}', [SolicituController::class, 'estado']);

    //este recibe en la tabla contratos un id de un vehiculo y un numero entero de dias
    //este genera un id el cual llena el campo contrato_id(este campo se crea 
    // nullo cuando se crea un vehiculo), asociandolo asi el contrato al vehiculo
    //por lo cual mediante el id del contrato se puede obtener el campo contrato el
    //cual tiene por defecto una cade que dice "contratado"
    Route::post('/contratos/{vehiculoId}/{diasContratados}', [ContratoController::class, 'contrato']);


    Route::get('/solicitudes/pendientes-aprobadas', [SolicituController::class, 'indexPendientes']);




    Route::post('/logout', [AuthController::class, 'logout']);
});



// Ruta para procesar la solicitud de restablecimiento
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');



// Ruta para procesar el restablecimiento de la contraseña
Route::get('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.reset');


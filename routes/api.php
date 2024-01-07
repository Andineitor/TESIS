<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Response;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContratoController;
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
    Route::post('/contratos/{vehiculoId}/{diasContratados}', [ContratoController::class, 'contrato'])->name('contratos.contrato');
    Route::get('/contratados', [ContratoController::class, 'indexContrato']);
    //mira los vehiculos a disposicion
    Route::get('aceptados', [SolicituController::class, 'indexAceptados']);



    //para deslogearse y eliminar el token de autenticacion
    //RODOS LOS REOLES
    Route::post('/logout', [AuthController::class, 'logout']);
});



// Ruta para mostrar el formulario de restablecimiento de contraseña
Route::get('/reset-password/{token}', function (string $token) {
    try {
        return view('auth.reset-password', ['token' => $token]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al procesar la solicitud: ' . $e->getMessage()], 500);
    }
})->middleware('guest')->name('password.reset');

// Ruta para enviar el correo de restablecimiento de contraseña
Route::post('/forgot-password', function (Request $request) {
    try {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? Response::json(['message' => __('Correo enviado con éxito')], 200)
            : Response::json(['error' => 'Error al enviar el correo: ' . __($status)], 422);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al procesar la solicitud: ' . $e->getMessage()], 500);
    }
})->middleware('guest')->name('password.email');




// Ruta para procesar el restablecimiento de contraseña
Route::post('/reset-password', function (Request $request) {
    try {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? Response::json(['message' => __('Contraseña cambiada exitosamente')], 200)
            : Response::json(['error' => 'Error al restablecer la contraseña: ' . __($status)], 422);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al procesar la solicitud: ' . $e->getMessage()], 500);
    }
})->middleware('guest')->name('password.update');

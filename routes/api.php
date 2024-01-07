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



    Route::get('/propietario/aceptado', [VehiculoController::class, 'index']);
    Route::get('/solicitudes/pendientes', [SolicituController::class, 'indexPendientes']);
    Route::get('aceptados', [SolicituController::class, 'indexAceptados']);
    





    Route::post('/logout', [AuthController::class, 'logout']);
});


// Ruta para mostrar el formulario de restablecimiento de contraseña
Route::get('/reset-password/{token}', function (string $token) {
    try {
        // Aquí puedes verificar el token y realizar otras acciones si es necesario
        // Si solo necesitas mostrar el formulario, no necesitas hacer mucho aquí
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

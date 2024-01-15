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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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
    Route::put('/estado/{id}', [SolicituController::class, 'estado']);
    Route::get('/solicitudes/pendientes', [SolicituController::class, 'indexPendientes']);



    //contratar vehiculos y ver los contratos que tengo
    //CLIENTE
    Route::post('/contratos/{vehiculoId}/{diasContratados}', [ContratoController::class, 'contrato'])->name('contratos.contrato');
    Route::get('/contratados', [ContratoController::class, 'indexContrato']);
    //mira los vehiculos a disposicion
    Route::get('aceptados', [SolicituController::class, 'indexAceptados']);



    //para deslogearse y eliminar el token de autenticacion
    //TODOS LOS REOLES
    Route::post('/logout', [AuthController::class, 'logout']);
});



//rutas reset password

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);
 
    $status = Password::sendResetLink(
        $request->only('email'),
    );
 
    return $status === Password::RESET_LINK_SENT
                ? back()->with(['status' => __($status)])
                : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');




Route::get('/reset-password/{token}', function (string $token) {
    $frontendUrl = config('https://cargod.netlify.app');
    $redirectUrl = $frontendUrl . '/reset-password' . $token;

    // Retorna el token en la respuesta JSON
    return response()->json(['token' => $token, 'redirect_url' => $redirectUrl]);
})->middleware('guest')->name('password.reset');




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

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => __('Contraseña restablecida correctamente')], 200);
        } else {
            throw ValidationException::withMessages(['email' => [__($status)]]);
        }
    } catch (\Exception $e) {
        // Log any unexpected exceptions
        Log::error($e);
        return response()->json(['message' => 'Error al restablecer la contraseña'], 500);
    }
})->middleware('guest')->name('password.update');
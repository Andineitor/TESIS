<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //


     //clase que permitira el registro del usuario
     public function registro(Request $request)
{
    $request->validate([
        'nombre' => 'required|string|max:10',
        'apellido' => 'required|string|max:10',
        'cedula'=>'required|string|max:10',
        'direccion' => 'required|string|nullable',
        'celular' => 'required|max:10',
        'email' => 'required|email|unique:users,email|max:30',
        'role_id' => 'required|exists:roles,id',
        'password' => 'required|min:6|string',
    ]);

    try {
        $role = Role::find($request->role_id);

        if (!$role) {
            return response()->json(['res' => false, 'message' => 'El rol especificado no existe'], 400);
        }

        // Creando el usuario con los datos necesarios del usuario
        $user = User::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'cedula' => $request->cedula,
            'direccion' => $request->direccion,
            'celular' => $request->celular,
            'email' => $request->email,
            'role_id' => $role->id,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['user' => $user, 'message' => 'Usuario registrado correctamente'], 201);
    } catch (\Exception $e) {
        Log::error('Error en el registro: ' . $e->getMessage());
        return response()->json(['res' => false, 'message' => 'Error en el registro', 'errors' => $e->getMessage()], 500);
    }
}


 
 
 
     //clase que permitira logear al usuario que previamente 
 
     public function login(Request $request)
     {
 
         $request->validate([
             'email' => 'required|email|max:25',
             'password' => 'required',
         ]);
 
 
 
         // Obtengo el usuario basado en el email
         $user = User::whereEmail($request->email)->first();
 
         // Compruebo el usuario y la contraseña
         if ($user && Hash::check($request->password, $user->password)) {
             $token = $user->createToken('api-token')->plainTextToken;
             $role = $user->role->role;
             return response()->json([
                 "res" => true,
                 "message" => "Bienvenido al sistema, " . $user->nombre,
                 "token" => $token,
                 "role" => $role,
             ], 200);
         } else {
             return response()->json([
                 "res" => false,
                 "message" => "Cuenta o Password Invalido"
             ], 401);
         }
 
 
     }
 
 
     //clase que permite al usuario deslogearse y elminar el token 
 
     public function logout(Request $request)
{
    // Verificar si el usuario está autenticado
    if (Auth::check()) {
        // Obtener el usuario actualmente autenticado
        $user = $request->user();

        // Revocar todos los tokens del usuario 
        $user->tokens()->delete();

        return response()->json(['res' => true, 'message' => 'Adiós ' . $user->nombre]);
    }

    return response()->json(['res' => false, 'message' => 'No se ha iniciado sesión'], 401);
}


     //USER UPDATE



///UPDATE USER

public function update(Request $request, $id)
{
    //
    $input = $request->all();
    $user = User::findOrFail($id);
    $user->update($input);
    return response()->json(['res'=> true,'message'=> 'Modificado correctamente'],200);

}

}

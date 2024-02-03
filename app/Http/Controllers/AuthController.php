<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //


     //clase que permitira el registro del usuario
     public function registro(Request $request)
{
    $request->validate([
        'nombre' => 'required|string|max:20',
        'apellido' => 'required|string|max:20',
        'cedula'=>'required|string|max:10',
        'direccion' => 'required|string|nullable',
        'celular' => 'required|max:10|min:8',
        'email' => 'required|email|unique:users,email|max:30',
        'role_id' => 'required|exists:roles,id',
        'password' => 'required|min:8|string',
        // 'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ],
    
        [
            'cedula.unique' => 'Ya existe un usuario con esa cedula ',
            'email.unique' => 'Ya existe un usuario con ese email ',
        ]
    
);


      // // Subir imagen a Cloudinary
    //   $imagePath = $request->file('avatar')->getRealPath();
    //   $cloudinaryUpload = Cloudinary::upload($imagePath, ['folder' => 'avatares']);


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
            // 'avatar' => $cloudinaryUpload->getSecurePath(),
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






///UPDATE USER

public function update(Request $request, $id)
{
    $request->validate([
        'nombre' => 'string|max:20',
        'apellido' => 'string|max:20',
        // 'cedula' => 'string|max:10',
        'direccion' => 'string|nullable',
        'celular' => 'max:10|min:10',
        // 'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ],
        [
            'email.unique' => 'Ya existe un usuario con ese email ',
        ]
    );

      // // Subir imagen a Cloudinary
    //   $imagePath = $request->file('avatar')->getRealPath();
    //   $cloudinaryUpload = Cloudinary::upload($imagePath, ['folder' => 'avatares']);


    try {
        // Obtener el ID del usuario autenticado
        $userId = Auth::id();

        // Buscar al usuario por su ID
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['res' => false, 'message' => 'Usuario no encontrado'], 404);
        }   

        // Actualizar los campos del usuario con los nuevos valores proporcionados
        $user->update([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'cedula' => $request->cedula,
            'direccion' => $request->direccion,
            'celular' => $request->celular,
            'email' => $request->email,
            // 'avatar' => $cloudinaryUpload->getSecurePath(),
        ]);

        return response()->json(['user' => $user, 'message' => 'Datos actualizados correctamente'], 200);
    } catch (\Exception $e) {
        Log::error('Error al actualizar datos: ' . $e->getMessage());
        return response()->json(['res' => false, 'message' => 'Error al actualizar datos', 'errors' => $e->getMessage()], 500);
    }
}



}

<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use App\Models\Vehiculo;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Auth;

class VehiculoController extends Controller
{


    public function vehiculo(Request $request)
    {
        try {
            $request->validate(
                [
                    'tipo_vehiculo' => 'required|string',
                    'marca' => 'required|string',
                    'placas' => 'required|unique:vehiculos,placas',
                    'numero_pasajero' => 'required|integer',
                    // 'image_url' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ],
                    [
                        'placas.unique' => 'Ya existe un vehículo con estas placas ',
                    ]
            );

            // Subir imagen a Cloudinary
            $imagePath = $request->file('image_url')->getRealPath();
            $cloudinaryUpload = Cloudinary::upload($imagePath, ['folder' => 'vehiculos']);

            // Obtener el ID del estado "pendiente"
            $estadoPendienteId = Solicitud::where('estado', 'pendiente')->value('id');


            // Obtener el ID del usuario autenticado
            $usuarioId = auth()->user()->id;


            // Crear el vehículo en la base de datos y asignar el ID del estado "pendiente"
            $vehiculo = Vehiculo::create([
                'tipo_vehiculo' => $request->input('tipo_vehiculo'),
                'marca' => $request->input('marca'),
                'placas' => $request->input('placas'),
                'numero_pasajero' => $request->input('numero_pasajero'),
                // 'image_url' => $cloudinaryUpload->->getSecurePath(),
                'costo_alquiler' => $request->input('costo_alquiler'),
                'contacto' => $request->input('contacto'),
                'descripcion' => $request->input('descripcion'),
                'solicitud_id' => $estadoPendienteId,
                'user_id' => $usuarioId,
            ]);

            // Respuesta de éxito en formato JSON
            return response()->json(['success' => true, 'message' => 'Vehículo registrado con éxito. Placas: ' . $vehiculo->placas]);
        } catch (\Exception $e) {
            // Respuesta de error en formato JSON
            return response()->json(['success' => false, 'message' => 'Error al registrar el vehículo. ' . $e->getMessage()]);
        }
    }





    public function index()
    {
        try {
            // Obtén el usuario autenticado
            $user = Auth::user();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Usuario no autenticado']);
            }

            // Obtener todos los vehículos pertenecientes al usuario autenticado
            $vehiculos = $user->vehiculos;

            // Puedes devolver la colección de vehículos en la respuesta
            return response()->json(['success' => true, 'estadoPropietario' => $vehiculos]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener los vehículos. ' . $e->getMessage()]);
        }
    }

}

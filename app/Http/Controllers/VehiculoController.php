<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use App\Models\Vehiculo;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

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
      // Check if an image has been provided
      if ($request->hasFile('image_url')) {
        // Subir imagen a Cloudinary
        $imagePath = $request->file('image_url')->getRealPath();
        $cloudinaryUpload = Cloudinary::upload($imagePath, ['folder' => 'vehiculos']);

        // Check if Cloudinary upload was successful
        if (!$cloudinaryUpload || !isset($cloudinaryUpload['secure_url'])) {
            return response()->json(['success' => false, 'message' => 'Error al subir la imagen a Cloudinary']);
        }

        // Set image URL to the secure URL from Cloudinary
        $imageUrl = $cloudinaryUpload['secure_url'];
    } else {
        // If no image is provided, set image URL to null
        $imageUrl = null;
    }

            // Obtener el ID del estado "pendiente"
            $estadoPendienteId = Solicitud::where('estado', 'pendiente')->value('id');



            // Crear el vehículo en la base de datos y asignar el ID del estado "pendiente"
            $vehiculo = Vehiculo::create([
                'tipo_vehiculo' => $request->input('tipo_vehiculo'),
                'marca' => $request->input('marca'),
                'placas' => $request->input('placas'),
                'numero_pasajero' => $request->input('numero_pasajero'),
                'image_url' => $imageUrl,
                'costo_alquiler' => $request->input('costo_alquiler'),
                'contacto' => $request->input('contacto'),
                'descripcion' => $request->input('descripcion'),
                'solicitud_id' => $estadoPendienteId,
                'contrato_id'=> null,

            ]);

            // Obtener el ID del estado "disponible" desde la tabla Contratos
            $contratoDisponibleId = Contrato::where('contrato', 'disponible')->value('id');

            // Actualizar el vehículo con el ID del contrato "disponible"
            $vehiculo->update(['contrato_id' => $contratoDisponibleId]);



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
            // Obtener todos los vehículos que no tienen solicitud pendiente
            $vehiculos = Vehiculo::whereDoesntHave('solicitud', function ($query) {
                $query->where('estado', 'pendiente');
            })->get();

            // Puedes devolver la colección de vehículos en la respuesta
            return response()->json(['success' => true, 'vehiculos' => $vehiculos]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener los vehículos. ' . $e->getMessage()]);
        }
    }

}

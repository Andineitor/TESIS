<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculo;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class VehiculoController extends Controller
{
    public function create()
    {
        return view('vehiculos.create');
    }

    public function vehiculo(Request $request)
{
    try {
        $request->validate([
            'tipo_vehiculo' => 'required|string',
            'marca' => 'required|string',
            'placas' => 'required|unique:vehiculos,placas',
            'numero_pasajero' => 'required|integer',
            'image_url' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ],
        [            'placas.unique' => 'Ya existe un vehículo con estas placas ',
        ]
    
    );

        // Subir imagen a Cloudinary
        $imagePath = $request->file('image_url')->getRealPath();
        $cloudinaryUpload = Cloudinary::upload($imagePath, ['folder' => 'vehiculos']);

        // Crear el vehículo en la base de datos
        $vehiculo = Vehiculo::create([
            'tipo_vehiculo' => $request->input('tipo_vehiculo'),
            'marca' => $request->input('marca'),
            'placas' => $request->input('placas'),
            'numero_pasajero' => $request->input('numero_pasajero'),
            'image_url' => $cloudinaryUpload->getSecurePath(),
            'costo_alquiler' => $request->input('costo_alquiler'),
            'contacto' => $request->input('contacto'),
            'descripcion' => $request->input('descripcion'),
            // 'solicitud_id' => $request->input('solicitud_id', 1),
        ]);

        // Respuesta de éxito en formato JSON
        return response()->json(['success' => true, 'message' => 'Vehículo registrado con éxito. Placas: ' . $vehiculo->placas]);
    } catch (\Exception $e) {
        // Respuesta de error en formato JSON
        return response()->json(['success' => false, 'message' => 'Error al registrar el vehículo. ' . $e->getMessage()]);
    }
}

}
<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Vehiculo;
use Illuminate\Http\Request;

class SolicituController extends Controller
{
    
    
    public function estado(Request $request, $id)
    {
        // Validar que el vehículo existe
        $vehiculo = Vehiculo::findOrFail($id);
    
        // Validar que el nuevo id de estado sea válido
        $nuevoIdEstado = $request->input('estado');
    
        // Validar que el nuevo id de estado exista en la tabla de solicitudes
        $solicitudExistente = Solicitud::find($nuevoIdEstado);
        if (!$solicitudExistente) {
            return response()->json(['error' => 'ID de estado no válido'], 422);
        }
    
        // Cambiar el id de estado del vehículo
        $vehiculo->solicitud_id = $nuevoIdEstado;
        $vehiculo->save();
    
        return response()->json(['message' => 'ID de estado cambiado exitosamente']);
    }

    public function indexPendientes()
    {
        $solicitudes = Solicitud::whereIn('estado', ['pendiente', 'aprobada'])
        ->with('vehiculos') // Eager load para evitar N+1 queries
        ->get();

        return response()->json(['solicitudes' => $solicitudes], 200);
    }
}

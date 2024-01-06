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
    
        // Mensaje según el nuevo estado
        $mensaje = '';
        if ($nuevoIdEstado == 2) {
            $mensaje = 'Solicitud aceptada';
        } elseif ($nuevoIdEstado == 3) {
            $mensaje = 'Solicitud rechazada';
        }
    
        return response()->json(['message' => 'ID de estado cambiado exitosamente', 'estado' => $mensaje]);
    }

    
    public function indexAceptados()
    {
        try {
            // Obtener todos los vehículos con solicitudes aceptadas
            $vehiculosAceptados = Vehiculo::whereHas('solicitud', function ($query) {
                $query->where('estado', 'aceptado');
            })->get();
    
            // Puedes devolver la colección de vehículos en la respuesta
            return response()->json(['success' => true, 'vehiculos_aceptados' => $vehiculosAceptados]);
    
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener los vehículos aceptados. ' . $e->getMessage()]);
        }
    }
    
    public function indexPendientes()
    {
        try {
            // Obtener todos los vehículos con solicitudes pendientes
            $vehiculosPendientes = Vehiculo::whereHas('solicitud', function ($query) {
                $query->where('estado', 'pendiente');
            })->get();
    
            // Puedes devolver la colección de vehículos en la respuesta
            return response()->json(['success' => true, 'vehiculos_pendientes' => $vehiculosPendientes]);
    
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener los vehículos pendientes. ' . $e->getMessage()]);
        }
}
}

<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Vehiculo;
use Database\Factories\VehiculoFactory;
use Illuminate\Http\Request;

class SolicituController extends Controller
{
    
    
    public function estado()
    {
        // Crear un vehículo y una solicitud
        $vehiculo = Vehiculo::factory()->create();
        $solicitud = Solicitud::factory()->create(['estado' => 'nuevo']);

        // Realizar una solicitud para cambiar el estado del vehículo
        $response = $this->json('POST', "/api/solicitud/{$vehiculo->id}/estado", ['estado' => $solicitud->id]);

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(200);

        // Verificar que el estado del vehículo haya cambiado
        $this->assertEquals($solicitud->id, $vehiculo->fresh()->solicitud_id);
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

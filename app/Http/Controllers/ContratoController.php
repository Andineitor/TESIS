<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Vehiculo;
use Illuminate\Http\Request;

class ContratoController extends Controller
{
    public function contrato(Request $request, $vehiculoId, $diasContratados)
    {
        try {
            // Crear un nuevo contrato
            $contrato = Contrato::create([
                'dias' => $diasContratados,
                'contrato' => 'contratado',  // Estado ini cial contratado
            ]);
            
            // Obtener el vehículo por su ID
            $vehiculo = Vehiculo::find($vehiculoId);

            // Asignar el ID del contrato al vehículo
            $vehiculo->update(['contrato_id' => $contrato->id]);

            return response()->json(['success' => true, 'message' => 'Contrato creado y vehículo contratado con éxito']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al contratar el vehículo. ' . $e->getMessage()]);
        }
    }

    public function indexContrato($userId)
{
    try {
        // Obtener todos los vehículos contratados por el usuario específico
        $vehiculosContratados = Vehiculo::where('usuario_id', $userId)->whereHas('contrato', function ($query) {
            $query->where('contrato', 'contratado');
        })->get();

        // Puedes devolver la colección de vehículos en la respuesta
        return response()->json(['success' => true, 'vehiculos_contratados' => $vehiculosContratados]);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Error al obtener los vehículos contratados. ' . $e->getMessage()]);
    }
}
}
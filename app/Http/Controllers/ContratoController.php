<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Vehiculo;
use Illuminate\Http\Request;

class ContratoController extends Controller
{
    public function contrato(Request $request, $vehiculoId, $diasContratados)
    {
        $usuarioId = auth()->user()->id;

        try {
            // Crear un nuevo contrato
            $contrato = Contrato::create([
                'dias' => $diasContratados,
                'contrato' => 'contratado',
                'user_id' => $usuarioId,
                // Estado inicial contratado
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

    public function indexContrato()
    {
        $usuarioId = auth()->user()->id;

        try {
            // Obtener todos los vehículos contratados por el usuario específico
            $contratosUsuario = Contrato::where('user_id', $usuarioId)->get();

            $contratosDetallados = [];

            foreach ($contratosUsuario as $contrato) {
                $vehiculo = Vehiculo::where('contrato_id', $contrato->id)->first();

                if ($vehiculo) {
                    $contratosDetallados[] = [
                        'contrato' => $contrato,
                        'vehiculo' => $vehiculo,
                    ];
                }
            }

            return response()->json(['success' => true, 'contratos_detallados' => $contratosDetallados]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener los contratos. ' . $e->getMessage()]);
        }
    }
}

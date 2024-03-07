<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContratoController extends Controller
{
    public function contrato(Request $request, $vehiculoId, $diasContratados)
    {
        $usuarioId = auth()->user()->id;

        try {
            // Crear un nuevo contrato con la fecha de finalización
            $contrato = Contrato::create([
                'dias' => $diasContratados,
                'contrato' => 'contratado',
                'user_id' => $usuarioId,
                //'fecha_fin' => now()->addDays($diasContratados), // Fecha de finalización
            ]);

            // Obtener el vehículo por su ID
            $vehiculo = Vehiculo::find($vehiculoId);

            // Asignar el ID del contrato al vehículo
            $vehiculo->update(['contrato_id' => $contrato->id]);

           // Log::info('Fecha de finalización calculada:', ['fecha_fin' => $contrato->fecha_fin]);

            // Verificar si el contrato se debe finalizar automáticamente
           // $this->verificarYFinalizarContrato($contrato);

            return response()->json(['success' => true, 'message' => 'Contrato creado y vehículo contratado con éxito']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al contratar el vehículo. ' . $e->getMessage()]);
        }
    }


    // Finalización de los contratos automatizado
    // public function finalizarContratos()
    // {
    //     try {
    //         // Obtener los contratos vencidos
    //         $contratosVencidos = Contrato::where('contrato', 'contratado')
    //             ->whereDate('created_at', '<', now()->subDays($contratosVencidos->dias))
    //             ->get();

    //         foreach ($contratosVencidos as $contrato) {
    //             // Utilizar la relación para obtener el vehículo asociado al contrato
    //             $vehiculo = $contrato->vehiculo;

    //             if ($vehiculo) {
    //                 // Cambiar el estado del vehículo a "disponible" o asignar null
    //                 $vehiculo->update(['contrato_id' => null]);

    //                 // Actualizar el estado del contrato
    //                 $contrato->update(['contrato' => 'finalizado']);
    //             }
    //         }

    //         return response()->json(['success' => true, 'message' => 'Contratos finalizados automáticamente.']);
    //     } catch (\Exception $e) {
    //         return response()->json(['success' => false, 'message' => 'Error al finalizar los contratos. ' . $e->getMessage()]);
    //     }
    // }

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

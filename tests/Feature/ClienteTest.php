<?php

namespace Tests\Feature;

use App\Models\Contrato;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class ClienteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function testContrato()
    {
        $user = User::factory()->create();

        // Crear un contrato asociado al usuario
        $contrato = Contrato::factory()->create(['user_id' => $user->id]);

        // Verificar la relación
        $this->assertInstanceOf(User::class, $contrato->user);
        $this->assertEquals($user->id, $contrato->user->id);
    }

    
    


    public function testIndexVehiculos()
    {
        try {
            // Obtener todos los vehículos con solicitudes aceptadas y contrato NULL
            $vehiculosAceptados = Vehiculo::whereHas('solicitud', function ($query) {
                $query->where('estado', 'aceptado')->whereNull('contrato_id');
            })->get();

            // Realizar una solicitud HTTP al endpoint correspondiente
            $response = $this->get('/aceptados');

            // Verificar que la respuesta tenga un código HTTP 200 (éxito)
            $response->assertStatus(200);

            // Obtener la colección de vehículos desde la respuesta JSON
            $responseVehiculos = collect($response->json('vehiculos_aceptados'));

            // Verificar que la colección de la respuesta tiene la misma cantidad de vehículos aceptados
            $this->assertCount($vehiculosAceptados->count(), $responseVehiculos);
            return response()->json(['success' => true, 'vehiculos_aceptados' => $responseVehiculos]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener los vehículos aceptados. ' . $e->getMessage()]);
        }
    }

    /////////////////////

    public function testContratados()
    {
        //Crear un usuario
        $user = User::factory()->create();
        // Crear un contrato con datos quemados y asociarle un usuario
        $contratoData = [
            'contrato' => 'contratado',
            'dias' => 7,
            'user_id' => $user->id,
        ];
        $contrato = Contrato::create($contratoData);

        // Crear vehículos asociados al contrato
        $vehiculoData1 = [
            'marca' => 'Toyota',
            'modelo' => 'Camry',
            'tipo_vehiculo' => 'Sedan',  
            'placas'=> '2',
            'contrato_id' => $contrato->id,
            'numero_pasajero'=> 3,
            'costo_alquiler'=>4,
            'contacto'=>'sccvsdvsdv',
            'descripcion'=>'sdds',
            'user_id'=> $user->id,
            'contrado_id'=>1,
        ];
        $vehiculo1 = Vehiculo::create($vehiculoData1);
        // Verificar que la relación entre contrato y vehículos esté correctamente establecida
        $this->assertCount(1, $contrato->vehiculos);

        // Verificar que la relación entre contrato y usuario esté correctamente establecida
        $this->assertEquals($user->id, $contrato->user->id);
    }
    }
    
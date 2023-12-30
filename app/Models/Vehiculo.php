<?php
// app/Models/Vehiculo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';

    protected $fillable = [
        'tipo_vehiculo',
        'marca',
        'placas',
        'numero_pasajero',
        'image_url',
        'costo_alquiler',
        'contacto',
        'descripcion',
        'solicitud_id',
    ];

    // Puedes definir relaciones con otros modelos aquí si es necesario

    // Ejemplo de relación con Solicitud
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }
}


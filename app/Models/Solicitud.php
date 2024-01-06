<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;
    protected $table = 'solicitudes'; // Especifica el nombre correcto de la tabla

    protected $fillable = ['estado']; // Lista de atributos que pueden ser asignados masivamente

    // Relación con vehículos
    public function vehiculos()
    {
        return $this->hasone(Vehiculo::class);
    }

    

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    use HasFactory;

    protected $table = 'contratos'; 


    protected $fillable = ['contrato','dias', 'user_id', 'fecha_fin'
];


protected $hidden = [
    'created_at',
    'updated_at',
];
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}

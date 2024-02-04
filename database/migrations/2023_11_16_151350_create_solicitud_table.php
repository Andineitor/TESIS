<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->enum('estado', ['pendiente', 'aceptado', 'rechazado']);
            $table->timestamps();

        });

        DB::table('solicitudes')->insert([
            ['estado' => 'pendiente'],
            ['estado' => 'aceptado'],
            ['estado' => 'rechazado'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};

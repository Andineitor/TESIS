    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         */
        public function up(): void
        {
            Schema::create('vehiculos', function (Blueprint $table) {
                $table->id();
                $table->string('tipo_vehiculo');
                $table->string('marca');
                $table->string('placas')->unique();
                $table->integer('numero_pasajero');
                $table->string('image_url');
                $table->decimal('costo_alquiler',10,2);
                $table->string('contacto');
                $table->string('descripcion');
                $table->foreignId('solicitud_id')->default(1)->constrained('solicitudes'); // Relaciona con la tabla roles
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('vehiculos');
        }
    };

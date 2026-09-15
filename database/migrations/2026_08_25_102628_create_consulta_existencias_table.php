<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
       public function up(): void
    {
        Schema::create('consultas_existencias', function (Blueprint $table) {
            $table->id();

            // Identificador único de la consulta
            $table->uuid('uuid')->unique();

            // Clave del artículo que se escaneó
            $table->string('clave', 100)->index();

            // pendiente | procesando | completado | error
            $table->string('estado', 20)->default('pendiente')->index();

            // Resultado de SAE
            $table->string('descripcion')->nullable();
            $table->decimal('existencia', 18, 4)->nullable();

            // Mensaje en caso de error
            $table->text('error')->nullable();

            $table->timestamps();

            // Ayuda para que el puente encuentre rápido las pendientes
            $table->index(['estado', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultas_existencias');
    }
};

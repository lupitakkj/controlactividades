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
        Schema::create('pausas', function (Blueprint $table) {

            $table->id();

            $table->foreignId('actividad_id')
                ->constrained('actividades')
                ->onDelete('cascade');

            $table->string('motivo');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pausas');
    }
};

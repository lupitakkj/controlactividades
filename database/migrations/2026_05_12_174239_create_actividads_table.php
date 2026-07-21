<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividades', function (Blueprint $table) {

            $table->id();

            $table->foreignId('cliente_id')
                ->nullable()
                ->constrained('clientes')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->string('titulo');

            $table->longText('descripcion')->nullable();

            $table->enum('prioridad', [
                'baja',
                'media',
                'alta',
                'urgente'
            ])->default('media');

            $table->enum('estado', [
                'pendiente',
                'en_proceso',
                'pausada',
                'terminada',
                'cancelada'
            ])->default('pendiente');

            $table->integer('tiempo_estimado')
                ->nullable();

            $table->timestamp('fecha_inicio')
                ->nullable();

            $table->timestamp('fecha_limite')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
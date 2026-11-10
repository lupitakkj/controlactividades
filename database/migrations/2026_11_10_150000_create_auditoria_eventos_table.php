<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditoria_eventos', function (Blueprint $table) {
            $table->id();
            $table->uuid('solicitud_id')->index();
            // Sin FK: conservar la identidad histórica aunque se elimine el usuario.
            $table->unsignedBigInteger('usuario_id')->nullable()->index();
            $table->string('usuario_nombre')->nullable();
            $table->string('accion', 30)->index();
            $table->string('ruta')->nullable()->index();
            $table->string('metodo', 10);
            $table->text('url');
            $table->unsignedSmallInteger('estado_http')->nullable();
            $table->string('modelo')->nullable();
            $table->string('registro_id')->nullable()->index();
            $table->json('antes')->nullable();
            $table->json('despues')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
            $table->index(['modelo', 'registro_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria_eventos');
    }
};

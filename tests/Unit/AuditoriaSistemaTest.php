<?php

use App\Models\Actividad;
use App\Models\AuditoriaEvento;
use App\Models\DespieceProceso;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

uses(Tests\TestCase::class);

beforeEach(function () {
    config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
    DB::purge('sqlite');
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email');
        $table->string('password');
        $table->string('remember_token')->nullable();
        $table->timestamps();
    });
    Schema::create('actividades', function (Blueprint $table) {
        $table->id();
        $table->string('titulo');
        $table->string('estado')->nullable();
        $table->integer('orden')->nullable();
        $table->timestamps();
    });
    Schema::create('despiece_procesos', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('partida_id');
        $table->unsignedBigInteger('proceso_id');
        $table->decimal('cantidad_realizada', 12, 4);
        $table->timestamps();
    });
    (require database_path('migrations/2026_11_10_150000_create_auditoria_eventos_table.php'))->up();
    $this->actingAs(User::create(['name' => 'Operador de prueba', 'email' => 'audit@example.test', 'password' => 'privado']));
});

it('atribuye altas modificaciones y eliminaciones al usuario y conserva valores anteriores', function () {
    $actividad = Actividad::create(['titulo' => 'Inicial']);
    Route::middleware('web')->post('/prueba-auditoria', function () use ($actividad) {
        $actividad->update(['titulo' => 'Corregida']);
        $actividad->delete();
        return response()->noContent();
    })->name('prueba.auditoria');

    $this->post('/prueba-auditoria')->assertNoContent();
    $eventos = AuditoriaEvento::orderBy('id')->get();
    expect($eventos->pluck('accion')->all())->toBe(['modificar', 'eliminar', 'solicitud'])
        ->and($eventos[0]->antes)->toBe(['titulo' => 'Inicial'])
        ->and($eventos[0]->despues)->toBe(['titulo' => 'Corregida'])
        ->and($eventos[0]->usuario_id)->toBe(auth()->id())
        ->and($eventos[0]->usuario_nombre)->toBe('Operador de prueba')
        ->and($eventos[0]->registro_id)->toBe((string) $actividad->id)
        ->and($eventos->pluck('solicitud_id')->unique())->toHaveCount(1)
        ->and($eventos->last()->estado_http)->toBe(204);
});

it('identifica quien registra el avance de cada proceso de despiece', function () {
    Route::middleware('web')->post('/prueba-despiece', function () {
        DespieceProceso::create(['partida_id' => 17, 'proceso_id' => 3, 'cantidad_realizada' => 8]);
        return response()->noContent();
    })->name('despiece.prueba');
    $this->post('/prueba-despiece')->assertNoContent();
    $evento = AuditoriaEvento::where('accion', 'crear')->firstOrFail();
    expect($evento->modelo)->toBe(DespieceProceso::class)
        ->and($evento->despues['partida_id'])->toBe(17)
        ->and($evento->despues['proceso_id'])->toBe(3)
        ->and((float) $evento->despues['cantidad_realizada'])->toBe(8.0)
        ->and($evento->usuario_id)->toBe(auth()->id());
});

it('excluye contrasenas tokens y parametros sensibles de la bitacora', function () {
    Route::middleware('web')->post('/prueba-secretos', function () {
        auth()->user()->update(['name' => 'Nombre actualizado', 'password' => 'secreto-nuevo', 'remember_token' => 'token-privado']);
        return response()->noContent();
    });
    $this->post('/prueba-secretos?token=secreto-query', ['password' => 'secreto-body'])->assertNoContent();
    $evento = AuditoriaEvento::where('accion', 'modificar')->firstOrFail();
    expect($evento->despues)->toBe(['name' => 'Nombre actualizado'])
        ->and($evento->antes)->toBe(['name' => 'Operador de prueba'])
        ->and(DB::table('auditoria_eventos')->get()->toJson())->not->toContain('secreto-query', 'secreto-body', 'secreto-nuevo', 'token-privado');
});

it('descarta cambios revertidos y conserva el intento con error', function () {
    Route::middleware('web')->post('/prueba-rollback', function () {
        DB::transaction(function () {
            Actividad::create(['titulo' => 'No persistir']);
            abort(422);
        });
    });
    $this->post('/prueba-rollback')->assertStatus(422);
    expect(Actividad::count())->toBe(0)
        ->and(AuditoriaEvento::count())->toBe(1)
        ->and(AuditoriaEvento::first()->estado_http)->toBe(422);
});

it('registra consultas y acciones masivas aunque no emitan eventos de modelos', function () {
    Route::middleware('web')->get('/prueba-consulta', fn () => response()->noContent());
    $this->get('/prueba-consulta')->assertNoContent();
    expect(AuditoriaEvento::first()->accion)->toBe('consultar');
    Route::middleware('web')->post('/prueba-masiva', function () {
        DB::table('actividades')->insert(['titulo' => 'Masiva']);
        return response()->noContent();
    })->name('actividades.prueba.masiva');
    $this->post('/prueba-masiva')->assertNoContent();
    expect(AuditoriaEvento::where('ruta', 'actividades.prueba.masiva')->firstOrFail()->usuario_id)->toBe(auth()->id());
});

it('conserva el autor del cierre de sesion aunque deje de estar autenticado', function () {
    $usuarioId = auth()->id();
    Route::middleware('web')->post('/prueba-salir', function () {
        auth()->logout();
        return redirect('/login');
    })->name('logout.prueba');
    $this->post('/prueba-salir')->assertRedirect('/login');
    expect(AuditoriaEvento::firstOrFail()->usuario_id)->toBe($usuarioId)
        ->and(AuditoriaEvento::first()->estado_http)->toBe(302);
});

it('registra cambios confirmados y no inventa modificaciones al guardar sin cambios', function () {
    Route::middleware('web')->post('/prueba-confirmar', function () {
        DB::transaction(function () {
            $actividad = Actividad::create(['titulo' => 'Confirmada']);
            $actividad->save();
        });
        return response()->noContent();
    });
    $this->post('/prueba-confirmar')->assertNoContent();
    expect(AuditoriaEvento::where('accion', 'crear')->count())->toBe(1)
        ->and(AuditoriaEvento::where('accion', 'modificar')->count())->toBe(0)
        ->and(AuditoriaEvento::where('accion', 'solicitud')->count())->toBe(1);
});

it('audita el reordenamiento real de tarjetas de actividades', function () {
    $actividad = Actividad::create(['titulo' => 'Tarjeta', 'estado' => 'pendiente', 'orden' => 0]);
    Route::middleware('web')->post('/prueba-orden-real', [App\Http\Controllers\ActividadController::class, 'guardarOrden']);
    $this->post('/prueba-orden-real', ['columnas' => [
        ['estado' => 'en_proceso', 'tarjetas' => [['id' => $actividad->id, 'orden' => 2]]],
    ]])->assertOk()->assertJson(['success' => true]);
    $evento = AuditoriaEvento::where('accion', 'modificar')->firstOrFail();
    expect($evento->antes)->toBe(['estado' => 'pendiente', 'orden' => 0])
        ->and($evento->despues)->toBe(['estado' => 'en_proceso', 'orden' => 2])
        ->and($evento->usuario_id)->toBe(auth()->id());
});

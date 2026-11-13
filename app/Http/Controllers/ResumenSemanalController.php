<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\SemanaOperativa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResumenSemanalController extends Controller
{
    public const ROLES = ['Administrador', 'Gerente Producción', 'Supervisor'];

    public function show(Request $request)
    {
        abort_unless($request->user()->hasAnyRole(self::ROLES), 403);

        $semana = SemanaOperativa::actual(Carbon::now());
        if (!$semana) {
            return response()->noContent();
        }

        $inicio = $semana->fecha_inicio->toDateString();
        $visto = DB::table('resumen_semanal_vistas')
            ->where('usuario_id', $request->user()->id)
            ->where('inicio_semana', $inicio)
            ->exists();

        if ($request->boolean('automatico') && $visto) {
            return response()->noContent();
        }

        $anterior = SemanaOperativa::where('fecha_inicio', '<', $inicio)
            ->latest('fecha_inicio')
            ->first();

        $cierre = $anterior ? Pedido::whereBetween('fecha_terminado', [
            $anterior->fecha_inicio->startOfDay(),
            $anterior->fecha_fin->endOfDay(),
        ])->selectRaw('COUNT(*) AS terminados, COALESCE(SUM(importe_total), 0) AS importe')->first() : null;

        $pendientes = Pedido::whereNull('fecha_terminado')
            ->whereHas('controlOperativo', fn ($query) => $query
                ->where('fecha_produccion', '<=', $semana->fecha_fin->endOfDay()))
            ->count();

        $rezago = Pedido::whereNull('fecha_terminado')
            ->whereHas('controlOperativo', fn ($query) => $query
                ->where('fecha_produccion', '<', $inicio))
            ->count();

        return response()->view('resumen-semanal.contenido', compact('semana', 'anterior', 'cierre', 'pendientes', 'rezago'))
            ->header('X-Inicio-Semana', $visto ? '' : $inicio)
            ->header('Cache-Control', 'no-store');
    }

    public function visto(Request $request)
    {
        abort_unless($request->user()->hasAnyRole(self::ROLES), 403);

        $datos = $request->validate(['inicio_semana' => ['required', 'date_format:Y-m-d']]);
        $semana = SemanaOperativa::actual(Carbon::now());
        abort_unless($semana?->fecha_inicio->toDateString() === $datos['inicio_semana'], 409);

        DB::table('resumen_semanal_vistas')->upsert([
            'usuario_id' => $request->user()->id,
            'inicio_semana' => $datos['inicio_semana'],
        ], ['usuario_id', 'inicio_semana'], ['inicio_semana']);

        return response()->noContent();
    }
}

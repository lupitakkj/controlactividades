<?php

namespace App\Services;

use App\Models\Partida;
use App\Models\PartidaRuta;
use App\Models\PartidaRutaEtapa;
use App\Models\Proceso;
use App\Models\RutaFabricacion;
use App\Models\RutaFabricacionClave;
use Illuminate\Support\Facades\DB;

class AsignarRutaPorClave
{
    public function aplicarPredeterminada(Partida $partida): bool
    {
        if (!$this->partidaElegible($partida)) {
            return false;
        }

        $clave = $this->normalizarClave($partida->clave);
        if ($clave === '') {
            return false;
        }

        $regla = RutaFabricacionClave::with('ruta.etapas.proceso')
            ->whereRaw('UPPER(TRIM(clave)) = ?', [$clave])
            ->first();

        if (!$regla || !$regla->ruta || !$regla->ruta->activo) {
            return false;
        }

        $asignacion = PartidaRuta::where('partida_id', $partida->id)->first();

        // Una partida sin asignación toma la regla actual.
        if (!$asignacion) {
            $this->guardarSnapshot($partida, $regla->ruta, 'predeterminada', null);
            return true;
        }

        // Nunca pisar una excepción manual ni una ruta que ya empezó.
        if ($asignacion->origen_asignacion !== 'predeterminada' || $this->rutaIniciada($partida, $asignacion)) {
            return false;
        }

        // La asignación automática puede actualizarse si todavía no inició.
        if ((int) $asignacion->ruta_fabricacion_id === (int) $regla->ruta->id) {
            return false;
        }

        $this->guardarSnapshot($partida, $regla->ruta, 'predeterminada', null, $asignacion);
        return true;
    }

    public function asignarManual(Partida $partida, RutaFabricacion $ruta, ?int $usuarioId): void
    {
        if ($this->rutaActualIniciada($partida)) {
            throw new \RuntimeException('No se puede cambiar la ruta porque la partida ya tiene avance registrado en uno de sus procesos.');
        }

        $this->guardarSnapshot($partida, $ruta, 'manual', $usuarioId, $partida->rutaAsignada()->first());
    }

    public function rutaIniciada(Partida $partida, PartidaRuta $asignacion): bool
    {
        $idsProcesos = $asignacion->etapas()->pluck('proceso_id')->all();
        if (!$idsProcesos) {
            return false;
        }

        return $partida->despieceProcesos()
            ->whereIn('proceso_id', $idsProcesos)
            ->where(function ($q) {
                $q->where('cantidad_realizada', '>', 0)
                  ->orWhere('porcentaje', '>', 0);
            })
            ->exists();
    }

    public function rutaActualIniciada(Partida $partida): bool
    {
        $asignacion = $partida->rutaAsignada()->with('etapas')->first();
        return $asignacion ? $this->rutaIniciada($partida, $asignacion) : false;
    }

    /**
     * Calcula el impacto de una regla sin escribir en la base de datos.
     * Devuelve resumen y detalle por partida para la pantalla de confirmación.
     */
    public function previsualizarClave(string $clave, RutaFabricacion $ruta): array
    {
        $clave = $this->normalizarClave($clave);
        $procesos = Proceso::where('activo', true)->orderBy('orden')->get(['id', 'orden']);
        $partidas = Partida::with(['pedido.controlOperativo', 'despieceProcesos', 'rutaAsignada.etapas'])
            ->whereRaw('UPPER(TRIM(clave)) = ?', [$clave])
            ->orderBy('id')
            ->get();

        $resumen = [
            'asignar' => 0,
            'actualizar' => 0,
            'conservar' => 0,
            'sin_cambios' => 0,
            'excluir' => 0,
        ];
        $detalle = [];

        foreach ($partidas as $partida) {
            $estado = mb_strtolower(trim((string) ($partida->pedido?->controlOperativo?->estado_operativo ?? '')), 'UTF-8');
            $avance = $this->calcularAvancePartida($partida, $procesos);
            $asignacion = $partida->rutaAsignada;
            $accion = 'excluir';
            $motivo = '';

            if ($estado === 'terminado') {
                $motivo = 'Pedido terminado';
            } elseif ($avance >= 1.0) {
                $motivo = 'Partida al 100 %';
            } elseif (!$asignacion) {
                $accion = 'asignar';
                $motivo = 'No tiene ruta asignada';
            } elseif ($asignacion->origen_asignacion === 'manual') {
                $accion = 'conservar';
                $motivo = 'Excepción manual';
            } elseif ($this->rutaIniciada($partida, $asignacion)) {
                $accion = 'conservar';
                $motivo = 'La ruta ya tiene avance';
            } elseif ((int) $asignacion->ruta_fabricacion_id !== (int) $ruta->id) {
                $accion = 'actualizar';
                $motivo = 'Ruta automática sin iniciar';
            } else {
                $accion = 'sin_cambios';
                $motivo = 'Ya tiene la ruta seleccionada';
            }

            $resumen[$accion]++;
            $detalle[] = [
                'partida_id' => $partida->id,
                'pedido_no' => $partida->pedido?->pedido_no ?? '—',
                'clave' => $partida->clave,
                'descripcion' => $partida->descripcion,
                'cantidad' => (float) $partida->cantidad,
                'avance' => $avance,
                'ruta_actual' => $asignacion?->ruta_nombre_snapshot ?? 'Sin ruta',
                'accion' => $accion,
                'motivo' => $motivo,
            ];
        }

        return ['resumen' => $resumen, 'detalle' => $detalle];
    }

    private function calcularAvancePartida(Partida $partida, $procesos): float
    {
        if ($procesos->isEmpty()) {
            return 0.0;
        }

        $despieces = $partida->despieceProcesos->keyBy('proceso_id');
        $suma = 0.0;
        $cantidadAplicables = 0;
        foreach ($procesos as $proceso) {
            $despiece = $despieces->get($proceso->id);
            if (!$despiece) {
                $cantidadAplicables++;
                continue;
            }
            if (!$despiece->aplica) {
                continue;
            }
            $suma += max(0.0, min(1.0, (float) $despiece->porcentaje));
            $cantidadAplicables++;
        }

        return $cantidadAplicables > 0 ? max(0.0, min(1.0, $suma / $cantidadAplicables)) : 0.0;
    }

    public function sincronizarClave(string $clave, RutaFabricacion $ruta): array
    {
        $clave = $this->normalizarClave($clave);
        $resumen = ['asignadas' => 0, 'actualizadas' => 0, 'protegidas' => 0, 'excluidas' => 0, 'sin_cambios' => 0];

        Partida::whereRaw('UPPER(TRIM(clave)) = ?', [$clave])
            ->orderBy('id')
            ->chunkById(100, function ($partidas) use ($ruta, &$resumen) {
                foreach ($partidas as $partida) {
                    if (!$this->partidaElegible($partida)) {
                        $resumen['excluidas']++;
                        continue;
                    }

                    $asignacion = $partida->rutaAsignada()->with('etapas')->first();

                    if (!$asignacion) {
                        $this->guardarSnapshot($partida, $ruta, 'predeterminada', null);
                        $resumen['asignadas']++;
                        continue;
                    }

                    if ($asignacion->origen_asignacion === 'manual' || $this->rutaIniciada($partida, $asignacion)) {
                        $resumen['protegidas']++;
                        continue;
                    }

                    if ((int) $asignacion->ruta_fabricacion_id !== (int) $ruta->id) {
                        $this->guardarSnapshot($partida, $ruta, 'predeterminada', null, $asignacion);
                        $resumen['actualizadas']++;
                    } else {
                        $resumen['sin_cambios']++;
                    }
                }
            });

        return $resumen;
    }


    /**
     * Solo se asignan rutas a partidas de pedidos no terminados y con avance menor al 100%.
     */
    private function partidaElegible(Partida $partida): bool
    {
        $partida->loadMissing(['pedido.controlOperativo', 'despieceProcesos']);

        $estado = mb_strtolower(trim((string) ($partida->pedido?->controlOperativo?->estado_operativo ?? '')), 'UTF-8');
        if ($estado === 'terminado') {
            return false;
        }

        $procesos = Proceso::where('activo', true)->orderBy('orden')->get(['id', 'orden']);
        if ($procesos->isEmpty()) {
            return true;
        }

        $despieces = $partida->despieceProcesos->keyBy('proceso_id');
        $suma = 0.0;
        $aplicables = 0;

        foreach ($procesos as $proceso) {
            $despiece = $despieces->get($proceso->id);
            if (!$despiece) {
                $aplicables++;
                continue;
            }
            if (!$despiece->aplica) {
                continue;
            }
            $suma += max(0.0, min(1.0, (float) $despiece->porcentaje));
            $aplicables++;
        }

        $avance = $aplicables > 0 ? $suma / $aplicables : 0.0;
        return $avance < 1.0;
    }

    private function guardarSnapshot(
        Partida $partida,
        RutaFabricacion $ruta,
        string $origen,
        ?int $usuarioId,
        ?PartidaRuta $asignacion = null
    ): void {
        DB::transaction(function () use ($partida, $ruta, $origen, $usuarioId, $asignacion) {
            $ruta->loadMissing('etapas.proceso');

            $asignacion = $asignacion ?: PartidaRuta::firstOrNew(['partida_id' => $partida->id]);
            $asignacion->fill([
                'ruta_fabricacion_id' => $ruta->id,
                'ruta_nombre_snapshot' => $ruta->nombre,
                'asignada_por' => $usuarioId,
                'origen_asignacion' => $origen,
            ]);
            $asignacion->save();

            // Rehacer etapas solo si la ruta todavía no ha comenzado (validado por quien llama).
            $asignacion->etapas()->delete();
            foreach ($ruta->etapas as $etapa) {
                $asignacion->etapas()->create([
                    'proceso_id' => $etapa->proceso_id,
                    'nombre_proceso_snapshot' => $etapa->proceso?->nombre ?? 'Proceso',
                    'orden' => $etapa->orden,
                    'obligatorio' => $etapa->obligatorio,
                ]);
            }
        });
    }

    private function normalizarClave(?string $clave): string
    {
        return mb_strtoupper(trim((string) $clave), 'UTF-8');
    }
}

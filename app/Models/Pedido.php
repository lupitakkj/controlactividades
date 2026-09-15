<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $fillable = [
        'pedido_no',
        'cliente',
        'su_pedido',
        'enviar_a',
        'importe_total',
        'fecha_solicitud',
        'fecha_entrega',
        'fecha_terminado',
        'nombre_archivo',
        'importacion_id',
    ];

    protected $casts = [
        'importe_total' => 'decimal:4',
        'fecha_solicitud' => 'date',
        'fecha_entrega' => 'date',
        'fecha_terminado' => 'date',
    ];

    public function importacion(): BelongsTo
    {
        return $this->belongsTo(
            Importacion::class,
            'importacion_id'
        );
    }

    public function partidas(): HasMany
    {
        return $this->hasMany(
            Partida::class,
            'pedido_id'
        );
    }

    public function controlOperativo(): HasOne
    {
        return $this->hasOne(
            ControlOperativo::class,
            'pedido_id'
        );
    }

    /**
     * =========================================================================
     * AVANCE TOTAL DEL PEDIDO
     * =========================================================================
     *
     * Reglas:
     *
     * - Cada proceso activo debe tener:
     *      1. Cantidad capturada
     *      2. O N/A
     *
     * - Si un proceso está vacío, se considera 0% para el cálculo.
     *
     * - N/A no participa en el promedio.
     *
     * - Una partida solamente puede llegar a 100% cuando TODOS
     *   sus procesos activos están resueltos.
     *
     * - El avance del pedido es el promedio de las partidas.
     *
     */

    public function getAvanceAttribute(): ?float
    {
        $partidas = $this->partidas;

        if ($partidas->isEmpty()) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | PROCESOS ACTIVOS
        |--------------------------------------------------------------------------
        */

        $procesosActivos = Proceso::where('activo', true)
            ->orderBy('orden')
            ->get();

        if ($procesosActivos->isEmpty()) {
            return 0;
        }

        /*
        |--------------------------------------------------------------------------
        | ACUMULAR AVANCE DE PARTIDAS
        |--------------------------------------------------------------------------
        */

        $sumaAvances = 0;
        $cantidadPartidas = 0;

        foreach ($partidas as $partida) {

            /*
            |--------------------------------------------------------------------------
            | OBTENER PROCESOS CAPTURADOS DE LA PARTIDA
            |--------------------------------------------------------------------------
            */

            $despieces = $partida->despieceProcesos
                ->keyBy('proceso_id');

            $sumaPorcentajes = 0;
            $cantidadProcesosAplicables = 0;

            foreach ($procesosActivos as $proceso) {

                /*
                |--------------------------------------------------------------------------
                | BUSCAR CAPTURA DEL PROCESO
                |--------------------------------------------------------------------------
                */

                $despiece = $despieces->get($proceso->id);

                /*
                |--------------------------------------------------------------------------
                | PROCESO VACÍO
                |--------------------------------------------------------------------------
                |
                | No existe registro:
                |
                |      → todavía no está capturado
                |      → cuenta como 0%
                |
                | Esto evita que una sola captura al 100%
                | haga que toda la partida quede terminada.
                |
                */

                if (!$despiece) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | N/A
                |--------------------------------------------------------------------------
                |
                | N/A significa que el proceso está resuelto,
                | pero no participa en el promedio.
                |
                */

                if (!$despiece->aplica) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | CANTIDAD CAPTURADA
                |--------------------------------------------------------------------------
                */

                $porcentaje = (float) $despiece->porcentaje;

                $porcentaje = max(
                    0,
                    min(
                        1,
                        $porcentaje
                    )
                );

                $sumaPorcentajes += $porcentaje;
                $cantidadProcesosAplicables++;
            }

            /*
            |--------------------------------------------------------------------------
            | CALCULAR AVANCE DE LA PARTIDA
            |--------------------------------------------------------------------------
            */

            if ($cantidadProcesosAplicables > 0) {

                $avancePartida =
                    $sumaPorcentajes /
                    $cantidadProcesosAplicables;

            } else {

                $avancePartida = 0;
            }

            /*
            |--------------------------------------------------------------------------
            | VERIFICAR SI FALTA ALGÚN PROCESO
            |--------------------------------------------------------------------------
            |
            | Si falta aunque sea un proceso, la partida NO puede
            | considerarse 100%.
            |
            */

            $todosLosProcesosResueltos = true;

            foreach ($procesosActivos as $proceso) {

                $despiece = $despieces->get($proceso->id);

                /*
                |--------------------------------------------------------------------------
                | No existe captura = VACÍO
                |--------------------------------------------------------------------------
                */

                if (!$despiece) {
                    $todosLosProcesosResueltos = false;
                    break;
                }

                /*
                |--------------------------------------------------------------------------
                | Si aplica=true debe existir cantidad.
                |--------------------------------------------------------------------------
                */

                if ($despiece->aplica) {

                    if (
                        $despiece->cantidad_realizada === null
                    ) {
                        $todosLosProcesosResueltos = false;
                        break;
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | SI FALTA UN PROCESO
            |--------------------------------------------------------------------------
            |
            | No permitimos que la partida aparezca como 100%.
            |
            */

            if (
                !$todosLosProcesosResueltos &&
                $avancePartida >= 1
            ) {
                $avancePartida = 0.9999;
            }

            /*
            |--------------------------------------------------------------------------
            | LIMITAR AVANCE
            |--------------------------------------------------------------------------
            */

            $avancePartida = max(
                0,
                min(
                    1,
                    $avancePartida
                )
            );

            $sumaAvances += $avancePartida;
            $cantidadPartidas++;
        }

        /*
        |--------------------------------------------------------------------------
        | CALCULAR AVANCE DEL PEDIDO
        |--------------------------------------------------------------------------
        */

        if ($cantidadPartidas === 0) {
            return null;
        }

        $avancePedido =
            $sumaAvances /
            $cantidadPartidas;

        /*
        |--------------------------------------------------------------------------
        | LIMITAR ENTRE 0 Y 1
        |--------------------------------------------------------------------------
        */

        return max(
            0,
            min(
                1,
                $avancePedido
            )
        );
    }
}
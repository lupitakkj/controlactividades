<?php

namespace App\Models;

use App\Models\ActividadControlOperativo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $fillable = [
        'pedido_no',
        'cliente',
        'su_pedido',
        'enviar_a',
        'importe_total',
        'pedido_interno',
        'fecha_solicitud',
        'fecha_entrega',
        'fecha_terminado',
        'nombre_archivo',
        'importacion_id',
    ];

    protected $casts = [
        'importe_total' => 'decimal:4',
        'pedido_interno' => 'boolean',
        'fecha_solicitud' => 'date',
        'fecha_entrega' => 'date',
        'fecha_terminado' => 'date',
    ];

    /**
     * Cache de procesos activos durante la petición actual.
     */
    protected static ?Collection $procesosActivosCache = null;

    /**
     * Cache del avance calculado para este pedido.
     */
    protected ?float $avanceCalculado = null;

    protected bool $avanceYaCalculado = false;


    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

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


    public function actividadesControlOperativo(): HasMany
    {
        return $this->hasMany(
            ActividadControlOperativo::class,
            'pedido_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PROCESOS ACTIVOS
    |--------------------------------------------------------------------------
    |
    | Se consultan una sola vez durante la petición.
    |
    */

    protected static function obtenerProcesosActivos(): Collection
    {
        if (static::$procesosActivosCache === null) {

            static::$procesosActivosCache =
                Proceso::where('activo', true)
                    ->orderBy('orden')
                    ->get();
        }

        return static::$procesosActivosCache;
    }


    /*
    |--------------------------------------------------------------------------
    | AVANCE TOTAL DEL PEDIDO
    |--------------------------------------------------------------------------
    |
    | El avance se calcula de la siguiente manera:
    |
    | 1. Cada partida obtiene su avance mediante sus procesos.
    |
    | 2. Los procesos con aplica = false (N/A)
    |    no participan en el promedio.
    |
    | 3. Un proceso con 0% sí participa.
    |
    | 4. Si falta un proceso o no tiene cantidad realizada,
    |    la partida no puede llegar a 100%.
    |
    | 5. El avance del pedido es un PROMEDIO PONDERADO
    |    por la cantidad de piezas de cada partida.
    |
    | Fórmula:
    |
    |   Avance pedido =
    |
    |   SUMA(avance partida × cantidad partida)
    |   --------------------------------------
    |            SUMA(cantidad partida)
    |
    */

    public function getAvanceAttribute(): ?float
    {
        /*
        |--------------------------------------------------------------------------
        | EVITAR RECALCULAR EL MISMO PEDIDO
        |--------------------------------------------------------------------------
        */

        if ($this->avanceYaCalculado) {
            return $this->avanceCalculado;
        }


        /*
        |--------------------------------------------------------------------------
        | PARTIDAS
        |--------------------------------------------------------------------------
        */

        $partidas = $this->partidas;


        if ($partidas->isEmpty()) {

            $this->avanceCalculado = null;
            $this->avanceYaCalculado = true;

            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | PROCESOS ACTIVOS
        |--------------------------------------------------------------------------
        */

        $procesosActivos =
            static::obtenerProcesosActivos();


        if ($procesosActivos->isEmpty()) {

            $this->avanceCalculado = 0;
            $this->avanceYaCalculado = true;

            return 0;
        }


        /*
        |--------------------------------------------------------------------------
        | ACUMULADORES DEL PROMEDIO PONDERADO
        |--------------------------------------------------------------------------
        |
        | sumaAvancePonderado:
        |
        |   avance de partida × cantidad
        |
        | sumaCantidad:
        |
        |   cantidad total de piezas
        |
        */

        $sumaAvancePonderado = 0;

        $sumaCantidad = 0;


        /*
        |--------------------------------------------------------------------------
        | RECORRER PARTIDAS
        |--------------------------------------------------------------------------
        */

        foreach ($partidas as $partida) {

            /*
            |--------------------------------------------------------------------------
            | CANTIDAD DE LA PARTIDA
            |--------------------------------------------------------------------------
            */

            $cantidadPartida =
                (float) $partida->cantidad;


            /*
            |--------------------------------------------------------------------------
            | SI LA CANTIDAD ES 0 O MENOR
            |--------------------------------------------------------------------------
            |
            | No podemos utilizar una cantidad negativa o cero
            | como peso.
            |
            */

            if ($cantidadPartida <= 0) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | PROCESOS DE LA PARTIDA
            |--------------------------------------------------------------------------
            */

            $despieces =
                $partida->despieceProcesos
                    ->keyBy('proceso_id');


            $sumaPorcentajes = 0;

            $cantidadProcesosAplicables = 0;

            $todosLosProcesosResueltos = true;


            /*
            |--------------------------------------------------------------------------
            | RECORRER PROCESOS ACTIVOS
            |--------------------------------------------------------------------------
            */

            foreach ($procesosActivos as $proceso) {

                $despiece =
                    $despieces->get($proceso->id);


                /*
                |--------------------------------------------------------------------------
                | PROCESO FALTANTE
                |--------------------------------------------------------------------------
                */

                if (!$despiece) {

                    $todosLosProcesosResueltos = false;

                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | N/A
                |--------------------------------------------------------------------------
                |
                | Si aplica = false significa que el proceso
                | no corresponde a esta partida.
                |
                | Por lo tanto no participa en el promedio.
                |
                */

                if (!$despiece->aplica) {
                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | PROCESO APLICABLE
                |--------------------------------------------------------------------------
                */

                if ($despiece->cantidad_realizada === null) {

                    $todosLosProcesosResueltos = false;
                }


                /*
                |--------------------------------------------------------------------------
                | PORCENTAJE DEL PROCESO
                |--------------------------------------------------------------------------
                */

                $porcentaje =
                    (float) $despiece->porcentaje;


                /*
                |--------------------------------------------------------------------------
                | LIMITAR PORCENTAJE ENTRE 0 Y 1
                |--------------------------------------------------------------------------
                */

                $porcentaje =
                    max(
                        0,
                        min(
                            1,
                            $porcentaje
                        )
                    );


                /*
                |--------------------------------------------------------------------------
                | ACUMULAR PORCENTAJE
                |--------------------------------------------------------------------------
                */

                $sumaPorcentajes +=
                    $porcentaje;


                $cantidadProcesosAplicables++;
            }


            /*
            |--------------------------------------------------------------------------
            | AVANCE DE LA PARTIDA
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
            | NO PERMITIR 100% SI FALTA UN PROCESO
            |--------------------------------------------------------------------------
            */

            if (
                !$todosLosProcesosResueltos &&
                $avancePartida >= 1
            ) {

                $avancePartida = 0.9999;
            }


            /*
            |--------------------------------------------------------------------------
            | LIMITAR AVANCE DE PARTIDA
            |--------------------------------------------------------------------------
            */

            $avancePartida =
                max(
                    0,
                    min(
                        1,
                        $avancePartida
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | PROMEDIO PONDERADO
            |--------------------------------------------------------------------------
            |
            | Aquí está el cambio principal.
            |
            | En lugar de:
            |
            |   sumar avances / número de partidas
            |
            | hacemos:
            |
            |   avance × cantidad
            |
            */

            $sumaAvancePonderado +=
                $avancePartida *
                $cantidadPartida;


            /*
            |--------------------------------------------------------------------------
            | ACUMULAR CANTIDAD TOTAL
            |--------------------------------------------------------------------------
            */

            $sumaCantidad +=
                $cantidadPartida;
        }


        /*
        |--------------------------------------------------------------------------
        | SIN CANTIDAD VÁLIDA
        |--------------------------------------------------------------------------
        */

        if ($sumaCantidad <= 0) {

            $this->avanceCalculado = null;
            $this->avanceYaCalculado = true;

            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | AVANCE PONDERADO DEL PEDIDO
        |--------------------------------------------------------------------------
        */

        $avancePedido =
            $sumaAvancePonderado /
            $sumaCantidad;


        /*
        |--------------------------------------------------------------------------
        | LIMITAR ENTRE 0 Y 1
        |--------------------------------------------------------------------------
        */

        $avancePedido =
            max(
                0,
                min(
                    1,
                    $avancePedido
                )
            );


        /*
        |--------------------------------------------------------------------------
        | GUARDAR EN CACHE DEL MODELO
        |--------------------------------------------------------------------------
        */

        $this->avanceCalculado =
            $avancePedido;

        $this->avanceYaCalculado =
            true;


        return $avancePedido;
    }
}
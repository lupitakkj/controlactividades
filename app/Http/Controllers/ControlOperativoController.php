<?php

namespace App\Http\Controllers;

use App\Models\ActividadControlOperativo;
use App\Models\Pedido;
use App\Models\Proceso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ControlOperativoController extends Controller
{
    /**
     * Mostrar Control Operativo.
     */
    public function index(Request $request)
    {
        $pedidos = Pedido::with([
            'controlOperativo',

            'actividadesControlOperativo' => function ($query) {
                $query->where('activa', true);
            },

            'partidas.despieceProcesos.proceso',
        ])
            ->orderByRaw('CAST(pedido_no AS UNSIGNED) ASC')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | PROCESO CON MENOR AVANCE
        |--------------------------------------------------------------------------
        */

        foreach ($pedidos as $pedido) {

            $pedido->proceso_menor_avance =
                $this->obtenerProcesoMenorAvance($pedido);
        }
        /*
            |--------------------------------------------------------------------------
            | PEDIDOS ACTIVOS
            |--------------------------------------------------------------------------
            |
            | No contamos pedidos internos.
            | Tampoco contamos pedidos con avance de 100%.
            |
            */

        $pedidosActivos = $pedidos->filter(function ($pedido) {
            // Mantener el mismo criterio que el filtro "Pendientes"
            // de la tabla: pedidos con avance menor al 100%.
            return $pedido->avance !== null
                && $pedido->avance < 1;
        });




        /*
        |--------------------------------------------------------------------------
        | AVANCE GLOBAL
        |--------------------------------------------------------------------------
        |
        | Avance global ponderado por cantidad de piezas.
        |
        | Cada pedido pesa de acuerdo con la cantidad total
        | de piezas que contiene.
        |
        | Fórmula:
        |
        |   SUMA(avance pedido × piezas pedido)
        |   ---------------------------------
        |          SUMA(piezas pedido)
        |
        | Los pedidos internos también participan.
        |
        */

        $sumaAvancePonderado = 0;
        $sumaPiezas = 0;

        foreach ($pedidosActivos as $pedido) {

            /*
            |--------------------------------------------------------------------------
            | CANTIDAD TOTAL DE PIEZAS DEL PEDIDO
            |--------------------------------------------------------------------------
            */

            $piezasPedido = $pedido->partidas->sum(function ($partida) {

                return (float) $partida->cantidad;
            });

            /*
            |--------------------------------------------------------------------------
            | IGNORAR PEDIDOS SIN PIEZAS VÁLIDAS
            |--------------------------------------------------------------------------
            */

            if ($piezasPedido <= 0) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | ACUMULAR AVANCE PONDERADO
            |--------------------------------------------------------------------------
            */

            $sumaAvancePonderado +=
                ((float) $pedido->avance * $piezasPedido);

            $sumaPiezas += $piezasPedido;
        }

        /*
        |--------------------------------------------------------------------------
        | CALCULAR AVANCE GLOBAL
        |--------------------------------------------------------------------------
        */

        if ($sumaPiezas > 0) {

            $avanceGlobal =
                $sumaAvancePonderado / $sumaPiezas;
        } else {

            $avanceGlobal = 0;
        }


        /*
        |--------------------------------------------------------------------------
        | VISTA
        |--------------------------------------------------------------------------
        */

        return view(
            'control-operativo.index',
            compact(
                'pedidos',
                'pedidosActivos',
                'avanceGlobal'
            )
        );
    }

    /**
     * Exporta los pedidos que quedaron visibles con los filtros actuales.
     */
    public function exportar(Request $request)
    {
        $datos = $request->validate([
            'pedido_ids' => 'required|array|min:1|max:5000',
            'pedido_ids.*' => 'required|integer|distinct',
        ]);

        $pedidos = Pedido::with('controlOperativo')
            ->whereIn('id', $datos['pedido_ids'])
            ->get()
            ->keyBy('id');

        $libro = new Spreadsheet();
        $hoja = $libro->getActiveSheet();
        $hoja->setTitle('Pedidos');
        $hoja->fromArray(['Pedido', 'Cliente', 'Fecha Producción'], null, 'A1');

        $hoja->getStyle('A1:C1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $hoja->getStyle('A1:C1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('2563EB');
        $hoja->freezePane('A2');
        $hoja->setAutoFilter('A1:C1');

        $fila = 2;
        foreach ($datos['pedido_ids'] as $pedidoId) {
            $pedido = $pedidos->get($pedidoId);
            if (!$pedido) {
                continue;
            }

            $hoja->setCellValueExplicit(
                'A' . $fila,
                (string) $pedido->pedido_no,
                \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
            );
            $hoja->setCellValue('B' . $fila, $pedido->cliente ?? '');

            $fechaProduccion = $pedido->controlOperativo?->fecha_produccion;
            if ($fechaProduccion) {
                $hoja->setCellValue(
                    'C' . $fila,
                    ExcelDate::dateTimeToExcel($fechaProduccion->copy()->startOfDay())
                );
                $hoja->getStyle('C' . $fila)
                    ->getNumberFormat()
                    ->setFormatCode('dd/mm/yyyy');
            }

            $fila++;
        }

        $hoja->getColumnDimension('A')->setWidth(18);
        $hoja->getColumnDimension('B')->setWidth(36);
        $hoja->getColumnDimension('C')->setWidth(22);

        $nombreArchivo = 'pedidos_control_operativo_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($libro) {
            (new Xlsx($libro))->save('php://output');
            $libro->disconnectWorksheets();
        }, $nombreArchivo, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }


    /**
     * Actualizar Fecha de Producción.
     */
    public function actualizarFechaProduccion(
        Request $request,
        Pedido $pedido
    ) {
        $request->validate([
            'fecha_produccion' => [
                'nullable',
                'date'
            ],
        ]);

        $control = $pedido->controlOperativo;

        if (!$control) {
            $control = $pedido->controlOperativo()->create([
                'fecha_produccion' => $request->fecha_produccion,
                'estado_operativo' => 'Por Iniciar',
            ]);
        } else {
            $control->fecha_produccion =
                $request->fecha_produccion;

            $control->save();
        }

        return response()->json([
            'success' => true,
            'fecha_produccion' =>
            $control->fecha_produccion
                ? $control->fecha_produccion->format('Y-m-d')
                : null,
        ]);
    }

    /**
     * Crear actividad.
     */
    public function crearActividad(
        Request $request,
        Pedido $pedido
    ) {
        $request->validate([
            'actividad' => [
                'required',
                'string',
                'max:1000'
            ],

            'responsable' => [
                'nullable',
                'string',
                'max:255'
            ],

            'fecha_limite' => [
                'nullable',
                'date'
            ],
        ]);

        $actividad = $pedido->actividadesControlOperativo()->create([
            'actividad' => $request->input('actividad'),
            'responsable' => $request->input('responsable'),
            'completada' => false,
            'fecha_limite' => $request->input('fecha_limite'),
        ]);

        return response()->json([
            'success' => true,

            'actividad' => $actividad,

            'url_actualizar' => route(
                'control-operativo.actividad.actualizar',
                $actividad
            ),

            'url_eliminar' => route(
                'control-operativo.actividad.eliminar',
                $actividad
            ),
        ]);
    }


    /**
     * Actualizar actividad.
     */
    public function actualizarActividad(
        Request $request,
        ActividadControlOperativo $actividad
    ) {
        $request->validate([
            'actividad' => [
                'sometimes',
                'required',
                'string',
                'max:1000'
            ],

            'responsable' => [
                'sometimes',
                'nullable',
                'string',
                'max:255'
            ],

            'completada' => [
                'sometimes',
                'boolean'
            ],

            'fecha_limite' => [
                'sometimes',
                'nullable',
                'date'
            ],
        ]);

        $datos = [];

        if ($request->has('actividad')) {
            $datos['actividad'] =
                $request->input('actividad');
        }

        if ($request->has('responsable')) {
            $datos['responsable'] =
                $request->input('responsable');
        }

        if ($request->has('completada')) {
            $datos['completada'] =
                $request->boolean('completada');
        }

        if ($request->has('fecha_limite')) {
            $datos['fecha_limite'] =
                $request->input('fecha_limite');
        }

        $actividad->update($datos);

        return response()->json([
            'success' => true,
            'actividad' => $actividad->fresh(),
        ]);
    }


    /**
     * Eliminar actividad visualmente.
     * No se borra de la base de datos.
     */
    public function eliminarActividad(
        ActividadControlOperativo $actividad
    ) {
        $actividad->activa = false;
        $actividad->save();

        return response()->json([
            'success' => true,
        ]);
    }

    public function actualizarPrioridad(Request $request, Pedido $pedido)
    {
        $request->validate([
            'prioridad' => [
                'nullable',
                'in:1,2,3,4',
            ],
        ]);

        $control = $pedido->controlOperativo;

        if (!$control) {

            $control = $pedido->controlOperativo()->create([
                'prioridad' => $request->input('prioridad'),
                'estado_operativo' => 'Por Iniciar',
            ]);
        } else {

            $control->prioridad =
                $request->input('prioridad');

            $control->save();
        }

        return response()->json([
            'success' => true,
            'prioridad' => $control->prioridad,
        ]);
    }

    public function actualizarAbastecimiento(Request $request, Pedido $pedido)
    {
        $datos = $request->validate([
            'abastecimiento' => [
                'nullable',
                'in:otra_opcion,apartado,parcialidad,entregado,faltante_mp',
            ],
            'motivo' => 'required_if:abastecimiento,otra_opcion,apartado,parcialidad,entregado,faltante_mp|nullable|string|max:200',
            'comentario' => 'nullable|string|max:1000',
            'responsable' => 'nullable|string|max:255',
            'fecha_limite' => 'nullable|date',
        ]);

        if (($datos['motivo'] ?? null) === 'Otro' && empty($datos['comentario'])) {
            return response()->json([
                'message' => 'Escribe un comentario para la opción Otro.',
                'errors' => ['comentario' => ['Este campo es obligatorio cuando eliges Otro.']],
            ], 422);
        }

        $resultado = DB::transaction(function () use ($pedido, $datos) {
            $control = $pedido->controlOperativo;
            if (!$control) {
                $control = $pedido->controlOperativo()->create([
                    'abastecimiento' => $datos['abastecimiento'] ?? null,
                    'estado_operativo' => 'Por Iniciar',
                ]);
            } else {
                $control->abastecimiento = $datos['abastecimiento'] ?? null;
                $control->save();
            }

            $actividad = null;
            if (!empty($datos['motivo'])) {
                $etiquetas = [
                    'otra_opcion' => 'Otra opción',
                    'apartado' => 'Preparado',
                    'parcialidad' => 'Parcialidad',
                    'entregado' => 'Entregado',
                    'faltante_mp' => 'Pendiente',
                ];
                $texto = 'Surtido de almacén a producción: ' . ($etiquetas[$datos['abastecimiento']] ?? 'Actualizado')
                    . ' — ' . $datos['motivo'];
                if (!empty($datos['comentario'])) {
                    $texto .= ' — ' . trim($datos['comentario']);
                }

                $actividad = $pedido->actividadesControlOperativo()->create([
                    'actividad' => $texto,
                    'responsable' => $datos['responsable'] ?? null,
                    'fecha_limite' => $datos['fecha_limite'] ?? null,
                    'completada' => ($datos['abastecimiento'] ?? null) === 'entregado',
                    'activa' => true,
                ]);
            }

            return [$control, $actividad];
        });

        [$control, $actividad] = $resultado;

        return response()->json([
            'success' => true,
            'abastecimiento' => $control->abastecimiento,
            'actividad' => $actividad,
            'url_actualizar' => $actividad
                ? route('control-operativo.actividad.actualizar', $actividad)
                : null,
            'url_eliminar' => $actividad
                ? route('control-operativo.actividad.eliminar', $actividad)
                : null,
        ]);
    }

    /**
     * Actualizar resultado de entrega registrado por Ventas.
     */
    public function actualizarResultadoEntrega(Request $request, Pedido $pedido)
    {
        $request->validate([
            'resultado_entrega' => [
                'nullable',
                'in:Pendiente,Información incompleta,Información completa',
            ],
        ]);

        $control = $pedido->controlOperativo;

        if (!$control) {
            $control = $pedido->controlOperativo()->create([
                'resultado_entrega' => $request->input('resultado_entrega'),
                'estado_operativo' => 'Por Iniciar',
            ]);
        } else {
            $control->resultado_entrega = $request->input('resultado_entrega');
            $control->save();
        }

        return response()->json([
            'success' => true,
            'resultado_entrega' => $control->resultado_entrega,
        ]);
    }

    /**
     * Actualizar Pedido Interno.
     */
    public function actualizarPedidoInterno(
        Request $request,
        Pedido $pedido
    ) {
        $request->validate([
            'pedido_interno' => [
                'required',
                'boolean',
            ],
        ]);

        $pedido->pedido_interno =
            $request->boolean('pedido_interno');

        $pedido->save();

        return response()->json([
            'success' => true,
            'pedido_interno' => $pedido->pedido_interno,
        ]);
    }

    /**
     * Actualizar Estado Operativo.
     */
    public function actualizarEstado(
        Request $request,
        Pedido $pedido
    ) {
        $request->validate([
            'estado_operativo' => [
                'required',
                'in:Por Iniciar,En proceso,Cancelado,Terminado',
            ],
        ]);

        $nuevoEstado = $request->input('estado_operativo');

        $control = DB::transaction(function () use ($pedido, $nuevoEstado) {
            $pedido = Pedido::query()->lockForUpdate()->findOrFail($pedido->id);
            $control = $pedido->controlOperativo()->updateOrCreate([], [
                'estado_operativo' => $nuevoEstado,
            ]);

            if ($nuevoEstado === 'Terminado') {
                $pedido->fecha_terminado ??= now()->toDateString();
                $pedido->save();
                $pedido->completarProcesosDespiece();
            }

            return $control;
        }, 3);

        return response()->json([
            'success' => true,
            'estado_operativo' => $control->estado_operativo,
            'proceso_menor_avance' => $nuevoEstado === 'Terminado'
                ? $this->obtenerProcesoMenorAvance($pedido->load('partidas.despieceProcesos'))
                : null,
        ]);
    }


    /**
     * Obtener el proceso con menor avance de un pedido.
     */
    private function obtenerProcesoMenorAvance(Pedido $pedido): array
    {
        $procesos = Proceso::where('activo', true)
            ->orderBy('orden')
            ->get();

        $procesosParciales = [];
        $procesosCero = [];
        $procesosAplicables = 0;
        $procesosCompletos = 0;

        foreach ($procesos as $proceso) {

            $suma = 0;
            $cantidad = 0;

            foreach ($pedido->partidas as $partida) {

                $despiece = $partida->despieceProcesos
                    ->firstWhere('proceso_id', $proceso->id);

                // Si no existe el proceso para la partida,
                // se considera 0%
                if (!$despiece) {
                    $suma += 0;
                    $cantidad++;
                    continue;
                }

                // N/A no participa
                if (!$despiece->aplica) {
                    continue;
                }

                $porcentaje = (float) $despiece->porcentaje;

                // Limitar entre 0 y 100%
                $porcentaje = max(0, min(1, $porcentaje));

                $suma += $porcentaje;
                $cantidad++;
            }

            if ($cantidad === 0) {
                continue;
            }

            $porcentajeProceso = $suma / $cantidad;

            $procesosAplicables++;

            /*
        |------------------------------------------------------------
        | 1. PROCESOS ENTRE 1% Y 99%
        |------------------------------------------------------------
        */
            if ($porcentajeProceso > 0 && $porcentajeProceso < 1) {

                $procesosParciales[] = [
                    'proceso_id' => $proceso->id,
                    'proceso' => $proceso->nombre,
                    'porcentaje' => $porcentajeProceso,
                ];

                continue;
            }

            /*
        |------------------------------------------------------------
        | 2. PROCESOS EN 0%
        |------------------------------------------------------------
        */
            if ($porcentajeProceso == 0) {

                $procesosCero[] = [
                    'proceso_id' => $proceso->id,
                    'proceso' => $proceso->nombre,
                    'porcentaje' => 0,
                ];

                continue;
            }

            /*
        |------------------------------------------------------------
        | 3. PROCESOS AL 100%
        |------------------------------------------------------------
        */
            if ($porcentajeProceso >= 1) {
                $procesosCompletos++;
            }
        }

        /*
    |------------------------------------------------------------
    | PRIORIDAD 1:
    | Existe algún proceso entre 1% y 99%.
    | Mostrar el menor.
    |------------------------------------------------------------
    */
        if (!empty($procesosParciales)) {

            usort($procesosParciales, function ($a, $b) {
                return $a['porcentaje'] <=> $b['porcentaje'];
            });

            return $procesosParciales[0];
        }

        /*
    |------------------------------------------------------------
    | PRIORIDAD 2:
    | No hay procesos parciales, pero existen procesos en 0%.
    | Mostrar 0%.
    |------------------------------------------------------------
    */
        if (!empty($procesosCero)) {
            return $procesosCero[0];
        }

        /*
    |------------------------------------------------------------
    | PRIORIDAD 3:
    | Todos los procesos aplicables están al 100%.
    |------------------------------------------------------------
    */
        if ($procesosAplicables > 0 && $procesosCompletos === $procesosAplicables) {

            return [
                'proceso_id' => null,
                'proceso' => 'COMPLETADO',
                'porcentaje' => 1,
            ];
        }

        /*
    |------------------------------------------------------------
    | Sin procesos
    |------------------------------------------------------------
    */
        return [
            'proceso_id' => null,
            'proceso' => 'SIN PROCESOS',
            'porcentaje' => null,
        ];
    }
}

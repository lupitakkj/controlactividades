<?php

namespace App\Http\Controllers;

use App\Models\Importacion;
use App\Models\Pedido;
use App\Models\Partida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportacionController extends Controller
{
    /**
     * Mostrar pantalla de importación.
     */
    public function index()
    {
        return view('importaciones.index');
    }

    /**
     * Importar uno o varios archivos Excel.
     */
    /**
     * Importar uno o varios archivos Excel.
     */
    public function importar(Request $request)
    {
        /* ============================================================
       PARTIDAS EXCLUIDAS DEL DESPIECE
    ============================================================ */

        $clavesExcluidas = [

            '510002-03',
            '510002-04',
            '510003-03',
            '510003-04',
            '510004-03',
            '510004-04',
            '510005-04',
            '510005-10',
            '510006-04',
            '510006-09',
            '510007-03',
            '510007-10',
            '510008-04',
            '510009-04',
            '510010-04',
            '510011-04',
            '510013-04',
            '510016-04',
            '510023-10',
            '520010-01',
            'SARM001',
            'SERV-EN',
            'TKCONEL',
            'TRON002',
            'TTCON0022',
            'TTOR002',
            'TTOR004',
            'TTOR005',
            'TTOR006',
            'TTOR041',
            'TTOR063',
            'TTOR065',
            'TTOR081',

        ];


        $descripcionesExcluidas = [

            'PATA ATORNILLABLE DE 18.68CM ALT FABRICADO EN PERFIL DE 1X1" C-18 CON PLACA CALIBRE 14 E INSERTO NIVELADOR 5/16" . COLOR NEGRO. (DE ACUERDO A DISEÑO)',

            'PINTURA (GRIS RIJAYA) DE 40 POSTES DE 180CM',

            'RONDANA 3/8"',

        ];


        /* ============================================================
       VALIDAR ARCHIVOS
    ============================================================ */

        $request->validate([
            'archivos' => ['required', 'array', 'min:1'],

            'archivos.*' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:20480',
            ],
        ]);


        $resultados = [];


        /* ============================================================
       RECORRER ARCHIVOS
    ============================================================ */

        foreach ($request->file('archivos') as $archivo) {

            $nombreArchivo =
                $archivo->getClientOriginalName();


            try {

                /*
            |--------------------------------------------------------------------------
            | LEER EXCEL
            |--------------------------------------------------------------------------
            */

                $spreadsheet =
                    IOFactory::load(
                        $archivo->getRealPath()
                    );


                $hoja =
                    $spreadsheet->getActiveSheet();


                $filas =
                    $hoja->toArray(
                        null,
                        true,
                        true,
                        false
                    );


                if (count($filas) < 2) {

                    throw new \Exception(
                        'El archivo no contiene filas de datos.'
                    );
                }


                /*
            |--------------------------------------------------------------------------
            | ENCABEZADOS
            |--------------------------------------------------------------------------
            */

                $encabezados =
                    array_map(
                        function ($valor) {

                            return trim(
                                (string) $valor
                            );
                        },
                        $filas[0]
                    );


                $columnasEsperadas = [

                    'PEDIDO No. :',
                    'Cliente:',
                    'Su Pedido',
                    'Enviar a:',
                    'Clave',
                    'Linea',
                    'Descripción',
                    'Cantidad',
                    'P/U',
                    'Importe Total',
                    'Fecha solicutud:',
                    'Fecha de Entrega',

                ];


                /*
            |--------------------------------------------------------------------------
            | VALIDAR ESTRUCTURA
            |--------------------------------------------------------------------------
            */

                foreach ($columnasEsperadas as $columna) {

                    if (
                        !in_array(
                            $columna,
                            $encabezados,
                            true
                        )
                    ) {

                        throw new \Exception(
                            "No se encontró la columna: {$columna}"
                        );
                    }
                }


                /*
            |--------------------------------------------------------------------------
            | UBICACIÓN DE COLUMNAS
            |--------------------------------------------------------------------------
            */

                $indice = [];


                foreach ($columnasEsperadas as $columna) {

                    $indice[$columna] =
                        array_search(
                            $columna,
                            $encabezados,
                            true
                        );
                }


                /*
            |--------------------------------------------------------------------------
            | OBTENER PEDIDO
            |--------------------------------------------------------------------------
            */

                $pedidoNo =
                    trim(
                        (string)
                        $filas[1][$indice['PEDIDO No. :']]
                    );


                if ($pedidoNo === '') {

                    throw new \Exception(
                        'No se encontró el número de pedido.'
                    );
                }


                /*
            |--------------------------------------------------------------------------
            | CREAR / ACTUALIZAR PEDIDO
            |--------------------------------------------------------------------------
            */

                DB::transaction(function () use (
                    $filas,
                    $indice,
                    $pedidoNo,
                    $nombreArchivo,
                    $clavesExcluidas,
                    $descripcionesExcluidas,
                    &$resultados
                ) {


                    /*
                |--------------------------------------------------------------------------
                | REGISTRAR IMPORTACIÓN
                |--------------------------------------------------------------------------
                */

                    $importacion =
                        Importacion::create([

                            'nombre_archivo' =>
                            $nombreArchivo,

                            'fecha_importacion' =>
                            now(),

                            'usuario_id' =>
                            auth()->id(),

                            'cantidad_pedidos' =>
                            1,

                            'cantidad_partidas' =>
                            0,

                            'resultado' =>
                            'correcto',

                        ]);


                    /*
                |--------------------------------------------------------------------------
                | DATOS GENERALES DEL PEDIDO
                |--------------------------------------------------------------------------
                */

                    $cliente =
                        trim(
                            (string)
                            $filas[1][$indice['Cliente:']]
                        );


                    $suPedido =
                        trim(
                            (string)
                            $filas[1][$indice['Su Pedido']]
                        );


                    $enviarA =
                        $this->limpiarEnviarA(
                            $filas[1][$indice['Enviar a:']]
                        );


                    $fechaSolicitud =
                        $this->convertirFecha(
                            $filas[1][$indice['Fecha solicutud:']]
                        );


                    $fechaEntrega =
                        $this->convertirFecha(
                            $filas[1][$indice['Fecha de Entrega']]
                        );


                    /*
                |--------------------------------------------------------------------------
                | BUSCAR SI EL PEDIDO YA EXISTE
                |--------------------------------------------------------------------------
                */

                    $pedido =
                        Pedido::where(
                            'pedido_no',
                            $pedidoNo
                        )->first();


                    $esActualizacion =
                        $pedido !== null;


                    /*
                |--------------------------------------------------------------------------
                | ACTUALIZAR PEDIDO EXISTENTE
                |--------------------------------------------------------------------------
                */

                    if ($esActualizacion) {

                        $pedido->update([

                            'cliente' =>
                            $cliente ?: null,

                            'su_pedido' =>
                            $suPedido ?: null,

                            'enviar_a' =>
                            $enviarA ?: null,

                            'importe_total' =>
                            0,

                            'fecha_solicitud' =>
                            $fechaSolicitud,

                            'fecha_entrega' =>
                            $fechaEntrega,

                            'nombre_archivo' =>
                            $nombreArchivo,

                            'importacion_id' =>
                            $importacion->id,

                        ]);


                        /*
                    |--------------------------------------------------------------------------
                    | ELIMINAR PARTIDAS ANTERIORES
                    |--------------------------------------------------------------------------
                    */

                        Partida::where(
                            'pedido_id',
                            $pedido->id
                        )->delete();
                    }


                    /*
                |--------------------------------------------------------------------------
                | CREAR PEDIDO NUEVO
                |--------------------------------------------------------------------------
                */ else {

                        $pedido =
                            Pedido::create([

                                'pedido_no' =>
                                $pedidoNo,

                                'cliente' =>
                                $cliente ?: null,

                                'su_pedido' =>
                                $suPedido ?: null,

                                'enviar_a' =>
                                $enviarA ?: null,

                                'importe_total' =>
                                0,

                                'fecha_solicitud' =>
                                $fechaSolicitud,

                                'fecha_entrega' =>
                                $fechaEntrega,

                                'nombre_archivo' =>
                                $nombreArchivo,

                                'importacion_id' =>
                                $importacion->id,

                            ]);
                    }


                    /*
                |--------------------------------------------------------------------------
                | PARTIDAS
                |--------------------------------------------------------------------------
                */

                    $cantidadPartidas = 0;


                    /*
                |--------------------------------------------------------------------------
                | ACUMULADOR DEL IMPORTE TOTAL
                |--------------------------------------------------------------------------
                */

                    $importePedido = 0;


                    /*
                |--------------------------------------------------------------------------
                | RECORRER FILAS DEL EXCEL
                |--------------------------------------------------------------------------
                */

                    foreach (
                        array_slice($filas, 1)
                        as $fila
                    ) {


                        /*
                    |--------------------------------------------------------------------------
                    | CLAVE
                    |--------------------------------------------------------------------------
                    */

                        $clave =
                            trim(
                                (string)
                                $fila[$indice['Clave']]
                            );


                        /*
                    |--------------------------------------------------------------------------
                    | DESCRIPCIÓN
                    |--------------------------------------------------------------------------
                    */

                        $descripcion =
                            trim(
                                (string)
                                $fila[$indice['Descripción']]
                            );


                        /*
                    |--------------------------------------------------------------------------
                    | IGNORAR FILAS COMPLETAMENTE VACÍAS
                    |--------------------------------------------------------------------------
                    */

                        if (
                            $clave === '' &&
                            $descripcion === ''
                        ) {

                            continue;
                        }


                        /*
                    |--------------------------------------------------------------------------
                    | EXCLUIR POR CLAVE O DESCRIPCIÓN
                    |--------------------------------------------------------------------------
                    */

                        if (
                            in_array(
                                $clave,
                                $clavesExcluidas,
                                true
                            )
                            ||
                            in_array(
                                $descripcion,
                                $descripcionesExcluidas,
                                true
                            )
                        ) {

                            continue;
                        }


                        /*
                    |--------------------------------------------------------------------------
                    | LÍNEA
                    |--------------------------------------------------------------------------
                    */

                        $linea =
                            trim(
                                (string)
                                $fila[$indice['Linea']]
                            );


                        /*
                    |--------------------------------------------------------------------------
                    | CANTIDAD
                    |--------------------------------------------------------------------------
                    */

                        $cantidad =
                            $this->convertirNumero(
                                $fila[$indice['Cantidad']]
                            );


                        /*
                    |--------------------------------------------------------------------------
                    | PRECIO UNITARIO
                    |--------------------------------------------------------------------------
                    */

                        $precioUnitario =
                            $this->convertirNumero(
                                $fila[$indice['P/U']]
                            );


                        /*
                    |--------------------------------------------------------------------------
                    | IMPORTE TOTAL
                    |--------------------------------------------------------------------------
                    */

                        $importeTotal =
                            $this->convertirNumero(
                                $fila[$indice['Importe Total']]
                            );


                        /*
                    |--------------------------------------------------------------------------
                    | CREAR PARTIDA
                    |--------------------------------------------------------------------------
                    */

                        Partida::create([

                            'pedido_id' =>
                            $pedido->id,

                            'clave' =>
                            $clave ?: null,

                            'linea' =>
                            $linea ?: null,

                            'descripcion' =>
                            $descripcion ?: null,

                            'cantidad' =>
                            $cantidad,

                            'precio_unitario' =>
                            $precioUnitario,

                            'importe_total' =>
                            $importeTotal,

                        ]);


                        /*
                    |--------------------------------------------------------------------------
                    | ACUMULAR IMPORTE
                    |--------------------------------------------------------------------------
                    */

                        $importePedido +=
                            $importeTotal;


                        $cantidadPartidas++;
                    }


                    /*
                |--------------------------------------------------------------------------
                | ACTUALIZAR IMPORTE TOTAL DEL PEDIDO
                |--------------------------------------------------------------------------
                */

                    $pedido->update([

                        'importe_total' =>
                        $importePedido,

                    ]);


                    /*
                |--------------------------------------------------------------------------
                | ACTUALIZAR HISTORIAL
                |--------------------------------------------------------------------------
                */

                    $importacion->update([

                        'cantidad_partidas' =>
                        $cantidadPartidas,

                    ]);


                    /*
                |--------------------------------------------------------------------------
                | RESULTADO
                |--------------------------------------------------------------------------
                */

                    $resultados[] = [

                        'archivo' =>
                        $nombreArchivo,

                        'estado' =>
                        $esActualizacion
                            ? 'actualizado'
                            : 'importado',

                        'mensaje' =>
                        $esActualizacion
                            ? "Pedido {$pedidoNo} actualizado correctamente. "
                            : "Pedido {$pedidoNo} importado correctamente. "

                            . "{$cantidadPartidas} partidas. "

                            . "Importe total: $"

                            . number_format(
                                $importePedido,
                                2
                            ),

                    ];
                });
            } catch (\Throwable $e) {

                $resultados[] = [

                    'archivo' =>
                    $nombreArchivo,

                    'estado' =>
                    'error',

                    'mensaje' =>
                    $e->getMessage(),

                ];
            }
        }


        /*
    |--------------------------------------------------------------------------
    | REGRESAR A LA PANTALLA DE IMPORTACIÓN
    |--------------------------------------------------------------------------
    */

        return back()->with(
            'resultados_importacion',
            $resultados
        );
    }

    /**
     * Convertir fechas provenientes de Excel.
     */
    private function convertirFecha($valor)
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | FECHA SERIAL DE EXCEL
            |--------------------------------------------------------------------------
            */

            if (is_numeric($valor)) {

                return \PhpOffice\PhpSpreadsheet\Shared\Date
                    ::excelToDateTimeObject($valor)
                    ->format('Y-m-d');
            }

            /*
            |--------------------------------------------------------------------------
            | FECHA EN FORMATO DD/MM/YYYY
            |--------------------------------------------------------------------------
            */

            return \Carbon\Carbon::createFromFormat(
                'd/m/Y',
                trim((string) $valor)
            )->format('Y-m-d');
        } catch (\Throwable $e) {

            return null;
        }
    }

    /**
     * Limpiar campo Enviar a.
     *
     * Ejemplos:
     * Calle: RUTA LUIS
     *     -> RUTA LUIS
     *
     * Calle: RUTA LUIS, CP: 72150
     *     -> RUTA LUIS
     *
     * , CP: 72150
     *     -> NULL
     */
    private function limpiarEnviarA($valor)
    {
        if (
            $valor === null ||
            trim((string) $valor) === ''
        ) {
            return null;
        }

        $valor = trim((string) $valor);

        /*
        |--------------------------------------------------------------------------
        | QUITAR "Calle:"
        |--------------------------------------------------------------------------
        */

        $valor = preg_replace(
            '/^\s*Calle\s*:\s*/i',
            '',
            $valor
        );

        /*
        |--------------------------------------------------------------------------
        | QUITAR "CP:"
        |--------------------------------------------------------------------------
        */

        $valor = preg_replace(
            '/\s*,?\s*CP\s*:\s*\d+\s*$/i',
            '',
            $valor
        );

        /*
        |--------------------------------------------------------------------------
        | LIMPIAR COMAS Y ESPACIOS SOBRANTES
        |--------------------------------------------------------------------------
        */

        $valor = trim(
            $valor,
            " \t\n\r\0\x0B,"
        );

        return $valor !== ''
            ? $valor
            : null;
    }

    /**
     * Convertir cantidades y precios.
     */
    private function convertirNumero($valor)
    {
        if ($valor === null || $valor === '') {
            return 0;
        }

        $valor = str_replace(
            [',', '$', ' '],
            '',
            (string) $valor
        );

        return (float) $valor;
    }
}

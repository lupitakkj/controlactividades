<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReporteEntradasController extends Controller
{
    private function autorizar(): void
    {
        abort_unless(auth()->user()->hasAnyRole(['Administrador', 'Compras']), 403);
    }


    private function consultaRecepciones(array $filtros = [])
    {
        $query = DB::table('compras_recepciones as r')
            ->join('compras_ordenes as o', 'o.id', '=', 'r.orden_id')
            ->leftJoin('proveedores as p', 'p.id', '=', 'o.proveedor_id')
            ->leftJoin('almacenes as a', 'a.id', '=', 'r.almacen_id')
            ->leftJoin('users as u', 'u.id', '=', 'r.recibido_por')
            ->select([
                'r.id as recepcion_id',
                'r.fecha_recepcion',
                'r.almacen_id',
                'r.observaciones as observaciones_recepcion',
                'r.recibido_por',
                'r.evidencia_nombre_original',
                'o.id as orden_id',
                'o.folio as orden_folio',
                'o.proveedor_id',
                'p.razon_social as proveedor_nombre',
                'p.rfc as proveedor_rfc',
                'a.nombre as almacen_nombre',
                'a.clave as almacen_clave',
                'u.name as usuario_nombre',
            ])
            ->selectSub(function ($sub) {
                $sub->from('compras_recepciones_detalle as rd')
                    ->whereColumn('rd.recepcion_id', 'r.id')
                    ->selectRaw('COUNT(*)');
            }, 'cantidad_lineas')
            ->selectSub(function ($sub) {
                $sub->from('compras_recepciones_detalle as rd')
                    ->whereColumn('rd.recepcion_id', 'r.id')
                    ->selectRaw('COALESCE(SUM(rd.cantidad_recibida), 0)');
            }, 'cantidad_recibida_total');

        if (!empty($filtros['fecha_inicio'])) {
            $query->whereDate('r.fecha_recepcion', '>=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $query->whereDate('r.fecha_recepcion', '<=', $filtros['fecha_fin']);
        }
        if (!empty($filtros['almacen_id'])) {
            $query->where('r.almacen_id', $filtros['almacen_id']);
        }
        if (!empty($filtros['proveedor_id'])) {
            $query->where('o.proveedor_id', $filtros['proveedor_id']);
        }

        return $query->orderByDesc('r.fecha_recepcion')->orderByDesc('r.id');
    }

    public function index(Request $request)
    {
        $this->autorizar();

        $filtros = $request->validate([
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'almacen_id' => ['nullable', 'integer', 'exists:almacenes,id'],
            'proveedor_id' => ['nullable', 'integer', 'exists:proveedores,id'],
        ]);

        $almacenes = DB::table('almacenes')
            ->orderBy('clave')
            ->get(['id', 'clave', 'nombre']);

        $proveedores = DB::table('proveedores')
            ->orderBy('razon_social')
            ->get(['id', 'razon_social']);

        $recepciones = $this->consultaRecepciones($filtros)
            ->paginate(15)
            ->withQueryString();

        return view('compras.reportes.entradas', compact(
            'almacenes',
            'proveedores',
            'recepciones',
            'filtros'
        ));
    }

    public function exportar(Request $request)
    {
        $this->autorizar();

        $filtros = $request->validate([
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'almacen_id' => ['nullable', 'integer', 'exists:almacenes,id'],
            'proveedor_id' => ['nullable', 'integer', 'exists:proveedores,id'],
        ]);

        $plantilla = storage_path('app/plantillas/ENTRADAS.xlsx');
        abort_unless(is_file($plantilla), 500, 'No se encontró la plantilla storage/app/plantillas/ENTRADAS.xlsx.');

        $recepcionesQuery = DB::table('compras_recepciones as r')
            ->join('compras_ordenes as o', 'o.id', '=', 'r.orden_id')
            ->leftJoin('proveedores as p', 'p.id', '=', 'o.proveedor_id')
            ->leftJoin('almacenes as a', 'a.id', '=', 'r.almacen_id')
            ->leftJoin('users as u', 'u.id', '=', 'r.recibido_por')
            ->select([
                'r.id as recepcion_id',
                'r.fecha_recepcion',
                'r.almacen_id',
                'r.observaciones as observaciones_recepcion',
                'r.recibido_por',
                'r.evidencia_nombre_original',
                'o.id as orden_id',
                'o.folio as orden_folio',
                'o.moneda',
                'p.razon_social as proveedor_nombre',
                'p.rfc as proveedor_rfc',
                'a.nombre as almacen_nombre',
                'a.clave as almacen_clave',
                'u.name as usuario_nombre',
            ]);

        if (!empty($filtros['fecha_inicio'])) {
            $recepcionesQuery->whereDate('r.fecha_recepcion', '>=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $recepcionesQuery->whereDate('r.fecha_recepcion', '<=', $filtros['fecha_fin']);
        }
        if (!empty($filtros['almacen_id'])) {
            $recepcionesQuery->where('r.almacen_id', $filtros['almacen_id']);
        }
        if (!empty($filtros['proveedor_id'])) {
            $recepcionesQuery->where('o.proveedor_id', $filtros['proveedor_id']);
        }

        $recepciones = $recepcionesQuery->orderBy('r.fecha_recepcion')->orderBy('r.id')->get();
        $ids = $recepciones->pluck('recepcion_id')->all();

        $detalles = collect();
        if ($ids) {
            $detalles = DB::table('compras_recepciones_detalle as rd')
                ->join('compras_ordenes_detalle as od', 'od.id', '=', 'rd.orden_detalle_id')
                ->whereIn('rd.recepcion_id', $ids)
                ->orderBy('od.renglon')
                ->select([
                    'rd.recepcion_id',
                    'rd.cantidad_recibida',
                    'rd.observaciones as observaciones_detalle',
                    'od.clave_material',
                    'od.descripcion',
                    'od.unidad',
                    'od.precio_unitario',
                    'od.importe',
                ])->get()->groupBy('recepcion_id');
        }

        $spreadsheet = IOFactory::load($plantilla);
        $general = $spreadsheet->getSheetByName('Registro_Entradas');
        $productos = $spreadsheet->getSheetByName('Detalle_Productos');

        abort_unless($general && $productos, 500, 'La plantilla debe contener Registro_Entradas y Detalle_Productos.');

        // Quitar la referencia SAE del reporte sin removeColumn(), porque la plantilla
        // contiene fórmulas que pueden fallar al desplazar físicamente columnas.
        // Se recorren los encabezados B:P hacia A:O y se conserva el diseño de la hoja.
        for ($col = 1; $col <= 15; $col++) {
            $origen = Coordinate::stringFromColumnIndex($col + 1);
            $destino = Coordinate::stringFromColumnIndex($col);
            $general->setCellValue($destino . '4', $general->getCell($origen . '4')->getValue());
            $general->duplicateStyle($general->getStyle($origen . '4'), $destino . '4');
            $anchoOrigen = $general->getColumnDimension($origen)->getWidth();
            $general->getColumnDimension($destino)->setWidth($anchoOrigen);
        }
        $general->setCellValue('P4', null);

        // Limpiar únicamente valores de ejemplo; se conserva el diseño y los encabezados.
        foreach ([$general, $productos] as $sheet) {
            $lastColumn = Coordinate::columnIndexFromString($sheet->getHighestColumn());
            for ($row = 5; $row <= $sheet->getHighestRow(); $row++) {
                for ($col = 1; $col <= $lastColumn; $col++) {
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($col) . $row, null);
                }
            }
        }

        $filaGeneral = 5;
        $filaDetalle = 5;

        foreach ($recepciones as $recepcion) {
            $lineas = $detalles->get($recepcion->recepcion_id, collect());
            $subtotalRecibido = 0.0;

            foreach ($lineas as $linea) {
                $cantidad = (float) $linea->cantidad_recibida;
                $precio = $linea->precio_unitario !== null ? (float) $linea->precio_unitario : null;
                $importe = $precio === null ? null : round($cantidad * $precio, 2);
                if ($importe !== null) $subtotalRecibido += $importe;

                $productos->setCellValue("A{$filaDetalle}", null); // Folio SAE no almacenado: vacío.
                $productos->setCellValueExplicit("B{$filaDetalle}", (string) ($linea->clave_material ?? ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $productos->setCellValue("C{$filaDetalle}", $linea->descripcion ?? '');
                $productos->setCellValue("D{$filaDetalle}", $linea->unidad ?? '');
                $productos->setCellValue("E{$filaDetalle}", $cantidad);
                $productos->setCellValue("F{$filaDetalle}", $precio);
                $productos->setCellValue("G{$filaDetalle}", $importe);
                $productos->setCellValue("H{$filaDetalle}", $recepcion->fecha_recepcion ? date('Y-m-d', strtotime($recepcion->fecha_recepcion)) : null);
                $productos->setCellValue("I{$filaDetalle}", $linea->observaciones_detalle ?? $recepcion->observaciones_recepcion ?? '');
                $filaDetalle++;
            }

            // Columnas de Registro_Entradas después de eliminar Referencia/folio en SAE:
            // A Fecha, B Almacén, C Proveedor, D RFC, E Orden, F Documento recibido,
            // G Folio documento, H Recibido por, I Concepto, J Subtotal,
            // K IVA, L Otros impuestos, M Total, N Condición de pago, O Observaciones.
            $general->setCellValue("A{$filaGeneral}", $recepcion->fecha_recepcion ? date('Y-m-d', strtotime($recepcion->fecha_recepcion)) : null);
            $general->setCellValue("B{$filaGeneral}", $recepcion->almacen_clave ?? '');
            $general->setCellValue("C{$filaGeneral}", $recepcion->proveedor_nombre ?? '');
            $general->setCellValue("D{$filaGeneral}", $recepcion->proveedor_rfc ?? '');
            $general->setCellValueExplicit("E{$filaGeneral}", (string) ($recepcion->orden_folio ?? ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $general->setCellValue("F{$filaGeneral}", $recepcion->evidencia_nombre_original ?? '');
            $general->setCellValue("G{$filaGeneral}", null);
            $general->setCellValue("H{$filaGeneral}", $recepcion->usuario_nombre ?? '');
            $general->setCellValue("I{$filaGeneral}", $lineas->pluck('descripcion')->filter()->unique()->implode(', '));
            $general->setCellValue("J{$filaGeneral}", $lineas->isEmpty() ? null : round($subtotalRecibido, 2));
            $general->setCellValue("K{$filaGeneral}", null);
            $general->setCellValue("L{$filaGeneral}", null);
            $general->setCellValue("M{$filaGeneral}", null);
            $general->setCellValue("N{$filaGeneral}", null);
            $general->setCellValue("O{$filaGeneral}", $recepcion->observaciones_recepcion ?? '');
            $filaGeneral++;
        }

        // Eliminar fórmulas de ejemplo que quedaron en filas sin datos.
        for ($r = $filaGeneral; $r <= $general->getHighestRow(); $r++) {
            foreach (range('J', 'M') as $col) $general->setCellValue($col . $r, null);
        }
        for ($r = $filaDetalle; $r <= $productos->getHighestRow(); $r++) {
            $productos->setCellValue("G{$r}", null);
        }

        $nombre = 'ENTRADAS_' . now()->format('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(true);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $nombre, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}

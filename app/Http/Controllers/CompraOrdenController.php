<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\CompraOrden;
use App\Models\CompraOrdenDetalle;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Smalot\PdfParser\Parser;

class CompraOrdenController extends Controller
{
    private function autorizar(): void
    {
        abort_unless(auth()->user()->hasAnyRole(['Administrador', 'Compras', 'Almacén']), 403);
    }

    public function index(Request $request)
    {
        $this->autorizar();
        $buscar = trim((string) $request->query('buscar', ''));
        $ordenes = CompraOrden::with('proveedor')
            ->withSum('detalles as renglones', 'cantidad_solicitada')
            ->when($buscar !== '', fn($q) => $q->where(function ($s) use ($buscar) {
                $s->where('folio', 'like', "%{$buscar}%")
                    ->orWhereHas('proveedor', fn($p) => $p->where('razon_social', 'like', "%{$buscar}%"));
            }))
            ->orderByDesc('fecha_orden')->orderByDesc('id')->paginate(15)->withQueryString();
        return view('compras.ordenes.index', compact('ordenes', 'buscar'));
    }

    public function create()
    {
        $this->autorizar();
        $proveedores = Proveedor::where('activo', true)->orderBy('razon_social')->get();
        return view('compras.ordenes.create', compact('proveedores'));
    }


    /**
     * Lee un PDF de orden de compra y devuelve los datos para revisar en el formulario.
     * No guarda nada en la base de datos; el usuario confirma y guarda después.
     */
    public function leerPdf(Request $request)
    {
        $this->autorizar();

        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:10240',
        ]);

        if (!class_exists(Parser::class)) {
            return response()->json([
                'message' => 'No está instalada la librería para leer PDF en el servidor. Debe desplegarse smalot/pdfparser y sus dependencias.',
            ], 422);
        }

        try {
            $documento = (new Parser())->parseFile($request->file('pdf')->getRealPath());
            $texto = str_replace(["\r\n", "\r"], "\n", $documento->getText());
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'No fue posible leer el PDF. Verifica que no esté dañado y que contenga texto seleccionable.',
            ], 422);
        }

        $normalizado = preg_replace('/[ \t]+/u', ' ', $texto);
        $folio = null;
        if (preg_match('/ORDEN\s*No\.?\s*:?\s*([0-9]{4,})/iu', $normalizado, $m)) {
            $folio = trim($m[1]);
        }

        $fecha = null;
        if (
            preg_match('/Fe\s*cha\s*:\s*([0-3]?\d[\/.-][01]?\d[\/.-]\d{4})/iu', $normalizado, $m)
            || preg_match('/Fecha\s*:\s*([0-3]?\d[\/.-][01]?\d[\/.-]\d{4})/iu', $normalizado, $m)
        ) {
            $partes = preg_split('/[\/.-]/', $m[1]);
            if (count($partes) === 3) {
                $fecha = sprintf('%04d-%02d-%02d', (int)$partes[2], (int)$partes[1], (int)$partes[0]);
            }
        }

        $rfc = null;
        if (preg_match_all('/RFC\s*:\s*([A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3})/iu', $normalizado, $coincidencias) && !empty($coincidencias[1])) {
            // En este formato puede aparecer primero el RFC del comprador y después el del proveedor.
            $rfc = strtoupper(trim(end($coincidencias[1])));
        }

        $proveedorNombre = null;
        if ($rfc && preg_match('/([A-ZÁÉÍÓÚÑ][A-ZÁÉÍÓÚÑ .,&-]{4,})\s*\(\s*' . preg_quote($rfc, '/') . '\s*\)/iu', $normalizado, $m)) {
            $proveedorNombre = trim($m[1]);
        }
        if (!$proveedorNombre && preg_match('/RIJAYA\s+COMERCIAL\s+(.+?)\s+Fe\s*cha\s*:/isu', $normalizado, $m)) {
            $proveedorNombre = trim(preg_replace('/\s*\([^)]*\)\s*$/u', '', preg_replace('/\s+/', ' ', $m[1])));
        }

        $proveedor = null;
        if ($rfc) {
            $proveedor = Proveedor::whereRaw('UPPER(rfc) = ?', [$rfc])->first();
        }
        if (!$proveedor && $proveedorNombre) {
            $proveedor = Proveedor::whereRaw('UPPER(razon_social) = ?', [mb_strtoupper($proveedorNombre)])->first();
        }

        $detalles = [];

        // Normalizar las líneas conservando la separación entre renglón principal y observaciones.
        $lineas = array_values(array_map(
            static fn($linea) => trim(preg_replace('/\s+/u', ' ', $linea)),
            explode("\n", $texto)
        ));

        // Los PDF de Rijaya incluyen importes con miles (69,714.45) y precios de hasta 6 decimales.
        $importeRegex = '(?:\d{1,3}(?:,\d{3})+|\d+)\.\d{2}';

        $patronFila = '/^\s*'
            . '(\d+(?:\.\d+)?)\s*'
            . '([A-Z][A-Z0-9._-]{3,})\s+'
            . '(.+?)\s+'
            . '(\d+\.\d{2})\s+'
            . '(\d+\.\d{3,8})\s+'
            . '(' . $importeRegex . ')\s*$/iu';


        $esFilaProducto = static function (string $linea) use ($patronFila): bool {
            return preg_match($patronFila, trim($linea)) === 1;
        };

        for ($i = 0; $i < count($lineas); $i++) {
            if (!preg_match($patronFila, $lineas[$i], $m)) {
                continue;
            }

            $observacionesPartida = [];
            for ($j = $i + 1; $j < count($lineas); $j++) {
                $siguiente = trim($lineas[$j]);
                if ($siguiente === '') {
                    continue;
                }
                if ($esFilaProducto($siguiente)) {
                    break;
                }
                if (preg_match('/^(Subtotal|Descuento|Desc\. Fin\.|I\.?E\.?P\.?S\.?|I\.?V\.?A\.?|Total|Solicita|Elabor[oó]|Autoriza)\b/iu', $siguiente)) {
                    break;
                }
                if (preg_match('/^(Cantidad|Producto|Descripci[oó]n|%?\s*Desc\.?|Costo unitario|Importe)$/iu', $siguiente)) {
                    continue;
                }
                $observacionesPartida[] = $siguiente;
            }

            $detalles[] = [
                'clave_material' => trim($m[2]),
                'descripcion' => mb_substr(trim($m[3]), 0, 500),
                'unidad' => null,
                'cantidad_solicitada' => (float) $m[1],
                'precio_unitario' => (float) $m[5],
                'observaciones' => mb_substr(implode(' ', $observacionesPartida), 0, 1000),
            ];
        }

        // Respaldo para casos donde Smalot elimina los saltos de línea entre columnas.
        if (!$detalles) {
            $textoPlano = trim(preg_replace('/\s+/u', ' ', $texto));
            $patronPlano = '/(\d+(?:\.\d+)?)\s*'
                . '([A-Z][A-Z0-9._-]{3,})\s+'
                . '(.+?)\s+'
                . '(\d+\.\d{2})\s+'
                . '(\d+\.\d{3,8})\s+'
                . '(' . $importeRegex . ')(?=\s+\d+(?:\.\d+)?\s+[A-Z][A-Z0-9._-]{3,}\s+|\s+Subtotal\b)/iu';

            if (preg_match_all($patronPlano, $textoPlano, $coincidencias, PREG_SET_ORDER)) {
                foreach ($coincidencias as $m) {
                    $detalles[] = [
                        'clave_material' => trim($m[2]),
                        'descripcion' => mb_substr(trim($m[3]), 0, 500),
                        'unidad' => null,
                        'cantidad_solicitada' => (float) $m[1],
                        'precio_unitario' => (float) $m[5],
                        'observaciones' => '',
                    ];
                }
            }
        }

        $subtotal = null;
        $iva = null;
        $total = null;

        // Calcular subtotal de partidas detectadas como respaldo.
        if ($detalles) {
            $subtotal = round(array_sum(array_map(
                static fn($d) => (float) $d['cantidad_solicitada'] * (float) $d['precio_unitario'],
                $detalles
            )), 2);
        }

        // Normalizar espacios porque Smalot puede separar etiquetas y cifras en líneas distintas.
        $textoImportes = preg_replace('/\s+/u', ' ', str_replace(["\r\n", "\r"], "\n", $texto));
        $numeroImporte = '(?:\d{1,3}(?:,\d{3})+|\d+)\.\d{2}';

        $limpiarImporte = static function (string $valor): float {
            return (float) str_replace([',', '$', ' '], '', trim($valor));
        };

        // Priorizar el subtotal impreso en el documento, no solo la multiplicación de cantidades y precios.
        if (preg_match('/\bSubtotal\b\s*:?\s*\$?\s*(' . $numeroImporte . ')/iu', $textoImportes, $m)) {
            $subtotalImpreso = $limpiarImporte($m[1]);
            $subtotal = $subtotalImpreso;
        }

        // Leer IVA y total por etiqueta. Se admiten puntos entre las letras y espacios del extractor.
        if (preg_match('/\bI\s*\.?\s*V\s*\.?\s*A\s*\.?\s*:?\s*\$?\s*(' . $numeroImporte . ')/iu', $textoImportes, $m)) {
            $iva = $limpiarImporte($m[1]);
        }

        if (preg_match('/\bTotal\b\s*:?\s*\$?\s*(' . $numeroImporte . ')/iu', $textoImportes, $m)) {
            $total = $limpiarImporte($m[1]);
        }

        // Respaldo si el extractor separó la etiqueta IVA de su importe de una manera no reconocida:
        // si subtotal y total impresos existen, la diferencia permite recuperar el IVA real.
        if ($iva === null && $subtotal !== null && $total !== null && $total >= $subtotal) {
            $iva = round($total - $subtotal, 2);
        }


        // Respaldo para PDF que imprime las etiquetas y los importes en bloques separados.
        // Si no se detectó el total por etiqueta, recupera los últimos importes del documento.
        if ($iva === null || $total === null) {
            preg_match_all(
                '/(?<![\d.])(?:\d{1,3}(?:,\d{3})+|\d+)\.\d{2}(?!\d)/',
                $textoImportes,
                $montosEncontrados
            );

            $montos = array_map($limpiarImporte, $montosEncontrados[0] ?? []);

            if (count($montos) >= 2) {
                $totalDetectado = $montos[count($montos) - 1];
                $ivaDetectado = $montos[count($montos) - 2];

                if ($total === null && $subtotal !== null && $totalDetectado >= $subtotal) {
                    $total = $totalDetectado;
                }

                if (
                    $iva === null &&
                    $total !== null &&
                    $subtotal !== null &&
                    abs(($subtotal + $ivaDetectado) - $total) <= 0.02
                ) {
                    $iva = $ivaDetectado;
                }
            }
        }


        if (!$folio && !$detalles) {
            return response()->json([
                'message' => 'El PDF se pudo abrir, pero no reconocí su estructura como una orden de compra compatible. Puedes capturar los datos manualmente.',
            ], 422);
        }

        return response()->json([
            'folio' => $folio,
            'fecha_orden' => $fecha,
            'moneda' => 'MXN',
            'proveedor_nombre' => $proveedorNombre,
            'proveedor_rfc' => $rfc,
            'proveedor_id' => $proveedor?->id,
            'proveedor_encontrado' => (bool)$proveedor,
            'detalles' => $detalles,
            'subtotal_pdf' => $subtotal,
            'iva' => $iva,
            'total_pdf' => $total,
            'advertencias' => array_values(array_filter([
                !$folio ? 'No se detectó el folio; revísalo manualmente.' : null,
                !$fecha ? 'No se detectó la fecha; revísala manualmente.' : null,
                !$proveedor ? 'No se encontró un proveedor coincidente por RFC/nombre. Selecciónalo manualmente o regístralo en Proveedores.' : null,
                !$detalles ? 'No se detectaron renglones de producto; captúralos manualmente.' : null,
            ])),
        ]);
    }

    /**
     * Registra un proveedor desde el formulario de orden de compra.
     * Si el RFC ya existe, devuelve el proveedor existente para evitar duplicados.
     */
    public function guardarProveedorDesdeOrden(Request $request)
    {
        $this->autorizar();

        $data = $request->validate([
            'razon_social' => ['required', 'string', 'max:180'],
            'rfc' => ['nullable', 'string', 'max:13', 'regex:/^[A-Za-z&Ññ]{3,4}[0-9]{6}[A-Za-z0-9]{3}$/'],
            'clave' => ['nullable', 'string', 'max:40', 'unique:proveedores,clave'],
            'contacto' => ['nullable', 'string', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:40'],
            'correo' => ['nullable', 'email', 'max:180'],
            'direccion' => ['nullable', 'string', 'max:5000'],
            'observaciones' => ['nullable', 'string', 'max:5000'],
        ]);

        $data['razon_social'] = trim($data['razon_social']);
        $data['rfc'] = isset($data['rfc']) && $data['rfc'] !== ''
            ? strtoupper(trim($data['rfc']))
            : null;

        // Validar duplicados por RFC y después por razón social normalizada.
        $existente = null;

        if (!empty($data['rfc'])) {
            $existente = Proveedor::whereRaw('UPPER(TRIM(rfc)) = ?', [$data['rfc']])->first();
        }

        if (!$existente) {
            $nombreNormalizado = mb_strtoupper(preg_replace('/\\s+/u', ' ', trim($data['razon_social'])));
            $existente = Proveedor::whereRaw(
                'UPPER(TRIM(razon_social)) = ?',
                [$nombreNormalizado]
            )->first();
        }

        if ($existente) {
            if (!$existente->activo) {
                return response()->json([
                    'message' => 'Ya existe un proveedor con esos datos, pero está inactivo. Actívalo desde el catálogo antes de usarlo.',
                    'proveedor' => [
                        'id' => $existente->id,
                        'razon_social' => $existente->razon_social,
                        'rfc' => $existente->rfc,
                    ],
                    'existente' => true,
                    'inactivo' => true,
                ], 422);
            }

            return response()->json([
                'message' => 'Ya existe un proveedor con ese RFC o razón social. Se seleccionó el registro existente para evitar duplicados.',
                'proveedor' => [
                    'id' => $existente->id,
                    'razon_social' => $existente->razon_social,
                    'rfc' => $existente->rfc,
                ],
                'existente' => true,
            ]);
        }

        $data['activo'] = true;
        $data['creado_por'] = auth()->id();
        $data['actualizado_por'] = auth()->id();

        $proveedor = Proveedor::create($data);

        return response()->json([
            'message' => 'Proveedor registrado correctamente.',
            'proveedor' => [
                'id' => $proveedor->id,
                'razon_social' => $proveedor->razon_social,
                'rfc' => $proveedor->rfc,
            ],
            'existente' => false,
        ], 201);
    }

    public function store(Request $request)
    {
        $this->autorizar();
        $data = $request->validate([
            'folio' => 'required|string|max:80|unique:compras_ordenes,folio',
            'proveedor_id' => 'required|integer|exists:proveedores,id',
            'fecha_orden' => 'nullable|date',
            'moneda' => 'required|string|max:10',
            'iva' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string|max:5000',
            'detalles' => 'required|array|min:1',
            'detalles.*.clave_material' => 'nullable|string|max:100',
            'detalles.*.descripcion' => 'required|string|max:500',
            'detalles.*.unidad' => 'nullable|string|max:40',
            'detalles.*.cantidad_solicitada' => 'required|numeric|gt:0',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
            'detalles.*.observaciones' => 'nullable|string|max:1000',
        ], [
            'folio.required' => 'El folio de la orden de compra es obligatorio.',
            'folio.string' => 'El folio debe ser texto válido.',
            'folio.max' => 'El folio no debe superar los 80 caracteres.',
            'folio.unique' => 'El folio de la orden de compra ya está registrado. Verifica el folio o consulta la orden existente.',
            'proveedor_id.required' => 'Debes seleccionar un proveedor.',
            'proveedor_id.integer' => 'El proveedor seleccionado no es válido.',
            'proveedor_id.exists' => 'El proveedor seleccionado ya no existe o no está disponible. Actualiza la página y vuelve a seleccionarlo.',
            'fecha_orden.date' => 'La fecha de la orden no tiene un formato válido.',
            'moneda.required' => 'Debes seleccionar la moneda de la orden.',
            'moneda.string' => 'La moneda seleccionada no es válida.',
            'moneda.max' => 'La moneda no debe superar los 10 caracteres.',
            'iva.numeric' => 'El IVA debe ser una cantidad numérica.',
            'iva.min' => 'El IVA no puede ser negativo.',
            'observaciones.string' => 'Las observaciones deben ser texto.',
            'observaciones.max' => 'Las observaciones no deben superar los 5000 caracteres.',
            'detalles.required' => 'Agrega al menos un producto a la orden.',
            'detalles.array' => 'Los productos de la orden tienen un formato inválido.',
            'detalles.min' => 'Agrega al menos un producto a la orden.',
            'detalles.*.clave_material.string' => 'La clave del producto debe ser texto.',
            'detalles.*.clave_material.max' => 'La clave del producto no debe superar los 100 caracteres.',
            'detalles.*.descripcion.required' => 'La descripción de cada producto es obligatoria.',
            'detalles.*.descripcion.string' => 'La descripción del producto debe ser texto.',
            'detalles.*.descripcion.max' => 'La descripción del producto no debe superar los 500 caracteres.',
            'detalles.*.unidad.string' => 'La unidad debe ser texto.',
            'detalles.*.unidad.max' => 'La unidad no debe superar los 40 caracteres.',
            'detalles.*.cantidad_solicitada.required' => 'La cantidad solicitada de cada producto es obligatoria.',
            'detalles.*.cantidad_solicitada.numeric' => 'La cantidad solicitada debe ser numérica.',
            'detalles.*.cantidad_solicitada.gt' => 'La cantidad solicitada debe ser mayor que cero.',
            'detalles.*.precio_unitario.required' => 'El precio unitario de cada producto es obligatorio.',
            'detalles.*.precio_unitario.numeric' => 'El precio unitario debe ser numérico.',
            'detalles.*.precio_unitario.min' => 'El precio unitario no puede ser negativo.',
            'detalles.*.observaciones.string' => 'Las observaciones del producto deben ser texto.',
            'detalles.*.observaciones.max' => 'Las observaciones del producto no deben superar los 1000 caracteres.',
        ]);
        // El PDF solo se utiliza para extraer datos; no se envía al guardar la orden.
        $pdfPath = null;
        try {
            DB::transaction(function () use ($data, $request, $pdfPath) {
                $subtotal = 0;
                foreach ($data['detalles'] as $d) $subtotal += (float)$d['cantidad_solicitada'] * (float)$d['precio_unitario'];
                $iva = (float)($data['iva'] ?? 0);
                $orden = CompraOrden::create([
                    'folio' => $data['folio'],
                    'proveedor_id' => $data['proveedor_id'],
                    'fecha_orden' => $data['fecha_orden'] ?? null,
                    'moneda' => $data['moneda'],
                    'subtotal' => round($subtotal, 2),
                    'iva' => round($iva, 2),
                    'total' => round($subtotal + $iva, 2),
                    'estado' => 'pendiente',
                    'pdf_path' => $pdfPath,
                    'observaciones' => $data['observaciones'] ?? null,
                    'creado_por' => auth()->id(),
                    'actualizado_por' => auth()->id(),
                ]);
                foreach (array_values($data['detalles']) as $i => $d) {
                    $cantidad = (float)$d['cantidad_solicitada'];
                    $precio = (float)$d['precio_unitario'];
                    CompraOrdenDetalle::create([
                        'orden_id' => $orden->id,
                        'renglon' => $i + 1,
                        'clave_material' => $d['clave_material'] ?? null,
                        'descripcion' => $d['descripcion'],
                        'unidad' => $d['unidad'] ?? null,
                        'cantidad_solicitada' => $cantidad,
                        'precio_unitario' => $precio,
                        'importe' => round($cantidad * $precio, 2),
                        'observaciones' => $d['observaciones'] ?? null,
                    ]);
                }
            });
        } catch (\Throwable $e) {
            if ($pdfPath && file_exists(public_path($pdfPath))) @unlink(public_path($pdfPath));
            throw $e;
        }
        return redirect()->route('compras.ordenes.index')->with('success', 'Orden de compra registrada. Revisa el detalle y el PDF asociado.');
    }




    public function show(CompraOrden $orden)
    {
        $this->autorizar();

        $orden->load([
            'proveedor',
            'detalles',
            'creador',
            'autorizadorCierre',
            'recepciones.recibidoPor',
            'recepciones.almacen',
            'recepciones.detalles.ordenDetalle',
        ]);

        foreach ($orden->detalles as $detalle) {
            $detalle->recibido_calculado = (float) DB::table('compras_recepciones_detalle')
                ->join(
                    'compras_recepciones',
                    'compras_recepciones.id',
                    '=',
                    'compras_recepciones_detalle.recepcion_id'
                )
                ->where('compras_recepciones.orden_id', $orden->id)
                ->where('compras_recepciones_detalle.orden_detalle_id', $detalle->id)
                ->sum('compras_recepciones_detalle.cantidad_recibida');
        }

        $almacenes = Almacen::where('activo', true)->orderBy('nombre')->get();

        return view('compras.ordenes.show', compact('orden', 'almacenes'));
    }


    public function edit(CompraOrden $orden)
    {
        $this->autorizar();
        $orden->load(['detalles', 'recepciones']);

        if (
            $orden->recepciones->isNotEmpty() ||
            in_array($orden->estado, ['recibida', 'cerrada_con_faltantes'], true)
        ) {
            return redirect()->route('compras.ordenes.show', $orden)
                ->with('error', 'Esta orden ya tiene recepciones o está cerrada; no se puede editar para proteger el historial.');
        }

        $proveedores = Proveedor::where(function ($q) use ($orden) {
            $q->where('activo', true)->orWhere('id', $orden->proveedor_id);
        })->orderBy('razon_social')->get();

        return view('compras.ordenes.edit', compact('orden', 'proveedores'));
    }

    public function update(Request $request, CompraOrden $orden)
    {
        $this->autorizar();
        $orden->load('recepciones');

        if (
            $orden->recepciones->isNotEmpty() ||
            in_array($orden->estado, ['recibida', 'cerrada_con_faltantes'], true)
        ) {
            return redirect()->route('compras.ordenes.show', $orden)
                ->with('error', 'Esta orden ya tiene recepciones o está cerrada; no se puede editar para proteger el historial.');
        }

        $data = $request->validate([
            'folio' => ['required', 'string', 'max:80', Rule::unique('compras_ordenes', 'folio')->ignore($orden->id)],
            'proveedor_id' => ['required', 'integer', 'exists:proveedores,id'],
            'fecha_orden' => ['nullable', 'date'],
            'moneda' => ['required', 'string', 'max:10'],
            'iva' => ['nullable', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string', 'max:5000'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.clave_material' => ['nullable', 'string', 'max:100'],
            'detalles.*.descripcion' => ['required', 'string', 'max:500'],
            'detalles.*.unidad' => ['nullable', 'string', 'max:40'],
            'detalles.*.cantidad_solicitada' => ['required', 'numeric', 'gt:0'],
            'detalles.*.precio_unitario' => ['required', 'numeric', 'min:0'],
            'detalles.*.observaciones' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($data, $orden) {
            $subtotal = 0.0;
            foreach ($data['detalles'] as $d) {
                $subtotal += (float) $d['cantidad_solicitada'] * (float) $d['precio_unitario'];
            }
            $iva = (float) ($data['iva'] ?? 0);

            $orden->update([
                'folio' => $data['folio'],
                'proveedor_id' => $data['proveedor_id'],
                'fecha_orden' => $data['fecha_orden'] ?? null,
                'moneda' => $data['moneda'],
                'subtotal' => round($subtotal, 2),
                'iva' => round($iva, 2),
                'total' => round($subtotal + $iva, 2),
                'observaciones' => $data['observaciones'] ?? null,
                'actualizado_por' => auth()->id(),
            ]);

            $orden->detalles()->delete();
            foreach (array_values($data['detalles']) as $i => $d) {
                $cantidad = (float) $d['cantidad_solicitada'];
                $precio = (float) $d['precio_unitario'];
                CompraOrdenDetalle::create([
                    'orden_id' => $orden->id,
                    'renglon' => $i + 1,
                    'clave_material' => $d['clave_material'] ?? null,
                    'descripcion' => $d['descripcion'],
                    'unidad' => $d['unidad'] ?? null,
                    'cantidad_solicitada' => $cantidad,
                    'precio_unitario' => $precio,
                    'importe' => round($cantidad * $precio, 2),
                    'observaciones' => $d['observaciones'] ?? null,
                ]);
            }
        });

        return redirect()->route('compras.ordenes.show', $orden)
            ->with('success', 'La orden de compra se actualizó correctamente.');
    }
}

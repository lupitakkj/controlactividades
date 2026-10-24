<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\AlmacenSalida;
use App\Models\AlmacenSalidaDetalle;
use App\Models\AlmacenSolicitudCancelacion;
use App\Models\AlmacenNotificacion;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AlmacenSalidaController extends Controller
{
    private function ver(): void
    {
        abort_unless(auth()->user()->hasAnyRole(['Almacén', 'Compras', 'Administrador']), 403);
    }

    private function registrar(): void
    {
        abort_unless(auth()->user()->hasRole('Almacén'), 403, 'Solo Almacén puede registrar salidas.');
    }

    private function resolver(): void
    {
        abort_unless(auth()->user()->hasAnyRole(['Compras', 'Administrador']), 403, 'Solo Compras o Administrador pueden resolver solicitudes.');
    }

    public function index()
    {
        $this->ver();
        $salidas = AlmacenSalida::with(['pedido', 'pedidos', 'almacen', 'creador'])
            ->when(request('buscar'), function ($q, $buscar) {
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('folio', 'like', "%{$buscar}%")
                        ->orWhereHas('pedido', fn ($p) => $p->where('pedido_no', 'like', "%{$buscar}%"))
                        ->orWhereHas('pedidos', fn ($p) => $p->where('pedido_no', 'like', "%{$buscar}%"))
                        ->orWhere('nombre_documento', 'like', "%{$buscar}%");
                });
            })
            ->latest('id')->paginate(20)->withQueryString();

        $pendientes = collect();
        if (auth()->user()->hasAnyRole(['Compras', 'Administrador'])) {
            $pendientes = AlmacenSolicitudCancelacion::with(['salida.pedido', 'solicitante'])
                ->where('estado', 'pendiente')->latest()->get();
        }
        return view('almacen-salidas.index', compact('salidas', 'pendientes'));
    }

    public function create()
    {
        $this->registrar();
        $almacenes = Almacen::where('activo', true)->orderBy('nombre')->get();
        $pedidos = Pedido::whereNull('fecha_terminado')
            ->orderByDesc('id')
            ->limit(500)
            ->get(['id', 'pedido_no', 'cliente']);
        $materiales = $this->existenciasPorAlmacen();
        return view('almacen-salidas.create', compact('almacenes', 'pedidos', 'materiales'));
    }

    private function existenciasPorAlmacen(?int $almacenId = null)
    {
        $entradas = DB::table('compras_recepciones_detalle as rd')
            ->join('compras_recepciones as r', 'r.id', '=', 'rd.recepcion_id')
            ->join('compras_ordenes_detalle as od', 'od.id', '=', 'rd.orden_detalle_id')
            ->whereNotNull('od.clave_material')->where('od.clave_material', '<>', '')
            ->when($almacenId, fn ($q) => $q->where('r.almacen_id', $almacenId))
            ->select('r.almacen_id', 'od.clave_material')
            ->selectRaw('MAX(od.descripcion) as descripcion, MAX(od.unidad) as unidad, SUM(rd.cantidad_recibida) as entradas')
            ->groupBy('r.almacen_id', 'od.clave_material');

        $salidas = DB::table('almacen_salidas_detalle as sd')
            ->join('almacen_salidas as s', 's.id', '=', 'sd.salida_id')
            ->whereIn('s.estado', ['apartada', 'registrada'])
            ->when($almacenId, fn ($q) => $q->where('s.almacen_id', $almacenId))
            ->select('s.almacen_id', 'sd.clave_material')
            ->selectRaw('SUM(sd.cantidad) as salidas')
            ->groupBy('s.almacen_id', 'sd.clave_material');

        return DB::query()->fromSub($entradas, 'e')
            ->leftJoinSub($salidas, 's', function ($join) {
                $join->on('s.almacen_id', '=', 'e.almacen_id')
                    ->on('s.clave_material', '=', 'e.clave_material');
            })
            ->select('e.almacen_id', 'e.clave_material', 'e.descripcion', 'e.unidad', 'e.entradas')
            ->selectRaw('COALESCE(s.salidas, 0) as salidas')
            ->selectRaw('(e.entradas - COALESCE(s.salidas, 0)) as existencia')
            ->whereRaw('(e.entradas - COALESCE(s.salidas, 0)) > 0')
            ->orderBy('e.clave_material')->get();
    }

    public function store(Request $request)
    {
        $this->registrar();

        $data = $request->validate([
            'nombre_documento' => ['required', 'string', 'max:180'],
            'pedido_ids' => ['nullable', 'array'],
            'pedido_ids.*' => ['integer', 'distinct', 'exists:pedidos,id'],
            'referencias_manuales' => ['nullable', 'array'],
            'referencias_manuales.*.numero' => ['required', 'string', 'max:100'],
            'referencias_manuales.*.nombre' => ['nullable', 'string', 'max:180'],
            'almacen_id' => ['required', 'exists:almacenes,id'],
            'fecha_salida' => ['required', 'date'],
            'recibido_por' => ['required', 'string', 'max:180'],
            'observaciones' => ['nullable', 'string', 'max:5000'],
            'materiales' => ['required', 'array', 'min:1'],
            'materiales.*.clave_material' => ['required', 'string', 'max:100'],
            'materiales.*.cantidad' => ['required', 'numeric', 'gt:0'],
        ]);

        $pedidoIds = collect($data['pedido_ids'] ?? [])->map(fn ($id) => (int) $id)->unique()->values();
        $referenciasManuales = collect($data['referencias_manuales'] ?? [])
            ->map(function ($ref) {
                return [
                    'numero' => trim((string) ($ref['numero'] ?? '')),
                    'nombre' => trim((string) ($ref['nombre'] ?? '')),
                ];
            })
            ->filter(fn ($ref) => $ref['numero'] !== '')
            ->unique(fn ($ref) => mb_strtolower($ref['numero'] . '|' . $ref['nombre']))
            ->values()
            ->all();

        if ($pedidoIds->isEmpty() && empty($referenciasManuales)) {
            throw ValidationException::withMessages([
                'pedido_ids' => 'Selecciona al menos una OP existente o agrega una referencia manual.',
            ]);
        }

        // Los pedidos reales terminados no se aceptan aunque se envíe su ID manualmente.
        if ($pedidoIds->isNotEmpty()) {
            $pedidosValidos = Pedido::whereIn('id', $pedidoIds)
                ->whereNull('fecha_terminado')
                ->pluck('id')
                ->map(fn ($id) => (int) $id);
            if ($pedidosValidos->count() !== $pedidoIds->count()) {
                throw ValidationException::withMessages([
                    'pedido_ids' => 'Una o más órdenes seleccionadas ya están terminadas o no están disponibles.',
                ]);
            }
        }

        abort_unless(Almacen::whereKey($data['almacen_id'])->where('activo', true)->exists(), 422, 'El almacén no está activo.');

        $salida = DB::transaction(function () use ($data, $pedidoIds, $referenciasManuales) {
            Almacen::whereKey($data['almacen_id'])->lockForUpdate()->firstOrFail();

            $salida = AlmacenSalida::create([
                // Se conserva la relación antigua para compatibilidad; las demás van en la tabla pivote.
                // Si no hay OP real, pedido_id queda null (la columna debe aceptar NULL).
                'pedido_id' => $pedidoIds->first(),
                'nombre_documento' => trim($data['nombre_documento']),
                'referencias_manuales' => $referenciasManuales ?: null,
                'almacen_id' => $data['almacen_id'],
                'fecha_salida' => $data['fecha_salida'],
                'recibido_por' => $data['recibido_por'],
                'observaciones' => $data['observaciones'] ?? null,
                'estado' => 'apartada',
                'creado_por' => auth()->id(),
                'folio' => 'TEMP-' . strtoupper(bin2hex(random_bytes(5))),
            ]);
            $salida->update(['folio' => 'SA-' . now()->format('Ymd') . '-' . str_pad((string) $salida->id, 6, '0', STR_PAD_LEFT)]);

            if ($pedidoIds->isNotEmpty()) {
                $salida->pedidos()->sync($pedidoIds->all());
            }

            $materiales = $this->existenciasPorAlmacen((int) $data['almacen_id'])->keyBy('clave_material');
            $agrupados = [];
            foreach ($data['materiales'] as $linea) {
                $clave = trim($linea['clave_material']);
                $agrupados[$clave] = ($agrupados[$clave] ?? 0) + (float) $linea['cantidad'];
            }

            foreach ($agrupados as $clave => $cantidad) {
                $material = $materiales->get($clave);
                if (!$material || (float) $material->existencia + 0.00001 < $cantidad) {
                    throw ValidationException::withMessages([
                        'materiales' => "Existencia insuficiente para {$clave}. Revisa la cantidad disponible y vuelve a intentar.",
                    ]);
                }
                AlmacenSalidaDetalle::create([
                    'salida_id' => $salida->id,
                    'clave_material' => $clave,
                    'descripcion' => $material->descripcion,
                    'unidad' => $material->unidad,
                    'cantidad' => $cantidad,
                ]);
            }

            $this->bitacora(
                'material_apartado',
                $salida,
                'Material apartado; pendiente de entrega por Almacén.',
                [
                    'pedido_ids' => $pedidoIds->all(),
                    'referencias_manuales' => $referenciasManuales,
                    'almacen_id' => $salida->almacen_id,
                    'materiales' => $agrupados,
                ]
            );

            return $salida;
        });

        return redirect()->route('almacen-salidas.show', $salida)->with('success', 'Material apartado correctamente. Quedó reservado y aún no se registra como salida.');
    }

    /**
     * Exporta el listado de salidas con el formato de Movimientos_SALIDAS.
     * Genera HTML compatible con Excel para evitar depender de PhpSpreadsheet
     * o extensiones que no siempre están disponibles en hosting compartido.
     */
    public function exportarListado(Request $request)
    {
        $this->ver();

        $salidas = AlmacenSalida::with(['pedido', 'pedidos', 'almacen', 'detalles'])
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $buscar = trim((string) $request->input('buscar'));
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('folio', 'like', "%{$buscar}%")
                        ->orWhereHas('pedido', fn ($p) => $p->where('pedido_no', 'like', "%{$buscar}%"));
                });
            })
            ->when($request->filled('desde'), fn ($q) => $q->whereDate('fecha_salida', '>=', $request->input('desde')))
            ->when($request->filled('hasta'), fn ($q) => $q->whereDate('fecha_salida', '<=', $request->input('hasta')))
            ->when($request->filled('almacen_id'), fn ($q) => $q->where('almacen_id', $request->integer('almacen_id')))
            ->orderBy('fecha_salida')
            ->orderBy('id')
            ->get();

        $headers = [
            'Clave artículo',
            'Almacén',
            'Concepto',
            'Fecha',
            'Tipo de documento',
            'Documento',
            'Clave cliente/proveedor',
            'Clave vendedor',
            'Cantidad',
            'Precio',
            'Costo',
        ];

        $escape = static function ($value): string {
            return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        };

        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>';
        $html .= '<table border="1"><thead><tr>';
        foreach ($headers as $header) {
            $html .= '<th>' . $escape($header) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        foreach ($salidas as $salida) {
            foreach ($salida->detalles as $detalle) {
                // Documento = OP + número de pedido, quitando ceros iniciales.
                $documentos = $salida->pedidos->map(function ($pedido) {
                    $numero = ltrim((string) $pedido->pedido_no, '0');
                    return $numero !== '' ? 'OP' . $numero : '';
                })->filter()->values()->all();
                if (empty($documentos) && $salida->pedido) {
                    $numero = ltrim((string) $salida->pedido->pedido_no, '0');
                    if ($numero !== '') $documentos[] = 'OP' . $numero;
                }
                foreach (($salida->referencias_manuales ?? []) as $ref) {
                    $documentos[] = trim(($ref['numero'] ?? '') . (!empty($ref['nombre']) ? ' - ' . $ref['nombre'] : ''));
                }
                $documento = implode(', ', array_unique(array_filter($documentos)));

                // Clave cliente/proveedor y vendedor no se infieren si Rijaya no las tiene.
                $row = [
                    $detalle->clave_material,
                    $salida->almacen->nombre ?? '',
                    $detalle->descripcion ?: ('Salida de material para OT ' . ($salida->pedido->pedido_no ?? '')),
                    optional($salida->fecha_salida)->format('d/m/Y H:i'),
                    'SALIDA',
                    $documento,
                    '',
                    '',
                    $detalle->cantidad,
                    '',
                    '',
                ];

                $html .= '<tr>';
                foreach ($row as $i => $value) {
                    // Evita que las claves con ceros iniciales se conviertan en números en Excel.
                    if (in_array($i, [0, 1, 2, 4, 5, 6, 7], true)) {
                        $html .= '<td style="mso-number-format:\'\\@\';">' . $escape($value) . '</td>';
                    } else {
                        $html .= '<td>' . $escape($value) . '</td>';
                    }
                }
                $html .= '</tr>';
            }
        }

        $html .= '</tbody></table></body></html>';
        $filename = 'Movimientos_SALIDAS_' . now()->format('Ymd_His') . '.xls';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    /**
     * Confirma la entrega física de un apartado y lo convierte en salida registrada.
     */
    public function confirmarEntrega(Request $request, AlmacenSalida $salida)
    {
        $this->registrar();

        DB::transaction(function () use ($salida) {
            $salida = AlmacenSalida::whereKey($salida->id)->lockForUpdate()->firstOrFail();

            if ($salida->estado !== 'apartada') {
                throw ValidationException::withMessages([
                    'salida' => 'Solo se puede confirmar la entrega de un material que esté apartado.',
                ]);
            }

            $salida->update(['estado' => 'registrada']);

            $this->bitacora(
                'apartado_entregado',
                $salida,
                'Almacén confirmó la entrega física; el apartado quedó registrado como salida.',
                ['almacen_id' => $salida->almacen_id]
            );
        });

        return redirect()->route('almacen-salidas.show', $salida)
            ->with('success', 'Entrega confirmada. La salida quedó registrada y el inventario se descontó.');
    }

    public function show(AlmacenSalida $salida)
    {
        $this->ver();
        $salida->load(['pedido', 'pedidos', 'almacen', 'creador', 'detalles', 'solicitudesCancelacion.solicitante', 'solicitudesCancelacion.resolver']);
        return view('almacen-salidas.show', compact('salida'));
    }

    public function solicitarCancelacion(Request $request, AlmacenSalida $salida)
    {
        abort_unless(auth()->user()->hasAnyRole(['Almacén', 'Compras', 'Administrador']), 403);
        $data = $request->validate(['motivo' => ['required', 'string', 'min:5', 'max:3000']]);
        if (!in_array($salida->estado, ['apartada', 'registrada'], true)) {
            throw ValidationException::withMessages(['salida' => 'Solo se puede solicitar la cancelación de un apartado o salida vigente.']);
        }
        if ($salida->solicitudesCancelacion()->where('estado', 'pendiente')->exists()) {
            throw ValidationException::withMessages(['salida' => 'Esta salida ya tiene una solicitud pendiente.']);
        }

        $solicitud = DB::transaction(function () use ($salida, $data) {
            $solicitud = AlmacenSolicitudCancelacion::create([
                'salida_id' => $salida->id,
                'solicitado_por' => auth()->id(),
                'motivo' => trim($data['motivo']),
                'estado' => 'pendiente',
            ]);
            $compras = \App\Models\User::role('Compras')->get();
            foreach ($compras as $usuario) {
                AlmacenNotificacion::create([
                    'user_id' => $usuario->id,
                    'tipo' => 'cancelacion_salida',
                    'titulo' => 'Solicitud de cancelación de salida',
                    'mensaje' => "La salida {$salida->folio} requiere autorización. Motivo: {$solicitud->motivo}",
                    'url' => route('almacen-salidas.show', $salida),
                ]);
            }
            $this->bitacora('cancelacion_solicitada', $salida, 'Se solicitó cancelar la salida.', ['solicitud_id' => $solicitud->id, 'motivo' => $solicitud->motivo]);
            return $solicitud;
        });

        return back()->with('success', 'Solicitud enviada a Compras para autorización.');
    }

    public function resolverCancelacion(Request $request, AlmacenSolicitudCancelacion $solicitud)
    {
        $this->resolver();
        $data = $request->validate([
            'decision' => ['required', 'in:autorizar,rechazar'],
            'respuesta_compras' => ['required_if:decision,rechazar', 'nullable', 'string', 'min:5', 'max:3000'],
        ]);

        DB::transaction(function () use ($solicitud, $data) {
            $solicitud = AlmacenSolicitudCancelacion::whereKey($solicitud->id)->lockForUpdate()->firstOrFail();
            if ($solicitud->estado !== 'pendiente') {
                throw ValidationException::withMessages(['solicitud' => 'Esta solicitud ya fue resuelta.']);
            }
            if ((int) $solicitud->solicitado_por === (int) auth()->id()) {
                throw ValidationException::withMessages(['solicitud' => 'Otra persona autorizada debe resolver una solicitud que tú presentaste.']);
            }
            $salida = AlmacenSalida::whereKey($solicitud->salida_id)->lockForUpdate()->firstOrFail();
            if (!in_array($salida->estado, ['apartada', 'registrada'], true)) {
                throw ValidationException::withMessages(['salida' => 'El apartado o salida ya no está vigente.']);
            }

            $autoriza = $data['decision'] === 'autorizar';
            $solicitud->update([
                'estado' => $autoriza ? 'autorizada' : 'rechazada',
                'resuelto_por' => auth()->id(),
                'respuesta_compras' => $autoriza ? ($data['respuesta_compras'] ?? null) : $data['respuesta_compras'],
                'resuelto_en' => now(),
            ]);
            if ($autoriza) {
                $salida->update(['estado' => 'cancelada']);
            }
            $this->bitacora(
                $autoriza ? 'cancelacion_autorizada' : 'cancelacion_rechazada',
                $salida,
                $autoriza ? 'Compras autorizó la cancelación; la salida quedó cancelada automáticamente.' : 'Compras rechazó la solicitud de cancelación.',
                ['solicitud_id' => $solicitud->id, 'motivo_solicitud' => $solicitud->motivo, 'respuesta' => $data['respuesta_compras'] ?? null]
            );

            AlmacenNotificacion::create([
                'user_id' => $solicitud->solicitado_por,
                'tipo' => 'resultado_cancelacion',
                'titulo' => $autoriza ? 'Cancelación autorizada' : 'Cancelación rechazada',
                'mensaje' => $autoriza ? "El apartado/salida {$salida->folio} fue cancelado." : "La solicitud de {$salida->folio} fue rechazada.",
                'url' => route('almacen-salidas.show', $salida),
            ]);
        });

        return back()->with('success', 'La solicitud se resolvió correctamente.');
    }

    public function notificaciones()
    {
        $this->ver();
        $notificaciones = AlmacenNotificacion::where('user_id', auth()->id())->latest()->paginate(30);
        return view('almacen-salidas.notificaciones', compact('notificaciones'));
    }

    public function marcarNotificacionLeida(AlmacenNotificacion $notificacion)
    {
        $this->ver();
        abort_unless($notificacion->user_id === auth()->id(), 403);
        $notificacion->update(['leida_en' => now()]);
        return redirect($notificacion->url ?: route('almacen-salidas.index'));
    }

    private function bitacora(string $evento, AlmacenSalida $salida, string $descripcion, array $datos = []): void
    {
        DB::table('almacen_movimientos_bitacora')->insert([
            'user_id' => auth()->id(),
            'evento' => $evento,
            'entidad' => 'almacen_salida',
            'entidad_id' => $salida->id,
            'folio' => $salida->folio,
            'descripcion' => $descripcion,
            'datos' => json_encode($datos, JSON_UNESCAPED_UNICODE),
            'ip' => request()->ip(),
            'user_agent' => substr((string)request()->userAgent(), 0, 500),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

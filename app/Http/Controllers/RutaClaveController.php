<?php

namespace App\Http\Controllers;

use App\Models\Partida;
use App\Models\Proceso;
use App\Models\RutaFabricacion;
use App\Models\RutaFabricacionClave;
use App\Services\AsignarRutaPorClave;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RutaClaveController extends Controller
{
    private function authorizeManagement(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasAnyRole(['Supervisor', 'Administrador']), 403);
    }

    public function index(Request $request)
    {
        $this->authorizeManagement();

        $procesos = Proceso::where('activo', true)->orderBy('orden')->get(['id', 'orden']);

        $reglas = RutaFabricacionClave::with('ruta')
            ->orderBy('clave')
            ->get()
            ->map(function ($regla) use ($procesos) {
                $regla->cantidad_partidas = $this->partidasElegiblesQuery($regla->clave)
                    ->with('despieceProcesos')
                    ->get()
                    ->filter(fn($partida) => $this->avancePartida($partida, $procesos) < 1)
                    ->count();
                return $regla;
            });

        $rutas = RutaFabricacion::where('activo', true)->orderBy('nombre')->get();
        $reglaEditar = $request->filled('editar') ? RutaFabricacionClave::find($request->query('editar')) : null;

        return view('rutas-fabricacion.claves', compact('reglas', 'rutas', 'reglaEditar'));
    }

    public function guardarClave(Request $request, AsignarRutaPorClave $asignador)
    {
        $this->authorizeManagement();

        $data = $request->validate([
            'clave' => ['required', 'string', 'max:100'],
            'ruta_fabricacion_id' => ['required', 'integer', Rule::exists('rutas_fabricacion', 'id')->where('activo', 1)],
        ]);

        $clave = mb_strtoupper(trim($data['clave']), 'UTF-8');
        $ruta = RutaFabricacion::with('etapas.proceso')->findOrFail($data['ruta_fabricacion_id']);

        if ($ruta->etapas->isEmpty()) {
            return back()->withErrors(['ruta_fabricacion_id' => 'La ruta seleccionada no tiene etapas.'])->withInput();
        }

        DB::transaction(function () use ($clave, $ruta, $asignador) {
            RutaFabricacionClave::updateOrCreate(
                ['clave' => $clave],
                ['ruta_fabricacion_id' => $ruta->id]
            );
            $resumen = $asignador->sincronizarClave($clave, $ruta);
            session()->flash('resumen_rutas', $resumen);
        });

        return redirect()->route('rutas-fabricacion.claves')
            ->with('success', "Se guardó la ruta predeterminada para {$clave}. Solo se sincronizan partidas elegibles; las excepciones manuales y rutas iniciadas se conservan.");
    }

    /** Muestra una simulación sin guardar la regla ni modificar partidas. */
    public function previsualizarClave(Request $request, AsignarRutaPorClave $asignador)
    {
        $this->authorizeManagement();

        $data = $request->validate([
            'clave' => ['required', 'string', 'max:100'],
            'ruta_fabricacion_id' => ['required', 'integer', Rule::exists('rutas_fabricacion', 'id')->where('activo', 1)],
        ]);

        $clave = mb_strtoupper(trim($data['clave']), 'UTF-8');
        $ruta = RutaFabricacion::with('etapas.proceso')->findOrFail($data['ruta_fabricacion_id']);
        if ($ruta->etapas->isEmpty()) {
            return back()->withErrors(['ruta_fabricacion_id' => 'La ruta seleccionada no tiene etapas.'])->withInput();
        }

        $plan = $asignador->previsualizarClave($clave, $ruta);
        return view('rutas-fabricacion.previsualizar', compact('clave', 'ruta', 'plan'));
    }

    /** Confirma la regla después de volver a calcular el plan con los datos actuales. */
    public function confirmarSincronizacion(Request $request, AsignarRutaPorClave $asignador)
    {
        $this->authorizeManagement();

        $data = $request->validate([
            'clave' => ['required', 'string', 'max:100'],
            'ruta_fabricacion_id' => ['required', 'integer', Rule::exists('rutas_fabricacion', 'id')->where('activo', 1)],
        ]);

        $clave = mb_strtoupper(trim($data['clave']), 'UTF-8');
        $ruta = RutaFabricacion::with('etapas.proceso')->findOrFail($data['ruta_fabricacion_id']);
        if ($ruta->etapas->isEmpty()) {
            return redirect()->route('rutas-fabricacion.claves')->withErrors(['ruta_fabricacion_id' => 'La ruta seleccionada no tiene etapas.']);
        }

        $resumen = DB::transaction(function () use ($clave, $ruta, $asignador) {
            RutaFabricacionClave::updateOrCreate(['clave' => $clave], ['ruta_fabricacion_id' => $ruta->id]);
            return $asignador->sincronizarClave($clave, $ruta);
        });

        return redirect()->route('rutas-fabricacion.claves')->with(
            'success',
            "Regla guardada para {$clave}. Asignadas: {$resumen['asignadas']}; actualizadas: {$resumen['actualizadas']}; protegidas: {$resumen['protegidas']}; excluidas: {$resumen['excluidas']}."
        );
    }

    public function eliminarClave(RutaFabricacionClave $regla)
    {
        $this->authorizeManagement();
        $regla->delete();

        return redirect()->route('rutas-fabricacion.claves')
            ->with('success', 'Se eliminó la regla predeterminada. Las rutas ya asignadas a las partidas no se modificaron.');
    }

    public function asignarIndex(Request $request)
    {
        $this->authorizeManagement();

        $busqueda = trim((string) $request->query('q', ''));
        $procesos = Proceso::where('activo', true)->orderBy('orden')->get(['id', 'orden']);

        // Solo partidas sin ruta asignada, de pedidos no terminados.
        $query = Partida::query()
            ->with(['pedido.controlOperativo', 'despieceProcesos'])
            ->whereDoesntHave('rutaAsignada')
            ->whereHas('pedido', function ($pedido) {
                $pedido->whereDoesntHave('controlOperativo', function ($control) {
                    $control->whereRaw("LOWER(TRIM(estado_operativo)) = ?", ['terminado']);
                });
            })
            ->when($busqueda !== '', function ($q) use ($busqueda) {
                $q->where(function ($sub) use ($busqueda) {
                    $sub->where('clave', 'like', "%{$busqueda}%")
                        ->orWhere('descripcion', 'like', "%{$busqueda}%")
                        ->orWhereHas('pedido', function ($pedido) use ($busqueda) {
                            $pedido->where('pedido_no', 'like', "%{$busqueda}%");
                        });
                });
            })
            ->orderByDesc('id');

        // El porcentaje se calcula con el mismo criterio de Despiece: procesos activos,
        // N/A fuera del promedio y procesos sin captura contando como 0%.
        $todasElegibles = $query->get()
            ->filter(fn($partida) => $this->avancePartida($partida, $procesos) < 1)
            ->values();

        $perPage = 30;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $partidas = new LengthAwarePaginator(
            $todasElegibles->slice(($page - 1) * $perPage, $perPage)->values(),
            $todasElegibles->count(),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        $rutas = RutaFabricacion::where('activo', true)->orderBy('nombre')->get();

        return view('rutas-fabricacion.asignar', compact('partidas', 'rutas', 'busqueda'));
    }

    public function asignarManual(Request $request, Partida $partida, AsignarRutaPorClave $asignador)
    {
        $this->authorizeManagement();

        // Evita guardar una ruta desde una pestaña antigua si la partida ya no es elegible.
        abort_if($partida->rutaAsignada()->exists(), 422, 'Esta partida ya tiene una ruta asignada. Actualiza la lista.');
        abort_if($partida->pedido?->controlOperativo && mb_strtolower(trim((string) $partida->pedido->controlOperativo->estado_operativo), 'UTF-8') === 'terminado', 422, 'El pedido está terminado y no se puede modificar.');

        $procesos = Proceso::where('activo', true)->orderBy('orden')->get(['id', 'orden']);
        $partida->load('despieceProcesos');
        abort_if($this->avancePartida($partida, $procesos) >= 1, 422, 'La partida ya está al 100 % y no se puede modificar.');

        $data = $request->validate([
            'ruta_fabricacion_id' => ['required', 'integer', Rule::exists('rutas_fabricacion', 'id')->where('activo', 1)],
        ]);

        $ruta = RutaFabricacion::with('etapas.proceso')->findOrFail($data['ruta_fabricacion_id']);
        if ($ruta->etapas->isEmpty()) {
            return back()->withErrors(['ruta_fabricacion_id' => 'La ruta seleccionada no tiene etapas.']);
        }

        try {
            $asignador->asignarManual($partida, $ruta, auth()->id());
        } catch (\RuntimeException $e) {
            return back()->withErrors(['ruta_fabricacion_id' => $e->getMessage()]);
        }

        return back()->with('success', "La partida {$partida->id} ahora tiene la ruta manual «{$ruta->nombre}». La regla general de la clave no cambió.");
    }


    /**
     * Resumen de partidas asignadas agrupadas por ruta.
     */

    public function seguimientoIndex()
    {
        $this->authorizeManagement();

        $asignaciones = \App\Models\PartidaRuta::with([
            'partida.pedido.controlOperativo',
            'partida.despieceProcesos',
            'etapas.proceso',
        ])->get();

        $procesos = Proceso::where('activo', true)
            ->orderBy('orden')
            ->get(['id', 'orden']);

        // Consultar los nombres actuales de las plantillas.
        $idsRutas = $asignaciones
            ->pluck('ruta_fabricacion_id')
            ->filter()
            ->unique()
            ->values();

        $nombresRutas = RutaFabricacion::whereIn('id', $idsRutas)
            ->pluck('nombre', 'id');

        // Agrupar por ID de ruta, no por el nombre histórico guardado.
        $distribucion = $asignaciones
            ->groupBy(
                fn($asignacion) =>
                $asignacion->ruta_fabricacion_id
                    ? 'ruta:' . $asignacion->ruta_fabricacion_id
                    : 'historica:' . ($asignacion->ruta_nombre_snapshot ?: 'sin-nombre')
            )
            ->map(function ($items) use ($procesos, $nombresRutas) {
                $primera = $items->first();
                $rutaId = $primera->ruta_fabricacion_id;

                $activas = 0;
                $terminadas = 0;

                foreach ($items as $asignacion) {
                    $partida = $asignacion->partida;

                    $estado = mb_strtolower(trim((string) (
                        $partida?->pedido?->controlOperativo?->estado_operativo ?? ''
                    )), 'UTF-8');

                    $avance = $partida
                        ? $this->avancePartida($partida, $procesos)
                        : 0;

                    if ($estado === 'terminado' || $avance >= 1) {
                        $terminadas++;
                    } else {
                        $activas++;
                    }
                }

                return (object) [
                    'ruta_id' => $rutaId,
                    'nombre' => $rutaId
                        ? ($nombresRutas[$rutaId] ?? $primera->ruta_nombre_snapshot ?? 'Ruta sin nombre')
                        : ($primera->ruta_nombre_snapshot ?: 'Ruta histórica sin plantilla'),
                    'total' => $items->count(),
                    'activas' => $activas,
                    'terminadas' => $terminadas,
                ];
            })
            ->sortBy('nombre')
            ->values();

        $totalAsignadas = $asignaciones->count();

        return view(
            'rutas-fabricacion.seguimiento-rutas',
            compact('distribucion', 'totalAsignadas')
        );
    }


    /**
     * Seguimiento visual de las partidas que tienen asignada una ruta.
     */
    public function seguimientoRuta(RutaFabricacion $ruta)
    {
        $this->authorizeManagement();

        $procesos = Proceso::where('activo', true)->orderBy('orden')->get(['id', 'orden']);

        $asignaciones = \App\Models\PartidaRuta::with([
            'partida.pedido.controlOperativo',
            'partida.despieceProcesos',
            'etapas.proceso',
        ])->where('ruta_fabricacion_id', $ruta->id)->get();

        $partidas = $asignaciones->map(function ($asignacion) use ($procesos) {
            $partida = $asignacion->partida;
            $despieces = $partida ? $partida->despieceProcesos->keyBy('proceso_id') : collect();
            $etapas = $asignacion->etapas->sortBy('orden')->map(function ($etapa) use ($despieces) {
                $despiece = $despieces->get($etapa->proceso_id);
                $aplica = $despiece ? (bool) $despiece->aplica : true;
                $porcentaje = $despiece && $aplica
                    ? max(0, min(1, (float) $despiece->porcentaje))
                    : 0;
                $estado = !$aplica ? 'No aplica' : ($porcentaje >= 1 ? 'Terminado' : ($porcentaje > 0 ? 'En proceso' : 'Pendiente'));

                return (object) [
                    'nombre' => $etapa->nombre_proceso_snapshot ?: ($etapa->proceso?->nombre ?? 'Proceso'),
                    'estado' => $estado,
                    'porcentaje' => $porcentaje,
                    'aplica' => $aplica,
                ];
            });

            $avance = $partida ? $this->avancePartida($partida, $procesos) : 0;
            $estadoPedido = trim((string) ($partida?->pedido?->controlOperativo?->estado_operativo ?? ''));
            return (object) [
                'pedido_no' => $partida?->pedido?->pedido_no ?? '—',
                'partida_id' => $partida?->id,
                'clave' => $partida?->clave ?? 'Sin clave',
                'descripcion' => $partida?->descripcion ?? '',
                'cantidad' => (float) ($partida?->cantidad ?? 0),
                'avance' => $avance,
                'estado_pedido' => $estadoPedido ?: 'Sin estado',
                'etapas' => $etapas,
                'origen' => $asignacion->origen_asignacion ?? 'predeterminada',
            ];
        })->sortBy([['pedido_no', 'asc'], ['partida_id', 'asc']])->values();

        return view('rutas-fabricacion.seguimiento-partidas', compact('ruta', 'partidas'));
    }

    private function partidasElegiblesQuery(string $clave)
    {
        return Partida::query()
            ->whereRaw('UPPER(TRIM(clave)) = ?', [mb_strtoupper(trim($clave), 'UTF-8')])
            ->whereDoesntHave('rutaAsignada')
            ->whereHas('pedido', function ($pedido) {
                $pedido->whereDoesntHave('controlOperativo', function ($control) {
                    $control->whereRaw("LOWER(TRIM(estado_operativo)) = ?", ['terminado']);
                });
            });
    }

    private function avancePartida(Partida $partida, $procesosActivos): float
    {
        if ($procesosActivos->isEmpty()) {
            return 0.0;
        }

        $despieces = $partida->relationLoaded('despieceProcesos')
            ? $partida->despieceProcesos->keyBy('proceso_id')
            : $partida->despieceProcesos()->get()->keyBy('proceso_id');

        $suma = 0.0;
        $cantidadAplicables = 0;

        foreach ($procesosActivos as $proceso) {
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

        return $cantidadAplicables > 0
            ? max(0.0, min(1.0, $suma / $cantidadAplicables))
            : 0.0;
    }
}

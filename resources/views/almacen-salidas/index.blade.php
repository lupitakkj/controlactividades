<x-app-layout>
    <div class="max-w-7xl mx-auto p-6 space-y-6 text-slate-900">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div><h1 class="text-2xl font-bold text-white">Salidas de almacén</h1><p class="text-sm text-slate-300">Historial y solicitudes de cancelación.</p></div>
            @if(auth()->user()->hasRole('Almacén'))
                <a class="rounded-lg bg-blue-600 px-4 py-2 text-white" href="{{ route('almacen-salidas.create') }}">Registrar salida</a>
            @endif
            <a class="rounded-lg border border-slate-300 bg-white px-4 py-2 font-medium text-slate-800 hover:bg-slate-100" href="{{ route('almacen-salidas.notificaciones') }}">Notificaciones</a>
        </div>

        @if(session('success')) <div class="rounded-lg bg-green-50 p-3 text-green-800">{{ session('success') }}</div> @endif
        <form method="GET" class="flex gap-2">
            <input name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por folio u orden de trabajo" class="w-full rounded-lg border border-slate-300 bg-white text-slate-900 placeholder:text-slate-500 focus:border-blue-600 focus:ring-blue-600">
            <button class="rounded-lg bg-slate-800 px-4 text-white">Buscar</button>
        </form>

        @if($pendientes->isNotEmpty())
            <section class="rounded-xl border border-amber-300 bg-amber-50 p-4 space-y-3">
                <h2 class="font-semibold text-amber-900">Solicitudes de cancelación pendientes</h2>
                @foreach($pendientes as $solicitud)
                    <div class="flex flex-wrap items-start justify-between gap-3 rounded-lg bg-white p-3">
                        <div>
                            <a class="font-semibold text-blue-700 underline" href="{{ route('almacen-salidas.show', $solicitud->salida) }}">{{ $solicitud->salida->folio }}</a>
                            <p class="text-sm">OT: {{ $solicitud->salida->pedidos->isNotEmpty() ? $solicitud->salida->pedidos->map(fn($p) => $p->pedido_no)->implode(', ') : ($solicitud->salida->pedido->pedido_no ?? '—') }} · Solicitó: {{ $solicitud->solicitante->name ?? '—' }}</p>
                            <p class="text-sm mt-1"><strong>Motivo:</strong> {{ $solicitud->motivo }}</p>
                        </div>
                        @if(auth()->user()->hasAnyRole(['Compras','Administrador']) && $solicitud->solicitado_por !== auth()->id())
                            <form method="POST" action="{{ route('almacen-salidas.cancelaciones.resolver', $solicitud) }}" class="flex flex-wrap items-center gap-2">
                                @csrf
                                <input name="respuesta_compras" class="rounded-lg border-slate-300 text-sm" placeholder="Motivo si rechazas">
                                <button name="decision" value="autorizar" class="rounded-lg bg-green-700 px-3 py-2 text-sm text-white" onclick="return confirm('¿Autorizar y cancelar automáticamente esta salida?')">Autorizar</button>
                                <button name="decision" value="rechazar" class="rounded-lg bg-red-700 px-3 py-2 text-sm text-white" onclick="return confirm('¿Rechazar esta solicitud?')">Rechazar</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </section>
        @endif

        <div class="overflow-x-auto rounded-xl border bg-white">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100 text-left"><tr><th class="p-3">Folio</th><th class="p-3">Orden de trabajo / referencia manual</th><th class="p-3">Documento</th><th class="p-3">Almacén</th><th class="p-3">Fecha</th><th class="p-3">Estado</th><th class="p-3"></th></tr></thead>
                <tbody>
                @forelse($salidas as $salida)
                    <tr class="border-t">
                        <td class="p-3 font-semibold">{{ $salida->folio }}</td>
                        <td class="p-3 text-slate-800">
                            @if($salida->pedidos->isNotEmpty())
                                <div class="space-y-1">
                                    @foreach($salida->pedidos as $pedido)
                                        <div><span class="font-semibold text-slate-900">OP{{ ltrim((string) $pedido->pedido_no, '0') ?: '0' }}</span><span class="block text-xs text-slate-600">{{ $pedido->cliente }}</span></div>
                                    @endforeach
                                </div>
                            @elseif($salida->pedido)
                                <span class="font-semibold">OP{{ ltrim((string) $salida->pedido->pedido_no, '0') ?: '0' }}</span>
                                <span class="block text-xs text-slate-600">{{ $salida->pedido->cliente }}</span>
                            @else
                                <span class="text-slate-500">Sin OP registrada</span>
                            @endif
                            @php
                                $refsTabla = $salida->referencias_manuales ?? [];
                                if (is_string($refsTabla)) $refsTabla = json_decode($refsTabla, true) ?: [];
                            @endphp
                            @if(is_array($refsTabla) && count($refsTabla))
                                <div class="mt-2 space-y-1 border-t border-slate-200 pt-2">
                                    @foreach($refsTabla as $ref)
                                        @php
                                            $refNumero = is_array($ref) ? ($ref['numero'] ?? $ref['referencia'] ?? '') : $ref;
                                            $refNombre = is_array($ref) ? ($ref['nombre'] ?? '') : '';
                                        @endphp
                                        @if($refNumero !== '')
                                            <div><span class="inline-block rounded bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-900">{{ $refNumero }}</span>
                                                @if($refNombre)<span class="block text-xs text-slate-600">{{ $refNombre }}</span>@endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="p-3 text-slate-800">{{ $salida->nombre_documento ?: '—' }}</td>
                        <td class="p-3 text-slate-800">{{ $salida->almacen->nombre ?? '—' }}</td>
                        <td class="p-3">{{ $salida->fecha_salida?->format('d/m/Y H:i') }}</td>
                        <td class="p-3">
                            @php
                                $claseEstado = match($salida->estado) {
                                    'apartada' => 'bg-amber-100 text-amber-900',
                                    'registrada' => 'bg-green-100 text-green-800',
                                    'cancelada' => 'bg-red-100 text-red-800',
                                    default => 'bg-slate-100 text-slate-800',
                                };
                                $textoEstado = match($salida->estado) {
                                    'apartada' => 'Apartada',
                                    'registrada' => 'Registrada',
                                    'cancelada' => 'Cancelada',
                                    default => ucfirst($salida->estado),
                                };
                            @endphp
                            <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $claseEstado }}">{{ $textoEstado }}</span>
                        </td>
                        <td class="p-3">
                            <a href="{{ route('almacen-salidas.show', $salida) }}"
                               aria-label="Ver detalle de la salida {{ $salida->folio }}"
                               title="Ver detalle"
                               class="inline-flex h-9 w-9 items-center justify-center rounded-lg
                                      border border-blue-200 bg-blue-50 text-blue-700
                                      transition hover:border-blue-400 hover:bg-blue-600
                                      hover:text-white focus:outline-none focus:ring-2
                                      focus:ring-blue-500 focus:ring-offset-2">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-5 w-5"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="1.8"
                                     stroke-linecap="round"
                                     stroke-linejoin="round"
                                     aria-hidden="true">
                                    <path d="M2.5 12s3.5-7 9.5-7 9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7Z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-6 text-center text-slate-600">No hay salidas registradas.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $salidas->links() }}
    </div>
</x-app-layout>

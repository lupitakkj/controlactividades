<x-app-layout>
    <div class="max-w-7xl mx-auto p-6 space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div><h1 class="text-2xl font-bold text-slate-800">Salidas de almacén</h1><p class="text-sm text-slate-500">Historial y solicitudes de cancelación.</p></div>
            @if(auth()->user()->hasRole('Almacén'))
                <a class="rounded-lg bg-blue-600 px-4 py-2 text-white" href="{{ route('almacen-salidas.create') }}">Registrar salida</a>
            @endif
            <a class="rounded-lg border px-4 py-2" href="{{ route('almacen-salidas.notificaciones') }}">Notificaciones</a>
        </div>

        @if(session('success')) <div class="rounded-lg bg-green-50 p-3 text-green-800">{{ session('success') }}</div> @endif
        <form method="GET" class="flex gap-2">
            <input name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por folio u orden de trabajo" class="w-full rounded-lg border-slate-300">
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
                <thead class="bg-slate-100 text-left"><tr><th class="p-3">Folio</th><th class="p-3">Orden de trabajo</th><th class="p-3">Almacén</th><th class="p-3">Fecha</th><th class="p-3">Estado</th><th class="p-3"></th></tr></thead>
                <tbody>
                @forelse($salidas as $salida)
                    <tr class="border-t">
                        <td class="p-3 font-semibold">{{ $salida->folio }}</td>
                        <td class="p-3">{{ $salida->pedidos->isNotEmpty() ? $salida->pedidos->map(fn($p) => $p->pedido_no)->implode(', ') : ($salida->pedido->pedido_no ?? '—') }}</td>
                        <td class="p-3">{{ $salida->almacen->nombre ?? '—' }}</td>
                        <td class="p-3">{{ $salida->fecha_salida?->format('d/m/Y H:i') }}</td>
                        <td class="p-3"><span class="rounded-full px-2 py-1 {{ $salida->estado === 'cancelada' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">{{ ucfirst($salida->estado) }}</span></td>
                        <td class="p-3"><a class="text-blue-700 underline" href="{{ route('almacen-salidas.show', $salida) }}">Ver detalle</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-slate-500">No hay salidas registradas.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $salidas->links() }}
    </div>
</x-app-layout>

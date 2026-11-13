<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Seguimiento de partidas</h1>
                    <p class="mt-1 text-sm text-gray-500">Ruta: {{ $ruta->nombre }} · {{ $partidas->count() }} partidas</p>
                </div>
                <a href="{{ route('rutas-fabricacion.seguimiento') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700">Volver a distribución</a>
            </div>

            @forelse($partidas as $partida)
                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm space-y-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <div class="text-lg font-bold text-gray-900">{{ $partida->pedido_no }} · {{ $partida->clave }}</div>
                            <div class="mt-1 text-sm text-gray-600">{{ $partida->descripcion }}</div>
                            <div class="mt-1 text-xs text-gray-500">Partida ID: {{ $partida->partida_id }} · Cantidad: {{ number_format($partida->cantidad, 0) }} · Asignación: {{ $partida->origen === 'manual' ? 'Excepción manual' : 'Predeterminada' }}</div>
                        </div>
                        <div class="text-left sm:text-right">
                            <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">{{ $partida->estado_pedido }}</span>
                            <div class="mt-2 text-xl font-bold text-gray-900">{{ number_format($partida->avance * 100, 1) }}%</div>
                            <div class="text-xs text-gray-500">Avance de Despiece</div>
                        </div>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                        <div class="h-full rounded-full {{ $partida->avance >= 1 ? 'bg-emerald-500' : 'bg-indigo-600' }}" style="width: {{ min(100, max(0, $partida->avance * 100)) }}%"></div>
                    </div>
                    @if($partida->etapas->isNotEmpty())
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                            @foreach($partida->etapas as $etapa)
                                @php
                                    $color = $etapa->estado === 'Terminado' ? 'bg-emerald-500' : ($etapa->estado === 'En proceso' ? 'bg-amber-500' : ($etapa->estado === 'No aplica' ? 'bg-gray-300' : 'bg-gray-300'));
                                @endphp
                                <div class="min-w-0">
                                    <div class="h-2 rounded-full {{ $color }}"></div>
                                    <div class="mt-2 text-sm font-semibold text-gray-800">{{ $etapa->nombre }}</div>
                                    <div class="text-xs text-gray-500">{{ $etapa->estado }}@if($etapa->aplica) · {{ number_format($etapa->porcentaje * 100, 0) }}%@endif</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-amber-700">La asignación no tiene etapas guardadas.</p>
                    @endif
                </section>
            @empty
                <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-gray-500">Esta ruta no tiene partidas asignadas actualmente.</div>
            @endforelse
        </div>
    </div>
</x-app-layout>

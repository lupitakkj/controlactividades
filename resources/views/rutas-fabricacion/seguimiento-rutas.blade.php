<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Distribución de partidas por ruta</h1>
                    <p class="mt-1 text-sm text-gray-500">Consulta cuántas partidas utilizan cada ruta y abre su seguimiento de fabricación.</p>
                </div>
                <a href="{{ route('rutas-fabricacion.claves') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700">Volver a rutas por clave</a>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="text-sm text-gray-500">Partidas con ruta</div>
                    <div class="mt-1 text-3xl font-bold text-gray-900">{{ $totalAsignadas }}</div>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="text-sm text-gray-500">Rutas con asignaciones</div>
                    <div class="mt-1 text-3xl font-bold text-gray-900">{{ $distribucion->count() }}</div>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="text-sm text-gray-500">Partidas activas</div>
                    <div class="mt-1 text-3xl font-bold text-emerald-700">{{ $distribucion->sum('activas') }}</div>
                </div>
            </div>

            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="font-semibold text-gray-900">Partidas por ruta</h2>
                    <p class="mt-1 text-sm text-gray-500">El conteo incluye asignaciones activas y terminadas; ambas se distinguen para conservar visibilidad del historial.</p>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($distribucion as $item)
                        <div class="p-5">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-semibold text-gray-900">{{ $item->nombre }}</h3>
                                        <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">{{ $item->total }} partidas</span>
                                    </div>
                                    <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-sm">
                                        <span class="text-emerald-700">{{ $item->activas }} activas</span>
                                        <span class="text-gray-500">{{ $item->terminadas }} terminadas o al 100 %</span>
                                    </div>
                                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-gray-100">
                                        <div class="h-full rounded-full bg-emerald-500" style="width: {{ $totalAsignadas > 0 ? min(100, ($item->total / $totalAsignadas) * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                                @if($item->ruta_id)
                                    <a href="{{ route('rutas-fabricacion.seguimiento.detalle', $item->ruta_id) }}" class="inline-flex justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Ver partidas</a>
                                @else
                                    <span class="text-sm text-gray-500">Ruta histórica sin plantilla vinculada</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-sm text-gray-500">Todavía no hay partidas con rutas asignadas.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>

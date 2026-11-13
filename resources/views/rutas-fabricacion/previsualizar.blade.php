<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Vista previa de sincronización</h1>
                    <p class="mt-1 text-sm text-gray-500">Clave: <strong>{{ $clave }}</strong>. Esta simulación no ha guardado cambios.</p>
                </div>
                <a href="{{ route('rutas-fabricacion.claves') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700">Volver sin guardar</a>
            </div>

            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm space-y-2">
                <div class="text-sm text-gray-500">Ruta predeterminada que se propone</div>
                <div class="text-lg font-semibold text-gray-900">{{ $ruta->nombre }}</div>
                <div class="flex flex-wrap gap-2 pt-2">
                    @foreach($ruta->etapas->sortBy('orden') as $etapa)
                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-800">{{ $etapa->orden }}. {{ $etapa->proceso?->nombre ?? 'Proceso' }}</span>
                    @endforeach
                </div>
            </section>

            @php($resumen = $plan['resumen'])
            <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
                @foreach([
                    'asignar' => ['Asignar', 'Partidas sin ruta', 'text-emerald-700'],
                    'actualizar' => ['Actualizar', 'Ruta automática sin iniciar', 'text-indigo-700'],
                    'conservar' => ['Conservar', 'Excepción manual o ruta iniciada', 'text-amber-700'],
                    'sin_cambios' => ['Sin cambios', 'Ya tienen la ruta seleccionada', 'text-gray-700'],
                    'excluir' => ['Excluir', 'Pedido terminado o partida al 100 %', 'text-red-700'],
                ] as $key => $info)
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                        <div class="text-sm text-gray-500">{{ $info[0] }}</div>
                        <div class="mt-1 text-3xl font-bold {{ $info[2] }}">{{ $resumen[$key] }}</div>
                        <div class="mt-1 text-xs text-gray-500">{{ $info[1] }}</div>
                    </div>
                @endforeach
            </section>

            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="font-semibold text-gray-900">Detalle por partida</h2>
                    <p class="mt-1 text-sm text-gray-500">Las partidas excluidas se muestran solo para explicar por qué no se modificarán.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Pedido</th><th class="px-4 py-3">Partida / clave</th><th class="px-4 py-3">Cantidad</th><th class="px-4 py-3">Avance</th><th class="px-4 py-3">Ruta actual</th><th class="px-4 py-3">Acción prevista</th><th class="px-4 py-3">Motivo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($plan['detalle'] as $item)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $item['pedido_no'] }}</td>
                                    <td class="min-w-56 px-4 py-3"><div class="font-semibold">{{ $item['clave'] ?: 'Sin clave' }}</div><div class="text-xs text-gray-500">{{ $item['descripcion'] }}</div><div class="text-xs text-gray-400">ID partida: {{ $item['partida_id'] }}</div></td>
                                    <td class="px-4 py-3">{{ number_format($item['cantidad'], 0) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ number_format($item['avance'] * 100, 2) }}%</td>
                                    <td class="min-w-40 px-4 py-3">{{ $item['ruta_actual'] }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        @php($colores = ['asignar' => 'bg-emerald-50 text-emerald-700', 'actualizar' => 'bg-indigo-50 text-indigo-700', 'conservar' => 'bg-amber-50 text-amber-700', 'sin_cambios' => 'bg-gray-100 text-gray-700', 'excluir' => 'bg-red-50 text-red-700'])
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $colores[$item['accion']] ?? 'bg-gray-100 text-gray-700' }}">{{ ucfirst(str_replace('_', ' ', $item['accion'])) }}</span>
                                    </td>
                                    <td class="min-w-44 px-4 py-3 text-gray-600">{{ $item['motivo'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-5 py-8 text-center text-gray-500">No se encontraron partidas con esta clave.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('rutas-fabricacion.claves') }}" class="rounded-lg border border-gray-300 bg-white px-5 py-3 text-center text-sm font-semibold text-gray-700">Cancelar</a>
                <form method="POST" action="{{ route('rutas-fabricacion.claves.confirmar') }}" onsubmit="return confirm('¿Confirmas guardar la regla y sincronizar las partidas elegibles? Se volverán a validar las condiciones antes de aplicar los cambios.');">
                    @csrf
                    <input type="hidden" name="clave" value="{{ $clave }}">
                    <input type="hidden" name="ruta_fabricacion_id" value="{{ $ruta->id }}">
                    <button type="submit" class="w-full rounded-lg bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700">Confirmar y sincronizar</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

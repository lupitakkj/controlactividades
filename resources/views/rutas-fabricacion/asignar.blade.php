<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Excepciones de ruta por partida</h1>
                    <p class="mt-1 text-sm text-gray-500">Cambia la ruta de una sola partida activa con avance menor al 100 %. Los pedidos terminados y las partidas al 100 % no aparecen; la regla predeterminada de la clave no se modifica.</p>
                </div>
                <a href="{{ route('rutas-fabricacion.claves') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700">Rutas por clave</a>
            </div>

            @if(session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
            @endif

            <form method="GET" class="flex flex-col gap-2 rounded-xl border border-gray-200 bg-white p-4 sm:flex-row">
                <input name="q" value="{{ $busqueda }}" placeholder="Buscar por clave, descripción o pedido..." class="min-w-0 flex-1 rounded-lg border-gray-300">
                <button class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white">Buscar</button>
                @if($busqueda !== '')<a href="{{ route('rutas-fabricacion.asignar') }}" class="rounded-lg border px-4 py-2 text-center text-sm">Limpiar</a>@endif
            </form>

            <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                        <tr><th class="px-4 py-3">Pedido</th><th class="px-4 py-3">Clave / producto</th><th class="px-4 py-3">Cantidad</th><th class="px-4 py-3">Ruta actual</th><th class="px-4 py-3">Cambiar ruta individual</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($partidas as $partida)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-3">{{ $partida->pedido?->pedido_no ?? '—' }}</td>
                                <td class="min-w-56 px-4 py-3"><div class="font-semibold text-gray-900">{{ $partida->clave ?? 'Sin clave' }}</div><div class="mt-1 text-xs text-gray-500">{{ $partida->descripcion }}</div><div class="mt-1 text-xs {{ $partida->rutaAsignada?->origen_asignacion === 'manual' ? 'text-amber-700' : 'text-gray-500' }}">{{ $partida->rutaAsignada?->origen_asignacion === 'manual' ? 'Excepción manual' : ($partida->rutaAsignada ? 'Ruta asignada por sistema' : 'Sin ruta asignada') }}</div></td>
                                <td class="whitespace-nowrap px-4 py-3">{{ number_format((float)$partida->cantidad, 0) }}</td>
                                <td class="min-w-44 px-4 py-3">{{ $partida->rutaAsignada?->ruta_nombre_snapshot ?? 'Sin ruta' }}</td>
                                <td class="min-w-72 px-4 py-3">
                                    <form method="POST" action="{{ route('rutas-fabricacion.asignar.guardar', $partida) }}" class="flex gap-2">
                                        @csrf
                                        <select name="ruta_fabricacion_id" required class="min-w-0 flex-1 rounded-lg border-gray-300 text-xs">
                                            <option value="">Selecciona ruta</option>
                                            @foreach($rutas as $ruta)
                                                <option value="{{ $ruta->id }}" @selected((string)($partida->rutaAsignada?->ruta_fabricacion_id ?? '') === (string)$ruta->id)>{{ $ruta->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <button class="rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700">Guardar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-8 text-center text-gray-500">No se encontraron partidas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div>{{ $partidas->links() }}</div>
        </div>
    </div>
</x-app-layout>

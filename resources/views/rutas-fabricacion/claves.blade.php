<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Rutas predeterminadas por clave</h1>
                    <p class="mt-1 text-sm text-gray-500">Configura una ruta por clave de producto. Solo se asigna o actualiza en pedidos activos y partidas con avance menor al 100 %, respetando excepciones manuales y rutas iniciadas.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('rutas-fabricacion.seguimiento') }}" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">Distribución y seguimiento</a>
                    <a href="{{ route('rutas-fabricacion.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700">Plantillas de rutas</a>
                    <a href="{{ route('rutas-fabricacion.asignar') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Excepciones por partida</a>
                </div>
            </div>

            @if(session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
            @endif

            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="font-semibold text-gray-900">{{ $reglaEditar ? 'Editar regla: ' . $reglaEditar->clave : 'Crear una regla' }}</h2>
                <p class="mt-1 text-sm text-gray-500">Primero revisa el impacto. Nada se guarda ni se modifica hasta confirmar la sincronización.</p>
                <form method="POST" action="{{ route('rutas-fabricacion.claves.previsualizar') }}" class="mt-4 grid gap-4 sm:grid-cols-3">
                    @csrf
                    <label class="block">
                        <span class="text-sm font-medium text-gray-700">Clave del producto</span>
                        <input name="clave" required maxlength="100" value="{{ old('clave', $reglaEditar?->clave) }}" placeholder="Ej. TPGO11136" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm">
                    </label>
                    <label class="block sm:col-span-2">
                        <span class="text-sm font-medium text-gray-700">Ruta predeterminada</span>
                        <select name="ruta_fabricacion_id" required class="mt-1 w-full rounded-lg border-gray-300 shadow-sm">
                            <option value="">Selecciona una ruta</option>
                            @foreach($rutas as $ruta)
                                <option value="{{ $ruta->id }}" @selected((string)old('ruta_fabricacion_id', $reglaEditar?->ruta_fabricacion_id) === (string)$ruta->id)>{{ $ruta->nombre }}</option>
                            @endforeach
                        </select>
                    </label>
                    <div class="sm:col-span-3">
                        <button class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">{{ $reglaEditar ? 'Previsualizar cambios' : 'Previsualizar sincronización' }}</button>
                        @if($reglaEditar)
                            <a href="{{ route('rutas-fabricacion.claves') }}" class="ml-2 inline-block rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700">Cancelar edición</a>
                        @endif
                    </div>
                </form>
            </section>

            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="font-semibold text-gray-900">Reglas configuradas</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ $reglas->count() }} claves configuradas</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                            <tr><th class="px-5 py-3">Clave</th><th class="px-5 py-3">Ruta predeterminada</th><th class="px-5 py-3">Partidas elegibles</th><th class="px-5 py-3">Acción</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($reglas as $regla)
                                <tr>
                                    <td class="whitespace-nowrap px-5 py-3 font-semibold text-gray-900">{{ $regla->clave }}</td>
                                    <td class="px-5 py-3">{{ $regla->ruta?->nombre ?? 'Ruta no disponible' }}</td>
                                    <td class="px-5 py-3">{{ $regla->cantidad_partidas }}</td>
                                    <td class="px-5 py-3">
                                        <div class="flex flex-wrap items-center gap-3">
                                        <a href="{{ route('rutas-fabricacion.claves', ['editar' => $regla->id]) }}" class="font-semibold text-indigo-600 hover:text-indigo-800">Editar</a>
                                        <form method="POST" action="{{ route('rutas-fabricacion.claves.eliminar', $regla) }}" onsubmit="return confirm('¿Eliminar solo la regla predeterminada? Las rutas ya asignadas no se borrarán.');">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600 hover:text-red-800">Eliminar</button>
                                        </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500">Todavía no hay reglas. Agrega una clave y su ruta predeterminada.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>

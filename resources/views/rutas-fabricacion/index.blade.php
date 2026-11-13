<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Rutas de fabricación</h1>
                    <p class="mt-1 text-sm text-gray-500">Administra las secuencias de procesos que puede seguir cada producto.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('rutas-fabricacion.claves') }}" class="inline-flex items-center justify-center rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-2.5 text-sm font-semibold text-indigo-700">Rutas por clave</a>
                    <a href="{{ route('rutas-fabricacion.asignar') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700">Excepciones por partida</a>
                    <a href="{{ route('rutas-fabricacion.create') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">+ Nueva ruta</a>
                </div>
            </div>

            @if(session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
            @endif

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse($rutas as $ruta)
                    <article class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="flex items-start justify-between gap-3 border-b border-gray-100 px-5 py-4">
                            <div>
                                <h2 class="font-semibold text-gray-900">{{ $ruta->nombre }}</h2>
                                <p class="mt-1 text-xs text-gray-500">{{ $ruta->etapas->count() }} procesos</p>
                            </div>
                            @if($ruta->activo)
                                <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-800">Activa</span>
                            @else
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">Inactiva</span>
                            @endif
                        </div>
                        @if($ruta->descripcion)
                            <p class="px-5 pt-4 text-sm text-gray-600">{{ $ruta->descripcion }}</p>
                        @endif
                        <ol class="space-y-2 px-5 py-4">
                            @foreach($ruta->etapas as $etapa)
                                <li class="flex items-center gap-3 text-sm text-gray-700">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-xs font-semibold text-indigo-700">{{ $etapa->orden }}</span>
                                    <span>{{ $etapa->proceso?->nombre ?? 'Proceso eliminado' }}</span>
                                </li>
                            @endforeach
                        </ol>
                        <div class="flex flex-wrap gap-2 border-t border-gray-100 bg-gray-50 px-5 py-3">
                            <a class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100" href="{{ route('rutas-fabricacion.edit', $ruta) }}">Editar</a>
                            <form method="POST" action="{{ route('rutas-fabricacion.duplicate', $ruta) }}">@csrf<button class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">Duplicar</button></form>
                            <form method="POST" action="{{ route('rutas-fabricacion.toggle', $ruta) }}">@csrf @method('PATCH')<button class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">{{ $ruta->activo ? 'Desactivar' : 'Activar' }}</button></form>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center md:col-span-2 xl:col-span-3">
                        <h2 class="font-semibold text-gray-800">Todavía no hay rutas</h2>
                        <p class="mt-1 text-sm text-gray-500">Crea la primera ruta y agrega los procesos en el orden de fabricación.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

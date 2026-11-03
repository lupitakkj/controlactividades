<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ $ruta->exists ? route('rutas-fabricacion.update', $ruta) : route('rutas-fabricacion.store') }}" class="space-y-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-7">
                @csrf
                @if($ruta->exists) @method('PUT') @endif
                <div>
                    <a href="{{ route('rutas-fabricacion.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">← Volver a rutas</a>
                    <h1 class="mt-3 text-2xl font-bold text-gray-900">{{ $ruta->exists ? 'Editar ruta' : 'Crear ruta de fabricación' }}</h1>
                    <p class="mt-1 text-sm text-gray-500">Selecciona los procesos existentes y ordénalos según el flujo de fabricación.</p>
                </div>
                @if($errors->any())
                    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
                @endif
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block sm:col-span-2"><span class="text-sm font-medium text-gray-700">Nombre de la ruta *</span><input name="nombre" required maxlength="150" value="{{ old('nombre', $ruta->nombre) }}" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ej. Poste con corte láser"></label>
                    <label class="block sm:col-span-2"><span class="text-sm font-medium text-gray-700">Descripción</span><textarea name="descripcion" rows="2" maxlength="1000" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Notas sobre cuándo se utiliza esta ruta">{{ old('descripcion', $ruta->descripcion) }}</textarea></label>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-3"><h2 class="font-semibold text-gray-900">Secuencia de procesos</h2><button type="button" id="agregar-etapa" class="rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">+ Agregar proceso</button></div>
                    <p class="text-sm text-gray-500">Usa las flechas para cambiar el orden. Cada proceso solo puede aparecer una vez por ruta.</p>
                    <div id="etapas" class="space-y-2">
                        @php $etapasActuales = old('etapas', $ruta->exists ? $ruta->etapas->map(fn($e) => ['proceso_id' => $e->proceso_id, 'obligatorio' => $e->obligatorio])->toArray() : []); @endphp
                        @foreach($etapasActuales as $i => $etapa)
                            <div class="etapa flex flex-col gap-2 rounded-lg border border-gray-200 p-3 sm:flex-row sm:items-center">
                                <span class="numero flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold text-gray-700">{{ $i + 1 }}</span>
                                <select name="etapas[{{ $i }}][proceso_id]" required class="proceso-select min-w-0 flex-1 rounded-lg border-gray-300 text-sm">
                                    <option value="">Selecciona un proceso</option>
                                    @foreach($procesos as $proceso)
                                        <option value="{{ $proceso->id }}" @selected((string)($etapa['proceso_id'] ?? '') === (string)$proceso->id)>{{ $proceso->nombre }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" class="orden-input" name="etapas[{{ $i }}][orden]" value="{{ $i + 1 }}">
                                <label class="flex items-center gap-2 whitespace-nowrap text-sm text-gray-600"><input type="checkbox" name="etapas[{{ $i }}][obligatorio]" value="1" class="rounded border-gray-300 text-indigo-600" @checked(!isset($etapa['obligatorio']) || $etapa['obligatorio'])> Obligatorio</label>
                                <div class="flex gap-1"><button type="button" class="subir rounded border px-2 py-1 text-gray-600" title="Subir">↑</button><button type="button" class="bajar rounded border px-2 py-1 text-gray-600" title="Bajar">↓</button><button type="button" class="quitar rounded border border-red-200 px-2 py-1 text-red-600" title="Quitar">×</button></div>
                            </div>
                        @endforeach
                    </div>
                    <p id="sin-etapas" class="{{ count($etapasActuales) ? 'hidden' : '' }} rounded-lg bg-amber-50 p-3 text-sm text-amber-800">Agrega al menos un proceso para guardar la ruta.</p>
                </div>

                <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700"><input type="checkbox" name="activo" value="1" class="rounded border-gray-300 text-indigo-600" @checked(old('activo', $ruta->exists ? $ruta->activo : true))> Ruta activa</label>
                <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end"><a href="{{ route('rutas-fabricacion.index') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-center text-sm font-semibold text-gray-700">Cancelar</a><button class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Guardar ruta</button></div>
            </form>
        </div>
    </div>

    <template id="plantilla-etapa">
        <div class="etapa flex flex-col gap-2 rounded-lg border border-gray-200 p-3 sm:flex-row sm:items-center">
            <span class="numero flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold text-gray-700"></span>
            <select required class="proceso-select min-w-0 flex-1 rounded-lg border-gray-300 text-sm"><option value="">Selecciona un proceso</option>@foreach($procesos as $proceso)<option value="{{ $proceso->id }}">{{ $proceso->nombre }}</option>@endforeach</select>
            <input type="hidden" class="orden-input">
            <label class="flex items-center gap-2 whitespace-nowrap text-sm text-gray-600"><input type="checkbox" value="1" class="obligatorio rounded border-gray-300 text-indigo-600" checked> Obligatorio</label>
            <div class="flex gap-1"><button type="button" class="subir rounded border px-2 py-1 text-gray-600" title="Subir">↑</button><button type="button" class="bajar rounded border px-2 py-1 text-gray-600" title="Bajar">↓</button><button type="button" class="quitar rounded border border-red-200 px-2 py-1 text-red-600" title="Quitar">×</button></div>
        </div>
    </template>
    <script>
    (() => {
        const list = document.getElementById('etapas');
        const template = document.getElementById('plantilla-etapa');
        const empty = document.getElementById('sin-etapas');
        function renumber() {
            [...list.querySelectorAll('.etapa')].forEach((row, i) => {
                row.querySelector('.numero').textContent = i + 1;
                row.querySelector('.proceso-select').name = `etapas[${i}][proceso_id]`;
                row.querySelector('.orden-input').name = `etapas[${i}][orden]`;
                row.querySelector('.orden-input').value = i + 1;
                row.querySelector('.obligatorio').name = `etapas[${i}][obligatorio]`;
                const checkbox = row.querySelector('input[type="checkbox"]');
                checkbox.name = `etapas[${i}][obligatorio]`;
                row.querySelector('.subir').disabled = i === 0;
                row.querySelector('.bajar').disabled = i === list.children.length - 1;
            });
            empty.classList.toggle('hidden', list.children.length > 0);
        }
        document.getElementById('agregar-etapa').addEventListener('click', () => {
            list.appendChild(template.content.cloneNode(true)); renumber();
        });
        list.addEventListener('click', e => {
            const row = e.target.closest('.etapa'); if (!row) return;
            if (e.target.closest('.quitar')) row.remove();
            if (e.target.closest('.subir') && row.previousElementSibling) list.insertBefore(row, row.previousElementSibling);
            if (e.target.closest('.bajar') && row.nextElementSibling) list.insertBefore(row.nextElementSibling, row);
            renumber();
        });
        renumber();
    })();
    </script>
</x-app-layout>

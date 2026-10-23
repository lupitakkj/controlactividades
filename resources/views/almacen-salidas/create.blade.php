<x-app-layout>
    <div class="max-w-5xl mx-auto p-6 space-y-5">
        <a class="text-blue-700 underline" href="{{ route('almacen-salidas.index') }}">← Volver a salidas</a>
        <h1 class="text-2xl font-bold">Registrar salida de almacén</h1>
        @if($errors->any()) <div class="rounded-lg bg-red-50 p-3 text-red-800"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
        <form method="POST" action="{{ route('almacen-salidas.store') }}" class="space-y-5 rounded-xl border bg-white p-5">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                <label class="block text-sm font-medium md:col-span-2">Órdenes de trabajo / pedidos
                    <select name="pedido_ids[]" required multiple size="6" class="mt-1 w-full rounded-lg border-slate-300">
                        @foreach($pedidos as $pedido)<option value="{{ $pedido->id }}" @selected(in_array($pedido->id, old('pedido_ids', [])))>{{ $pedido->pedido_no }} — {{ $pedido->cliente }}</option>@endforeach
                    </select>
                    <span class="mt-1 block text-xs font-normal text-slate-500">Puedes seleccionar varias órdenes. En Windows, mantén presionada la tecla Ctrl mientras haces clic.</span>
                </label>
                <label class="block text-sm font-medium">Almacén
                    <select name="almacen_id" required id="almacen_id" class="mt-1 w-full rounded-lg border-slate-300">
                        <option value="">Seleccionar almacén</option>
                        @foreach($almacenes as $almacen)<option value="{{ $almacen->id }}" @selected(old('almacen_id')==$almacen->id)>{{ $almacen->nombre }}</option>@endforeach
                    </select>
                </label>
                <label class="block text-sm font-medium">Fecha y hora
                    <input type="datetime-local" name="fecha_salida" value="{{ old('fecha_salida', now()->format('Y-m-d\TH:i')) }}" required class="mt-1 w-full rounded-lg border-slate-300">
                </label>
                <label class="block text-sm font-medium">Recibe el material
                    <input name="recibido_por" value="{{ old('recibido_por') }}" required maxlength="180" class="mt-1 w-full rounded-lg border-slate-300">
                </label>
            </div>
            <div>
                <div class="flex items-center justify-between"><h2 class="font-semibold">Materiales disponibles</h2><button type="button" id="agregar" class="rounded-lg bg-slate-800 px-3 py-2 text-sm text-white">Agregar material</button></div>
                <p class="text-sm text-slate-500 mb-3">Las existencias se filtran por almacén y se validan nuevamente al guardar.</p>
                <div id="lineas" class="space-y-3"></div>
            </div>
            <label class="block text-sm font-medium">Observaciones
                <textarea name="observaciones" rows="3" class="mt-1 w-full rounded-lg border-slate-300">{{ old('observaciones') }}</textarea>
            </label>
            <button class="rounded-lg bg-blue-600 px-5 py-3 font-semibold text-white">Registrar salida</button>
        </form>
    </div>
    <script>
        const stocks = @json($materiales);
        const warehouse = document.getElementById('almacen_id');
        const lines = document.getElementById('lineas');
        function addLine() {
            const index = lines.children.length;
            const row = document.createElement('div');
            row.className = 'grid gap-2 md:grid-cols-[2fr_1fr_auto] rounded-lg border p-3';
            const select = document.createElement('select');
            select.name = `materiales[${index}][clave_material]`;
            select.required = true;
            select.className = 'rounded-lg border-slate-300';
            select.innerHTML = '<option value="">Seleccionar material</option>';
            stocks.filter(s => String(s.almacen_id) === warehouse.value).forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.clave_material;
                opt.textContent = `${s.clave_material} — ${s.descripcion} (Disponible: ${Number(s.existencia).toFixed(3)} ${s.unidad || ''})`;
                select.appendChild(opt);
            });
            const qty = document.createElement('input');
            qty.type = 'number'; qty.name = `materiales[${index}][cantidad]`; qty.min = '0.001'; qty.step = '0.001'; qty.required = true;
            qty.placeholder = 'Cantidad'; qty.className = 'rounded-lg border-slate-300';
            const remove = document.createElement('button');
            remove.type = 'button'; remove.textContent = 'Quitar'; remove.className = 'rounded-lg bg-red-100 px-3 text-red-800';
            remove.onclick = () => row.remove();
            row.append(select, qty, remove); lines.appendChild(row);
        }
        document.getElementById('agregar').addEventListener('click', addLine);
        warehouse.addEventListener('change', () => { lines.innerHTML = ''; addLine(); });
        addLine();
    </script>
</x-app-layout>

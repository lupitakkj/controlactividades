<x-app-layout>
    <div class="max-w-5xl mx-auto p-6 space-y-5 text-slate-900">
        <header class="flex flex-col gap-4 rounded-xl border border-slate-700 bg-slate-800 p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div class="flex min-w-0 items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-700 text-white" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <path d="M14 2v6h6M8 13h8M8 17h8M8 9h2"/>
                    </svg>
                </span>
                <div class="min-w-0">
                    <h1 class="text-xl font-bold leading-tight text-white sm:text-2xl">Registrar salida de almacén</h1>
                    <p class="mt-1 text-sm text-slate-300">Apartado y entrega de materiales</p>
                </div>
            </div>
            <a href="{{ route('almacen-salidas.index') }}"
               class="group inline-flex shrink-0 items-center justify-center gap-2 rounded-lg border border-slate-500 bg-transparent px-4 py-2.5 text-sm font-semibold text-slate-100 transition duration-200 hover:border-blue-400 hover:bg-slate-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-800"
               aria-label="Volver al listado de salidas de almacén">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:-translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M19 12H5"/>
                    <path d="m12 19-7-7 7-7"/>
                </svg>
                <span>Volver a salidas</span>
            </a>
        </header>
        @if($errors->any()) <div class="rounded-lg bg-red-50 p-3 text-red-800">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div> @endif
        <form method="POST" action="{{ route('almacen-salidas.store') }}" class="space-y-5 rounded-xl border border-slate-200 bg-white p-5 text-slate-900 shadow-sm">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2 space-y-2">
                    <label for="nombre_documento" class="block text-sm font-medium">Nombre del documento</label>
                    <input id="nombre_documento" name="nombre_documento" value="{{ old('nombre_documento') }}" required maxlength="180" placeholder="Ej. Salida de material para producción" class="mt-1 w-full rounded-lg border border-slate-300 bg-white text-slate-900 placeholder:text-slate-500 focus:border-blue-600 focus:ring-blue-600">
                    <span class="block text-xs font-normal text-slate-500">Este nombre se mostrará en el detalle y el historial de la salida.</span>
                </div>
                <div class="md:col-span-2 space-y-2">
                    <label for="buscar_pedido" class="block text-sm font-medium">Órdenes de trabajo / pedidos</label>
                    <input id="buscar_pedido" type="search" placeholder="Buscar por número o cliente..." class="w-full rounded-lg border border-slate-300 bg-white text-slate-900 placeholder:text-slate-500 focus:border-blue-600 focus:ring-blue-600">
                    <div id="lista_pedidos" hidden class="max-h-56 overflow-y-auto rounded-lg border border-slate-300 p-3 space-y-2">
                        @foreach($pedidos as $pedido)
                        <label class="pedido-opcion flex items-start gap-2 rounded-md p-2 hover:bg-slate-50" data-busqueda="{{ strtolower($pedido->pedido_no.' OP'.(ltrim((string) $pedido->pedido_no, '0') ?: '0').' '.$pedido->cliente) }}">
                            <input type="checkbox" name="pedido_ids[]" value="{{ $pedido->id }}" class="mt-1 rounded border-slate-300" @checked(in_array($pedido->id, old('pedido_ids', [])))>
                            <span><strong>{{ 'OP' . (ltrim((string) $pedido->pedido_no, '0') ?: '0') }}</strong><span class="block text-sm text-slate-500">{{ $pedido->cliente }}</span></span>
                        </label>
                        @endforeach
                    </div>
                    <p id="contador_pedidos" class="text-xs text-slate-500">0 órdenes seleccionadas</p>
                    <div id="pedidos_seleccionados" class="flex flex-wrap gap-2"></div>
                    <span class="block text-xs font-normal text-slate-500">Busca y marca órdenes existentes o agrega una referencia manual si la OP no existe.</span>
                    <div class="mt-3 rounded-lg border border-dashed border-slate-300 p-3 space-y-2">
                        <p class="text-sm font-semibold">Agregar OP o pedido manual</p>
                        <div class="grid gap-2 md:grid-cols-2">
                            <input id="referencia_numero" type="text" maxlength="100" placeholder="Número o nombre de OP (ej. OP700)" class="rounded-lg border border-slate-300 bg-white text-slate-900 placeholder:text-slate-500 focus:border-blue-600 focus:ring-blue-600">
                            <input id="referencia_nombre" type="text" maxlength="180" placeholder="Cliente o descripción (opcional)" class="rounded-lg border border-slate-300 bg-white text-slate-900 placeholder:text-slate-500 focus:border-blue-600 focus:ring-blue-600">
                        </div>
                        <button type="button" id="agregar_referencia" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">Agregar referencia manual</button>
                        <div id="referencias_manuales_lista" class="space-y-2"></div>
                    </div>
                </div>
                <label class="block text-sm font-medium">Almacén
                    <select name="almacen_id" required id="almacen_id" class="mt-1 w-full rounded-lg border border-slate-300 bg-white text-slate-900 placeholder:text-slate-500 focus:border-blue-600 focus:ring-blue-600">
                        <option value="">Seleccionar almacén</option>
                        @foreach($almacenes as $almacen)<option value="{{ $almacen->id }}" @selected(old('almacen_id')==$almacen->id)>{{ $almacen->nombre }}</option>@endforeach
                    </select>
                </label>
                <label class="block text-sm font-medium">Fecha y hora
                    <input type="datetime-local" name="fecha_salida" value="{{ old('fecha_salida', now()->format('Y-m-d\TH:i')) }}" required class="mt-1 w-full rounded-lg border border-slate-300 bg-white text-slate-900 placeholder:text-slate-500 focus:border-blue-600 focus:ring-blue-600">
                </label>
                <label class="block text-sm font-medium">Recibe el material
                    <input name="recibido_por" value="{{ old('recibido_por') }}" required maxlength="180" class="mt-1 w-full rounded-lg border border-slate-300 bg-white text-slate-900 placeholder:text-slate-500 focus:border-blue-600 focus:ring-blue-600">
                </label>
            </div>
            <div>
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold">Materiales disponibles</h2><button type="button" id="agregar" class="rounded-lg bg-slate-800 px-3 py-2 text-sm text-white">Agregar material</button>
                </div>
                <p class="text-sm text-slate-500 mb-3">Las existencias se filtran por almacén y se validan nuevamente al guardar.</p>
                <div id="lineas" class="space-y-3"></div>
            </div>
            <label class="block text-sm font-medium">Observaciones
                <textarea name="observaciones" rows="3" class="mt-1 w-full rounded-lg border border-slate-300 bg-white text-slate-900 placeholder:text-slate-500 focus:border-blue-600 focus:ring-blue-600">{{ old('observaciones') }}</textarea>
            </label>
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900">
                <strong>Importante:</strong> al guardar, el material quedará apartado y reservado. El inventario se descontará como salida registrada cuando Almacén confirme la entrega.
            </div>
            <button class="rounded-lg bg-amber-600 px-5 py-3 font-semibold text-white transition hover:bg-amber-700">Apartar materiales</button>
        </form>
    </div>
    <script>
        const buscadorPedidos = document.getElementById('buscar_pedido');
        const opcionesPedidos = [...document.querySelectorAll('.pedido-opcion')];
        const contadorPedidos = document.getElementById('contador_pedidos');
        const listaPedidos = document.getElementById('lista_pedidos');
        const seleccionadosPedidos = document.getElementById('pedidos_seleccionados');

        // Usa display inline para que las clases flex de Tailwind no anulen el ocultamiento.
        listaPedidos.style.display = 'none';

        function mostrarPedidosSeleccionados() {
            seleccionadosPedidos.innerHTML = '';
            const marcados = document.querySelectorAll('input[name="pedido_ids[]"]:checked');

            marcados.forEach(checkbox => {
                const etiqueta = checkbox.closest('.pedido-opcion');
                if (!etiqueta) return;

                const nombre = etiqueta.querySelector('strong')?.textContent.trim() || 'Orden';
                const cliente = etiqueta.querySelector('.text-slate-500')?.textContent.trim() || '';
                const chip = document.createElement('span');
                chip.className = 'inline-flex max-w-full items-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-sm text-blue-800';

                const texto = document.createElement('span');
                texto.className = 'break-words';
                texto.textContent = cliente ? `${nombre} — ${cliente}` : nombre;

                const quitar = document.createElement('button');
                quitar.type = 'button';
                quitar.textContent = '×';
                quitar.className = 'shrink-0 font-bold text-blue-800 hover:text-red-700';
                quitar.setAttribute('aria-label', `Quitar ${nombre}`);
                quitar.addEventListener('click', () => {
                    checkbox.checked = false;
                    actualizarPedidos();
                });

                chip.append(texto, quitar);
                seleccionadosPedidos.appendChild(chip);
            });
        }

        function actualizarPedidos() {
            const texto = buscadorPedidos.value.trim().toLowerCase();
            let coincidencias = 0;

            opcionesPedidos.forEach(op => {
                const coincide = texto.length > 0 &&
                    (op.dataset.busqueda || '').toLowerCase().includes(texto);

                // Evita conflictos entre el atributo hidden y las clases flex de Tailwind.
                op.style.display = coincide ? 'flex' : 'none';
                if (coincide) coincidencias++;
            });

            // Mostrar resultados solo cuando el usuario escribe.
            listaPedidos.style.display = texto.length > 0 ? 'block' : 'none';

            const total = document.querySelectorAll('input[name="pedido_ids[]"]:checked').length;
            contadorPedidos.textContent = `${total} ${total === 1 ? 'orden seleccionada' : 'órdenes seleccionadas'}`;
            mostrarPedidosSeleccionados();

            let aviso = document.getElementById('sin_pedidos');
            if (!aviso) {
                aviso = document.createElement('p');
                aviso.id = 'sin_pedidos';
                aviso.className = 'p-3 text-sm text-slate-500';
                listaPedidos.appendChild(aviso);
            }

            aviso.style.display = texto.length > 0 && coincidencias === 0 ? 'block' : 'none';
            aviso.textContent = 'No se encontraron órdenes existentes. Puedes agregar una referencia manual abajo.';
        }

        buscadorPedidos.addEventListener('input', actualizarPedidos);

        // Al seleccionar una OP, limpiar el buscador y ocul
        // 
        tar los resultados.
        // La orden permanece marcada y visible en la sección de seleccionadas.
        document.querySelectorAll('input[name="pedido_ids[]"]').forEach(cb => {
            cb.addEventListener('change', () => {
                if (cb.checked) {
                    buscadorPedidos.value = '';
                }
                actualizarPedidos();
            });
        });

        actualizarPedidos();

        const referenciasManual = document.getElementById('referencias_manuales_lista');
        document.getElementById('agregar_referencia').addEventListener('click', () => {
            const numeroInput = document.getElementById('referencia_numero');
            const nombreInput = document.getElementById('referencia_nombre');
            const numero = numeroInput.value.trim();
            const nombre = nombreInput.value.trim();
            if (!numero) {
                numeroInput.focus();
                alert('Escribe el número o nombre de la OP/pedido.');
                return;
            }
            const fila = document.createElement('div');
            fila.className = 'flex items-start justify-between gap-3 rounded-md bg-slate-50 p-2';
            const datos = document.createElement('div');
            datos.className = 'text-sm';
            const titulo = document.createElement('strong');
            titulo.textContent = numero;
            datos.appendChild(titulo);
            if (nombre) {
                const descripcion = document.createElement('span');
                descripcion.className = 'block text-slate-500';
                descripcion.textContent = nombre;
                datos.appendChild(descripcion);
            }
            const numeroHidden = document.createElement('input');
            numeroHidden.type = 'hidden';
            numeroHidden.name = `referencias_manuales[${referenciasManual.children.length}][numero]`;
            numeroHidden.value = numero;
            const nombreHidden = document.createElement('input');
            nombreHidden.type = 'hidden';
            nombreHidden.name = `referencias_manuales[${referenciasManual.children.length}][nombre]`;
            nombreHidden.value = nombre;
            const quitar = document.createElement('button');
            quitar.type = 'button';
            quitar.textContent = 'Quitar';
            quitar.className = 'text-sm text-red-700';
            quitar.addEventListener('click', () => {
                fila.remove();
                [...referenciasManual.children].forEach((item, i) => {
                    item.querySelectorAll('input[type="hidden"]')[0].name = `referencias_manuales[${i}][numero]`;
                    item.querySelectorAll('input[type="hidden"]')[1].name = `referencias_manuales[${i}][nombre]`;
                });
            });
            fila.append(datos, quitar, numeroHidden, nombreHidden);
            referenciasManual.appendChild(fila);
            numeroInput.value = '';
            nombreInput.value = '';
            numeroInput.focus();
        });

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
            qty.type = 'number';
            qty.name = `materiales[${index}][cantidad]`;
            qty.min = '0.001';
            qty.step = '0.001';
            qty.required = true;
            qty.placeholder = 'Cantidad';
            qty.className = 'rounded-lg border-slate-300';
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.textContent = 'Quitar';
            remove.className = 'rounded-lg bg-red-100 px-3 text-red-800';
            remove.onclick = () => row.remove();
            row.append(select, qty, remove);
            lines.appendChild(row);
        }
        document.getElementById('agregar').addEventListener('click', addLine);
        warehouse.addEventListener('change', () => {
            lines.innerHTML = '';
            addLine();
        });
        addLine();
    </script>
</x-app-layout>

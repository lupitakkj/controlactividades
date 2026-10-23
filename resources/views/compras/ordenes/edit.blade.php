@php
    $detallesIniciales = old('detalles');
    if (!is_array($detallesIniciales)) {
        $detallesIniciales = $orden->detalles->map(function ($d) {
            return [
                'clave_material' => $d->clave_material,
                'descripcion' => $d->descripcion,
                'observaciones' => $d->observaciones,
                'unidad' => $d->unidad,
                'cantidad_solicitada' => $d->cantidad_solicitada,
                'precio_unitario' => $d->precio_unitario,
            ];
        })->values()->all();
    }
@endphp

<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-xl bg-slate-800 border border-slate-700 p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-white"><i class="fa-solid fa-file-invoice-dollar mr-2"></i>Editar orden de compra</h1>
                    <p class="mt-1 text-sm text-slate-300">Modifica los datos y productos de la orden. Solo se permite editar antes de registrar recepciones.</p>
                </div>
                <a href="{{ route('compras.ordenes.index') }}" class="inline-flex justify-center rounded-lg border border-slate-500 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-700">Volver</a>
            </div>
            @if(session('success'))<div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-green-800">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-red-800"><strong>Revisa los siguientes datos:</strong>
                <ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>@endif
            <form method="POST"
                action="{{ route('compras.ordenes.update', $orden) }}"
                id="formOrden"
                class="space-y-6">
                @csrf
                @method('PUT')
                <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                        <h2 class="font-semibold text-slate-800">Datos generales</h2>
                    </div>
                    <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 lg:grid-cols-4">
                        <label class="text-sm font-medium text-slate-700">Folio de orden *<input name="folio" value="{{ old('folio', $orden->folio) }}" required maxlength="80" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"></label>
                        <label class="text-sm font-medium text-slate-700 sm:col-span-2">Proveedor *<select id="proveedor_id" name="proveedor_id" required class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Selecciona proveedor</option>@foreach($proveedores as $p)<option value="{{ $p->id }}" @selected(old('proveedor_id', $orden->proveedor_id)==$p->id)>{{ $p->razon_social }}{{ $p->rfc ? ' — '.$p->rfc : '' }}</option>@endforeach
                            </select><span class="mt-1 block text-xs text-slate-500">Si no existe, regístralo primero en Proveedores.</span></label>
                        <label class="text-sm font-medium text-slate-700">Fecha<input type="date" name="fecha_orden" value="{{ old('fecha_orden', optional($orden->fecha_orden)->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"></label>
                        <label class="text-sm font-medium text-slate-700">Moneda<select name="moneda" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="MXN" @selected(old('moneda', $orden->moneda)==='MXN' )>MXN</option>
                                <option value="USD" @selected(old('moneda', $orden->moneda)==='USD' )>USD</option>
                            </select></label>
                        <label class="text-sm font-medium text-slate-700">IVA total<input type="number" min="0" step="0.01" name="iva" value="{{ old('iva', $orden->iva) }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"></label>
                        <label class="text-sm font-medium text-slate-700 sm:col-span-2 lg:col-span-4">Observaciones<textarea name="observaciones" rows="2" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('observaciones', $orden->observaciones) }}</textarea></label>
                    </div>
                </section>
                <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-col gap-3 border-b border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <h2 class="font-semibold text-slate-800">Productos solicitados</h2><button type="button" id="agregarRenglon" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"> <i class="fa-solid fa-plus mr-1"></i>Agregar producto</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm" id="tablaDetalle">
                            <thead class="bg-slate-100 text-slate-700">
                                <tr>
                                    <th class="px-3 py-3 text-left">Clave</th>
                                    <th class="px-3 py-3 text-left">Descripción *</th>
                                    <th class="px-3 py-3 text-left">Observaciones del producto</th>
                                    <th class="px-3 py-3 text-left">Unidad</th>
                                    <th class="px-3 py-3 text-left">Cantidad *</th>
                                    <th class="px-3 py-3 text-left">Precio unitario *</th>
                                    <th class="px-3 py-3 text-right">Importe</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="renglones" class="divide-y divide-slate-200"></tbody>
                            <tfoot class="bg-slate-50">
                                <tr>
                                    <th colspan="6" class="px-3 py-3 text-right text-slate-700">Subtotal calculado</th>
                                    <th id="subtotalVista" class="px-3 py-3 text-right font-semibold text-slate-900">$0.00</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th colspan="6" class="px-3 py-3 text-right text-slate-700">Total con IVA</th>
                                    <th id="totalVista" class="px-3 py-3 text-right font-bold text-slate-900">$0.00</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>
                <div class="flex flex-wrap gap-3"><button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"><i class="fa-solid fa-floppy-disk mr-2"></i>Guardar cambios</button><a href="{{ route('compras.ordenes.index') }}" class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancelar</a></div>
            </form>
        </div>
    </div>
    <script>
        (function() {
            const body = document.getElementById('renglones');
            let idx = 0;
            const currency = () => document.querySelector('[name=moneda]').value || 'MXN';
            const money = n => new Intl.NumberFormat('es-MX', {
                style: 'currency',
                currency: currency()
            }).format(n || 0);

            function addRow(data = {}) {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td class="p-2"><input class="w-32 rounded-lg border-slate-300 text-sm" name="detalles[${idx}][clave_material]" maxlength="100"></td>
        <td class="p-2"><input class="w-64 rounded-lg border-slate-300 text-sm" name="detalles[${idx}][descripcion]" required maxlength="500"></td>
        <td class="p-2"><textarea class="w-72 rounded-lg border-slate-300 text-sm" name="detalles[${idx}][observaciones]" rows="2" maxlength="1000" placeholder="Especificaciones o instrucciones del producto"></textarea></td>
        <td class="p-2"><input class="w-24 rounded-lg border-slate-300 text-sm" name="detalles[${idx}][unidad]" maxlength="40"></td>
        <td class="p-2"><input class="qty w-32 rounded-lg border-slate-300 text-sm" type="number" min="0.001" step="0.001" name="detalles[${idx}][cantidad_solicitada]" required></td>
        <td class="p-2"><input class="price w-36 rounded-lg border-slate-300 text-sm" type="number" min="0" step="0.000001" name="detalles[${idx}][precio_unitario]" required></td>
        <td class="importe whitespace-nowrap p-2 text-right font-medium text-slate-800">$0.00</td>
        <td class="p-2 text-center"><button type="button" class="quitar rounded-md px-2 py-1 font-bold text-red-600 hover:bg-red-50" aria-label="Quitar renglón">×</button></td>`;
                body.appendChild(tr);
                ['clave_material', 'descripcion', 'observaciones', 'unidad', 'cantidad_solicitada', 'precio_unitario'].forEach(k => {
                    if (data[k] !== undefined && data[k] !== null) tr.querySelector(`[name="detalles[${idx}][${k}]"]`).value = data[k];
                });
                idx++;
                tr.querySelector('.quitar').onclick = () => {
                    if (body.children.length > 1) {
                        tr.remove();
                        calc();
                    }
                };
                tr.querySelectorAll('input').forEach(el => el.addEventListener('input', calc));
                calc();
            }

            function calc() {
                let subtotal = 0;
                body.querySelectorAll('tr').forEach(tr => {
                    const q = parseFloat(tr.querySelector('.qty').value) || 0;
                    const p = parseFloat(tr.querySelector('.price').value) || 0;
                    const importe = q * p;
                    subtotal += importe;
                    tr.querySelector('.importe').textContent = money(importe);
                });
                document.getElementById('subtotalVista').textContent = money(subtotal);
                document.getElementById('totalVista').textContent = money(subtotal + (parseFloat(document.querySelector('[name=iva]').value) || 0));
            }

            function setValue(selector, value) {
                const el = document.querySelector(selector);
                if (el && value !== null && value !== undefined && value !== '') el.value = value;
            }

            document.getElementById('agregarRenglon').onclick = () => addRow();
            document.querySelector('[name=iva]').addEventListener('input', calc);
            document.querySelector('[name=moneda]').addEventListener('change', calc);
            const detallesIniciales = @json($detallesIniciales);
            if (detallesIniciales.length) detallesIniciales.forEach(d => addRow(d));
            else addRow();
        })();
    </script>
</x-app-layout>
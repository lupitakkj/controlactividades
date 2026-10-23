<x-app-layout>
    <div class="min-h-screen bg-slate-900 py-5">
        <div class="mx-auto max-w-[1600px] space-y-4 px-4 sm:px-6 lg:px-8">
            <section class="relative overflow-hidden rounded-2xl border border-slate-700 bg-gradient-to-br from-slate-800 via-slate-800 to-blue-950 px-5 py-4 text-white shadow-xl sm:px-6 sm:py-5">
                <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-blue-500/10 blur-2xl"></div>
                <div class="relative flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-600 shadow-lg shadow-blue-950/40">
                            <i class="fa-solid fa-file-invoice-dollar text-xl"></i>
                        </div>
                        <div>
                            <p class="mb-0.5 text-[10px] font-bold uppercase tracking-[0.18em] text-blue-300">Compras · Reportes</p>
                            <h1 class="text-xl font-bold tracking-tight sm:text-2xl">Reporte de entradas</h1>
                            <p class="mt-1 max-w-2xl text-xs leading-5 text-slate-300">Consulta las recepciones registradas, filtra por fecha, almacén o proveedor y exporta la información a Excel.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-xs text-slate-200">
                        <i class="fa-solid fa-database text-blue-300"></i><span>Registro de recepciones</span>
                    </div>
                </div>
            </section>

            @if ($errors->any())
                <div class="rounded-xl border border-red-300 bg-red-50 p-4 text-red-800 shadow-sm">
                    <div class="mb-2 flex items-center gap-2 font-semibold"><i class="fa-solid fa-circle-exclamation"></i> Revisa los siguientes datos</div>
                    <ul class="list-disc space-y-1 pl-5 text-sm">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg shadow-slate-950/10">
                <div class="flex items-center gap-3 border-b border-slate-200 bg-slate-50 px-4 py-3 sm:px-5">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-100 text-blue-700"><i class="fa-solid fa-sliders"></i></div>
                    <div><h2 class="text-sm font-bold text-slate-800">Filtros de búsqueda</h2><p class="text-xs text-slate-500">Consulta por fecha, almacén o proveedor.</p></div>
                </div>
                <div class="px-4 py-4 sm:px-5 sm:py-4">
                    <form method="GET" action="{{ route('compras.reportes.entradas.index') }}" class="space-y-0">
                        <div class="grid grid-cols-1 items-end gap-3 sm:grid-cols-2 xl:grid-cols-6">
                            <label class="block min-w-0 text-sm font-semibold text-slate-700">
                                <span class="mb-2 flex items-center gap-2"><i class="fa-regular fa-calendar text-slate-400"></i> Fecha inicial</span>
                                <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="w-full min-w-0 rounded-xl border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </label>
                            <label class="block min-w-0 text-sm font-semibold text-slate-700">
                                <span class="mb-2 flex items-center gap-2"><i class="fa-regular fa-calendar-check text-slate-400"></i> Fecha final</span>
                                <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" class="w-full min-w-0 rounded-xl border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </label>
                            <label class="block min-w-0 text-sm font-semibold text-slate-700">
                                <span class="mb-2 flex items-center gap-2"><i class="fa-solid fa-warehouse text-slate-400"></i> Almacén</span>
                                <select name="almacen_id" class="w-full min-w-0 rounded-xl border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Todos los almacenes</option>
                                    @foreach ($almacenes as $almacen)
                                        <option value="{{ $almacen->id }}" @selected((string)request('almacen_id') === (string)$almacen->id)>{{ $almacen->clave }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="block min-w-0 text-sm font-semibold text-slate-700">
                                <span class="mb-2 flex items-center gap-2"><i class="fa-solid fa-truck-field text-slate-400"></i> Proveedor</span>
                                <select name="proveedor_id" class="w-full min-w-0 rounded-xl border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Todos los proveedores</option>
                                    @foreach ($proveedores as $proveedor)
                                        <option value="{{ $proveedor->id }}" @selected((string)request('proveedor_id') === (string)$proveedor->id)>{{ $proveedor->razon_social }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <div class="flex min-w-0 items-end">
                                <a href="{{ route('compras.reportes.entradas.index') }}" class="inline-flex min-h-11 w-full items-center justify-center gap-2 whitespace-nowrap rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                                    <i class="fa-solid fa-rotate-left"></i> Limpiar
                                </a>
                            </div>
                            <div class="flex min-w-0 items-end">
                                <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-blue-700 px-3 py-3 text-sm font-bold text-white shadow-md shadow-blue-900/20 transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <i class="fa-solid fa-filter"></i> Aplicar
                                </button>
                            </div>
                        </div>
                    </form>

                    <form method="GET" action="{{ route('compras.reportes.entradas.exportar') }}" id="form-exportar-entradas" class="hidden">
                        <input type="hidden" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
                        <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin') }}">
                        <input type="hidden" name="almacen_id" value="{{ request('almacen_id') }}">
                        <input type="hidden" name="proveedor_id" value="{{ request('proveedor_id') }}">
                    </form>
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg shadow-slate-950/10">
                <div class="flex flex-col gap-4 border-b border-slate-200 bg-slate-50 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div class="flex items-start gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700"><i class="fa-solid fa-list-check text-lg"></i></div>
                        <div><h2 class="text-lg font-bold text-slate-800">Recepciones registradas</h2><p class="mt-1 text-sm text-slate-500">Cada fila representa una recepción de mercancía.</p></div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="inline-flex w-fit items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-4 py-2 text-sm font-bold text-blue-800"><i class="fa-solid fa-chart-simple"></i>{{ number_format($recepciones->total()) }} {{ $recepciones->total() === 1 ? 'resultado' : 'resultados' }}</div>
                        <button type="submit" form="form-exportar-entradas" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                            <i class="fa-solid fa-file-excel"></i><span class="whitespace-nowrap">Exportar Excel</span>
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-100">
                            <tr class="text-xs font-bold uppercase tracking-wide text-slate-600">
                                <th class="whitespace-nowrap px-5 py-4 text-left">Recepción</th>
                                <th class="whitespace-nowrap px-5 py-4 text-left">Fecha y hora</th>
                                <th class="whitespace-nowrap px-5 py-4 text-left">Orden de compra</th>
                                <th class="min-w-52 px-5 py-4 text-left">Proveedor</th>
                                <th class="whitespace-nowrap px-5 py-4 text-left">Almacén</th>
                                <th class="whitespace-nowrap px-5 py-4 text-right">Líneas</th>
                                <th class="whitespace-nowrap px-5 py-4 text-right">Cantidad recibida</th>
                                <th class="min-w-52 px-5 py-4 text-left">Recibió</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($recepciones as $recepcion)
                                <tr class="transition hover:bg-blue-50/60">
                                    <td class="whitespace-nowrap px-5 py-4"><span class="inline-flex items-center rounded-lg bg-slate-100 px-3 py-1.5 font-bold text-slate-700">#{{ $recepcion->recepcion_id }}</span></td>
                                    <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $recepcion->fecha_recepcion ? \Illuminate\Support\Carbon::parse($recepcion->fecha_recepcion)->format('d/m/Y H:i') : '—' }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 font-semibold text-blue-800">{{ $recepcion->orden_folio ?: '—' }}</td>
                                    <td class="px-5 py-4 font-medium text-slate-800">{{ $recepcion->proveedor_nombre ?: '—' }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 text-slate-600"><span class="inline-flex items-center gap-2"><i class="fa-solid fa-warehouse text-slate-400"></i>{{ $recepcion->almacen_clave ?: 'Sin clave' }}</span></td>
                                    <td class="px-5 py-4 text-right tabular-nums text-slate-700">{{ number_format((float)$recepcion->cantidad_lineas) }}</td>
                                    <td class="px-5 py-4 text-right font-bold tabular-nums text-slate-800">{{ number_format((float)$recepcion->cantidad_recibida_total, 3) }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $recepcion->usuario_nombre ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="px-6 py-14 text-center">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><i class="fa-regular fa-folder-open text-2xl"></i></div>
                                    <p class="mt-4 font-bold text-slate-700">No se encontraron recepciones</p>
                                    <p class="mx-auto mt-1 max-w-lg text-sm text-slate-500">Prueba con otros filtros. Si una orden aparece como recibida, verifica que exista un registro en compras_recepciones.</p>
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($recepciones->hasPages())
                    <div class="border-t border-slate-200 bg-slate-50 px-5 py-4 sm:px-6">{{ $recepciones->links() }}</div>
                @endif
            </section>


        </div>
    </div>
</x-app-layout>

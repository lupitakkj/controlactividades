<x-app-layout>


    <div class="py-6">

        <div class="w-full max-w-[1800px] mx-auto px-4">

            {{-- ENCABEZADO --}}
            <div class="bg-white overflow-visible shadow-sm sm:rounded-2xl mb-5 relative z-50 border border-slate-100">

                <div class="p-4 sm:p-5">

                    @php
                        // Indicadores calculados con los pedidos ya cargados en esta vista.
                        $totalPedidosVista = $pedidos->count();
                        $pedidosEnProcesoVista = $pedidos->filter(function ($pedido) {
                            $estado = mb_strtolower(trim((string) ($pedido->controlOperativo?->estado_operativo ?? 'Por Iniciar')));
                            return $estado === 'en proceso';
                        })->count();
                        $pedidosTerminadosVista = $pedidos->filter(function ($pedido) {
                            $estado = mb_strtolower(trim((string) ($pedido->controlOperativo?->estado_operativo ?? 'Por Iniciar')));
                            return $estado === 'terminado';
                        })->count();
                        $pedidosAtrasadosVista = $pedidos->filter(function ($pedido) {
                            $estado = mb_strtolower(trim((string) ($pedido->controlOperativo?->estado_operativo ?? 'Por Iniciar')));
                            $fechaEntrega = $pedido->fecha_entrega ?? null;
                            if (!$fechaEntrega || in_array($estado, ['terminado', 'cancelado'], true)) {
                                return false;
                            }
                            try {
                                return \Illuminate\Support\Carbon::parse($fechaEntrega)->startOfDay()->lt(now()->startOfDay());
                            } catch (\Throwable $e) {
                                return false;
                            }
                        })->count();
                    @endphp

                    <div class="flex flex-col 2xl:flex-row 2xl:items-center gap-2">

                        {{-- TÍTULO --}}
                        <div class="flex items-center gap-2 min-w-[220px] 2xl:w-[255px] shrink-0">
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600 border border-blue-100 shrink-0">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <rect x="3.5" y="5" width="17" height="16" rx="2.5"></rect>
                                    <path d="M8 3v4M16 3v4M4 9h16M8 13h3v3H8z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold tracking-tight text-slate-900">Control Operativo</h3>
                                <p class="text-xs text-slate-500 mt-1">Seguimiento de pedidos y producción.</p>
                            </div>
                        </div>

                        {{-- INDICADORES --}}
                        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-1.5 flex-1 min-w-0">
                            <div class="flex min-h-[54px] items-center gap-2 rounded-lg px-2 py-1.5 text-white shadow-sm" style="background:linear-gradient(135deg,#ff9417,#ff7b00)">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-white/20 text-base">✚</span>
                                <div class="min-w-0"><div class="text-[10px] font-semibold leading-tight">Pedidos activos</div><div class="text-xl font-bold leading-tight">{{ $pedidosActivos->count() }}</div></div>
                            </div>
                            <div class="flex min-h-[54px] items-center gap-2 rounded-lg px-2 py-1.5 text-white shadow-sm" style="background:linear-gradient(135deg,#08b8f0,#009fe3)">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-white/20 text-base">◉</span>
                                <div class="min-w-0"><div class="text-[10px] font-semibold leading-tight">Avance global</div><div class="text-xl font-bold leading-tight">{{ number_format($avanceGlobal * 100, 0) }}%</div></div>
                            </div>
                            <div class="flex min-h-[54px] items-center gap-2 rounded-lg px-2 py-1.5 text-white shadow-sm" style="background:linear-gradient(135deg,#8b5cf6,#7048df)">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-white/20 text-base">▣</span>
                                <div class="min-w-0"><div class="text-[10px] font-semibold leading-tight">En proceso</div><div class="text-xl font-bold leading-tight">{{ $pedidosEnProcesoVista }}</div></div>
                            </div>
                            <div class="flex min-h-[54px] items-center gap-2 rounded-lg px-2 py-1.5 text-white shadow-sm" style="background:linear-gradient(135deg,#10b981,#059669)">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-white/20 text-base">✓</span>
                                <div class="min-w-0"><div class="text-[10px] font-semibold leading-tight">Terminados</div><div class="text-xl font-bold leading-tight">{{ $pedidosTerminadosVista }}</div></div>
                            </div>
                            <div class="flex min-h-[54px] items-center gap-2 rounded-lg px-2 py-1.5 text-white shadow-sm" style="background:linear-gradient(135deg,#fb4554,#ef233c)">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-white/20 text-base">⚠</span>
                                <div class="min-w-0"><div class="text-[10px] font-semibold leading-tight">Atrasados</div><div class="text-xl font-bold leading-tight">{{ $pedidosAtrasadosVista }}</div></div>
                            </div>
                        </div>

                    <div class="mt-0 flex flex-wrap 2xl:flex-nowrap items-center justify-end gap-2 2xl:ml-1 border-t border-slate-100 2xl:border-t-0 pt-2 2xl:pt-0 shrink-0">
                        {{-- FILTRO DE AVANCE --}}
                        <div class="flex items-center gap-2 whitespace-nowrap">

                            <label
                                for="filtroAvance"
                                class="text-sm font-medium text-gray-700">
                                Avance:
                            </label>

                            <select
                                id="filtroAvance"
                                style="appearance: auto; -webkit-appearance: menulist; background-image: none; padding-right: 1.75rem;"
                                class="border border-gray-300 rounded-lg px-2.5 py-1.5
                           text-xs bg-white
                           focus:ring-blue-500 focus:border-blue-500">

                                <option value="todos">
                                    Todos
                                </option>

                                <option value="pendientes">
                                    Pendientes
                                </option>

                                <option value="terminados">
                                    Terminados
                                </option>

                                <option value="sin-avance">
                                    Sin avance
                                </option>

                                <option value="en-proceso">
                                    En proceso
                                </option>

                            </select>



                        </div>

                        {{-- CONTROLES DE COLUMNAS; el contador dinámico conserva solo los pedidos filtrados --}}
                        <div class="flex items-center gap-2">

                            {{-- BOTÓN COLUMNAS --}}
                            <div class="relative">

                                <button
                                    type="button"
                                    id="btnColumnas"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5
                               bg-gray-100 hover:bg-gray-200
                               text-gray-700 text-xs font-medium
                               rounded-lg border border-gray-300 whitespace-nowrap">

                                    ⚙️ Columnas

                                </button>

                                <span id="columnasOcultasResumen" class="hidden ml-1 rounded-full bg-amber-100 px-2 py-1 text-[10px] font-semibold text-amber-800" aria-live="polite"></span>


                                {{-- PANEL DE COLUMNAS --}}
                                <div
                                    id="panelColumnas"
                                    class="hidden absolute right-0 mt-2 w-72
                               bg-white border border-gray-200
                               rounded-lg shadow-xl z-[1000]">

                                    <div class="p-4">

                                        <div class="flex items-center justify-between mb-4">

                                            <h4 class="font-semibold text-gray-800">
                                                Mostrar columnas
                                            </h4>

                                            <button
                                                type="button"
                                                id="cerrarColumnas"
                                                class="text-gray-400 hover:text-gray-700 text-lg">

                                                ✕

                                            </button>

                                        </div>

                                        <div class="mb-3 flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                                            <span class="text-xs text-slate-500">Las columnas ocultas no aparecen en la tabla.</span>
                                            <button type="button" id="restaurarColumnas" class="whitespace-nowrap text-xs font-semibold text-blue-600 hover:text-blue-800">Mostrar todas</button>
                                        </div>


                                        <div class="space-y-3">

                                            {{-- PEDIDO --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="pedido"
                                                    checked>

                                                <span class="text-sm text-gray-700">
                                                    Pedido
                                                </span>

                                            </label>


                                            {{-- CLIENTE --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="cliente"
                                                    checked>

                                                <span class="text-sm text-gray-700">
                                                    Cliente
                                                </span>

                                            </label>


                                            {{-- SU PEDIDO --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="su-pedido"
                                                    checked>

                                                <span class="text-sm text-gray-700">
                                                    Su Pedido
                                                </span>

                                            </label>


                                            {{-- ENVIAR A --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="enviar-a"
                                                    checked>

                                                <span class="text-sm text-gray-700">
                                                    Enviar a
                                                </span>

                                            </label>
                                            {{-- FECHA ENTREGA --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="fecha-entrega"
                                                    checked>

                                                <span class="text-sm text-gray-700">
                                                    Fecha Entrega
                                                </span>

                                            </label>


                                            {{-- ABASTECIMIENTO --}}
                                            <label class="flex items-center gap-3 cursor-pointer">
                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="abastecimiento"
                                                    checked>
                                                <span class="text-sm text-gray-700">Abastecimiento</span>
                                            </label>


                                            {{-- ESTADO OPERATIVO --}}
                                            <label class="flex items-center gap-3 cursor-pointer">
                                                <input type="checkbox" class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500" style="appearance: auto; -webkit-appearance: checkbox;" data-columna="estado-operativo" checked>
                                                <span class="text-sm text-gray-700">Estado operativo</span>
                                            </label>

                                            {{-- FECHA PRODUCCIÓN --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="fecha-produccion"
                                                    checked>

                                                <span class="text-sm text-gray-700">
                                                    Fecha Producción
                                                </span>

                                            </label>


                                            {{-- AVANCE --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="avance"
                                                    checked>

                                                <span class="text-sm text-gray-700">
                                                    Avance
                                                </span>

                                            </label>

                                            {{-- PROCESO CON MENOR AVANCE --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="proceso-menor-avance"
                                                    checked>

                                                <span class="text-sm text-gray-700">
                                                    Proceso con menor avance
                                                </span>

                                            </label>


                                            {{-- ENTREGA DE INFORMACIÓN --}}
                                            <label class="flex items-center gap-3 cursor-pointer">
                                                <input type="checkbox" class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500" style="appearance: auto; -webkit-appearance: checkbox;" data-columna="resultado-entrega" checked>
                                                <span class="text-sm text-gray-700">Entrega de información</span>
                                            </label>

                                            {{-- SEMÁFORO --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="semaforo"
                                                    checked>

                                                <span class="text-sm text-gray-700">
                                                    Semáforo
                                                </span>

                                            </label>


                                            {{-- DÍAS --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="dias"
                                                    checked>

                                                <span class="text-sm text-gray-700">
                                                    Días
                                                </span>

                                            </label>


                                            {{-- ACTIVIDADES --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="comentario"
                                                    checked>

                                                <span class="text-sm text-gray-700">
                                                    Actividades
                                                </span>

                                            </label>

                                        </div>


                                        {{-- BOTÓN APLICAR --}}
                                        <div class="mt-5 pt-4 border-t border-gray-200">

                                            <button
                                                type="button"
                                                id="aplicarColumnas"
                                                class="w-full px-4 py-2
                                           bg-blue-600 hover:bg-blue-700
                                           text-white text-sm font-medium
                                           rounded-lg">

                                                Aplicar

                                            </button>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- CALENDARIO DE PRODUCCIÓN --}}
            <section id="panelCalendarioProduccion" class="bg-white shadow-sm sm:rounded-2xl mb-4 border border-slate-100 overflow-hidden">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 bg-slate-50/70 px-4 py-2.5">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-800">Agenda de producción</h3>
                        <p class="text-[11px] text-slate-500">Calendario, pedidos del día y próximos pedidos</p>
                    </div>
                    <button type="button" id="toggleAgendaProduccion" class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-blue-50" aria-expanded="false" aria-controls="contenidoAgendaProduccion">
                        <span id="toggleAgendaProduccionTexto">Mostrar agenda</span><span id="toggleAgendaProduccionIcono" aria-hidden="true">⌄</span>
                    </button>
                </div>
                <div class="p-3 lg:p-4">
                    <div id="contenidoAgendaProduccion" class="hidden">
                    <div class="grid grid-cols-1 xl:grid-cols-[minmax(300px,0.95fr)_minmax(380px,1.25fr)_minmax(250px,0.72fr)] gap-3 items-stretch">
                        <div class="w-full min-w-0 rounded-xl bg-white border border-slate-100 p-3">
                            <div class="flex items-center gap-2.5 mb-3 min-w-0">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 text-xl">▦</span>
                                <div class="min-w-0">
                                    <h4 class="text-base font-semibold text-gray-800">Calendario</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">Pedidos programados por fecha de producción.</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-center gap-2 mb-2.5">
                                <button type="button" id="calMesAnterior" aria-label="Mes anterior"
                                    class="w-9 h-9 shrink-0 rounded-lg border border-slate-200 bg-white hover:bg-blue-50 text-slate-600">‹</button>
                                <button type="button" id="calIrHoy" class="h-9 px-4 shrink-0 rounded-lg border border-slate-200 bg-blue-50/60 hover:bg-blue-100 text-xs font-semibold text-slate-700">Hoy</button>
                                <div class="h-9 min-w-0 flex-1 max-w-[190px] flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-800 capitalize">
                                    <span id="calMesAnio"></span><span class="text-slate-400">⌄</span>
                                </div>
                                <button type="button" id="calMesSiguiente" aria-label="Mes siguiente"
                                    class="w-9 h-9 shrink-0 rounded-lg border border-slate-200 bg-white hover:bg-blue-50 text-slate-600">›</button>
                            </div>
                            <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-semibold text-slate-600 mb-1.5">
                                <div>Lun</div><div>Mar</div><div>Mié</div><div>Jue</div><div>Vie</div><div>Sáb</div><div>Dom</div>
                            </div>
                            <div id="calDias" class="grid grid-cols-7 gap-0 border-l border-t border-slate-100 rounded-b-lg overflow-hidden"></div>
                            <div class="flex flex-wrap gap-x-3 gap-y-1.5 mt-3 text-[10px] text-gray-600">
                                <span class="inline-flex items-center gap-1"><i class="w-2 h-2 rounded-full bg-orange-500"></i>Por iniciar</span>
                                <span class="inline-flex items-center gap-1"><i class="w-2 h-2 rounded-full bg-sky-500"></i>En proceso</span>
                                <span class="inline-flex items-center gap-1"><i class="w-2 h-2 rounded-full bg-green-500"></i>Terminado</span>
                                <span class="inline-flex items-center gap-1"><i class="w-2 h-2 rounded-full bg-red-500"></i>Atrasado</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0 rounded-xl border border-slate-100 p-3">
                            <div class="flex flex-wrap items-center justify-between gap-2 mb-2.5">
                                <div>
                                    <h5 class="font-semibold text-gray-800">Pedidos del día</h5>
                                    <p id="calFechaSeleccionada" class="text-sm text-gray-500"></p>
                                </div>
                                <span id="calTotalDia" class="text-xs font-semibold rounded-full bg-blue-50 text-blue-700 px-3 py-1">0 pedidos</span>
                            </div>
                            <div id="calListaPedidos" class="space-y-1.5 max-h-[260px] overflow-y-auto pr-1">
                                <p class="text-sm text-gray-400 italic">Selecciona un día para consultar sus pedidos.</p>
                            </div>
                            <p class="mt-2 text-[11px] text-gray-400">La fecha se toma de “Fecha Producción”.</p>
                        </div>

                        {{-- PRÓXIMOS PEDIDOS --}}
                        <aside class="min-w-0 rounded-xl border border-slate-100 bg-white p-3 flex flex-col">
                            <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-3 mb-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 text-lg">◷</span>
                                    <div class="min-w-0">
                                        <h5 class="font-semibold text-gray-800 text-sm">Próximos pedidos</h5>
                                        <p class="text-[11px] text-gray-500">Por fecha de producción</p>
                                    </div>
                                </div>
                                <button type="button" id="verAgendaCompleta" class="text-[11px] font-semibold text-blue-600 hover:text-blue-800 whitespace-nowrap">Ver agenda →</button>
                            </div>
                            <div id="listaProximosProduccion" class="space-y-1.5 flex-1 min-h-[120px] max-h-[220px] overflow-y-auto">
                                <p class="text-xs text-slate-400">Cargando pedidos...</p>
                            </div>
                            <div class="mt-3 rounded-xl border border-red-100 bg-red-50 p-3">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-100 text-red-600 text-lg">⚠</span>
                                    <div><p class="text-[11px] font-semibold text-red-600">Sin fecha de producción</p><p id="totalSinFechaProduccion" class="text-xl font-bold leading-tight text-red-700">0</p></div>
                                </div>
                                <button type="button" id="verSinFechaProduccion" class="mt-2 w-full rounded-lg border border-red-200 bg-white px-2 py-1.5 text-[11px] font-semibold text-red-700 hover:bg-red-100">Ver pedidos sin fecha →</button>
                            </div>
                        </aside>
                    </div>
                    </div>
                    <div class="grid grid-cols-2 xl:grid-cols-5 gap-2.5 mt-3" id="resumenSemanalProduccion">
                        <button type="button" data-resumen-semanal="total" aria-haspopup="dialog" aria-controls="modalResumenSemanal" class="w-full rounded-xl border border-blue-100 bg-blue-50/80 px-3 py-2 flex items-center gap-2.5 text-left transition hover:border-blue-300 hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600 text-lg">▦</span>
                            <div><p class="text-[11px] text-blue-800">Pedidos esta semana</p><p id="calSemanaTotal" class="text-lg font-bold leading-tight text-slate-800">0</p></div>
                        </button>
                        <button type="button" data-resumen-semanal="terminados" aria-haspopup="dialog" aria-controls="modalResumenSemanal" class="w-full rounded-xl border border-emerald-100 bg-emerald-50/80 px-3 py-2 flex items-center gap-2.5 text-left transition hover:border-emerald-300 hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 text-lg">✓</span>
                            <div><p class="text-[11px] text-emerald-800">Terminados esta semana</p><p id="calSemanaTerminados" class="text-lg font-bold leading-tight text-slate-800">0</p></div>
                        </button>
                        <button type="button" data-resumen-semanal="en-proceso" aria-haspopup="dialog" aria-controls="modalResumenSemanal" class="w-full rounded-xl border border-sky-100 bg-sky-50/80 px-3 py-2 flex items-center gap-2.5 text-left transition hover:border-sky-300 hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-400">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-sky-100 text-sky-600 text-lg">◉</span>
                            <div><p class="text-[11px] text-sky-800">En proceso esta semana</p><p id="calSemanaEnProceso" class="text-lg font-bold leading-tight text-slate-800">0</p></div>
                        </button>
                        <button type="button" data-resumen-semanal="por-iniciar" aria-haspopup="dialog" aria-controls="modalResumenSemanal" class="w-full rounded-xl border border-orange-100 bg-orange-50/80 px-3 py-2 flex items-center gap-2.5 text-left transition hover:border-orange-300 hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-100 text-orange-600 text-lg">◷</span>
                            <div><p class="text-[11px] text-orange-800">Por iniciar esta semana</p><p id="calSemanaIniciar" class="text-lg font-bold leading-tight text-slate-800">0</p></div>
                        </button>
                        <button type="button" data-resumen-semanal="atrasados" aria-haspopup="dialog" aria-controls="modalResumenSemanal" class="w-full rounded-xl border border-red-100 bg-red-50/80 px-3 py-2 flex items-center gap-2.5 text-left transition hover:border-red-300 hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-red-400">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-red-100 text-red-600 text-lg">⚠</span>
                            <div><p class="text-[11px] text-red-700">Atrasados esta semana</p><p id="calSemanaAtrasados" class="text-lg font-bold leading-tight text-slate-800">0</p></div>
                        </button>
                    </div>
                </div>
            </section>

            <div id="modalResumenSemanal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/60 p-4" role="dialog" aria-modal="true" aria-labelledby="modalResumenSemanalTitulo" aria-describedby="modalResumenSemanalDescripcion">
                <div class="flex max-h-[85vh] w-full max-w-3xl flex-col overflow-hidden rounded-xl bg-white shadow-2xl">
                    <div class="flex items-start justify-between border-b border-slate-200 px-5 py-4">
                        <div>
                            <h2 id="modalResumenSemanalTitulo" class="text-base font-bold text-slate-800">Pedidos de esta semana</h2>
                            <p id="modalResumenSemanalDescripcion" class="mt-1 text-xs text-slate-500">Pedidos programados para la semana actual.</p>
                        </div>
                        <button type="button" data-cerrar-resumen-semanal aria-label="Cerrar" class="rounded-lg px-2 py-1 text-xl leading-none text-slate-400 hover:bg-slate-100 hover:text-slate-700">&times;</button>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-5 py-2 text-xs text-slate-600">
                        <span id="modalResumenSemanalRango"></span><span id="modalResumenSemanalConteo" class="font-semibold"></span>
                    </div>
                    <div id="modalResumenSemanalLista" class="min-h-0 flex-1 space-y-2 overflow-y-auto p-4" aria-live="polite"></div>
                    <div class="flex justify-end border-t border-slate-200 px-5 py-3">
                        <button type="button" data-cerrar-resumen-semanal class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cerrar</button>
                    </div>
                </div>
            </div>

            {{-- TABLA --}}
            {{-- TABLA --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

            {{-- FILTROS INTEGRADOS A LA TABLA --}}
            <div class="border-b border-slate-200 bg-slate-50/80 px-4 py-3">
                <div class="flex flex-wrap items-end gap-x-3 gap-y-2">

                        <div class="min-w-[220px] flex-[2]">
                            <label for="filtroBusqueda" class="block text-[11px] font-semibold text-slate-500 mb-1">
                                Buscar
                            </label>
                            <input
                                type="text"
                                id="filtroBusqueda"
                                placeholder="Pedido, cliente, su pedido o enviar a..."
                                class="w-full min-w-0 border border-slate-200 rounded-lg px-2.5 py-2 text-xs bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        </div>

                        <div class="min-w-[95px] flex-1">
                            <label for="filtroPrioridad" class="block text-[11px] font-semibold text-slate-500 mb-1">
                                Prioridad
                            </label>
                            <select id="filtroPrioridad" class="w-full min-w-0 border border-slate-200 rounded-lg px-2.5 py-2 text-xs bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                <option value="todos">Todas</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="sin-prioridad">Sin prioridad</option>
                            </select>
                        </div>

                        <div class="min-w-[110px] flex-1">
                            <label for="filtroEstado" class="block text-[11px] font-semibold text-slate-500 mb-1">
                                Estado
                            </label>
                            <select id="filtroEstado" class="w-full min-w-0 border border-slate-200 rounded-lg px-2.5 py-2 text-xs bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                <option value="todos">Todos</option>
                                <option value="Por Iniciar">Por Iniciar</option>
                                <option value="En proceso">En proceso</option>
                                <option value="Cancelado">Cancelado</option>
                                <option value="Terminado">Terminado</option>
                            </select>
                        </div>

                        <div class="min-w-[110px] flex-1">
                            <label for="filtroSemaforo" class="block text-[11px] font-semibold text-slate-500 mb-1">
                                Semáforo
                            </label>
                            <select id="filtroSemaforo" class="w-full min-w-0 border border-slate-200 rounded-lg px-2.5 py-2 text-xs bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                <option value="todos">Todos</option>
                                <option value="EN TIEMPO">EN TIEMPO</option>
                                <option value="EN RIESGO">EN RIESGO</option>
                                <option value="ATRASADO">ATRASADO</option>
                                <option value="SIN FECHA">SIN FECHA</option>
                                <option value="TERMINADO">TERMINADO</option>
                                <option value="CANCELADO">CANCELADO</option>
                            </select>
                        </div>

                        <div class="min-w-[95px] flex-1">
                            <label for="filtroInterno" class="block text-[11px] font-semibold text-slate-500 mb-1">
                                Pedido Interno
                            </label>
                            <select id="filtroInterno" class="w-full min-w-0 border border-slate-200 rounded-lg px-2.5 py-2 text-xs bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                <option value="todos">Todos</option>
                                <option value="si">Sí</option>
                                <option value="no">No</option>
                            </select>
                        </div>

                        <div class="min-w-[125px] flex-1">
                            <label for="filtroProduccionDesde" class="block text-[11px] font-semibold text-slate-500 mb-1">
                                Producción desde
                            </label>
                            <input type="date" id="filtroProduccionDesde" class="w-full min-w-0 border border-slate-200 rounded-lg px-2.5 py-2 text-xs bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        </div>

                        <div class="min-w-[125px] flex-1">
                            <label for="filtroProduccionHasta" class="block text-[11px] font-semibold text-slate-500 mb-1">
                                Producción hasta
                            </label>
                            <input type="date" id="filtroProduccionHasta" class="w-full min-w-0 border border-slate-200 rounded-lg px-2.5 py-2 text-xs bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        </div>

                        <div class="min-w-[125px] flex-1">
                            <label for="filtroEntregaDesde" class="block text-[11px] font-semibold text-slate-500 mb-1">
                                Entrega desde
                            </label>
                            <input type="date" id="filtroEntregaDesde" class="w-full min-w-0 border border-slate-200 rounded-lg px-2.5 py-2 text-xs bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        </div>

                        <div class="min-w-[125px] flex-1">
                            <label for="filtroEntregaHasta" class="block text-[11px] font-semibold text-slate-500 mb-1">
                                Entrega hasta
                            </label>
                            <input type="date" id="filtroEntregaHasta" class="w-full min-w-0 border border-slate-200 rounded-lg px-2.5 py-2 text-xs bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        </div>

                        <button
                            type="button"
                            id="limpiarFiltros"
                            class="px-3 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 text-xs font-semibold whitespace-nowrap">
                            Limpiar filtros
                        </button>

                        <button
                            type="button"
                            id="exportarPedidosXlsx"
                            data-url="{{ route('control-operativo.exportar') }}"
                            class="px-3 py-2 rounded-lg border border-emerald-200 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold whitespace-nowrap disabled:cursor-wait disabled:opacity-60">
                            Exportar .xlsx
                        </button>

                    </div>

                    <div class="mt-2 flex items-center justify-between gap-4 text-xs">
                        <div id="resumenFiltros" class="flex flex-wrap items-center gap-1.5 text-gray-500" aria-live="polite"></div>
                        <span id="contadorFiltroAvanzado" class="font-semibold text-gray-700"></span>
                    </div>
            </div>

                <div class="px-2 pt-2 pb-1">

                    <div
                        id="contenedorTabla"
                        class="overflow-auto"
                        style="height: calc(100vh - 140px); min-height: 600px; overflow-y: auto; overflow-x: auto; position: relative;">

                        <table
                            id="tablaPedidos"
                            class="w-full min-w-[1800px] divide-y divide-gray-200">

                            <thead class="bg-gray-00">

                                <tr>

                                    {{-- PEDIDO --}}
                                    <th
                                        data-columna="pedido"
                                        class="col-pedido px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                        <button
                                            type="button"
                                            class="btn-ordenar w-full inline-flex items-center justify-between gap-2 group"
                                            data-sort="pedido">
                                            <span>Pedido</span>
                                            <span class="icono-orden text-gray-400 text-[10px] leading-none">
                                                <span class="flecha-arriba">▲</span>
                                                <span class="flecha-abajo">▼</span>
                                            </span>
                                        </button>
                                    </th>

                                    {{-- CLIENTE --}}
                                    <th
                                        data-columna="cliente"
                                        class="col-cliente px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                        <button
                                            type="button"
                                            class="btn-ordenar w-full inline-flex items-center justify-between gap-2 group"
                                            data-sort="cliente">
                                            <span>Cliente</span>
                                            <span class="icono-orden text-gray-400 text-[10px] leading-none">
                                                <span class="flecha-arriba">▲</span>
                                                <span class="flecha-abajo">▼</span>
                                            </span>
                                        </button>
                                    </th>

                                    {{-- SU PEDIDO --}}
                                    <th
                                        data-columna="su-pedido"
                                        class="col-su-pedido px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                        <button
                                            type="button"
                                            class="btn-ordenar w-full inline-flex items-center justify-between gap-2 group"
                                            data-sort="su-pedido">
                                            <span>Su Pedido</span>
                                            <span class="icono-orden text-gray-400 text-[10px] leading-none">
                                                <span class="flecha-arriba">▲</span>
                                                <span class="flecha-abajo">▼</span>
                                            </span>
                                        </button>
                                    </th>

                                    {{-- ENVIAR A --}}
                                    <th
                                        data-columna="enviar-a"
                                        class="col-enviar-a px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                        <button
                                            type="button"
                                            class="btn-ordenar w-full inline-flex items-center justify-between gap-2 group"
                                            data-sort="enviar-a">
                                            <span>Enviar a</span>
                                            <span class="icono-orden text-gray-400 text-[10px] leading-none">
                                                <span class="flecha-arriba">▲</span>
                                                <span class="flecha-abajo">▼</span>
                                            </span>
                                        </button>
                                    </th>
                                    {{-- FECHA ENTREGA --}}
                                    <th
                                        data-columna="fecha-entrega"
                                        class="col-fecha-entrega px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                        <button
                                            type="button"
                                            class="btn-ordenar w-full inline-flex items-center justify-between gap-2 group"
                                            data-sort="fecha-entrega">
                                            <span>Fecha Entrega</span>
                                            <span class="icono-orden text-gray-400 text-[10px] leading-none">
                                                <span class="flecha-arriba">▲</span>
                                                <span class="flecha-abajo">▼</span>
                                            </span>
                                        </button>
                                    </th>

                                    {{-- ESTADO OPERATIVO --}}
                                    <th
                                        data-columna="abastecimiento"
                                        class="col-abastecimiento px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase min-w-[150px]">
                                        <button
                                            type="button"
                                            class="btn-ordenar w-full inline-flex items-center justify-between gap-2 group"
                                            data-sort="abastecimiento">
                                            <span>Abastecimiento</span>
                                            <span class="icono-orden text-gray-400 text-[10px] leading-none">
                                                <span class="flecha-arriba">▲</span>
                                                <span class="flecha-abajo">▼</span>
                                            </span>
                                        </button>
                                    </th>

                                    {{-- ESTADO OPERATIVO --}}
                                    <th data-columna="estado-operativo" class="px-3 py-3 text-center text-xs font-semibold text-gray-600 min-w-[145px]">
                                        <button
                                            type="button"
                                            class="btn-ordenar w-full inline-flex items-center justify-between gap-2 group"
                                            data-sort="estado-operativo">
                                            <span>Estado operativo</span>
                                            <span class="icono-orden text-gray-400 text-[10px] leading-none">
                                                <span class="flecha-arriba">▲</span>
                                                <span class="flecha-abajo">▼</span>
                                            </span>
                                        </button>
                                    </th>

                                    {{-- FECHA PRODUCCIÓN --}}
                                    <th
                                        data-columna="fecha-produccion"
                                        class="col-fecha-produccion px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                        <button
                                            type="button"
                                            class="btn-ordenar w-full inline-flex items-center justify-between gap-2 group"
                                            data-sort="fecha-produccion">
                                            <span>Fecha Producción</span>
                                            <span class="icono-orden text-gray-400 text-[10px] leading-none">
                                                <span class="flecha-arriba">▲</span>
                                                <span class="flecha-abajo">▼</span>
                                            </span>
                                        </button>
                                    </th>

                                    {{-- AVANCE --}}
                                    <th
                                        data-columna="avance"
                                        class="col-avance px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                        <button
                                            type="button"
                                            class="btn-ordenar w-full inline-flex items-center justify-between gap-2 group"
                                            data-sort="avance">
                                            <span>Avance</span>
                                            <span class="icono-orden text-gray-400 text-[10px] leading-none">
                                                <span class="flecha-arriba">▲</span>
                                                <span class="flecha-abajo">▼</span>
                                            </span>
                                        </button>
                                    </th>
                                    {{-- =================================================
                                        PROCESO CON MENOR AVANCE
                                        ================================================== --}}

                                    <th
                                        data-columna="proceso-menor-avance"
                                        class="col-proceso-menor-avance px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">

                                        <button
                                            type="button"
                                            class="btn-ordenar w-full inline-flex items-center justify-between gap-2 group"
                                            data-sort="proceso-menor-avance">

                                            <span>Proceso con menor avance</span>

                                            <span class="icono-orden text-gray-400 text-[10px] leading-none">
                                                <span class="flecha-arriba">▲</span>
                                                <span class="flecha-abajo">▼</span>
                                            </span>

                                        </button>

                                    </th>

                                    {{-- ENTREGA DE INFORMACIÓN --}}
                                    <th data-columna="resultado-entrega" class="col-resultado-entrega px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase min-w-[190px]">
                                        <button
                                            type="button"
                                            class="btn-ordenar w-full inline-flex items-center justify-between gap-2 group"
                                            data-sort="resultado-entrega">
                                            <span>Entrega de información</span>
                                            <span class="icono-orden text-gray-400 text-[10px] leading-none">
                                                <span class="flecha-arriba">▲</span>
                                                <span class="flecha-abajo">▼</span>
                                            </span>
                                        </button>
                                    </th>

                                    {{-- SEMÁFORO --}}
                                    <th
                                        data-columna="semaforo"
                                        class="col-semaforo px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                        <button
                                            type="button"
                                            class="btn-ordenar w-full inline-flex items-center justify-between gap-2 group"
                                            data-sort="semaforo">
                                            <span>Semáforo</span>
                                            <span class="icono-orden text-gray-400 text-[10px] leading-none">
                                                <span class="flecha-arriba">▲</span>
                                                <span class="flecha-abajo">▼</span>
                                            </span>
                                        </button>
                                    </th>

                                    {{-- DÍAS --}}
                                    <th
                                        data-columna="dias"
                                        class="col-dias px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">

                                        <button
                                            type="button"
                                            class="btn-ordenar w-full inline-flex items-center justify-between gap-2 group"
                                            data-sort="dias">

                                            <span>Días</span>

                                            <span class="icono-orden text-gray-400 text-[10px] leading-none">
                                                <span class="flecha-arriba">▲</span>
                                                <span class="flecha-abajo">▼</span>
                                            </span>

                                        </button>

                                    </th>

                                    {{-- ACTIVIDADES --}}
                                    <th
                                        data-columna="comentario"
                                        class="col-comentario w-[340px] min-w-[340px] px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                        <button
                                            type="button"
                                            class="btn-ordenar w-full inline-flex items-center justify-between gap-2 group"
                                            data-sort="comentario">
                                            <span>Actividades</span>
                                            <span class="icono-orden text-gray-400 text-[10px] leading-none">
                                                <span class="flecha-arriba">▲</span>
                                                <span class="flecha-abajo">▼</span>
                                            </span>
                                        </button>
                                    </th>

                                </tr>

                            </thead>


                            <tbody class="bg-white divide-y divide-gray-200">

                                @forelse($pedidos as $pedido)

                                @php

                                $control = $pedido->controlOperativo;

                                $prioridad = $control?->prioridad;
                                $abastecimiento = $control?->abastecimiento;
                                $resultadoEntrega = $control?->resultado_entrega;

                                $fechaProduccion =
                                $control?->fecha_produccion;

                                $fechaEntrega =
                                $pedido->fecha_entrega;



                                $estado =
                                $control?->estado_operativo
                                ?? 'Por Iniciar';


                                $actividades = $pedido->actividadesControlOperativo;

                                $pedidoInterno =
                                (bool) ($pedido->pedido_interno ?? false);

                                /*
                                |--------------------------------------------------------------------------
                                | AVANCE
                                |--------------------------------------------------------------------------
                                */

                                $avance = $pedido->avance;

                                $procesoMenor = $pedido->proceso_menor_avance ?? null;

                                $nombreProceso = $procesoMenor['proceso'] ?? 'SIN DATOS';

                                $porcentajeProceso = $procesoMenor['porcentaje'] ?? null;

                                $porcentajeProcesoTexto = $porcentajeProceso !== null
                                ? round($porcentajeProceso * 100)
                                : null;
                                /*
                                |--------------------------------------------------------------------------
                                | DÍAS
                                |--------------------------------------------------------------------------
                                */

                                $dias = null;
                                $textoDias = '—';

                                /*
                                |--------------------------------------------------------------------------
                                | SEMÁFORO
                                |--------------------------------------------------------------------------
                                */

                                if (strtoupper($estado) === 'TERMINADO') {

                                $semaforo = 'TERMINADO';

                                } elseif (
                                strtoupper($estado) === 'CANCELADO'
                                ) {

                                $semaforo = 'CANCELADO';

                                } elseif (!$fechaEntrega) {

                                $semaforo = 'SIN FECHA';

                                } else {

                                $dias = now()
                                ->startOfDay()
                                ->diffInDays(
                                $fechaEntrega,
                                false
                                );

                                if ($dias < 0) {

                                    $diasAtraso=abs($dias);

                                    $textoDias=$diasAtraso . ' ' .
                                    ($diasAtraso==1 ? 'día' : 'días' ) . ' de atraso' ;

                                    $semaforo='ATRASADO' ;

                                    } elseif ($dias==0) {

                                    $textoDias='Entrega hoy' ;

                                    $semaforo='EN RIESGO' ;

                                    } else {

                                    $textoDias=$dias . ' ' .
                                    ($dias==1 ? 'día' : 'días' ) . ' restantes' ;

                                    if ($dias <=7) {

                                    $semaforo='EN RIESGO' ;

                                    } else {

                                    $semaforo='EN TIEMPO' ;

                                    }
                                    }

                                    }

                                    @endphp


                                    <tr
                                    class="hover:bg-gray-50 fila-pedido"
                                    data-avance="{{ $avance !== null ? $avance : '' }}"
                                    data-pedido-id="{{ $pedido->id }}"
                                    data-pedido="{{ ltrim((string) $pedido->pedido_no, '0') ?: '0' }}"
                                    data-cliente="{{ e($pedido->cliente ?? '') }}"
                                    data-su-pedido="{{ e($pedido->su_pedido ?? '') }}"
                                    data-enviar-a="{{ e($pedido->enviar_a ?? '') }}"
                                    data-fecha-entrega="{{ $pedido->fecha_entrega ? $pedido->fecha_entrega->format('Y-m-d') : '' }}"
                                    data-prioridad="{{ $prioridad ?? '' }}"
                                    data-abastecimiento="{{ $abastecimiento ?? '' }}"
                                    data-fecha-produccion="{{ $fechaProduccion ? $fechaProduccion->format('Y-m-d') : '' }}"
                                    data-estado="{{ e($estado) }}"
                                    data-semaforo="{{ $semaforo }}"
                                    data-dias="{{ $dias }}"
                                    data-pedido-interno="{{ $pedidoInterno ? 'si' : 'no' }}"
                                    data-proceso-menor-avance="{{ $procesoMenor['porcentaje'] ?? '' }}"
                                    data-url-actividad="{{ route('control-operativo.actividad.crear', $pedido) }}">


                                    {{-- PEDIDO --}}
                                    <td
                                        data-columna="pedido"
                                        class="col-pedido px-4 py-4 whitespace-nowrap">

                                        <div class="font-semibold text-gray-900 pedido-numero">
                                            {{ ltrim((string) $pedido->pedido_no, '0') ?: '0' }}
                                        </div>

                                    </td>


                                    {{-- CLIENTE --}}
                                    <td
                                        data-columna="cliente"
                                        class="col-cliente px-4 py-4 text-sm text-gray-700">

                                        {{ $pedido->cliente ?? '—' }}

                                    </td>


                                    {{-- SU PEDIDO --}}
                                    <td
                                        data-columna="su-pedido"
                                        class="col-su-pedido px-4 py-4 text-sm text-gray-700">

                                        {{ $pedido->su_pedido ?? '—' }}

                                    </td>


                                    {{-- ENVIAR A --}}
                                    <td
                                        data-columna="enviar-a"
                                        class="col-enviar-a px-4 py-4 text-sm text-gray-700">

                                        {{ $pedido->enviar_a ?? '—' }}

                                    </td>
                                    {{-- FECHA ENTREGA --}}
                                    <td
                                        data-columna="fecha-entrega"
                                        class="col-fecha-entrega px-4 py-4 whitespace-nowrap text-sm text-gray-700 text-center">

                                        {{ $pedido->fecha_entrega
                                                ? $pedido->fecha_entrega->format('d/m/Y')
                                                : '—'
                                            }}

                                    </td>


                                    {{-- ABASTECIMIENTO --}}
                                    <td
                                        data-columna="abastecimiento"
                                        class="col-abastecimiento px-3 py-4 whitespace-nowrap text-center">
                                        <select
                                            class="abastecimiento-input w-full min-w-0 max-w-full rounded-lg border border-slate-300 bg-white px-2 py-2 text-xs font-semibold text-slate-700 cursor-pointer focus:ring-blue-500 focus:border-blue-500"
                                            data-url="{{ route('control-operativo.abastecimiento', $pedido) }}">
                                            <option value="" {{ !$abastecimiento ? 'selected' : '' }}>—</option>
                                            <option value="otra_opcion" {{ $abastecimiento === 'otra_opcion' ? 'selected' : '' }}>Ninguno</option>
                                            <option value="apartado" {{ $abastecimiento === 'apartado' ? 'selected' : '' }}>Preparado</option>
                                            <option value="parcialidad" {{ $abastecimiento === 'parcialidad' ? 'selected' : '' }}>Parcialidad</option>
                                            <option value="entregado" {{ $abastecimiento === 'entregado' ? 'selected' : '' }}>Entregado</option>
                                            <option value="faltante_mp" {{ $abastecimiento === 'faltante_mp' ? 'selected' : '' }}>Pendiente</option>
                                        </select>
                                    </td>


                                    {{-- ESTADO OPERATIVO --}}
                                    <td data-columna="estado-operativo" class="px-3 py-3 text-center align-middle">
                                        <select
                                            class="estado-input w-full min-w-[130px] rounded-lg border px-2 py-2 text-xs font-semibold cursor-pointer focus:ring-blue-500 focus:border-blue-500"
                                            data-url="{{ route('control-operativo.estado', $pedido) }}"
                                            data-estado-original="{{ $estado }}">
                                            <option value="Por Iniciar" {{ $estado === 'Por Iniciar' ? 'selected' : '' }}>Por Iniciar</option>
                                            <option value="En proceso" {{ $estado === 'En proceso' ? 'selected' : '' }}>En proceso</option>
                                            <option value="Cancelado" {{ $estado === 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                                            <option value="Terminado" {{ $estado === 'Terminado' ? 'selected' : '' }}>Terminado</option>
                                        </select>
                                    </td>

                                    {{-- FECHA PRODUCCIÓN --}}
                                    <td
                                        data-columna="fecha-produccion"
                                        class="col-fecha-produccion px-4 py-4 whitespace-nowrap text-sm text-gray-700 text-center">

                                        <input
                                            type="date"
                                            class="fecha-produccion-input block w-full min-w-0 box-border border border-gray-300 rounded-lg px-2 py-1 text-sm focus:ring-blue-500 focus:border-blue-500"
                                            data-url="{{ route('control-operativo.fecha-produccion', $pedido) }}"
                                            value="{{ $fechaProduccion
                                                    ? $fechaProduccion->format('Y-m-d')
                                                    : ''
                                                }}">

                                    </td>


                                    {{-- AVANCE --}}
                                    <td
                                        data-columna="avance"
                                        class="col-avance px-4 py-4 whitespace-nowrap text-center">

                                        @if($avance !== null)

                                        <span class="avance-valor font-semibold text-gray-800">
                                            {{ strtoupper($estado) === 'TERMINADO'
                                                ? '100%'
                                                : number_format($avance * 100, 0) . '%'
                                            }}
                                        </span>

                                        @else

                                        <span class="text-gray-400">
                                            —
                                        </span>

                                        @endif

                                    </td>

                                    {{-- =================================================
     PROCESO CON MENOR AVANCE
================================================== --}}

                                    @php
                                    $procesoMenor = $pedido->proceso_menor_avance ?? null;

                                    $nombreProceso =
                                    $procesoMenor['proceso'] ?? 'SIN DATOS';

                                    $porcentajeProceso =
                                    $procesoMenor['porcentaje'] ?? null;

                                    $porcentajeProcesoTexto =
                                    $porcentajeProceso !== null
                                    ? round($porcentajeProceso * 100)
                                    : null;

                                    if ($porcentajeProceso === null) {

                                    $colorProceso = 'text-gray-400';
                                    $fondoProceso = 'bg-gray-700';

                                    } elseif ($porcentajeProcesoTexto <= 30) {

                                        $colorProceso='text-red-700' ;
                                        $fondoProceso='bg-red-100' ;

                                        } elseif ($porcentajeProcesoTexto <=60) {

                                        $colorProceso='text-orange-700' ;
                                        $fondoProceso='bg-orange-100' ;

                                        } elseif ($porcentajeProcesoTexto < 100) {

                                        $colorProceso='text-yellow-700' ;
                                        $fondoProceso='bg-yellow-100' ;

                                        } else {

                                        $colorProceso='text-green-700' ;
                                        $fondoProceso='bg-green-100' ;
                                        }
                                        @endphp

                                        <td
                                        data-columna="proceso-menor-avance"
                                        class="px-3 py-2 text-center  align-middle">
                                        <div class="flex flex-col items-center justify-center gap-1">

                                            <span class="proceso-menor-nombre inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold {{ $fondoProceso }} {{ $colorProceso }}">
                                                {{ $nombreProceso }}
                                            </span>

                                            <span class="proceso-menor-porcentaje text-xs font-bold text-gray-700" @if($porcentajeProcesoTexto === null) hidden @endif>
                                                {{ $porcentajeProcesoTexto !== null ? $porcentajeProcesoTexto . '%' : '' }}
                                            </span>

                                        </div>
                                        </td>

                                        {{-- ENTREGA DE INFORMACIÓN (VENTAS) --}}
                                        <td data-columna="resultado-entrega" class="col-resultado-entrega px-3 py-3 text-center align-middle">
    <select class="resultado-entrega-input w-full max-w-[210px] rounded-lg border border-gray-300 px-2 py-2 text-xs font-semibold focus:ring-blue-500 focus:border-blue-500"
            data-url="{{ route('control-operativo.resultado-entrega', $pedido) }}">
        <option value="" {{ !$resultadoEntrega ? 'selected' : '' }}>Seleccionar...</option>
        <option value="Pendiente" {{ $resultadoEntrega === 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
        <option value="Información incompleta" {{ $resultadoEntrega === 'Información incompleta' ? 'selected' : '' }}>Información incompleta</option>
        <option value="Información completa" {{ $resultadoEntrega === 'Información completa' ? 'selected' : '' }}>Información completa</option>
    </select>
</td>

                                        {{-- SEMÁFORO --}}
                                        <td
                                            data-columna="semaforo"
                                            class="col-semaforo px-4 py-4 whitespace-nowrap text-sm text-center">

                                            @if($semaforo === 'TERMINADO')

                                            <span class="semaforo-valor inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                TERMINADO
                                            </span>

                                            @elseif($semaforo === 'CANCELADO')

                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                CANCELADO
                                            </span>

                                            @elseif($semaforo === 'ATRASADO')

                                            <span
                                                class="inline-flex px-3 py-1 rounded-full text-xs font-bold"
                                                style="
                                                    background-color: #fee2e2 !important;
                                                    color: #dc2626 !important;
                                                ">
                                                ATRASADO
                                            </span>

                                            @elseif($semaforo === 'EN RIESGO')

                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-yellow-500 text-yellow-800">
                                                EN RIESGO
                                            </span>

                                            @elseif($semaforo === 'EN TIEMPO')

                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                EN TIEMPO
                                            </span>

                                            @else

                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                                SIN FECHA
                                            </span>

                                            @endif

                                        </td>


                                        {{-- DÍAS --}}
                                        <td
                                            data-columna="dias"
                                            class="col-dias px-4 py-3 text-center text-sm font-medium">

                                            {{ $textoDias }}

                                        </td>


                                        {{-- ACTIVIDADES --}}
                                        <td
                                            data-columna="comentario"
                                            class="col-comentario px-3 py-2 min-w-[340px] align-top">

                                            {{-- ACTIVIDADES --}}
                                            @php
                                                $actividadesTerminadas = $actividades->where('completada', true);
                                            @endphp
                                            <div class="actividades-container space-y-2">

                                                @forelse($actividades as $actividad)

                                                <div
                                                    class="actividad-item flex items-start gap-2 p-2 rounded-md border border-gray-200 bg-gray-50 {{ $actividad->completada ? 'actividad-terminada hidden opacity-60' : '' }}"
                                                    data-completada="{{ $actividad->completada ? '1' : '0' }}"
                                                    data-actividad-id="{{ $actividad->id }}"
                                                    data-url-actualizar="{{ route('control-operativo.actividad.actualizar', $actividad) }}"
                                                    data-url-eliminar="{{ route('control-operativo.actividad.eliminar', $actividad) }}">

                                                    {{-- CHECKBOX --}}
                                                    <input
                                                        type="checkbox"
                                                        class="actividad-completada mt-1 w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                        {{ $actividad->completada ? 'checked' : '' }}>

                                                    {{-- TEXTO --}}
                                                    <div class="actividad-texto flex-1 min-w-0 text-sm cursor-pointer">

                                                        <div class="actividad-nombre">
                                                            {{ $actividad->actividad }}
                                                        </div>

                                                        @if($actividad->responsable)
                                                        <div class="actividad-responsable text-xs text-gray-500 mt-1">
                                                            ({{ $actividad->responsable }})
                                                        </div>
                                                        @endif
                                                        @if($actividad->fecha_limite)
                                                        <div class="actividad-fecha text-xs text-slate-500 mt-1">
                                                            Fecha límite: {{ $actividad->fecha_limite->format('d/m/Y') }}
                                                        </div>
                                                        @endif

                                                    </div>

                                                    {{-- ELIMINAR --}}
                                                    <button
                                                        type="button"
                                                        class="eliminar-actividad text-red-500 hover:text-red-700 px-1"
                                                        title="Eliminar actividad">
                                                        ×
                                                    </button>

                                                </div>

                                                @empty

                                                <div class="sin-actividades text-gray-400 text-sm italic">
                                                    Sin actividades
                                                </div>

                                                @endforelse

                                            </div>

                                            {{-- MOSTRAR / OCULTAR ACTIVIDADES TERMINADAS --}}
                                            <button
                                                type="button"
                                                class="toggle-actividades-terminadas mt-2 text-xs font-medium text-gray-600 hover:text-blue-700 {{ $actividadesTerminadas->isEmpty() ? 'hidden' : '' }}"
                                                aria-expanded="false">
                                                Mostrar terminadas ({{ $actividadesTerminadas->count() }})
                                            </button>

                                            {{-- AGREGAR ACTIVIDAD --}}
                                            <button
                                                type="button"
                                                class="agregar-actividad mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                + Agregar actividad
                                            </button>




                                        </td>

                                        </tr>

                                        @empty

                                        <tr>

                                            <td
                                                colspan="15"
                                                class="px-6 py-10 text-center text-gray-500">

                                                No hay pedidos registrados.

                                            </td>

                                        </tr>

                                        @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <style>
        /* ============================================================
           CONTENEDOR DE LA TABLA
           ============================================================ */
        #contenedorTabla {
            overflow-x: auto;
            overflow-y: auto;
            position: relative;
            scrollbar-gutter: stable;
        }

        /* ============================================================
           TABLA: DISTRIBUCIÓN Y ANCHOS
           ============================================================ */
        #tablaPedidos {
            table-layout: fixed !important;
            width: 100% !important;
            min-width: 100% !important;
            border-collapse: separate;
            border-spacing: 0;
        }

        #tablaPedidos th,
        #tablaPedidos td {
            box-sizing: border-box;
            padding: 8px 10px !important;
        }

        /* Alinear verticalmente el contenido de todas las celdas de cada pedido.
           Las actividades pueden ocupar varias líneas y aumentar la altura de la fila;
           el resto de las columnas queda centrado respecto a esa altura. */
        #tablaPedidos tbody td {
            vertical-align: middle !important;
        }

        /* Mantener controles y etiquetas centrados dentro de su celda. */
        #tablaPedidos tbody td:not([data-columna="comentario"]) > select,
        #tablaPedidos tbody td:not([data-columna="comentario"]) > input,
        #tablaPedidos tbody td:not([data-columna="comentario"]) > button {
            vertical-align: middle;
        }

        /* Los controles respetan el ancho disponible de estas columnas
           incluso cuando se reducen con el tirador. */
        #tablaPedidos td[data-columna="estado-operativo"],
        #tablaPedidos td[data-columna="fecha-produccion"] {
            padding-left: 4px !important;
            padding-right: 4px !important;
            overflow: hidden;
        }

        #tablaPedidos select.estado-input,
        #tablaPedidos input.fecha-produccion-input {
            display: block;
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }

        #tablaPedidos select.estado-input {
            padding-left: 5px !important;
            padding-right: 16px !important;
            font-size: 10px !important;
        }

        #tablaPedidos input.fecha-produccion-input {
            padding-left: 3px !important;
            padding-right: 2px !important;
            font-size: 11px !important;
        }

        /* El ancho de cada columna lo controla una sola regla compartida
           por su encabezado y todas las celdas. */
        #tablaPedidos td[data-columna="pedido"] .pedido-numero {
            width: 100% !important;
            max-width: 100% !important;
            overflow: hidden !important;
            text-overflow: ellipsis;
            white-space: nowrap !important;
            display: block;
        }

        /* ============================================================
           ENCABEZADO DE TABLA FIJO
           El scroll vertical ocurre dentro de #contenedorTabla.
           ============================================================ */
        #contenedorTabla thead th,
        #tablaPedidos thead th {
            position: sticky !important;
            top: 0 !important;
            z-index: 100 !important;
            background-color: #f9fafb !important;
        }

        #tablaPedidos thead {
            position: relative;
            z-index: 100;
        }

        #tablaPedidos thead th {
            box-shadow: inset 0 -1px 0 #e5e7eb;
        }

        /* Mantener a la vista Pedido y Cliente durante el desplazamiento horizontal. */
        #tablaPedidos th[data-columna="pedido"],
        #tablaPedidos td[data-columna="pedido"] {
            position: sticky !important;
            left: 0;
            background-color: #f8fafc;
            box-shadow: 1px 0 0 #dbe3ec;
        }

        #tablaPedidos th[data-columna="cliente"],
        #tablaPedidos td[data-columna="cliente"] {
            position: sticky !important;
            left: var(--sticky-pedido-width, 90px);
            background-color: #f8fafc;
            box-shadow: 1px 0 0 #dbe3ec;
        }

        #tablaPedidos tbody td[data-columna="pedido"] { z-index: 31; }
        #tablaPedidos tbody td[data-columna="cliente"] { z-index: 30; }
        #tablaPedidos thead th[data-columna="pedido"],
        #tablaPedidos thead th[data-columna="cliente"] { z-index: 130 !important; }

        /* ============================================================
           BOTONES DE ORDENAR
           ============================================================ */
        .btn-ordenar {
            border: 0;
            background: transparent;
            padding: 0;
            margin: 0;
            cursor: pointer;
            color: inherit;
            text-align: inherit;
        }

        .btn-ordenar:hover .icono-orden {
            color: #2563eb;
        }

        .btn-ordenar .flecha-arriba,
        .btn-ordenar .flecha-abajo {
            opacity: .35;
        }

        .btn-ordenar[data-direccion="asc"] .flecha-arriba,
        .btn-ordenar[data-direccion="desc"] .flecha-abajo {
            opacity: 1;
            color: #2563eb;
        }

        /* ============================================================
           REDIMENSIONAMIENTO TIPO EXCEL
           ============================================================ */
        #tablaPedidos thead th.columna-redimensionando {
            user-select: none;
        }

        .columna-resizer {
            position: absolute;
            top: 0;
            right: -3px;
            width: 7px;
            height: 100%;
            cursor: col-resize;
            z-index: 150;
            touch-action: none;
        }

        .columna-resizer::after {
            content: "";
            position: absolute;
            top: 25%;
            bottom: 25%;
            left: 3px;
            width: 1px;
            background: transparent;
        }

        .columna-resizer:hover::after,
        .columna-redimensionando .columna-resizer::after {
            background: #2563eb;
        }

        #tablaPedidos.redimensionando-columna,
        #tablaPedidos.redimensionando-columna * {
            cursor: col-resize !important;
        }

        #tablaPedidos.redimensionando-fila,
        #tablaPedidos.redimensionando-fila * {
            cursor: row-resize !important;
        }

        #tablaPedidos tbody tr {
            position: relative;
        }

        #tablaPedidos tbody tr.redimensionando-fila td {
            user-select: none;
        }

        #tablaPedidos tbody tr:hover td {
            /* Mantiene el comportamiento visual actual. */
        }

        #tablaPedidos tbody td {
            overflow-wrap: anywhere;
        }

        /* ============================================================
           CONTENIDO ADAPTABLE A LA CELDA
           ============================================================ */
        #tablaPedidos td.col-cliente,
        #tablaPedidos td.col-su-pedido,
        #tablaPedidos td.col-enviar-a,
        #tablaPedidos td.col-comentario,
        #tablaPedidos td.col-proceso-menor-avance {
            white-space: normal !important;
            overflow-wrap: anywhere;
            word-break: break-word;
            vertical-align: top;
        }

        /* Pedido, fechas y controles se mantienen compactos. */
        #tablaPedidos td.col-pedido,
        #tablaPedidos td.col-fecha-entrega,
        #tablaPedidos td.col-fecha-produccion,
        #tablaPedidos td.col-avance,
        #tablaPedidos td.col-estado,
        #tablaPedidos td.col-semaforo,
        #tablaPedidos td.col-dias {
            white-space: nowrap;
        }

        /* Actividades: el texto se envuelve y no se corta. */
        #tablaPedidos td.col-comentario {
            min-width: 340px;
            white-space: normal !important;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        #tablaPedidos td.col-comentario .actividades-container {
            width: 100%;
            min-width: 0;
        }

        #tablaPedidos td.col-comentario .actividad-item {
            width: 100%;
            min-width: 0;
            white-space: normal !important;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        #tablaPedidos td.col-comentario .actividad-texto {
            min-width: 0 !important;
            max-width: 100%;
            white-space: normal !important;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        #tablaPedidos td.col-comentario .actividad-nombre,
        #tablaPedidos td.col-comentario .actividad-responsable {
            white-space: normal !important;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        #tablaPedidos td>div,
        #tablaPedidos td>span {
            max-width: 100%;
        }

        #tablaPedidos tbody td {
            vertical-align: top;
        }

        #tablaPedidos td.col-fecha-entrega,
        #tablaPedidos td.col-fecha-produccion,
        #tablaPedidos td.col-avance,
        #tablaPedidos td.col-estado,
        #tablaPedidos td.col-semaforo,
        #tablaPedidos td.col-dias {
            vertical-align: middle;
        }


        /* ============================================================
           REFINAMIENTO VISUAL — SOLO PRESENTACIÓN
           No modifica lógica, cálculos, filtros ni JavaScript.
           ============================================================ */

        /* Tarjetas superiores */
        #contenedorTabla ~ * { }

        /* Encabezado general */
        .py-6 > .w-full > .bg-white:first-child {
            border: 1px solid #e5e7eb;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        }

        /* Tabla más limpia y profesional */
        #tablaPedidos {
            font-size: 13px;
            color: #334155;
        }

        #tablaPedidos thead th {
            font-size: 11px !important;
            font-weight: 700 !important;
            letter-spacing: .035em;
            text-transform: uppercase;
            color: #475569 !important;
            background: #f8fafc !important;
            border-bottom: 1px solid #cbd5e1 !important;
            box-shadow: inset 0 -1px 0 #cbd5e1, 0 2px 5px rgba(15,23,42,.05) !important;
        }

        #tablaPedidos thead th[data-columna="estado-operativo"] {
            text-transform: none !important;
        }

        #tablaPedidos tbody tr {
            transition: background-color .15s ease, box-shadow .15s ease;
        }

        #tablaPedidos tbody tr:nth-child(even) td {
            background-color: #fbfdff;
        }

        #tablaPedidos tbody tr:hover td {
            background-color: #eff6ff !important;
        }

        #tablaPedidos tbody td {
            border-bottom: 1px solid #eef2f7;
            color: #334155;
        }

        #tablaPedidos tbody tr:last-child td {
            border-bottom: 0;
        }

        /* Número de pedido */
        #tablaPedidos td[data-columna="pedido"] .pedido-numero {
            font-weight: 700;
            color: #0f172a;
        }
        /* Campos de entrada dentro de la tabla */
        #tablaPedidos input:not([type="checkbox"]),
        #tablaPedidos select,
        #tablaPedidos textarea {
            border-color: #cbd5e1;
            border-radius: 7px;
            transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
        }

        #tablaPedidos input:not([type="checkbox"]):focus,
        #tablaPedidos select:focus,
        #tablaPedidos textarea:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 3px rgba(59,130,246,.12);
            outline: none;
        }

        /* Botones dentro de la tabla */
        #tablaPedidos button {
            transition: transform .12s ease, box-shadow .12s ease, background-color .12s ease;
        }

        #tablaPedidos button:hover {
            transform: translateY(-1px);
        }

        #tablaPedidos .estado-guardado {
            display: block;
            margin-top: 3px;
            min-height: 12px;
            font-size: 9px;
            line-height: 1.2;
            white-space: normal;
        }

        #tablaPedidos .estado-guardado[data-estado="guardando"] { color: #64748b; }
        #tablaPedidos .estado-guardado[data-estado="guardado"] { color: #15803d; }
        #tablaPedidos .estado-guardado[data-estado="error"] { color: #dc2626; }

        /* Panel de columnas: siempre por encima de la tabla y su encabezado sticky */
        #panelColumnas {
            position: absolute !important;
            z-index: 999999 !important;
            border-color: #dbe3ec !important;
            box-shadow: 0 14px 30px rgba(15,23,42,.14) !important;
        }

        /* La tabla queda en una capa inferior para no cubrir el menú de columnas */
        #contenedorTabla {
            position: relative;
            z-index: 1;
        }

        #panelColumnas label:hover {
            background: #f8fafc;
            border-radius: 7px;
        }

        /* Scrollbar discreto */
        #contenedorTabla::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        #contenedorTabla::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        #contenedorTabla::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border: 2px solid #f1f5f9;
            border-radius: 999px;
        }

        #contenedorTabla::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Todas las celdas pueden reducirse sin invadir la columna vecina.
           El contenido se envuelve dentro de su celda y los controles se
           limitan al espacio interior disponible. */
        #tablaPedidos thead th,
        #tablaPedidos tbody td {
            min-width: 0;
            overflow: hidden;
        }

        #tablaPedidos thead th .btn-ordenar {
            width: 100%;
            min-width: 0;
            gap: 3px;
            white-space: normal;
        }

        #tablaPedidos thead th .btn-ordenar > span:first-child {
            min-width: 0;
            overflow-wrap: anywhere;
            word-break: normal;
        }

        #tablaPedidos thead th .icono-orden {
            flex: 0 0 auto;
        }

        #tablaPedidos tbody td {
            white-space: normal !important;
            overflow-wrap: anywhere;
            word-break: normal;
        }

        #tablaPedidos tbody td > *,
        #tablaPedidos tbody td > * > * {
            box-sizing: border-box;
            min-width: 0;
            max-width: 100%;
        }

        #tablaPedidos tbody td select,
        #tablaPedidos tbody td input:not([type="checkbox"]),
        #tablaPedidos tbody td textarea {
            display: block;
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }

        /* Semáforo y días suelen contener frases largas; se muestran en
           varias líneas cuando el usuario estrecha esas columnas. */
        #tablaPedidos td[data-columna="semaforo"] .semaforo-valor,
        #tablaPedidos td[data-columna="dias"] {
            white-space: normal !important;
            overflow-wrap: anywhere;
            word-break: normal;
        }

        #tablaPedidos td[data-columna="semaforo"] .semaforo-valor {
            display: inline-flex;
            justify-content: center;
            max-width: 100%;
            box-sizing: border-box;
        }

        /* Los botones de actividad no deben desplazarse sobre otra celda al
           pasar el cursor. */
        #tablaPedidos tbody td button:hover {
            transform: none;
        }

        /* Indicadores superiores: más profundidad sin cambiar sus colores */
        .text-white.rounded-lg.min-w-\[145px\] {
            box-shadow: 0 6px 16px rgba(15,23,42,.10) !important;
        }

        /* Select de avance */
        #filtroAvance {
            min-height: 38px;
            border-color: #cbd5e1;
            box-shadow: 0 1px 2px rgba(15,23,42,.04);
        }

        #filtroAvance:hover {
            border-color: #94a3b8;
        }
    </style>

    {{-- ================================================================
     MODAL NUEVA ACTIVIDAD
     ================================================================ --}}
    <div
        id="modalActividad"
        class="hidden fixed inset-0 flex items-center justify-center bg-black/60 backdrop-blur-[2px] px-4"
        style="z-index: 999999 !important;">
        <div
            class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl"
            style="z-index: 1000000;">

            {{-- ENCABEZADO --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">

                <div>
                    <h3
                        id="tituloModalActividad"
                        class="text-lg font-semibold text-gray-800">

                        Nueva actividad

                    </h3>

                    <p class="text-sm text-gray-500 mt-1">

                        Pedido
                        <span
                            id="modalActividadPedido"
                            class="font-semibold">
                        </span>

                    </p>
                </div>

                <button
                    type="button"
                    id="cerrarModalActividad"
                    class="text-gray-400 hover:text-gray-700 text-2xl leading-none">

                    &times;

                </button>

            </div>


            {{-- CONTENIDO --}}
            <div class="px-6 py-5">

                <div
                    id="estadoActividadModal"
                    class="hidden mb-4">

                    <label class="inline-flex items-center gap-2 cursor-pointer">

                        <input
                            type="checkbox"
                            id="modalActividadCompletada"
                            class="w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500">

                        <span class="text-sm text-gray-700">
                            Actividad completada
                        </span>

                    </label>

                </div>

                <label
                    for="inputActividad"
                    class="block text-sm font-medium text-gray-700 mb-2">

                    Actividad

                </label>

                <textarea
                    id="inputActividad"
                    rows="4"
                    maxlength="1000"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm resize-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Escribe la actividad que deseas agregar..."></textarea>

                <div class="mt-1 text-right text-xs text-gray-400">
                    Máximo 1000 caracteres
                </div>

            </div>

            <div class="mt-4 px-6">
                <label
                    for="inputResponsableActividad"
                    class="block text-sm font-medium text-gray-700 mb-1">
                    Responsable
                </label>

                <input
                    type="text"
                    id="inputResponsableActividad"
                    class="w-full rounded-md border border-gray-300 px-3 py-2
               focus:border-blue-500 focus:ring-blue-500">
            </div>


            {{-- BOTONES --}}
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200">

                <button
                    type="button"
                    id="cancelarModalActividad"
                    class="px-4 py-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium">

                    Cancelar

                </button>


                <button
                    type="button"
                    id="guardarActividadModal"
                    class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium">

                    Guardar actividad

                </button>

            </div>

        </div>

    </div>

    {{-- ================================================================
         MODAL DE ACTUALIZACIÓN DE ABASTECIMIENTO
         ================================================================ --}}
    <div id="modalAbastecimiento" class="hidden fixed inset-0 z-[1000000] flex items-center justify-center bg-slate-950/50 px-4 py-6">
        <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex items-start justify-between border-b border-slate-200 px-6 py-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">Registrar surtido a producción</h3>
                    <p class="mt-1 text-sm text-slate-500">Pedido <span id="modalAbastecimientoPedido" class="font-semibold text-slate-700"></span></p>
                    <p class="mt-1 text-xs text-slate-500">Estado del surtido: <span id="modalAbastecimientoEstado" class="font-semibold text-blue-700"></span></p>
                </div>
                <button type="button" id="cerrarModalAbastecimiento" class="rounded-lg px-2 text-2xl leading-none text-slate-400 hover:bg-slate-100 hover:text-slate-700" aria-label="Cerrar">&times;</button>
            </div>

            <div class="space-y-4 px-6 py-5">
                <div>
                    <label for="motivoAbastecimiento" class="mb-1 block text-sm font-medium text-slate-700">Motivo</label>
                    <select id="motivoAbastecimiento" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500"></select>
                </div>
                <div>
                    <label for="comentarioAbastecimiento" class="mb-1 block text-sm font-medium text-slate-700">Comentario <span class="font-normal text-slate-400">(opcional; requerido para “Otro”)</span></label>
                    <textarea id="comentarioAbastecimiento" rows="3" maxlength="1000" class="w-full resize-y rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Agrega un detalle si hace falta"></textarea>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="responsableAbastecimiento" class="mb-1 block text-sm font-medium text-slate-700">Responsable <span class="font-normal text-slate-400">(opcional)</span></label>
                        <input id="responsableAbastecimiento" type="text" maxlength="255" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Nombre">
                    </div>
                    <div>
                        <label for="fechaLimiteAbastecimiento" class="mb-1 block text-sm font-medium text-slate-700">Fecha límite <span class="font-normal text-slate-400">(opcional)</span></label>
                        <input id="fechaLimiteAbastecimiento" type="date" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
                <p id="errorAbastecimiento" class="hidden rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700"></p>
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-200 px-6 py-4">
                <button type="button" id="cancelarModalAbastecimiento" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancelar</button>
                <button type="button" id="guardarModalAbastecimiento" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-wait disabled:opacity-60">Guardar y agregar actividad</button>
            </div>
        </div>
    </div>

    {{-- ================================================================
         REDIMENSIONAMIENTO TIPO EXCEL
         - Ancho de columnas con arrastre
         - Alto de filas con arrastre desde el borde inferior
         - Guarda tamaños en localStorage
       ================================================================ --}}
    <script>
        window.mostrarEstadoGuardadoControl = function(control, estado, detalle = '') {
            const celda = control?.closest('td');
            if (!celda) return;

            let indicador = celda.querySelector('.estado-guardado');
            if (!indicador) {
                indicador = document.createElement('span');
                indicador.className = 'estado-guardado';
                indicador.setAttribute('role', 'status');
                indicador.setAttribute('aria-live', 'polite');
                celda.appendChild(indicador);
            }

            const textos = {
                guardando: 'Guardando…',
                guardado: 'Guardado',
                error: 'Error al guardar'
            };
            indicador.dataset.estado = estado;
            indicador.textContent = textos[estado] || '';
            indicador.title = detalle;

            if (estado === 'guardado') {
                window.setTimeout(() => {
                    if (indicador.dataset.estado === 'guardado') {
                        indicador.textContent = '';
                        indicador.removeAttribute('data-estado');
                        indicador.removeAttribute('title');
                    }
                }, 3000);
            }
        };

        window.encabezadosSolicitudJsonControl = function(accept = 'application/json') {
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute('content');

            if (!csrfToken) {
                throw new Error('No se encontró el token de seguridad para guardar los cambios.');
            }

            return {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': accept
            };
        };

        window.actualizarOffsetColumnasFijasControl = function() {
            const tabla = document.getElementById('tablaPedidos');
            const encabezadoPedido = tabla?.querySelector('thead th[data-columna="pedido"]');
            const anchoPedido = encabezadoPedido && !encabezadoPedido.hidden
                ? encabezadoPedido.getBoundingClientRect().width
                : 0;

            tabla?.style.setProperty('--sticky-pedido-width', `${anchoPedido}px`);
        };

        document.addEventListener('DOMContentLoaded', function() {

            const tabla = document.getElementById('tablaPedidos');

            if (!tabla) {
                return;
            }

            const storageAnchos = 'controlOperativoAnchosColumnas_v2';
            const storageAlturas = 'controlOperativoAlturasFilas';

            let anchosColumnas = {};
            let alturasFilas = {};

            try {
                anchosColumnas =
                    JSON.parse(localStorage.getItem(storageAnchos) || '{}');

                alturasFilas =
                    JSON.parse(localStorage.getItem(storageAlturas) || '{}');
            } catch (error) {
                anchosColumnas = {};
                alturasFilas = {};
            }

            const anchosPorDefecto = {
                'pedido': 90,
                'cliente': 130,
                'su-pedido': 110,
                'enviar-a': 100,
                'fecha-entrega': 105,
                'abastecimiento': 150,
                'estado-operativo': 130,
                'fecha-produccion': 100,
                'avance': 75,
                'proceso-menor-avance': 120,
                'resultado-entrega': 190,
                'semaforo': 105,
                'dias': 100,
                'comentario': 340
            };

            // Ajusta una sola vez los anchos anteriores de estas dos columnas;
            // luego el usuario puede volver a modificarlos y se conservarán.
            const anchosCompactosSolicitados = {
                'estado-operativo': 65,
                'fecha-produccion': 75
            };
            const versionAnchosCompactos = 'controlOperativoAnchosCompactos_estado_fecha_v3';
            if (!localStorage.getItem(versionAnchosCompactos)) {
                Object.entries(anchosCompactosSolicitados).forEach(function([nombre, anchoCompacto]) {
                    const anchoGuardado = Number(anchosColumnas[nombre]);
                    if (Number.isFinite(anchoGuardado) && anchoGuardado > anchoCompacto) {
                        anchosColumnas[nombre] = anchoCompacto;
                    }
                });
                localStorage.setItem(versionAnchosCompactos, '1');
            }

            function elementosColumna(nombre) {
                return tabla.querySelectorAll(
                    '[data-columna="' +
                    CSS.escape(nombre) +
                    '"]'
                );
            }

            function anchoMinimoColumna(nombre) {
                if (nombre === 'estado-operativo') return 55;
                if (nombre === 'fecha-produccion') return 65;
                if (nombre === 'resultado-entrega') return 120;
                if (nombre === 'comentario') return 160;
                return 70;
            }

            function aplicarAnchoColumna(nombre, ancho) {

                const anchoMinimo = anchoMinimoColumna(nombre);
                const anchoPx =
                    Math.max(anchoMinimo, Math.min(700, Number(ancho)));

                elementosColumna(nombre).forEach(function(elemento) {
                    elemento.style.setProperty('width', anchoPx + 'px', 'important');
                    elemento.style.setProperty('min-width', anchoMinimo + 'px', 'important');
                    elemento.style.removeProperty('max-width');
                });
            }

            function actualizarAnchoTotalTabla() {
                const encabezadosVisibles = Array.from(
                    tabla.querySelectorAll('thead th[data-columna]')
                ).filter(function(th) {
                    return th.offsetParent !== null;
                });

                const sumaAnchos = encabezadosVisibles.reduce(
                    function(total, th) {
                        const anchoConfigurado = Number.parseFloat(
                            th.style.width
                        );
                        return total + (Number.isFinite(anchoConfigurado) ?
                            anchoConfigurado : th.getBoundingClientRect().width);
                    },
                    0
                );
                const anchoContenedor = tabla.parentElement?.clientWidth ?? 0;

                tabla.style.setProperty(
                    'width',
                    Math.max(sumaAnchos, anchoContenedor) + 'px',
                    'important'
                );

                window.actualizarOffsetColumnasFijasControl();
            }

            function guardarAnchos() {
                localStorage.setItem(
                    storageAnchos,
                    JSON.stringify(anchosColumnas)
                );
            }

            function guardarAlturas() {
                localStorage.setItem(
                    storageAlturas,
                    JSON.stringify(alturasFilas)
                );
            }

            /*
             * ==========================================================
             * ANCHO DE COLUMNAS
             * ==========================================================
             */

            function prepararRedimensionamientoColumnas() {

                const encabezados =
                    tabla.querySelectorAll('thead th[data-columna]');

                encabezados.forEach(function(th) {

                    if (th.querySelector('.columna-resizer')) {
                        return;
                    }

                    const nombre = th.dataset.columna;

                    th.style.position = 'relative';

                    const resizer =
                        document.createElement('div');

                    resizer.className = 'columna-resizer';
                    resizer.title = 'Arrastra para cambiar el ancho';

                    th.appendChild(resizer);

                    resizer.addEventListener('pointerdown', function(event) {

                        event.preventDefault();
                        event.stopPropagation();

                        const anchoInicial = th.getBoundingClientRect().width;
                        const posicionInicial = event.clientX;

                        th.classList.add('columna-redimensionando');
                        tabla.classList.add('redimensionando-columna');

                        resizer.setPointerCapture?.(event.pointerId);

                        function mover(e) {

                            const diferencia =
                                e.clientX - posicionInicial;

                            const nuevoAncho =
                                Math.max(
                                    anchoMinimoColumna(nombre),
                                    Math.min(
                                        700,
                                        anchoInicial + diferencia
                                    )
                                );

                            aplicarAnchoColumna(nombre, nuevoAncho);
                            anchosColumnas[nombre] = Math.round(nuevoAncho);
                            actualizarAnchoTotalTabla();
                        }

                        function terminar(e) {

                            try {
                                resizer.releasePointerCapture?.(
                                    e.pointerId
                                );
                            } catch (_) {}

                            th.classList.remove(
                                'columna-redimensionando'
                            );

                            tabla.classList.remove(
                                'redimensionando-columna'
                            );

                            guardarAnchos();

                            resizer.removeEventListener(
                                'pointermove',
                                mover
                            );

                            resizer.removeEventListener(
                                'pointerup',
                                terminar
                            );

                            resizer.removeEventListener(
                                'pointercancel',
                                terminar
                            );
                        }

                        resizer.addEventListener(
                            'pointermove',
                            mover
                        );

                        resizer.addEventListener(
                            'pointerup',
                            terminar
                        );

                        resizer.addEventListener(
                            'pointercancel',
                            terminar
                        );
                    });
                });
            }

            function restaurarAnchosColumnas() {

                Object.entries(anchosPorDefecto).forEach(
                    function([nombre, anchoDefecto]) {

                        const ancho =
                            anchosColumnas[nombre] ?? anchoDefecto;

                        if (
                            document.querySelector(
                                '#tablaPedidos thead th[data-columna="' +
                                CSS.escape(nombre) +
                                '"]'
                            )
                        ) {
                            aplicarAnchoColumna(
                                nombre,
                                ancho
                            );
                        }

                        if (anchosColumnas[nombre] === undefined) {
                            anchosColumnas[nombre] = anchoDefecto;
                        }
                    }
                );

                actualizarAnchoTotalTabla();
                guardarAnchos();
            }

            /*
             * ==========================================================
             * ALTO DE FILAS
             * ==========================================================
             *
             * Se puede arrastrar desde los últimos 8 px inferiores
             * de cualquier fila, como en Excel.
             */

            let filaRedimensionando = null;
            let filaYInicial = 0;
            let filaAlturaInicial = 0;

            function restaurarAlturasFilas() {

                Object.entries(alturasFilas).forEach(
                    function([pedido, altura]) {

                        const fila =
                            tabla.querySelector(
                                'tbody tr[data-pedido="' +
                                CSS.escape(pedido) +
                                '"]'
                            );

                        if (!fila) {
                            return;
                        }

                        const alturaPx =
                            Math.max(
                                40,
                                Math.min(
                                    600,
                                    Number(altura)
                                )
                            );

                        fila.style.height =
                            alturaPx + 'px';
                    }
                );
            }

            function guardarAlturaFila(fila) {

                const pedido =
                    fila.dataset.pedido;

                if (!pedido) {
                    return;
                }

                alturasFilas[pedido] =
                    Math.round(
                        fila.getBoundingClientRect().height
                    );

                guardarAlturas();
            }

            tabla.addEventListener(
                'mousemove',
                function(event) {

                    if (
                        filaRedimensionando ||
                        tabla.classList.contains(
                            'redimensionando-columna'
                        )
                    ) {
                        return;
                    }

                    const celda =
                        event.target.closest(
                            'tbody td'
                        );

                    if (!celda) {
                        tabla.style.cursor = '';
                        return;
                    }

                    const fila =
                        celda.closest('tr');

                    if (!fila) {
                        return;
                    }

                    const rect =
                        fila.getBoundingClientRect();

                    const cercaDelBorde =
                        event.clientY >= rect.bottom - 8 &&
                        event.clientY <= rect.bottom + 3;

                    tabla.style.cursor =
                        cercaDelBorde ?
                        'row-resize' :
                        '';
                }
            );

            tabla.addEventListener(
                'mousedown',
                function(event) {

                    if (
                        event.button !== 0 ||
                        tabla.classList.contains(
                            'redimensionando-columna'
                        )
                    ) {
                        return;
                    }

                    const celda =
                        event.target.closest(
                            'tbody td'
                        );

                    if (!celda) {
                        return;
                    }

                    const fila =
                        celda.closest('tr');

                    if (!fila) {
                        return;
                    }

                    const rect =
                        fila.getBoundingClientRect();

                    const cercaDelBorde =
                        event.clientY >= rect.bottom - 8 &&
                        event.clientY <= rect.bottom + 3;

                    if (!cercaDelBorde) {
                        return;
                    }

                    event.preventDefault();

                    filaRedimensionando = fila;
                    filaYInicial = event.clientY;
                    filaAlturaInicial = rect.height;

                    fila.classList.add(
                        'redimensionando-fila'
                    );

                    tabla.classList.add(
                        'redimensionando-fila'
                    );

                    document.body.style.userSelect =
                        'none';
                }
            );

            document.addEventListener(
                'mousemove',
                function(event) {

                    if (!filaRedimensionando) {
                        return;
                    }

                    const diferencia =
                        event.clientY - filaYInicial;

                    const nuevaAltura =
                        Math.max(
                            40,
                            Math.min(
                                600,
                                filaAlturaInicial +
                                diferencia
                            )
                        );

                    filaRedimensionando.style.height =
                        nuevaAltura + 'px';
                }
            );

            document.addEventListener(
                'mouseup',
                function() {

                    if (!filaRedimensionando) {
                        return;
                    }

                    guardarAlturaFila(
                        filaRedimensionando
                    );

                    filaRedimensionando.classList.remove(
                        'redimensionando-fila'
                    );

                    filaRedimensionando = null;

                    tabla.classList.remove(
                        'redimensionando-fila'
                    );

                    document.body.style.userSelect =
                        '';
                    tabla.style.cursor = '';
                }
            );

            /*
             * ==========================================================
             * INICIALIZACIÓN
             * ==========================================================
             */

            prepararRedimensionamientoColumnas();
            restaurarAnchosColumnas();
            restaurarAlturasFilas();

        });
    </script>

    {{-- JAVASCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ================================================================
            // COLUMNAS
            // ================================================================

            const btnColumnas =
                document.getElementById('btnColumnas');

            const panelColumnas =
                document.getElementById('panelColumnas');

            const cerrarColumnas =
                document.getElementById('cerrarColumnas');

            const aplicarColumnas =
                document.getElementById('aplicarColumnas');

            const restaurarColumnas =
                document.getElementById('restaurarColumnas');

            const columnasOcultasResumen =
                document.getElementById('columnasOcultasResumen');

            const checks =
                document.querySelectorAll('.columna-check');

            const columnasStorage =
                'controlOperativoColumnas';

            function actualizarResumenColumnasOcultas() {
                if (!columnasOcultasResumen) return;

                const cantidadOcultas = Array.from(checks)
                    .filter(check => !check.checked)
                    .length;

                columnasOcultasResumen.textContent =
                    cantidadOcultas === 1 ? '1 oculta' : `${cantidadOcultas} ocultas`;
                columnasOcultasResumen.classList.toggle('hidden', cantidadOcultas === 0);
                btnColumnas?.setAttribute(
                    'aria-label',
                    cantidadOcultas > 0
                        ? `Columnas, ${cantidadOcultas} ocultas`
                        : 'Columnas'
                );
            }


            // ---------------------------------------------------------------
            // APLICAR COLUMNAS
            // ---------------------------------------------------------------

            function aplicarColumnasVisibles() {

                checks.forEach(function(check) {

                    const columna =
                        check.dataset.columna;

                    const elementos =
                        document.querySelectorAll(
                            '#tablaPedidos [data-columna="' +
                            columna +
                            '"]'
                        );

                    elementos.forEach(function(elemento) {

                        elemento.hidden = !check.checked;

                    });

                });

                window.actualizarOffsetColumnasFijasControl();

            }


            // ---------------------------------------------------------------
            // GUARDAR CONFIGURACIÓN
            // ---------------------------------------------------------------

            function guardarColumnas() {

                const configuracion = {};

                checks.forEach(function(check) {

                    configuracion[
                        check.dataset.columna
                    ] = check.checked;

                });

                localStorage.setItem(
                    columnasStorage,
                    JSON.stringify(configuracion)
                );

            }


            // ---------------------------------------------------------------
            // CARGAR CONFIGURACIÓN
            // ---------------------------------------------------------------

            function cargarColumnas() {

                const guardado =
                    localStorage.getItem(columnasStorage);

                // -----------------------------------------------------------
                // PRIMERA VEZ
                // -----------------------------------------------------------

                if (!guardado) {

                    // Todas las columnas visibles
                    checks.forEach(function(check) {

                        check.checked = true;

                    });

                    aplicarColumnasVisibles();

                    guardarColumnas();

                    return;
                }


                // -----------------------------------------------------------
                // CONFIGURACIÓN EXISTENTE
                // -----------------------------------------------------------

                try {

                    const configuracion =
                        JSON.parse(guardado);

                    checks.forEach(function(check) {

                        const columna =
                            check.dataset.columna;

                        if (
                            Object.prototype.hasOwnProperty.call(
                                configuracion,
                                columna
                            )
                        ) {

                            check.checked =
                                configuracion[columna];

                        } else {

                            // Si aparece una columna nueva,
                            // se muestra por defecto.

                            check.checked = true;

                        }

                    });

                    aplicarColumnasVisibles();

                } catch (error) {

                    console.error(
                        'Error al cargar configuración de columnas:',
                        error
                    );

                    // Si la configuración está dañada,
                    // volvemos a mostrar todas.

                    checks.forEach(function(check) {

                        check.checked = true;

                    });

                    aplicarColumnasVisibles();

                    guardarColumnas();

                }

            }


            // ---------------------------------------------------------------
            // ABRIR PANEL
            // ---------------------------------------------------------------

            btnColumnas.addEventListener(
                'click',
                function() {

                    panelColumnas.classList.toggle(
                        'hidden'
                    );

                }
            );


            // ---------------------------------------------------------------
            // CERRAR PANEL
            // ---------------------------------------------------------------

            cerrarColumnas.addEventListener(
                'click',
                function() {

                    panelColumnas.classList.add(
                        'hidden'
                    );

                }
            );


            // ---------------------------------------------------------------
            // APLICAR
            // ---------------------------------------------------------------

            aplicarColumnas.addEventListener(
                'click',
                function() {

                    aplicarColumnasVisibles();

                    guardarColumnas();
                    actualizarResumenColumnasOcultas();

                    panelColumnas.classList.add(
                        'hidden'
                    );

                }
            );

            restaurarColumnas?.addEventListener('click', function() {
                checks.forEach(check => { check.checked = true; });
                aplicarColumnasVisibles();
                guardarColumnas();
                actualizarResumenColumnasOcultas();
                panelColumnas.classList.add('hidden');
            });


            // ---------------------------------------------------------------
            // CERRAR AL HACER CLICK FUERA
            // ---------------------------------------------------------------

            document.addEventListener(
                'click',
                function(event) {

                    if (
                        !panelColumnas.contains(event.target) &&
                        !btnColumnas.contains(event.target)
                    ) {

                        panelColumnas.classList.add(
                            'hidden'
                        );

                    }

                }
            );


            // ---------------------------------------------------------------
            // CARGAR CONFIGURACIÓN AL ABRIR LA PÁGINA
            // ---------------------------------------------------------------

            cargarColumnas();
            actualizarResumenColumnasOcultas();


            // ================================================================
            // FILTROS Y ORDENAMIENTO
            // ================================================================

            const filasPedidos =
                Array.from(document.querySelectorAll('.fila-pedido'));

            const filtroAvance =
                document.getElementById('filtroAvance');

            const contadorFiltro =
                document.getElementById('contadorFiltro');

            const contadorFiltroAvanzado =
                document.getElementById('contadorFiltroAvanzado');

            const resumenFiltros =
                document.getElementById('resumenFiltros');

            const filtros = {
                busqueda: document.getElementById('filtroBusqueda'),
                prioridad: document.getElementById('filtroPrioridad'),
                estado: document.getElementById('filtroEstado'),
                semaforo: document.getElementById('filtroSemaforo'),
                interno: document.getElementById('filtroInterno'),
                produccionDesde: document.getElementById('filtroProduccionDesde'),
                produccionHasta: document.getElementById('filtroProduccionHasta'),
                entregaDesde: document.getElementById('filtroEntregaDesde'),
                entregaHasta: document.getElementById('filtroEntregaHasta')
            };

            const filtroStorage = 'controlOperativoFiltros';
            const ordenStorage = 'controlOperativoOrden';

            function normalizarTexto(valor) {
                return String(valor || '')
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .trim();
            }

            function coincideRangoFecha(valor, desde, hasta) {
                if (!desde && !hasta) return true;
                if (!valor) return false;
                if (desde && valor < desde) return false;
                if (hasta && valor > hasta) return false;
                return true;
            }

            function filaCoincideFiltros(fila) {
                const busqueda = normalizarTexto(filtros.busqueda.value);
                const textoBusqueda = normalizarTexto([
                    fila.dataset.pedido,
                    fila.dataset.cliente,
                    fila.dataset.suPedido,
                    fila.dataset.enviarA
                ].join(' '));

                if (busqueda && !textoBusqueda.includes(busqueda)) {
                    return false;
                }

                const prioridad = filtros.prioridad.value;
                if (prioridad !== 'todos') {
                    if (prioridad === 'sin-prioridad') {
                        if (fila.dataset.prioridad) return false;
                    } else if (fila.dataset.prioridad !== prioridad) {
                        return false;
                    }
                }

                const estado = filtros.estado.value;
                if (estado !== 'todos' && fila.dataset.estado !== estado) {
                    return false;
                }

                const semaforo = filtros.semaforo.value;
                if (semaforo !== 'todos' && fila.dataset.semaforo !== semaforo) {
                    return false;
                }

                const interno = filtros.interno.value;
                if (interno !== 'todos' && fila.dataset.pedidoInterno !== interno) {
                    return false;
                }

                if (!coincideRangoFecha(
                        fila.dataset.fechaProduccion,
                        filtros.produccionDesde.value,
                        filtros.produccionHasta.value
                    )) {
                    return false;
                }

                if (!coincideRangoFecha(
                        fila.dataset.fechaEntrega,
                        filtros.entregaDesde.value,
                        filtros.entregaHasta.value
                    )) {
                    return false;
                }

                const avanceFiltro = filtroAvance.value;
                const valor = fila.dataset.avance;

                if (avanceFiltro === 'sin-avance') {
                    if (valor === '') return true;
                    return parseFloat(valor) === 0;
                }

                if (valor === '') {
                    return avanceFiltro === 'todos';
                }

                const avance = parseFloat(valor);

                if (avanceFiltro === 'pendientes') return avance < 1;
                if (avanceFiltro === 'terminados') return avance >= 1;
                if (avanceFiltro === 'en-proceso') return avance > 0 && avance < 1;

                return true;
            }

            function guardarFiltros() {
                const estado = {
                    busqueda: filtros.busqueda.value,
                    prioridad: filtros.prioridad.value,
                    estado: filtros.estado.value,
                    semaforo: filtros.semaforo.value,
                    interno: filtros.interno.value,
                    produccionDesde: filtros.produccionDesde.value,
                    produccionHasta: filtros.produccionHasta.value,
                    entregaDesde: filtros.entregaDesde.value,
                    entregaHasta: filtros.entregaHasta.value,
                    avance: filtroAvance.value
                };

                localStorage.setItem(filtroStorage, JSON.stringify(estado));
            }

            function cargarFiltros() {
                const guardado = localStorage.getItem(filtroStorage);

                filtroAvance.value = 'todos';

                if (!guardado) return;

                try {
                    const estado = JSON.parse(guardado);

                    if (typeof estado.busqueda === 'string') filtros.busqueda.value = estado.busqueda;
                    if (filtros.prioridad.querySelector(`option[value="${CSS.escape(estado.prioridad || 'todos')}"]`)) filtros.prioridad.value = estado.prioridad;
                    if (filtros.estado.querySelector(`option[value="${CSS.escape(estado.estado || 'todos')}"]`)) filtros.estado.value = estado.estado;
                    if (filtros.semaforo.querySelector(`option[value="${CSS.escape(estado.semaforo || 'todos')}"]`)) filtros.semaforo.value = estado.semaforo;
                    if (filtros.interno.querySelector(`option[value="${CSS.escape(estado.interno || 'todos')}"]`)) filtros.interno.value = estado.interno;
                    if (typeof estado.produccionDesde === 'string') filtros.produccionDesde.value = estado.produccionDesde;
                    if (typeof estado.produccionHasta === 'string') filtros.produccionHasta.value = estado.produccionHasta;
                    if (typeof estado.entregaDesde === 'string') filtros.entregaDesde.value = estado.entregaDesde;
                    if (typeof estado.entregaHasta === 'string') filtros.entregaHasta.value = estado.entregaHasta;

                    if (estado.avance && filtroAvance.querySelector(`option[value="${CSS.escape(estado.avance)}"]`)) {
                        filtroAvance.value = estado.avance;
                    }
                } catch (error) {
                    console.error('Error al cargar filtros:', error);
                    localStorage.removeItem(filtroStorage);
                }
            }

            function actualizarResumenFiltros(visibles) {
                contadorFiltroAvanzado.textContent = visibles + ' de ' + filasPedidos.length + ' pedidos';

                const activos = [];
                const agregarFiltro = (clave, etiqueta, detalle, limpiar) => {
                    activos.push({ clave, etiqueta, detalle, limpiar });
                };
                const textoSeleccionado = campo => campo.selectedOptions[0]?.textContent.trim() || campo.value;
                const fechaCorta = valor => {
                    if (!valor) return '';
                    const partes = valor.split('-');
                    return partes.length === 3 ? `${partes[2]}/${partes[1]}/${partes[0]}` : valor;
                };

                if (filtros.busqueda.value.trim()) {
                    agregarFiltro('busqueda', 'Búsqueda', filtros.busqueda.value.trim(), () => { filtros.busqueda.value = ''; });
                }
                if (filtros.prioridad.value !== 'todos') {
                    agregarFiltro('prioridad', 'Prioridad', textoSeleccionado(filtros.prioridad), () => { filtros.prioridad.value = 'todos'; });
                }
                if (filtros.estado.value !== 'todos') {
                    agregarFiltro('estado', 'Estado', textoSeleccionado(filtros.estado), () => { filtros.estado.value = 'todos'; });
                }
                if (filtros.semaforo.value !== 'todos') {
                    agregarFiltro('semaforo', 'Semáforo', textoSeleccionado(filtros.semaforo), () => { filtros.semaforo.value = 'todos'; });
                }
                if (filtros.interno.value !== 'todos') {
                    agregarFiltro('interno', 'Pedido interno', textoSeleccionado(filtros.interno), () => { filtros.interno.value = 'todos'; });
                }
                if (filtros.produccionDesde.value || filtros.produccionHasta.value) {
                    const desde = fechaCorta(filtros.produccionDesde.value);
                    const hasta = fechaCorta(filtros.produccionHasta.value);
                    const detalle = desde && hasta ? `${desde} – ${hasta}` : desde ? `desde ${desde}` : `hasta ${hasta}`;
                    agregarFiltro('produccion', 'Producción', detalle, () => {
                        filtros.produccionDesde.value = '';
                        filtros.produccionHasta.value = '';
                    });
                }
                if (filtros.entregaDesde.value || filtros.entregaHasta.value) {
                    const desde = fechaCorta(filtros.entregaDesde.value);
                    const hasta = fechaCorta(filtros.entregaHasta.value);
                    const detalle = desde && hasta ? `${desde} – ${hasta}` : desde ? `desde ${desde}` : `hasta ${hasta}`;
                    agregarFiltro('entrega', 'Entrega', detalle, () => {
                        filtros.entregaDesde.value = '';
                        filtros.entregaHasta.value = '';
                    });
                }
                if (filtroAvance.value !== 'todos') {
                    agregarFiltro('avance', 'Avance', textoSeleccionado(filtroAvance), () => { filtroAvance.value = 'todos'; });
                }

                resumenFiltros.replaceChildren();
                if (!activos.length) {
                    const vacio = document.createElement('span');
                    vacio.textContent = 'Sin filtros aplicados';
                    resumenFiltros.appendChild(vacio);
                    return;
                }

                const titulo = document.createElement('span');
                titulo.className = 'mr-1';
                titulo.textContent = 'Filtros activos:';
                resumenFiltros.appendChild(titulo);

                activos.forEach(filtro => {
                    const chip = document.createElement('button');
                    chip.type = 'button';
                    chip.className = 'inline-flex items-center gap-1 rounded-full border border-blue-100 bg-blue-50 px-2 py-1 text-[11px] font-medium text-blue-800 hover:border-blue-300 hover:bg-blue-100';
                    chip.setAttribute('aria-label', `Quitar filtro ${filtro.etiqueta}: ${filtro.detalle}`);
                    chip.title = `Quitar filtro ${filtro.etiqueta}`;

                    const texto = document.createElement('span');
                    texto.textContent = `${filtro.etiqueta}: ${filtro.detalle}`;
                    const cerrar = document.createElement('span');
                    cerrar.setAttribute('aria-hidden', 'true');
                    cerrar.className = 'font-bold';
                    cerrar.textContent = '×';

                    chip.append(texto, cerrar);
                    chip.addEventListener('click', function() {
                        filtro.limpiar();
                        aplicarTodosLosFiltros();
                    });
                    resumenFiltros.appendChild(chip);
                });
            }

            function aplicarTodosLosFiltros() {
                let visibles = 0;

                filasPedidos.forEach(function(fila) {
                    const mostrar = filaCoincideFiltros(fila);
                    fila.style.display = mostrar ? '' : 'none';
                    if (mostrar) visibles++;
                });

                actualizarResumenFiltros(visibles);
                guardarFiltros();
            }

            Object.values(filtros).forEach(function(campo) {
                campo.addEventListener('input', aplicarTodosLosFiltros);
                campo.addEventListener('change', aplicarTodosLosFiltros);
            });

            filtroAvance.addEventListener('change', aplicarTodosLosFiltros);

            document.getElementById('limpiarFiltros').addEventListener('click', function() {
                filtros.busqueda.value = '';
                filtros.prioridad.value = 'todos';
                filtros.estado.value = 'todos';
                filtros.semaforo.value = 'todos';
                filtros.interno.value = 'todos';
                filtros.produccionDesde.value = '';
                filtros.produccionHasta.value = '';
                filtros.entregaDesde.value = '';
                filtros.entregaHasta.value = '';
                filtroAvance.value = 'todos';
                localStorage.removeItem(filtroStorage);
                aplicarTodosLosFiltros();
            });

            const botonExportarXlsx = document.getElementById('exportarPedidosXlsx');
            botonExportarXlsx.addEventListener('click', async function() {
                const pedidoIds = Array.from(document.querySelectorAll('#tablaPedidos tbody tr.fila-pedido'))
                    .filter(fila => getComputedStyle(fila).display !== 'none')
                    .map(fila => fila.dataset.pedidoId)
                    .filter(Boolean);

                if (!pedidoIds.length) {
                    alert('No hay pedidos que coincidan con los filtros actuales.');
                    return;
                }

                botonExportarXlsx.disabled = true;
                botonExportarXlsx.textContent = 'Exportando...';

                try {
                    const response = await fetch(botonExportarXlsx.dataset.url, {
                        method: 'POST',
                        headers: window.encabezadosSolicitudJsonControl('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/json'),
                        body: JSON.stringify({ pedido_ids: pedidoIds })
                    });

                    if (!response.ok) {
                        const error = await response.json().catch(() => ({}));
                        throw new Error(error.message || 'No se pudo exportar el archivo.');
                    }

                    const archivo = URL.createObjectURL(await response.blob());
                    const enlace = document.createElement('a');
                    enlace.href = archivo;
                    enlace.download = 'pedidos_control_operativo.xlsx';
                    document.body.appendChild(enlace);
                    enlace.click();
                    enlace.remove();
                    setTimeout(() => URL.revokeObjectURL(archivo), 1000);
                } catch (error) {
                    alert(error.message);
                } finally {
                    botonExportarXlsx.disabled = false;
                    botonExportarXlsx.textContent = 'Exportar .xlsx';
                }
            });

            // ================================================================
            // ORDENAMIENTO POR COLUMNA
            // ================================================================

            const botonesOrden =
                Array.from(document.querySelectorAll('.btn-ordenar'));

            const tbody =
                document.querySelector('#tablaPedidos tbody');

            let ordenActual = {
                columna: null,
                direccion: null
            };

            const columnasNumericas = new Set([
                'avance',
                'dias',
                'proceso-menor-avance',
            ]);

            const columnasFecha = new Set([
                'fecha-entrega',
                'fecha-produccion'
            ]);

            const atributosOrden = {
                'pedido': 'pedido',
                'cliente': 'cliente',
                'su-pedido': 'suPedido',
                'enviar-a': 'enviarA',
                'fecha-entrega': 'fechaEntrega',
                'abastecimiento': 'abastecimiento',
                'estado-operativo': 'estado',
                'fecha-produccion': 'fechaProduccion',
                'avance': 'avance',
                'proceso-menor-avance': 'procesoMenorAvance',
                'resultado-entrega': null,
                'semaforo': 'semaforo',
                'dias': 'dias',
                'comentario': null,
            };

            function obtenerValorOrden(fila, columna) {
                if (columna === 'comentario') {
                    const actividades = fila.querySelectorAll('.actividad-nombre');

                    return Array.from(actividades)
                        .map(function(elemento) {
                            return elemento.textContent.trim();
                        })
                        .join(' | ');
                }
                if (columna === 'resultado-entrega') {
                    const selector = fila.querySelector('.resultado-entrega-input');
                    return selector ? selector.value : '';
                }
                const atributo = atributosOrden[columna];
                return atributo ? (fila.dataset[atributo] || '') : '';
            }

            function compararValores(a, b, columna) {
                if (columnasNumericas.has(columna)) {
                    const na = a === '' ? -Infinity : parseFloat(a);
                    const nb = b === '' ? -Infinity : parseFloat(b);
                    return na - nb;
                }

                if (columnasFecha.has(columna)) {
                    const na = a ? new Date(a + 'T00:00:00').getTime() : -Infinity;
                    const nb = b ? new Date(b + 'T00:00:00').getTime() : -Infinity;
                    return na - nb;
                }

                return String(a || '').localeCompare(
                    String(b || ''),
                    'es', {
                        numeric: true,
                        sensitivity: 'base'
                    }
                );
            }

            function actualizarIconosOrden() {
                botonesOrden.forEach(function(boton) {
                    const columna = boton.dataset.sort;
                    boton.dataset.direccion = '';

                    const arriba = boton.querySelector('.flecha-arriba');
                    const abajo = boton.querySelector('.flecha-abajo');

                    if (arriba) arriba.style.opacity = '.35';
                    if (abajo) abajo.style.opacity = '.35';

                    if (ordenActual.columna === columna) {
                        boton.dataset.direccion = ordenActual.direccion;
                        if (ordenActual.direccion === 'asc' && arriba) {
                            arriba.style.opacity = '1';
                            arriba.style.color = '#2563eb';
                        }
                        if (ordenActual.direccion === 'desc' && abajo) {
                            abajo.style.opacity = '1';
                            abajo.style.color = '#2563eb';
                        }
                    }
                });
            }

            function ordenarPorColumna(columna) {
                if (ordenActual.columna === columna) {
                    ordenActual.direccion = ordenActual.direccion === 'asc' ? 'desc' : 'asc';
                } else {
                    ordenActual.columna = columna;
                    ordenActual.direccion = 'asc';
                }

                const filas = Array.from(tbody.querySelectorAll('.fila-pedido'));
                const multiplicador = ordenActual.direccion === 'asc' ? 1 : -1;

                filas.sort(function(filaA, filaB) {
                    const a = obtenerValorOrden(filaA, columna);
                    const b = obtenerValorOrden(filaB, columna);
                    return compararValores(a, b, columna) * multiplicador;
                });

                filas.forEach(function(fila) {
                    tbody.appendChild(fila);
                });

                localStorage.setItem(
                    ordenStorage,
                    JSON.stringify(ordenActual)
                );

                actualizarIconosOrden();
            }

            botonesOrden.forEach(function(boton) {
                boton.addEventListener('click', function() {
                    ordenarPorColumna(this.dataset.sort);
                });
            });

            function cargarOrden() {
                const guardado = localStorage.getItem(ordenStorage);
                if (!guardado) {
                    actualizarIconosOrden();
                    return;
                }

                try {
                    const orden = JSON.parse(guardado);
                    const boton = botonesOrden.find(function(item) {
                        return item.dataset.sort === orden.columna;
                    });

                    if (
                        boton &&
                        (orden.direccion === 'asc' || orden.direccion === 'desc')
                    ) {
                        ordenActual = {
                            columna: orden.columna,
                            direccion: orden.direccion === 'desc' ? 'asc' : 'desc'
                        };
                        ordenarPorColumna(ordenActual.columna);
                    }
                } catch (error) {
                    console.error('Error al cargar ordenamiento:', error);
                    localStorage.removeItem(ordenStorage);
                }
            }

            cargarFiltros();
            aplicarTodosLosFiltros();
            cargarOrden();


            // ================================================================
            // AJUSTE AUTOMÁTICO DE CAMPOS
            // ================================================================

            function ajustarAnchoCampo(campo) {

                const canvas = document.createElement('canvas');
                const contexto = canvas.getContext('2d');

                const estilos = window.getComputedStyle(campo);

                contexto.font =
                    estilos.fontWeight + ' ' +
                    estilos.fontSize + ' ' +
                    estilos.fontFamily;

                const texto =
                    campo.value ||
                    campo.placeholder ||
                    '';

                const anchoTexto =
                    contexto.measureText(texto).width;

                const anchoMinimo = 180;
                const anchoMaximo = 400;
                const espacioExtra = 35;

                const nuevoAncho = Math.min(
                    anchoMaximo,
                    Math.max(
                        anchoMinimo,
                        Math.ceil(anchoTexto + espacioExtra)
                    )
                );

                campo.style.width = nuevoAncho + 'px';
            }


            document
                .querySelectorAll('.campo-ajustable')
                .forEach(function(campo) {

                    ajustarAnchoCampo(campo);

                    campo.addEventListener('input', function() {
                        ajustarAnchoCampo(this);
                    });

                });


            // ================================================================
            // ACTIVIDADES
            // ================================================================

            const modalActividad =
                document.getElementById('modalActividad');

            if (modalActividad) {
                document.body.appendChild(modalActividad);
            }

            const inputActividad =
                document.getElementById('inputActividad');

            const inputResponsableActividad =
                document.getElementById('inputResponsableActividad');

            const modalActividadPedido =
                document.getElementById('modalActividadPedido');

            const cerrarModalActividad =
                document.getElementById('cerrarModalActividad');

            const cancelarModalActividad =
                document.getElementById('cancelarModalActividad');

            const guardarActividadModal =
                document.getElementById('guardarActividadModal');

            const tituloModalActividad =
                document.getElementById('tituloModalActividad');

            const estadoActividadModal =
                document.getElementById('estadoActividadModal');

            const modalActividadCompletada =
                document.getElementById('modalActividadCompletada');


            // Actividad que se está editando
            let actividadEditando = null;

            // Actividad actualmente seleccionada
            let botonActividadActual = null;


            // ---------------------------------------------------------------
            // ABRIR MODAL
            // ---------------------------------------------------------------

            document
                .querySelectorAll('.agregar-actividad')
                .forEach(function(boton) {

                    boton.addEventListener('click', function() {

                        // MODO CREAR
                        actividadEditando = null;

                        botonActividadActual = this;

                        const fila =
                            this.closest('.fila-pedido');

                        const pedido =
                            fila.dataset.pedido;

                        modalActividadPedido.textContent =
                            pedido;

                        tituloModalActividad.textContent =
                            'Nueva actividad';

                        inputActividad.value = '';

                        inputResponsableActividad.value = '';

                        estadoActividadModal.classList.add(
                            'hidden'
                        );

                        modalActividadCompletada.checked =
                            false;

                        guardarActividadModal.textContent =
                            'Guardar actividad';

                        modalActividad.classList.remove(
                            'hidden'
                        );

                        setTimeout(function() {

                            inputActividad.focus();

                        }, 100);

                    });

                });


            // ================================================================
            // ABRIR ACTIVIDAD EXISTENTE PARA EDITAR
            // ================================================================

            document.addEventListener(
                'click',
                function(event) {

                    const actividad =
                        event.target.closest('.actividad-item');


                    // No hizo clic en una actividad
                    if (!actividad) {
                        return;
                    }


                    // Si hizo clic en el checkbox,
                    // dejamos que el checkbox funcione normalmente
                    if (
                        event.target.classList.contains(
                            'actividad-completada'
                        )
                    ) {
                        return;
                    }


                    // Si hizo clic en eliminar,
                    // dejamos que eliminar funcione
                    if (
                        event.target.classList.contains(
                            'eliminar-actividad'
                        )
                    ) {
                        return;
                    }


                    const fila =
                        actividad.closest('.fila-pedido');


                    const textoActividad =
                        actividad.querySelector(
                            '.actividad-nombre'
                        );


                    const responsableActividad =
                        actividad.querySelector(
                            '.actividad-responsable'
                        );


                    const checkbox =
                        actividad.querySelector(
                            '.actividad-completada'
                        );


                    if (!fila || !textoActividad) {
                        return;
                    }


                    // ------------------------------------------------
                    // GUARDAR ACTIVIDAD QUE ESTAMOS EDITANDO
                    // ------------------------------------------------

                    actividadEditando =
                        actividad;


                    // ------------------------------------------------
                    // PEDIDO
                    // ------------------------------------------------

                    modalActividadPedido.textContent =
                        fila.dataset.pedido;


                    // ------------------------------------------------
                    // TÍTULO
                    // ------------------------------------------------

                    tituloModalActividad.textContent =
                        'Editar actividad';


                    // ------------------------------------------------
                    // ACTIVIDAD
                    // ------------------------------------------------

                    inputActividad.value =
                        textoActividad.textContent.trim();


                    // ------------------------------------------------
                    // RESPONSABLE
                    // ------------------------------------------------

                    inputResponsableActividad.value =
                        responsableActividad ?
                        responsableActividad.textContent
                        .trim()
                        .replace(/^\(|\)$/g, '') :
                        '';


                    // ------------------------------------------------
                    // ESTADO
                    // ------------------------------------------------

                    estadoActividadModal.classList.remove(
                        'hidden'
                    );


                    modalActividadCompletada.checked =
                        checkbox ?
                        checkbox.checked :
                        false;


                    // ------------------------------------------------
                    // BOTÓN
                    // ------------------------------------------------

                    guardarActividadModal.textContent =
                        'Guardar cambios';


                    // ------------------------------------------------
                    // ABRIR MODAL
                    // ------------------------------------------------

                    modalActividad.classList.remove(
                        'hidden'
                    );


                    setTimeout(function() {

                        inputActividad.focus();

                        inputActividad.selectionStart =
                            inputActividad.value.length;

                        inputActividad.selectionEnd =
                            inputActividad.value.length;

                    }, 100);

                }
            );

            // ================================================================
            // ELIMINAR ACTIVIDAD
            // ================================================================

            document.addEventListener('click', function(event) {

                const boton = event.target.closest('.eliminar-actividad');

                if (!boton) {
                    return;
                }

                const actividad = boton.closest('.actividad-item');

                if (!actividad) {
                    return;
                }

                const url = actividad.dataset.urlEliminar;

                if (!url) {
                    console.error(
                        'No se encontró la URL para eliminar la actividad.'
                    );

                    return;
                }

                if (!confirm('¿Eliminar esta actividad?')) {
                    return;
                }

                boton.disabled = true;

                fetch(url, {
                        method: 'PATCH',

                        headers: window.encabezadosSolicitudJsonControl(),

                        body: JSON.stringify({
                            activa: false
                        })
                    })

                    .then(function(response) {

                        if (!response.ok) {
                            throw new Error(
                                'Error al eliminar la actividad.'
                            );
                        }

                        return response.json();

                    })

                    .then(function(data) {

                        if (!data.success) {
                            throw new Error(
                                'No se pudo eliminar la actividad.'
                            );
                        }

                        // Desaparece visualmente
                        actividad.remove();

                    })

                    .catch(function(error) {

                        console.error(error);

                        alert(
                            'No se pudo eliminar la actividad.'
                        );

                        boton.disabled = false;

                    });

            });

            // ---------------------------------------------------------------
            // CERRAR MODAL
            // ---------------------------------------------------------------

            function cerrarModalActividadFuncion() {

                modalActividad.classList.add('hidden');

                inputActividad.value = '';

                inputResponsableActividad.value = '';

                modalActividadCompletada.checked =
                    false;

                estadoActividadModal.classList.add(
                    'hidden'
                );

                actividadEditando = null;

                botonActividadActual = null;

                guardarActividadModal.textContent =
                    'Guardar actividad';

            }


            cerrarModalActividad.addEventListener(
                'click',
                cerrarModalActividadFuncion
            );


            cancelarModalActividad.addEventListener(
                'click',
                cerrarModalActividadFuncion
            );


            // ---------------------------------------------------------------
            // CERRAR AL HACER CLICK EN EL FONDO
            // ---------------------------------------------------------------

            modalActividad.addEventListener(
                'click',
                function(event) {

                    if (event.target === modalActividad) {

                        cerrarModalActividadFuncion();

                    }

                }
            );


            // ---------------------------------------------------------------
            // ESCAPE PARA CERRAR
            // ---------------------------------------------------------------

            document.addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Escape' &&
                        !modalActividad.classList.contains('hidden')
                    ) {

                        cerrarModalActividadFuncion();

                    }

                }
            );


            // ---------------------------------------------------------------
            // GUARDAR ACTIVIDAD
            // ---------------------------------------------------------------

            guardarActividadModal.addEventListener(
                'click',
                function() {

                    const actividad =
                        inputActividad.value.trim();


                    // Validar
                    if (!actividad) {

                        inputActividad.focus();

                        return;
                    }

                    if (actividadEditando) {

                        actualizarActividadDesdeModal(
                            actividadEditando,
                            actividad
                        );

                        return;

                    }


                    if (!botonActividadActual) {

                        return;
                    }


                    const fila =
                        botonActividadActual.closest('.fila-pedido');

                    const celda =
                        botonActividadActual.closest('.col-comentario');

                    const url =
                        fila.dataset.urlActividad;


                    if (!url) {

                        console.error(
                            'No se encontró la URL para crear la actividad.'
                        );

                        return;
                    }


                    // Desactivar botón
                    guardarActividadModal.disabled = true;

                    guardarActividadModal.textContent =
                        'Guardando...';


                    fetch(url, {

                            method: 'POST',

                            headers: window.encabezadosSolicitudJsonControl(),

                            body: JSON.stringify({

                                actividad: actividad,

                                completada: modalActividadCompletada.checked,

                                responsable: inputResponsableActividad.value.trim() || null

                            })

                        })

                        .then(function(response) {

                            if (!response.ok) {

                                throw new Error(
                                    'Error al crear la actividad.'
                                );

                            }

                            return response.json();

                        })

                        .then(function(data) {

                            if (!data.success) {

                                throw new Error(
                                    'No se pudo crear la actividad.'
                                );

                            }


                            // ---------------------------------------------------
                            // CREAR LA ACTIVIDAD VISUALMENTE
                            // ---------------------------------------------------

                            const contenedor =
                                celda.querySelector(
                                    '.actividades-container'
                                );


                            // Quitar "Sin actividades"
                            const sinActividades =
                                contenedor.querySelector(
                                    '.sin-actividades'
                                );

                            if (sinActividades) {

                                sinActividades.remove();

                            }


                            // Crear elemento
                            const actividadElemento =
                                document.createElement('div');

                            actividadElemento.className =
                                'actividad-item flex items-start gap-2 p-2 rounded-md border border-gray-200 bg-gray-50';

                            actividadElemento.dataset.actividadId =
                                data.actividad.id;

                            actividadElemento.dataset.urlActualizar =
                                data.url_actualizar;

                            actividadElemento.dataset.urlEliminar =
                                data.url_eliminar;

                            actividadElemento.innerHTML = `

                                <input
                                    type="checkbox"
                                    class="actividad-completada mt-1 w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500">

                                <div
                                    class="actividad-texto flex-1 min-w-0 text-sm cursor-pointer">

                                    <div class="actividad-nombre"></div>

                                    <div class="actividad-responsable text-xs text-gray-500 mt-1 hidden"></div>

                                </div>

                                <button
                                    type="button"
                                    class="eliminar-actividad text-red-500 hover:text-red-700 px-1"
                                    title="Eliminar actividad">
                                    ×
                                </button>
                            `;


                            const textoActividad =
                                actividadElemento.querySelector(
                                    '.actividad-nombre'
                                );

                            if (textoActividad) {

                                textoActividad.textContent =
                                    data.actividad.actividad;

                            }


                            const responsableElemento =
                                actividadElemento.querySelector(
                                    '.actividad-responsable'
                                );

                            if (responsableElemento) {

                                if (data.actividad.responsable) {

                                    responsableElemento.textContent =
                                        `(${data.actividad.responsable})`;

                                    responsableElemento.classList.remove(
                                        'hidden'
                                    );

                                } else {

                                    responsableElemento.textContent = '';

                                    responsableElemento.classList.add(
                                        'hidden'
                                    );

                                }

                            }


                            contenedor.appendChild(
                                actividadElemento
                            );


                            // Cerrar modal
                            cerrarModalActividadFuncion();

                        })

                        .catch(function(error) {

                            console.error(error);

                            alert(
                                'No se pudo guardar la actividad.'
                            );

                        })

                        .finally(function() {

                            guardarActividadModal.disabled =
                                false;

                            guardarActividadModal.textContent =
                                'Guardar actividad';

                        });

                }
            );

            function actualizarActividadDesdeModal(
                actividadElemento,
                texto
            ) {

                const url =
                    actividadElemento.dataset.urlActualizar;


                if (!url) {

                    console.error(
                        'No se encontró la URL para actualizar la actividad.'
                    );

                    return;
                }


                const completada =
                    modalActividadCompletada.checked;


                const responsable =
                    inputResponsableActividad.value.trim() || null;


                guardarActividadModal.disabled =
                    true;

                guardarActividadModal.textContent =
                    'Guardando...';


                fetch(url, {

                        method: 'PATCH',

                        headers: window.encabezadosSolicitudJsonControl(),

                        body: JSON.stringify({

                            actividad: actividad,

                            completada: modalActividadCompletada.checked,

                            responsable: inputResponsableActividad.value.trim() || null

                        })

                    })

                    .then(function(response) {

                        if (!response.ok) {

                            throw new Error(
                                'Error al actualizar la actividad.'
                            );

                        }

                        return response.json();

                    })

                    .then(function(data) {

                        if (!data.success) {

                            throw new Error(
                                'No se pudo actualizar la actividad.'
                            );

                        }


                        // =========================================================
                        // ACTUALIZAR ACTIVIDAD VISUALMENTE
                        // =========================================================

                        const textoActividad =
                            actividadElemento.querySelector(
                                '.actividad-nombre'
                            );


                        if (textoActividad) {

                            textoActividad.textContent =
                                data.actividad.actividad;

                        }


                        // =========================================================
                        // ACTUALIZAR RESPONSABLE VISUALMENTE
                        // =========================================================

                        let responsableElemento =
                            actividadElemento.querySelector(
                                '.actividad-responsable'
                            );


                        // Si existe responsable
                        if (data.actividad.responsable) {

                            // Si todavía no existe el elemento,
                            // lo creamos
                            if (!responsableElemento) {

                                const contenedorTexto =
                                    actividadElemento.querySelector(
                                        '.actividad-texto'
                                    );

                                responsableElemento =
                                    document.createElement('div');

                                responsableElemento.className =
                                    'actividad-responsable text-xs text-gray-500 mt-1';

                                contenedorTexto.appendChild(
                                    responsableElemento
                                );
                            }


                            responsableElemento.textContent =
                                `(${data.actividad.responsable})`;

                            responsableElemento.classList.remove(
                                'hidden'
                            );


                        } else {

                            // Si se dejó vacío
                            if (responsableElemento) {

                                responsableElemento.textContent = '';

                                responsableElemento.classList.add(
                                    'hidden'
                                );
                            }
                        }


                        // =========================================================
                        // ACTUALIZAR CHECKBOX
                        // =========================================================

                        const checkbox =
                            actividadElemento.querySelector(
                                '.actividad-completada'
                            );


                        if (checkbox) {

                            checkbox.checked =
                                data.actividad.completada;
                        }


                        // =========================================================
                        // CERRAR MODAL
                        // =========================================================

                        cerrarModalActividadFuncion();

                    })

                    .catch(function(error) {

                        console.error(error);

                        alert(
                            'No se pudo actualizar la actividad.'
                        );

                    })

                    .finally(function() {

                        guardarActividadModal.disabled =
                            false;

                        guardarActividadModal.textContent =
                            'Guardar cambios';

                    });

            }


            // ================================================================
            // FECHA PRODUCCIÓN
            // ================================================================

            const fechasProduccion =
                document.querySelectorAll(
                    '.fecha-produccion-input'
                );

            fechasProduccion.forEach(function(input) {

                input.addEventListener('change', function() {

                    const campo = this;

                    const url = campo.dataset.url;

                    const fecha = campo.value;

                    const fechaAnterior = campo.dataset.valorAnterior ?? campo.defaultValue;

                    window.mostrarEstadoGuardadoControl(campo, 'guardando');

                    fetch(url, {

                            method: 'PATCH',

                            headers: window.encabezadosSolicitudJsonControl(),

                            body: JSON.stringify({

                                fecha_produccion: fecha || null

                            })

                        })

                        .then(response => {

                            if (!response.ok) {

                                throw new Error(
                                    'Error al guardar la fecha'
                                );

                            }

                            return response.json();

                        })

                        .then(data => {

                            if (!data.success) {

                                throw new Error(
                                    'No se pudo guardar la fecha'
                                );

                            }

                            /*
                            |--------------------------------------------------------------------------
                            | GUARDADO CORRECTO
                            |--------------------------------------------------------------------------
                            |
                            | No recargamos la página.
                            | El usuario puede seleccionar otra fecha inmediatamente.
                            |
                            */

                            const filaFechaActualizada = campo.closest('.fila-pedido');
                            if (filaFechaActualizada) {
                                filaFechaActualizada.dataset.fechaProduccion = campo.value || '';
                            }
                            campo.dataset.valorAnterior = campo.value;
                            document.dispatchEvent(new CustomEvent('fecha-produccion-actualizada'));
                            window.mostrarEstadoGuardadoControl(campo, 'guardado');

                        })

                        .catch(error => {

                            console.error(error);

                            campo.value = fechaAnterior;
                            window.mostrarEstadoGuardadoControl(campo, 'error', error.message);

                            alert(
                                'No se pudo guardar la Fecha de Producción.'
                            );

                        });

                });

            });


            // ================================================================
            // MOSTRAR / OCULTAR ACTIVIDADES TERMINADAS
            // ================================================================

            function actualizarBotonActividadesTerminadas(contenedor, abiertoActual) {
                if (!contenedor) return;

                const boton = contenedor.parentElement.querySelector('.toggle-actividades-terminadas');
                const terminadas = Array.from(contenedor.querySelectorAll('.actividad-item'))
                    .filter(item => item.dataset.completada === '1');

                if (!boton) return;

                boton.textContent = (abiertoActual ? 'Ocultar terminadas (' : 'Mostrar terminadas (') + terminadas.length + ')';
                boton.setAttribute('aria-expanded', abiertoActual ? 'true' : 'false');
                boton.dataset.abierto = abiertoActual ? '1' : '0';
                boton.classList.toggle('hidden', terminadas.length === 0);

                terminadas.forEach(item => {
                    item.classList.toggle('hidden', !abiertoActual);
                });
            }

            document.addEventListener('click', function(event) {
                const boton = event.target.closest('.toggle-actividades-terminadas');
                if (!boton) return;

                const celda = boton.closest('.col-comentario');
                const contenedor = celda ? celda.querySelector('.actividades-container') : null;
                if (!contenedor) return;

                const abrir = boton.dataset.abierto !== '1';
                actualizarBotonActividadesTerminadas(contenedor, abrir);
            });

            // ================================================================
            // COMPLETAR / DESCOMPLETAR ACTIVIDAD
            // ================================================================

            document.addEventListener('change', function(event) {

                if (
                    !event.target.classList.contains(
                        'actividad-completada'
                    )
                ) {
                    return;
                }


                const checkbox =
                    event.target;

                const actividadElemento =
                    checkbox.closest('.actividad-item');


                if (!actividadElemento) {
                    return;
                }


                const url =
                    actividadElemento.dataset.urlActualizar;


                if (!url) {

                    console.error(
                        'No se encontró la URL para actualizar la actividad.'
                    );

                    checkbox.checked = !checkbox.checked;

                    return;
                }


                const completada =
                    checkbox.checked;


                // Evitar varios clics mientras guarda
                checkbox.disabled = true;


                fetch(url, {

                        method: 'PATCH',

                        headers: window.encabezadosSolicitudJsonControl(),

                        body: JSON.stringify({

                            completada: completada

                        })

                    })

                    .then(function(response) {

                        if (!response.ok) {

                            throw new Error(
                                'Error al actualizar la actividad.'
                            );

                        }

                        return response.json();

                    })

                    .then(function(data) {

                        if (!data.success) {

                            throw new Error(
                                'No se pudo actualizar la actividad.'
                            );

                        }


                        // Mantener la actividad en el DOM para poder consultarla después.
                        // Las completadas se ocultan por defecto, pero se pueden mostrar con el botón.
                        const contenedorActividades = actividadElemento.closest('.actividades-container');

                        if (completada) {
                            actividadElemento.dataset.completada = '1';
                            actividadElemento.classList.add('actividad-terminada', 'opacity-60');
                            actividadElemento.classList.add('hidden');
                        } else {
                            actividadElemento.dataset.completada = '0';
                            actividadElemento.classList.remove('actividad-terminada', 'opacity-60', 'hidden');
                        }

                        actualizarBotonActividadesTerminadas(contenedorActividades, false);

                    })

                    .catch(function(error) {

                        console.error(error);


                        // Regresar checkbox a su estado anterior
                        checkbox.checked = !completada;


                        alert(
                            'No se pudo actualizar la actividad.'
                        );

                    })

                    .finally(function() {

                        checkbox.disabled = false;

                    });

            });

        });

        // ================================================================
        // ABASTECIMIENTO
        // ================================================================
        const modalAbastecimiento = document.getElementById('modalAbastecimiento');
        const motivoAbastecimiento = document.getElementById('motivoAbastecimiento');
        const comentarioAbastecimiento = document.getElementById('comentarioAbastecimiento');
        const responsableAbastecimiento = document.getElementById('responsableAbastecimiento');
        const fechaLimiteAbastecimiento = document.getElementById('fechaLimiteAbastecimiento');
        const guardarModalAbastecimiento = document.getElementById('guardarModalAbastecimiento');
        const errorAbastecimiento = document.getElementById('errorAbastecimiento');
        let abastecimientoSelectActivo = null;

        if (modalAbastecimiento) {
            document.body.appendChild(modalAbastecimiento);
            modalAbastecimiento.style.position = 'fixed';
            modalAbastecimiento.style.inset = '0';
            modalAbastecimiento.style.zIndex = '1000000';
            modalAbastecimiento.style.alignItems = 'center';
            modalAbastecimiento.style.justifyContent = 'center';
            modalAbastecimiento.style.backgroundColor = 'rgba(2, 6, 23, 0.5)';
            modalAbastecimiento.style.padding = '1.5rem';
            modalAbastecimiento.style.display = 'none';
        }

        const etiquetasAbastecimiento = {
            otra_opcion: 'Ninguno',
            apartado: 'Preparado',
            parcialidad: 'Parcialidad',
            entregado: 'Entregado',
            faltante_mp: 'Pendiente'
        };
        const motivosPorAbastecimiento = {
            otra_opcion: ['Falta orden de compra', 'Falta requisición de materiales', 'Otro'],
            apartado: ['Material separado en almacén', 'Otro'],
            parcialidad: ['Surtido parcial a producción', 'Otro'],
            entregado: ['Surtido completo a producción', 'Otro'],
            faltante_mp: ['Pendiente de surtir desde almacén', 'Otro']
        };

        function cerrarModalAbastecimiento(restaurarSelect = true) {
            if (restaurarSelect && abastecimientoSelectActivo) {
                abastecimientoSelectActivo.value = abastecimientoSelectActivo.dataset.valorAnterior ?? '';
            }
            modalAbastecimiento.classList.add('hidden');
            modalAbastecimiento.style.display = 'none';
            comentarioAbastecimiento.value = '';
            responsableAbastecimiento.value = '';
            fechaLimiteAbastecimiento.value = '';
            errorAbastecimiento.textContent = '';
            errorAbastecimiento.classList.add('hidden');
            guardarModalAbastecimiento.disabled = false;
            guardarModalAbastecimiento.textContent = 'Guardar y agregar actividad';
            abastecimientoSelectActivo = null;
        }

        function renderActividadAbastecimiento(fila, data) {
            if (!fila || !data.actividad) return;
            const contenedor = fila.querySelector('.actividades-container');
            if (!contenedor) return;
            contenedor.querySelector('.sin-actividades')?.remove();

            const item = document.createElement('div');
            const completada = Boolean(data.actividad.completada);
            item.className = 'actividad-item flex items-start gap-2 p-2 rounded-md border border-gray-200 bg-gray-50';
            if (completada) item.classList.add('actividad-terminada', 'hidden', 'opacity-60');
            item.dataset.completada = completada ? '1' : '0';
            item.dataset.actividadId = data.actividad.id;
            item.dataset.urlActualizar = data.url_actualizar;
            item.dataset.urlEliminar = data.url_eliminar;

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.className = 'actividad-completada mt-1 w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500';
            checkbox.checked = completada;

            const texto = document.createElement('div');
            texto.className = 'actividad-texto flex-1 min-w-0 text-sm cursor-pointer';
            const nombre = document.createElement('div');
            nombre.className = 'actividad-nombre';
            nombre.textContent = data.actividad.actividad;
            texto.appendChild(nombre);

            if (data.actividad.responsable) {
                const responsable = document.createElement('div');
                responsable.className = 'actividad-responsable text-xs text-gray-500 mt-1';
                responsable.textContent = `(${data.actividad.responsable})`;
                texto.appendChild(responsable);
            }
            if (data.actividad.fecha_limite) {
                const fecha = document.createElement('div');
                fecha.className = 'actividad-fecha text-xs text-slate-500 mt-1';
                const iso = String(data.actividad.fecha_limite).slice(0, 10);
                fecha.textContent = `Fecha límite: ${iso.split('-').reverse().join('/')}`;
                texto.appendChild(fecha);
            }

            const eliminar = document.createElement('button');
            eliminar.type = 'button';
            eliminar.className = 'eliminar-actividad text-red-500 hover:text-red-700 px-1';
            eliminar.title = 'Eliminar actividad';
            eliminar.textContent = '×';

            item.append(checkbox, texto, eliminar);
            contenedor.appendChild(item);

            const terminadas = Array.from(contenedor.querySelectorAll('.actividad-item'))
                .filter(actividad => actividad.dataset.completada === '1').length;
            const botonTerminadas = contenedor.parentElement.querySelector('.toggle-actividades-terminadas');
            if (botonTerminadas) {
                botonTerminadas.textContent = `Mostrar terminadas (${terminadas})`;
                botonTerminadas.dataset.abierto = '0';
                botonTerminadas.setAttribute('aria-expanded', 'false');
                botonTerminadas.classList.toggle('hidden', terminadas === 0);
            }
        }

        function guardarCambioAbastecimiento(select, payload, revertirEnError = true) {
            const valorAnterior = select.dataset.valorAnterior ?? '';
            const fila = select.closest('.fila-pedido');
            select.disabled = true;
            window.mostrarEstadoGuardadoControl(select, 'guardando');

            return fetch(select.dataset.url, {
                method: 'PATCH',
                headers: window.encabezadosSolicitudJsonControl(),
                body: JSON.stringify(payload)
            }).then(async function(response) {
                const data = await response.json();
                if (!response.ok || !data.success) {
                    const mensaje = data.message || Object.values(data.errors || {}).flat().join(' ') || 'No se pudo guardar el abastecimiento.';
                    throw new Error(mensaje);
                }
                select.dataset.valorAnterior = select.value;
                if (fila) fila.dataset.abastecimiento = select.value;
                renderActividadAbastecimiento(fila, data);
                window.mostrarEstadoGuardadoControl(select, 'guardado');
                return data;
            }).catch(function(error) {
                if (revertirEnError) select.value = valorAnterior;
                window.mostrarEstadoGuardadoControl(select, 'error', error.message);
                throw error;
            }).finally(function() {
                select.disabled = false;
            });
        }

        document.querySelectorAll('.abastecimiento-input').forEach(function(select) {
            select.dataset.valorAnterior = select.value;
            select.addEventListener('change', function() {
                const nuevoValor = this.value;
                if (!nuevoValor) {
                    guardarCambioAbastecimiento(this, { abastecimiento: null })
                        .catch(error => alert(error.message));
                    return;
                }

                if (nuevoValor === 'entregado') {
                    guardarCambioAbastecimiento(this, {
                        abastecimiento: nuevoValor,
                        motivo: 'Surtido completo a producción'
                    }).catch(error => alert(error.message));
                    return;
                }

                abastecimientoSelectActivo = this;
                document.getElementById('modalAbastecimientoPedido').textContent = this.closest('.fila-pedido')?.dataset.pedido || '';
                document.getElementById('modalAbastecimientoEstado').textContent = etiquetasAbastecimiento[nuevoValor] || nuevoValor;
                motivoAbastecimiento.replaceChildren();
                (motivosPorAbastecimiento[nuevoValor] || ['Otro']).forEach(function(motivo, indice) {
                    const option = document.createElement('option');
                    option.value = motivo;
                    option.textContent = motivo;
                    option.selected = indice === 0;
                    motivoAbastecimiento.appendChild(option);
                });
                comentarioAbastecimiento.value = '';
                responsableAbastecimiento.value = '';
                fechaLimiteAbastecimiento.value = '';
                errorAbastecimiento.textContent = '';
                errorAbastecimiento.classList.add('hidden');
                modalAbastecimiento.classList.remove('hidden');
                modalAbastecimiento.style.display = 'flex';
                motivoAbastecimiento.focus();
            });
        });

        guardarModalAbastecimiento.addEventListener('click', function() {
            if (!abastecimientoSelectActivo) return;
            const motivo = motivoAbastecimiento.value;
            const comentario = comentarioAbastecimiento.value.trim();
            if (motivo === 'Otro' && !comentario) {
                errorAbastecimiento.textContent = 'Agrega un comentario para explicar el motivo.';
                errorAbastecimiento.classList.remove('hidden');
                comentarioAbastecimiento.focus();
                return;
            }

            guardarModalAbastecimiento.disabled = true;
            guardarModalAbastecimiento.textContent = 'Guardando...';
            guardarCambioAbastecimiento(abastecimientoSelectActivo, {
                abastecimiento: abastecimientoSelectActivo.value,
                motivo: motivo,
                comentario: comentario || null,
                responsable: responsableAbastecimiento.value.trim() || null,
                fecha_limite: fechaLimiteAbastecimiento.value || null
            }, false).then(function() {
                cerrarModalAbastecimiento(false);
            }).catch(function(error) {
                errorAbastecimiento.textContent = error.message;
                errorAbastecimiento.classList.remove('hidden');
                guardarModalAbastecimiento.disabled = false;
                guardarModalAbastecimiento.textContent = 'Guardar y agregar actividad';
            });
        });

        document.getElementById('cerrarModalAbastecimiento').addEventListener('click', () => {
            if (!guardarModalAbastecimiento.disabled) cerrarModalAbastecimiento();
        });
        document.getElementById('cancelarModalAbastecimiento').addEventListener('click', () => {
            if (!guardarModalAbastecimiento.disabled) cerrarModalAbastecimiento();
        });
        modalAbastecimiento.addEventListener('click', event => {
            if (event.target === modalAbastecimiento && !guardarModalAbastecimiento.disabled) cerrarModalAbastecimiento();
        });
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && !modalAbastecimiento.classList.contains('hidden') && !guardarModalAbastecimiento.disabled) {
                cerrarModalAbastecimiento();
            }
        });

        // ================================================================
        // ENTREGA DE INFORMACIÓN (VENTAS)
        // ================================================================
        document.querySelectorAll('.resultado-entrega-input').forEach(function(select) {
            select.dataset.valorAnterior = select.value;

            function aplicarEstiloResultado(elemento) {
                elemento.classList.remove('border-green-300', 'bg-green-50', 'text-green-800', 'border-red-300', 'bg-red-50', 'text-red-800', 'border-amber-300', 'bg-amber-50', 'text-amber-800', 'border-slate-300', 'bg-slate-50', 'text-slate-700');
                if (elemento.value === 'Información completa') {
                    elemento.classList.add('border-green-300', 'bg-green-50', 'text-green-800');
                } else if (elemento.value === 'Información incompleta') {
                    elemento.classList.add('border-red-300', 'bg-red-50', 'text-red-800');
                } else if (elemento.value === 'Pendiente') {
                    elemento.classList.add('border-amber-300', 'bg-amber-50', 'text-amber-800');
                } else if (elemento.value === 'Pendiente') {
                    elemento.classList.add('border-slate-300', 'bg-slate-50', 'text-slate-700');
                }
            }

            aplicarEstiloResultado(select);
            select.addEventListener('change', function() {
                const control = this;
                const valorAnterior = control.dataset.valorAnterior ?? '';
                control.disabled = true;

                fetch(control.dataset.url, {
                    method: 'PATCH',
                    headers: window.encabezadosSolicitudJsonControl(),
                    body: JSON.stringify({ resultado_entrega: control.value || null })
                })
                .then(function(response) {
                    if (!response.ok) throw new Error('Error al guardar entrega de información');
                    return response.json();
                })
                .then(function(data) {
                    if (!data.success) throw new Error('No se pudo guardar el entrega de información');
                    control.dataset.valorAnterior = control.value;
                    aplicarEstiloResultado(control);
                })
                .catch(function(error) {
                    console.error(error);
                    control.value = valorAnterior;
                    aplicarEstiloResultado(control);
                    alert('No se pudo guardar el entrega de información. Verifica tu conexión e inténtalo de nuevo.');
                })
                .finally(function() { control.disabled = false; });
            });
        });

        // ================================================================
        // ESTADO OPERATIVO
        // ================================================================

        const estados =
            document.querySelectorAll('.estado-input');


        function aplicarColorEstado(select) {

            select.style.backgroundColor = '';
            select.style.color = '';
            select.style.borderColor = '';

            switch (select.value) {

                case 'Por Iniciar':
                    select.style.backgroundColor = '#f4f5ee';
                    select.style.color = '#374151';
                    select.style.borderColor = '#f4f5ee';
                    break;

                case 'En proceso':
                    select.style.backgroundColor = '#bae6fd';
                    select.style.color = '#075985';
                    select.style.borderColor = '#7dd3fc';
                    break;

                case 'Cancelado':
                    select.style.backgroundColor = '#6b7280';
                    select.style.color = '#ffffff';
                    select.style.borderColor = '#4b5563';
                    break;

                case 'Terminado':
                    select.style.backgroundColor = '#bbf7d0';
                    select.style.color = '#166534';
                    select.style.borderColor = '#86efac';
                    break;

            }
        }


        estados.forEach(function(select) {

            // Color inicial
            aplicarColorEstado(select);


            select.addEventListener('change', function() {

                const campo = this;

                const estadoAnterior =
                    campo.dataset.estadoOriginal;

                const nuevoEstado =
                    campo.value;

                const url =
                    campo.dataset.url;

                window.mostrarEstadoGuardadoControl(campo, 'guardando');


                // Cambiar inmediatamente el color
                aplicarColorEstado(campo);


                campo.disabled = true;


                fetch(url, {

                        method: 'PATCH',

                        headers: window.encabezadosSolicitudJsonControl(),

                        body: JSON.stringify({

                            estado_operativo: nuevoEstado

                        })

                    })

                    .then(response => {

                        if (!response.ok) {

                            throw new Error(
                                'Error al guardar estado'
                            );

                        }

                        return response.json();

                    })

                    .then(data => {

                        if (!data.success) {

                            throw new Error(
                                'No se pudo guardar el estado'
                            );

                        }


                        // Guardamos el nuevo estado como
                        // estado anterior para el siguiente cambio.

                        campo.dataset.estadoOriginal =
                            data.estado_operativo;

                        window.mostrarEstadoGuardadoControl(campo, 'guardado');

                        const filaEstadoActualizada = campo.closest('.fila-pedido');
                        if (filaEstadoActualizada) {
                            filaEstadoActualizada.dataset.estado = data.estado_operativo || nuevoEstado;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | SI ES TERMINADO
                        |--------------------------------------------------------------------------
                        |
                        | El avance operativo pasa inmediatamente
                        | a 100%.
                        |
                        */

                        const fila =
                            campo.closest('.fila-pedido');


                        if (
                            nuevoEstado === 'Terminado' &&
                            fila
                        ) {

                            const avance =
                                fila.querySelector(
                                    '.avance-valor'
                                );

                            if (avance) {

                                avance.textContent =
                                    '100%';

                            }


                            const semaforo =
                                fila.querySelector(
                                    '.semaforo-valor'
                                );

                            if (semaforo) {

                                semaforo.textContent =
                                    'TERMINADO';

                            }

                            const proceso = data.proceso_menor_avance;
                            const nombre = fila.querySelector('.proceso-menor-nombre');
                            const porcentaje = fila.querySelector('.proceso-menor-porcentaje');
                            const completo = proceso.porcentaje === 1;

                            fila.dataset.avance = '1';
                            fila.dataset.procesoMenorAvance = proceso.porcentaje ?? '';
                            nombre.textContent = proceso.proceso;
                            nombre.classList.remove('bg-gray-700', 'text-gray-400', 'bg-red-100', 'text-red-700',
                                'bg-orange-100', 'text-orange-700', 'bg-yellow-100', 'text-yellow-700',
                                'bg-green-100', 'text-green-700');
                            nombre.classList.add(...(completo ? ['bg-green-100', 'text-green-700'] : ['bg-gray-700', 'text-gray-400']));
                            porcentaje.textContent = completo ? '100%' : '';
                            porcentaje.hidden = !completo;
                        }

                        document.dispatchEvent(new CustomEvent('estado-operativo-actualizado'));
                    })

                    .catch(error => {

                        console.error(error);


                        // Regresar al estado anterior
                        campo.value =
                            estadoAnterior;

                        window.mostrarEstadoGuardadoControl(campo, 'error', error.message);


                        aplicarColorEstado(campo);


                        alert(
                            'No se pudo guardar el Estado.'
                        );

                    })

                    .finally(() => {

                        campo.disabled = false;

                    });

            });

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const panel = document.getElementById('panelCalendarioProduccion');
            if (!panel) return;

            const tabla = document.getElementById('tablaPedidos');
            const contenedorDias = document.getElementById('calDias');
            const etiquetaMes = document.getElementById('calMesAnio');
            const etiquetaFecha = document.getElementById('calFechaSeleccionada');
            const etiquetaTotal = document.getElementById('calTotalDia');
            const listaPedidos = document.getElementById('calListaPedidos');

            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);
            let mesVisible = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
            let fechaSeleccionada = new Date(hoy);

            const pad = numero => String(numero).padStart(2, '0');
            const claveFecha = fecha => `${fecha.getFullYear()}-${pad(fecha.getMonth() + 1)}-${pad(fecha.getDate())}`;
            const fechaLocal = valor => {
                if (!valor || !/^\d{4}-\d{2}-\d{2}$/.test(valor)) return null;
                const partes = valor.split('-').map(Number);
                return new Date(partes[0], partes[1] - 1, partes[2]);
            };
            const mismoDia = (a, b) => claveFecha(a) === claveFecha(b);
            const textoFecha = fecha => fecha.toLocaleDateString('es-MX', {
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
            });
            const obtenerFilas = () => Array.from(tabla.querySelectorAll('tbody tr.fila-pedido'));

            function pedidosPorFecha() {
                const mapa = new Map();
                obtenerFilas().forEach(fila => {
                    const fecha = fila.dataset.fechaProduccion;
                    if (!fecha) return;
                    if (!mapa.has(fecha)) mapa.set(fecha, []);
                    mapa.get(fecha).push(fila);
                });
                return mapa;
            }

            function claseEstado(fila, fecha) {
                const estado = (fila.dataset.estado || '').toLowerCase();
                if (estado === 'terminado') return 'border-green-200 bg-green-50';
                if (estado === 'cancelado') return 'border-gray-200 bg-gray-50';
                const fechaDia = fechaLocal(fecha);
                if (fechaDia && fechaDia < hoy) return 'border-red-200 bg-red-50';
                if (estado === 'en proceso') return 'border-sky-200 bg-sky-50';
                return 'border-orange-200 bg-orange-50';
            }

            function colorPunto(fila, fecha) {
                const estado = (fila.dataset.estado || '').toLowerCase();
                if (estado === 'terminado') return 'bg-green-500';
                if (estado === 'cancelado') return 'bg-gray-400';
                const fechaDia = fechaLocal(fecha);
                if (fechaDia && fechaDia < hoy) return 'bg-red-500';
                if (estado === 'en proceso') return 'bg-sky-500';
                return 'bg-orange-500';
            }

            function renderizarLista() {
                const fecha = claveFecha(fechaSeleccionada);
                const pedidos = pedidosPorFecha().get(fecha) || [];
                etiquetaFecha.textContent = textoFecha(fechaSeleccionada);
                etiquetaTotal.textContent = `${pedidos.length} ${pedidos.length === 1 ? 'pedido' : 'pedidos'}`;
                listaPedidos.replaceChildren();

                if (!pedidos.length) {
                    const vacio = document.createElement('p');
                    vacio.className = 'text-sm text-gray-400 italic py-4';
                    vacio.textContent = 'No hay pedidos programados para esta fecha.';
                    listaPedidos.appendChild(vacio);
                    return;
                }

                pedidos.forEach(fila => {
                    const tarjeta = document.createElement('button');
                    tarjeta.type = 'button';
                    tarjeta.className = `w-full text-left border rounded-lg border-l-4 px-3 py-2 hover:shadow-sm transition ${claseEstado(fila, fecha)}`;

                    const linea = document.createElement('div');
                    linea.className = 'flex flex-wrap items-center justify-between gap-2';

                    const pedido = document.createElement('span');
                    pedido.className = 'font-semibold text-xs text-gray-800';
                    pedido.textContent = `Pedido ${fila.dataset.pedido || '—'}`;

                    const estado = document.createElement('span');
                    estado.className = 'text-[10px] font-semibold text-gray-600';
                    estado.textContent = fila.dataset.estado || 'Por Iniciar';

                    const cliente = document.createElement('p');
                    cliente.className = 'text-[11px] text-gray-600 mt-1';
                    cliente.textContent = fila.dataset.cliente || 'Sin cliente registrado';

                    const avance = document.createElement('p');
                    avance.className = 'text-[10px] text-gray-500 mt-1';
                    const valorAvance = fila.dataset.avance;
                    avance.textContent = `Avance: ${valorAvance === '' ? 'Sin datos' : `${Math.round(Number(valorAvance) * 100)}%`}`;

                    linea.append(pedido, estado);
                    tarjeta.append(linea, cliente, avance);
                    tarjeta.addEventListener('click', () => {
                        fila.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        fila.classList.add('ring-2', 'ring-blue-400');
                        window.setTimeout(() => fila.classList.remove('ring-2', 'ring-blue-400'), 1800);
                    });
                    listaPedidos.appendChild(tarjeta);
                });
            }

            function actualizarResumenSemanal() {
                const inicio = new Date(hoy);
                inicio.setDate(inicio.getDate() - ((inicio.getDay() + 6) % 7));
                const fin = new Date(inicio);
                fin.setDate(fin.getDate() + 6);
                const filasSemana = obtenerFilas().filter(fila => {
                    const fecha = fechaLocal(fila.dataset.fechaProduccion || '');
                    const estado = (fila.dataset.estado || '').toLowerCase();
                    return fecha && fecha >= inicio && fecha <= fin && estado !== 'cancelado';
                });
                const terminados = filasSemana.filter(fila => (fila.dataset.estado || '').toLowerCase() === 'terminado');
                const esAtrasado = fila => {
                    const estado = (fila.dataset.estado || '').toLowerCase();
                    const semaforo = (fila.dataset.semaforo || '').toLowerCase();
                    return estado !== 'terminado' && estado !== 'cancelado' && (semaforo.includes('atras') || (fechaLocal(fila.dataset.fechaProduccion || '') < hoy));
                };
                const atrasados = filasSemana.filter(esAtrasado);
                const porIniciar = filasSemana.filter(fila =>
                    (fila.dataset.estado || '').toLowerCase() === 'por iniciar' && !esAtrasado(fila)
                );
                const enProceso = filasSemana.filter(fila =>
                    (fila.dataset.estado || '').toLowerCase() === 'en proceso' && !esAtrasado(fila)
                );
                const set = (id, valor) => { const el = document.getElementById(id); if (el) el.textContent = valor; };
                set('calSemanaTotal', filasSemana.length);
                set('calSemanaTerminados', terminados.length);
                set('calSemanaEnProceso', enProceso.length);
                set('calSemanaIniciar', porIniciar.length);
                set('calSemanaAtrasados', atrasados.length);
                return { total: filasSemana, terminados, 'en-proceso': enProceso, 'por-iniciar': porIniciar, atrasados, inicio, fin };
            }

            const modalResumen = document.getElementById('modalResumenSemanal');
            const listaResumen = document.getElementById('modalResumenSemanalLista');
            const tituloResumen = document.getElementById('modalResumenSemanalTitulo');
            const descripcionResumen = document.getElementById('modalResumenSemanalDescripcion');
            const rangoResumen = document.getElementById('modalResumenSemanalRango');
            const conteoResumen = document.getElementById('modalResumenSemanalConteo');
            let disparadorResumen = null;
            const etiquetasResumen = {
                total: 'Pedidos esta semana', terminados: 'Terminados esta semana',
                'en-proceso': 'En proceso esta semana', 'por-iniciar': 'Por iniciar esta semana', atrasados: 'Atrasados esta semana'
            };
            const fechaResumen = fecha => fecha.toLocaleDateString('es-MX', { day: '2-digit', month: 'short', year: 'numeric' });
            function cerrarModalResumen() {
                if (!modalResumen) return;
                modalResumen.classList.add('hidden');
                modalResumen.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
                disparadorResumen?.focus();
            }
            document.querySelectorAll('[data-resumen-semanal]').forEach(boton => boton.addEventListener('click', () => {
                const resumen = actualizarResumenSemanal();
                const tipo = boton.dataset.resumenSemanal;
                const pedidos = resumen[tipo] || [];
                disparadorResumen = boton;
                tituloResumen.textContent = etiquetasResumen[tipo] || 'Pedidos de esta semana';
                descripcionResumen.textContent = 'Selecciona una tarjeta semanal para consultar sus pedidos.';
                rangoResumen.textContent = `${fechaResumen(resumen.inicio)} – ${fechaResumen(resumen.fin)}`;
                conteoResumen.textContent = `${pedidos.length} ${pedidos.length === 1 ? 'pedido' : 'pedidos'}`;
                listaResumen.replaceChildren();
                if (!pedidos.length) {
                    const vacio = document.createElement('p');
                    vacio.className = 'rounded-lg border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500';
                    vacio.textContent = 'No hay pedidos en esta categoría para la semana.';
                    listaResumen.append(vacio);
                } else {
                    pedidos.forEach(fila => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'grid w-full grid-cols-1 gap-2 rounded-lg border border-slate-200 bg-white p-3 text-left transition hover:border-blue-300 hover:bg-blue-50/50 focus:outline-none focus:ring-2 focus:ring-blue-400 sm:grid-cols-[1fr_auto] sm:items-center';
                        const info = document.createElement('div');
                        const pedido = document.createElement('p');
                        pedido.className = 'text-sm font-semibold text-slate-800';
                        pedido.textContent = `Pedido ${fila.dataset.pedido || '—'}`;
                        const cliente = document.createElement('p');
                        cliente.className = 'mt-0.5 text-xs text-slate-600';
                        cliente.textContent = fila.dataset.cliente || 'Cliente sin nombre';
                        info.append(pedido, cliente);
                        const detalles = document.createElement('div');
                        detalles.className = 'flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500 sm:justify-end';
                        const estado = document.createElement('span');
                        estado.textContent = `Estado: ${fila.dataset.estado || 'Sin estado'}`;
                        const fecha = document.createElement('span');
                        const fechaPedido = fechaLocal(fila.dataset.fechaProduccion || '');
                        fecha.textContent = `Producción: ${fechaPedido ? fechaResumen(fechaPedido) : 'Sin fecha'}`;
                        const avance = document.createElement('span');
                        const avanceCrudo = fila.dataset.avance;
                        const avanceDecimal = avanceCrudo === undefined || avanceCrudo === '' ? null : Number(avanceCrudo);
                        const avanceRedondeado = (fila.dataset.estado || '').trim().toLowerCase() === 'terminado'
                            ? 100
                            : (avanceDecimal !== null && Number.isFinite(avanceDecimal) ? Math.round(avanceDecimal * 100) : null);
                        avance.textContent = `Avance: ${avanceRedondeado === null ? '—' : `${avanceRedondeado}%`}`;
                        detalles.append(estado, fecha, avance);
                        item.append(info, detalles);
                        item.setAttribute('aria-label', `Ver Pedido ${fila.dataset.pedido || ''} en la tabla`);
                        item.addEventListener('click', () => {
                            const pedidoId = fila.dataset.pedidoId;
                            cerrarModalResumen();
                            if (!pedidoId) return;

                            let filaTabla = document.querySelector(`#tablaPedidos tbody tr[data-pedido-id="${CSS.escape(pedidoId)}"]`);
                            if (filaTabla && getComputedStyle(filaTabla).display === 'none') {
                                document.getElementById('limpiarFiltros')?.click();
                                filaTabla = document.querySelector(`#tablaPedidos tbody tr[data-pedido-id="${CSS.escape(pedidoId)}"]`);
                            }
                            if (!filaTabla || getComputedStyle(filaTabla).display === 'none') return;

                            filaTabla.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' });
                            filaTabla.classList.add('ring-2', 'ring-blue-400', 'bg-blue-50');
                            window.setTimeout(() => filaTabla.classList.remove('ring-2', 'ring-blue-400', 'bg-blue-50'), 2200);
                        });
                        listaResumen.append(item);
                    });
                }
                modalResumen.classList.remove('hidden');
                modalResumen.classList.add('flex');
                document.body.classList.add('overflow-hidden');
                modalResumen.querySelector('[data-cerrar-resumen-semanal]')?.focus();
            }));
            modalResumen?.querySelectorAll('[data-cerrar-resumen-semanal]').forEach(boton => boton.addEventListener('click', cerrarModalResumen));
            modalResumen?.addEventListener('click', evento => { if (evento.target === modalResumen) cerrarModalResumen(); });
            document.addEventListener('keydown', evento => { if (evento.key === 'Escape' && modalResumen && !modalResumen.classList.contains('hidden')) cerrarModalResumen(); });

            function renderizarCalendario() {
                etiquetaMes.textContent = mesVisible.toLocaleDateString('es-MX', {
                    month: 'long', year: 'numeric'
                });
                contenedorDias.replaceChildren();

                const mapa = pedidosPorFecha();
                const primerDia = new Date(mesVisible.getFullYear(), mesVisible.getMonth(), 1);
                const desplazamiento = (primerDia.getDay() + 6) % 7;
                const diasMes = new Date(mesVisible.getFullYear(), mesVisible.getMonth() + 1, 0).getDate();
                const totalCeldas = Math.ceil((desplazamiento + diasMes) / 7) * 7;

                for (let i = 0; i < totalCeldas; i++) {
                    const numeroDia = i - desplazamiento + 1;
                    const fecha = new Date(mesVisible.getFullYear(), mesVisible.getMonth(), numeroDia);
                    const esMesActual = fecha.getMonth() === mesVisible.getMonth();
                    const clave = claveFecha(fecha);
                    const pedidos = mapa.get(clave) || [];
                    const boton = document.createElement('button');
                    boton.type = 'button';
                    boton.className = 'relative flex flex-col items-center justify-start gap-0 h-[40px] sm:h-[42px] px-0.5 pt-1 border-r border-b border-slate-100 rounded-none text-[11px] transition-colors';
                    boton.setAttribute('aria-label', `${fecha.getDate()}, ${pedidos.length} pedidos`);

                    if (!esMesActual) {
                        boton.classList.add('text-slate-300', 'bg-white');
                    } else {
                        boton.classList.add('text-slate-800', 'bg-white', 'hover:bg-blue-50');
                    }
                    if (mismoDia(fecha, fechaSeleccionada)) {
                        boton.classList.remove('bg-white', 'text-slate-800', 'text-slate-300');
                        boton.classList.add('bg-blue-600', 'text-white', 'shadow-inner');
                    } else if (mismoDia(fecha, hoy)) {
                        boton.classList.add('font-bold', 'ring-1', 'ring-blue-300');
                    }
                    if (pedidos.length) boton.classList.add('font-semibold');

                    const numero = document.createElement('span');
                    numero.className = 'relative z-10 leading-4';
                    numero.textContent = fecha.getDate();
                    boton.appendChild(numero);

                    if (pedidos.length) {
                        const basePuntos = document.createElement('span');
                        basePuntos.className = `mt-1 flex min-h-[9px] w-full items-center justify-center gap-[3px] rounded-full ${mismoDia(fecha, fechaSeleccionada) ? 'bg-blue-600' : 'bg-blue-50'}`;
                        pedidos.slice(0, 4).forEach(fila => {
                            const punto = document.createElement('i');
                            punto.className = `block w-[5px] h-[5px] shrink-0 rounded-full ${mismoDia(fecha, fechaSeleccionada) ? 'bg-white' : colorPunto(fila, clave)}`;
                            basePuntos.appendChild(punto);
                        });
                        boton.appendChild(basePuntos);
                    }

                    boton.addEventListener('click', () => {
                        fechaSeleccionada = fecha;
                        mesVisible = new Date(fecha.getFullYear(), fecha.getMonth(), 1);
                        renderizarCalendario();
                        renderizarLista();
                    });
                    contenedorDias.appendChild(boton);
                }
            }

            document.getElementById('calMesAnterior').addEventListener('click', () => {
                mesVisible = new Date(mesVisible.getFullYear(), mesVisible.getMonth() - 1, 1);
                renderizarCalendario();
            });
            document.getElementById('calMesSiguiente').addEventListener('click', () => {
                mesVisible = new Date(mesVisible.getFullYear(), mesVisible.getMonth() + 1, 1);
                renderizarCalendario();
            });
            document.getElementById('calIrHoy').addEventListener('click', () => {
                fechaSeleccionada = new Date(hoy);
                mesVisible = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
                renderizarCalendario();
                renderizarLista();
            });

            // El controlador de fecha emite este evento únicamente después de guardar correctamente.
            document.addEventListener('fecha-produccion-actualizada', () => {
                renderizarCalendario();
                renderizarLista();
                actualizarResumenSemanal();
            });
            document.addEventListener('estado-operativo-actualizado', () => {
                renderizarCalendario();
                renderizarLista();
                actualizarResumenSemanal();
            });

            renderizarCalendario();
            renderizarLista();
            actualizarResumenSemanal();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const lista = document.getElementById('listaProximosProduccion');
            const totalSinFecha = document.getElementById('totalSinFechaProduccion');
            const botonAgenda = document.getElementById('verAgendaCompleta');
            const botonSinFecha = document.getElementById('verSinFechaProduccion');
            const contenedorTabla = document.getElementById('contenedorTabla');
            const contenidoAgenda = document.getElementById('contenidoAgendaProduccion');
            const toggleAgenda = document.getElementById('toggleAgendaProduccion');
            const textoToggleAgenda = document.getElementById('toggleAgendaProduccionTexto');
            const iconoToggleAgenda = document.getElementById('toggleAgendaProduccionIcono');
            const claveAgenda = 'controlOperativoAgendaVisible';

            function establecerAgendaVisible(visible, guardar = true) {
                if (!contenidoAgenda || !toggleAgenda) return;
                contenidoAgenda.classList.toggle('hidden', !visible);
                toggleAgenda.setAttribute('aria-expanded', visible ? 'true' : 'false');
                if (textoToggleAgenda) textoToggleAgenda.textContent = visible ? 'Ocultar agenda' : 'Mostrar agenda';
                if (iconoToggleAgenda) iconoToggleAgenda.textContent = visible ? '⌃' : '⌄';
                if (guardar) localStorage.setItem(claveAgenda, visible ? '1' : '0');
            }

            // La primera visita prioriza la tabla; se conserva la preferencia después.
            establecerAgendaVisible(localStorage.getItem(claveAgenda) === '1', false);
            toggleAgenda?.addEventListener('click', function() {
                establecerAgendaVisible(toggleAgenda.getAttribute('aria-expanded') !== 'true');
            });

            function leerFilasProduccion() {
                return Array.from(document.querySelectorAll('#tablaPedidos tbody tr.fila-pedido'))
                    .filter(fila => !fila.querySelector('td[colspan]'))
                    .map(fila => ({
                        fila,
                        pedido: (fila.dataset.pedido || fila.querySelector('[data-columna="pedido"]')?.innerText || 'Pedido').trim(),
                        cliente: (fila.dataset.cliente || fila.querySelector('[data-columna="cliente"]')?.innerText || '').trim(),
                        fecha: (fila.dataset.fechaProduccion || '').trim(),
                        estado: (fila.dataset.estado || '').trim().toLowerCase(),
                        semaforo: (fila.dataset.semaforo || '').trim().toLowerCase()
                    }));
            }

            function fechaCorta(valor) {
                if (!valor) return 'Sin fecha';
                const partes = valor.split('-');
                return partes.length === 3 ? `${partes[2]} ${['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'][Number(partes[1])-1] || ''}` : valor;
            }

            function actualizarPanelPedidos() {
                if (!lista) return;
                const filas = leerFilasProduccion();
                const hoy = new Date(); hoy.setHours(0,0,0,0);
                const fechaHoy = `${hoy.getFullYear()}-${String(hoy.getMonth()+1).padStart(2,'0')}-${String(hoy.getDate()).padStart(2,'0')}`;
                const activos = filas.filter(p => !['terminado','cancelado'].includes(p.estado));
                const sinFecha = activos.filter(p => !p.fecha);
                const proximos = activos.filter(p => p.fecha && p.fecha >= fechaHoy).sort((a,b) => a.fecha.localeCompare(b.fecha)).slice(0,6);
                if (totalSinFecha) totalSinFecha.textContent = sinFecha.length;
                lista.innerHTML = '';
                if (!proximos.length) {
                    lista.innerHTML = '<p class="text-xs text-slate-400 py-3">No hay próximos pedidos programados.</p>';
                } else {
                    proximos.forEach(p => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'w-full flex items-center gap-2 rounded-lg border-b border-slate-100 px-1.5 py-2 text-left hover:bg-blue-50';
                        const color = p.semaforo.includes('atras') ? 'bg-red-500' : p.estado.includes('terminado') ? 'bg-green-500' : p.estado.includes('proceso') ? 'bg-blue-500' : 'bg-orange-400';
                        item.innerHTML = `<span class="w-1.5 h-1.5 rounded-full ${color} shrink-0"></span><span class="w-[58px] shrink-0 text-[11px] font-semibold text-slate-700">${fechaCorta(p.fecha)}</span><span class="min-w-0 flex-1"><span class="block truncate text-xs font-semibold text-slate-700">Pedido ${p.pedido.replace(/^0+/, '') || '0'}</span><span class="block truncate text-[10px] text-slate-500">${p.cliente.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]))}</span></span><span class="text-slate-400">›</span>`;
                        item.addEventListener('click', () => { p.fila.scrollIntoView({behavior:'smooth', block:'center'}); p.fila.classList.add('bg-blue-50'); setTimeout(() => p.fila.classList.remove('bg-blue-50'), 1600); });
                        lista.appendChild(item);
                    });
                }
                if (botonAgenda) botonAgenda.onclick = () => {
                    establecerAgendaVisible(true);
                    document.getElementById('panelCalendarioProduccion')?.scrollIntoView({behavior:'smooth',block:'start'});
                };
                if (botonSinFecha) botonSinFecha.onclick = () => {
                    const campo = document.getElementById('filtroProduccionDesde');
                    if (campo) { campo.value = ''; campo.dispatchEvent(new Event('input',{bubbles:true})); }
                    const filasActuales = leerFilasProduccion();
                    filasActuales.forEach(p => { if (!p.fecha && !['terminado','cancelado'].includes(p.estado)) p.fila.style.outline = '2px solid #f87171'; else p.fila.style.outline = ''; });
                    if (contenedorTabla) contenedorTabla.scrollIntoView({behavior:'smooth',block:'start'});
                };
            }
            actualizarPanelPedidos();
            document.addEventListener('fecha-produccion-actualizada', actualizarPanelPedidos);
            document.addEventListener('estado-operativo-actualizado', actualizarPanelPedidos);
        });
    </script>

</x-app-layout>

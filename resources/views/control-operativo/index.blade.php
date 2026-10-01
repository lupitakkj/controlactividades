<x-app-layout>


    <div class="py-6">

        <div class="w-full max-w-[1800px] mx-auto px-4">

            {{-- ENCABEZADO --}}
            <div class="bg-white overflow-visible shadow-sm sm:rounded-lg mb-6 relative z-50">

                <div class="p-5">

                    <div class="flex items-center justify-between gap-6">

                        {{-- TÍTULO --}}
                        <div class="min-w-[220px]">

                            <h3 class="text-lg font-semibold text-gray-800">
                                Control Operativo
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Seguimiento de pedidos y producción.
                            </p>

                        </div>


                        {{-- INDICADORES --}}
                        <div class="flex items-center gap-3">

                            {{-- PEDIDOS ACTIVOS --}}
                            <div
                                class="text-white rounded-lg px-5 py-3 min-w-[145px] shadow-sm"
                                style="background-color: #F58220;">

                                <div class="text-xs font-semibold uppercase tracking-wide">
                                    Pedidos activos
                                </div>

                                <div class="text-3xl font-bold leading-none mt-1">
                                    {{ $pedidosActivos->count() }}
                                </div>

                            </div>


                            {{-- AVANCE GLOBAL --}}
                            <div
                                class="text-white rounded-lg px-5 py-3 min-w-[145px] shadow-sm"
                                style="background-color: #00AEEF;">

                                <div class="text-xs font-semibold uppercase tracking-wide">
                                    Avance global
                                </div>

                                <div class="text-3xl font-bold leading-none mt-1">
                                    {{ number_format($avanceGlobal * 100, 0) }}%
                                </div>

                            </div>

                        </div>


                        {{-- FILTRO DE AVANCE --}}
                        <div class="flex items-center gap-3">

                            <label
                                for="filtroAvance"
                                class="text-sm font-medium text-gray-700">
                                Avance:
                            </label>

                            <select
                                id="filtroAvance"
                                class="border border-gray-300 rounded-lg px-3 py-2
                           text-sm bg-white
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

                            <span
                                id="contadorFiltro"
                                class="text-sm text-gray-500">
                            </span>

                        </div>


                        {{-- TOTAL + COLUMNAS --}}
                        <div class="flex items-center gap-4">


                            {{-- BOTÓN COLUMNAS --}}
                            <div class="relative">

                                <button
                                    type="button"
                                    id="btnColumnas"
                                    class="inline-flex items-center gap-2 px-4 py-2
                               bg-gray-100 hover:bg-gray-200
                               text-gray-700 text-sm font-medium
                               rounded-lg border border-gray-300">

                                    ⚙️ Columnas

                                </button>


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


                                            {{-- IMPORTE --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="importe"
                                                    checked>

                                                <span class="text-sm text-gray-700">
                                                    Importe
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


                                            {{-- PRIORIDAD --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="prioridad"
                                                    checked>

                                                <span class="text-sm text-gray-700">
                                                    Prioridad
                                                </span>

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


                                            {{-- ESTADO --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="estado"
                                                    checked>

                                                <span class="text-sm text-gray-700">
                                                    Estado
                                                </span>

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


                                            {{-- PEDIDO INTERNO --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="pedido-interno"
                                                    checked>

                                                <span class="text-sm text-gray-700">
                                                    Pedido Interno
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


            {{-- FILTROS AVANZADOS --}}
            <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                <div class="p-5">
                    <div class="flex flex-wrap items-end gap-4">

                        <div class="min-w-[260px] flex-1">
                            <label for="filtroBusqueda" class="block text-xs font-semibold text-gray-600 uppercase mb-1">
                                Buscar
                            </label>
                            <input
                                type="text"
                                id="filtroBusqueda"
                                placeholder="Pedido, cliente, su pedido o enviar a..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="min-w-[150px]">
                            <label for="filtroPrioridad" class="block text-xs font-semibold text-gray-600 uppercase mb-1">
                                Prioridad
                            </label>
                            <select id="filtroPrioridad" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-blue-500 focus:border-blue-500">
                                <option value="todos">Todas</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="sin-prioridad">Sin prioridad</option>
                            </select>
                        </div>

                        <div class="min-w-[165px]">
                            <label for="filtroEstado" class="block text-xs font-semibold text-gray-600 uppercase mb-1">
                                Estado
                            </label>
                            <select id="filtroEstado" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-blue-500 focus:border-blue-500">
                                <option value="todos">Todos</option>
                                <option value="Por Iniciar">Por Iniciar</option>
                                <option value="En proceso">En proceso</option>
                                <option value="Cancelado">Cancelado</option>
                                <option value="Terminado">Terminado</option>
                            </select>
                        </div>

                        <div class="min-w-[165px]">
                            <label for="filtroSemaforo" class="block text-xs font-semibold text-gray-600 uppercase mb-1">
                                Semáforo
                            </label>
                            <select id="filtroSemaforo" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-blue-500 focus:border-blue-500">
                                <option value="todos">Todos</option>
                                <option value="EN TIEMPO">EN TIEMPO</option>
                                <option value="EN RIESGO">EN RIESGO</option>
                                <option value="ATRASADO">ATRASADO</option>
                                <option value="SIN FECHA">SIN FECHA</option>
                                <option value="TERMINADO">TERMINADO</option>
                                <option value="CANCELADO">CANCELADO</option>
                            </select>
                        </div>

                        <div class="min-w-[150px]">
                            <label for="filtroInterno" class="block text-xs font-semibold text-gray-600 uppercase mb-1">
                                Pedido Interno
                            </label>
                            <select id="filtroInterno" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-blue-500 focus:border-blue-500">
                                <option value="todos">Todos</option>
                                <option value="si">Sí</option>
                                <option value="no">No</option>
                            </select>
                        </div>

                        <div class="min-w-[155px]">
                            <label for="filtroProduccionDesde" class="block text-xs font-semibold text-gray-600 uppercase mb-1">
                                Producción desde
                            </label>
                            <input type="date" id="filtroProduccionDesde" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="min-w-[155px]">
                            <label for="filtroProduccionHasta" class="block text-xs font-semibold text-gray-600 uppercase mb-1">
                                Producción hasta
                            </label>
                            <input type="date" id="filtroProduccionHasta" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="min-w-[155px]">
                            <label for="filtroEntregaDesde" class="block text-xs font-semibold text-gray-600 uppercase mb-1">
                                Entrega desde
                            </label>
                            <input type="date" id="filtroEntregaDesde" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="min-w-[155px]">
                            <label for="filtroEntregaHasta" class="block text-xs font-semibold text-gray-600 uppercase mb-1">
                                Entrega hasta
                            </label>
                            <input type="date" id="filtroEntregaHasta" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <button
                            type="button"
                            id="limpiarFiltros"
                            class="px-4 py-2 rounded-lg border border-gray-300 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium">
                            Limpiar filtros
                        </button>

                    </div>

                    <div class="mt-4 flex items-center justify-between gap-4 text-sm">
                        <span id="resumenFiltros" class="text-gray-500"></span>
                        <span id="contadorFiltroAvanzado" class="font-semibold text-gray-700"></span>
                    </div>
                </div>
            </div>

            {{-- TABLA --}}
            {{-- TABLA --}}
            <div class="bg-white shadow-sm sm:rounded-lg">

                <div class="p-6">

                    <div
                        id="contenedorTabla"
                        class="overflow-auto"
                        style="height: calc(100vh - 330px); overflow-y: auto; overflow-x: auto; position: relative;">

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

                                    {{-- IMPORTE --}}
                                    <th
                                        data-columna="importe"
                                        class="col-importe px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                                        <button
                                            type="button"
                                            class="btn-ordenar w-full inline-flex items-center justify-between gap-2 group"
                                            data-sort="importe">
                                            <span>Importe</span>
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

                                    {{-- PRIORIDAD --}}
                                    <th
                                        data-columna="prioridad"
                                        class="col-prioridad px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                        <button
                                            type="button"
                                            class="btn-ordenar w-full inline-flex items-center justify-between gap-2 group"
                                            data-sort="prioridad">
                                            <span>Prioridad</span>
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

                                    {{-- ESTADO --}}
                                    <th
                                        data-columna="estado"
                                        class="col-estado px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                        <button
                                            type="button"
                                            class="btn-ordenar w-full inline-flex items-center justify-between gap-2 group"
                                            data-sort="estado">
                                            <span>Estado</span>
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

                                    {{-- PEDIDO INTERNO --}}
                                    <th
                                        data-columna="pedido-interno"
                                        class="col-pedido-interno px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                        <button
                                            type="button"
                                            class="btn-ordenar w-full inline-flex items-center justify-between gap-2 group"
                                            data-sort="pedido-interno">
                                            <span>Pedido Interno</span>
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
                                    data-pedido="{{ ltrim((string) $pedido->pedido_no, '0') ?: '0' }}"
                                    data-cliente="{{ e($pedido->cliente ?? '') }}"
                                    data-su-pedido="{{ e($pedido->su_pedido ?? '') }}"
                                    data-enviar-a="{{ e($pedido->enviar_a ?? '') }}"
                                    data-importe="{{ (float) $pedido->importe_total }}"
                                    data-fecha-entrega="{{ $pedido->fecha_entrega ? $pedido->fecha_entrega->format('Y-m-d') : '' }}"
                                    data-prioridad="{{ $prioridad ?? '' }}"
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


                                    {{-- IMPORTE --}}
                                    <td
                                        data-columna="importe"
                                        class="col-importe px-4 py-4 whitespace-nowrap text-sm text-gray-700 text-right">

                                        ${{ number_format((float) $pedido->importe_total, 2) }}

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


                                    {{-- PRIORIDAD --}}
                                    <td
                                        data-columna="prioridad"
                                        class="col-prioridad px-4 py-4 whitespace-nowrap text-sm text-center">

                                        <select
                                            class="prioridad-input rounded-md border px-3 py-1.5 text-sm font-semibold text-center cursor-pointer focus:ring-blue-500 focus:border-blue-500"
                                            data-url="{{ route('control-operativo.prioridad', $pedido) }}">

                                            <option value="" {{ !$prioridad ? 'selected' : '' }}>
                                                —
                                            </option>

                                            <option value="1" {{ (string) $prioridad === '1' ? 'selected' : '' }}>
                                                1
                                            </option>

                                            <option value="2" {{ (string) $prioridad === '2' ? 'selected' : '' }}>
                                                2
                                            </option>

                                            <option value="3" {{ (string) $prioridad === '3' ? 'selected' : '' }}>
                                                3
                                            </option>

                                            <option value="4" {{ (string) $prioridad === '4' ? 'selected' : '' }}>
                                                4
                                            </option>

                                        </select>

                                    </td>


                                    {{-- FECHA PRODUCCIÓN --}}
                                    <td
                                        data-columna="fecha-produccion"
                                        class="col-fecha-produccion px-4 py-4 whitespace-nowrap text-sm text-gray-700 text-center">

                                        <input
                                            type="date"
                                            class="fecha-produccion-input border border-gray-300 rounded-lg px-2 py-1 text-sm focus:ring-blue-500 focus:border-blue-500"
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

                                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold {{ $fondoProceso }} {{ $colorProceso }}">
                                                {{ $nombreProceso }}
                                            </span>

                                            @if($porcentajeProcesoTexto !== null)
                                            <span class="text-xs font-bold text-gray-700">
                                                {{ $porcentajeProcesoTexto }}%
                                            </span>
                                            @endif

                                        </div>
                                        </td>


                                        {{-- ESTADO --}}
                                        <td
                                            data-columna="estado"
                                            class="col-estado px-4 py-4 whitespace-nowrap text-center">

                                            <select
                                                class="estado-input rounded-md border border-gray-300
                                                    px-3 py-1.5 text-sm font-semibold
                                                    cursor-pointer
                                                    focus:ring-blue-500 focus:border-blue-500"
                                                data-url="{{ route('control-operativo.estado', $pedido) }}"
                                                data-estado-original="{{ $estado }}">

                                                <option value="Por Iniciar"
                                                    {{ $estado === 'Por Iniciar' ? 'selected' : '' }}>
                                                    Por Iniciar
                                                </option>


                                                <option value="En proceso"
                                                    {{ $estado === 'En proceso' ? 'selected' : '' }}>
                                                    En proceso
                                                </option>

                                                <option value="Cancelado"
                                                    {{ $estado === 'Cancelado' ? 'selected' : '' }}>
                                                    Cancelado
                                                </option>

                                                <option value="Terminado"
                                                    {{ $estado === 'Terminado' ? 'selected' : '' }}>
                                                    Terminado
                                                </option>

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


                                        {{-- PEDIDO INTERNO --}}
                                        <td
                                            data-columna="pedido-interno"
                                            class="col-pedido-interno px-4 py-4 whitespace-nowrap text-center">

                                            <input
                                                type="checkbox"
                                                class="pedido-interno-input w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                style="appearance: auto; -webkit-appearance: checkbox;"
                                                data-url="{{ route('control-operativo.pedido-interno', $pedido) }}"
                                                {{ $pedidoInterno ? 'checked' : '' }}>

                                        </td>




                                        {{-- ACTIVIDADES --}}
                                        <td
                                            data-columna="comentario"
                                            class="col-comentario px-3 py-2 min-w-[340px] align-top">

                                            {{-- ACTIVIDADES --}}
                                            <div class="actividades-container space-y-2">

                                                @forelse($actividades as $actividad)

                                                <div
                                                    class="actividad-item flex items-start gap-2 p-2 rounded-md border border-gray-200 bg-gray-50"
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
            min-width: 0 !important;
            border-collapse: separate;
            border-spacing: 0;
        }

        #tablaPedidos th,
        #tablaPedidos td {
            box-sizing: border-box;
        }

        /* Anchos iniciales. Se pueden cambiar arrastrando el borde. */
        #tablaPedidos th[data-columna="pedido"],
        #tablaPedidos td[data-columna="pedido"] {
            width: 78px !important;
            min-width: 78px !important;
            max-width: 78px !important;
            overflow: hidden !important;
        }

        #tablaPedidos td[data-columna="pedido"] .pedido-numero {
            width: 100% !important;
            max-width: 100% !important;
            overflow: hidden !important;
            text-overflow: ellipsis;
            white-space: nowrap !important;
            display: block;
        }

        #tablaPedidos th[data-columna="cliente"],
        #tablaPedidos td[data-columna="cliente"] {
            width: 130px !important;
            min-width: 130px !important;
        }

        #tablaPedidos th[data-columna="su-pedido"],
        #tablaPedidos td[data-columna="su-pedido"] {
            width: 110px !important;
            min-width: 110px !important;
        }

        #tablaPedidos th[data-columna="enviar-a"],
        #tablaPedidos td[data-columna="enviar-a"] {
            width: 100px !important;
            min-width: 100px !important;
        }

        #tablaPedidos th[data-columna="importe"],
        #tablaPedidos td[data-columna="importe"] {
            width: 100px !important;
            min-width: 100px !important;
        }

        #tablaPedidos th[data-columna="fecha-entrega"],
        #tablaPedidos td[data-columna="fecha-entrega"] {
            width: 105px !important;
            min-width: 105px !important;
        }

        #tablaPedidos th[data-columna="prioridad"],
        #tablaPedidos td[data-columna="prioridad"] {
            width: 70px !important;
            min-width: 70px !important;
        }

        #tablaPedidos th[data-columna="fecha-produccion"],
        #tablaPedidos td[data-columna="fecha-produccion"] {
            width: 110px !important;
            min-width: 110px !important;
        }

        #tablaPedidos th[data-columna="avance"],
        #tablaPedidos td[data-columna="avance"] {
            width: 75px !important;
            min-width: 75px !important;
        }

        #tablaPedidos th[data-columna="proceso-menor-avance"],
        #tablaPedidos td[data-columna="proceso-menor-avance"] {
            width: 120px !important;
            min-width: 120px !important;
        }

        #tablaPedidos th[data-columna="estado"],
        #tablaPedidos td[data-columna="estado"] {
            width: 100px !important;
            min-width: 100px !important;
        }

        #tablaPedidos th[data-columna="semaforo"],
        #tablaPedidos td[data-columna="semaforo"] {
            width: 105px !important;
            min-width: 105px !important;
        }

        #tablaPedidos th[data-columna="dias"],
        #tablaPedidos td[data-columna="dias"] {
            width: 100px !important;
            min-width: 100px !important;
        }

        #tablaPedidos th[data-columna="pedido-interno"],
        #tablaPedidos td[data-columna="pedido-interno"] {
            width: 100px !important;
            min-width: 100px !important;
        }

        #tablaPedidos th[data-columna="comentario"],
        #tablaPedidos td[data-columna="comentario"] {
            width: 340px !important;
            min-width: 340px !important;
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
        #tablaPedidos td.col-importe,
        #tablaPedidos td.col-fecha-entrega,
        #tablaPedidos td.col-prioridad,
        #tablaPedidos td.col-fecha-produccion,
        #tablaPedidos td.col-avance,
        #tablaPedidos td.col-estado,
        #tablaPedidos td.col-semaforo,
        #tablaPedidos td.col-dias,
        #tablaPedidos td.col-pedido-interno {
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

        #tablaPedidos td.col-prioridad,
        #tablaPedidos td.col-fecha-entrega,
        #tablaPedidos td.col-fecha-produccion,
        #tablaPedidos td.col-avance,
        #tablaPedidos td.col-estado,
        #tablaPedidos td.col-semaforo,
        #tablaPedidos td.col-dias,
        #tablaPedidos td.col-pedido-interno {
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

        /* Importe */
        #tablaPedidos td[data-columna="importe"] {
            font-weight: 600;
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
         REDIMENSIONAMIENTO TIPO EXCEL
         - Ancho de columnas con arrastre
         - Alto de filas con arrastre desde el borde inferior
         - Guarda tamaños en localStorage
       ================================================================ --}}
    <script>
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
                'importe': 100,
                'fecha-entrega': 105,
                'prioridad': 70,
                'fecha-produccion': 110,
                'avance': 75,
                'proceso-menor-avance': 120,
                'estado': 100,
                'semaforo': 105,
                'dias': 100,
                'pedido-interno': 100,
                'comentario': 340
            };

            function elementosColumna(nombre) {
                return tabla.querySelectorAll(
                    '[data-columna="' +
                    CSS.escape(nombre) +
                    '"]'
                );
            }

            function aplicarAnchoColumna(nombre, ancho) {

                const anchoPx =
                    Math.max(70, Math.min(700, Number(ancho)));

                elementosColumna(nombre).forEach(function(elemento) {
                    elemento.style.setProperty('width', anchoPx + 'px', 'important');
                    elemento.style.setProperty('min-width', '70px', 'important');
                    elemento.style.removeProperty('max-width');
                });
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

                        // Buscar la siguiente columna visible.
                        // Esa columna será la que aproveche el espacio
                        // que se libere al reducir la columna actual.
                        const encabezadosArray =
                            Array.from(
                                tabla.querySelectorAll('thead th[data-columna]')
                            );

                        const indiceActual =
                            encabezadosArray.indexOf(th);

                        let siguienteTh = null;

                        for (
                            let i = indiceActual + 1; i < encabezadosArray.length; i++
                        ) {
                            if (encabezadosArray[i].offsetParent !== null) {
                                siguienteTh = encabezadosArray[i];
                                break;
                            }
                        }

                        const siguienteNombre =
                            siguienteTh?.dataset.columna ?? null;

                        const anchoSiguienteInicial =
                            siguienteTh ?
                            siguienteTh.getBoundingClientRect().width :
                            null;

                        th.classList.add('columna-redimensionando');
                        tabla.classList.add('redimensionando-columna');

                        if (siguienteTh) {
                            siguienteTh.classList.add(
                                'columna-recibiendo-espacio'
                            );
                        }

                        resizer.setPointerCapture?.(event.pointerId);

                        function mover(e) {

                            const diferencia =
                                e.clientX - posicionInicial;

                            const nuevoAncho =
                                Math.max(
                                    70,
                                    Math.min(
                                        700,
                                        anchoInicial + diferencia
                                    )
                                );

                            // Cuánto espacio realmente se ganó o perdió.
                            const cambioReal =
                                nuevoAncho - anchoInicial;

                            // Si existe una columna a la derecha,
                            // ella absorbe exactamente el cambio.
                            if (
                                siguienteTh &&
                                siguienteNombre &&
                                anchoSiguienteInicial !== null
                            ) {

                                const nuevoAnchoSiguiente =
                                    anchoSiguienteInicial - cambioReal;

                                // La columna siguiente nunca puede
                                // reducirse por debajo de 70 px.
                                if (nuevoAnchoSiguiente < 70) {
                                    return;
                                }

                                aplicarAnchoColumna(
                                    nombre,
                                    nuevoAncho
                                );

                                aplicarAnchoColumna(
                                    siguienteNombre,
                                    nuevoAnchoSiguiente
                                );

                                anchosColumnas[nombre] =
                                    Math.round(nuevoAncho);

                                anchosColumnas[siguienteNombre] =
                                    Math.round(nuevoAnchoSiguiente);

                            } else {

                                // Si no hay columna siguiente,
                                // solamente cambia la actual.
                                aplicarAnchoColumna(
                                    nombre,
                                    nuevoAncho
                                );

                                anchosColumnas[nombre] =
                                    Math.round(nuevoAncho);
                            }
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

                            if (siguienteTh) {
                                siguienteTh.classList.remove(
                                    'columna-recibiendo-espacio'
                                );
                            }

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

            const checks =
                document.querySelectorAll('.columna-check');

            const columnasStorage =
                'controlOperativoColumnas';


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

                    panelColumnas.classList.add(
                        'hidden'
                    );

                }
            );


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
                contadorFiltro.textContent = visibles + ' pedidos';
                contadorFiltroAvanzado.textContent = visibles + ' de ' + filasPedidos.length + ' pedidos';

                const activos = [];
                if (filtros.busqueda.value.trim()) activos.push('búsqueda');
                if (filtros.prioridad.value !== 'todos') activos.push('prioridad');
                if (filtros.estado.value !== 'todos') activos.push('estado');
                if (filtros.semaforo.value !== 'todos') activos.push('semáforo');
                if (filtros.interno.value !== 'todos') activos.push('pedido interno');
                if (filtros.produccionDesde.value || filtros.produccionHasta.value) activos.push('fecha producción');
                if (filtros.entregaDesde.value || filtros.entregaHasta.value) activos.push('fecha entrega');
                if (filtroAvance.value !== 'todos') activos.push('avance');

                resumenFiltros.textContent = activos.length ?
                    'Filtros activos: ' + activos.join(' · ') :
                    'Sin filtros aplicados';
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
                'importe',
                'prioridad',
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
                'importe': 'importe',
                'fecha-entrega': 'fechaEntrega',
                'prioridad': 'prioridad',
                'fecha-produccion': 'fechaProduccion',
                'avance': 'avance',
                'proceso-menor-avance': 'procesoMenorAvance',
                'estado': 'estado',
                'semaforo': 'semaforo',
                'dias': 'dias',
                'pedido-interno': 'pedidoInterno',
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

                        headers: {
                            'Content-Type': 'application/json',

                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),

                            'Accept': 'application/json'
                        },

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

                            headers: {

                                'Content-Type': 'application/json',

                                'X-CSRF-TOKEN': document
                                    .querySelector(
                                        'meta[name="csrf-token"]'
                                    )
                                    .getAttribute('content'),

                                'Accept': 'application/json'

                            },

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

                        headers: {

                            'Content-Type': 'application/json',

                            'X-CSRF-TOKEN': document
                                .querySelector(
                                    'meta[name="csrf-token"]'
                                )
                                .getAttribute('content'),

                            'Accept': 'application/json'

                        },

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
            // PEDIDO INTERNO
            // ================================================================

            const pedidosInternos =
                document.querySelectorAll(
                    '.pedido-interno-input'
                );


            pedidosInternos.forEach(function(input) {

                input.addEventListener('change', function() {

                    const campo =
                        this;

                    const url =
                        campo.dataset.url;

                    const valor =
                        campo.checked;


                    campo.disabled = true;


                    fetch(url, {

                            method: 'PATCH',

                            headers: {

                                'Content-Type': 'application/json',

                                'X-CSRF-TOKEN': document
                                    .querySelector(
                                        'meta[name="csrf-token"]'
                                    )
                                    .getAttribute('content'),

                                'Accept': 'application/json'

                            },

                            body: JSON.stringify({

                                pedido_interno: valor

                            })

                        })

                        .then(response => {

                            if (!response.ok) {

                                throw new Error(
                                    'Error al guardar Pedido Interno'
                                );

                            }

                            return response.json();

                        })

                        .then(data => {

                            if (!data.success) {

                                throw new Error(
                                    'No se pudo guardar Pedido Interno'
                                );

                            }

                        })

                        .catch(error => {

                            console.error(error);

                            campo.checked = !valor;

                            alert(
                                'No se pudo guardar el Pedido Interno.'
                            );

                        })

                        .finally(() => {

                            campo.disabled =
                                false;

                        });

                });

            });

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

                    fetch(url, {

                            method: 'PATCH',

                            headers: {

                                'Content-Type': 'application/json',

                                'X-CSRF-TOKEN': document
                                    .querySelector(
                                        'meta[name="csrf-token"]'
                                    )
                                    .getAttribute('content'),

                                'Accept': 'application/json'

                            },

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

                            campo.style.borderColor = '#22c55e';

                            setTimeout(function() {

                                campo.style.borderColor = '';

                            }, 800);

                        })

                        .catch(error => {

                            console.error(error);

                            alert(
                                'No se pudo guardar la Fecha de Producción.'
                            );

                        });

                });

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

                        headers: {

                            'Content-Type': 'application/json',

                            'X-CSRF-TOKEN': document
                                .querySelector(
                                    'meta[name="csrf-token"]'
                                )
                                .getAttribute('content'),

                            'Accept': 'application/json'

                        },

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


                        // --------------------------------------------------------
                        // CAMBIAR APARIENCIA
                        // --------------------------------------------------------

                        if (completada) {

                            actividadElemento.classList.add(
                                'opacity-60'
                            );

                            actividadElemento.classList.add(
                                'bg-gray-100'
                            );

                        } else {

                            actividadElemento.classList.remove(
                                'opacity-60'
                            );

                            actividadElemento.classList.remove(
                                'bg-gray-100'
                            );

                        }

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
        // PRIORIDAD
        // ================================================================

        const prioridades =
            document.querySelectorAll('.prioridad-input');


        function aplicarColorPrioridad(select) {

            // Quitar colores anteriores
            select.style.backgroundColor = '';
            select.style.color = '';
            select.style.borderColor = '';

            switch (select.value) {

                // ---------------------------------------------------------
                // PRIORIDAD 1 - ROJO
                // ---------------------------------------------------------
                case '1':

                    select.style.backgroundColor = '#ff0000';
                    select.style.color = '#000000';
                    select.style.borderColor = '#cc0000';

                    break;


                    // ---------------------------------------------------------
                    // PRIORIDAD 2 - AMARILLO
                    // ---------------------------------------------------------
                case '2':

                    select.style.backgroundColor = '#ffff00';
                    select.style.color = '#000000';
                    select.style.borderColor = '#d4d400';

                    break;


                    // ---------------------------------------------------------
                    // PRIORIDAD 3 - VERDE
                    // ---------------------------------------------------------
                case '3':

                    select.style.backgroundColor = '#00b050';
                    select.style.color = '#000000';
                    select.style.borderColor = '#008c3a';

                    break;


                    // ---------------------------------------------------------
                    // PRIORIDAD 4 - AZUL / CIAN
                    // ---------------------------------------------------------
                case '4':

                    select.style.backgroundColor = '#00b0f0';
                    select.style.color = '#000000';
                    select.style.borderColor = '#008ccc';

                    break;
            }
        }


        // Aplicar color al cargar la página

        prioridades.forEach(function(select) {

            aplicarColorPrioridad(select);


            // Cambiar color y guardar

            select.addEventListener('change', function() {

                aplicarColorPrioridad(this);

                const url =
                    this.dataset.url;

                const valor =
                    this.value || null;

                this.disabled = true;


                fetch(url, {

                        method: 'PATCH',

                        headers: {

                            'Content-Type': 'application/json',

                            'X-CSRF-TOKEN': document
                                .querySelector(
                                    'meta[name="csrf-token"]'
                                )
                                .getAttribute('content'),

                            'Accept': 'application/json'

                        },

                        body: JSON.stringify({

                            prioridad: valor

                        })

                    })

                    .then(response => {

                        if (!response.ok) {

                            throw new Error(
                                'Error al guardar prioridad'
                            );

                        }

                        return response.json();

                    })

                    .then(data => {

                        if (!data.success) {

                            throw new Error(
                                'No se pudo guardar prioridad'
                            );

                        }

                    })

                    .catch(error => {

                        console.error(error);

                        alert(
                            'No se pudo guardar la prioridad.'
                        );

                    })

                    .finally(() => {

                        this.disabled = false;

                    });

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


                // Cambiar inmediatamente el color
                aplicarColorEstado(campo);


                campo.disabled = true;


                fetch(url, {

                        method: 'PATCH',

                        headers: {

                            'Content-Type': 'application/json',

                            'X-CSRF-TOKEN': document
                                .querySelector(
                                    'meta[name="csrf-token"]'
                                )
                                .getAttribute('content'),

                            'Accept': 'application/json'

                        },

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

                        }

                    })

                    .catch(error => {

                        console.error(error);


                        // Regresar al estado anterior
                        campo.value =
                            estadoAnterior;


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
</x-app-layout>
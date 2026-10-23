<x-app-layout>

    <div class="p-6">

        <!-- HEADER -->

        <div class="flex justify-between items-center mb-8">

            <h1 class="text-3xl font-bold">
                Reportes avanzados
            </h1>

            <a href="/dashboard"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">

                Volver

            </a>

        </div>

        <!-- FILTROS -->

        <div class="bg-white rounded-2xl shadow p-6 mb-8">

            <form method="GET"
                action="{{ route('reportes') }}">

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-8 gap-4">

                    <!-- TIPO -->

                    <div>

                        <label class="block text-sm mb-1">
                            Tipo
                        </label>

                        <select
                            name="tipo"
                            class="w-full border rounded-lg p-2">

                            <option
                                value="general"
                                @selected(request('tipo')=='general' )>

                                General

                            </option>

                            <option
                                value="disenador"
                                @selected(request('tipo')=='disenador' )>

                                Diseñador

                            </option>

                            <option
                                value="actividad"
                                @selected(request('tipo')=='actividad' )>

                                Actividad

                            </option>

                            <option
                                value="cliente"
                                @selected(request('tipo')=='cliente' )>

                                Cliente

                            </option>

                        </select>

                    </div>

                    <!-- DISEÑADOR -->

                    <div>

                        <label class="block text-sm mb-1">
                            Diseñador
                        </label>

                        <select
                            name="user_id"
                            class="w-full border rounded-lg p-2">

                            <option value="">
                                Todos
                            </option>

                            @foreach($usuarios as $usuario)

                            <option
                                value="{{ $usuario->id }}"
                                @selected(request('user_id')==$usuario->id)>

                                {{ $usuario->name }}

                            </option>

                            @endforeach

                        </select>

                    </div>

                    <!-- ACTIVIDAD -->

                    <div>

                        <label class="block text-sm mb-1">
                            Actividad
                        </label>

                        <select
                            name="actividad_id"
                            class="w-full border rounded-lg p-2">

                            <option value="">
                                Todas
                            </option>

                            @foreach($todasActividades as $actividad)

                            <option
                                value="{{ $actividad->id }}"
                                @selected(request('actividad_id')==$actividad->id)>

                                {{ $actividad->titulo }}

                            </option>

                            @endforeach

                        </select>

                    </div>

                    <!-- CLIENTE -->

                    <div>

                        <label class="block text-sm mb-1">
                            Cliente
                        </label>

                        <select
                            name="cliente_id"
                            class="w-full border rounded-lg p-2">

                            <option value="">
                                Todos
                            </option>

                            @foreach($clientes as $cliente)

                            <option
                                value="{{ $cliente->id }}"
                                @selected(request('cliente_id')==$cliente->id)>

                                {{ $cliente->nombre }}

                            </option>

                            @endforeach

                        </select>

                    </div>

                    <!-- PERIODO DE REPORTE -->
                    <div>
                        <label class="block text-sm mb-1">Vista del reporte</label>
                        <select name="periodo" id="periodoReporte" class="w-full border rounded-lg p-2">
                            <option value="semanal" @selected(($periodo ?? request('periodo', 'mensual')) === 'semanal')>Semanal</option>
                            <option value="mensual" @selected(($periodo ?? request('periodo', 'mensual')) === 'mensual')>Mensual</option>
                        </select>
                    </div>

                    <!-- FECHA DE REFERENCIA -->
                    <div>
                        <label class="block text-sm mb-1">Semana o mes</label>
                        <div class="flex gap-1">
                            <button type="button" id="periodoAnterior" class="border rounded-lg px-3 hover:bg-gray-100" title="Periodo anterior">‹</button>
                            <input type="date" name="periodo_fecha" id="periodoFecha" value="{{ $fechaReferencia ?? now()->format('Y-m-d') }}" class="w-full min-w-0 border rounded-lg p-2">
                            <button type="button" id="periodoSiguiente" class="border rounded-lg px-3 hover:bg-gray-100" title="Periodo siguiente">›</button>
                        </div>
                    </div>

                    <!-- FECHA INICIO -->

                    <div>

                        <label class="block text-sm mb-1">
                            Inicio
                        </label>

                        <input
                            type="date"
                            name="fecha_inicio"
                            value="{{ request('fecha_inicio') }}"
                            class="w-full border rounded-lg p-2">

                    </div>

                    <!-- FECHA FIN -->

                    <div>

                        <label class="block text-sm mb-1">
                            Fin
                        </label>

                        <input
                            type="date"
                            name="fecha_fin"
                            value="{{ request('fecha_fin') }}"
                            class="w-full border rounded-lg p-2">

                    </div>

                </div>

                <div class="mt-6 flex gap-3">

                    <button
                        class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-2 rounded-lg">

                        Generar reporte

                    </button>

                    <a href="/reportes"
                        class="bg-gray-300 hover:bg-gray-400 px-6 py-2 rounded-lg">

                        Limpiar

                    </a>

                </div>

            </form>

        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5 text-white">
            <div>
                <div class="text-sm text-gray-300">Periodo seleccionado</div>
                <div class="text-lg font-semibold">{{ ucfirst($etiquetaPeriodo ?? 'Periodo actual') }}</div>
            </div>
            <div class="flex flex-wrap gap-2 text-sm">
                <span class="rounded-full bg-blue-500/20 border border-blue-400/40 px-3 py-1">Pendientes: {{ $estados['pendiente'] ?? 0 }}</span>
                <span class="rounded-full bg-amber-500/20 border border-amber-400/40 px-3 py-1">En proceso: {{ $actividadesEnProceso ?? 0 }}</span>
                <span class="rounded-full bg-rose-500/20 border border-rose-400/40 px-3 py-1">Pausadas: {{ $actividadesPausadas ?? 0 }}</span>
            </div>
        </div>

        <!-- RESUMEN -->

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6 mb-8">

            <!-- ACTIVIDADES -->

            <div class="bg-white rounded-2xl shadow p-6">

                <div class="text-gray-500 mb-2">
                    Actividades
                </div>

                <div class="text-4xl font-bold">

                    {{ $totalActividades }}

                </div>

            </div>

            <!-- HORAS -->

            <div class="bg-white rounded-2xl shadow p-6">

                <div class="text-gray-500 mb-2">
                    Horas reales
                </div>

                <div class="text-4xl font-bold">

                    {{ $totalHoras }}

                </div>

            </div>

            <!-- TERMINADAS -->

            <div class="bg-white rounded-2xl shadow p-6">

                <div class="text-gray-500 mb-2">
                    Terminadas
                </div>

                <div class="text-4xl font-bold text-green-600">

                    {{ $terminadas }}

                </div>

            </div>

            <!-- PENDIENTES -->

            <div class="bg-white rounded-2xl shadow p-6">

                <div class="text-gray-500 mb-2">
                    Sin terminar
                </div>

                <div class="text-4xl font-bold text-orange-500">

                    {{ $pendientes }}

                </div>

            </div>

            <!-- DIFERENCIA -->

            <div class="bg-white rounded-2xl shadow p-6">

                <div class="text-gray-500 mb-2">
                    Diferencia
                </div>

                <div class="text-4xl font-bold
                    {{ $diferencia > 0 ? 'text-red-500' : 'text-green-600' }}">

                    {{ $diferencia }} hrs

                </div>

            </div>

        </div>

        <!-- INDICADORES OPERATIVOS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow p-6 border-l-4 border-blue-500">
                <div class="text-gray-500 mb-2">Avance de terminación</div>
                <div class="text-3xl font-bold text-blue-600">{{ $porcentajeTerminadas }}%</div>
                <div class="mt-3 h-2 rounded-full bg-gray-200 overflow-hidden">
                    <div class="h-full rounded-full bg-blue-500" style="width: {{ min(100, max(0, $porcentajeTerminadas)) }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Actividades terminadas respecto al total filtrado</p>
            </div>
            <div class="bg-white rounded-2xl shadow p-6 border-l-4 border-amber-500">
                <div class="text-gray-500 mb-2">En proceso</div>
                <div class="text-3xl font-bold text-amber-600">{{ $actividadesEnProceso }}</div>
                <p class="text-xs text-gray-500 mt-2">Actividades actualmente en ejecución</p>
            </div>
            <div class="bg-white rounded-2xl shadow p-6 border-l-4 border-rose-500">
                <div class="text-gray-500 mb-2">Pausadas</div>
                <div class="text-3xl font-bold text-rose-600">{{ $actividadesPausadas }}</div>
                <p class="text-xs text-gray-500 mt-2">Actividades detenidas que requieren seguimiento</p>
            </div>
        </div>
        <!-- GRAFICAS -->

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

            <!-- HORAS POR DISEÑADOR -->

            <div class="bg-white rounded-2xl shadow p-6">

                <h2 class="text-2xl font-bold mb-6">

                    Horas por diseñador

                </h2>
                <div style="height:300px; position:relative;">

                    <canvas id="horasChart"></canvas>
                </div>

            </div>

            <!-- ESTADOS -->

            <div class="bg-white rounded-2xl shadow p-6">

                <h2 class="text-2xl font-bold mb-6">

                    Actividades por estado

                </h2>
                <div style="height:300px; position:relative;">
                    <canvas id="estadoChart"></canvas>
                </div>

            </div>

            <!-- ESTIMADO VS REAL -->

            <div class="bg-white rounded-2xl shadow p-6">

                <h2 class="text-2xl font-bold mb-6">

                    Estimado vs real

                </h2>
                <div style="height:300px; position:relative;">
                    <canvas id="estimadoChart"></canvas>
                </div>

            </div>

            <!-- ACTIVIDADES TERMINADAS POR SEMANA -->

            <div class="bg-white rounded-2xl shadow p-6">

                <h2 class="text-2xl font-bold mb-2">
                    Actividades terminadas por semana
                </h2>
                <p class="text-sm text-gray-500 mb-4">
                    Últimas 8 semanas. Respeta los filtros aplicados al reporte.
                </p>

                <div style="height:300px; position:relative;">
                    <canvas id="terminadasSemanaChart"></canvas>
                </div>

            </div>

        </div>
        <!-- TABLA -->

        <div class="bg-white rounded-2xl shadow overflow-hidden">

            <div class="p-6 border-b flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold">Detalle de actividades</h2>
                    <p class="text-sm text-gray-500 mt-1">Busca dentro de los resultados y exporta las actividades visibles a Excel.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <input id="buscarTabla" type="search" placeholder="Buscar actividad, diseñador, estado..."
                        class="border border-gray-300 rounded-lg px-4 py-2 min-w-[260px] focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <button type="button" id="exportarExcel"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded-lg">
                        Exportar Excel
                    </button>
                </div>
            </div>

            <div class="max-h-[650px] overflow-auto rounded-b-xl">

                <table id="tablaActividades" class="w-full min-w-[1300px] border-collapse">

                    <thead class="bg-gray-100 sticky top-0 z-20 shadow-sm">

                        <tr>

                            <th scope="col" data-sortable="true" aria-sort="none" class="text-left p-4 whitespace-nowrap bg-gray-100 sticky top-0 z-20 cursor-pointer select-none hover:bg-gray-200" title="Ordenar ascendente o descendente">
                                <span class="inline-flex items-center gap-2">Actividad<span class="indicador-orden text-gray-400 text-xs" aria-hidden="true">↕</span></span>
                            </th>

                            <th scope="col" data-sortable="true" aria-sort="none" class="text-left p-4 whitespace-nowrap bg-gray-100 sticky top-0 z-20 cursor-pointer select-none hover:bg-gray-200" title="Ordenar ascendente o descendente">
                                <span class="inline-flex items-center gap-2">Diseñador<span class="indicador-orden text-gray-400 text-xs" aria-hidden="true">↕</span></span>
                            </th>

                            <th scope="col" data-sortable="true" aria-sort="none" class="text-left p-4 whitespace-nowrap bg-gray-100 sticky top-0 z-20 cursor-pointer select-none hover:bg-gray-200" title="Ordenar ascendente o descendente">
                                <span class="inline-flex items-center gap-2">Cliente<span class="indicador-orden text-gray-400 text-xs" aria-hidden="true">↕</span></span>
                            </th>

                            <th scope="col" data-sortable="true" aria-sort="none" class="text-left p-4 whitespace-nowrap bg-gray-100 sticky top-0 z-20 cursor-pointer select-none hover:bg-gray-200" title="Ordenar ascendente o descendente">
                                <span class="inline-flex items-center gap-2">Estado<span class="indicador-orden text-gray-400 text-xs" aria-hidden="true">↕</span></span>
                            </th>

                            <th scope="col" data-sortable="true" aria-sort="none" class="text-left p-4 whitespace-nowrap bg-gray-100 sticky top-0 z-20 cursor-pointer select-none hover:bg-gray-200" title="Ordenar ascendente o descendente">
                                <span class="inline-flex items-center gap-2">Prioridad<span class="indicador-orden text-gray-400 text-xs" aria-hidden="true">↕</span></span>
                            </th>

                            <th scope="col" data-sortable="true" aria-sort="none" class="text-left p-4 whitespace-nowrap bg-gray-100 sticky top-0 z-20 cursor-pointer select-none hover:bg-gray-200" title="Ordenar ascendente o descendente">
                                <span class="inline-flex items-center gap-2">Estimado<span class="indicador-orden text-gray-400 text-xs" aria-hidden="true">↕</span></span>
                            </th>

                            <th scope="col" data-sortable="true" aria-sort="none" class="text-left p-4 whitespace-nowrap bg-gray-100 sticky top-0 z-20 cursor-pointer select-none hover:bg-gray-200" title="Ordenar ascendente o descendente">
                                <span class="inline-flex items-center gap-2">Real<span class="indicador-orden text-gray-400 text-xs" aria-hidden="true">↕</span></span>
                            </th>

                            <th scope="col" data-sortable="true" aria-sort="none" class="text-left p-4 whitespace-nowrap bg-gray-100 sticky top-0 z-20 cursor-pointer select-none hover:bg-gray-200" title="Ordenar ascendente o descendente">
                                <span class="inline-flex items-center gap-2">Diferencia<span class="indicador-orden text-gray-400 text-xs" aria-hidden="true">↕</span></span>
                            </th>

                            <th scope="col" data-sortable="true" aria-sort="none" class="text-left p-4 whitespace-nowrap bg-gray-100 sticky top-0 z-20 cursor-pointer select-none hover:bg-gray-200" title="Ordenar ascendente o descendente">
                                <span class="inline-flex items-center gap-2">Comentarios<span class="indicador-orden text-gray-400 text-xs" aria-hidden="true">↕</span></span>
                            </th>

                            <th scope="col" data-sortable="true" aria-sort="none" class="text-left p-4 whitespace-nowrap bg-gray-100 sticky top-0 z-20 cursor-pointer select-none hover:bg-gray-200" title="Ordenar ascendente o descendente">
                                <span class="inline-flex items-center gap-2">Archivos<span class="indicador-orden text-gray-400 text-xs" aria-hidden="true">↕</span></span>
                            </th>

                            <th scope="col" data-sortable="true" aria-sort="none" class="text-left p-4 whitespace-nowrap bg-gray-100 sticky top-0 z-20 cursor-pointer select-none hover:bg-gray-200" title="Ordenar ascendente o descendente">
                                <span class="inline-flex items-center gap-2">Fecha<span class="indicador-orden text-gray-400 text-xs" aria-hidden="true">↕</span></span>
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($actividades as $actividad)

                        <tr class="border-t hover:bg-gray-50 fila-actividad">

                            <!-- ACTIVIDAD -->

                            <td class="p-4 font-bold">

                                {{ $actividad->titulo }}

                            </td>

                            <!-- DISEÑADOR -->

                            <td class="p-4">

                                {{ $actividad->user->name ?? '-' }}

                            </td>

                            <!-- CLIENTE -->

                            <td class="p-4">

                                {{ $actividad->cliente->nombre ?? '-' }}

                            </td>

                            <!-- ESTADO -->

                            <td class="p-4">

                                <span class="px-2 py-1 rounded text-xs bg-gray-200">

                                    {{ strtoupper($actividad->estado) }}

                                </span>

                            </td>

                            <!-- PRIORIDAD -->

                            <td class="p-4">

                                @if($actividad->prioridad == 'urgente')

                                <span class="text-red-600 font-bold">

                                    URGENTE

                                </span>

                                @elseif($actividad->prioridad == 'alta')

                                <span class="text-orange-500 font-bold">

                                    ALTA

                                </span>

                                @elseif($actividad->prioridad == 'media')

                                <span class="text-yellow-500 font-bold">

                                    MEDIA

                                </span>

                                @else

                                <span class="text-green-600 font-bold">

                                    BAJA

                                </span>

                                @endif

                            </td>

                            <!-- ESTIMADO -->

                            <td class="p-4">

                                {{ $actividad->tiempo_estimado }} hrs

                            </td>

                            <!-- REAL -->

                            <td class="p-4">

                                {{ round($actividad->tiempo_total / 60, 2) }} hrs

                            </td>

                            <!-- DIFERENCIA -->

                            <td class="p-4">

                                @if($actividad->diferencia_tiempo > 0)

                                <span class="text-red-500 font-bold">

                                    +{{ $actividad->diferencia_tiempo }} hrs

                                </span>

                                @else

                                <span class="text-green-600 font-bold">

                                    {{ $actividad->diferencia_tiempo }} hrs

                                </span>

                                @endif

                            </td>

                            <!-- COMENTARIOS -->

                            <td class="p-4">

                                {{ $actividad->comentarios->count() }}

                            </td>

                            <!-- ARCHIVOS -->

                            <td class="p-4">

                                {{ $actividad->archivos->count() }}

                            </td>

                            <!-- FECHA -->

                            <td class="p-4">

                                {{ $actividad->created_at->format('d/m/Y') }}

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="11"
                                class="text-center p-10 text-gray-500">

                                No hay actividades para mostrar

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <script>
        /*
    |--------------------------------------------------------------------------
    | HORAS POR DISEÑADOR
    |--------------------------------------------------------------------------
    */

        new Chart(
            document.getElementById('horasChart'), {

                type: 'bar',

                data: {

                    labels: @json($labelsHoras),

                    datasets: [{

                        label: 'Horas',

                        data: @json($datosHoras),

                        borderWidth: 1
                    }]
                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false

                }
            }
        );

        /*
        |--------------------------------------------------------------------------
        | ESTADOS
        |--------------------------------------------------------------------------
        */

        new Chart(
            document.getElementById('estadoChart'), {

                type: 'pie',

                data: {

                    labels: [

                        'Pendientes',
                        'En proceso',
                        'Pausadas',
                        'Terminadas'

                    ],

                    datasets: [{

                        data: @json($estadoData)

                    }]
                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false

                }
            }
        );

        /*
        |--------------------------------------------------------------------------
        | ESTIMADO VS REAL
        |--------------------------------------------------------------------------
        */

        new Chart(
            document.getElementById('estimadoChart'), {

                type: 'bar',

                data: {

                    labels: [

                        'Estimado',
                        'Real'

                    ],

                    datasets: [{

                        label: 'Horas',

                        data: @json($estimadoData),

                        borderWidth: 1
                    }]
                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false

                }
            }
        );

        /*
        |--------------------------------------------------------------------------
        | ACTIVIDADES TERMINADAS POR SEMANA
        |--------------------------------------------------------------------------
        */

        new Chart(
            document.getElementById('terminadasSemanaChart'), {
                type: 'line',
                data: {
                    labels: @json($labelsSemanas),
                    datasets: [{
                        label: 'Actividades terminadas',
                        data: @json($datosTerminadasSemana),
                        tension: 0.3,
                        fill: false,
                        borderWidth: 3,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    },
                    plugins: {
                        legend: { display: true }
                    }
                }
            }
        );
        // Encabezados fijos y orden ascendente/descendente por cualquier columna.
        const tablaActividades = document.getElementById('tablaActividades');
        const cuerpoActividades = tablaActividades?.querySelector('tbody');
        const normalizarTexto = valor => valor.trim().toLocaleLowerCase('es-MX');

        tablaActividades?.querySelectorAll('thead th[data-sortable="true"]').forEach((encabezado, indiceColumna) => {
            encabezado.addEventListener('click', () => {
                const ordenActual = encabezado.getAttribute('aria-sort');
                const direccion = ordenActual === 'ascending' ? 'descending' : 'ascending';
                const filas = Array.from(cuerpoActividades.querySelectorAll('tr.fila-actividad'));

                const obtenerValor = fila => {
                    const celda = fila.cells[indiceColumna];
                    if (!celda) return '';
                    let valor = normalizarTexto(celda.innerText);

                    // Fechas dd/mm/aaaa: comparar como fecha real, no como texto.
                    if (indiceColumna === 10) {
                        const partes = valor.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
                        if (partes) return Number(partes[3] + partes[2].padStart(2, '0') + partes[1].padStart(2, '0'));
                    }

                    // Columnas de horas y cantidades: ordenar por valor numérico.
                    if ([5, 6, 7, 8, 9].includes(indiceColumna)) {
                        const numero = valor.replace(/,/g, '').match(/-?\d+(?:\.\d+)?/);
                        if (numero) return Number(numero[0]);
                    }
                    return valor;
                };

                filas.sort((a, b) => {
                    const valorA = obtenerValor(a);
                    const valorB = obtenerValor(b);
                    let comparacion;
                    if (typeof valorA === 'number' && typeof valorB === 'number') {
                        comparacion = valorA - valorB;
                    } else {
                        comparacion = String(valorA).localeCompare(String(valorB), 'es-MX', { numeric: true, sensitivity: 'base' });
                    }
                    return direccion === 'ascending' ? comparacion : -comparacion;
                });

                filas.forEach(fila => cuerpoActividades.appendChild(fila));
                tablaActividades.querySelectorAll('thead th[data-sortable="true"]').forEach(th => {
                    th.setAttribute('aria-sort', 'none');
                    const indicador = th.querySelector('.indicador-orden');
                    if (indicador) indicador.textContent = '↕';
                });
                encabezado.setAttribute('aria-sort', direccion);
                const indicador = encabezado.querySelector('.indicador-orden');
                if (indicador) indicador.textContent = direccion === 'ascending' ? '↑' : '↓';
            });
        });

        // Búsqueda rápida en la tabla sin volver a consultar el servidor.
        const buscarTabla = document.getElementById('buscarTabla');
        const filasActividad = Array.from(document.querySelectorAll('#tablaActividades tbody tr.fila-actividad'));

        buscarTabla?.addEventListener('input', function () {
            const termino = this.value.trim().toLocaleLowerCase('es-MX');
            filasActividad.forEach(fila => {
                fila.style.display = fila.textContent.toLocaleLowerCase('es-MX').includes(termino) ? '' : 'none';
            });
        });

        // Exportar a Excel (.xlsx) los resultados visibles en la tabla.
        document.getElementById('exportarExcel')?.addEventListener('click', function () {
            const tabla = document.getElementById('tablaActividades');
            const encabezados = Array.from(tabla.querySelectorAll('thead th'))
                .map(th => th.innerText.trim().replace(/[↕↑↓]/g, '').trim());
            const filasVisibles = Array.from(tabla.querySelectorAll('tbody tr.fila-actividad'))
                .filter(fila => fila.style.display !== 'none')
                .map(fila => Array.from(fila.querySelectorAll('td')).map(td => td.innerText.trim()));

            if (!filasVisibles.length) {
                alert('No hay actividades visibles para exportar.');
                return;
            }

            if (typeof XLSX === 'undefined') {
                alert('No se pudo cargar el módulo de Excel. Revisa tu conexión a internet e inténtalo nuevamente.');
                return;
            }

            const hoja = XLSX.utils.aoa_to_sheet([encabezados, ...filasVisibles]);
            hoja['!cols'] = encabezados.map((encabezado, indice) => {
                const maximo = Math.max(
                    String(encabezado).length,
                    ...filasVisibles.map(fila => String(fila[indice] ?? '').length)
                );
                return { wch: Math.min(Math.max(maximo + 2, 12), 40) };
            });

            const libro = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(libro, hoja, 'Actividades');
            XLSX.writeFile(libro, 'reporte_actividades_' + new Date().toISOString().slice(0, 10) + '.xlsx');
        });
        // Navegación entre semanas o meses; conserva los demás filtros del formulario.
        (function () {
            const form = document.querySelector('form[action="{{ route('reportes') }}"]');
            const periodo = document.getElementById('periodoReporte');
            const fecha = document.getElementById('periodoFecha');
            const anterior = document.getElementById('periodoAnterior');
            const siguiente = document.getElementById('periodoSiguiente');
            if (!form || !periodo || !fecha) return;

            function configurarSelectorPeriodo() {
                if (periodo.value === 'mensual') {
                    fecha.type = 'month';
                    fecha.value = (fecha.value || new Date().toISOString().slice(0, 10)).slice(0, 7);
                } else {
                    fecha.type = 'date';
                    if (fecha.value && fecha.value.length === 7) fecha.value = fecha.value + '-01';
                    if (!fecha.value) fecha.value = new Date().toISOString().slice(0, 10);
                }
            }
            configurarSelectorPeriodo();

            function moverPeriodo(direccion) {
                if (!fecha.value) fecha.value = periodo.value === 'mensual'
                    ? new Date().toISOString().slice(0, 7)
                    : new Date().toISOString().slice(0, 10);
                const partes = fecha.value.split('-').map(Number);
                const actual = new Date(partes[0], partes[1] - 1, partes[2] || 1, 12, 0, 0);
                if (periodo.value === 'semanal') {
                    actual.setDate(actual.getDate() + (7 * direccion));
                } else {
                    actual.setDate(1);
                    actual.setMonth(actual.getMonth() + direccion);
                }
                const yyyy = actual.getFullYear();
                const mm = String(actual.getMonth() + 1).padStart(2, '0');
                const dd = String(actual.getDate()).padStart(2, '0');
                fecha.value = periodo.value === 'mensual' ? `${yyyy}-${mm}` : `${yyyy}-${mm}-${dd}`;
                // Los campos de fecha manual se limpian para que se aplique el periodo seleccionado.
                const inicio = form.querySelector('[name="fecha_inicio"]');
                const fin = form.querySelector('[name="fecha_fin"]');
                if (inicio) inicio.value = '';
                if (fin) fin.value = '';
                form.submit();
            }
            anterior?.addEventListener('click', () => moverPeriodo(-1));
            siguiente?.addEventListener('click', () => moverPeriodo(1));
            periodo.addEventListener('change', () => {
                const valorActual = fecha.value;
                if (periodo.value === 'mensual') {
                    fecha.type = 'month';
                    fecha.value = valorActual ? valorActual.slice(0, 7) : new Date().toISOString().slice(0, 7);
                } else {
                    fecha.type = 'date';
                    fecha.value = valorActual && valorActual.length === 7 ? valorActual + '-01' : (valorActual || new Date().toISOString().slice(0, 10));
                }
                const inicio = form.querySelector('[name="fecha_inicio"]');
                const fin = form.querySelector('[name="fecha_fin"]');
                if (inicio) inicio.value = '';
                if (fin) fin.value = '';
            });
        })();
    </script>
</x-app-layout>
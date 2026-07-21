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

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-4">

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
                    Pendientes
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

            <!-- TERMINADAS -->

            <div class="bg-white rounded-2xl shadow p-6">

                <h2 class="text-2xl font-bold mb-6">

                    Terminadas vs pendientes

                </h2>

                <div class="h-[300px]"> <canvas id="terminadasChart"></canvas></div>

            </div>

        </div>
        <!-- TABLA -->

        <div class="bg-white rounded-2xl shadow overflow-hidden">

            <div class="p-6 border-b">

                <h2 class="text-2xl font-bold">
                    Actividades
                </h2>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full min-w-[1300px]">

                    <thead class="bg-gray-100">

                        <tr>

                            <th class="text-left p-4">
                                Actividad
                            </th>

                            <th class="text-left p-4">
                                Diseñador
                            </th>

                            <th class="text-left p-4">
                                Cliente
                            </th>

                            <th class="text-left p-4">
                                Estado
                            </th>

                            <th class="text-left p-4">
                                Prioridad
                            </th>

                            <th class="text-left p-4">
                                Estimado
                            </th>

                            <th class="text-left p-4">
                                Real
                            </th>

                            <th class="text-left p-4">
                                Diferencia
                            </th>

                            <th class="text-left p-4">
                                Comentarios
                            </th>

                            <th class="text-left p-4">
                                Archivos
                            </th>

                            <th class="text-left p-4">
                                Fecha
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($actividades as $actividad)

                        <tr class="border-t hover:bg-gray-50">

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
        | TERMINADAS VS PENDIENTES
        |--------------------------------------------------------------------------
        */

        new Chart(
            document.getElementById('terminadasChart'), {

                type: 'doughnut',

                data: {

                    labels: [

                        'Terminadas',
                        'Pendientes'

                    ],

                    datasets: [{

                        data: @json($terminadasData)

                    }]
                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false

                }
            }
        );
    </script>
</x-app-layout>
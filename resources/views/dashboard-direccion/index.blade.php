<x-app-layout>

    <div class="py-6">

        <div class="max-w-screen-2xl mx-auto px-6">

            {{-- =====================================================
                 ENCABEZADO
            ====================================================== --}}

            <div class="mb-6">

                <h1 class="text-2xl font-bold text-white">
                    Dashboard de Dirección
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Resumen ejecutivo de producción y pedidos
                </p>

            </div>


            {{-- =====================================================
                 INDICADORES PRINCIPALES
            ====================================================== --}}

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">

                {{-- =================================================
                     VALOR DE PEDIDOS ACTIVOS
                ================================================== --}}

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">

                    <div class="text-xs text-gray-500 uppercase font-semibold">
                        Valor de pedidos
                    </div>

                    <div class="text-2xl font-bold text-gray-800 mt-2">
                        ${{ number_format($valorPedidos, 2) }}
                    </div>

                    <div class="text-xs text-gray-400 mt-1">
                        Pedidos activos
                    </div>

                </div>


                {{-- =================================================
                     PEDIDOS ACTIVOS
                ================================================== --}}

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">

                    <div class="text-xs text-gray-500 uppercase font-semibold">
                        Pedidos activos
                    </div>

                    <div class="text-2xl font-bold text-gray-800 mt-2">
                        {{ $pedidosActivos }}
                    </div>

                    <div class="text-xs text-gray-400 mt-1">
                        Pedidos pendientes
                    </div>

                </div>


                {{-- =================================================
                     EN PRODUCCIÓN
                ================================================== --}}

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">

                    <div class="text-xs text-gray-500 uppercase font-semibold">
                        En producción
                    </div>

                    <div class="text-2xl font-bold text-blue-600 mt-2">
                        ${{ number_format($valorEnProduccion, 2) }}
                    </div>

                    <div class="text-xs text-gray-400 mt-1">
                        Valor en proceso
                    </div>
                </div>


                {{-- =================================================
                     POR INICIAR
                ================================================== --}}

                <div
                    onclick="abrirModalTarjeta('por_iniciar')"
                    class="bg-white rounded-xl shadow-sm border border-gray-200 p-5
                            cursor-pointer hover:shadow-md hover:border-yellow-300
                            transition">

                    <div class="text-xs text-gray-500 uppercase font-semibold">
                        Por iniciar
                    </div>

                    <div class="text-2xl font-bold text-yellow-600 mt-2">
                        ${{ number_format($valorPorIniciar, 2) }}
                    </div>

                    <div class="text-xs text-gray-400 mt-1">
                        Pendiente de producción
                    </div>

                </div>


                {{-- =================================================
                     POR ENTREGAR
                ================================================== --}}

                <div
                    onclick="abrirModalTarjeta('por_entregar')"
                    class="bg-white rounded-xl shadow-sm border border-gray-200 p-5
                        cursor-pointer hover:shadow-md hover:border-green-300
                        transition">

                    <div class="text-xs text-gray-500 uppercase font-semibold">
                        Por entregar
                    </div>

                    <div class="text-2xl font-bold text-green-600 mt-2">
                        ${{ number_format($valorPorEntregar, 2) }}
                    </div>

                    <div class="text-xs text-gray-400 mt-1">
                        Entrega pendiente
                    </div>

                </div>


                {{-- =================================================
                     ATRASADO
                ================================================== --}}

                <div
                    onclick="abrirModalTarjeta('atrasado')"
                    class="bg-white rounded-xl shadow-sm border border-gray-200 p-5
                        cursor-pointer hover:shadow-md hover:border-red-300
                        transition">

                    <div class="text-xs text-gray-500 uppercase font-semibold">
                        Atrasado
                    </div>

                    <div class="text-2xl font-bold text-red-600 mt-2">
                        ${{ number_format($valorAtrasado, 2) }}
                    </div>

                    <div class="text-xs text-gray-400 mt-1">
                        Entrega vencida
                    </div>

                </div>

            </div>


            {{-- =====================================================
                SITUACIÓN ECONÓMICA
            ====================================================== --}}

            @php

            /*
            |--------------------------------------------------------------------------
            | IMPORTES DE LA DONA
            |--------------------------------------------------------------------------
            |
            | La dona representa cinco estados independientes:
            |
            | Entregados
            | En tiempo
            | En riesgo
            | Atrasados
            | Sin fecha
            |
            | "Por iniciar" y "Por entregar" NO se incluyen en la dona.
            | "Por iniciar" se conserva como KPI independiente.
            |
            */

            $valorEntregados = (float) ($situacionImportes['Entregados'] ?? 0);
            $valorEnTiempo = (float) ($situacionImportes['En tiempo'] ?? 0);
            $valorEnRiesgo = (float) ($situacionImportes['En riesgo'] ?? 0);
            $valorAtrasados = (float) ($situacionImportes['Atrasados'] ?? 0);
            $valorSinFecha = (float) ($situacionImportes['Sin fecha'] ?? 0);
            $totalEconomico =
            $valorEntregados
            + $valorEnTiempo
            + $valorEnRiesgo
            + $valorAtrasados
            + $valorSinFecha;

            $cantidadEntregados = $situacionPedidos['Entregados'] ?? 0;
            $cantidadEnTiempoDona = $situacionPedidos['En tiempo'] ?? 0;
            $cantidadEnRiesgoDona = $situacionPedidos['En riesgo'] ?? 0;
            $cantidadAtrasadosDona = $situacionPedidos['Atrasados'] ?? 0;
            $cantidadSinFechaDona = $situacionPedidos['Sin fecha'] ?? 0;
            $porcentajeEntregados = $totalEconomico > 0
            ? ($valorEntregados / $totalEconomico) * 100 : 0;

            $porcentajeEnTiempo = $totalEconomico > 0
            ? ($valorEnTiempo / $totalEconomico) * 100 : 0;

            $porcentajeEnRiesgo = $totalEconomico > 0
            ? ($valorEnRiesgo / $totalEconomico) * 100 : 0;

            $porcentajeAtrasados = $totalEconomico > 0
            ? ($valorAtrasados / $totalEconomico) * 100 : 0;

            $porcentajeSinFecha = $totalEconomico > 0
            ? ($valorSinFecha / $totalEconomico) * 100 : 0;

            $circunferencia = 2 * pi() * 90;
            $offset = 0;

            $dashEntregados = ($porcentajeEntregados / 100) * $circunferencia;
            $dashEnTiempo = ($porcentajeEnTiempo / 100) * $circunferencia;
            $dashEnRiesgo = ($porcentajeEnRiesgo / 100) * $circunferencia;
            $dashAtrasados = ($porcentajeAtrasados / 100) * $circunferencia;
            $dashSinFecha = ($porcentajeSinFecha / 100) * $circunferencia;

            @endphp


            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">


                {{-- =====================================================
                    ENCABEZADO
                ====================================================== --}}

                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">

                    <div>

                        <h2 class="text-lg font-bold text-gray-800">
                            Situación económica
                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Distribución del valor de los pedidos
                        </p>

                    </div>


                    <div class="mt-3 lg:mt-0 text-right">

                        <div class="text-sm font-semibold text-gray-700">
                            Semana {{ $semana }}
                        </div>

                        <div class="text-xs text-gray-400 mt-1">

                            {{ $inicioSemana->format('d/m/Y') }}

                            -

                            {{ $finSemana->format('d/m/Y') }}

                        </div>

                    </div>

                </div>


                {{-- =====================================================
                    CONTENIDO
                ====================================================== --}}

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mt-8">


                    {{-- =================================================
                        DONA SVG
                    ================================================== --}}

                    <div class="flex justify-center items-center">

                        <div class="relative w-64 h-64">

                            <svg
                                viewBox="0 0 220 220"
                                class="w-full h-full transform -rotate-90">

                                <circle
                                    cx="110" cy="110" r="90"
                                    fill="none"
                                    stroke="#f3f4f6"
                                    stroke-width="28" />

                                {{-- ENTREGADOS --}}
                                @if($dashEntregados > 0)
                                <circle cx="110" cy="110" r="90" fill="none"
                                    stroke="#10b0f0" stroke-width="28" stroke-linecap="butt"
                                    stroke-dasharray="{{ $dashEntregados }} {{ $circunferencia - $dashEntregados }}"
                                    stroke-dashoffset="{{ $offset }}" />
                                @php $offset -= $dashEntregados; @endphp
                                @endif

                                {{-- EN TIEMPO --}}
                                @if($dashEnTiempo > 0)
                                <circle cx="110" cy="110" r="90" fill="none"
                                    stroke="#16a34a" stroke-width="28" stroke-linecap="butt"
                                    stroke-dasharray="{{ $dashEnTiempo }} {{ $circunferencia - $dashEnTiempo }}"
                                    stroke-dashoffset="{{ $offset }}" />
                                @php $offset -= $dashEnTiempo; @endphp
                                @endif

                                {{-- EN RIESGO --}}
                                @if($dashEnRiesgo > 0)
                                <circle cx="110" cy="110" r="90" fill="none"
                                    stroke="#f59e0b" stroke-width="28" stroke-linecap="butt"
                                    stroke-dasharray="{{ $dashEnRiesgo }} {{ $circunferencia - $dashEnRiesgo }}"
                                    stroke-dashoffset="{{ $offset }}" />
                                @php $offset -= $dashEnRiesgo; @endphp
                                @endif

                                {{-- ATRASADOS --}}
                                @if($dashAtrasados > 0)
                                <circle cx="110" cy="110" r="90" fill="none"
                                    stroke="#ef4444" stroke-width="28" stroke-linecap="butt"
                                    stroke-dasharray="{{ $dashAtrasados }} {{ $circunferencia - $dashAtrasados }}"
                                    stroke-dashoffset="{{ $offset }}" />
                                @php $offset -= $dashAtrasados; @endphp
                                @endif

                                {{-- SIN FECHA --}}
                                @if($dashSinFecha > 0)
                                <circle cx="110" cy="110" r="90" fill="none"
                                    stroke="#9ca3af" stroke-width="28" stroke-linecap="butt"
                                    stroke-dasharray="{{ $dashSinFecha }} {{ $circunferencia - $dashSinFecha }}"
                                    stroke-dashoffset="{{ $offset }}" />
                                @php $offset -= $dashSinFecha; @endphp
                                @endif

                            </svg>


                            {{-- =================================================
                                    CENTRO DE LA DONA
                                ================================================== --}}

                            <div class="absolute inset-0 flex flex-col items-center justify-center text-center">

                                <div class="text-xl font-bold text-gray-800">

                                    ${{ number_format($totalEconomico, 2) }}

                                </div>

                                <div class="text-xs text-gray-500 mt-1">
                                    Valor representado
                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                        DETALLE
                    ================================================== --}}

                    <div class="flex flex-col justify-center space-y-5">

                        {{-- ENTREGADOS --}}
                        <div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">

                                    <span
                                        style="
                        display: inline-block;
                        width: 12px;
                        height: 12px;
                        min-width: 12px;
                        min-height: 12px;
                        background-color: #06b6d4;
                        border-radius: 50%;
                        opacity: 1;
                        visibility: visible;
                    ">
                                    </span>

                                    <span class="text-sm font-semibold text-gray-700">
                                        Entregados
                                    </span>

                                </div>

                                <span class="text-sm font-bold text-gray-800">
                                    ${{ number_format($valorEntregados, 2) }}
                                </span>
                            </div>

                            <div class="text-xs text-gray-400 mt-1 ml-5">
                                {{ $cantidadEntregados }}
                                {{ $cantidadEntregados == 1 ? 'pedido' : 'pedidos' }}
                                · {{ number_format($porcentajeEntregados, 1) }}%
                            </div>
                        </div>


                        {{-- EN TIEMPO --}}
                        <div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">

                                    <span
                                        style="
                        display: inline-block;
                        width: 12px;
                        height: 12px;
                        min-width: 12px;
                        min-height: 12px;
                        background-color: #16a34a;
                        border-radius: 50%;
                        opacity: 1;
                        visibility: visible;
                    ">
                                    </span>

                                    <span class="text-sm font-semibold text-gray-700">
                                        En tiempo
                                    </span>

                                </div>

                                <span class="text-sm font-bold text-gray-800">
                                    ${{ number_format($valorEnTiempo, 2) }}
                                </span>
                            </div>

                            <div class="text-xs text-gray-400 mt-1 ml-5">
                                {{ $cantidadEnTiempoDona }}
                                {{ $cantidadEnTiempoDona == 1 ? 'pedido' : 'pedidos' }}
                                · {{ number_format($porcentajeEnTiempo, 1) }}%
                            </div>
                        </div>


                        {{-- EN RIESGO --}}
                        <div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">

                                    <span
                                        style="
                        display: inline-block;
                        width: 12px;
                        height: 12px;
                        min-width: 12px;
                        min-height: 12px;
                        background-color: #eab308;
                        border-radius: 50%;
                        opacity: 1;
                        visibility: visible;
                    ">
                                    </span>

                                    <span class="text-sm font-semibold text-gray-700">
                                        En riesgo
                                    </span>

                                </div>

                                <span class="text-sm font-bold text-gray-800">
                                    ${{ number_format($valorEnRiesgo, 2) }}
                                </span>
                            </div>

                            <div class="text-xs text-gray-400 mt-1 ml-5">
                                {{ $cantidadEnRiesgoDona }}
                                {{ $cantidadEnRiesgoDona == 1 ? 'pedido' : 'pedidos' }}
                                · {{ number_format($porcentajeEnRiesgo, 1) }}%
                            </div>
                        </div>


                        {{-- ATRASADOS --}}
                        <div class="border-t border-gray-100 pt-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">

                                    <span
                                        style="
                        display: inline-block;
                        width: 12px;
                        height: 12px;
                        min-width: 12px;
                        min-height: 12px;
                        background-color: #ef4444;
                        border-radius: 50%;
                        opacity: 1;
                        visibility: visible;
                    ">
                                    </span>

                                    <span class="text-sm font-semibold text-gray-700">
                                        Atrasados
                                    </span>

                                </div>

                                <span class="text-sm font-bold text-gray-800">
                                    ${{ number_format($valorAtrasados, 2) }}
                                </span>
                            </div>

                            <div class="text-xs text-gray-400 mt-1 ml-5">
                                {{ $cantidadAtrasadosDona }}
                                {{ $cantidadAtrasadosDona == 1 ? 'pedido' : 'pedidos' }}
                                · {{ number_format($porcentajeAtrasados, 1) }}%
                            </div>
                        </div>


                        {{-- SIN FECHA --}}
                        <div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">

                                    <span
                                        style="
                        display: inline-block;
                        width: 12px;
                        height: 12px;
                        min-width: 12px;
                        min-height: 12px;
                        background-color: #9ca3af;
                        border-radius: 50%;
                        opacity: 1;
                        visibility: visible;
                    ">
                                    </span>

                                    <span class="text-sm font-semibold text-gray-700">
                                        Sin fecha
                                    </span>

                                </div>

                                <span class="text-sm font-bold text-gray-800">
                                    ${{ number_format($valorSinFecha, 2) }}
                                </span>
                            </div>

                            <div class="text-xs text-gray-400 mt-1 ml-5">
                                {{ $cantidadSinFechaDona }}
                                {{ $cantidadSinFechaDona == 1 ? 'pedido' : 'pedidos' }}
                                · {{ number_format($porcentajeSinFecha, 1) }}%
                            </div>
                        </div>

                    </div>


                </div>

                {{-- =====================================================
     PLANEACIÓN
====================================================== --}}

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

                    {{-- PLANEACIÓN SEMANAL --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

                        <div class="flex items-center justify-between mb-5">
                            <div>
                                <h2 class="text-lg font-bold text-gray-800">
                                    Planeación semanal
                                </h2>

                                <p class="text-sm text-gray-500 mt-1">
                                    Meta de venta semanal
                                </p>
                            </div>

                            <div class="text-2xl">
                                🎯
                            </div>
                        </div>

                        <div class="space-y-4">

                            <div>
                                <p class="text-xs text-gray-500">
                                    Meta semanal
                                </p>

                                <p class="text-2xl font-bold text-gray-800">
                                    ${{ number_format($metaSemanal, 2) }}
                                </p>
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs text-gray-500">
                                        Programado
                                    </span>

                                    <span class="text-sm font-semibold text-gray-700">
                                        ${{ number_format($programadoSemanal, 2) }}
                                    </span>
                                </div>

                                <div class="w-full bg-gray-100 rounded-full h-3">
                                    <div
                                        class="bg-blue-500 h-3 rounded-full"
                                        style="width: {{ min($porcentajeSemanal, 100) }}%">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-between items-center pt-2">

                                <div>
                                    <p class="text-xs text-gray-500">
                                        Faltante
                                    </p>

                                    <p class="text-lg font-bold text-gray-800">
                                        ${{ number_format($faltanteSemanal, 2) }}
                                    </p>
                                </div>

                                <div class="text-right">
                                    <p class="text-xs text-gray-500">
                                        Cumplimiento
                                    </p>

                                    <p class="text-lg font-bold text-blue-600">
                                        {{ number_format($porcentajeSemanal, 1) }}%
                                    </p>
                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- PLANEACIÓN MENSUAL --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

                        <div class="flex items-center justify-between mb-5">
                            <div>
                                <p class="text-sm text-gray-800 font-bold">
                                    Meta de venta mensual
                                </p>

                                <p class="text-xs text-gray-800 mt-1">
                                    Periodo: {{ $inicioSemana->locale('es')->translatedFormat('F Y') }}
                                </p>
                            </div>

                            <div class="text-2xl">
                                📅
                            </div>
                        </div>

                        <div class="space-y-4">

                            <div>
                                <p class="text-xs text-gray-500">
                                    Meta mensual
                                </p>

                                <p class="text-2xl font-bold text-gray-800">
                                    ${{ number_format($metaMensual, 2) }}
                                </p>
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs text-gray-500">
                                        Programado
                                    </span>

                                    <span class="text-sm font-semibold text-gray-700">
                                        ${{ number_format($programadoMensual, 2) }}
                                    </span>
                                </div>

                                <div class="w-full bg-gray-100 rounded-full h-3">
                                    <div
                                        class="bg-blue-500 h-3 rounded-full"
                                        style="width: {{ min($porcentajeMensual, 100) }}%">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-between items-center pt-2">

                                <div>
                                    <p class="text-xs text-gray-500">
                                        Faltante
                                    </p>

                                    <p class="text-lg font-bold text-gray-800">
                                        ${{ number_format($faltanteMensual, 2) }}
                                    </p>
                                </div>

                                <div class="text-right">
                                    <p class="text-xs text-gray-500">
                                        Cumplimiento
                                    </p>

                                    <p class="text-lg font-bold text-blue-600">
                                        {{ number_format($porcentajeMensual, 1) }}%
                                    </p>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- =====================================================
            CONTENEDOR GENERAL: PROYECCIÓN + ATENCIÓN
            Mismo ancho que "Situación económica"
        ====================================================== --}}
        <div class="max-w-screen-2xl mx-auto px-6">

        {{-- =====================================================
            PROYECCIÓN DE LA SEMANA ACTUAL + 3 SEMANAS
        ====================================================== --}}

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mt-6">

            <h2 class="text-lg font-bold text-gray-800">
                Proyección de la semana actual y próximas 3 semanas
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Valor de pedidos programados por fecha de entrega
            </p>

            {{-- ORDENAMIENTO --}}
            <div class="flex justify-end items-center gap-2 mt-4">
                <label for="ordenProyeccion" class="text-xs font-semibold text-gray-500">
                    Ordenar:
                </label>
                <select
                    id="ordenProyeccion"
                    onchange="ordenarProyeccion(this.value)"
                    class="text-xs border border-gray-300 rounded-lg px-3 py-2
                           text-gray-700 bg-white focus:ring-2 focus:ring-blue-500
                           focus:border-blue-500">
                    <option value="importe_desc">Importe: mayor a menor</option>
                    <option value="importe_asc">Importe: menor a mayor</option>
                </select>
            </div>


            {{-- 2 COLUMNAS --}}
            <div
                id="proyeccionSemanasLista"
                class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">


                @foreach($proyeccionSemanas as $semanaFutura)

                @php

                /*
                |--------------------------------------------------------------------------
                | Importe total de la semana
                |--------------------------------------------------------------------------
                */

                $importe = (float) $semanaFutura['importe'];


                /*
                |--------------------------------------------------------------------------
                | Importe ya entregado
                |--------------------------------------------------------------------------
                */

                $importeEntregado =
                (float) ($semanaFutura['importe_entregado'] ?? 0);


                /*
                |--------------------------------------------------------------------------
                | Importe pendiente
                |--------------------------------------------------------------------------
                */

                $importePendiente =
                max(0, $importe - $importeEntregado);


                /*
                |--------------------------------------------------------------------------
                | Porcentaje entregado
                |--------------------------------------------------------------------------
                */

                $porcentajeEntregado =
                $importe > 0
                ? ($importeEntregado / $importe) * 100
                : 0;


                /*
                |--------------------------------------------------------------------------
                | Porcentaje pendiente
                |--------------------------------------------------------------------------
                */

                $porcentajePendiente =
                100 - $porcentajeEntregado;


                /*
                |--------------------------------------------------------------------------
                | Evitar valores fuera de 0 - 100
                |--------------------------------------------------------------------------
                */

                $porcentajeEntregado =
                min(100, max(0, $porcentajeEntregado));

                $porcentajePendiente =
                min(100, max(0, $porcentajePendiente));

                @endphp


                {{-- =================================================
                 TARJETA DE LA SEMANA
                ================================================== --}}

                <div
                    class="border border-gray-200 rounded-lg p-4 proyeccion-card"
                    data-importe="{{ $importe }}">


                    {{-- SEMANA --}}
                    <div class="flex items-center justify-between">

                        <div class="text-sm font-semibold text-gray-700">

                            Semana {{ $semanaFutura['semana'] }}

                        </div>


                        <div class="text-xs text-gray-400">

                            {{ $semanaFutura['cantidad'] }}

                            {{ $semanaFutura['cantidad'] == 1
                            ? 'pedido'
                            : 'pedidos'
                        }}

                        </div>

                    </div>


                    {{-- IMPORTE TOTAL --}}
                    <div class="text-xl font-bold text-gray-800 mt-2">

                        ${{ number_format($importe, 2) }}

                    </div>


                    {{-- =================================================
                     BARRA DE AVANCE ECONÓMICO
                    ================================================== --}}

                    <div
                        class="w-full bg-gray-100 rounded-full h-2.5 mt-3 overflow-hidden flex">

                        {{-- IMPORTE ENTREGADO --}}
                        @if($porcentajeEntregado > 0)

                        <div
                            class="bg-green-500 h-2.5"
                            style="width: {{ $porcentajeEntregado }}%;">
                        </div>

                        @endif


                        {{-- IMPORTE PENDIENTE --}}
                        @if($porcentajePendiente > 0)

                        <div
                            class="bg-blue-500 h-2.5"
                            style="width: {{ $porcentajePendiente }}%;">
                        </div>

                        @endif

                    </div>


                    {{-- =================================================
                     INFORMACIÓN INFERIOR
                    ================================================== --}}

                    <div class="flex items-center justify-between mt-2">


                        {{-- FECHAS --}}
                        <div class="text-xs text-gray-400">

                            {{ $semanaFutura['inicio']->format('d/m/Y') }}

                            -

                            {{ $semanaFutura['fin']->format('d/m/Y') }}

                        </div>


                        {{-- IMPORTE ENTREGADO --}}
                        @if($importeEntregado > 0)

                        <button
                            type="button"
                            onclick="abrirModalPedidos('entregados', '{{ $semanaFutura['semana'] }}')"
                            class="text-base font-semibold text-green-600
                                    hover:text-green-700 hover:underline
                                    cursor-pointer text-left">

                            ${{ number_format(
                                $importeEntregado,
                                2
                            ) }}

                            entregado

                        </button>

                        @endif

                    </div>


                    {{-- IMPORTE PENDIENTE --}}
                    @if($importePendiente > 0)

                    <button
                        type="button"
                        onclick="abrirModalPedidos('pendientes', '{{ $semanaFutura['semana'] }}')"
                        class="text-base font-semibold text-blue-500 mt-1
                                hover:text-blue-700 hover:underline
                                cursor-pointer text-left">

                        ${{ number_format(
                            $importePendiente,
                            2
                        ) }}

                        pendiente

                    </button>

                    @endif


                </div>

                @endforeach

            </div>


            {{-- =====================================================
                LEYENDA
             ====================================================== --}}

            <div class="flex items-center justify-center gap-5 mt-4">


                {{-- ENTREGADO --}}
                <div class="flex items-center gap-2">

                    <span
                        style="
                    display: inline-block;
                    width: 10px;
                    height: 10px;
                    min-width: 10px;
                    background-color: #22c55e;
                    border-radius: 50%;
                ">
                    </span>

                    <span class="text-xs text-gray-500">
                        Entregado
                    </span>

                </div>


                {{-- PENDIENTE --}}
                <div class="flex items-center gap-2">

                    <span
                        style="
                    display: inline-block;
                    width: 10px;
                    height: 10px;
                    min-width: 10px;
                    background-color: #3b82f6;
                    border-radius: 50%;
                ">
                    </span>

                    <span class="text-xs text-gray-500">
                        Pendiente
                    </span>

                </div>


            </div>

        </div>

        {{-- =====================================================
            PEDIDOS QUE REQUIEREN ATENCIÓN
            ====================================================== --}}

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mt-6">

            <div class="px-6 py-4 border-b border-gray-200">

                <h2 class="text-lg font-bold text-gray-800">
                    Pedidos que requieren atención
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Pedidos atrasados o en riesgo
                </p>

                <div class="flex justify-end items-center gap-2 mt-3">
                    <label for="ordenAtencion" class="text-xs font-semibold text-gray-500">
                        Ordenar:
                    </label>
                    <select
                        id="ordenAtencion"
                        onchange="ordenarAtencion(this.value)"
                        class="text-xs border border-gray-300 rounded-lg px-3 py-2
                               text-gray-700 bg-white focus:ring-2 focus:ring-blue-500
                               focus:border-blue-500">
                        <option value="importe_desc">Importe: mayor a menor</option>
                        <option value="importe_asc">Importe: menor a mayor</option>
                        <option value="avance_desc">Avance: mayor a menor</option>
                        <option value="dias_desc">Días: mayor a menor</option>
                    </select>
                </div>

            </div>


            <div class="overflow-x-auto overflow-y-auto max-h-[500px] rounded-b-xl">

                <table class="w-full text-sm">

                    <thead class="sticky top-0 z-20 bg-gray-50 shadow-sm">

                        <tr>

                            {{-- PEDIDO --}}
                            <th class="px-4 py-3 text-left">
                                Pedido
                            </th>


                            {{-- CLIENTE --}}
                            <th class="px-4 py-3 text-left">
                                Cliente
                            </th>


                            {{-- FECHA DE ENTREGA --}}
                            <th class="px-4 py-3 text-left">
                                Entrega
                            </th>


                            {{-- DÍAS --}}
                            <th class="px-4 py-3 text-center">
                                Días
                            </th>


                            {{-- FECHA DE PRODUCCIÓN --}}
                            <th class="px-4 py-3 text-left">
                                Producción
                            </th>


                            {{-- AVANCE --}}
                            <th class="px-4 py-3 text-center">
                                Avance
                            </th>


                            {{-- IMPORTE --}}
                            <th class="px-4 py-3 text-right">
                                Importe
                            </th>


                            {{-- SITUACIÓN --}}
                            <th class="px-4 py-3 text-center">
                                Situación
                            </th>

                        </tr>

                    </thead>


                    <tbody id="tablaAtencionBody">

                        @forelse($pedidosAtencion as $pedido)

                        <tr
                            class="border-t border-gray-100 hover:bg-gray-50 fila-atencion"
                            data-importe="{{ (float) $pedido->importe_total }}"
                            data-avance="{{ ((float) $pedido->avance) * 100 }}"
                            data-dias="{{ $pedido->fecha_entrega ? \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($pedido->fecha_entrega)->startOfDay(), false) : 0 }}">

                            {{-- =================================================
                    PEDIDO
                    ================================================== --}}

                            <td class="px-4 py-3 font-semibold text-gray-700">

                                {{ $pedido->pedido_no ?? '—' }}

                            </td>


                            {{-- =================================================
                    CLIENTE
                    ================================================== --}}

                            <td class="px-4 py-3 text-gray-600">

                                {{ $pedido->cliente ?? '—' }}

                            </td>


                            {{-- =================================================
                    FECHA DE ENTREGA
                    ================================================== --}}

                            <td class="px-4 py-3 text-gray-500">

                                {{ $pedido->fecha_entrega
                            ? \Carbon\Carbon::parse(
                                $pedido->fecha_entrega
                            )->format('d/m/Y')
                            : '—'
                        }}

                            </td>


                            {{-- =================================================
                    DÍAS
                    ================================================== --}}

                            <td class="px-4 py-3 text-center">

                                @if($pedido->fecha_entrega)

                                @php

                                $fechaEntrega =
                                \Carbon\Carbon::parse(
                                $pedido->fecha_entrega
                                )->startOfDay();

                                $hoy =
                                \Carbon\Carbon::today();

                                /*
                                |--------------------------------------------------------------------------
                                | SIGNIFICADO DEL RESULTADO
                                |--------------------------------------------------------------------------
                                |
                                | Negativo = la fecha de entrega ya pasó → pedido atrasado
                                |
                                | Positivo = faltan días para la entrega
                                |
                                | Cero = la entrega es hoy
                                |
                                */

                                $dias =
                                $hoy->diffInDays(
                                $fechaEntrega,
                                false
                                );

                                @endphp


                                {{-- =========================================
        ATRASADO
        Ejemplo: -104, -87, -61
        ========================================== --}}

                                @if($dias < 0)

                                    <span
                                    class="inline-flex
                       items-center
                       justify-center
                       min-w-[38px]
                       px-2
                       py-1
                       text-xs
                       font-bold
                       rounded-full
                       bg-red-100
                       text-red-700">

                                    {{ $dias }}

                                    </span>


                                    {{-- =========================================
        ENTREGA FUTURA
        Ejemplo: 4, 7, 3
        ========================================== --}}

                                    @elseif($dias > 0)

                                    <span
                                        class="inline-flex
                       items-center
                       justify-center
                       min-w-[38px]
                       px-2
                       py-1
                       text-xs
                       font-bold
                       rounded-full
                       bg-yellow-100
                       text-yellow-700">

                                        {{ $dias }}

                                    </span>


                                    {{-- =========================================
        ENTREGA HOY
        ========================================== --}}

                                    @else

                                    <span
                                        class="inline-flex
                       items-center
                       justify-center
                       min-w-[38px]
                       px-2
                       py-1
                       text-xs
                       font-bold
                       rounded-full
                       bg-blue-100
                       text-blue-700">

                                        0

                                    </span>

                                    @endif


                                    @else

                                    <span class="text-gray-400">
                                        —
                                    </span>

                                    @endif

                            </td>


                            {{-- =================================================
                    FECHA DE PRODUCCIÓN
                    ================================================== --}}

                            <td class="px-4 py-3 text-gray-500">

                                {{ $pedido->controlOperativo?->fecha_produccion
                            ? \Carbon\Carbon::parse(
                                $pedido->controlOperativo->fecha_produccion
                            )->format('d/m/Y')
                            : '—'
                        }}

                            </td>


                            {{-- =================================================
                    AVANCE
                    ================================================== --}}

                            <td class="px-4 py-3 text-center">

                                {{ number_format(
                            ((float) $pedido->avance) * 100,
                            0
                        ) }}%

                            </td>


                            {{-- =================================================
                    IMPORTE
                    ================================================== --}}

                            <td class="px-4 py-3 text-right font-semibold">

                                ${{ number_format(
                            (float) $pedido->importe_total,
                            2
                        ) }}

                            </td>


                            {{-- =================================================
                    SITUACIÓN
                    ================================================== --}}

                            <td class="px-4 py-3 text-center">

                                @if($pedidosAtrasados->contains('id', $pedido->id))

                                <span
                                    style="
                                    display: inline-flex;
                                    padding: 4px 8px;
                                    font-size: 12px;
                                    font-weight: 600;
                                    border-radius: 9999px;
                                    background-color: #fee2e2;
                                    color: #b91c1c;
                                ">

                                    Atrasado

                                </span>


                                @elseif($pedidosEnRiesgo->contains('id', $pedido->id))

                                <span
                                    class="inline-flex
                                       px-2
                                       py-1
                                       text-xs
                                       font-semibold
                                       rounded-full
                                       bg-yellow-100
                                       text-yellow-700">

                                    En riesgo

                                </span>

                                @endif

                            </td>

                        </tr>


                        @empty

                        {{-- =================================================
                SIN PEDIDOS
                ================================================== --}}

                        <tr>

                            <td
                                colspan="8"
                                class="px-4 py-8 text-center text-gray-400">

                                No hay pedidos atrasados o en riesgo.

                            </td>

                        </tr>

                        @endforelse


                        {{-- =================================================
                RESUMEN
                ================================================== --}}

                        @if($pedidosAtencion->count() > 0)

                        <tr id="resumenAtencion" class="border-t-2 border-gray-200 bg-gray-50">


                            {{-- RESUMEN --}}

                            <td
                                colspan="6"
                                class="px-4 py-4">

                                <div class="flex items-center gap-2">

                                    <span class="text-sm font-bold text-gray-700">

                                        Total

                                    </span>


                                    <span class="text-xs text-gray-500">

                                        {{ $pedidosAtencion->count() }}

                                        {{ $pedidosAtencion->count() == 1
                                    ? 'pedido'
                                    : 'pedidos'
                                }}

                                    </span>

                                </div>

                            </td>


                            {{-- =================================================
                    IMPORTE TOTAL
                    ================================================== --}}

                            <td
                                class="px-4 py-4 text-right">

                                <span
                                    class="text-sm font-bold text-gray-800">

                                    ${{ number_format(
                                $pedidosAtencion->sum(
                                    'importe_total'
                                ),
                                2
                            ) }}

                                </span>

                            </td>


                            {{-- =================================================
                    TOTAL DE PEDIDOS
                    ================================================== --}}

                            <td
                                class="px-4 py-4 text-center">

                                <span
                                    class="inline-flex
                                   px-3
                                   py-1
                                   text-xs
                                   font-bold
                                   rounded-full
                                   bg-gray-100
                                   text-gray-700">

                                    {{ $pedidosAtencion->count() }}
                                    pedidos

                                </span>

                            </td>

                        </tr>

                        @endif


                    </tbody>

                </table>

            </div>

        </div>

        </div>

    </div>


    {{-- =====================================================
     MODAL DE PEDIDOS POR TARJETA
    ====================================================== --}}

    @php

    $datosModalTarjetas = [

    'por_iniciar' => $pedidosPorIniciar
    ->map(function ($pedido) {
    return [
    'pedido_no' => $pedido->pedido_no,
    'cliente' => $pedido->cliente,
    'estado' => $pedido->controlOperativo?->estado_operativo ?? 'POR INICIAR',
    'fecha_entrega' => !empty($pedido->fecha_entrega)
    ? \Carbon\Carbon::parse($pedido->fecha_entrega)->format('d/m/Y')
    : null,
    'avance' => round(((float) $pedido->avance) * 100, 1),
    'importe' => (float) $pedido->importe_total,
    ];
    })
    ->values()
    ->all(),

    'por_entregar' => $pedidosPorEntregar
    ->map(function ($pedido) {
    return [
    'pedido_no' => $pedido->pedido_no,
    'cliente' => $pedido->cliente,
    'estado' => $pedido->controlOperativo?->estado_operativo ?? 'EN PROCESO',
    'fecha_entrega' => !empty($pedido->fecha_entrega)
    ? \Carbon\Carbon::parse($pedido->fecha_entrega)->format('d/m/Y')
    : null,
    'avance' => round(((float) $pedido->avance) * 100, 1),
    'importe' => (float) $pedido->importe_total,
    ];
    })
    ->values()
    ->all(),

    'atrasado' => $pedidosAtrasados
    ->map(function ($pedido) {
    return [
    'pedido_no' => $pedido->pedido_no,
    'cliente' => $pedido->cliente,
    'estado' => $pedido->controlOperativo?->estado_operativo ?? '—',
    'fecha_entrega' => !empty($pedido->fecha_entrega)
    ? \Carbon\Carbon::parse($pedido->fecha_entrega)->format('d/m/Y')
    : null,
    'avance' => round(((float) $pedido->avance) * 100, 1),
    'importe' => (float) $pedido->importe_total,
    ];
    })
    ->values()
    ->all(),

    ];

    @endphp


    <div
        id="modalTarjetas"
        class="fixed inset-0 z-[110] hidden items-center justify-center
           bg-black/70 p-4"
        onclick="cerrarModalTarjeta(event)">

        <div
            class="bg-white rounded-2xl shadow-2xl w-[90%] max-w-4xl
           max-h-[80vh] overflow-hidden"
            onclick="event.stopPropagation()">

            {{-- ENCABEZADO --}}
            <div class="flex items-center justify-between
                    px-5 py-4 border-b border-gray-200">

                <div>

                    <h2
                        id="modalTarjetaTitulo"
                        class="text-lg font-bold text-gray-800">
                        Pedidos
                    </h2>

                    <p
                        id="modalTarjetaSubtitulo"
                        class="text-sm text-gray-500 mt-1">
                        Detalle de pedidos
                    </p>

                </div>

                <button
                    type="button"
                    onclick="cerrarModalTarjeta()"
                    class="w-9 h-9 rounded-full flex items-center
                       justify-center text-gray-400
                       hover:text-gray-700 hover:bg-gray-100
                       transition">

                    <span class="text-2xl leading-none">&times;</span>

                </button>

            </div>


            {{-- RESUMEN --}}
            <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">

                <div class="flex items-center justify-between">

                    <div
                        id="modalTarjetaCantidad"
                        class="text-sm text-gray-500">
                        0 pedidos
                    </div>

                    <div class="text-right">

                        <div class="text-xs text-gray-400">
                            Importe total
                        </div>

                        <div
                            id="modalTarjetaImporte"
                            class="text-xl font-bold text-gray-800">
                            $0.00
                        </div>

                    </div>

                </div>

            </div>


            {{-- TABLA --}}
            <div class="overflow-y-auto max-h-[55vh]">

                <table class="w-full text-sm">

                    <thead
                        class="sticky top-0 z-20 bg-white
                           border-b border-gray-200 shadow-sm">

                        <tr>

                            <th class="px-5 py-3 text-left text-xs
                                   font-semibold text-gray-500 uppercase">
                                Pedido
                            </th>

                            <th class="px-5 py-3 text-left text-xs
                                   font-semibold text-gray-500 uppercase">
                                Cliente
                            </th>

                            <th class="px-5 py-3 text-left text-xs
                                   font-semibold text-gray-500 uppercase">
                                Estado
                            </th>

                            <th class="px-5 py-3 text-left text-xs
                                   font-semibold text-gray-500 uppercase">
                                Entrega
                            </th>

                            <th class="px-5 py-3 text-center text-xs
                                   font-semibold text-gray-500 uppercase">
                                Avance
                            </th>

                            <th class="px-5 py-3 text-right text-xs
                                   font-semibold text-gray-500 uppercase">
                                Importe
                            </th>

                        </tr>

                    </thead>

                    <tbody
                        id="modalTarjetaLista"
                        class="divide-y divide-gray-100">
                    </tbody>

                </table>


                <div
                    id="modalTarjetaVacio"
                    class="hidden px-6 py-12 text-center">

                    <div class="text-3xl mb-2">
                        📋
                    </div>

                    <p class="text-sm font-semibold text-gray-600">
                        No hay pedidos para mostrar.
                    </p>

                </div>

            </div>


            {{-- PIE --}}
            <div class="flex justify-end px-5 py-3
                    border-t border-gray-200">

                <button
                    type="button"
                    onclick="cerrarModalTarjeta()"
                    class="px-5 py-3 text-sm font-semibold
                       text-gray-600 bg-gray-100
                       rounded-lg hover:bg-gray-200 transition">

                    Cerrar

                </button>

            </div>

        </div>

    </div>


    <script>
        const datosModalTarjetas =
            @json($datosModalTarjetas);


        function abrirModalTarjeta(tipo) {

            const modal =
                document.getElementById('modalTarjetas');

            const titulo =
                document.getElementById('modalTarjetaTitulo');

            const subtitulo =
                document.getElementById('modalTarjetaSubtitulo');

            const cantidad =
                document.getElementById('modalTarjetaCantidad');

            const importeTotal =
                document.getElementById('modalTarjetaImporte');

            const lista =
                document.getElementById('modalTarjetaLista');

            const vacio =
                document.getElementById('modalTarjetaVacio');


            const pedidos =
                datosModalTarjetas[tipo] || [];


            let tituloTexto = '';
            let subtituloTexto = '';


            if (tipo === 'por_iniciar') {

                tituloTexto = 'Pedidos por iniciar';
                subtituloTexto =
                    'Pedidos pendientes de comenzar producción';

            } else if (tipo === 'por_entregar') {

                tituloTexto = 'Pedidos por entregar';
                subtituloTexto =
                    'Pedidos en proceso con entrega pendiente';

            } else if (tipo === 'atrasado') {

                tituloTexto = 'Pedidos atrasados';
                subtituloTexto =
                    'Pedidos cuya fecha de entrega ya venció';
            }


            titulo.textContent = tituloTexto;
            subtitulo.textContent = subtituloTexto;


            cantidad.textContent =
                pedidos.length +
                (pedidos.length === 1 ?
                    ' pedido' :
                    ' pedidos');


            const total =
                pedidos.reduce(
                    function(suma, pedido) {
                        return suma + Number(pedido.importe || 0);
                    },
                    0
                );


            importeTotal.textContent =
                total.toLocaleString(
                    'es-MX', {
                        style: 'currency',
                        currency: 'MXN',
                        minimumFractionDigits: 2
                    }
                );


            lista.innerHTML = '';


            if (pedidos.length === 0) {

                vacio.classList.remove('hidden');

            } else {

                vacio.classList.add('hidden');


                pedidos.forEach(function(pedido) {

                    const fila =
                        document.createElement('tr');

                    fila.className =
                        'hover:bg-gray-50 transition';


                    let colorAvance =
                        'text-gray-700';


                    if (Number(pedido.avance) >= 100) {

                        colorAvance =
                            'text-green-600';

                    } else if (Number(pedido.avance) > 0) {

                        colorAvance =
                            'text-blue-600';

                    }


                    const importe =
                        Number(pedido.importe || 0)
                        .toLocaleString(
                            'es-MX', {
                                style: 'currency',
                                currency: 'MXN',
                                minimumFractionDigits: 2
                            }
                        );


                    fila.innerHTML = `

                    <td class="px-5 py-3">
                        <span class="font-semibold text-gray-700">
                            ${pedido.pedido_no ?? '—'}
                        </span>
                    </td>

                    <td class="px-5 py-3 text-gray-600">
                        ${pedido.cliente ?? '—'}
                    </td>

                    <td class="px-5 py-3">
                        <span class="text-xs font-semibold
                                     px-2 py-1 rounded-full
                                     whitespace-nowrap
                                     bg-gray-100 text-gray-700">
                            ${pedido.estado ?? '—'}
                        </span>
                    </td>

                    <td class="px-5 py-3 text-gray-600">
                        ${pedido.fecha_entrega ?? 'Sin fecha'}
                    </td>

                    <td class="px-5 py-3 text-center">
                        <span class="font-semibold ${colorAvance}">
                            ${Number(pedido.avance).toFixed(1)}%
                        </span>
                    </td>

                    <td class="px-5 py-3 text-right font-semibold">
                        ${importe}
                    </td>

                `;


                    lista.appendChild(fila);

                });

            }


            modal.classList.remove('hidden');
            modal.classList.add('flex');

            document.body.classList.add('overflow-hidden');
        }


        function cerrarModalTarjeta(event) {

            if (
                event &&
                event.target !== event.currentTarget
            ) {
                return;
            }


            const modal =
                document.getElementById('modalTarjetas');


            modal.classList.add('hidden');
            modal.classList.remove('flex');

            document.body.classList.remove('overflow-hidden');
        }


        document.addEventListener(
            'keydown',
            function(event) {

                if (event.key === 'Escape') {
                    cerrarModalTarjeta();
                }

            }
        );
    </script>

    {{-- =====================================================
         MODAL DE PEDIDOS ENTREGADOS / PENDIENTES
    ====================================================== --}}

    @php
    $datosModalProyeccion = [];

    foreach ($proyeccionSemanas as $semanaModal) {
    $numeroSemanaModal = (string) $semanaModal['semana'];

    $datosModalProyeccion[$numeroSemanaModal] = [
    'entregados' => $semanaModal['pedidos_entregados'] ?? [],
    'pendientes' => $semanaModal['pedidos_pendientes'] ?? [],
    ];
    }
    @endphp


    <div
        id="modalPedidosProyeccion"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 p-4"
        onclick="cerrarModalPedidos(event)">

        <div
            class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[78vh] overflow-hidden"
            onclick="event.stopPropagation()">

            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-200">

                <div>
                    <h2
                        id="modalPedidosTitulo"
                        class="text-lg font-bold text-gray-800">
                        Pedidos entregados
                    </h2>

                    <p
                        id="modalPedidosSubtitulo"
                        class="text-sm text-gray-500 mt-1">
                        Semana
                    </p>
                </div>

                <button
                    type="button"
                    onclick="cerrarModalPedidos()"
                    class="w-9 h-9 rounded-full flex items-center justify-center
                           text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">

                    <span class="text-2xl leading-none">&times;</span>

                </button>

            </div>


            <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">

                <div class="flex flex-wrap items-center justify-between gap-3">

                    <div
                        id="modalPedidosCantidad"
                        class="text-sm text-gray-500">
                        0 pedidos
                    </div>

                    <div class="text-right">

                        <div class="text-xs text-gray-400">
                            Importe
                        </div>

                        <div
                            id="modalPedidosTotal"
                            class="text-xl font-bold text-gray-800">
                            $0.00
                        </div>

                    </div>

                </div>

            </div>


            <div class="overflow-y-auto max-h-[48vh]">

                <table class="w-full text-sm">

                    <thead class="sticky top-0 z-20 bg-white border-b border-gray-200 shadow-sm">

                        <tr>

                            <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">
                                Pedido
                            </th>

                            <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">
                                Cliente
                            </th>

                            <th
                                id="modalPedidosFechaHeader"
                                class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">
                                Fecha
                            </th>

                            <th class="px-5 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">
                                Importe
                            </th>

                        </tr>

                    </thead>

                    <tbody
                        id="modalPedidosLista"
                        class="divide-y divide-gray-100">
                    </tbody>

                </table>


                <div
                    id="modalPedidosVacio"
                    class="hidden px-6 py-12 text-center">

                    <div class="text-3xl mb-2">
                        📋
                    </div>

                    <p class="text-sm font-semibold text-gray-600">
                        No hay pedidos para mostrar.
                    </p>

                    <p class="text-xs text-gray-400 mt-1">
                        Esta semana no tiene pedidos en esta categoría.
                    </p>

                </div>

            </div>


            <div class="flex justify-end px-5 py-3 border-t border-gray-200">

                <button
                    type="button"
                    onclick="cerrarModalPedidos()"
                    class="px-5 py-3 text-sm font-semibold
                           text-gray-600 bg-gray-100
                           rounded-lg hover:bg-gray-200 transition">

                    Cerrar

                </button>

            </div>

        </div>

    </div>


    @php
    $datosModalProyeccion = [];

    foreach ($proyeccionSemanas as $semanaModal) {
    $numeroSemanaModal = (string) $semanaModal['semana'];

    $datosModalProyeccion[$numeroSemanaModal] = [
    'entregados' => $semanaModal['pedidos_entregados'] ?? [],
    'pendientes' => $semanaModal['pedidos_pendientes'] ?? [],
    ];
    }
    @endphp

    <script>
        const datosModalProyeccion =
            @json($datosModalProyeccion);


        function abrirModalPedidos(tipo, semana) {

            const modal =
                document.getElementById('modalPedidosProyeccion');

            const titulo =
                document.getElementById('modalPedidosTitulo');

            const subtitulo =
                document.getElementById('modalPedidosSubtitulo');

            const cantidad =
                document.getElementById('modalPedidosCantidad');

            const total =
                document.getElementById('modalPedidosTotal');

            const lista =
                document.getElementById('modalPedidosLista');

            const vacio =
                document.getElementById('modalPedidosVacio');

            const encabezadoFecha =
                document.getElementById('modalPedidosFechaHeader');


            const datosSemana =
                datosModalProyeccion[String(semana)] || {
                    entregados: [],
                    pendientes: []
                };


            const pedidos =
                tipo === 'entregados' ?
                datosSemana.entregados :
                datosSemana.pendientes;


            const esEntregado =
                tipo === 'entregados';


            titulo.textContent =
                esEntregado ?
                'Pedidos entregados' :
                'Pedidos pendientes';


            subtitulo.textContent =
                'Semana ' + semana;


            encabezadoFecha.textContent =
                esEntregado ?
                'Fecha terminado' :
                'Fecha entrega';


            lista.innerHTML = '';


            cantidad.textContent =
                pedidos.length +
                (pedidos.length === 1 ? ' pedido' : ' pedidos');


            const totalImporte =
                pedidos.reduce(
                    function(suma, pedido) {
                        return suma + Number(pedido.importe || 0);
                    },
                    0
                );


            total.textContent =
                totalImporte.toLocaleString(
                    'es-MX', {
                        style: 'currency',
                        currency: 'MXN',
                        minimumFractionDigits: 2
                    }
                );


            if (pedidos.length === 0) {

                vacio.classList.remove('hidden');

            } else {

                vacio.classList.add('hidden');


                pedidos.forEach(function(pedido) {

                    const fila =
                        document.createElement('tr');

                    fila.className =
                        'hover:bg-gray-50 transition';


                    const fecha =
                        esEntregado ?
                        pedido.fecha_terminado :
                        pedido.fecha_entrega;


                    const importe =
                        Number(pedido.importe || 0)
                        .toLocaleString(
                            'es-MX', {
                                style: 'currency',
                                currency: 'MXN',
                                minimumFractionDigits: 2
                            }
                        );


                    fila.innerHTML = `

                        <td class="px-5 py-2.5">
                            <span class="font-semibold text-gray-700">
                                ${pedido.pedido_no ?? '—'}
                            </span>
                        </td>

                        <td class="px-5 py-2.5 text-gray-600">
                            ${pedido.cliente ?? '—'}
                        </td>

                        <td class="px-5 py-2.5 text-gray-500">
                            ${fecha ?? '—'}
                        </td>

                        <td class="px-5 py-2.5 text-right font-semibold">
                            ${importe}
                        </td>

                    `;


                    lista.appendChild(fila);

                });

            }


            modal.classList.remove('hidden');
            modal.classList.add('flex');

            document.body.classList.add('overflow-hidden');
        }


        function cerrarModalPedidos(event) {

            if (
                event &&
                event.target !== event.currentTarget
            ) {
                return;
            }


            const modal =
                document.getElementById('modalPedidosProyeccion');


            modal.classList.add('hidden');
            modal.classList.remove('flex');

            document.body.classList.remove('overflow-hidden');
        }


        document.addEventListener(
            'keydown',
            function(event) {

                if (event.key === 'Escape') {
                    cerrarModalPedidos();
                }

            }
        );
    </script>


    <script>
        function ordenarProyeccion(orden) {
            const contenedor = document.getElementById('proyeccionSemanasLista');
            if (!contenedor) return;

            const tarjetas = Array.from(
                contenedor.querySelectorAll('.proyeccion-card')
            );

            tarjetas.sort(function(a, b) {
                const importeA = Number(a.dataset.importe || 0);
                const importeB = Number(b.dataset.importe || 0);

                return orden === 'importe_asc'
                    ? importeA - importeB
                    : importeB - importeA;
            });

            tarjetas.forEach(function(tarjeta) {
                contenedor.appendChild(tarjeta);
            });
        }

        function ordenarAtencion(orden) {
            const cuerpo = document.getElementById('tablaAtencionBody');
            if (!cuerpo) return;

            const filas = Array.from(
                cuerpo.querySelectorAll('.fila-atencion')
            );

            filas.sort(function(a, b) {
                let valorA = 0;
                let valorB = 0;

                if (orden === 'avance_desc') {
                    valorA = Number(a.dataset.avance || 0);
                    valorB = Number(b.dataset.avance || 0);
                    return valorB - valorA;
                }

                if (orden === 'dias_desc') {
                    valorA = Number(a.dataset.dias || 0);
                    valorB = Number(b.dataset.dias || 0);
                    return valorB - valorA;
                }

                valorA = Number(a.dataset.importe || 0);
                valorB = Number(b.dataset.importe || 0);

                return orden === 'importe_asc'
                    ? valorA - valorB
                    : valorB - valorA;
            });

            const resumen = document.getElementById('resumenAtencion');

            filas.forEach(function(fila) {
                if (resumen) {
                    cuerpo.insertBefore(fila, resumen);
                } else {
                    cuerpo.appendChild(fila);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            ordenarProyeccion('importe_desc');
            ordenarAtencion('importe_desc');
        });
    </script>

</x-app-layout>
<x-app-layout>

    <div class="py-4 dashboard-produccion-refinado">

        <div class="w-full max-w-[1600px] mx-auto px-4">

            {{-- =========================================================
                 TÍTULO
            ========================================================== --}}

            <div class="bg-[#1f2937] text-white text-center
                        font-bold text-xl py-2 rounded-t">

                SISTEMA DE CONTROL DE PRODUCCIÓN RIJAYA

            </div>


            {{-- =========================================================
     RESUMEN SEMANAL
========================================================== --}}

            <div class="mt-5">

                {{-- =====================================================
         ENCABEZADO DEL RESUMEN
    ====================================================== --}}

                <div class="bg-[#1f2937]
                text-white
                rounded-t-xl
                px-5
                py-3
                flex
                items-center
                justify-between
                shadow-lg">

                    <div>

                        <div class="text-lg font-bold">
                            📊 RESUMEN SEMANAL
                        </div>

                        <div class="text-sm text-gray-300 mt-0.5">

                            Semana Operativa {{ $semana }}

                            ·

                            {{ $inicioSemana->format('d/m/Y') }}

                            -

                            {{ $finSemana->format('d/m/Y') }}

                        </div>

                    </div>

                    <div class="text-sm
                    bg-white/10
                    px-3
                    py-1.5
                    rounded-lg">

                        Carga de trabajo

                    </div>

                </div>


                {{-- =====================================================
                    DOS SECCIONES
====================================================== --}}

                <div class="grid grid-cols-1 lg:grid-cols-2
            gap-3
            items-stretch
            bg-[#111827]
            p-3
            rounded-b-xl
            shadow-lg">


                    {{-- =================================================
        ESTADO DE PRODUCCIÓN
    ================================================== --}}

                    <div class="rounded-xl
                overflow-hidden
                h-full
                border border-[#ed7d31]/40
                bg-[#1f2937]">

                        {{-- ENCABEZADO --}}

                        <div class="bg-[#ed7d31]
                    text-white
                    px-4
                    py-2.5
                    font-bold
                    text-center">

                            🏭 ESTADO DE PRODUCCIÓN



                        </div>


                        {{-- TARJETAS --}}

                        <div class="grid grid-cols-3
                    gap-2
                    p-3">


                            {{-- CARGA TOTAL --}}

                            <div class="bg-[#374151]
                        rounded-lg
                        h-[115px]
                        p-3
                        text-center
                        border border-gray-600
                        shadow-sm
                        hover:scale-[1.02]
                        transition
                        flex flex-col justify-center">

                                <div class="text-white
                            text-xs
                            font-semibold
                            uppercase">

                                    Carga Total

                                </div>

                                <div class="text-white
                            text-3xl
                            font-bold
                            mt-2">

                                    {{ $cargaTotal }}

                                </div>

                                <div class="text-white
                            text-xs
                            mt-1">

                                    Pedidos

                                </div>

                            </div>


                            {{-- EN PRODUCCIÓN --}}

                            <div class="bg-[#374151]
                        rounded-lg
                        h-[115px]
                        p-3
                        text-center
                        border border-orange-400/30
                        shadow-sm
                        hover:scale-[1.02]
                        transition
                        flex flex-col justify-center">

                                <div class="text-orange-300
                            text-xs
                            font-semibold
                            uppercase">

                                    En Producción

                                </div>

                                <div class="text-white
                            text-3xl
                            font-bold
                            mt-2">

                                    {{ $enProduccion }}

                                </div>

                                <div class="text-gray-400
                            text-xs
                            mt-1">

                                    Pedidos

                                </div>

                            </div>


                            {{-- TERMINADOS --}}

                            <div class="bg-[#374151]
                        rounded-lg
                        h-[115px]
                        p-3
                        text-center
                        border border-green-400/30
                        shadow-sm
                        hover:scale-[1.02]
                        transition
                        flex flex-col justify-center">

                                <div class="text-green-300
                            text-xs
                            font-semibold
                            uppercase">

                                    Terminados

                                </div>

                                <div class="text-white
                            text-3xl
                            font-bold
                            mt-2">

                                    {{ $terminados }}

                                </div>

                                <div class="text-gray-400
                            text-xs
                            mt-1">

                                    Pedidos

                                </div>

                            </div>

                        </div>

                    </div>



                    {{-- =================================================
        ESTADO DE CUMPLIMIENTO
    ================================================== --}}

                    <div class="rounded-xl
                overflow-hidden
                h-full
                border border-[#00b0f0]/40
                bg-[#1f2937]">

                        {{-- ENCABEZADO --}}

                        <div class="bg-[#00b0f0]
                    text-white
                    px-4
                    py-2.5
                    font-bold
                    text-center">

                            ⏰ ESTADO DE CUMPLIMIENTO

                        </div>


                        {{-- TARJETAS --}}

                        <div class="grid grid-cols-4
                    gap-2
                    p-3">


                            {{-- EN TIEMPO --}}

                            <div class="bg-[#374151]
                        rounded-lg
                        h-[115px]
                        p-3
                        text-center
                        border border-green-400/30
                        shadow-sm
                        hover:scale-[1.02]
                        transition
                        flex flex-col justify-center">

                                <div class="flex
                            justify-center
                            items-center
                            gap-1">

                                    <span class="w-2 h-2
                                 rounded-full
                                 bg-green-500">
                                    </span>

                                    <span class="text-green-300
                                 text-[11px]
                                 font-semibold
                                 uppercase">

                                        En Tiempo

                                    </span>

                                </div>

                                <div class="text-white
                            text-2xl
                            font-bold
                            mt-1">

                                    {{ $enTiempo }}

                                </div>

                                <div class="text-white
                            text-[10px]
                            mt-0.5">

                                    Pedidos

                                </div>

                            </div>



                            {{-- RECUPERADOS --}}

                            <div class="bg-[#374151]
                                rounded-lg
                                h-[115px]
                                p-3
                                text-center
                                border border-orange-400/30
                                shadow-sm
                                hover:scale-[1.02]
                                transition
                                flex flex-col justify-center">

                                <div style="
                                color: #00b0f0 !important;
                                font-size: 11px;
                                font-weight: 600;
                                text-transform: uppercase;
                            ">

                                    RECUPERADOS

                                </div>

                                <div class="text-white
                            text-2xl
                            font-bold
                            mt-1">

                                    {{ $recuperados }}

                                </div>

                                <div class="text-gray-400
                            text-[10px]
                            mt-0.5">

                                    Pedidos

                                </div>

                            </div>



                            {{-- EN RIESGO --}}

                            <div class="bg-[#374151]
                        rounded-lg
                        h-[115px]
                        p-3
                        text-center
                        border border-yellow-400/30
                        shadow-sm
                        hover:scale-[1.02]
                        transition
                        flex flex-col justify-center">

                                <div class="flex
                            justify-center
                            items-center
                            gap-1">

                                    <span class="w-2 h-2
                                 rounded-full
                                 bg-yellow-400">
                                    </span>

                                    <span style="
                            color:#facc15 !important;
                            font-size:11px;
                            font-weight:600;
                            text-transform:uppercase;
                          ">

                                        En Riesgo

                                    </span>

                                </div>

                                <div class="text-white
                            text-2xl
                            font-bold
                            mt-1">

                                    {{ $enRiesgo }}

                                </div>

                                <div class="text-white
                            text-[10px]
                            mt-0.5">

                                    Pedidos

                                </div>

                            </div>



                            {{-- ATRASADOS --}}

                            <div class="bg-[#374151]
                                    rounded-lg
                                    h-[115px]
                                    p-3
                                    text-center
                                    border border-red-400/30
                                    shadow-sm
                                    hover:scale-[1.02]
                                    transition
                                    flex flex-col justify-center">

                                <div class="flex
                                    justify-center
                                    items-center
                                    gap-1">

                                    <span class="w-2 h-2
                                        rounded-full
                                        bg-red-500">
                                    </span>

                                    <span style="
                                            color:#f87171 !important;
                                            font-size:11px;
                                            font-weight:600;
                                            text-transform:uppercase;
                                        ">

                                        Atrasados

                                    </span>

                                </div>

                                <div class="text-white
                                    text-2xl
                                    font-bold
                                    mt-1">

                                    {{ $atrasados }}

                                </div>

                                <div class="text-white
                                        text-[10px]
                                        mt-0.5">

                                    Pedidos

                                </div>

                            </div>


                        </div>

                    </div>

                </div>

            </div>



            {{-- =========================================================
    ESTADÍSTICAS DE PEDIDOS INTERNOS
========================================================== --}}

            <div class="mt-3">

                <div class="rounded-xl overflow-hidden
                border border-purple-400/40
                bg-[#1f2937]">

                    {{-- ENCABEZADO --}}
                    <div class="bg-purple-600
                    text-white
                    px-3
                    py-1.5
                    text-center
                    font-bold
                    text-sm">

                        📦 PEDIDOS INTERNOS

                    </div>


                    {{-- =================================================
            TARJETAS EN UNA SOLA FILA
        ================================================== --}}
                    <div class="flex flex-nowrap gap-2 p-2">


                        {{-- CARGA TOTAL --}}
                        <div class="flex-1 min-w-0
                        bg-[#111827]
                        rounded-lg
                        px-2
                        py-3
                        text-center
                        border border-purple-400/30
                        shadow-sm
                        hover:scale-[1.02]
                        transition">

                            <div class="text-purple-300
                                    text-[11px]
                                    font-bold
                                    uppercase
                                    whitespace-nowrap
                                    tracking-wide"
                                style="color: #d8b4fe;">
                                Carga Total
                            </div>

                            <div class="text-white
                            text-2xl
                            font-bold
                            leading-none
                            mt-2">
                                {{ $internosCargaTotal ?? 0 }}
                            </div>

                            <div class="text-gray-400
                            text-[9px]
                            mt-1">
                                Pedidos
                            </div>

                        </div>


                        {{-- EN PRODUCCIÓN --}}
                        <div class="flex-1 min-w-0
                        bg-[#111827]
                        rounded-lg
                        px-2
                        py-3
                        text-center
                        border border-orange-400/30
                        shadow-sm
                        hover:scale-[1.02]
                        transition">

                            <div class="text-orange-300
                            text-[10px]
                            font-semibold
                            uppercase
                            whitespace-nowrap">
                                En Producción
                            </div>

                            <div class="text-white
                            text-2xl
                            font-bold
                            leading-none
                            mt-2">
                                {{ $internosEnProduccion ?? 0 }}
                            </div>

                            <div class="text-gray-400
                            text-[9px]
                            mt-1">
                                Pedidos
                            </div>

                        </div>


                        {{-- TERMINADOS --}}
                        <div class="flex-1 min-w-0
                        bg-[#111827]
                        rounded-lg
                        px-2
                        py-3
                        text-center
                        border border-green-400/30
                        shadow-sm
                        hover:scale-[1.02]
                        transition">

                            <div class="text-green-300
                            text-[10px]
                            font-semibold
                            uppercase
                            whitespace-nowrap">
                                Terminados
                            </div>

                            <div class="text-white
                            text-2xl
                            font-bold
                            leading-none
                            mt-2">
                                {{ $internosTerminados ?? 0 }}
                            </div>

                            <div class="text-gray-400
                            text-[9px]
                            mt-1">
                                Pedidos
                            </div>

                        </div>


                        {{-- RECUPERADOS --}}
                        <div class="flex-1 min-w-0
                        bg-[#111827]
                        rounded-lg
                        px-2
                        py-3
                        text-center
                        border border-cyan-400/30
                        shadow-sm
                        hover:scale-[1.02]
                        transition">

                            <div class="text-cyan-300
                            text-[10px]
                            font-semibold
                            uppercase
                            whitespace-nowrap">
                                Recuperados
                            </div>

                            <div class="text-white
                            text-2xl
                            font-bold
                            leading-none
                            mt-2">
                                {{ $internosRecuperados ?? 0 }}
                            </div>

                            <div class="text-gray-400
                            text-[9px]
                            mt-1">
                                Pedidos
                            </div>

                        </div>


                        {{-- ATRASADOS --}}
                        <div class="flex-1 min-w-0
                        bg-[#111827]
                        rounded-lg
                        px-2
                        py-3
                        text-center
                        border border-red-400/30
                        shadow-sm
                        hover:scale-[1.02]
                        transition">

                            <div class="text-red-300
                            text-[10px]
                            font-semibold
                            uppercase
                            whitespace-nowrap">
                                Atrasados
                            </div>

                            <div class="text-white
                            text-2xl
                            font-bold
                            leading-none
                            mt-2">
                                {{ $internosAtrasados ?? 0 }}
                            </div>

                            <div class="text-gray-400
                            text-[9px]
                            mt-1">
                                Pedidos
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =========================================================
                CONFIGURACIÓN DEL DASHBOARD
            ========================================================== --}}

            <form
                method="GET"
                action="{{ route('dashboard.produccion') }}"
                class="flex flex-wrap items-center justify-center gap-4 mt-3">

                {{-- MODO --}}
                <div class="flex items-center gap-2">

                    <label
                        for="modo"
                        class="font-semibold text-sm text-white">
                        Modo
                    </label>

                    <select
                        id="modo"
                        name="modo"
                        onchange="cambiarModoDashboard()"
                        class="
                            bg-white
                            text-gray-900
                            border border-gray-300
                            rounded-lg
                            px-3 py-2
                            text-sm
                            font-semibold
                            w-[150px]
                            focus:ring-2
                            focus:ring-blue-500
                        ">

                        <option
                            value="automatico"
                            {{ strtolower($modo ?? 'automatico') === 'automatico' ? 'selected' : '' }}>
                            Automático
                        </option>

                        <option
                            value="manual"
                            {{ strtolower($modo ?? 'automatico') === 'manual' ? 'selected' : '' }}>
                            Manual
                        </option>

                    </select>

                </div>


                {{-- SEMANA --}}
                <div class="flex items-center gap-2">

                    <label
                        for="semana"
                        class="font-semibold text-sm text-white">
                        Semana
                    </label>

                    <select
                        id="semana"
                        name="semana"
                        class="
                            bg-white
                            text-gray-900
                            border border-gray-300
                            rounded-lg
                            px-3 py-2
                            text-sm
                            font-semibold
                            w-[150px]
                            focus:ring-2
                            focus:ring-blue-500
                        ">

                        @foreach($semanasOperativas as $semanaO)

                        <option
                            value="{{ $semanaO->semana }}"
                            {{ (int)$semanaO->semana === (int)$semana ? 'selected' : '' }}>
                            Semana {{ $semanaO->semana }}
                        </option>

                        @endforeach

                    </select>

                </div>


                {{-- BOTÓN --}}
                <button
                    type="submit"
                    class="
                        bg-blue-500
                        hover:bg-blue-600
                        text-white
                        font-semibold
                        px-5
                        py-2
                        rounded-lg
                        shadow
                        transition
                    ">
                    Actualizar Dashboard
                </button>

            </form>


            <script>
                function cambiarModoDashboard() {

                    const modo = document.getElementById('modo');
                    const semana = document.getElementById('semana');

                    if (modo.value === 'automatico') {

                        semana.disabled = true;

                        semana.classList.add(
                            'bg-gray-200',
                            'text-gray-500',
                            'cursor-not-allowed'
                        );

                    } else {

                        semana.disabled = false;

                        semana.classList.remove(
                            'bg-gray-200',
                            'text-gray-500',
                            'cursor-not-allowed'
                        );

                    }

                }


                document.addEventListener('DOMContentLoaded', function() {

                    cambiarModoDashboard();

                });
            </script>



            {{-- =========================================================
     AVANCE SEMANAL
========================================================== --}}

            <div class="mt-4">

                <div class="bg-[#1f2937]
                text-white
                rounded-t-xl
                px-5
                py-3
                flex
                items-center
                justify-between
                shadow">

                    <div>
                        <div class="text-lg font-bold">
                            📈 AVANCE SEMANAL
                        </div>

                        <div class="text-sm text-gray-300">
                            Avance de la carga de trabajo
                            sin pedidos internos
                        </div>
                    </div>

                    <div class="text-3xl font-bold">
                        {{ number_format($avanceSemanal * 100, 0) }}%
                    </div>

                </div>


                @php

                $porcentajeAvance = $avanceSemanal * 100;

                if ($porcentajeAvance < 30) {

                    $colorBarra='#dc3545' ;

                    } elseif ($porcentajeAvance < 60) {

                    $colorBarra='#ff8c00' ;

                    } elseif ($porcentajeAvance < 85) {

                    $colorBarra='#ffc107' ;

                    } else {

                    $colorBarra='#28a745' ;

                    }

                    @endphp


                    <div class="bg-gray-200
                border-x
                border-b
                border-gray-300
                rounded-b-xl
                p-5
                shadow">


                    {{-- BARRA DE AVANCE --}}

                    {{-- BARRA DE AVANCE --}}

                    <div
                        style="
        width: 100%;
        height: 32px;
        background-color: #d1d5db;
        border-radius: 9999px;
        overflow: hidden;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.15);
    ">

                        <div
                            style="
            width: {{ $porcentajeAvance }}%;
            height: 32px;
            min-width: {{ $porcentajeAvance > 0 ? '15px' : '0px' }};
            background-color: {{ $colorBarra }};
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 12px;
            box-sizing: border-box;
            color: white;
            font-weight: bold;
            font-size: 14px;
            transition: width 0.7s ease;
        ">

                            @if($porcentajeAvance > 0)
                            {{ number_format($porcentajeAvance, 0) }}%
                            @endif

                        </div>

                    </div>


                    {{-- ESCALA PROPORCIONAL --}}

                    <div
                        style="
        position: relative;
        width: 100%;
        height: 22px;
        margin-top: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #374151;
    ">

                        {{-- 0% --}}
                        <span
                            style="
            position: absolute;
            left: 0%;
            transform: translateX(0);
        ">
                            0%
                        </span>

                        {{-- 30% --}}
                        <span
                            style="
            position: absolute;
            left: 30%;
            transform: translateX(-50%);
        ">
                            30%
                        </span>

                        {{-- 60% --}}
                        <span
                            style="
            position: absolute;
            left: 60%;
            transform: translateX(-50%);
        ">
                            60%
                        </span>

                        {{-- 85% --}}
                        <span
                            style="
            position: absolute;
            left: 85%;
            transform: translateX(-50%);
        ">
                            85%
                        </span>

                        {{-- 100% --}}
                        <span
                            style="
            position: absolute;
            right: 0%;
            transform: translateX(0);
        ">
                            100%
                        </span>

                    </div>


                    {{-- INFORMACIÓN --}}

                    <div class="text-center mt-3">

                        <span class="text-gray-700 font-semibold">

                            Avance promedio de la carga:

                        </span>

                        <span
                            class="font-bold text-lg"
                            style="color: {{ $colorBarra }};">

                            {{ number_format($avanceSemanal * 100, 0) }}%

                        </span>

                    </div>

            </div>

        </div>


        {{-- =========================================================
            GRÁFICAS SEMANALES
        ========================================================== --}}

        <div
            style="
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 20px;
                margin-top: 24px;
                align-items: stretch;
                width: 100%;
            ">

            {{-- =====================================================
                COLUMNA 1 - ENTREGAS A TIEMPO (ODT)
            ====================================================== --}}

            <div
                style="
                    background: #ffffff;
                    border-radius: 8px;
                    padding: 12px;
                    min-width: 0;
                    width: 100%;
                    height: 100%;
                    box-sizing: border-box;
                ">

                {{-- =================================================
                        DATOS DEL DONUT
                    ================================================== --}}

                @php

                $totalDonut =
                $otdATiempo +
                $otdFueraTiempo +
                $otdPendientes;

                if ($totalDonut > 0) {

                $porcentajeTiempo =
                ($otdATiempo / $totalDonut) * 100;

                $porcentajeFuera =
                ($otdFueraTiempo / $totalDonut) * 100;

                $porcentajePendiente =
                ($otdPendientes / $totalDonut) * 100;

                } else {

                $porcentajeTiempo = 0;
                $porcentajeFuera = 0;
                $porcentajePendiente = 100;

                }

                $inicioFuera = $porcentajeTiempo;

                $inicioPendiente =
                $porcentajeTiempo +
                $porcentajeFuera;

                @endphp


                {{-- =================================================
                    TÍTULO
                ================================================== --}}

                <div
                    style="
                        text-align: center;
                        ">

                    <div
                        style="
                            font-size: 18px;
                            font-weight: 700;
                            color: #111827;
                        ">
                        Entregas a Tiempo (ODT)
                    </div>


                    <div
                        style="
                            font-size: 13px;
                            font-weight: 600;
                            color: #111827;
                            margin-top: 4px;
                        ">
                        Semana {{ $semana }}
                    </div>


                    <div
                        style="
                            font-size: 13px;
                            font-weight: 600;
                            color: #111827;
                            margin-top: 4px;
                        ">
                        {{ $inicioSemana->format('d M') }}
                        -
                        {{ $finSemana->format('d M') }}
                    </div>

                </div>


                {{-- =================================================
                    LEYENDA
                ================================================== --}}

                <div
                    style="
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        gap: 12px;
                        margin-top: 14px;
                        font-size: 10px;
                        white-space: nowrap;
                    ">

                    {{-- A TIEMPO --}}

                    <div
                        style="
                            display: flex;
                            align-items: center;
                            gap: 4px;
                        ">

                        <span
                            style="
                                width: 6px;
                                height: 6px;
                                display: inline-block;
                                background-color: #65ad3d;
                            "></span>

                        <span style="color:#6b7280;">
                            A Tiempo
                        </span>

                    </div>


                    {{-- FUERA DE TIEMPO --}}

                    <div
                        style="
                            display: flex;
                            align-items: center;
                            gap: 4px;
                        ">

                        <span
                            style="
                        width: 6px;
                        height: 6px;
                        display: inline-block;
                        background-color: #f47b20;
                         "></span>

                        <span style="color:#6b7280;">
                            Fuera de Tiempo
                        </span>

                    </div>


                    {{-- PENDIENTES --}}

                    <div
                        style="
                            display: flex;
                            align-items: center;
                            gap: 4px;
                        ">

                        <span
                            style="
                                width: 6px;
                                height: 6px;
                                display: inline-block;
                                background-color: #a6a6a6;
                            "></span>

                        <span style="color:#6b7280;">
                            Pendientes
                        </span>

                    </div>

                </div>


                {{-- =================================================
                    DONA
                ================================================== --}}

                <div
                    style="
                        display: flex;
                        justify-content: center;
                        margin-top: 14px;
                    ">

                    <div
                        style="
                            width: 150px;
                            height: 150px;
                            border-radius: 50%;
                            background:
                                conic-gradient(
                                    #65ad3d 0% {{ $porcentajeTiempo }}%,
                                    #f47b20 {{ $inicioFuera }}% {{ $inicioPendiente }}%,
                                    #a6a6a6 {{ $inicioPendiente }}% 100%
                                );
                            position: relative;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            ">

                        {{-- CENTRO DE LA DONA --}}

                        <div
                            style="
                                width: 116px;
                                height: 116px;
                                border-radius: 50%;
                                background: #ffffff;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            ">

                            <span
                                style="
                            font-size: 28px;
                            font-weight: 700;
                            color: #000000;
                        ">
                                {{ number_format($otdPorcentaje * 100, 0) }}%
                            </span>

                        </div>

                    </div>

                </div>


                {{-- INFORMACIÓN --}}

                <div
                    style="
                        margin-top:10px;
                        font-size:11px;
                        font-weight:600;
                        color:#111827;
                        line-height:1.45;
                    ">

                    {{-- PROGRAMADOS --}}
                    <div>
                        Programados: {{ $otdProgramados }}
                    </div>

                    {{-- ENTREGADOS --}}
                    <div>
                        Entregados: {{ $otdEntregados }}
                    </div>

                    {{-- DETALLE DE ENTREGADOS --}}
                    <div style="
                            margin-left:15px;
                            color:#64748b;
                            font-weight:500;
                        ">

                        <div>
                            • A Tiempo: {{ $otdATiempo }}
                        </div>

                        <div>
                            • Fuera de Tiempo: {{ $otdFueraTiempo }}
                        </div>

                    </div>

                    {{-- PENDIENTES --}}
                    <div style="margin-top:6px;">
                        No Entregados: {{ $otdPendientes }}
                    </div>

                </div>

            </div>


            {{-- =====================================================
                COLUMNA 2 - AVANCE DE PRODUCCIÓN
            ====================================================== --}}

            <div
                style="
                    background: #ffffff;
                    border-radius: 8px;
                    padding: 12px;
                    min-width: 0;
                    width: 100%;
                    height: 100%;
                    box-sizing: border-box;
                ">

                @php

                /*
                |--------------------------------------------------------------------------
                | CARGA LABORAL
                |--------------------------------------------------------------------------
                */

                $totalProduccionDonut = $produccionCargaTotal;


                /*
                |--------------------------------------------------------------------------
                | TERMINADOS
                |
                | Los recuperados ya están incluidos dentro de los terminados.
                | Por eso los separamos para que la dona no los cuente dos veces.
                |--------------------------------------------------------------------------
                */

                $terminadosNormales =
                max(
                0,
                $produccionTerminados - $produccionRecuperados
                );


                $recuperados =
                $produccionRecuperados;


                $enProduccion =
                $produccionEnProduccion;


                /*
                |--------------------------------------------------------------------------
                | PORCENTAJES DE LA DONA
                |--------------------------------------------------------------------------
                */

                if ($totalProduccionDonut > 0) {

                $porcentajeTerminados =
                ($terminadosNormales / $totalProduccionDonut) * 100;

                $porcentajeRecuperados =
                ($recuperados / $totalProduccionDonut) * 100;

                $porcentajeEnProduccion =
                ($enProduccion / $totalProduccionDonut) * 100;

                } else {

                $porcentajeTerminados = 0;
                $porcentajeRecuperados = 0;
                $porcentajeEnProduccion = 0;

                }


                /*
                |--------------------------------------------------------------------------
                | POSICIONES DEL DONUT
                |--------------------------------------------------------------------------
                */

                $inicioRecuperados =
                $porcentajeTerminados;

                $inicioProduccion =
                $porcentajeTerminados +
                $porcentajeRecuperados;

                @endphp


                {{-- TÍTULO --}}

                <div style="text-align:center;">

                    <div
                        style="
                            font-size:18px;
                            font-weight:700;
                            color:#111827;
                        ">
                        Avance de Producción
                    </div>

                    <div
                        style="
                            font-size:13px;
                            font-weight:600;
                            color:#111827;
                            margin-top:4px;
                        ">
                        Semana {{ $semana }}
                    </div>

                    <div
                        style="
                            font-size:13px;
                            font-weight:600;
                            color:#111827;
                            margin-top:4px;
                        ">
                        {{ $inicioSemana->format('d M') }}
                        -
                        {{ $finSemana->format('d M') }}
                    </div>

                </div>


                {{-- LEYENDA --}}

                <div
                    style="
                        display:flex;
                        justify-content:center;
                        align-items:center;
                        gap:12px;
                        margin-top:14px;
                        font-size:10px;
                        white-space:nowrap;
                    ">

                    {{-- TERMINADOS --}}

                    <div
                        style="
                            display:flex;
                            align-items:center;
                            gap:4px;
                        ">

                        <span
                            style="
                                width:6px;
                                height:6px;
                                display:inline-block;
                                background-color:#65ad3d;
                            "></span>

                        <span style="color:#6b7280;">
                            Terminados
                        </span>

                    </div>


                    {{-- RECUPERADOS --}}

                    <div
                        style="
                            display:flex;
                            align-items:center;
                            gap:4px;
                        ">

                        <span
                            style="
                                width:6px;
                                height:6px;
                                display:inline-block;
                                background-color:#f47b20;
                            "></span>

                        <span style="color:#6b7280;">
                            Recuperados
                        </span>

                    </div>


                    {{-- EN PRODUCCIÓN --}}

                    <div
                        style="
                            display:flex;
                            align-items:center;
                            gap:4px;
                        ">

                        <span
                            style="
                                width:6px;
                                height:6px;
                                display:inline-block;
                                background-color:#a6a6a6;
                            "></span>

                        <span style="color:#6b7280;">
                            En Producción
                        </span>

                    </div>

                </div>


                {{-- DONA --}}

                <div
                    style="
                        display:flex;
                        justify-content:center;
                        margin-top:14px;
                    ">

                    <div
                        style="
                            width:150px;
                            height:150px;
                            border-radius:50%;
                            background:
                                conic-gradient(
                                    #65ad3d 0% {{ $porcentajeTerminados }}%,
                                    #f47b20 {{ $inicioRecuperados }}% {{ $inicioProduccion }}%,
                                    #a6a6a6 {{ $inicioProduccion }}% 100%
                                );
                            position:relative;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                        ">

                        <div
                            style="
                                width:116px;
                                height:116px;
                                border-radius:50%;
                                background:#ffffff;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                            ">

                            <span
                                style="
                                    font-size:28px;
                                    font-weight:700;
                                    color:#000000;
                                ">
                                {{ number_format($produccionPorcentaje * 100, 0) }}%
                            </span>

                        </div>

                    </div>

                </div>


                {{-- INFORMACIÓN --}}

                <div
                    style="
                    margin-top:10px;
                    font-size:11px;
                    font-weight:600;
                    color:#111827;
                    line-height:1.45;
                    ">

                    {{-- CARGA TOTAL --}}
                    <div>
                        Carga Total: {{ $produccionCargaTotal }}
                    </div>

                    {{-- TERMINADOS --}}
                    <div>
                        Terminados: {{ $produccionTerminados }}
                    </div>

                    {{-- DETALLE DE TERMINADOS --}}
                    <div style="
                            margin-left:15px;
                            color:#64748b;
                            font-weight:500;
                        ">

                        <div>
                            • De la Semana: {{ $produccionTerminadosSemana }}
                        </div>

                        <div>
                            • Recuperados: {{ $produccionRecuperados }}
                        </div>

                    </div>

                    {{-- EN PRODUCCIÓN --}}
                    <div style="margin-top:6px;">
                        En Producción: {{ $produccionEnProduccion }}
                    </div>

                </div>

            </div>


            {{-- =====================================================
                COLUMNA 3 - TENDENCIA OTD
            ====================================================== --}}

            <div
                style="
                    background: #ffffff;
                    border-radius: 8px;
                    padding: 12px;
                    min-width: 0;
                    width: 100%;
                    height: 100%;
                    box-sizing: border-box;
                ">

                {{-- =================================================
                    TÍTULO
                ================================================== --}}

                <div style="text-align:center;">

                    <div
                        style="
                font-size:18px;
                font-weight:700;
                color:#111827;
            ">
                        Tendencia de Entregas a Tiempo
                    </div>

                </div>


                {{-- =================================================
                    GRÁFICA
                ================================================== --}}

                @php

                $anchoGrafica = 430;
                $altoGrafica = 260;

                $margenIzquierdo = 45;
                $margenDerecho = 15;
                $margenSuperior = 25;
                $margenInferior = 55;

                $anchoUtil =
                $anchoGrafica
                - $margenIzquierdo
                - $margenDerecho;

                $altoUtil =
                $altoGrafica
                - $margenSuperior
                - $margenInferior;

                @endphp


                <div
                    style="
                        width:100%;
                        overflow:hidden;
                        margin-top:10px;
                    ">

                    <svg
                        viewBox="0 0 {{ $anchoGrafica }} {{ $altoGrafica }}"
                        width="100%"
                        height="260"
                        preserveAspectRatio="none">

                        {{-- =============================================
                            LÍNEAS HORIZONTALES
                        ============================================== --}}

                        @for($nivel = 0; $nivel
                        <= 100; $nivel +=20)

                            @php

                            $y=$margenSuperior
                            +
                            $altoUtil
                            -
                            (($nivel / 100) * $altoUtil);

                            @endphp

                            <line
                            x1="{{ $margenIzquierdo }}"
                            y1="{{ $y }}"
                            x2="{{ $margenIzquierdo + $anchoUtil }}"
                            y2="{{ $y }}"
                            stroke="#d1d5db"
                            stroke-width="1" />

                        <text
                            x="{{ $margenIzquierdo - 6 }}"
                            y="{{ $y + 4 }}"
                            text-anchor="end"
                            font-size="9"
                            fill="#6b7280">
                            {{ $nivel }}%
                        </text>

                        @endfor


                        {{-- =============================================
                            EJE INFERIOR
                        ============================================== --}}

                        <line
                            x1="{{ $margenIzquierdo }}"
                            y1="{{ $margenSuperior + $altoUtil }}"
                            x2="{{ $margenIzquierdo + $anchoUtil }}"
                            y2="{{ $margenSuperior + $altoUtil }}"
                            stroke="#9ca3af"
                            stroke-width="1" />


                        {{-- =============================================
                            SERIES
                        ============================================== --}}

                        @php

                        $puntosOtd = [];
                        $puntosFuera = [];
                        $puntosPendientes = [];

                        $cantidadSemanas = $tendenciaOtd->count();

                        @endphp


                        @foreach($tendenciaOtd as $indice => $dato)

                        @php

                        if ($cantidadSemanas > 1) {

                        $x =
                        $margenIzquierdo
                        +
                        (
                        $indice
                        *
                        (
                        $anchoUtil
                        /
                        ($cantidadSemanas - 1)
                        )
                        );

                        } else {

                        $x =
                        $margenIzquierdo
                        +
                        ($anchoUtil / 2);

                        }


                        $yOtd =
                        $margenSuperior
                        +
                        $altoUtil
                        -
                        (
                        $dato['otd']
                        *
                        $altoUtil
                        );


                        $yFuera =
                        $margenSuperior
                        +
                        $altoUtil
                        -
                        (
                        $dato['fuera_tiempo']
                        *
                        $altoUtil
                        );


                        $yPendientes =
                        $margenSuperior
                        +
                        $altoUtil
                        -
                        (
                        $dato['pendientes']
                        *
                        $altoUtil
                        );


                        $puntosOtd[] =
                        $x . ',' . $yOtd;

                        $puntosFuera[] =
                        $x . ',' . $yFuera;

                        $puntosPendientes[] =
                        $x . ',' . $yPendientes;

                        @endphp


                        {{-- =========================================
                            ETIQUETA DE SEMANA
                        ========================================== --}}

                        <text
                            x="{{ $x }}"
                            y="{{ $margenSuperior + $altoUtil + 18 }}"
                            text-anchor="middle"
                            font-size="9"
                            fill="#374151">
                            S{{ $dato['semana'] }}
                        </text>


                        {{-- =========================================
                            PORCENTAJE OTD
                        ========================================== --}}

                        <text
                            x="{{ $x }}"
                            y="{{ $yOtd - 8 }}"
                            text-anchor="middle"
                            font-size="9"
                            font-weight="600"
                            fill="#65ad3d">
                            {{ number_format($dato['otd'] * 100, 0) }}%
                        </text>


                        {{-- =========================================
                                PORCENTAJE FUERA DE TIEMPO
                            ========================================== --}}

                        @if($dato['fuera_tiempo'] > 0)

                        <text
                            x="{{ $x }}"
                            y="{{ $yFuera + 16 }}"
                            text-anchor="middle"
                            font-size="9"
                            font-weight="600"
                            fill="#f47b20">
                            {{ number_format($dato['fuera_tiempo'] * 100, 0) }}%
                        </text>

                        @endif


                        {{-- =========================================
                            PORCENTAJE PENDIENTES
                        ========================================== --}}

                        @if($dato['pendientes'] > 0)

                        <text
                            x="{{ $x }}"
                            y="{{ $yPendientes + 16 }}"
                            text-anchor="middle"
                            font-size="9"
                            font-weight="600"
                            fill="#6b7280">
                            {{ number_format($dato['pendientes'] * 100, 0) }}%
                        </text>

                        @endif


                        {{-- =========================================
                            PUNTO OTD
                        ========================================== --}}

                        <circle
                            cx="{{ $x }}"
                            cy="{{ $yOtd }}"
                            r="3"
                            fill="#65ad3d" />


                        {{-- PUNTO FUERA DE TIEMPO --}}

                        <circle
                            cx="{{ $x }}"
                            cy="{{ $yFuera }}"
                            r="3"
                            fill="#f47b20" />


                        {{-- PUNTO PENDIENTES --}}

                        <circle
                            cx="{{ $x }}"
                            cy="{{ $yPendientes }}"
                            r="3"
                            fill="#a6a6a6" />

                        @endforeach


                        {{-- =============================================
                            LÍNEA OTD
                        ============================================== --}}

                        @if(count($puntosOtd) > 1)

                        <polyline
                            points="{{ implode(' ', $puntosOtd) }}"
                            fill="none"
                            stroke="#65ad3d"
                            stroke-width="2.5" />

                        @endif


                        {{-- =============================================
                            LÍNEA FUERA DE TIEMPO
                        ============================================== --}}

                        @if(count($puntosFuera) > 1)

                        <polyline
                            points="{{ implode(' ', $puntosFuera) }}"
                            fill="none"
                            stroke="#f47b20"
                            stroke-width="2.5" />

                        @endif


                        {{-- =============================================
                            LÍNEA PENDIENTES
                        ============================================== --}}

                        @if(count($puntosPendientes) > 1)

                        <polyline
                            points="{{ implode(' ', $puntosPendientes) }}"
                            fill="none"
                            stroke="#a6a6a6"
                            stroke-width="2.5" />

                        @endif


                        {{-- =============================================
                            PERIODOS
                        ============================================== --}}

                        @foreach($tendenciaOtd as $indice => $dato)

                        @php

                        if ($cantidadSemanas > 1) {

                        $x =
                        $margenIzquierdo
                        +
                        (
                        $indice
                        *
                        (
                        $anchoUtil
                        /
                        ($cantidadSemanas - 1)
                        )
                        );

                        } else {

                        $x =
                        $margenIzquierdo
                        +
                        ($anchoUtil / 2);

                        }

                        @endphp

                        <text
                            x="{{ $x }}"
                            y="{{ $margenSuperior + $altoUtil + 33 }}"
                            text-anchor="middle"
                            font-size="8"
                            fill="#6b7280">
                            {{ $dato['periodo'] }}
                        </text>

                        @endforeach

                    </svg>

                </div>


                {{-- =================================================
                    LEYENDA
                ================================================== --}}

                <div
                    style="
                        display:flex;
                        justify-content:center;
                        align-items:center;
                        gap:14px;
                        margin-top:2px;
                        font-size:10px;
                    ">

                    <div style="display:flex;align-items:center;gap:4px;">

                        <span
                            style="
                                width:7px;
                                height:7px;
                                background:#65ad3d;
                                display:inline-block;
                            "></span>

                        <span>A Tiempo</span>

                    </div>


                    <div style="display:flex;align-items:center;gap:4px;">

                        <span
                            style="
                            width:7px;
                            height:7px;
                            background:#f47b20;
                            display:inline-block;
                        "></span>

                        <span>Fuera de Tiempo</span>

                    </div>


                    <div style="display:flex;align-items:center;gap:4px;">

                        <span
                            style="
                            width:7px;
                            height:7px;
                            background:#a6a6a6;
                            display:inline-block;
                        "></span>

                        <span>Pendientes</span>

                    </div>

                </div>

            </div>


        </div>
        {{-- FIN DEL GRID DE LAS 3 GRÁFICAS --}}

        {{-- =====================================================
     TITULO CARGA DE TRABAJO
====================================================== --}}

        <div class="mt-8 mb-4 text-center">

            <h2 class="text-xl font-bold text-white flex items-center justify-center gap-2">

                <span>📋</span>

                <span>CARGA DE TRABAJO</span>

            </h2>

            <p class="mt-2 text-sm text-white">
                Pedidos de la semana {{ $semana }} + pedidos atrasados
            </p>

        </div>
        {{-- =====================================================
     CONTENEDOR DE TABLA
====================================================== --}}

        <div class="overflow-x-hidden
            max-h-[550px]
            overflow-y-auto
            border
            border-gray-300
            shadow-md
            tabla-carga-trabajo">

            <table class="w-full
                  text-sm
                  border-collapse">

                {{-- =================================================
                    ENCABEZADO
                ================================================== --}}

                <thead class="sticky top-0 z-10">

                    <tr class="text-white">

                        {{-- =================================================
                            PEDIDO
                        ================================================== --}}

                        <th
                            style="background-color: #1e3a5f !important;"
                            class="px-4 py-3
                   text-center
                   border border-gray-300
                   whitespace-nowrap
                   font-bold">

                            <button
                                type="button"
                                class="ordenar-columna inline-flex items-center justify-center gap-2 w-full"
                                data-sort="pedido">

                                <span>PEDIDO</span>

                                <span class="flechas-orden text-xs leading-none">
                                    <span class="flecha-arriba opacity-50">▲</span>
                                    <span class="flecha-abajo opacity-50">▼</span>
                                </span>

                            </button>

                        </th>


                        {{-- =================================================
                            CLIENTE
                        ================================================== --}}

                        <th
                            style="background-color: #1e3a5f !important;"
                            class="px-4 py-3
                   text-left
                   border border-gray-300
                   min-w-[260px]
                   font-bold">

                            <button
                                type="button"
                                class="ordenar-columna inline-flex items-center justify-center gap-2 w-full"
                                data-sort="cliente">

                                <span>CLIENTE</span>

                                <span class="flechas-orden text-xs leading-none">
                                    <span class="flecha-arriba opacity-50">▲</span>
                                    <span class="flecha-abajo opacity-50">▼</span>
                                </span>

                            </button>

                        </th>


                        {{-- =================================================
                            PRIORIDAD
                        ================================================== --}}

                        <th
                            style="background-color: #1e3a5f !important;"
                            class="px-4 py-3
                   text-center
                   border border-gray-300
                   whitespace-nowrap
                   font-bold">

                            <button
                                type="button"
                                class="ordenar-columna inline-flex items-center justify-center gap-2 w-full"
                                data-sort="prioridad">

                                <span>PRIORIDAD</span>

                                <span class="flechas-orden text-xs leading-none">
                                    <span class="flecha-arriba opacity-50">▲</span>
                                    <span class="flecha-abajo opacity-50">▼</span>
                                </span>

                            </button>

                        </th>


                        {{-- =================================================
                            AVANCE
                        ================================================== --}}

                        <th
                            style="background-color: #1e3a5f !important;"
                            class="px-4 py-3
                   text-center
                   border border-gray-300
                   min-w-[190px]
                   font-bold">

                            <button
                                type="button"
                                class="ordenar-columna inline-flex items-center justify-center gap-2 w-full"
                                data-sort="avance">

                                <span>AVANCE</span>

                                <span class="flechas-orden text-xs leading-none">
                                    <span class="flecha-arriba opacity-50">▲</span>
                                    <span class="flecha-abajo opacity-50">▼</span>
                                </span>

                            </button>

                        </th>


                        {{-- =================================================
                            ENTREGA PRODUCCIÓN
                        ================================================== --}}

                        <th
                            style="background-color: #1e3a5f !important;"
                            class="px-4 py-3
                   text-center
                   border border-gray-300
                   whitespace-nowrap
                   font-bold">

                            <button
                                type="button"
                                class="ordenar-columna inline-flex items-center justify-center gap-2 w-full"
                                data-sort="fecha-produccion">

                                <span>ENTREGA PRODUCCIÓN</span>

                                <span class="flechas-orden text-xs leading-none">
                                    <span class="flecha-arriba opacity-50">▲</span>
                                    <span class="flecha-abajo opacity-50">▼</span>
                                </span>

                            </button>

                        </th>

                        {{-- FECHA PACTADA CLIENTE --}}

                        <th
                            style="background-color: #1e3a5f !important;"
                            class="px-4 py-3
                            text-center
                            border border-gray-300
                            whitespace-nowrap
                            font-bold">

                            <button
                                type="button"
                                class="ordenar-columna inline-flex items-center justify-center gap-2 w-full"
                                data-sort="fecha-entrega">

                                <span>FECHA PACTADA CLIENTE</span>

                                <span class="flechas-orden text-xs leading-none">
                                    <span class="flecha-arriba opacity-50">▲</span>
                                    <span class="flecha-abajo opacity-50">▼</span>
                                </span>

                            </button>

                        </th>

                        {{-- DÍAS --}}

                        <th
                            style="background-color: #1e3a5f !important;"
                            class="px-4 py-3
                                text-center
                                border border-gray-300
                                whitespace-nowrap
                                font-bold">

                            <button
                                type="button"
                                class="ordenar-columna inline-flex items-center justify-center gap-2 w-full"
                                data-sort="dias">

                                <span>DÍAS</span>

                                <span class="flechas-orden text-xs leading-none">
                                    <span class="flecha-arriba opacity-50">▲</span>
                                    <span class="flecha-abajo opacity-50">▼</span>
                                </span>

                            </button>

                        </th>


                        {{-- =================================================
                            ESTADO
                        ================================================== --}}

                        <th
                            style="background-color: #1e3a5f !important;"
                            class="px-4 py-3
                   text-center
                   border border-gray-300
                   min-w-[170px]
                   font-bold">

                            <button
                                type="button"
                                class="ordenar-columna inline-flex items-center justify-center gap-2 w-full"
                                data-sort="estado">

                                <span>ESTADO</span>

                                <span class="flechas-orden text-xs leading-none">
                                    <span class="flecha-arriba opacity-50">▲</span>
                                    <span class="flecha-abajo opacity-50">▼</span>
                                </span>

                            </button>

                        </th>

                    </tr>

                </thead>


                {{-- =================================================
                    CUERPO
                ================================================== --}}

                <tbody>

                    @forelse($pedidosCriticos as $pedido)

                    @php

                    $prioridad =
                    optional($pedido->controlOperativo)
                    ->prioridad;

                    $fechaProduccion =
                    optional($pedido->controlOperativo)
                    ->fecha_produccion;

                    $avance =
                    $pedido->avance ?? 0;

                    $estado =
                    $pedido->semaforo_dashboard
                    ?? 'SIN ESTADO';

                    @endphp


                    {{-- =================================================
                        FILA
                    ================================================== --}}

                    <tr
                        class="
                        grupo-fila
                        text-white
                        transition-all
                        duration-150
                        hover:bg-gray-100
                        hover:text-black
                    "
                        data-pedido="{{ $pedido->pedido_no }}"

                        data-cliente="{{ $pedido->cliente ?? '' }}"

                        data-prioridad="{{ $prioridad ?? '' }}"

                        data-avance="{{ $avance }}"

                        data-fecha-produccion="{{ $fechaProduccion ?? '' }}"

                        data-fecha-entrega="{{ $pedido->fecha_entrega ?? '' }}"

                        data-dias="{{ $pedido->dias_restantes_dashboard ?? 0 }}"

                        data-estado="{{ $estado }}">

                        {{-- =================================================
                            PEDIDO
                        ================================================== --}}

                        <td class="px-4 py-3
                               text-center
                               border border-gray-300
                               whitespace-nowrap
                               font-semibold">

                            {{ $pedido->pedido_no }}

                        </td>


                        {{-- =================================================
                            CLIENTE
                        ================================================== --}}

                        <td class="px-4 py-3
                               border border-gray-300">

                            {{ $pedido->cliente ?? 'SIN CLIENTE' }}

                        </td>


                        {{-- =================================================
                            PRIORIDAD
                        ================================================== --}}

                        <td class="px-4 py-3
                               text-center
                               border border-gray-300">

                            @if($prioridad == 1)

                            <span
                                class="inline-flex
                                    items-center
                                    justify-center
                                    w-9 h-9
                                    rounded-full
                                    text-white
                                    font-bold
                                    shadow"
                                style="background-color: #ef4444;">

                                1

                            </span>

                            @elseif($prioridad == 2)

                            <span
                                class="inline-flex
                                    items-center
                                    justify-center
                                    w-9 h-9
                                    rounded-full
                                    text-black
                                    font-bold
                                    shadow"
                                style="background-color: #facc15;">

                                2

                            </span>

                            @elseif($prioridad == 3)

                            <span class="inline-flex
                                         items-center
                                         justify-center
                                         w-9 h-9
                                         rounded-full
                                         bg-green-500
                                         text-white
                                         font-bold
                                         shadow">

                                3

                            </span>

                            @elseif($prioridad == 4)

                            <span
                                class="inline-flex
                                items-center
                                justify-center
                                w-9 h-9
                                rounded-full
                                text-white
                                font-bold
                                shadow"
                                style="background-color: #06b6d4;">

                                4

                            </span>

                            @else

                            <span class="text-gray-300">
                                —
                            </span>

                            @endif

                        </td>


                        {{-- =================================================
                            AVANCE
                        ================================================== --}}

                        <td class="px-4 py-3 border border-gray-300">

                            @php

                            $avance =
                            $pedido->avance ?? 0;

                            $avancePorcentaje =
                            round($avance * 100);

                            if ($avancePorcentaje >= 100) {

                            $colorAvance = '#22c55e';

                            } elseif ($avancePorcentaje > 0) {

                            $colorAvance = '#3b82f6';

                            } else {

                            $colorAvance = '#d1d5db';

                            }

                            @endphp


                            <div class="flex items-center gap-3">

                                {{-- BARRA --}}

                                <div
                                    style="
                                    width:80px;
                                    height:10px;
                                    background:#d1d5db;
                                    border-radius:999px;
                                    overflow:hidden;
                                    flex-shrink:0;
                                ">

                                    <div
                                        style="
                                        width:{{ min(100, max(0, $avancePorcentaje)) }}%;
                                        height:100%;
                                        background:{{ $colorAvance }};
                                        border-radius:999px;
                                        transition:width .3s ease;
                                    ">
                                    </div>

                                </div>


                                {{-- PORCENTAJE --}}

                                <span
                                    style="
                                    font-size:12px;
                                    font-weight:700;
                                    color:#ffffff;
                                    min-width:42px;
                                    text-align:right;
                                ">

                                    {{ $avancePorcentaje }}%

                                </span>

                            </div>

                        </td>


                        {{-- =================================================
                            ENTREGA PRODUCCIÓN
                        ================================================== --}}

                        <td class="px-4 py-3
                               text-center
                               border border-gray-300
                               whitespace-nowrap">

                            @if($fechaProduccion)

                            {{ \Carbon\Carbon::parse(
                                $fechaProduccion
                            )->format('d/m/Y') }}

                            @else

                            <span class="text-gray-300">
                                —
                            </span>

                            @endif

                        </td>

                        {{-- =================================================
                            FECHA PACTADA CLIENTE
                        ================================================== --}}

                        <td class="px-4 py-3
                            text-center
                            border border-gray-300
                            whitespace-nowrap">

                            @if($pedido->fecha_entrega)

                            {{ \Carbon\Carbon::parse(
                                    $pedido->fecha_entrega
                                )->format('d/m/Y') }}

                            @else

                            <span class="text-gray-300">
                                —
                            </span>

                            @endif

                        </td>

                        {{-- DÍAS --}}

                        @php

                        $dias = $pedido->dias_restantes_dashboard ?? 0;

                        if ($estado === 'TERMINADO') {

                        $fechaTerminado = $pedido->fecha_terminado;

                        if ($fechaTerminado) {
                        $textoDias = 'Entregado<br>' .
                        \Carbon\Carbon::parse($fechaTerminado)->format('d/m/Y');
                        } else {
                        $textoDias = 'Entregado';
                        }

                        } elseif (
                        $estado === 'CANCELADO' ||
                        $estado === 'SIN FECHA'
                        ) {

                        $textoDias = '—';

                        } elseif ($dias < 0) {

                            $diasAtraso=abs($dias);

                            $textoDias=$diasAtraso . ' ' .
                            ($diasAtraso==1 ? 'día' : 'días' ) . ' de atraso' ;

                            } elseif ($dias==0) {

                            $textoDias='Entrega hoy' ;

                            } else {

                            $textoDias=$dias . ' ' .
                            ($dias==1 ? 'día' : 'días' ) . ' restantes' ;

                            }

                            @endphp

                            <td class="px-4 py-3
       text-center
       border border-gray-300
       whitespace-nowrap">

                            {!! $textoDias !!}

                            </td>


                            {{-- =================================================
                            ESTADO
                        ================================================== --}}

                            <td class="px-4 py-3
                               text-center
                               border border-gray-300
                               whitespace-nowrap">

                                @if($estado === 'ATRASADO')

                                <span style="
                                        display: inline-flex;
                                        align-items: center;
                                        gap: 8px;
                                        padding: 6px 12px;
                                        border-radius: 9999px;
                                        background-color: #fee2e2;
                                        color: #b91c1c;
                                        font-weight: 700;
                                    ">

                                    <span style="
                                                width: 12px;
                                                height: 12px;
                                                border-radius: 50%;
                                                background-color: #ef4444;
                                                display: inline-block;
                                            ">
                                    </span>

                                    ATRASADO

                                </span>


                                @elseif($estado === 'EN RIESGO')

                                <span class="
                                inline-flex
                                items-center
                                gap-2
                                px-3
                                py-1.5
                                rounded-full
                                bg-yellow-100
                                text-yellow-700
                                font-bold
                            ">

                                    <span class="w-3 h-3
                                             rounded-full
                                             bg-yellow-400">
                                    </span>

                                    EN RIESGO

                                </span>


                                @elseif($estado === 'EN TIEMPO')

                                <span class="
                                inline-flex
                                items-center
                                gap-2
                                px-3
                                py-1.5
                                rounded-full
                                bg-green-100
                                text-green-700
                                font-bold
                            ">

                                    <span class="w-3 h-3
                                             rounded-full
                                             bg-green-500">
                                    </span>

                                    EN TIEMPO

                                </span>


                                @elseif($estado === 'TERMINADO')

                                <span class="
                                inline-flex
                                items-center
                                gap-2
                                px-3
                                py-1.5
                                rounded-full
                                text-blue-700
                                font-bold
                            ">

                                    ✓ TERMINADO

                                </span>


                                @else

                                <span class="
                                inline-flex
                                px-3
                                py-1.5
                                rounded-full
                                bg-gray-100
                                text-gray-600
                                font-bold
                            ">

                                    {{ $estado }}

                                </span>

                                @endif

                            </td>

                    </tr>


                    @empty

                    <tr>

                        <td
                            colspan="7"
                            class="
                            px-4
                            py-10
                            text-center
                            text-gray-300
                            border
                            border-gray-300
                        ">

                            No hay pedidos en la carga de trabajo
                            de esta semana.

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- =====================================================
            TOTAL
            ====================================================== --}}

        <div class="
        bg-gray-100
        border-x
        border-b
        border-gray-300
        rounded-b-lg
        px-5
        py-3
        text-right
        font-semibold
        shadow
    ">

            Total de pedidos:

            <span class="text-blue-600 text-lg">

                {{ $pedidosCriticos->count() }}

            </span>

        </div>

    </div>


    {{-- =========================================================
         ESTILOS DE LA TABLA
        ========================================================== --}}

    <style>
        /*
    |--------------------------------------------------------------------------
    | Texto normal de las filas
    |--------------------------------------------------------------------------
    */

        .tabla-carga-trabajo tbody tr {
            color: white !important;
        }


        /*
    |--------------------------------------------------------------------------
    | Hover
    |--------------------------------------------------------------------------
    */

        .tabla-carga-trabajo tbody tr:hover {
            color: black !important;
            background-color: #f3f4f6 !important;
        }


        .tabla-carga-trabajo tbody tr:hover td {
            color: black !important;
        }


        /*
    |--------------------------------------------------------------------------
    | Mantener los estados con sus colores
    |--------------------------------------------------------------------------
    */

        .tabla-carga-trabajo tbody tr:hover .bg-red-100 {
            color: #b91c1c !important;
        }

        .tabla-carga-trabajo tbody tr:hover .bg-yellow-100 {
            color: #a16207 !important;
        }

        .tabla-carga-trabajo tbody tr:hover .bg-green-100 {
            color: #15803d !important;
        }

        .tabla-carga-trabajo tbody tr:hover .bg-blue-100 {
            color: #1d4ed8 !important;
        }


        /*
    |--------------------------------------------------------------------------
    | Scroll
    |--------------------------------------------------------------------------
    */

        .tabla-carga-trabajo {
            scrollbar-width: thin;
        }
    </style>


    {{-- =========================================================
                 CUMPLIMIENTO
            ========================================================== --}}

    <div class="mt-5 bg-[#f3f6fa]
                        text-center
                        font-bold
                        py-2">

        CUMPLIMIENTO DE LA CARGA OPERATIVA

    </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const tabla = document.querySelector('.tabla-carga-trabajo table');

            if (!tabla) {
                return;
            }

            const tbody = tabla.querySelector('tbody');

            const botonesOrdenar =
                tabla.querySelectorAll('.ordenar-columna');

            let columnaActual = null;
            let direccionActual = 'asc';


            // ============================================================
            // ORDENAR TABLA
            // ============================================================

            function ordenarTabla(columna, direccion) {

                const filas = Array.from(
                    tbody.querySelectorAll('tr.grupo-fila')
                );

                filas.sort(function(filaA, filaB) {

                    /*
                     * IMPORTANTE:
                     * data-fecha-produccion y data-fecha-entrega
                     * no se leen con dataset["fecha-produccion"],
                     * porque dataset convierte esos nombres a camelCase.
                     * Aquí se lee directamente el atributo HTML para
                     * que ambas fechas se ordenen correctamente.
                     */
                    let valorA = filaA.getAttribute('data-' + columna) ?? '';
                    let valorB = filaB.getAttribute('data-' + columna) ?? '';

                    let resultado = 0;


                    // ----------------------------------------------------
                    // COLUMNAS NUMÉRICAS
                    // ----------------------------------------------------

                    if (
                        columna === 'prioridad' ||
                        columna === 'avance' ||
                        columna === 'dias'
                    ) {

                        let numeroA = parseFloat(
                            String(valorA).replace(',', '.')
                        );

                        let numeroB = parseFloat(
                            String(valorB).replace(',', '.')
                        );

                        /*
                         * Los valores vacíos se colocan al final,
                         * tanto en ascendente como descendente.
                         */
                        const vacioA = !Number.isFinite(numeroA);
                        const vacioB = !Number.isFinite(numeroB);

                        if (vacioA && vacioB) {
                            resultado = 0;
                        } else if (vacioA) {
                            resultado = 1;
                        } else if (vacioB) {
                            resultado = -1;
                        } else {
                            resultado = numeroA - numeroB;
                        }

                    }


                    // ----------------------------------------------------
                    // FECHAS
                    // ----------------------------------------------------

                    else if (
                        columna === 'fecha-produccion' ||
                        columna === 'fecha-entrega'
                    ) {

                        /*
                         * Se utiliza directamente YYYY-MM-DD cuando existe.
                         * Así no dependemos de cómo interprete el navegador
                         * la fecha con zona horaria.
                         */
                        const fechaA = String(valorA).trim().slice(0, 10);
                        const fechaB = String(valorB).trim().slice(0, 10);

                        const vacioA = !/^\d{4}-\d{2}-\d{2}$/.test(fechaA);
                        const vacioB = !/^\d{4}-\d{2}-\d{2}$/.test(fechaB);

                        if (vacioA && vacioB) {
                            resultado = 0;
                        } else if (vacioA) {
                            resultado = 1;
                        } else if (vacioB) {
                            resultado = -1;
                        } else {
                            // YYYY-MM-DD se puede comparar de forma segura.
                            resultado = fechaA.localeCompare(fechaB);
                        }

                    }


                    // ----------------------------------------------------
                    // TEXTO / ESTADO
                    // ----------------------------------------------------

                    else {

                        valorA = String(valorA)
                            .toLowerCase()
                            .trim();

                        valorB = String(valorB)
                            .toLowerCase()
                            .trim();

                        resultado = valorA.localeCompare(
                            valorB,
                            'es',
                            {
                                numeric: true,
                                sensitivity: 'base'
                            }
                        );

                    }


                    return direccion === 'asc'
                        ? resultado
                        : -resultado;

                });


                // --------------------------------------------------------
                // VOLVER A INSERTAR FILAS
                // --------------------------------------------------------

                filas.forEach(function(fila) {

                    tbody.appendChild(fila);

                });


                actualizarFlechas(columna, direccion);

            }


            // ACTUALIZAR FLECHAS
            // ============================================================

            function actualizarFlechas(columna, direccion) {

                botonesOrdenar.forEach(function(boton) {

                    const flechaArriba =
                        boton.querySelector('.flecha-arriba');

                    const flechaAbajo =
                        boton.querySelector('.flecha-abajo');


                    // Restaurar apariencia

                    flechaArriba.classList.add('opacity-50');

                    flechaAbajo.classList.add('opacity-50');

                    flechaArriba.classList.remove('font-bold');

                    flechaAbajo.classList.remove('font-bold');


                    // Columna seleccionada

                    if (boton.dataset.sort === columna) {

                        if (direccion === 'asc') {

                            flechaArriba.classList.remove(
                                'opacity-50'
                            );

                            flechaArriba.classList.add(
                                'font-bold'
                            );

                        } else {

                            flechaAbajo.classList.remove(
                                'opacity-50'
                            );

                            flechaAbajo.classList.add(
                                'font-bold'
                            );

                        }

                    }

                });

            }


            // ============================================================
            // CLICK EN ENCABEZADO
            // ============================================================

            botonesOrdenar.forEach(function(boton) {

                boton.addEventListener('click', function() {

                    const columna =
                        this.dataset.sort;


                    // Si es una columna diferente,
                    // empezar ascendente.

                    if (columnaActual !== columna) {

                        columnaActual = columna;

                        direccionActual = 'asc';

                    }

                    // Si es la misma columna,
                    // cambiar dirección.
                    else {

                        direccionActual =
                            direccionActual === 'asc' ?
                            'desc' :
                            'asc';

                    }


                    ordenarTabla(
                        columnaActual,
                        direccionActual
                    );

                });

            });

        });
    </script>


<style>
/* ============================================================
   REFINAMIENTO VISUAL — DASHBOARD DE PRODUCCIÓN
   Solo presentación. No modifica lógica, consultas ni JavaScript.
   ============================================================ */

.dashboard-produccion-refinado {
    --dp-bg: #0f172a;
    --dp-panel: #1f2937;
    --dp-panel-2: #111827;
    --dp-border: rgba(148, 163, 184, .20);
    --dp-text: #f8fafc;
    --dp-muted: #94a3b8;
}

/* Área general */
.dashboard-produccion-refinado > div {
    transition: all .2s ease;
}

/* Título principal */
.dashboard-produccion-refinado > div > .max-w-\[1500px\] > div:first-child {
    background: linear-gradient(135deg, #1e293b, #253247) !important;
    border: 1px solid rgba(148,163,184,.16);
    box-shadow: 0 10px 28px rgba(0,0,0,.18);
    letter-spacing: .02em;
    min-height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Encabezados de sección */
.dashboard-produccion-refinado .bg-\[\#1f2937\] {
    border-color: rgba(148,163,184,.16);
}

/* Resumen semanal */
.dashboard-produccion-refinado .rounded-t-xl {
    box-shadow: 0 8px 24px rgba(0,0,0,.14);
}

.dashboard-produccion-refinado .rounded-t-xl + .grid {
    box-shadow: 0 10px 28px rgba(0,0,0,.16);
}

/* Tarjetas KPI */
.dashboard-produccion-refinado .hover\:scale-\[1\.02\] {
    box-shadow:
        0 4px 12px rgba(0,0,0,.12),
        inset 0 1px 0 rgba(255,255,255,.04);
    transform-origin: center;
}

.dashboard-produccion-refinado .hover\:scale-\[1\.02\]:hover {
    box-shadow:
        0 10px 22px rgba(0,0,0,.20),
        inset 0 1px 0 rgba(255,255,255,.06);
}

/* Encabezados naranja / azul / morado */
.dashboard-produccion-refinado [class*="bg-[#ed7d31]"],
.dashboard-produccion-refinado [class*="bg-[#00b0f0]"],
.dashboard-produccion-refinado [class*="bg-purple-600"] {
    letter-spacing: .025em;
    box-shadow: inset 0 -1px 0 rgba(0,0,0,.12);
}

/* Panel de pedidos internos */
.dashboard-produccion-refinado .border-purple-400\/40 {
    box-shadow: 0 8px 22px rgba(0,0,0,.14);
}

/* Controles de modo / semana */
.dashboard-produccion-refinado form[action*="dashboard.produccion"] {
    background: rgba(31,41,55,.72);
    border: 1px solid rgba(148,163,184,.16);
    border-radius: 14px;
    padding: 10px 16px;
    box-shadow: 0 8px 22px rgba(0,0,0,.12);
}

.dashboard-produccion-refinado form[action*="dashboard.produccion"] select,
.dashboard-produccion-refinado form[action*="dashboard.produccion"] button {
    min-height: 40px;
    transition: all .18s ease;
}

.dashboard-produccion-refinado form[action*="dashboard.produccion"] select:hover {
    border-color: #60a5fa;
}

.dashboard-produccion-refinado form[action*="dashboard.produccion"] button {
    box-shadow: 0 5px 14px rgba(37,99,235,.25);
}

.dashboard-produccion-refinado form[action*="dashboard.produccion"] button:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 18px rgba(37,99,235,.32);
}

/* Avance semanal */
.dashboard-produccion-refinado .bg-gray-200.border-x.border-b {
    background: linear-gradient(180deg, #eef2f7 0%, #e5e7eb 100%) !important;
    box-shadow: 0 10px 24px rgba(0,0,0,.14);
}

.dashboard-produccion-refinado .bg-gray-200.border-x.border-b > div:first-child {
    box-shadow:
        inset 0 1px 1px rgba(255,255,255,.55),
        0 2px 5px rgba(0,0,0,.10);
}

/* Tarjetas de gráficas */
.dashboard-produccion-refinado div[style*="background: #ffffff"][style*="border-radius: 8px"] {
    border: 1px solid #e2e8f0;
    box-shadow:
        0 10px 24px rgba(0,0,0,.16),
        0 1px 2px rgba(0,0,0,.06);
    transition: transform .18s ease, box-shadow .18s ease;
}

.dashboard-produccion-refinado div[style*="background: #ffffff"][style*="border-radius: 8px"]:hover {
    transform: translateY(-2px);
    box-shadow:
        0 14px 30px rgba(0,0,0,.20),
        0 2px 4px rgba(0,0,0,.07);
}

/* Dona */
.dashboard-produccion-refinado div[style*="conic-gradient"] {
    box-shadow: 0 6px 16px rgba(15,23,42,.10);
}

/* Carga de trabajo */
.dashboard-produccion-refinado .tabla-carga-trabajo {
    border-radius: 12px 12px 0 0;
    overflow: auto;
    box-shadow:
        0 12px 28px rgba(0,0,0,.18),
        0 1px 2px rgba(0,0,0,.08);
    scrollbar-width: thin;
    scrollbar-color: #64748b #e5e7eb;
}

.dashboard-produccion-refinado .tabla-carga-trabajo table {
    min-width: 100%;
}

.dashboard-produccion-refinado .tabla-carga-trabajo thead th {
    box-shadow: inset 0 -2px 0 rgba(255,255,255,.14);
}

.dashboard-produccion-refinado .tabla-carga-trabajo tbody tr {
    transition: background-color .15s ease, transform .15s ease;
}

.dashboard-produccion-refinado .tabla-carga-trabajo tbody tr:nth-child(even) {
    background-color: rgba(255,255,255,.025);
}

.dashboard-produccion-refinado .tabla-carga-trabajo tbody tr:hover {
    background-color: #f1f5f9 !important;
    box-shadow: inset 3px 0 0 #3b82f6;
}

/* Total de pedidos */
.dashboard-produccion-refinado .tabla-carga-trabajo + div {
    border-radius: 0 0 12px 12px;
    box-shadow: 0 8px 18px rgba(0,0,0,.12);
}

/* Título Carga de Trabajo */
.dashboard-produccion-refinado .mt-8.mb-4 {
    padding: 2px 0;
}

.dashboard-produccion-refinado .mt-8.mb-4 h2 {
    letter-spacing: .02em;
}

/* Separador final */
.dashboard-produccion-refinado .mt-5.bg-\[\#f3f6fa\] {
    border-radius: 10px;
    margin-top: 28px !important;
    box-shadow: 0 4px 14px rgba(0,0,0,.10);
    color: #1f2937;
}

/* Responsive */
@media (max-width: 1100px) {
    .dashboard-produccion-refinado > div > .max-w-\[1500px\] {
        padding-left: 14px;
        padding-right: 14px;
    }

    .dashboard-produccion-refinado form[action*="dashboard.produccion"] {
        gap: 10px;
    }
}

@media (max-width: 900px) {
    .dashboard-produccion-refinado form[action*="dashboard.produccion"] {
        justify-content: stretch;
    }

    .dashboard-produccion-refinado form[action*="dashboard.produccion"] > div {
        flex: 1 1 220px;
    }
}

@media (max-width: 640px) {
    .dashboard-produccion-refinado > div > .max-w-\[1500px\] {
        padding-left: 8px;
        padding-right: 8px;
    }

    .dashboard-produccion-refinado form[action*="dashboard.produccion"] {
        padding: 10px;
    }

    .dashboard-produccion-refinado form[action*="dashboard.produccion"] > div {
        width: 100%;
        flex-basis: 100%;
    }

    .dashboard-produccion-refinado form[action*="dashboard.produccion"] select,
    .dashboard-produccion-refinado form[action*="dashboard.produccion"] button {
        width: 100%;
    }
}
</style>

</x-app-layout>
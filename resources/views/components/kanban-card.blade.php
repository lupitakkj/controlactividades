@props([
'actividad',
'estado'
])

@php
$actividadJson = htmlspecialchars(
json_encode($actividad),
ENT_QUOTES,
'UTF-8'
);
@endphp

<div
    data-id="{{ $actividad->id }}"
    onclick='abrirModal({!! $actividadJson !!})'
    class="kanban-card draggable-card bg-slate-900 hover:bg-slate-800 border border-slate-700 hover:border-blue-500 rounded-2xl p-6 shadow-lg transition-all duration-300 cursor-pointer">
    {{-- ========================= --}}
    {{-- TITULO --}}
    {{-- ========================= --}}

    <h3 class="text-xl font-bold text-white leading-tight truncate">

        {{ $actividad->titulo }}

    </h3>
    <div class="border-b border-slate-700 my-4"></div>
    {{-- ========================= --}}
    {{-- USUARIO --}}
    {{-- ========================= --}}

    <div class="flex items-center gap-3">

        <div
            class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">

            {{ strtoupper(substr($actividad->user->name ?? 'S',0,1)) }}

        </div>

        <div>

            <p class="text-xs text-slate-500">

                Responsable

            </p>

            <p class="text-sm text-white truncate">

                {{ $actividad->user->name ?? 'Sin asignar' }}

            </p>

        </div>

    </div>

    {{-- ========================= --}}
    {{-- DESCRIPCION --}}
    {{-- ========================= --}}

    <p class="mt-4 text-slate-300 text-sm line-clamp-2">

        {{ $actividad->descripcion }}

    </p>

    {{-- ========================= --}}
    {{-- BADGE --}}
    {{-- ========================= --}}

    <div class="flex-1">

        @if($estado == 'pendiente')

        <span class="px-3 py-1 rounded-full bg-blue-600/20 text-blue-300 border border-blue-500/30 text-xs">

            {{ strtoupper($actividad->prioridad) }}

        </span>

        @elseif($estado == 'en_proceso')

        <span class="px-3 py-1 rounded-full bg-cyan-600/20 text-cyan-300 border border-cyan-500/30 text-xs">

            EN PROCESO

        </span>

        @elseif($estado == 'pausada')

        <span class="px-3 py-1 rounded-full bg-orange-600/20 text-orange-300 border border-orange-500/30 text-xs">

            PAUSADA

        </span>

        @else

        <span class="px-3 py-1 rounded-full bg-green-600/20 text-green-300 border border-green-500/30 text-xs">

            TERMINADA

        </span>

        @endif

    </div>

    {{-- ========================= --}}
    {{-- BOTONES --}}
    {{-- ========================= --}}

    @if($estado != 'terminada')

    <div class="mt-5 flex flex-wrap gap-2">

        @if($estado == 'pendiente')

        <form
            id="formIniciar{{ $actividad->id }}"
            method="POST"
            action="{{ route('actividad.iniciar',$actividad->id) }}">

            @csrf

            <button
                type="button"
                onclick="event.stopPropagation(); iniciarActividad({{ $actividad->id }})"
                class="bg-blue-600 hover:bg-blue-700 px-3 py-2 rounded text-white text-sm">

                ▶ Iniciar

            </button>

        </form>

        @elseif($estado == 'en_proceso')

        <form
            id="formPausar{{ $actividad->id }}"
            method="POST"
            action="{{ route('actividad.pausar',$actividad->id) }}">

            @csrf

            <button
                type="button"
                onclick="event.stopPropagation(); pausarActividad({{$actividad->id}})"
                class="bg-yellow-500 hover:bg-yellow-600 px-3 py-2 rounded text-white text-sm">

                ⏸ Pausar

            </button>

        </form>

        @elseif($estado == 'pausada')

        <form
            id="formReanudar{{ $actividad->id }}"
            method="POST"
            action="{{ route('actividad.iniciar',$actividad->id) }}">

            @csrf

            <button
                type="button"
                onclick="event.stopPropagation(); reanudarActividad({{ $actividad->id }})"
                class="bg-blue-600 hover:bg-blue-700 px-3 py-2 rounded text-white text-sm">

                ▶ Reanudar

            </button>

        </form>

        @endif

        <form
            id="formTerminar{{ $actividad->id }}"
            method="POST"
            action="{{ route('actividad.terminar',$actividad->id) }}">

            @csrf

            <button
                type="button"
                onclick="event.stopPropagation(); terminarActividad({{ $actividad->id }})"
                class="bg-green-600 hover:bg-green-700 px-3 py-2 rounded text-white text-sm">

                ✅ Terminar

            </button>

        </form>

    </div>

    @endif

    {{-- ========================= --}}
    {{-- TIEMPOS --}}
    {{-- ========================= --}}

    <div class="mt-5 text-sm text-slate-400">

        @if($estado == 'en_proceso')

        Tiempo acumulado

        <div class="font-bold text-white">

            <span
                class="cronometro"
                data-minutos="{{ $actividad->tiempo_activo }}">

                {{ gmdate('H:i:s',$actividad->tiempo_activo*60) }}

            </span>

        </div>

        @else

        Tiempo acumulado

        <div class="font-bold text-white">

            {{ gmdate('H:i:s',$actividad->tiempo_total*60) }}

        </div>

        @endif

    </div>

    {{-- ========================= --}}
    {{-- ESTIMADO --}}
    {{-- ========================= --}}

    @if($estado != 'terminada')

    <div class="mt-3 text-sm text-slate-400">

        Estimado

        <div class="font-bold text-white">

            {{ $actividad->tiempo_estimado }} hrs

        </div>

    </div>

    @endif

    {{-- ========================= --}}
    {{-- DIFERENCIA --}}
    {{-- ========================= --}}

    @if($estado != 'terminada')

    <div class="mt-3">

        @if($actividad->diferencia_tiempo > 0)

        <span class="text-red-400 font-bold">

            +{{ $actividad->diferencia_tiempo }} hrs

        </span>

        @else

        <span class="text-green-400 font-bold">

            {{ $actividad->diferencia_tiempo }} hrs

        </span>

        @endif

    </div>

    @endif

</div>
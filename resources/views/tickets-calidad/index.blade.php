<x-app-layout>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="py-6" x-data="ticketDetalle()">

        <div class="max-w-[1450px] mx-auto sm:px-6 lg:px-8">


            {{-- ========================================================= --}}
            {{-- RESUMEN --}}
            {{-- ========================================================= --}}

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">


                {{-- TOTAL DE TICKETS --}}
                <div class="flex-1 bg-white rounded-xl shadow-sm border border-gray-100 p-5">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-gray-500">
                                Total de tickets
                            </p>

                            <p class="text-3xl font-bold text-gray-800 mt-1">
                                {{ $tickets->count() }}
                            </p>

                        </div>


                        <div class="w-12 h-12 rounded-xl bg-blue-100
                                    flex items-center justify-center">

                            <i class="fa-solid fa-ticket text-blue-600 text-xl"></i>

                        </div>

                    </div>

                </div>


                {{-- TICKETS ABIERTOS --}}
                <div class="flex-1 bg-white rounded-xl shadow-sm border border-gray-100 p-5">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-gray-500">
                                Tickets abiertos
                            </p>

                            <p id="total-tickets-abiertos" class="text-3xl font-bold text-gray-800 mt-1">
                                {{ $tickets->where('estado', 'Abierto')->count() }}
                            </p>

                        </div>


                        <div class="w-12 h-12 rounded-xl bg-yellow-100
                                    flex items-center justify-center">

                            <i class="fa-solid fa-envelope-open-text text-yellow-600 text-xl"></i>

                        </div>

                    </div>

                </div>


                {{-- EN PROCESO --}}
                <div class="flex-1 bg-white rounded-xl shadow-sm border border-gray-100 p-5">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-gray-500">
                                En proceso
                            </p>

                            <p id="total-tickets-proceso" class="text-3xl font-bold text-gray-800 mt-1">
                                {{ $tickets->where('estado', 'En proceso')->count() }}
                            </p>

                        </div>


                        <div class="w-12 h-12 rounded-xl bg-orange-100
                                    flex items-center justify-center">

                            <i class="fa-solid fa-spinner text-orange-600 text-xl"></i>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- TABLA DE TICKETS --}}
            {{-- ========================================================= --}}

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Tickets registrados
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Tickets recibidos desde el sistema de calidad.
                            </p>
                        </div>

                        <div class="inline-flex items-center gap-2 px-3 py-2
                                    rounded-lg bg-gray-50 border border-gray-200
                                    text-sm text-gray-600">
                            <i class="fa-solid fa-list text-gray-400"></i>
                            <span>
                                {{ $tickets->count() }} tickets
                            </span>
                        </div>

                    </div>
                </div>

                @if($tickets->isEmpty())

                <div class="p-12 text-center">

                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl
                                    bg-gray-100 flex items-center justify-center">
                        <i class="fa-solid fa-ticket text-gray-400 text-2xl"></i>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-700">
                        No hay tickets registrados
                    </h3>

                    <p class="text-sm text-gray-500 mt-1 max-w-md mx-auto">
                        Cuando se registre una queja de calidad desde el otro sistema,
                        aparecerá aquí.
                    </p>

                </div>

                @else

                <div class="overflow-x-auto">

                    <table class="w-full min-w-[1050px]">

                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">

                                <th class="px-5 py-3.5 text-left text-xs font-semibold
                                               text-gray-500 uppercase tracking-wide w-[105px]">
                                    Ticket
                                </th>

                                <th class="px-5 py-3.5 text-left text-xs font-semibold
                                               text-gray-500 uppercase tracking-wide w-[125px]">
                                    Orden
                                </th>

                                <th class="px-5 py-3.5 text-left text-xs font-semibold
                                               text-gray-500 uppercase tracking-wide min-w-[220px]">
                                    Cliente
                                </th>

                                <th class="px-5 py-3.5 text-left text-xs font-semibold
                                               text-gray-500 uppercase tracking-wide w-[120px]">
                                    Tipo
                                </th>

                                <th class="px-5 py-3.5 text-left text-xs font-semibold
                                               text-gray-500 uppercase tracking-wide w-[120px]">
                                    Prioridad
                                </th>

                                <th class="px-5 py-3.5 text-left text-xs font-semibold
                                               text-gray-500 uppercase tracking-wide w-[130px]">
                                    Estado
                                </th>

                                <th class="px-5 py-3.5 text-left text-xs font-semibold
                                               text-gray-500 uppercase tracking-wide w-[145px]">
                                    Fecha
                                </th>



                                <th class="px-5 py-3.5 text-center text-xs font-semibold
                                               text-gray-500 uppercase tracking-wide w-[95px]">
                                    Acción
                                </th>

                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            @foreach($tickets as $ticket)

                            <tr
                                id="ticket-row-{{ $ticket->id }}"
                                class="
                                            transition-colors
                                            hover:bg-gray-50/80
                                            @if($ticket->estado === 'Abierto')
                                                bg-blue-50/40
                                            @elseif($ticket->estado === 'Cerrado')
                                                bg-green-50/40
                                            @endif
                                        ">

                                {{-- TICKET --}}
                                <td class="px-5 py-4 align-top">
                                    <span class="inline-flex items-center
                                                         px-2.5 py-1 rounded-lg
                                                         bg-gray-100 text-gray-800
                                                         text-sm font-semibold">
                                        {{ $ticket->ticket_codigo ?? '—' }}
                                    </span>
                                </td>

                                {{-- ORDEN --}}
                                <td class="px-5 py-4 align-top">

                                    @if(!empty($ticket->numero_orden))

                                    <div class="font-semibold text-gray-800">
                                        {{ $ticket->numero_orden }}
                                    </div>

                                    <div class="text-xs text-gray-400 mt-1">
                                        Orden / venta
                                    </div>

                                    @else

                                    <span class="text-sm text-gray-400">
                                        Sin orden
                                    </span>

                                    @endif

                                </td>

                                {{-- CLIENTE --}}
                                <td class="px-5 py-4 align-top">

                                    <div class="font-medium text-gray-800">
                                        {{ $ticket->nombre ?? '—' }}
                                    </div>

                                    @if(!empty($ticket->correo))

                                    <div class="flex items-center gap-1.5
                                                            text-xs text-gray-500 mt-1">

                                        <i class="fa-regular fa-envelope text-gray-400"></i>

                                        <span class="truncate max-w-[210px]"
                                            title="{{ $ticket->correo }}">
                                            {{ $ticket->correo }}
                                        </span>

                                    </div>

                                    @endif

                                </td>

                                {{-- TIPO --}}
                                <td class="px-5 py-4 align-top">

                                    @if(!empty($ticket->tipo_calidad))

                                    <span class="text-sm font-medium text-gray-700">
                                        {{ ucfirst($ticket->tipo_calidad) }}
                                    </span>

                                    @else

                                    <span class="text-sm text-gray-400">
                                        —
                                    </span>

                                    @endif

                                </td>

                                {{-- PRIORIDAD --}}
                                <td class="px-5 py-4 align-top">

                                    @if($ticket->prioridad === 'Alta')

                                    <span class="inline-flex items-center gap-1.5
                                                             px-2.5 py-1 rounded-full
                                                             text-xs font-semibold
                                                             bg-red-50 text-red-700
                                                             border border-red-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Alta
                                    </span>

                                    @elseif($ticket->prioridad === 'Media')

                                    <span class="inline-flex items-center gap-1.5
                                                             px-2.5 py-1 rounded-full
                                                             text-xs font-semibold
                                                             bg-amber-50 text-amber-700
                                                             border border-amber-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Media
                                    </span>

                                    @elseif($ticket->prioridad === 'Baja')

                                    <span class="inline-flex items-center gap-1.5
                                                             px-2.5 py-1 rounded-full
                                                             text-xs font-semibold
                                                             bg-green-50 text-green-700
                                                             border border-green-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Baja
                                    </span>

                                    @else

                                    <span class="text-sm text-gray-400">
                                        Sin definir
                                    </span>

                                    @endif

                                </td>

                                {{-- ESTADO --}}
                                <td class="px-5 py-4 align-top">

                                    @if($ticket->estado === 'Abierto')

                                    <span id="ticket-estado-{{ $ticket->id }}" class="inline-flex items-center gap-1.5
                                                             px-2.5 py-1 rounded-full
                                                             text-xs font-semibold
                                                             bg-blue-50 text-blue-700
                                                             border border-blue-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        Abierto
                                    </span>

                                    @elseif($ticket->estado === 'En proceso')

                                    <span id="ticket-estado-{{ $ticket->id }}" class="inline-flex items-center gap-1.5
                                                             px-2.5 py-1 rounded-full
                                                             text-xs font-semibold
                                                             bg-orange-50 text-orange-700
                                                             border border-orange-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                        En proceso
                                    </span>

                                    @elseif($ticket->estado === 'Cerrado')

                                    <span id="ticket-estado-{{ $ticket->id }}" class="inline-flex items-center gap-1.5
                                                             px-2.5 py-1 rounded-full
                                                             text-xs font-semibold
                                                             bg-green-50 text-green-700
                                                             border border-green-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Cerrado
                                    </span>

                                    @else

                                    <span class="text-sm text-gray-400">
                                        {{ $ticket->estado ?? 'Sin definir' }}
                                    </span>

                                    @endif

                                </td>

                                {{-- FECHA --}}
                                <td class="px-5 py-4 align-top whitespace-nowrap">

                                    @if(!empty($ticket->fecha))

                                    @php
                                    $fechaTicket = \Carbon\Carbon::parse($ticket->fecha);
                                    @endphp

                                    <div class="text-sm font-medium text-gray-700">
                                        {{ $fechaTicket->format('d/m/Y') }}
                                    </div>

                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ $fechaTicket->format('H:i') }}
                                    </div>

                                    @else

                                    <span class="text-sm text-gray-400">
                                        —
                                    </span>

                                    @endif

                                </td>

                                {{-- ACCIÓN --}}
                                <td class="px-5 py-4 align-top text-center">

                                    <button
                                        type="button"
                                        @click="abrirTicket({{ $ticket->id }})"
                                        class="inline-flex items-center justify-center gap-2
                                                       px-3.5 py-2 rounded-lg
                                                       bg-blue-600 text-white
                                                       text-sm font-medium
                                                       hover:bg-blue-700
                                                       transition">
                                        <i class="fa-solid fa-eye"></i>
                                        Ver
                                    </button>

                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                <div class="px-5 py-4 border-t border-gray-100
                                bg-gray-50 flex items-center justify-between">

                    <p class="text-sm text-gray-500">
                        Mostrando
                        <span class="font-semibold text-gray-700">
                            {{ $tickets->count() }}
                        </span>
                        tickets
                    </p>

                    <p class="text-xs text-gray-400">
                        Información obtenida del sistema de calidad
                    </p>

                </div>

                @endif

            </div>


            {{-- ========================================================= --}}
            {{-- MODAL DETALLE DEL TICKET --}}
            {{-- ========================================================= --}}

            <div
                x-show="open"
                x-cloak
                @keydown.escape.window="cerrar()"
                class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4"
                aria-labelledby="modal-ticket-title"
                role="dialog"
                aria-modal="true">

                {{-- FONDO --}}
                <div
                    x-show="open"
                    x-transition.opacity
                    @click="cerrar()"
                    class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>


                {{-- CONTENIDO --}}
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    @click.stop
                    class="relative w-full max-w-3xl
                   h-[80vh]
                   bg-white rounded-2xl shadow-2xl
                   overflow-hidden
                   flex flex-col">

                    {{-- ENCABEZADO --}}
                    <div class="flex items-center justify-between
                        px-6 py-4
                        border-b border-gray-200
                        bg-white flex-shrink-0">

                        <div class="min-w-0">

                            <div class="flex items-center gap-3">

                                <h2
                                    id="modal-ticket-title"
                                    class="text-lg font-semibold text-gray-800">
                                    Detalle del ticket
                                </h2>

                                <span
                                    x-show="ticket"
                                    x-text="ticket ? ticket.ticket_codigo : ''"
                                    class="inline-flex items-center
                                   px-2.5 py-1 rounded-lg
                                   bg-gray-100 text-gray-700
                                   text-sm font-semibold"></span>

                            </div>

                            <p class="text-xs text-gray-500 mt-1">
                                Información y evidencias del ticket.
                            </p>

                        </div>


                        <button
                            type="button"
                            @click="cerrar()"
                            class="w-9 h-9 rounded-lg
                           flex items-center justify-center
                           text-gray-500
                           hover:bg-gray-100
                           hover:text-gray-700
                           transition
                           flex-shrink-0"
                            aria-label="Cerrar">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>

                    </div>


                    {{-- CONTENIDO SCROLLEABLE --}}
                    <div class="flex-1 overflow-y-auto">

                        {{-- CARGANDO --}}
                        <div
                            x-show="loading"
                            class="p-12 flex flex-col items-center justify-center">
                            <div class="w-10 h-10 rounded-full
                                border-4 border-gray-200
                                border-t-blue-600
                                animate-spin">
                            </div>

                            <p class="text-sm text-gray-500 mt-4">
                                Cargando información del ticket...
                            </p>
                        </div>


                        {{-- ERROR --}}
                        <div
                            x-show="error"
                            class="m-6 p-4 rounded-xl
                           bg-red-50 border border-red-200
                           text-red-700">
                            <div class="flex items-start gap-3">

                                <i class="fa-solid fa-circle-exclamation mt-0.5"></i>

                                <div>
                                    <p class="font-semibold">
                                        No se pudo cargar el ticket.
                                    </p>

                                    <p
                                        class="text-sm mt-1"
                                        x-text="error"></p>
                                </div>

                            </div>
                        </div>


                        {{-- INFORMACIÓN DEL TICKET --}}
                        <div
                            x-show="ticket && !loading && !error"
                            class="p-4 space-y-4">

                            {{-- DATOS PRINCIPALES --}}
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">

                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                        Orden / venta
                                    </p>

                                    <p
                                        class="text-base font-semibold text-gray-800 mt-1"
                                        x-text="ticket ? (ticket.numero_orden || 'Sin orden') : '—'"></p>
                                </div>


                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                        Cliente
                                    </p>

                                    <p
                                        class="text-base font-semibold text-gray-800 mt-1 truncate"
                                        :title="ticket ? ticket.nombre : ''"
                                        x-text="ticket ? (ticket.nombre || '—') : '—'"></p>
                                </div>


                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                        Tipo de calidad
                                    </p>

                                    <p
                                        class="text-base font-semibold text-gray-800 mt-1"
                                        x-text="ticket ? (ticket.tipo_calidad || '—') : '—'"></p>
                                </div>


                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                        Estado
                                    </p>

                                    @can('atender tickets calidad')
                                    <div class="mt-2">
                                        <select
                                            x-model="ticket.estado"
                                            @change="actualizarEstado()"
                                            :disabled="guardandoEstado"
                                            class="w-full rounded-lg border border-gray-300
                                               bg-white px-3 py-2
                                               text-sm font-medium text-gray-700
                                               focus:border-blue-500
                                               focus:ring-2 focus:ring-blue-100
                                               disabled:opacity-60 disabled:cursor-not-allowed">
                                            <option value="Abierto">Abierto</option>
                                            <option value="En proceso">En proceso</option>
                                            <option value="Cerrado">Cerrado</option>
                                        </select>

                                        <div
                                            x-show="guardandoEstado"
                                            class="flex items-center gap-1.5 mt-1.5 text-xs text-gray-400">
                                            <i class="fa-solid fa-spinner fa-spin"></i>
                                            Guardando estado...
                                        </div>

                                        <div
                                            x-show="mensajeEstado"
                                            x-text="mensajeEstado"
                                            class="text-xs text-green-600 mt-1.5"></div>

                                        <div
                                            x-show="errorEstado"
                                            x-text="errorEstado"
                                            class="text-xs text-red-600 mt-1.5"></div>
                                    </div>
                                    @else
                                    <div class="mt-2">
                                        <span
                                            class="inline-flex items-center gap-1.5
                                               px-2.5 py-1 rounded-full
                                               text-xs font-semibold"
                                            :class="{
                                            'bg-blue-50 text-blue-700 border border-blue-100': ticket && ticket.estado === 'Abierto',
                                            'bg-orange-50 text-orange-700 border border-orange-100': ticket && ticket.estado === 'En proceso',
                                            'bg-green-50 text-green-700 border border-green-100': ticket && ticket.estado === 'Cerrado',
                                            'bg-gray-100 text-gray-600 border border-gray-200': ticket && !['Abierto', 'En proceso', 'Cerrado'].includes(ticket.estado)
                                        }">
                                            <span
                                                class="w-1.5 h-1.5 rounded-full"
                                                :class="{
                                                'bg-blue-500': ticket && ticket.estado === 'Abierto',
                                                'bg-orange-500': ticket && ticket.estado === 'En proceso',
                                                'bg-green-500': ticket && ticket.estado === 'Cerrado',
                                                'bg-gray-400': ticket && !['Abierto', 'En proceso', 'Cerrado'].includes(ticket.estado)
                                            }"></span>

                                            <span
                                                x-text="ticket ? (ticket.estado || 'Sin definir') : '—'"></span>
                                        </span>
                                    </div>
                                    @endcan
                                </div>

                            </div>


                            {{-- PRIORIDAD / FECHA / CORREO --}}
                            <div class="grid grid-cols-3 gap-3">

                                <div class="rounded-lg border border-gray-200 p-3">

                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                        Prioridad
                                    </p>

                                    <div class="mt-2">
                                        <span
                                            class="inline-flex items-center gap-1.5
                                           px-2.5 py-1 rounded-full
                                           text-xs font-semibold"
                                            :class="{
                                        'bg-red-50 text-red-700 border border-red-100': ticket && ticket.prioridad === 'Alta',
                                        'bg-amber-50 text-amber-700 border border-amber-100': ticket && ticket.prioridad === 'Media',
                                        'bg-green-50 text-green-700 border border-green-100': ticket && ticket.prioridad === 'Baja',
                                        'bg-gray-100 text-gray-500 border border-gray-200': ticket && !['Alta', 'Media', 'Baja'].includes(ticket.prioridad)
                                    }">
                                            <span x-text="ticket ? (ticket.prioridad || 'Sin definir') : '—'"></span>
                                        </span>
                                    </div>

                                </div>


                                <div class="rounded-lg border border-gray-200 p-3">

                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                        Fecha
                                    </p>

                                    <p
                                        class="text-sm font-medium text-gray-700 mt-2"
                                        x-text="ticket ? formatearFecha(ticket.fecha) : '—'"></p>

                                </div>


                                <div class="rounded-lg border border-gray-200 p-3">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        Correo
                                    </p>

                                    <p
                                        class="mt-2 text-sm text-gray-800 break-all"
                                        x-text="ticket ? (ticket.correo || 'Sin correo') : '—'"></p>

                                    <div
                                        x-show="ticket && ticket.correo"
                                        class="mt-3 flex items-center gap-2">
                                        {{-- Gmail --}}
                                        <a
                                            :href="ticket && ticket.correo
                                                ? `https://mail.google.com/mail/?view=cm&fs=1&to=${encodeURIComponent(ticket.correo)}&su=${encodeURIComponent(
                                                    'Seguimiento ticket ' +
                                                    (ticket.ticket_codigo || '') +
                                                    (ticket.numero_orden
                                                        ? ' - Orden ' + ticket.numero_orden
                                                        : '')
                                                )}`
                                                : '#'"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center gap-2
                                                    px-3 py-2 rounded-lg
                                                    bg-red-600 text-white text-sm font-semibold
                                                    hover:bg-red-700
                                                    transition-colors shadow-sm"
                                            title="Abrir Gmail">
                                            <i class="fa-brands fa-google"></i>
                                            Gmail
                                        </a>

                                        {{-- Correo predeterminado --}}
                                        <a
                                            :href="ticket && ticket.correo
                                                ? `mailto:${ticket.correo}?subject=${encodeURIComponent(
                                                    'Seguimiento ticket ' +
                                                    (ticket.ticket_codigo || '') +
                                                    (ticket.numero_orden
                                                        ? ' - Orden ' + ticket.numero_orden
                                                        : '')
                                                )}`
                                                : '#'"
                                            class="inline-flex items-center gap-2
                                                    px-3 py-2 rounded-lg
                                                    bg-blue-600 text-white text-sm font-semibold
                                                    hover:bg-blue-700
                                                    transition-colors shadow-sm"
                                            title="Abrir correo predeterminado">
                                            <i class="fa-solid fa-envelope"></i>
                                            Correo
                                        </a>
                                    </div>
                                </div>

                            </div>


                            {{-- PROBLEMA --}}
                            <div>

                                <div class="flex items-center gap-2 mb-3">

                                    <i class="fa-solid fa-triangle-exclamation text-gray-400"></i>

                                    <h3 class="text-base font-semibold text-gray-800">
                                        Problema reportado
                                    </h3>

                                </div>

                                <div class="rounded-lg
                                    border border-gray-200
                                    bg-gray-50
                                    p-3">

                                    <p
                                        class="text-sm text-gray-700 leading-6 whitespace-pre-line"
                                        x-text="ticket ? (ticket.problema || 'Sin descripción') : ''"></p>

                                </div>

                            </div>


                            {{-- ARCHIVOS --}}
                            <div>

                                <div class="flex items-center justify-between gap-4 mb-3">

                                    <div class="flex items-center gap-2">

                                        <i class="fa-solid fa-paperclip text-gray-400"></i>

                                        <h3 class="text-base font-semibold text-gray-800">
                                            Evidencias
                                        </h3>

                                        <span
                                            class="inline-flex items-center justify-center
                                           min-w-6 h-6 px-2
                                           rounded-full
                                           bg-gray-100 text-gray-600
                                           text-xs font-semibold"
                                            x-text="archivos.length"></span>

                                    </div>

                                </div>


                                {{-- SIN ARCHIVOS --}}
                                <div
                                    x-show="archivos.length === 0"
                                    class="rounded-xl border border-dashed border-gray-300
                                   p-8 text-center">
                                    <div class="w-12 h-12 mx-auto rounded-xl
                                        bg-gray-100
                                        flex items-center justify-center">
                                        <i class="fa-regular fa-image text-gray-400 text-xl"></i>
                                    </div>

                                    <p class="text-sm font-medium text-gray-600 mt-3">
                                        Este ticket no tiene archivos adjuntos.
                                    </p>
                                </div>


                                {{-- GALERÍA --}}
                                <div
                                    x-show="archivos.length > 0"
                                    class="grid grid-cols-3 gap-3">

                                    <template x-for="archivo in archivos" :key="archivo.id">

                                        {{-- ===================================================== --}}
                                        {{-- IMAGEN --}}
                                        {{-- ===================================================== --}}
                                        <template x-if="esImagen(archivo.tipo, archivo.nombre_archivo)">

                                            <button
                                                type="button"
                                                @click="abrirImagen(archivo)"
                                                class="group block w-full text-left
                       rounded-xl overflow-hidden
                       border border-gray-200
                       bg-white
                       hover:border-blue-300
                       hover:shadow-md
                       transition">

                                                {{-- IMAGEN --}}
                                                <div class="aspect-[4/3] bg-gray-100 overflow-hidden">

                                                    <img
                                                        :src="archivo.url"
                                                        :alt="archivo.nombre_archivo"
                                                        loading="lazy"
                                                        class="w-full h-full object-cover
                               group-hover:scale-105
                               transition duration-300">

                                                </div>


                                                {{-- NOMBRE --}}
                                                <div class="p-2 border-t border-gray-100">

                                                    <p
                                                        class="text-xs font-medium text-gray-700 truncate"
                                                        :title="archivo.nombre_archivo"
                                                        x-text="archivo.nombre_archivo"></p>

                                                    <p class="text-[11px] text-gray-400 mt-1">

                                                        <span
                                                            x-text="formatearTamano(archivo.tamaño)"></span>

                                                        <span class="mx-1">•</span>

                                                        <span
                                                            x-text="formatearFecha(archivo.fecha_subida)"></span>

                                                    </p>

                                                </div>

                                            </button>

                                        </template>


                                        {{-- ===================================================== --}}
                                        {{-- ARCHIVO NO IMAGEN --}}
                                        {{-- ===================================================== --}}
                                        <template x-if="!esImagen(archivo.tipo, archivo.nombre_archivo)">

                                            <a
                                                :href="archivo.url"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="group block
                       rounded-xl overflow-hidden
                       border border-gray-200
                       bg-white
                       hover:border-blue-300
                       hover:shadow-md
                       transition">

                                                {{-- ICONO --}}
                                                <div
                                                    class="aspect-[4/3]
                           bg-gray-50
                           flex flex-col items-center justify-center
                           gap-2">

                                                    <i
                                                        class="fa-solid"
                                                        :class="iconoArchivo(
                            archivo.tipo,
                            archivo.nombre_archivo
                        )"
                                                        style="font-size: 2rem;"></i>

                                                    <span class="text-xs font-semibold text-gray-500">
                                                        Abrir archivo
                                                    </span>

                                                </div>


                                                {{-- NOMBRE --}}
                                                <div class="p-2 border-t border-gray-100">

                                                    <p
                                                        class="text-xs font-medium text-gray-700 truncate"
                                                        :title="archivo.nombre_archivo"
                                                        x-text="archivo.nombre_archivo"></p>

                                                    <p class="text-[11px] text-gray-400 mt-1">

                                                        <span
                                                            x-text="formatearTamano(archivo.tamaño)"></span>

                                                        <span class="mx-1">•</span>

                                                        <span
                                                            x-text="formatearFecha(archivo.fecha_subida)"></span>

                                                    </p>

                                                </div>

                                            </a>

                                        </template>

                                    </template>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- ========================================================= --}}
                    {{-- VISOR DE IMAGEN --}}
                    {{-- ========================================================= --}}

                    <div
                        x-show="imagenSeleccionada"
                        x-cloak
                        x-transition.opacity
                        @click.self="cerrarImagen()"
                        @keydown.escape.window="cerrarImagen()"
                        class="fixed inset-0 z-[100]
                               bg-black/80
                               flex items-center justify-center
                               p-4">

                        {{-- CONTENEDOR DEL VISOR --}}
                        <div
                            x-show="imagenSeleccionada"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="relative
                                   w-full max-w-5xl
                                   max-h-[90vh]
                                   flex flex-col
                                   items-center
                                   justify-center">

                            {{-- BOTÓN CERRAR --}}
                            <button
                                type="button"
                                @click="cerrarImagen()"
                                class="absolute top-0 right-0 z-20
                                       w-10 h-10
                                       rounded-full
                                       bg-black/60
                                       text-white
                                       flex items-center justify-center
                                       hover:bg-black/80
                                       transition"
                                title="Cerrar imagen">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>


                            {{-- IMAGEN --}}
                            <img
    x-show="imagenSeleccionada"
    :src="imagenSeleccionada ? imagenSeleccionada.url : ''"
    :alt="imagenSeleccionada ? imagenSeleccionada.nombre_archivo : ''"
    style="
        width: min(900px, 75vw);
        height: min(550px, 60vh);
        min-width: 300px;
        min-height: 200px;
        object-fit: contain;
    "
    class="rounded-lg shadow-2xl"
>


                            {{-- BARRA INFERIOR --}}
                            <div
                                x-show="imagenSeleccionada"
                                class="mt-4
                                       flex flex-col sm:flex-row
                                       items-center
                                       justify-center
                                       gap-3
                                       max-w-full">

                                {{-- NOMBRE --}}
                                <div
                                    class="px-3 py-2
                                           rounded-lg
                                           bg-black/70
                                           text-white
                                           text-xs
                                           max-w-[320px]
                                           truncate"
                                    :title="imagenSeleccionada
                                        ? imagenSeleccionada.nombre_archivo
                                        : ''"
                                    x-text="imagenSeleccionada
                                        ? imagenSeleccionada.nombre_archivo
                                        : ''"></div>


                                {{-- DESCARGAR --}}
                                <a
                                    :href="imagenSeleccionada
                                            ? `{{ url('/tickets-calidad/archivo') }}/${imagenSeleccionada.id}/descargar`
                                            : '#'"
                                    class="inline-flex
                                            items-center
                                            gap-2
                                            px-4 py-2
                                            rounded-lg
                                            bg-blue-600
                                            text-white
                                            text-sm
                                            font-semibold
                                            shadow-lg
                                            hover:bg-blue-700
                                            transition"
                                    title="Descargar imagen">
                                    <i class="fa-solid fa-download"></i>
                                    Descargar
                                </a>

                            </div>

                        </div>

                    </div>


                    {{-- PIE --}}
                    <div class="flex justify-end
                        px-5 py-3
                        border-t border-gray-200
                        bg-white flex-shrink-0">

                        <button
                            type="button"
                            @click="cerrar()"
                            class="inline-flex items-center gap-2
                           px-3 py-1.5
                           rounded-lg
                           border border-gray-300
                           text-gray-700
                           text-sm font-medium
                           hover:bg-gray-50
                           transition">
                            <i class="fa-solid fa-xmark"></i>
                            Cerrar
                        </button>

                    </div>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- ALPINE: DETALLE DEL TICKET --}}
            {{-- ========================================================= --}}

            <script>
                function ticketDetalle() {

                    return {

                        open: false,

                        loading: false,

                        error: '',

                        ticket: null,

                        archivos: [],

                        guardandoEstado: false,

                        mensajeEstado: '',

                        errorEstado: '',

                        estadoAnterior: '',

                        imagenSeleccionada: null,


                        abrirTicket(id) {

                            this.open = true;

                            this.loading = true;

                            this.error = '';

                            this.ticket = null;

                            this.archivos = [];

                            this.imagenSeleccionada = null;

                            document.body.classList.add('overflow-hidden');

                            fetch(`{{ url('/tickets-calidad') }}/${id}`, {

                                    method: 'GET',

                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }

                                })

                                .then(async response => {

                                    const data = await response.json();

                                    if (!response.ok) {

                                        throw new Error(

                                            data.mensaje ||

                                            data.message ||

                                            'No se pudo cargar el ticket.'

                                        );

                                    }

                                    return data;

                                })

                                .then(data => {

                                    this.ticket = data.ticket;

                                    this.archivos = data.archivos || [];

                                    this.estadoAnterior = data.ticket?.estado || '';

                                    this.mensajeEstado = '';

                                    this.errorEstado = '';

                                })

                                .catch(error => {

                                    console.error(error);

                                    this.error =
                                        error.message ||
                                        'Ocurrió un error al cargar el ticket.';

                                })

                                .finally(() => {

                                    this.loading = false;

                                });

                        },


                        /* ========================================================= */
                        /* VISOR DE IMÁGENES */
                        /* ========================================================= */

                        abrirImagen(archivo) {

                            if (!archivo || !archivo.url) {
                                return;
                            }

                            this.imagenSeleccionada = archivo;

                        },


                        cerrarImagen() {

                            this.imagenSeleccionada = null;

                        },


                        /* ========================================================= */
                        /* ACTUALIZAR ESTADO */
                        /* ========================================================= */

                        actualizarEstado() {

                            if (!this.ticket || this.guardandoEstado) {
                                return;
                            }

                            const nuevoEstado = this.ticket.estado;

                            const estadoAnterior = this.estadoAnterior;

                            if (!['Abierto', 'En proceso', 'Cerrado'].includes(nuevoEstado)) {

                                this.ticket.estado = estadoAnterior;

                                return;

                            }

                            if (nuevoEstado === estadoAnterior) {
                                return;
                            }

                            this.guardandoEstado = true;

                            this.mensajeEstado = '';

                            this.errorEstado = '';

                            const csrfToken = document
                                .querySelector('meta[name="csrf-token"]')
                                ?.getAttribute('content');

                            fetch(`{{ url('/tickets-calidad') }}/${this.ticket.id}/estado`, {

                                    method: 'PATCH',

                                    headers: {

                                        'Accept': 'application/json',

                                        'Content-Type': 'application/json',

                                        'X-Requested-With': 'XMLHttpRequest',

                                        'X-CSRF-TOKEN': csrfToken || ''

                                    },

                                    body: JSON.stringify({

                                        estado: nuevoEstado

                                    })

                                })

                                .then(async response => {

                                    const data = await response.json();

                                    if (!response.ok) {

                                        throw new Error(

                                            data.mensaje ||

                                            data.message ||

                                            'No se pudo actualizar el estado.'

                                        );

                                    }

                                    return data;

                                })

                                .then(data => {

                                    const estadoGuardado =
                                        data.estado || nuevoEstado;

                                    this.actualizarTabla(

                                        estadoAnterior,

                                        estadoGuardado

                                    );

                                    this.estadoAnterior = estadoGuardado;

                                    this.ticket.estado = estadoGuardado;

                                    this.mensajeEstado =
                                        data.mensaje || 'Estado actualizado correctamente.';

                                    setTimeout(() => {

                                        this.mensajeEstado = '';

                                    }, 2500);

                                })

                                .catch(error => {

                                    console.error(error);

                                    this.ticket.estado = estadoAnterior;

                                    this.errorEstado =
                                        error.message ||
                                        'No se pudo actualizar el estado.';

                                })

                                .finally(() => {

                                    this.guardandoEstado = false;

                                });

                        },


                        /* ========================================================= */
                        /* ACTUALIZAR TABLA */
                        /* ========================================================= */

                        actualizarTabla(estadoAnterior, nuevoEstado) {

                            if (!this.ticket) {
                                return;
                            }

                            const id = this.ticket.id;

                            const badge = document.getElementById(
                                `ticket-estado-${id}`
                            );

                            if (badge) {

                                badge.className =
                                    'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold';

                                const clasesEstado = {

                                    'Abierto': [
                                        'bg-blue-50',
                                        'text-blue-700',
                                        'border',
                                        'border-blue-100'
                                    ],

                                    'En proceso': [
                                        'bg-orange-50',
                                        'text-orange-700',
                                        'border',
                                        'border-orange-100'
                                    ],

                                    'Cerrado': [
                                        'bg-green-50',
                                        'text-green-700',
                                        'border',
                                        'border-green-100'
                                    ]

                                };

                                (
                                    clasesEstado[nuevoEstado] || [
                                        'bg-gray-100',
                                        'text-gray-600',
                                        'border',
                                        'border-gray-200'
                                    ]
                                ).forEach(clase => badge.classList.add(clase));

                                const colorPunto = {

                                    'Abierto': 'bg-blue-500',

                                    'En proceso': 'bg-orange-500',

                                    'Cerrado': 'bg-green-500'

                                };

                                badge.innerHTML = `

                        <span
                            class="w-1.5 h-1.5 rounded-full
                            ${colorPunto[nuevoEstado] || 'bg-gray-400'}"
                        ></span>

                        ${nuevoEstado}

                    `;

                            }


                            // Cambiar el color de la fila inmediatamente.

                            const fila = document.getElementById(
                                `ticket-row-${id}`
                            );

                            if (fila) {

                                fila.classList.remove(
                                    'bg-blue-50/40',
                                    'bg-green-50/40'
                                );

                                if (nuevoEstado === 'Abierto') {

                                    fila.classList.add(
                                        'bg-blue-50/40'
                                    );

                                }

                                if (nuevoEstado === 'Cerrado') {

                                    fila.classList.add(
                                        'bg-green-50/40'
                                    );

                                }

                            }


                            // Actualizar contadores.

                            const abiertos = document.getElementById(
                                'total-tickets-abiertos'
                            );

                            const proceso = document.getElementById(
                                'total-tickets-proceso'
                            );


                            if (estadoAnterior === 'Abierto' && abiertos) {

                                abiertos.textContent = Math.max(

                                    0,

                                    Number(abiertos.textContent) - 1

                                );

                            }


                            if (estadoAnterior === 'En proceso' && proceso) {

                                proceso.textContent = Math.max(

                                    0,

                                    Number(proceso.textContent) - 1

                                );

                            }


                            if (nuevoEstado === 'Abierto' && abiertos) {

                                abiertos.textContent =
                                    Number(abiertos.textContent) + 1;

                            }


                            if (nuevoEstado === 'En proceso' && proceso) {

                                proceso.textContent =
                                    Number(proceso.textContent) + 1;

                            }

                        },


                        /* ========================================================= */
                        /* CERRAR TICKET */
                        /* ========================================================= */

                        cerrar() {

                            this.open = false;

                            this.loading = false;

                            this.error = '';

                            this.ticket = null;

                            this.archivos = [];

                            this.imagenSeleccionada = null;

                            this.guardandoEstado = false;

                            this.mensajeEstado = '';

                            this.errorEstado = '';

                            this.estadoAnterior = '';

                            document.body.classList.remove('overflow-hidden');

                        },


                        /* ========================================================= */
                        /* FORMATEAR FECHA */
                        /* ========================================================= */

                        formatearFecha(fecha) {

                            if (!fecha) {

                                return '—';

                            }

                            const valor = new Date(fecha);

                            if (Number.isNaN(valor.getTime())) {

                                return fecha;

                            }

                            return valor.toLocaleString('es-MX', {

                                day: '2-digit',

                                month: '2-digit',

                                year: 'numeric',

                                hour: '2-digit',

                                minute: '2-digit'

                            });

                        },


                        /* ========================================================= */
                        /* FORMATEAR TAMAÑO */
                        /* ========================================================= */

                        formatearTamano(bytes) {

                            if (!bytes || Number(bytes) <= 0) {

                                return 'Tamaño no disponible';

                            }

                            const unidades = [

                                'B',

                                'KB',

                                'MB',

                                'GB'

                            ];

                            let valor = Number(bytes);

                            let indice = 0;

                            while (

                                valor >= 1024 &&

                                indice < unidades.length - 1

                            ) {

                                valor /= 1024;

                                indice++;

                            }

                            return `${valor.toFixed(indice === 0 ? 0 : 1)} ${unidades[indice]}`;

                        },


                        /* ========================================================= */
                        /* VALIDAR SI ES IMAGEN */
                        /* ========================================================= */

                        esImagen(tipo, nombre) {

                            if (tipo && tipo.startsWith('image/')) {

                                return true;

                            }

                            const extension = (

                                    nombre || ''

                                )

                                .split('.')

                                .pop()

                                .toLowerCase();

                            return [

                                'jpg',

                                'jpeg',

                                'png',

                                'gif',

                                'webp',

                                'bmp',

                                'svg'

                            ].includes(extension);

                        },


                        /* ========================================================= */
                        /* ICONO DE ARCHIVO */
                        /* ========================================================= */

                        iconoArchivo(tipo, nombre) {

                            if (

                                tipo === 'application/pdf' ||

                                (nombre || '').toLowerCase().endsWith('.pdf')

                            ) {

                                return 'fa-file-pdf text-red-500';

                            }

                            if (tipo && tipo.includes('word')) {

                                return 'fa-file-word text-blue-500';

                            }

                            if (

                                tipo && (

                                    tipo.includes('excel') ||

                                    tipo.includes('spreadsheet')

                                )

                            ) {

                                return 'fa-file-excel text-green-500';

                            }

                            return 'fa-file text-gray-400';

                        }

                    };

                }
            </script>


</x-app-layout>
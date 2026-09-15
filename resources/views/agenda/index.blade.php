<x-app-layout>

    <div class="p-6">

        {{-- Encabezado --}}
        <div class="flex items-center justify-between mb-6">

            <div>
                <h1 class="text-3xl font-bold text-white">
                    📅 Agenda Corporativa
                </h1>

                <p class="text-slate-400 mt-1">
                    Reservación de salas, vacaciones, permisos y eventos.
                </p>
            </div>

            <button
                onclick="document.getElementById('modalReserva').classList.remove('hidden')"
                class="inline-flex items-center gap-2 h-11 px-5 rounded-xl
                   bg-blue-600 border border-blue-400
                   text-white font-semibold
                   hover:bg-blue-700 transition">

                <i class="fa-solid fa-plus"></i>

                Nueva Reservación

            </button>

        </div>

        {{-- Barra de filtros --}}
        <div class="flex gap-3 mb-6">

            <input
                type="text"
                placeholder="🔍 Buscar evento..."
                class="w-80 h-11 rounded-xl
                   border border-slate-600
                   bg-slate-800
                   text-white px-4">

            <select
                class="w-52 h-11 rounded-xl
                   border border-slate-600
                   bg-slate-800
                   text-white px-3">

                <option>📍 Todos los recursos</option>

            </select>

            <select
                class="w-44 h-11 rounded-xl
                   border border-slate-600
                   bg-slate-800
                   text-white px-3">

                <option>📂 Todos los tipos</option>

            </select>

            <select
                class="w-44 h-11 rounded-xl
                   border border-slate-600
                   bg-slate-800
                   text-white px-3">

                <option>📅 Este mes</option>

            </select>

        </div>


        {{-- Calendario --}}
        <div
            class="bg-slate-800
           rounded-3xl
           border border-slate-700
           shadow-2xl
           p-6">

            <div id="calendar"></div>

        </div>

    </div>

    {{-- Modal --}}

    {{-- =========================================================
                        MODAL NUEVA RESERVACIÓN
    ========================================================= --}}
    <div
        id="modalReserva"
        class="hidden fixed inset-0 z-50">

        {{-- Overlay --}}
        <div
            class="absolute inset-0 bg-black/60 backdrop-blur-sm"
            onclick="document.getElementById('modalReserva').classList.add('hidden')">
        </div>

        {{-- Contenedor --}}
        <div class="relative flex items-center justify-center min-h-screen p-6">

            <div class="w-full max-w-[850px] bg-white rounded-3xl shadow-2xl overflow-hidden">

                {{-- =====================================================
                HEADER
            ====================================================== --}}
                <div class="flex items-center justify-between border-b border-slate-200 px-8 py-5">

                    <div class="flex items-center gap-4">

                        <div
                            class="w-14 h-14 rounded-2xl bg-blue-600 flex items-center justify-center shadow">

                            <i class="fa-solid fa-calendar-days text-white text-2xl"></i>

                        </div>

                        <div>

                            <h2 class="text-3xl font-bold text-slate-800">

                                Nueva Reservación

                            </h2>

                            <p class="text-slate-500 mt-1">

                                Programa reuniones, vacaciones, permisos y eventos corporativos.

                            </p>

                        </div>

                    </div>

                    <button
                        onclick="document.getElementById('modalReserva').classList.add('hidden')"
                        class="w-11 h-11 rounded-xl bg-slate-100 hover:bg-red-500 hover:text-white transition">

                        <i class="fa-solid fa-xmark text-lg"></i>

                    </button>

                </div>

                {{-- =====================================================
                                        BODY
                ====================================================== --}}
                <form
                    method="POST"
                    action="{{ route('agenda.store') }}"
                    id="formReserva">

                    @csrf
                    <div class="p-8">



                        <div class="grid grid-cols-2 gap-6">

                            {{-- Asunto --}}
                            <div class="col-span-2">

                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                    <i class="fa-solid fa-heading text-blue-600 mr-2"></i>
                                    Asunto
                                </label>

                                <input
                                    id="asuntoEvento"
                                    name="titulo"
                                    type="text"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3"
                                    placeholder="Ej. Reunión con Cliente ABC"
                                    require>

                            </div>

                            {{-- Tipo --}}
                            <div>

                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                    <i class="fa-solid fa-tags text-amber-500 mr-2"></i>
                                    Tipo
                                </label>

                                <select
                                    name="tipo"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3"
                                    required>

                                    <option value="Reunión">Reunión</option>
                                    <option value="Vacaciones">Vacaciones</option>
                                    <option value="Permiso">Permiso</option>
                                    <option value="Evento">Evento</option>

                                </select>

                            </div>

                            {{-- Recurso --}}
                            <div>

                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                    <i class="fa-solid fa-location-dot text-pink-500 mr-2"></i>
                                    Recurso
                                </label>

                                <select
                                    name="recurso_id"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3"
                                    required>

                                    <option value="1">Sala de Juntas</option>

                                </select>

                            </div>

                            {{-- Fecha --}}
                            <div>

                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                    <i class="fa-solid fa-calendar-days text-blue-500 mr-2"></i>
                                    Fecha
                                </label>

                                <input
                                    id="fechaEvento"
                                    name="fecha"
                                    type="date"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3"
                                    required>

                            </div>

                            {{-- Organizador --}}
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                    <i class="fa-solid fa-user text-green-600 mr-2"></i>
                                    Responsable del evento
                                </label>

                                <input
                                    type="number"
                                    name="personas"
                                    min="1"
                                    value="1"
                                    required
                                    placeholder="Número de asistentes"
                                    class="w-full rounded-xl border border-slate-300
                                            focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            {{-- Hora Inicio --}}
                            <div>

                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                    <i class="fa-regular fa-clock text-orange-500 mr-2"></i>
                                    Hora Inicio
                                </label>

                                <input
                                    id="horaInicio"
                                    name="hora_inicio"
                                    type="time"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3"
                                    required>

                            </div>

                            {{-- Hora Fin --}}
                            <div>

                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                    <i class="fa-regular fa-clock text-red-500 mr-2"></i>
                                    Hora Fin
                                </label>

                                <input
                                    id="horaFin"
                                    name="hora_fin"
                                    type="time"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3"
                                    required>

                            </div>

                            {{-- Descripción --}}
                            <div class="col-span-2">

                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                    <i class="fa-solid fa-align-left text-purple-500 mr-2"></i>
                                    Descripción
                                </label>

                                <textarea
                                    name="descripcion"
                                    rows="3"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 resize-none"
                                    placeholder="Describe el motivo de la reservación..."></textarea>

                            </div>

                        </div>

                    </div>

                    {{-- =====================================================
                    FOOTER
                    ====================================================== --}}
                    <div
                        class="flex justify-end gap-4 border-t border-slate-200 bg-slate-50 px-8 py-5">

                        <button
                            onclick="document.getElementById('modalReserva').classList.add('hidden')"
                            class="px-6 py-3 rounded-xl bg-slate-200 hover:bg-slate-300 transition font-medium">

                            Cancelar

                        </button>

                        <button
                            type="submit"
                            class="px-8 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold transition">

                            <i class="fa-solid fa-floppy-disk mr-2"></i>

                            Guardar Reservación

                        </button>

                    </div>
                </form>
            </div>

        </div>

    </div>
    {{-- =========================================================
                    MODAL DETALLE DEL EVENTO
========================================================= --}}

    <div
        id="modalEvento"
        class="hidden fixed inset-0 z-[60] flex items-center justify-center p-6">

        {{-- OVERLAY --}}
        <div
            class="absolute inset-0 bg-black/70 backdrop-blur-sm"
            onclick="cerrarModalEvento()">
        </div>


        {{-- CONTENEDOR --}}
        <div
            class="relative w-full max-w-2xl
               bg-white rounded-3xl
               shadow-2xl overflow-hidden">


            {{-- =====================================================
                            HEADER
        ====================================================== --}}

            <div
                class="flex items-center justify-between
                   border-b border-slate-200
                   px-8 py-5">

                <div class="flex items-center gap-4">

                    <div
                        class="w-14 h-14 rounded-2xl
                           bg-blue-600
                           flex items-center justify-center
                           shadow">

                        <i
                            class="fa-solid fa-calendar-check
                               text-white text-2xl">
                        </i>

                    </div>


                    <div>

                        <h2
                            id="detalleTitulo"
                            class="text-2xl font-bold text-slate-800">

                            Detalle del evento

                        </h2>

                        <p class="text-slate-500 mt-1">

                            Información de la reservación

                        </p>

                    </div>

                </div>


                {{-- CERRAR --}}

                <button
                    type="button"
                    onclick="cerrarModalEvento()"
                    class="w-11 h-11 rounded-xl
                       bg-slate-100
                       hover:bg-red-500
                       hover:text-white
                       transition">

                    <i class="fa-solid fa-xmark text-lg"></i>

                </button>

            </div>


            {{-- =====================================================
                            BODY
        ====================================================== --}}

            <div class="p-8 space-y-6">


                {{-- ASUNTO --}}

                <div>

                    <label
                        class="block text-sm font-semibold
                           text-slate-500 mb-2">

                        <i
                            class="fa-solid fa-heading
                               text-blue-600 mr-2">
                        </i>

                        Asunto

                    </label>

                    <div
                        id="detalleAsunto"
                        class="text-lg font-semibold
                           text-slate-800">

                        --

                    </div>

                </div>


                {{-- TIPO / RECURSO --}}

                <div class="grid grid-cols-2 gap-6">


                    {{-- TIPO --}}

                    <div>

                        <label
                            class="block text-sm font-semibold
                               text-slate-500 mb-2">

                            <i
                                class="fa-solid fa-tags
                                   text-amber-500 mr-2">
                            </i>

                            Tipo

                        </label>

                        <div
                            id="detalleTipo"
                            class="text-slate-800 font-medium">

                            --

                        </div>

                    </div>


                    {{-- RECURSO --}}

                    <div>

                        <label
                            class="block text-sm font-semibold
                               text-slate-500 mb-2">

                            <i
                                class="fa-solid fa-location-dot
                                   text-pink-500 mr-2">
                            </i>

                            Recurso

                        </label>

                        <div
                            id="detalleRecurso"
                            class="text-slate-800 font-medium">

                            --

                        </div>

                    </div>

                </div>


                {{-- FECHA / HORARIO --}}

                <div class="grid grid-cols-2 gap-6">


                    {{-- FECHA --}}

                    <div>

                        <label
                            class="block text-sm font-semibold
                               text-slate-500 mb-2">

                            <i
                                class="fa-solid fa-calendar-days
                                   text-blue-500 mr-2">
                            </i>

                            Fecha

                        </label>

                        <div
                            id="detalleFecha"
                            class="text-slate-800 font-medium">

                            --

                        </div>

                    </div>


                    {{-- HORARIO --}}

                    <div>

                        <label
                            class="block text-sm font-semibold
                               text-slate-500 mb-2">

                            <i
                                class="fa-regular fa-clock
                                   text-orange-500 mr-2">
                            </i>

                            Horario

                        </label>

                        <div
                            id="detalleHorario"
                            class="text-slate-800 font-medium">

                            --

                        </div>

                    </div>

                </div>


                {{-- PERSONAS --}}

                <div>

                    <label
                        class="block text-sm font-semibold
                           text-slate-500 mb-2">

                        <i
                            class="fa-solid fa-user
                               text-green-600 mr-2">
                        </i>

                        Responsable del evento

                    </label>

                    <div
                        id="detallePersonas"
                        class="text-slate-800 font-medium">

                        --

                    </div>

                </div>


                {{-- DESCRIPCIÓN --}}

                <div>

                    <label
                        class="block text-sm font-semibold
                           text-slate-500 mb-2">

                        <i
                            class="fa-solid fa-align-left
                               text-purple-500 mr-2">
                        </i>

                        Descripción

                    </label>

                    <div
                        id="detalleDescripcion"
                        class="min-h-[90px]
                           rounded-xl
                           border border-slate-200
                           bg-slate-50
                           p-4
                           text-slate-700">

                        --

                    </div>

                </div>

            </div>


            {{-- =====================================================
                            FOOTER
        ====================================================== --}}

            <div
                class="flex justify-end
                   border-t border-slate-200
                   bg-slate-50
                   px-8 py-5">

                <button
                    type="button"
                    onclick="cerrarModalEvento()"
                    class="px-6 py-3 rounded-xl
                       bg-slate-200
                       hover:bg-slate-300
                       transition
                       font-medium">

                    Cerrar

                </button>

            </div>

        </div>

    </div>
</x-app-layout>
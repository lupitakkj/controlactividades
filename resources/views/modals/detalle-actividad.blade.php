<!-- Ajustes compactos del modal de detalle -->
<style>
    /* Estados vacíos para archivos, comentarios e historial */
    #detalleModal .estado-vacio-actividad {
        min-height: 190px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .65rem;
        padding: 1.5rem 1rem;
        text-align: center;
        border: 1px dashed #dbe3ef;
        border-radius: 1rem;
        background: linear-gradient(180deg, #fbfdff 0%, #f8fafc 100%);
        color: #64748b;
    }
    #detalleModal .estado-vacio-actividad .estado-vacio-icono {
        width: 3.5rem;
        height: 3.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 1.1rem;
        background: #eff6ff;
        color: #2563eb;
        font-size: 1.55rem;
        margin-bottom: .15rem;
    }
    #detalleModal .estado-vacio-actividad strong {
        color: #334155;
        font-size: .9rem;
        font-weight: 700;
    }
    #detalleModal .estado-vacio-actividad p {
        max-width: 24rem;
        font-size: .8rem;
        line-height: 1.5;
        margin: 0;
    }
    #detalleModal #historialPausasContainer .estado-vacio-actividad {
        min-height: 155px;
    }

    /* Reduce el tamaño general sin quitar información ni controles. */
    #detalleModal > div { padding: .75rem !important; }
    #detalleModal > div > div {
        max-width: 68rem;
        max-height: 90vh;
        border-radius: 1.25rem;
    }
    #detalleModal > div > div > .relative.bg-slate-900 {
        padding: 1rem 1.5rem !important;
    }
    #detalleModal .overflow-y-auto.flex-1 > div {
        padding: 1rem !important;
        gap: 1rem !important;
    }
    #detalleModal .p-5 { padding: .875rem !important; }
    #detalleModal .px-5 { padding-left: 1rem !important; padding-right: 1rem !important; }
    #detalleModal .py-4 { padding-top: .75rem !important; padding-bottom: .75rem !important; }
    #detalleModal .sm\:px-6 { padding-left: 1rem !important; padding-right: 1rem !important; }
    #detalleModal .sm\:p-6 { padding: .875rem !important; }
    #detalleModal .lg\:p-8 { padding: 1rem !important; }
    #detalleModal .rounded-2xl { border-radius: 1rem !important; }
    #detalleModal .gap-4 { gap: .75rem !important; }
    #detalleModal .space-y-3 > :not([hidden]) ~ :not([hidden]) { margin-top: .5rem !important; }
    #detalleModal .space-y-6 > :not([hidden]) ~ :not([hidden]) { margin-top: 1rem !important; }
    #detalleModal h3 { line-height: 1.25; }
    #detalleModal .text-2xl { font-size: 1.35rem !important; line-height: 1.7rem !important; }
    #detalleModal .w-9.h-9 { width: 2rem !important; height: 2rem !important; border-radius: .65rem !important; }
    #detalleModal .w-10.h-10 { width: 2.25rem !important; height: 2.25rem !important; }
    #detalleModal textarea { min-height: 3.25rem; }
    #detalleModal #descripcionActividad { min-height: 4.5rem !important; }
    #detalleModal #historialPausasContainer > * { padding: .65rem !important; }
    #detalleModal #archivosContainer,
    #detalleModal #comentariosContainer { max-height: none !important; overflow: visible !important; }
    #detalleModal .grid.grid-cols-1.lg\:grid-cols-2.items-start > section { min-width: 0; }
    #detalleModal .grid.grid-cols-1.lg\:grid-cols-2.items-start > section .p-5 { padding: .75rem !important; }
    #detalleModal #listaSubtareas > * { padding: .75rem !important; }
    #detalleModal input:not([type="checkbox"]):not([type="file"]),
    #detalleModal select,
    #detalleModal textarea { font-size: .875rem; }
    #detalleModal .shadow-sm { box-shadow: 0 1px 2px rgb(15 23 42 / .05) !important; }
    @media (min-width: 1024px) {
        #detalleModal .grid-cols-\[minmax\(0\,1\.65fr\)_minmax\(320px\,1fr\)\] { align-items: start; }
        #detalleModal #detalleDisenador,
        #detalleModal #detalleCliente,
        #detalleModal #detalleCreacion,
        #detalleModal #detalleInicio,
        #detalleModal #detalleEntrega { overflow-wrap: anywhere; }
    }
    @media (max-width: 640px) {
        #detalleModal > div { padding: .25rem !important; }
        #detalleModal > div > div { max-height: 96vh; border-radius: 1rem; }
        #detalleModal > div > div > .relative.bg-slate-900 { padding: .875rem 1rem !important; }
        #detalleModal .overflow-y-auto.flex-1 > div { padding: .75rem !important; }
    }

    #detalleModal .tab-actividad { transition: background-color .15s, color .15s, border-color .15s; }
    #detalleModal #panelActividadArchivos > section,
    #detalleModal #panelActividadComentarios > section,
    #detalleModal #panelActividadHistorial > section { width: 100%; }
    /* Distribución principal: contenido amplio a la izquierda y panel informativo a la derecha. */
    @media (min-width: 1024px) {
        #detalleModal .grid-cols-\\[minmax\\(0\\,1\\.65fr\\)_minmax\\(320px\\,1fr\\)\\] {
            display: grid !important;
            grid-template-columns: minmax(0, 1.55fr) minmax(340px, .9fr) !important;
            align-items: start;
            width: 100% !important;
            max-width: none !important;
            grid-template-columns: minmax(0, 1.55fr) minmax(340px, .9fr) !important;
        }

        #detalleModal .grid.grid-cols-1.lg\:grid-cols-2 {
            min-width: 0;
            width: 100%;
        }

        #detalleModal #descripcionActividad {
            min-height: 7rem !important;
        }

        #detalleModal #detalleDisenador,
        #detalleModal #detalleCliente,
        #detalleModal #detalleCreacion,
        #detalleModal #detalleInicio,
        #detalleModal #detalleEntrega {
            min-width: 0;
            overflow-wrap: anywhere;
        }
    }

    @media (max-width: 1023px) {
        #detalleModal .grid-cols-\\[minmax\\(0\\,1\\.65fr\\)_minmax\\(320px\\,1fr\\)\\] {
            display: flex !important;
            flex-direction: column;
            width: 100%;
        }
    }

</style>

<!-- ========================================================= -->
<!-- MODAL DETALLE DE ACTIVIDAD - REDISEÑO -->
<!-- ========================================================= -->

<div id="detalleModal"
    class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm">

    <div class="flex items-center justify-center min-h-screen p-4 sm:p-6">

        <div
            class="bg-white w-full max-w-6xl max-h-[92vh] rounded-3xl shadow-2xl overflow-hidden flex flex-col">

            <!-- ================================================= -->
            <!-- ENCABEZADO -->
            <!-- ================================================= -->

            <div class="relative bg-slate-900 text-white px-6 sm:px-8 py-6">

                <button
                    type="button"
                    onclick="cerrarModal()"
                    class="absolute top-5 right-5 w-10 h-10 rounded-xl
                           bg-white/10 hover:bg-white/20 transition
                           flex items-center justify-center text-slate-200 hover:text-white">

                    <i class="fa-solid fa-xmark text-lg"></i>

                </button>

                <div class="pr-12">

                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                            Titulo de actividad
                        </span>

                        <span class="text-slate-600">•</span>

                        <span id="detalleEstadoHeader"
                            class="text-xs font-semibold uppercase tracking-wide text-slate-300">
                        </span>
                    </div>

                    <input
                        id="detalleTitulo"
                        class="w-full bg-transparent border-0 border-b border-white/10
                               focus:border-white/30 focus:ring-0 p-0 pb-2
                               text-2xl sm:text-3xl font-bold text-white placeholder-slate-500"
                        placeholder="Título de la actividad">

                    <div class="flex flex-wrap items-center gap-2 mt-4">

                        <select
                            id="detallePrioridad"
                            class="appearance-none rounded-xl border border-white/10
                                   bg-white/10 px-3 py-2 pr-8 text-sm font-semibold
                                   text-white focus:ring-2 focus:ring-white/20">

                            <option value="baja" class="text-slate-900">Baja</option>
                            <option value="media" class="text-slate-900">Media</option>
                            <option value="alta" class="text-slate-900">Alta</option>
                            <option value="urgente" class="text-slate-900">Urgente</option>

                        </select>

                        <span
                            id="detalleEstado"
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl
                                   bg-blue-500/15 border border-blue-400/20
                                   text-blue-200 text-sm font-semibold">
                        </span>

                        <span
                            id="detalleComplejidad"
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl
                                   bg-amber-500/15 border border-amber-400/20
                                   text-amber-200 text-sm font-semibold">
                        </span>

                    </div>

                </div>
            </div>


            <!-- ================================================= -->
            <!-- CONTENIDO -->
            <!-- ================================================= -->

            <div class="overflow-y-auto flex-1 bg-slate-50">

                <div class="p-5 sm:p-7 lg:p-8 space-y-6">

                    <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1.65fr)_minmax(320px,1fr)] gap-4 items-start">
                        <div class="min-w-0 space-y-4">

                    <!-- ========================================= -->
                    <!-- TIEMPOS -->
                    <!-- ========================================= -->

                    <section>

                        <div class="flex items-center gap-3 mb-3">

                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600
                                        flex items-center justify-center">
                                <i class="fa-solid fa-clock"></i>
                            </div>

                            <div>
                                <h3 class="font-bold text-slate-800">
                                    Seguimiento de tiempo
                                </h3>

                                <p class="text-xs text-slate-500">
                                    Tiempo trabajado frente al estimado
                                </p>
                            </div>

                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                                <div class="flex items-start justify-between">

                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            Trabajado
                                        </p>

                                        <p id="detalleTiempo"
                                            class="mt-2 text-2xl font-bold text-blue-600">
                                        </p>
                                    </div>

                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600
                                                flex items-center justify-center">
                                        <i class="fa-solid fa-stopwatch"></i>
                                    </div>

                                </div>
                            </div>

                            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                                <div class="flex items-start justify-between">

                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            Estimado
                                        </p>

                                        <p id="detalleEstimado"
                                            class="mt-2 text-2xl font-bold text-slate-800">
                                        </p>
                                    </div>

                                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600
                                                flex items-center justify-center">
                                        <i class="fa-solid fa-hourglass-half"></i>
                                    </div>

                                </div>
                            </div>

                            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                                <div class="flex items-start justify-between">

                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            Diferencia
                                        </p>

                                        <p id="detalleDiferencia"
                                            class="mt-2 text-2xl font-bold text-slate-800">
                                        </p>
                                    </div>

                                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600
                                                flex items-center justify-center">
                                        <i class="fa-solid fa-chart-line"></i>
                                    </div>

                                </div>
                            </div>

                        </div>

                    </section>


                    <!-- ========================================= -->
                    <!-- DESCRIPCIÓN -->
                    <!-- ========================================= -->

                    <section class="bg-white rounded-2xl border border-slate-200 shadow-sm">

                        <div class="px-5 sm:px-6 py-4 border-b border-slate-100">

                            <div class="flex items-center gap-3">

                                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600
                                            flex items-center justify-center">
                                    <i class="fa-solid fa-align-left"></i>
                                </div>

                                <div>
                                    <h3 class="font-bold text-slate-800">
                                        Descripción
                                    </h3>

                                    <p class="text-xs text-slate-500">
                                        Información y detalles de la actividad
                                    </p>
                                </div>

                            </div>

                        </div>

                        <div class="p-5 sm:p-6">

                            <textarea
                                id="detalleDescripcion"
                                rows="4"
                                class="w-full border border-slate-200 rounded-xl p-4
                                       text-sm text-slate-700 bg-slate-50
                                       focus:bg-white focus:border-blue-400
                                       focus:ring-2 focus:ring-blue-100
                                       resize-none"
                                placeholder="Descripción de la actividad..."></textarea>

                        </div>

                    </section>


                    <!-- ========================================= -->
                    <!-- SUBTAREAS -->
                    <!-- ========================================= -->

                    <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-5 sm:px-6 py-4 border-b border-slate-100">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                        <i class="fa-solid fa-list-check"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-800">Subtareas</h3>
                                        <p class="text-xs text-slate-500">Cada subtarea tiene responsable, tiempo estimado y avance propio.</p>
                                    </div>
                                </div>
                                <span id="contadorSubtareas" class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600">0 subtareas</span>
                            </div>
                        </div>

                        <div class="p-5 sm:p-6 space-y-4">
                            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                                <div class="flex items-center justify-between gap-3 mb-2">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Avance de la actividad</p>
                                        <p class="text-xs text-slate-500 mt-1">Sin subtareas se calcula por tiempo trabajado; con subtareas, se calcula de forma ponderada.</p>
                                    </div>
                                    <strong id="avanceSubtareasTexto" class="text-xl text-blue-700">0%</strong>
                                </div>
                                <div class="w-full h-2.5 rounded-full bg-blue-100 overflow-hidden">
                                    <div id="avanceSubtareasBarra" class="h-full rounded-full bg-blue-600 transition-all duration-300" style="width:0%"></div>
                                </div>
                                <p id="resumenSubtareas" class="text-xs text-slate-500 mt-2">Todavía no hay subtareas.</p>
                            </div>

                            <div id="mensajeSubtareas" class="hidden rounded-xl px-3 py-2 text-sm" role="status" aria-live="polite"></div>
                            <div id="subtareasContainer" class="space-y-3"></div>

                            <form id="crearSubtareaForm" method="POST" action="#" class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 space-y-3">
                                @csrf
                                <h4 class="font-semibold text-slate-800 text-sm">
                                    <i class="fa-solid fa-plus text-blue-600 mr-1"></i>
                                    Añadir subtarea
                                </h4>

                                <div>
                                    <label for="nuevaSubtareaTitulo" class="block text-xs font-semibold text-slate-600 mb-1">Nombre de la subtarea *</label>
                                    <input id="nuevaSubtareaTitulo" name="titulo" required maxlength="255"
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm"
                                        placeholder="Ej. Preparar el primer diseño">
                                </div>

                                <div>
                                    <label for="nuevaSubtareaDescripcion" class="block text-xs font-semibold text-slate-600 mb-1">Descripción (opcional)</label>
                                    <textarea id="nuevaSubtareaDescripcion" name="descripcion" rows="2" maxlength="5000"
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm"
                                        placeholder="Indicaciones para esta subtarea"></textarea>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label for="nuevaSubtareaResponsable" class="block text-xs font-semibold text-slate-600 mb-1">Responsable *</label>
                                        <select id="nuevaSubtareaResponsable" name="user_id" required
                                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
                                            <option value="">Selecciona una persona</option>
                                            @foreach($disenadores as $disenador)
                                            <option value="{{ $disenador->id }}">{{ $disenador->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="nuevaSubtareaHoras" class="block text-xs font-semibold text-slate-600 mb-1">Tiempo estimado (horas) *</label>
                                        <input id="nuevaSubtareaHoras" name="tiempo_estimado" type="number"
                                            min="0.25" max="10000" step="0.25" value="1" required
                                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
                                    </div>
                                    <div>
                                        <label for="nuevaSubtareaAvance" class="block text-xs font-semibold text-slate-600 mb-1">Avance inicial (%)</label>
                                        <input id="nuevaSubtareaAvance" name="avance" type="number"
                                            min="0" max="100" step="1" value="0"
                                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button id="btnCrearSubtarea" type="submit"
                                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 hover:bg-blue-700 px-4 py-2 text-sm font-semibold text-white transition">
                                        <i class="fa-solid fa-plus"></i> Añadir subtarea
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>

                    <!-- ========================================= -->
                    <!-- ========================================= -->
                    <!-- PESTAÑAS: ARCHIVOS, COMENTARIOS E HISTORIAL -->
                    <!-- ========================================= -->
                    <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-200 bg-white">
                            <div class="flex flex-wrap gap-1" role="tablist" aria-label="Contenido de la actividad">
                                <button type="button" id="tabActividadArchivos" role="tab"
                                    aria-controls="panelActividadArchivos" aria-selected="true"
                                    onclick="cambiarPestanaActividad('archivos')"
                                    class="tab-actividad px-4 py-2 rounded-lg border border-blue-200 bg-blue-50 text-blue-700 text-sm font-semibold">
                                    <i class="fa-solid fa-paperclip mr-2"></i>Archivos
                                    <span id="contadorArchivos" class="ml-1 rounded-md bg-white px-2 py-0.5 text-xs">0</span>
                                </button>
                                <button type="button" id="tabActividadComentarios" role="tab"
                                    aria-controls="panelActividadComentarios" aria-selected="false"
                                    onclick="cambiarPestanaActividad('comentarios')"
                                    class="tab-actividad px-4 py-2 rounded-lg border border-transparent text-slate-600 hover:bg-slate-50 text-sm font-semibold">
                                    <i class="fa-solid fa-comments mr-2"></i>Comentarios
                                    <span id="contadorComentarios" class="ml-1 rounded-md bg-slate-100 px-2 py-0.5 text-xs">0</span>
                                </button>
                                <button type="button" id="tabActividadHistorial" role="tab"
                                    aria-controls="panelActividadHistorial" aria-selected="false"
                                    onclick="cambiarPestanaActividad('historial')"
                                    class="tab-actividad px-4 py-2 rounded-lg border border-transparent text-slate-600 hover:bg-slate-50 text-sm font-semibold">
                                    <i class="fa-solid fa-clock-rotate-left mr-2"></i>Historial
                                    <span id="contadorPausas" class="ml-1 rounded-md bg-slate-100 px-2 py-0.5 text-xs">0</span>
                                </button>
                            </div>
                        </div>

                        <div id="panelActividadArchivos" role="tabpanel" aria-labelledby="tabActividadArchivos" class="p-4 sm:p-5">
                        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                            <div class="px-5 py-4 border-b border-slate-100">

                                <div class="flex items-center justify-between gap-3">

                                    <div class="flex items-center gap-3">

                                        <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600
                                                    flex items-center justify-center">
                                            <i class="fa-solid fa-paperclip"></i>
                                        </div>

                                        <div>
                                            <h3 class="font-bold text-slate-800">
                                                Archivos
                                            </h3>

                                            <p class="text-xs text-slate-500">
                                                Documentos relacionados
                                            </p>
                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="p-5">

                                <div
                                    id="archivosContainer"
                                    class="space-y-2 max-h-52 overflow-y-auto pr-1 mb-4">

                                </div>

                                <form
                                    id="archivoForm"
                                    method="POST"
                                    enctype="multipart/form-data"
                                    class="space-y-3">

                                    @csrf

                                    <label
                                        class="flex items-center gap-3 w-full p-3 rounded-xl
                                               border border-dashed border-slate-300
                                               bg-slate-50 hover:bg-slate-100
                                               cursor-pointer transition">

                                        <div class="w-9 h-9 rounded-lg bg-white border border-slate-200
                                                    flex items-center justify-center text-slate-500">
                                            <i class="fa-solid fa-upload"></i>
                                        </div>

                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-slate-700">
                                                Seleccionar archivo
                                            </p>

                                            <p class="text-xs text-slate-400 truncate">
                                                Agrega un documento a la actividad
                                            </p>
                                        </div>

                                        <input
                                            type="file"
                                            name="archivo"
                                            class="hidden">
                                    </label>

                                    <button
                                        type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2
                                               bg-indigo-600 hover:bg-indigo-700
                                               text-white px-4 py-2.5 rounded-xl
                                               font-semibold text-sm transition">

                                        <i class="fa-solid fa-cloud-arrow-up"></i>
                                        Agregar archivo

                                    </button>

                                </form>

                            </div>

                        </section>
                        </div>

                        <div id="panelActividadComentarios" role="tabpanel" aria-labelledby="tabActividadComentarios" class="hidden p-4 sm:p-5">
                        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                            <div class="px-5 py-4 border-b border-slate-100">

                                <div class="flex items-center justify-between gap-3">

                                    <div class="flex items-center gap-3">

                                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600
                                                    flex items-center justify-center">
                                            <i class="fa-solid fa-comments"></i>
                                        </div>

                                        <div>
                                            <h3 class="font-bold text-slate-800">
                                                Comentarios
                                            </h3>

                                            <p class="text-xs text-slate-500">
                                                Comunicación de la actividad
                                            </p>
                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="p-5">

                                <div
                                    id="comentariosContainer"
                                    class="space-y-3 max-h-52 overflow-y-auto pr-1 mb-4">

                                </div>

                                <form
                                    id="comentarioForm"
                                    method="POST"
                                    enctype="multipart/form-data"
                                    class="space-y-3">

                                    @csrf

                                    <textarea
                                        name="comentario"
                                        rows="3"
                                        class="w-full border border-slate-200 rounded-xl p-3
                                               text-sm bg-slate-50
                                               focus:bg-white focus:border-blue-400
                                               focus:ring-2 focus:ring-blue-100
                                               resize-none"
                                        placeholder="Escribe un comentario..."
                                        required></textarea>

                                    <div class="flex flex-col sm:flex-row gap-2">

                                        <label
                                            class="flex-1 flex items-center gap-2 px-3 py-2.5
                                                   rounded-xl border border-slate-200
                                                   bg-slate-50 hover:bg-slate-100
                                                   cursor-pointer text-sm text-slate-600">

                                            <i class="fa-solid fa-paperclip text-slate-400"></i>

                                            <span class="truncate">
                                                Adjuntar archivos
                                            </span>

                                            <input
                                                type="file"
                                                name="archivos[]"
                                                multiple
                                                class="hidden">

                                        </label>

                                        <button
                                            type="submit"
                                            class="inline-flex items-center justify-center gap-2
                                                   bg-blue-600 hover:bg-blue-700
                                                   text-white px-5 py-2.5 rounded-xl
                                                   font-semibold text-sm transition">

                                            <i class="fa-solid fa-paper-plane"></i>
                                            Enviar

                                        </button>

                                    </div>

                                </form>

                            </div>

                        </section>
                        </div>

                        <div id="panelActividadHistorial" role="tabpanel" aria-labelledby="tabActividadHistorial" class="hidden p-4 sm:p-5">
                    <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-5 sm:px-6 py-4 border-b border-slate-100">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                        <i class="fa-solid fa-mug-hot"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-800">Historial de pausas</h3>
                                        <p class="text-xs text-slate-500">Motivo, responsable y fecha de cada pausa</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-5 sm:p-6">
                            <div id="historialPausasContainer" class="space-y-3">
                                <div class="estado-vacio-actividad">
                                    <div class="estado-vacio-icono"><i class="fa-solid fa-mug-hot"></i></div>
                                    <strong>Sin pausas registradas</strong>
                                    <p>Cuando pauses esta actividad, aquí aparecerán el motivo, la persona responsable y la fecha.</p>
                                </div>
                            </div>
                        </div>
                    </section>
                        </div>
                    </section>

                    <script>
                        function cambiarPestanaActividad(pestana) {
                            const nombres = ['archivos', 'comentarios', 'historial'];
                            nombres.forEach(nombre => {
                                const panel = document.getElementById('panelActividad' + nombre.charAt(0).toUpperCase() + nombre.slice(1));
                                const boton = document.getElementById('tabActividad' + nombre.charAt(0).toUpperCase() + nombre.slice(1));
                                if (!panel || !boton) return;
                                const activo = nombre === pestana;
                                panel.classList.toggle('hidden', !activo);
                                boton.setAttribute('aria-selected', activo ? 'true' : 'false');
                                boton.classList.toggle('bg-blue-50', activo);
                                boton.classList.toggle('text-blue-700', activo);
                                boton.classList.toggle('border-blue-200', activo);
                                boton.classList.toggle('text-slate-600', !activo);
                                boton.classList.toggle('border-transparent', !activo);
                            });
                        }
                        window.cambiarPestanaActividad = cambiarPestanaActividad;
                    </script>
                        </div> <!-- /columna izquierda -->

                        <div class="min-w-0 space-y-4">

                    <!-- ========================================= -->
                    <!-- INFORMACIÓN GENERAL -->
                    <!-- ========================================= -->

                    <section class="bg-white rounded-2xl border border-slate-200 shadow-sm">

                        <div class="px-5 sm:px-6 py-4 border-b border-slate-100">

                            <div class="flex items-center gap-3">

                                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600
                                            flex items-center justify-center">
                                    <i class="fa-solid fa-circle-info"></i>
                                </div>

                                <div>
                                    <h3 class="font-bold text-slate-800">
                                        Información general
                                    </h3>

                                    <p class="text-xs text-slate-500">
                                        Datos principales de la actividad
                                    </p>
                                </div>

                            </div>

                        </div>

                        <div class="p-5 sm:p-6">

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-3">

                                <div class="rounded-xl bg-slate-50 border border-slate-100 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">
                                        Diseñador
                                    </p>

                                    <p id="detalleDisenador"
                                        class="font-semibold text-slate-800 truncate">
                                    </p>
                                </div>

                                <div class="rounded-xl bg-slate-50 border border-slate-100 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">
                                        Cliente
                                    </p>

                                    <p id="detalleCliente"
                                        class="font-semibold text-slate-800 truncate">
                                        -
                                    </p>
                                </div>

                                <div class="rounded-xl bg-slate-50 border border-slate-100 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">
                                        Creación
                                    </p>

                                    <p id="detalleCreacion"
                                        class="font-semibold text-slate-800">
                                    </p>
                                </div>

                                <div class="rounded-xl bg-slate-50 border border-slate-100 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">
                                        Inicio
                                    </p>

                                    <p id="detalleInicio"
                                        class="font-semibold text-slate-800">
                                    </p>
                                </div>

                                <div class="rounded-xl bg-slate-50 border border-slate-100 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">
                                        Fecha de entrega
                                    </p>

                                    <p id="detalleEntrega"
                                        class="font-semibold text-slate-800">
                                    </p>
                                </div>

                            </div>

                        </div>

                    </section>



                    <!-- ========================================= -->
                    <!-- ADMINISTRACIÓN -->
                    <!-- ========================================= -->

                    @role('Administrador|Supervisor')

                    <section class="bg-white rounded-2xl border border-slate-200 shadow-sm">

                        <div class="px-5 sm:px-6 py-4 border-b border-slate-100">

                            <div class="flex items-center gap-3">

                                <div class="w-9 h-9 rounded-xl bg-orange-50 text-orange-600
                                            flex items-center justify-center">
                                    <i class="fa-solid fa-user-gear"></i>
                                </div>

                                <div>
                                    <h3 class="font-bold text-slate-800">
                                        Administración
                                    </h3>

                                    <p class="text-xs text-slate-500">
                                        Gestión del responsable de la actividad
                                    </p>
                                </div>

                            </div>

                        </div>

                        <div class="p-5 sm:p-6">

                            <form
                                id="reasignarForm"
                                method="POST"
                                class="flex flex-col sm:flex-row gap-3">

                                @csrf

                                <select
                                    name="user_id"
                                    class="flex-1 border border-slate-200 rounded-xl px-4 py-3
                                           bg-slate-50 text-slate-700
                                           focus:bg-white focus:border-orange-400
                                           focus:ring-2 focus:ring-orange-100">

                                    @foreach($disenadores as $disenador)

                                    <option value="{{ $disenador->id }}">
                                        {{ $disenador->name }}
                                    </option>

                                    @endforeach

                                </select>

                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center gap-2
                                           bg-orange-500 hover:bg-orange-600
                                           text-white px-5 py-3 rounded-xl
                                           font-semibold transition">

                                    <i class="fa-solid fa-user-check"></i>
                                    Reasignar diseñador

                                </button>

                            </form>

                        </div>

                    </section>

                    @endrole

                        </div> <!-- /columna derecha -->
                    </div> <!-- /grid principal -->

                </div>

            </div>


            <!-- ================================================= -->
            <!-- PIE / ACCIONES -->
            <!-- ================================================= -->

            <div class="border-t border-slate-200 bg-white px-5 sm:px-7 py-4">

                <div class="flex flex-col sm:flex-row sm:items-center
                            justify-between gap-3">

                    <div class="flex flex-wrap items-center gap-2">

                        <!-- ELIMINAR: solo actividad pendiente -->

                        <button
                            id="btnEliminarActividad"
                            type="button"
                            onclick="eliminarActividad()"
                            class="hidden inline-flex items-center justify-center gap-2
                                   px-4 py-2.5 rounded-xl
                                   border border-slate-200 bg-white
                                   text-slate-600 hover:bg-slate-50 hover:text-red-600
                                   hover:border-red-200 font-semibold text-sm transition">

                            <i class="fa-solid fa-trash"></i>
                            Eliminar

                        </button>

                        <!-- CANCELAR: actividad ya iniciada -->

                        <button
                            id="btnCancelarActividad"
                            type="button"
                            onclick="abrirModalCancelacion()"
                            class="hidden inline-flex items-center justify-center gap-2
                                   px-4 py-2.5 rounded-xl
                                   border border-red-200 bg-red-50
                                   text-red-600 hover:bg-red-100
                                   font-semibold text-sm transition">

                            <i class="fa-solid fa-ban"></i>
                            Cancelar

                        </button>

                    </div>

                    <div class="flex items-center justify-end gap-2">

                        <button
                            type="button"
                            onclick="cerrarModal()"
                            class="px-5 py-2.5 rounded-xl
                                   bg-slate-100 hover:bg-slate-200
                                   text-slate-700 font-semibold text-sm transition">

                            Cerrar

                        </button>

                        <button
                            id="guardarActividad"
                            type="button"
                            class="inline-flex items-center justify-center gap-2
                                   px-5 py-2.5 rounded-xl
                                   bg-emerald-600 hover:bg-emerald-700
                                   text-white font-semibold text-sm transition">

                            <i class="fa-solid fa-floppy-disk"></i>
                            Guardar cambios

                        </button>

                    </div>

                </div>

            </div>

        </div>
    </div>

</div>


<!-- ========================================================= -->
<!-- MODAL CANCELAR ACTIVIDAD -->
<!-- ========================================================= -->

<div id="modalCancelacion"
    class="hidden fixed inset-0 z-[60] bg-slate-950/70 backdrop-blur-sm">

    <div class="flex items-center justify-center min-h-screen p-4">

        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden">

            <div class="bg-red-600 px-6 py-5 text-white">

                <div class="flex items-center gap-3">

                    <div class="w-11 h-11 rounded-xl bg-white/15
                                flex items-center justify-center">

                        <i class="fa-solid fa-ban text-lg"></i>

                    </div>

                    <div>
                        <h3 class="text-lg font-bold">
                            Cancelar actividad
                        </h3>

                        <p class="text-sm text-red-100">
                            Esta acción detendrá la actividad.
                        </p>
                    </div>

                </div>

            </div>

            <div class="p-6">

                <p class="text-sm text-slate-600 mb-4">
                    Indica el motivo por el que ya no se continuará con esta actividad.
                </p>

                <label
                    for="motivoCancelacion"
                    class="block text-sm font-semibold text-slate-700 mb-2">

                    Motivo de cancelación
                    <span class="text-red-500">*</span>

                </label>

                <textarea
                    id="motivoCancelacion"
                    rows="4"
                    maxlength="1000"
                    class="w-full border border-slate-200 rounded-xl p-3
                           text-sm text-slate-700 bg-slate-50
                           focus:bg-white focus:border-red-400
                           focus:ring-2 focus:ring-red-100 resize-none"
                    placeholder="Escribe el motivo de cancelación..."></textarea>

                <p class="text-xs text-slate-400 mt-2">
                    El motivo es obligatorio.
                </p>

            </div>

            <div class="px-6 py-4 border-t border-slate-100
                        flex justify-end gap-2 bg-slate-50">

                <button
                    type="button"
                    onclick="cerrarModalCancelacion()"
                    class="px-4 py-2.5 rounded-xl bg-white
                           border border-slate-200 hover:bg-slate-100
                           text-slate-700 font-semibold text-sm">

                    Volver

                </button>

                <button
                    type="button"
                    onclick="confirmarCancelacion()"
                    class="inline-flex items-center gap-2
                           px-5 py-2.5 rounded-xl
                           bg-red-600 hover:bg-red-700
                           text-white font-semibold text-sm">

                    <i class="fa-solid fa-ban"></i>
                    Cancelar actividad

                </button>

            </div>

        </div>

    </div>

</div>


<!-- ========================================================= -->
<!-- MODAL ELIMINAR ACTIVIDAD -->
<!-- ========================================================= -->

<div id="modalEliminarActividad"
    class="hidden fixed inset-0 z-[60] bg-slate-950/70 backdrop-blur-sm">

    <div class="flex items-center justify-center min-h-screen p-4">

        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-100">

                <div class="flex items-center gap-3">

                    <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600
                                flex items-center justify-center">

                        <i class="fa-solid fa-trash"></i>

                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-slate-800">
                            Eliminar actividad
                        </h3>

                        <p class="text-sm text-slate-500">
                            Confirma que deseas eliminar esta actividad.
                        </p>
                    </div>

                </div>

            </div>

            <div class="p-6">

                <p class="text-sm text-slate-600">
                    La actividad se eliminará del tablero.
                </p>

            </div>

            <div class="px-6 py-4 border-t border-slate-100
                        flex justify-end gap-2 bg-slate-50">

                <button
                    type="button"
                    onclick="cerrarModalEliminar()"
                    class="px-4 py-2.5 rounded-xl bg-white
                           border border-slate-200 hover:bg-slate-100
                           text-slate-700 font-semibold text-sm">

                    Volver

                </button>

                <button
                    type="button"
                    onclick="confirmarEliminarActividad()"
                    class="inline-flex items-center gap-2
                           px-5 py-2.5 rounded-xl
                           bg-slate-700 hover:bg-slate-800
                           text-white font-semibold text-sm">

                    <i class="fa-solid fa-trash"></i>
                    Eliminar actividad

                </button>

            </div>

        </div>

    </div>

</div>


<!-- MODAL PERSONALIZADO PARA PAUSAR ACTIVIDAD -->
<div id="modalPausaActividad" class="hidden fixed inset-0 z-[80] bg-slate-950/75 backdrop-blur-sm"
     role="dialog" aria-modal="true" aria-labelledby="tituloModalPausa" aria-hidden="true"
     onclick="if (event.target === this) cerrarModalPausa()">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="w-full max-w-lg overflow-hidden rounded-2xl border border-slate-700 bg-slate-900 shadow-2xl">
            <div class="flex items-start gap-4 border-b border-slate-700 px-6 py-5">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-500/15 text-amber-400">
                    <i class="fa-solid fa-mug-hot text-xl"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h2 id="tituloModalPausa" class="text-lg font-bold text-white">Pausar actividad</h2>
                    <p class="mt-1 text-sm text-slate-400">La actividad dejará de contabilizar tiempo trabajado.</p>
                </div>
                <button type="button" onclick="cerrarModalPausa()" aria-label="Cerrar modal"
                        class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-800 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="space-y-3 px-6 py-5">
                <label for="motivoPausaActividad" class="block text-sm font-semibold text-slate-200">
                    ¿Por qué necesitas pausar esta actividad? <span class="text-amber-400">*</span>
                </label>
                <textarea id="motivoPausaActividad" rows="4" maxlength="1000"
                          class="w-full resize-y rounded-xl border border-slate-600 bg-slate-800 px-4 py-3 text-sm text-white placeholder-slate-500 outline-none transition focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20"
                          placeholder="Ej. Hora de comida, reunión, falta de información..."
                          onkeydown="if (event.key === 'Escape') cerrarModalPausa(); if (event.key === 'Enter' && (event.ctrlKey || event.metaKey)) confirmarPausaActividad()"></textarea>
                <p id="errorMotivoPausa" class="hidden text-sm text-red-400" role="alert"></p>
                <p class="text-xs text-slate-400">El motivo quedará guardado en el historial con tu usuario y la fecha y hora.</p>
            </div>
            <div class="flex flex-col-reverse gap-2 border-t border-slate-700 bg-slate-950/40 px-6 py-4 sm:flex-row sm:justify-end">
                <button type="button" onclick="cerrarModalPausa()"
                        class="rounded-xl border border-slate-600 px-4 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">
                    Volver
                </button>
                <button id="btnConfirmarPausa" type="button" onclick="confirmarPausaActividad()"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-amber-400">
                    <i class="fa-solid fa-pause"></i> Confirmar pausa
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    /*
     * ============================================================
     * CONTADORES DE ARCHIVOS Y COMENTARIOS
     * ============================================================
     *
     * Esta función recibe la actividad que abre el modal y actualiza
     * los dos contadores con la cantidad real de elementos.
     *
     * Desde dashboard.blade.php se puede llamar con:
     *
     * actualizarContadoresActividad(actividad);
     *
     * Se admite tanto un arreglo como una colección serializada
     * que llegue como objeto con la propiedad data.
     */

    function actualizarContadoresActividad(actividad) {

        const archivos = Array.isArray(actividad?.archivos) ?
            actividad.archivos :
            Array.isArray(actividad?.archivos?.data) ?
            actividad.archivos.data : [];

        const comentarios = Array.isArray(actividad?.comentarios) ?
            actividad.comentarios :
            Array.isArray(actividad?.comentarios?.data) ?
            actividad.comentarios.data : [];

        const contadorArchivos =
            document.getElementById('contadorArchivos');

        const contadorComentarios =
            document.getElementById('contadorComentarios');

        console.log('CONTADORES ACTIVIDAD', {
            actividad_id: actividad?.id,
            archivos: archivos.length,
            comentarios: comentarios.length
        });

        if (contadorArchivos) contadorArchivos.textContent = String(archivos.length);
        if (contadorComentarios) contadorComentarios.textContent = String(comentarios.length);

        // Los contadores están únicamente en las pestañas del encabezado.
    }


    /*
     * ============================================================
     * SUBTAREAS: creación, actualización y eliminación vía AJAX
     * ============================================================
     */

    let actividadSubtareasActual = null;

    function obtenerListaSubtareas(valor) {
        if (Array.isArray(valor)) return valor;
        if (Array.isArray(valor?.data)) return valor.data;
        return [];
    }

    function mostrarMensajeSubtareas(texto, error = false) {
        const elemento = document.getElementById('mensajeSubtareas');
        if (!elemento) return;

        elemento.textContent = texto;
        elemento.classList.remove('hidden', 'bg-red-50', 'text-red-700', 'bg-green-50', 'text-green-700');
        elemento.classList.add(error ? 'bg-red-50' : 'bg-green-50');
        elemento.classList.add(error ? 'text-red-700' : 'text-green-700');
    }

    function csrfSubtareas() {
        return document.querySelector('#crearSubtareaForm input[name="_token"]')?.value ||
            document.querySelector('meta[name="csrf-token"]')?.content ||
            '';
    }

    function actualizarResumenSubtareas(subtareas, avanceServidor = null, actividad = window.actividadDetalleActual) {
        const lista = obtenerListaSubtareas(subtareas);
        const horas = lista.reduce((total, item) => total + Number(item.tiempo_estimado || 0), 0);

        // Con subtareas se calcula el avance ponderado.
        // Sin subtareas se utiliza el tiempo activo de la actividad frente al estimado.
        let calculado = 0;
        if (lista.length > 0) {
            calculado = horas > 0 ?
                lista.reduce((total, item) => total + Number(item.tiempo_estimado || 0) * Number(item.avance || 0), 0) / horas :
                0;
        } else {
            const minutosTrabajados = Number(actividad?.tiempo_activo ?? actividad?.tiempo_total ?? 0);
            const minutosEstimados = Number(actividad?.tiempo_estimado ?? 0) * 60;
            calculado = minutosEstimados > 0 ?
                (minutosTrabajados / minutosEstimados) * 100 :
                0;
        }

        const avance = avanceServidor === null || avanceServidor === undefined ?
            calculado :
            Number(avanceServidor || 0);

        const texto = document.getElementById('avanceSubtareasTexto');
        const barra = document.getElementById('avanceSubtareasBarra');
        const contador = document.getElementById('contadorSubtareas');
        const resumen = document.getElementById('resumenSubtareas');

        if (texto) texto.textContent = `${avance.toFixed(2).replace(/\.00$/, '')}%`;
        if (barra) barra.style.width = `${Math.max(0, Math.min(100, avance))}%`;
        if (contador) contador.textContent = `${lista.length} ${lista.length === 1 ? 'subtarea' : 'subtareas'}`;

        const terminadas = lista.filter(item => Number(item.avance) >= 100).length;
        const etiquetaAvance = document.querySelector('#avanceSubtareasTexto')?.closest('.rounded-xl')?.querySelector('p');
        const descripcionAvance = document.querySelector('#avanceSubtareasTexto')?.closest('.rounded-xl')?.querySelectorAll('p')[1];

        if (etiquetaAvance) {
            etiquetaAvance.textContent = lista.length ?
                'Avance ponderado de la actividad' :
                'Avance por tiempo de la actividad';
        }

        if (descripcionAvance) {
            descripcionAvance.textContent = lista.length ?
                'Calculado según las horas estimadas y el avance de cada subtarea.' :
                'Calculado con el tiempo trabajado respecto al tiempo estimado.';
        }

        if (resumen) {
            if (lista.length) {
                resumen.textContent = `${terminadas} de ${lista.length} subtareas terminadas · ${horas.toFixed(2).replace(/\.00$/, '')} horas estimadas`;
            } else {
                const minutosTrabajados = Number(actividad?.tiempo_activo ?? actividad?.tiempo_total ?? 0);
                const horasTrabajadas = minutosTrabajados / 60;
                const horasEstimadasActividad = Number(actividad?.tiempo_estimado ?? 0);
                const exceso = Math.max(0, horasTrabajadas - horasEstimadasActividad);
                resumen.textContent = horasEstimadasActividad > 0 ?
                    `${horasTrabajadas.toFixed(2)} de ${horasEstimadasActividad.toFixed(2)} horas trabajadas${exceso > 0 ? ` · Excedido: ${exceso.toFixed(2)} h` : ''}.` :
                    'Define un tiempo estimado para calcular el avance por tiempo.';
            }
        }
    }

    async function cargarSubtareasGuardadas(actividad) {
        if (!actividad || !actividad.id || actividad.__subtareasCargando || actividad.__subtareasCargadas) {
            return;
        }

        actividad.__subtareasCargando = true;

        try {
            const respuesta = await fetch(`/actividad/${actividad.id}/subtareas`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                cache: 'no-store'
            });

            let resultado = {};
            try {
                resultado = await respuesta.json();
            } catch (_) {
                throw new Error('El servidor no devolvió una respuesta válida al consultar las subtareas.');
            }

            if (!respuesta.ok) {
                throw new Error(resultado.message || `No se pudieron consultar las subtareas (HTTP ${respuesta.status}).`);
            }

            actividad.subtareas = obtenerListaSubtareas(resultado.subtareas);
            actividad.avance_subtareas = resultado.avance_subtareas ?? actividad.avance_subtareas ?? 0;
            actividad.__subtareasCargadas = true;
            window.actividadDetalleActual = actividad;

            // Actualiza la lista con los datos persistidos, sin recargar toda la página.
            renderizarSubtareas(actividad);
        } catch (error) {
            console.error('Error al cargar subtareas guardadas:', error);
            mostrarMensajeSubtareas(
                error.message || 'No se pudieron cargar las subtareas guardadas.',
                true
            );
        } finally {
            actividad.__subtareasCargando = false;
        }
    }

    function renderizarSubtareas(actividad) {
        if (!actividad) return;

        window.actividadDetalleActual = actividad;
        actividadSubtareasActual = actividad.id ?? null;

        const modalDetalle = document.getElementById('detalleModal');
        if (modalDetalle && actividadSubtareasActual) {
            modalDetalle.dataset.actividadId = String(actividadSubtareasActual);
        }

        const lista = obtenerListaSubtareas(actividad.subtareas);
        const contenedor = document.getElementById('subtareasContainer');
        const formulario = document.getElementById('crearSubtareaForm');

        actualizarResumenSubtareas(lista, actividad.avance_subtareas, actividad);

        if (formulario && actividadSubtareasActual) {
            // Guardar siempre el ID y la ruta de la actividad abierta.
            const idActual = String(actividadSubtareasActual);
            formulario.dataset.actividadId = idActual;
            formulario.dataset.url = `/actividad/${idActual}/subtareas`;
            formulario.method = 'POST';
            formulario.action = new URL(formulario.dataset.url, window.location.origin).href;
            if (modalDetalle) modalDetalle.dataset.actividadId = idActual;
        }

        // Al abrir el modal, consultar siempre los datos persistidos una vez
        // por objeto de actividad. Esto permite recuperar subtareas tras recargar.
        cargarSubtareasGuardadas(actividad);

        if (!contenedor) return;
        contenedor.innerHTML = '';

        if (!lista.length) {
            const vacio = document.createElement('div');
            vacio.className = 'rounded-xl border border-slate-200 bg-white p-4 text-sm text-slate-500 text-center';
            vacio.textContent = 'Esta actividad todavía no tiene subtareas.';
            contenedor.appendChild(vacio);
            return;
        }

        lista.forEach(subtarea => {
            const tarjeta = document.createElement('article');
            tarjeta.className = 'rounded-xl border border-slate-200 bg-white p-4 space-y-3';

            const encabezado = document.createElement('div');
            encabezado.className = 'flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2';

            const datos = document.createElement('div');
            datos.className = 'min-w-0 flex-1';

            const titulo = document.createElement('h4');
            titulo.className = 'font-semibold text-slate-800 break-words';
            titulo.textContent = subtarea.titulo || 'Subtarea';

            const descripcion = document.createElement('p');
            descripcion.className = 'text-xs text-slate-500 mt-1 whitespace-pre-wrap';
            descripcion.textContent = subtarea.descripcion || '';

            const responsable = document.createElement('p');
            responsable.className = 'text-xs text-slate-500 mt-2';
            responsable.textContent = `Responsable: ${subtarea.user?.name || 'Usuario #' + subtarea.user_id} · ${Number(subtarea.tiempo_estimado || 0)} h estimadas`;

            datos.append(titulo, descripcion, responsable);
            encabezado.appendChild(datos);

            const eliminar = document.createElement('button');
            eliminar.type = 'button';
            eliminar.className = 'self-start rounded-lg px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50';
            eliminar.textContent = 'Eliminar';
            eliminar.addEventListener('click', async () => {
                if (!confirm(`¿Eliminar la subtarea "${subtarea.titulo}"?`)) return;
                eliminar.disabled = true;

                try {
                    const respuesta = await fetch(`/actividad/${actividadSubtareasActual}/subtareas/${subtarea.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfSubtareas(),
                            'Accept': 'application/json'
                        }
                    });
                    const resultado = await respuesta.json();
                    if (!respuesta.ok) throw new Error(resultado.message || 'No se pudo eliminar la subtarea.');

                    actividad.subtareas = resultado.subtareas || [];
                    actividad.avance_subtareas = resultado.avance_subtareas;
                    actividad.__subtareasCargadas = true;
                    renderizarSubtareas(actividad);
                    mostrarMensajeSubtareas(resultado.message || 'Subtarea eliminada.');
                } catch (error) {
                    mostrarMensajeSubtareas(error.message || 'Error al eliminar la subtarea.', true);
                    eliminar.disabled = false;
                }
            });
            encabezado.appendChild(eliminar);

            const controles = document.createElement('div');
            controles.className = 'grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-3 items-end';

            const campo = document.createElement('div');
            const etiqueta = document.createElement('label');
            etiqueta.className = 'block text-xs font-semibold text-slate-600 mb-1';
            etiqueta.textContent = 'Avance individual (%)';

            const fila = document.createElement('div');
            fila.className = 'flex items-center gap-3';

            const entrada = document.createElement('input');
            entrada.type = 'number';
            entrada.min = '0';
            entrada.max = '100';
            entrada.step = '1';
            entrada.value = Number(subtarea.avance || 0);
            entrada.className = 'w-24 rounded-lg border border-slate-300 px-3 py-2 text-sm';
            entrada.setAttribute('aria-label', `Avance de ${subtarea.titulo}`);

            const fondo = document.createElement('div');
            fondo.className = 'flex-1 h-2 rounded-full bg-slate-100 overflow-hidden';
            const barra = document.createElement('div');
            barra.className = 'h-full rounded-full bg-blue-600';
            barra.style.width = `${Math.max(0, Math.min(100, Number(subtarea.avance || 0)))}%`;
            fondo.appendChild(barra);
            fila.append(entrada, fondo);
            campo.append(etiqueta, fila);

            const guardar = document.createElement('button');
            guardar.type = 'button';
            guardar.className = 'rounded-lg bg-blue-600 hover:bg-blue-700 px-4 py-2 text-sm font-semibold text-white';
            guardar.textContent = 'Guardar avance';
            guardar.addEventListener('click', async () => {
                const avance = Number(entrada.value);
                if (!Number.isInteger(avance) || avance < 0 || avance > 100) {
                    mostrarMensajeSubtareas('El avance debe ser un número entero entre 0 y 100.', true);
                    entrada.focus();
                    return;
                }

                guardar.disabled = true;
                guardar.textContent = 'Guardando...';

                try {
                    const respuesta = await fetch(`/actividad/${actividadSubtareasActual}/subtareas/${subtarea.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfSubtareas(),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            avance
                        })
                    });
                    const resultado = await respuesta.json();
                    if (!respuesta.ok) {
                        const errores = resultado.errors ? Object.values(resultado.errors).flat().join(' ') : '';
                        throw new Error(errores || resultado.message || 'No se pudo guardar el avance.');
                    }

                    actividad.subtareas = resultado.subtareas || [];
                    actividad.avance_subtareas = resultado.avance_subtareas;
                    actividad.__subtareasCargadas = true;
                    renderizarSubtareas(actividad);
                    mostrarMensajeSubtareas(resultado.message || 'Avance actualizado.');
                } catch (error) {
                    mostrarMensajeSubtareas(error.message || 'Error al actualizar el avance.', true);
                    guardar.disabled = false;
                    guardar.textContent = 'Guardar avance';
                }
            });

            controles.append(campo, guardar);
            tarjeta.append(encabezado, controles);
            contenedor.appendChild(tarjeta);
        });
    }

    (function inicializarFormularioSubtareas() {
        const inicializar = () => {
            const formulario = document.getElementById('crearSubtareaForm');
            if (!formulario || formulario.dataset.listenerSubtareas === '1') return;
            formulario.dataset.listenerSubtareas = '1';

            formulario.addEventListener('submit', async function(event) {
                event.preventDefault();

                if (!formulario.reportValidity()) return;

                // Resolver el ID desde la actividad que está abierta en el modal.
                // Priorizar el ID guardado en el propio formulario/modal.
                // Así no dependemos exclusivamente de variables JS compartidas entre scripts.
                const modalDetalle = document.getElementById('detalleModal');
                // Usar el primer ID válido y no una cadena vacía de dataset.
                const posiblesIds = [
                    modalDetalle?.dataset?.actividadId,
                    formulario.dataset.actividadId,
                    window.actividadDetalleActual?.id,
                    window.actividadSeleccionadaId,
                    actividadSubtareasActual
                ];
                const actividadActualId = posiblesIds.find(valor =>
                    valor !== null && valor !== undefined &&
                    String(valor).trim() !== '' &&
                    Number.isSafeInteger(Number(valor)) && Number(valor) > 0
                );

                if (actividadActualId === undefined) {
                    mostrarMensajeSubtareas(
                        'No se pudo identificar la actividad abierta. Vuelve a abrir el detalle desde el tablero.',
                        true
                    );
                    return;
                }

                actividadSubtareasActual = Number(actividadActualId);
                formulario.dataset.actividadId = String(actividadActualId);
                formulario.dataset.url = `/actividad/${actividadActualId}/subtareas`;
                formulario.method = 'POST';
                formulario.action = new URL(formulario.dataset.url, window.location.origin).href;

                if (modalDetalle) {
                    modalDetalle.dataset.actividadId = String(actividadActualId);
                }

                const boton = document.getElementById('btnCrearSubtarea');
                if (boton) {
                    boton.disabled = true;
                    boton.textContent = 'Guardando...';
                }

                try {
                    const respuesta = await fetch(formulario.dataset.url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfSubtareas(),
                            'Accept': 'application/json'
                        },
                        body: new FormData(formulario)
                    });

                    let resultado = {};
                    try {
                        resultado = await respuesta.json();
                    } catch (_) {
                        throw new Error('El servidor devolvió una respuesta no válida.');
                    }

                    if (!respuesta.ok) {
                        const errores = resultado.errors
                            ? Object.values(resultado.errors).flat().join(' ')
                            : '';
                        throw new Error(errores || resultado.message || 'No se pudo crear la subtarea.');
                    }

                    // Mantener la actividad actualizada en memoria para el siguiente cambio.
                    const actividad = window.actividadDetalleActual || { id: actividadActualId };
                    actividad.id = actividadActualId;
                    actividad.subtareas = resultado.subtareas || [];
                    actividad.avance_subtareas = resultado.avance_subtareas;
                    actividad.__subtareasCargadas = true;
                    window.actividadDetalleActual = actividad;

                    renderizarSubtareas(actividad);

                    formulario.reset();
                    const horas = document.getElementById('nuevaSubtareaHoras');
                    const avance = document.getElementById('nuevaSubtareaAvance');
                    if (horas) horas.value = '1';
                    if (avance) avance.value = '0';

                    mostrarMensajeSubtareas(resultado.message || 'Subtarea creada correctamente.');
                } catch (error) {
                    mostrarMensajeSubtareas(error.message || 'Error al crear la subtarea.', true);
                } finally {
                    if (boton) {
                        boton.disabled = false;
                        boton.innerHTML = '<i class="fa-solid fa-plus"></i> Añadir subtarea';
                    }
                }
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', inicializar, {
                once: true
            });
        } else {
            inicializar();
        }
    })();


    /*
     * ============================================================
     * CONTROLES VISUALES DEL REDISEÑO
     * ============================================================
     *
     * Esta parte NO elimina ni cancela todavía desde Laravel.
     * Solo prepara los modales y la visibilidad de los botones.
     *
     * La lógica de backend la agregaremos en la siguiente etapa.
     */

    let actividadSeleccionadaId = null;
    let actividadSeleccionadaEstado = null;


    function prepararAccionesActividad(actividad) {

        actividadSeleccionadaId = actividad?.id ?? null;
        actividadSeleccionadaEstado = actividad?.estado ?? null;

        // Compartir el ID con el módulo de subtareas cada vez que se abre/prepara el detalle.
        if (actividad?.id !== undefined && actividad?.id !== null) {
            window.actividadDetalleActual = actividad;
            actividadSubtareasActual = actividad.id;

            const modalDetalle = document.getElementById('detalleModal');
            if (modalDetalle) {
                modalDetalle.dataset.actividadId = String(actividad.id);
            }

            const formularioSubtareas = document.getElementById('crearSubtareaForm');
            if (formularioSubtareas) {
                // El formulario debe conservar el ID de la actividad seleccionada.
                formularioSubtareas.dataset.actividadId = String(actividad.id);
                formularioSubtareas.dataset.url = `/actividad/${actividad.id}/subtareas`;
                formularioSubtareas.method = 'POST';
                formularioSubtareas.action = new URL(formularioSubtareas.dataset.url, window.location.origin).href;
            }
        }

        const btnEliminar =
            document.getElementById('btnEliminarActividad');

        const btnCancelar =
            document.getElementById('btnCancelarActividad');

        if (!btnEliminar || !btnCancelar) {
            return;
        }

        /*
         * ELIMINAR:
         * Solo aparece cuando la actividad todavía está pendiente.
         */
        if (actividadSeleccionadaEstado === 'pendiente') {
            btnEliminar.classList.remove('hidden');
        } else {
            btnEliminar.classList.add('hidden');
        }

        /*
         * CANCELAR:
         * Solo aparece cuando la actividad ya comenzó.
         */
        if (
            actividadSeleccionadaEstado === 'en_proceso' ||
            actividadSeleccionadaEstado === 'pausada'
        ) {
            btnCancelar.classList.remove('hidden');
        } else {
            btnCancelar.classList.add('hidden');
        }
    }


    function abrirModalCancelacion() {

        const modal =
            document.getElementById('modalCancelacion');

        const motivo =
            document.getElementById('motivoCancelacion');

        if (!modal) {
            return;
        }

        if (motivo) {
            motivo.value = '';
        }

        modal.classList.remove('hidden');

        setTimeout(() => {
            motivo?.focus();
        }, 100);
    }


    function cerrarModalCancelacion() {

        document
            .getElementById('modalCancelacion')
            ?.classList.add('hidden');
    }


    function confirmarCancelacion() {

        const motivo =
            document.getElementById('motivoCancelacion');

        if (!motivo || !motivo.value.trim()) {

            motivo?.focus();

            return;
        }

        /*
         * Aquí conectaremos posteriormente:
         *
         * POST /actividad/{id}/cancelar
         *
         * Por ahora solo cerramos el modal.
         */
        cerrarModalCancelacion();
    }


    function eliminarActividad() {

        document
            .getElementById('modalEliminarActividad')
            ?.classList.remove('hidden');
    }


    function cerrarModalEliminar() {

        document
            .getElementById('modalEliminarActividad')
            ?.classList.add('hidden');
    }


    function confirmarEliminarActividad() {

        /*
         * Aquí conectaremos posteriormente:
         *
         * DELETE /actividad/{id}
         *
         * La acción real se agregará cuando terminemos
         * la parte visual.
         */
        cerrarModalEliminar();
    }


    /*
     * Permite cerrar los modales secundarios haciendo clic
     * fuera de la tarjeta.
     */

    document.addEventListener('click', function(event) {

        const modalCancelacion =
            document.getElementById('modalCancelacion');

        const modalEliminar =
            document.getElementById('modalEliminarActividad');

        if (
            modalCancelacion &&
            event.target === modalCancelacion
        ) {
            cerrarModalCancelacion();
        }

        if (
            modalEliminar &&
            event.target === modalEliminar
        ) {
            cerrarModalEliminar();
        }

    });
</script>

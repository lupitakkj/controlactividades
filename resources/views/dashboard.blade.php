<x-app-layout>

    <div class="p-6">
        <!-- HEADER -->

        <div class="flex items-center justify-between mb-6 gap-4">

            {{-- Barra de filtros --}}
            <form
                method="GET"
                action="{{ route('dashboard') }}"
                class="flex items-center gap-3 flex-1">

                {{-- Buscar --}}
                <input
                    type="text"
                    name="buscar"
                    value="{{ request('buscar') }}"
                    placeholder="🔍 Buscar actividad..."
                    class="w-72 h-11 rounded-xl border border-slate-600 bg-slate-800 text-white px-4">

                {{-- Cliente --}}
                <select
                    name="cliente"
                    class="w-52 h-11 rounded-xl border border-slate-600 bg-slate-800 text-white px-3">

                    <option value="">🏢 Cliente</option>

                    @foreach($clientes as $cliente)

                    <option
                        value="{{ $cliente }}"
                        {{ request('cliente') == $cliente ? 'selected' : '' }}>

                        {{ $cliente }}

                    </option>

                    @endforeach

                </select>

                @if(auth()->user()->hasRole('Administrador'))

                <select
                    name="disenador"
                    class="w-52 h-11 rounded-xl border border-slate-600 bg-slate-800 text-white px-3">

                    <option value="">🎨 Diseñador</option>

                    @foreach($disenadores as $disenador)

                    <option
                        value="{{ $disenador->id }}"
                        {{ request('disenador') == $disenador->id ? 'selected' : '' }}>

                        {{ $disenador->name }}

                    </option>

                    @endforeach

                </select>

                @endif

                {{-- Prioridad --}}
                <select
                    name="prioridad"
                    class="w-44 h-11 rounded-xl border border-slate-600 bg-slate-800 text-white px-3">

                    <option value="">🔥 Prioridad</option>

                    <option value="baja" {{ request('prioridad')=='baja' ? 'selected' : '' }}>
                        Baja
                    </option>

                    <option value="media" {{ request('prioridad')=='media' ? 'selected' : '' }}>
                        Media
                    </option>

                    <option value="alta" {{ request('prioridad')=='alta' ? 'selected' : '' }}>
                        Alta
                    </option>

                    <option value="urgente" {{ request('prioridad')=='urgente' ? 'selected' : '' }}>
                        Urgente
                    </option>

                </select>

                <button
                    type="submit"
                    class="h-11 px-5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white">

                    <i class="fa-solid fa-filter"></i>

                </button>

                <a
                    href="{{ route('dashboard') }}"
                    class="h-11 w-11 rounded-xl bg-slate-700
                        hover:bg-red-600
                        text-white
                        flex items-center justify-center
                        transition">

                    <i class="fa-solid fa-xmark"></i>

                </a>

            </form>

            <div class="flex items-center gap-3">

                @role('Administrador|Supervisor')

                <a
                    href="{{ route('reportes') }}"
                    class="inline-flex items-center gap-2 h-11 px-5 rounded-xl
                   bg-slate-800 border border-violet-500/50
                   text-violet-300 font-semibold
                   hover:bg-violet-600 hover:text-white
                   transition-all duration-300 shadow-lg">

                    <i class="fa-solid fa-chart-column"></i>

                    Reportes

                </a>

                @endrole

                @role('Administrador')

                <a
                    href="{{ route('usuarios.index') }}"
                    class="inline-flex items-center gap-2 h-11 px-5 rounded-xl
                   bg-slate-800 border border-emerald-500/50
                   text-emerald-300 font-semibold
                   hover:bg-emerald-600 hover:text-white
                   transition-all duration-300 shadow-lg">

                    <i class="fa-solid fa-users"></i>

                    Usuarios

                </a>

                @endrole

                <button
                    onclick="document.getElementById('modal').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 h-11 px-5 rounded-xl
                   bg-blue-600 border border-blue-400
                   text-white font-semibold
                   hover:bg-blue-700
                   transition-all duration-300 shadow-lg">

                    <i class="fa-solid fa-plus"></i>

                    Nueva actividad

                </button>

            </div>

        </div>

        <!-- TABLERO -->

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">

            <x-kanban-column
                id="pendiente"
                title="Pendientes"
                icon="fa-clipboard-list"
                borderClass="border-pendientes"
                :actividades="$pendientes" />

            <x-kanban-column
                id="en_proceso"
                title="En proceso"
                icon="fa-rotate"
                borderClass="border-proceso"
                :actividades="$proceso" />

            <x-kanban-column
                id="pausada"
                title="Pausadas"
                icon="fa-circle-pause"
                borderClass="border-pausa"
                :actividades="$pausadas" />

            <x-kanban-column
                id="terminada"
                title="Terminadas"
                icon="fa-circle-check"
                borderClass="border-terminado"
                :actividades="$terminadas" />

        </div>

    </div>

    <!-- MODAL NUEVA ACTIVIDAD -->
    <div id="modal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-3 sm:p-5">
        <section class="bg-white text-slate-900 rounded-2xl sm:rounded-3xl shadow-2xl w-full max-w-5xl max-h-[86vh] flex flex-col overflow-hidden"
                 role="dialog" aria-modal="true" aria-labelledby="tituloModalNuevaActividad">

            <header class="shrink-0 flex items-center justify-between gap-4 px-5 sm:px-7 py-4 sm:py-5 border-b border-slate-200 bg-white">
                <div class="min-w-0">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-600 mb-1">Gestión de actividades</p>
                    <h2 id="tituloModalNuevaActividad" class="text-xl sm:text-2xl font-bold tracking-tight">📝 Nueva actividad</h2>
                    <p class="text-sm text-slate-500 mt-1">Completa la información para registrar una nueva actividad.</p>
                </div>
                <button type="button" aria-label="Cerrar modal"
                        onclick="document.getElementById('modal').classList.add('hidden')"
                        class="shrink-0 w-10 h-10 rounded-xl flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </header>

            <form id="crearActividadForm" method="POST" action="{{ route('actividades.store') }}"
                  enctype="multipart/form-data" class="flex flex-col min-h-0 flex-1">
                @csrf

                <div class="flex-1 min-h-0 overflow-y-auto px-5 sm:px-7 py-4">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-5">
                        <div class="space-y-4">
                            <section class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:p-5">
                                <div class="mb-4">
                                    <h3 class="font-bold text-slate-800">Información general</h3>
                                    <p class="text-xs text-slate-500 mt-1">Identifica la actividad y explica qué se necesita realizar.</p>
                                </div>
                                <div class="mb-4">
                                    <label for="nuevaActividadTitulo" class="block text-sm font-semibold text-slate-700 mb-2">Título <span class="text-red-500">*</span></label>
                                    <input id="nuevaActividadTitulo" type="text" name="titulo" required maxlength="255" autocomplete="off"
                                           placeholder="Ej. Diseño de ficha técnica"
                                           class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                </div>
                                <div>
                                    <label for="nuevaActividadDescripcion" class="block text-sm font-semibold text-slate-700 mb-2">Descripción</label>
                                    <textarea id="nuevaActividadDescripcion" name="descripcion" rows="5"
                                              placeholder="Describe el objetivo, las indicaciones y los detalles importantes..."
                                              class="w-full min-h-[105px] rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-sm leading-6 outline-none transition resize-y focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"></textarea>
                                    <p class="text-xs text-slate-500 mt-2">Incluye la información que necesita el responsable para realizar el trabajo.</p>
                                </div>
                            </section>

                            <section class="rounded-2xl border border-slate-200 p-4 sm:p-5">
                                <div class="mb-4">
                                    <h3 class="font-bold text-slate-800">Archivos adjuntos</h3>
                                    <p class="text-xs text-slate-500 mt-1">Puedes seleccionar uno o varios archivos.</p>
                                </div>
                                <label for="archivos" class="flex flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50/50 transition">
                                    <span class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center"><i class="fa-solid fa-cloud-arrow-up text-lg"></i></span>
                                    <span class="text-sm font-semibold text-slate-700">Seleccionar archivos</span>
                                    <span class="text-xs text-slate-500">Haz clic para buscar en tu equipo</span>
                                    <input type="file" id="archivos" name="archivos[]" multiple class="sr-only">
                                </label>
                                <div id="listaArchivos" class="mt-3 space-y-2"></div>
                            </section>
                        </div>

                        <div class="space-y-4">
                            <section class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:p-5">
                                <div class="mb-4">
                                    <h3 class="font-bold text-slate-800">Planificación</h3>
                                    <p class="text-xs text-slate-500 mt-1">Define la prioridad, complejidad y fecha objetivo.</p>
                                </div>
                                <div class="mb-5">
                                    <label for="nuevaActividadPrioridad" class="block text-sm font-semibold text-slate-700 mb-2">Prioridad</label>
                                    <select id="nuevaActividadPrioridad" name="prioridad"
                                            class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                        <option value="baja">🟢 Baja</option>
                                        <option value="media">🟡 Media</option>
                                        <option value="alta">🟠 Alta</option>
                                        <option value="urgente">🔴 Urgente</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-slate-700 mb-3">Complejidad</label>
                                    <input type="hidden" name="complejidad" id="complejidad" value="1">
                                    <div class="grid grid-cols-3 gap-2 sm:gap-3">
                                        <button type="button" class="complejidad-card active rounded-xl border-2 border-blue-500 bg-blue-600 text-white p-3 text-center transition hover:-translate-y-0.5"
                                                data-value="1" data-horas="4" aria-pressed="true">
                                            <i class="fa-solid fa-star text-xl text-amber-300"></i>
                                            <span class="block font-bold mt-2 text-sm">Nivel 1</span>
                                            <span class="block text-xs mt-1 opacity-90">Sencillo</span>
                                            <span class="block text-xs font-semibold mt-2">2–4 h</span>
                                        </button>
                                        <button type="button" class="complejidad-card rounded-xl border-2 border-slate-200 bg-white text-slate-700 p-3 text-center transition hover:border-blue-300 hover:-translate-y-0.5"
                                                data-value="2" data-horas="16" aria-pressed="false">
                                            <i class="fa-solid fa-star-half-stroke text-xl text-amber-500"></i>
                                            <span class="block font-bold mt-2 text-sm">Nivel 2</span>
                                            <span class="block text-xs text-slate-500 mt-1">Medio</span>
                                            <span class="block text-xs font-semibold text-blue-600 mt-2">4–16 h</span>
                                        </button>
                                        <button type="button" class="complejidad-card rounded-xl border-2 border-slate-200 bg-white text-slate-700 p-3 text-center transition hover:border-blue-300 hover:-translate-y-0.5"
                                                data-value="3" data-horas="48" aria-pressed="false">
                                            <i class="fa-solid fa-crown text-xl text-amber-500"></i>
                                            <span class="block font-bold mt-2 text-sm">Nivel 3</span>
                                            <span class="block text-xs text-slate-500 mt-1">Complejo</span>
                                            <span class="block text-xs font-semibold text-blue-600 mt-2">16–48 h</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-5 flex items-center justify-between gap-3 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="w-9 h-9 rounded-lg bg-white text-blue-600 flex items-center justify-center"><i class="fa-solid fa-stopwatch"></i></span>
                                        <div>
                                            <p class="text-xs text-slate-500">Tiempo estimado</p>
                                            <p id="horasEstimadas" class="font-bold text-slate-800">4 horas</p>
                                        </div>
                                    </div>
                                    <i class="fa-solid fa-circle-info text-blue-400"></i>
                                </div>

                                <div>
                                    <label for="fecha_limite_nueva" class="block text-sm font-semibold text-slate-700 mb-2">📅 Fecha de entrega</label>
                                    <input id="fecha_limite_nueva" type="datetime-local" name="fecha_limite"
                                           min="{{ now()->subDays(30)->format('Y-m-d\TH:i') }}"
                                           class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                </div>
                            </section>

                            <section class="rounded-2xl border border-slate-200 p-4 sm:p-5">
                                <div class="mb-4">
                                    <h3 class="font-bold text-slate-800">Asignación</h3>
                                    <p class="text-xs text-slate-500 mt-1">Selecciona quién realizará la actividad y para qué cliente.</p>
                                </div>
                                <div class="mb-4">
                                    <label for="nuevaActividadDisenador" class="block text-sm font-semibold text-slate-700 mb-2">👤 Diseñador</label>
                                    <select id="nuevaActividadDisenador" name="user_id"
                                            class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                        @foreach($disenadores as $disenador)
                                            <option value="{{ $disenador->id }}" {{ (string) old('user_id', auth()->id()) === (string) $disenador->id ? 'selected' : '' }}>
                                                {{ $disenador->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="nuevaActividadCliente" class="block text-sm font-semibold text-slate-700 mb-2">🏢 Cliente</label>
                                    <input id="nuevaActividadCliente" type="text" name="cliente" value="{{ old('cliente') }}"
                                           maxlength="255" placeholder="Nombre del cliente"
                                           class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                </div>
                            </section>
                        </div>
                    </div>
                </div>

                <footer class="shrink-0 border-t border-slate-200 bg-white px-5 sm:px-7 py-3 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
                    <p class="text-xs text-slate-500"><span class="text-red-500">*</span> Campo obligatorio</p>
                    <div class="flex items-center justify-end gap-3">
                        <button type="button" onclick="document.getElementById('modal').classList.add('hidden')"
                                class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-700 font-semibold hover:bg-slate-50 transition">
                            Cancelar
                        </button>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-sm transition">
                            <i class="fa-solid fa-floppy-disk"></i> Crear actividad
                        </button>
                    </div>
                </footer>
            </form>
        </section>
    </div>
    <div id="animationModal"
        class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50">

        <div class="bg-white rounded-3xl p-10 text-center shadow-2xl">

            <lottie-player
                id="animationPlayer"
                autoplay
                loop="false"
                style="width:220px;height:220px">
            </lottie-player>

            <h2
                id="animationText"
                class="mt-6 text-2xl font-bold">
            </h2>

        </div>

    </div>
    <script>
        // ============================================================
        // NORMALIZAR RELACIONES DE LA ACTIVIDAD
        // ============================================================
        function obtenerLista(relacion) {
            if (Array.isArray(relacion)) return relacion;
            if (Array.isArray(relacion?.data)) return relacion.data;
            return [];
        }

        // ============================================================
        // ACTUALIZAR CONTADORES DEL DETALLE
        // Se mantiene aquí para que funcione aunque el script del
        // modal se cargue después del dashboard.
        // ============================================================
        function actualizarContadoresActividadDashboard(actividad) {
            const archivos = obtenerLista(actividad?.archivos);
            const comentarios = obtenerLista(actividad?.comentarios);

            const contadorArchivos = document.getElementById('contadorArchivos');
            const contadorComentarios = document.getElementById('contadorComentarios');

            if (contadorArchivos) {
                contadorArchivos.textContent = archivos.length;
            }

            if (contadorComentarios) {
                contadorComentarios.textContent = comentarios.length;
            }

            console.log('CONTADORES ACTIVIDAD', {
                actividad_id: actividad?.id,
                archivos: archivos.length,
                comentarios: comentarios.length,
                datosArchivos: archivos,
                datosComentarios: comentarios
            });
        }

        function abrirModal(actividad) {
            console.log('ACTIVIDAD ABIERTA:', actividad);

            // Compartir la actividad seleccionada con el modal de detalle
            // y con el formulario AJAX de subtareas.
            if (actividad && actividad.id != null) {
                window.actividadDetalleActual = actividad;
                window.actividadSeleccionadaId = Number(actividad.id);

                const formularioSubtareas = document.getElementById('crearSubtareaForm');
                if (formularioSubtareas) {
                    formularioSubtareas.dataset.actividadId = String(actividad.id);
                    formularioSubtareas.dataset.url = `/actividad/${actividad.id}/subtareas`;
                }

                const modalDetalle = document.getElementById('detalleModal');
                if (modalDetalle) {
                    modalDetalle.dataset.actividadId = String(actividad.id);
                }

                // Sincronizar el módulo de subtareas con la actividad recién abierta.
                if (typeof prepararAccionesActividad === 'function') {
                    prepararAccionesActividad(actividad);
                }
                if (typeof renderizarSubtareas === 'function') {
                    renderizarSubtareas(actividad);
                }
            }

            // Actualizar contadores antes de mostrar el modal
            actualizarContadoresActividadDashboard(actividad);

            const minutos = actividad.tiempo_total ?? 0;

            const horas = Math.floor(minutos / 60);
            const mins = minutos % 60;
            document.getElementById('detalleTitulo').value =
                actividad.titulo;

            document.getElementById('detalleDescripcion').value =
                actividad.descripcion ?? '';

            document.getElementById('detallePrioridad').value =
                actividad.prioridad.toLowerCase();
            document.getElementById('detalleDisenador').textContent =
                actividad.user?.name ?? 'Sin asignar';

            document.getElementById('detalleEstado')
                .innerText =
                actividad.estado.replace('_', ' ').toUpperCase();

            document.getElementById('detalleTiempo').innerText =
                `${horas.toString().padStart(2,'0')}:${mins.toString().padStart(2,'0')}`;

            // FORM COMENTARIOS

            document.getElementById('comentarioForm')
                .action =
                `/actividad/${actividad.id}/comentario`;

            // FORM REASIGNAR

            let reasignarForm =
                document.getElementById('reasignarForm');

            if (reasignarForm) {

                reasignarForm.action =
                    `/actividad/${actividad.id}/reasignar`;
            }

            // COMENTARIOS

            let container =
                document.getElementById('comentariosContainer');

            const comentarios = obtenerLista(actividad?.comentarios);
            const archivos = obtenerLista(actividad?.archivos);

            container.innerHTML = '';

            if (comentarios.length > 0) {

                comentarios.forEach(comentario => {

                    container.innerHTML += `

                    <div class="bg-gray-100 rounded-xl p-3">

                        <div class="font-bold mb-1">
                            ${comentario.user.name}
                        </div>

                        <div class="text-gray-700">
                            ${comentario.comentario}
                        </div>

                    </div>

                `;
                });
            } else {
                container.innerHTML = `
                    <div class="text-sm text-slate-500 text-center py-4">
                        No hay comentarios
                    </div>
                `;
            }

            // ARCHIVOS
            const archivosContainer = document.getElementById('archivosContainer');

            if (archivosContainer) {
                archivosContainer.innerHTML = '';

                if (archivos.length > 0) {
                    archivos.forEach(archivo => {
                        const nombre = archivo.nombre_original ?? archivo.nombre ?? 'Archivo';
                        const idArchivo = archivo.id;

                        archivosContainer.innerHTML += `
                            <div class="flex items-center justify-between gap-3 p-3 rounded-xl border border-slate-200 bg-slate-50">
                                <div class="min-w-0">
                                    <p class="font-semibold text-sm text-slate-700 truncate">${nombre}</p>
                                </div>
                                ${idArchivo ? `
                                    <a href="/archivo/${idArchivo}/descargar" target="_blank" class="shrink-0 inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                                        <i class="fa-solid fa-download"></i>
                                        Descargar
                                    </a>` : ''}
                            </div>
                        `;
                    });
                } else {
                    archivosContainer.innerHTML = `
                        <div class="text-sm text-slate-500 text-center py-4">
                            No hay archivos
                        </div>
                    `;
                }
            }

            // Última sincronización antes de mostrar el detalle.
            // Evita que el formulario conserve el ID de una actividad anterior.
            const formularioSubtareasFinal = document.getElementById('crearSubtareaForm');
            const modalDetalleFinal = document.getElementById('detalleModal');

            if (actividad && actividad.id != null) {
                const idActividadFinal = String(actividad.id);

                if (formularioSubtareasFinal) {
                    formularioSubtareasFinal.dataset.actividadId = idActividadFinal;
                    formularioSubtareasFinal.dataset.url = `/actividad/${idActividadFinal}/subtareas`;
                }

                if (modalDetalleFinal) {
                    modalDetalleFinal.dataset.actividadId = idActividadFinal;
                }

                window.actividadDetalleActual = actividad;
                window.actividadSeleccionadaId = Number(actividad.id);
            }

            document.getElementById('detalleModal')
                .classList.remove('hidden');
        }

        function cerrarModal() {

            document.getElementById('detalleModal')
                .classList.add('hidden');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.querySelectorAll('.kanban-body').forEach(column => {

            new Sortable(column, {

                group: 'kanban',
                animation: 200,
                ghostClass: 'opacity-50',
                delay: 150,

                onEnd: function() {

                    let columnas = [];

                    document.querySelectorAll('.kanban-column').forEach(columna => {

                        let estado = columna.id;

                        let tarjetas = [];

                        columna.querySelectorAll('.kanban-card').forEach((card, index) => {

                            tarjetas.push({

                                id: Number(card.dataset.id),
                                orden: index + 1

                            });

                        });

                        columnas.push({

                            estado: estado,
                            tarjetas: tarjetas

                        });

                    });

                    console.log(columnas);

                }

            });

        });
    </script>

    <script>
        function actualizarReloj() {

            const ahora = new Date();

            const elementoReloj = document.getElementById("clock");
            // Algunas vistas no contienen el reloj; no detener el resto del JavaScript.
            if (!elementoReloj) return;

            elementoReloj.innerHTML = ahora.toLocaleTimeString("es-MX", {
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit"
            });

        }

        actualizarReloj();

        setInterval(actualizarReloj, 1000);
    </script>





    @include('modals.detalle-actividad')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

    <script>
        /*
         * Corrección permanente de contadores:
         * conserva abrirModal y actualiza los contadores al abrir la actividad.
         */
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof window.abrirModal !== 'function') {
                console.error('No se encontró la función abrirModal.');
                return;
            }

            if (window.abrirModal.__contadoresCorregidos) {
                return;
            }

            const abrirModalOriginal = window.abrirModal;

            const abrirModalConContadores = function (actividad) {
                abrirModalOriginal(actividad);

                const archivos = Array.isArray(actividad?.archivos)
                    ? actividad.archivos
                    : Array.isArray(actividad?.archivos?.data)
                        ? actividad.archivos.data
                        : [];

                const comentarios = Array.isArray(actividad?.comentarios)
                    ? actividad.comentarios
                    : Array.isArray(actividad?.comentarios?.data)
                        ? actividad.comentarios.data
                        : [];

                const contadorArchivos = document.getElementById('contadorArchivos');
                const contadorComentarios = document.getElementById('contadorComentarios');

                if (contadorArchivos) {
                    contadorArchivos.textContent = String(archivos.length);
                }

                if (contadorComentarios) {
                    contadorComentarios.textContent = String(comentarios.length);
                }

                console.log('CONTADORES AL ABRIR MODAL', {
                    actividad_id: actividad?.id,
                    archivos: archivos.length,
                    comentarios: comentarios.length
                });
            };

            abrirModalConContadores.__contadoresCorregidos = true;
            window.abrirModal = abrirModalConContadores;
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const complexityCards = document.querySelectorAll('.complejidad-card');
            const complexityInput = document.getElementById('complejidad');
            const estimatedHours = document.getElementById('horasEstimadas');

            complexityCards.forEach(function (card) {
                card.addEventListener('click', function () {
                    complexityCards.forEach(function (item) {
                        const active = item === card;
                        item.classList.toggle('active', active);
                        item.setAttribute('aria-pressed', active ? 'true' : 'false');

                        if (active) {
                            item.classList.add('border-blue-500', 'bg-blue-600', 'text-white');
                            item.classList.remove('border-slate-200', 'bg-white', 'text-slate-700');
                        } else {
                            item.classList.remove('border-blue-500', 'bg-blue-600', 'text-white');
                            item.classList.add('border-slate-200', 'bg-white', 'text-slate-700');
                        }
                    });

                    if (complexityInput) complexityInput.value = card.dataset.value;
                    if (estimatedHours) estimatedHours.textContent = card.dataset.horas + ' horas';
                });
            });

            const fileInput = document.getElementById('archivos');
            const fileList = document.getElementById('listaArchivos');
            let selectedFiles = [];

            function formatFileSize(bytes) {
                return bytes < 1048576
                    ? (bytes / 1024).toFixed(1) + ' KB'
                    : (bytes / 1048576).toFixed(2) + ' MB';
            }

            function syncFileInput() {
                if (!fileInput || typeof DataTransfer === 'undefined') return;

                const transfer = new DataTransfer();
                selectedFiles.forEach(function (file) {
                    transfer.items.add(file);
                });
                fileInput.files = transfer.files;
            }

            function renderFileList() {
                if (!fileList) return;
                fileList.innerHTML = '';

                if (selectedFiles.length === 0) {
                    const empty = document.createElement('p');
                    empty.className = 'text-xs text-slate-500 py-1';
                    empty.textContent = 'Todavía no has seleccionado archivos.';
                    fileList.appendChild(empty);
                    return;
                }

                const totalBytes = selectedFiles.reduce(function (total, file) {
                    return total + file.size;
                }, 0);

                const summary = document.createElement('div');
                summary.className = 'flex items-center justify-between gap-2 text-xs text-slate-500 mb-2';

                const count = document.createElement('span');
                count.textContent = selectedFiles.length +
                    (selectedFiles.length === 1 ? ' archivo seleccionado' : ' archivos seleccionados');

                const total = document.createElement('span');
                total.textContent = 'Total: ' + formatFileSize(totalBytes);

                summary.appendChild(count);
                summary.appendChild(total);
                fileList.appendChild(summary);

                selectedFiles.forEach(function (file, index) {
                    const row = document.createElement('div');
                    row.className = 'flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2.5';

                    const icon = document.createElement('span');
                    icon.className = 'w-9 h-9 shrink-0 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center';
                    icon.innerHTML = '<i class="fa-regular fa-file"></i>';

                    const details = document.createElement('div');
                    details.className = 'min-w-0 flex-1';

                    const name = document.createElement('p');
                    name.className = 'text-sm font-semibold text-slate-700 break-all';
                    name.textContent = file.name;

                    const metadata = document.createElement('p');
                    metadata.className = 'text-xs text-slate-500 mt-0.5';
                    metadata.textContent = formatFileSize(file.size) +
                        (file.type ? ' · ' + file.type : '');

                    const remove = document.createElement('button');
                    remove.type = 'button';
                    remove.className = 'w-9 h-9 shrink-0 rounded-lg text-slate-500 hover:text-red-600 hover:bg-red-50 transition';
                    remove.setAttribute('aria-label', 'Quitar ' + file.name);
                    remove.title = 'Quitar archivo';
                    remove.innerHTML = '<i class="fa-solid fa-trash-can"></i>';

                    remove.addEventListener('click', function () {
                        selectedFiles.splice(index, 1);
                        syncFileInput();
                        renderFileList();
                    });

                    details.appendChild(name);
                    details.appendChild(metadata);
                    row.appendChild(icon);
                    row.appendChild(details);
                    row.appendChild(remove);
                    fileList.appendChild(row);
                });
            }

            if (fileInput && fileList) {
                renderFileList();

                fileInput.addEventListener('change', function () {
                    Array.from(fileInput.files || []).forEach(function (newFile) {
                        const duplicate = selectedFiles.some(function (file) {
                            return file.name === newFile.name &&
                                file.size === newFile.size &&
                                file.lastModified === newFile.lastModified;
                        });

                        if (!duplicate) selectedFiles.push(newFile);
                    });

                    syncFileInput();
                    renderFileList();
                });

                const createForm = document.getElementById('crearActividadForm');
                if (createForm) {
                    createForm.addEventListener('reset', function () {
                        selectedFiles = [];
                        fileInput.value = '';
                        renderFileList();
                    });
                }
            }

            const modal = document.getElementById('modal');
            if (modal) {
                modal.addEventListener('click', function (event) {
                    if (event.target === modal) modal.classList.add('hidden');
                });
                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                        modal.classList.add('hidden');
                    }
                });
            }
        });
    </script>

</x-app-layout>
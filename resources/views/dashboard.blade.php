<x-app-layout>

    <div class="p-6">

        <!-- HEADER -->

        <div class="flex justify-end items-center mb-6 gap-3">

            @role('Administrador|Supervisor')

            <a
                href="{{ route('reportes') }}"
                class="inline-flex items-center gap-2 h-11 px-5 rounded-xl
                        bg-slate-800 border border-violet-500/50
                        text-violet-300 font-semibold
                        hover:bg-violet-600 hover:text-white
                        hover:border-violet-500
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
                        hover:border-emerald-500
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
                        hover:shadow-blue-500/30
                        transition-all duration-300 shadow-lg">

                <i class="fa-solid fa-plus"></i>

                Nueva actividad

            </button>

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

    <div id="modal"
        class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">

        <div class="bg-white rounded-2xl p-6 w-full max-w-md">

            <h2 class="text-2xl font-bold mb-4">
                Nueva actividad
            </h2>

            <form method="POST"
                action="{{ route('actividades.store') }}"
                enctype="multipart/form-data">

                @csrf

                <div class="mb-4">

                    <label class="block mb-1 font-medium">
                        Título
                    </label>

                    <input
                        type="text"
                        name="titulo"
                        required
                        class="w-full border rounded-lg p-2">

                </div>

                <div class="mb-4">

                    <label class="block mb-1 font-medium">
                        Descripción
                    </label>

                    <textarea
                        name="descripcion"
                        rows="4"
                        class="w-full border rounded-lg p-2"></textarea>

                </div>

                <div class="mb-6">

                    <label class="block mb-1 font-medium">
                        Prioridad
                    </label>

                    <select
                        name="prioridad"
                        class="w-full border rounded-lg p-2">

                        <option value="baja">Baja</option>
                        <option value="media">Media</option>
                        <option value="alta">Alta</option>
                        <option value="urgente">Urgente</option>

                    </select>

                </div>

                <div class="mb-6">

                    <label class="block mb-3 font-semibold text-gray-700">

                        Complejidad

                    </label>

                    <input type="hidden"
                        name="complejidad"
                        id="complejidad"
                        value="1">

                    <div class="flex gap-4">

                        <div class="complejidad-card active flex-1"
                            data-value="1"
                            data-horas="4">

                            <i class="fa-solid fa-star"></i>

                            <h4>Nivel 1</h4>

                            <p>Diseño sencillo</p>

                            <span>2 - 4 horas</span>

                        </div>

                        <div class="complejidad-card flex-1"
                            data-value="2"
                            data-horas="16">

                            <i class="fa-solid fa-star-half-stroke"></i>

                            <h4>Nivel 2</h4>

                            <p>Diseño medio</p>

                            <span>4 - 16 horas</span>

                        </div>

                        <div class="complejidad-card flex-1"
                            data-value="3"
                            data-horas="48">

                            <i class="fa-solid fa-crown"></i>

                            <h4>Nivel 3</h4>

                            <p>Diseño complejo</p>

                            <span>16 - 48 horas</span>

                        </div>

                    </div>

                    <div class="mt-4 p-3 rounded-xl bg-blue-50 border border-blue-200">

                        <span class="font-semibold">
                            Tiempo estimado:
                        </span>

                        <span id="horasEstimadas">
                            2 - 4 horas
                        </span>

                    </div>

                </div>

                <div class="mb-6">

                    <label class="block mb-1 font-medium">
                        Diseñador
                    </label>

                    <select
                        name="user_id"
                        required
                        class="w-full border rounded-lg p-2">

                        @foreach($disenadores as $disenador)

                        <option value="{{ $disenador->id }}">

                            {{ $disenador->name }}

                        </option>

                        @endforeach

                    </select>

                </div>


                <div class="mb-4">

                    <label class="block mb-1 font-medium">

                        Archivos

                    </label>

                    <input
                        type="file"
                        name="archivos[]"
                        multiple
                        class="w-full border rounded-lg p-2">

                </div>
                <div class="flex justify-end gap-3">

                    <button
                        type="button"
                        onclick="document.getElementById('modal').classList.add('hidden')"
                        class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded-lg">

                        Cancelar

                    </button>

                    <button
                        type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">

                        Guardar

                    </button>

                </div>

            </form>

        </div>

    </div>
    <!-- MODAL DETALLE -->

    <div id="detalleModal"
        class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50">

        <div class="bg-white rounded-2xl w-full max-w-2xl p-6 relative max-h-[90vh] overflow-y-auto">

            <!-- CERRAR -->

            <button
                onclick="cerrarModal()"
                class="absolute top-4 right-4 text-gray-500 hover:text-black text-2xl">

                ✕

            </button>

            <!-- TITULO -->

            <h2 id="detalleTitulo"
                class="text-3xl font-bold mb-4">
            </h2>

            <!-- BADGES -->

            <div class="flex gap-3 mb-6">

                <span id="detallePrioridad"
                    class="px-3 py-1 rounded-full text-sm bg-gray-200">
                </span>

                <span id="detalleEstado"
                    class="px-3 py-1 rounded-full text-sm bg-blue-200">
                </span>

            </div>

            <!-- DESCRIPCION -->

            <div class="mb-6">

                <h3 class="font-bold text-lg mb-2">
                    Descripción
                </h3>

                <div id="detalleDescripcion"
                    class="bg-gray-100 rounded-xl p-4 min-h-[120px] whitespace-pre-line">
                </div>

            </div>

            <!-- TIEMPO -->

            <div class="mb-6">

                <h3 class="font-bold text-lg mb-2">
                    Tiempo acumulado
                </h3>

                <div class="text-2xl font-bold text-blue-600">

                    <span id="detalleTiempo"></span>

                </div>

            </div>

            <!-- COMENTARIOS -->

            <div class="mt-8">

                <h3 class="font-bold text-xl mb-4">
                    Comentarios
                </h3>

                <div id="comentariosContainer"
                    class="space-y-4 max-h-[300px] overflow-y-auto mb-6">

                </div>

                <form id="comentarioForm"
                    method="POST">

                    @csrf

                    <textarea
                        name="comentario"
                        required
                        placeholder="Escribe un comentario..."
                        class="w-full border rounded-xl p-3 mb-3"></textarea>

                    <button
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">

                        Comentar

                    </button>

                </form>

            </div>

            <!-- REASIGNAR -->

            @role('admin|supervisor')

            <div class="mt-8">

                <h3 class="font-bold text-xl mb-4">
                    Reasignar diseñador
                </h3>

                <form id="reasignarForm"
                    method="POST">

                    @csrf

                    <select
                        name="user_id"
                        class="w-full border rounded-xl p-3 mb-3">

                        @foreach($disenadores as $disenador)

                        <option value="{{ $disenador->id }}">

                            {{ $disenador->name }}

                        </option>

                        @endforeach

                    </select>

                    <button
                        class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg">

                        Reasignar

                    </button>

                </form>

            </div>

            @endrole

        </div>

    </div>

    <!-- SCRIPT MODAL -->

    <script>
        function abrirModal(actividad) {

            document.getElementById('detalleTitulo')
                .innerText = actividad.titulo;

            document.getElementById('detalleDescripcion')
                .innerText = actividad.descripcion ?? '';

            document.getElementById('detallePrioridad')
                .innerText = actividad.prioridad.toUpperCase();

            document.getElementById('detalleEstado')
                .innerText =
                actividad.estado.replace('_', ' ').toUpperCase();

            document.getElementById('detalleTiempo')
                .innerText =
                actividad.tiempo_total + ' min';

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

            container.innerHTML = '';

            if (actividad.comentarios) {

                actividad.comentarios.forEach(comentario => {

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
        document.querySelectorAll('.kanban-body')
            .forEach(column => {

                new Sortable(column, {

                    group: 'kanban',

                    animation: 200,

                    ghostClass: 'opacity-50',

                    delay: 150,

                    delayOnTouchOnly: false,

                    onEnd: function(evt) {

                        let actividadId =
                            evt.item.dataset.id;

                        let nuevoEstado =
                            evt.to.closest('.kanban-column').id;

                        fetch(`/actividad/mover/${actividadId}`, {

                                method: 'POST',

                                headers: {

                                    'Content-Type': 'application/json',

                                    'Accept': 'application/json',

                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },

                                body: JSON.stringify({

                                    estado: nuevoEstado
                                })

                            })
                            .then(response => response.json())
                            .then(data => {

                                location.reload();

                            })
                            .catch(error => {

                                console.error(error);

                            });

                    }

                });

            });
    </script>

    <script>
        function actualizarReloj() {

            const ahora = new Date();

            document.getElementById("clock").innerHTML =
                ahora.toLocaleTimeString("es-MX", {
                    hour: "2-digit",
                    minute: "2-digit",
                    second: "2-digit"
                });

        }

        actualizarReloj();

        setInterval(actualizarReloj, 1000);
    </script>

    <script>
        document.querySelectorAll(".complejidad-card").forEach(card => {

            card.addEventListener("click", function() {

                document.querySelectorAll(".complejidad-card").forEach(c => {
                    c.classList.remove("active");
                });

                this.classList.add("active");

                document.getElementById("complejidad").value = this.dataset.value;

                document.getElementById("horasEstimadas").textContent =
                    this.dataset.horas + " horas";

            });

        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const cards = document.querySelectorAll(".complejidad-card");
            const input = document.getElementById("complejidad");
            const horas = document.getElementById("horasEstimadas");

            cards.forEach(card => {

                card.addEventListener("click", function() {

                    cards.forEach(c => c.classList.remove("active"));

                    this.classList.add("active");

                    input.value = this.dataset.value;

                    horas.textContent = this.dataset.horas + " horas";

                });

            });

        });
    </script>
</x-app-layout>
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
        class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-6">

        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-6xl h-[90vh] flex flex-col">

            <div class="flex-1 overflow-y-auto p-8">

                <h2 class="text-3xl font-bold mb-8">
                    📝 Nueva actividad
                </h2>

                <form method="POST"
                    action="{{ route('actividades.store') }}"
                    enctype="multipart/form-data">

                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                        <!-- ===================================== -->
                        <!-- COLUMNA IZQUIERDA -->
                        <!-- ===================================== -->

                        <div>

                            <div class="mb-6">

                                <label class="block font-semibold mb-2">
                                    Título
                                </label>

                                <input
                                    type="text"
                                    name="titulo"
                                    required
                                    class="w-full rounded-xl border p-3">

                            </div>

                            <div>

                                <label class="block font-semibold mb-2">
                                    Descripción
                                </label>

                                <textarea
                                    name="descripcion"
                                    rows="12"
                                    class="w-full rounded-xl border p-3 resize-none"></textarea>

                            </div>

                        </div>

                        <!-- ===================================== -->
                        <!-- COLUMNA DERECHA -->
                        <!-- ===================================== -->

                        <div>

                            <!-- PRIORIDAD -->

                            <div class="mb-5">

                                <label class="block font-semibold mb-2">
                                    Prioridad
                                </label>

                                <select
                                    name="prioridad"
                                    class="w-full rounded-xl border p-3">

                                    <option value="baja">🟢 Baja</option>
                                    <option value="media">🟡 Media</option>
                                    <option value="alta">🟠 Alta</option>
                                    <option value="urgente">🔴 Urgente</option>

                                </select>

                            </div>

                            <!-- COMPLEJIDAD -->

                            <div class="mb-5">

                                <label class="block font-semibold mb-3">
                                    Complejidad
                                </label>

                                <input
                                    type="hidden"
                                    name="complejidad"
                                    id="complejidad"
                                    value="1">

                                <div class="grid grid-cols-3 gap-3">

                                    <div class="complejidad-card active"
                                        data-value="1"
                                        data-horas="4">

                                        <i class="fa-solid fa-star text-blue-600 text-2xl"></i>

                                        <h4 class="font-bold mt-2">
                                            Nivel 1
                                        </h4>

                                        <p class="text-sm text-gray-500">
                                            Diseño sencillo
                                        </p>

                                        <span class="text-blue-600 text-sm font-semibold">
                                            2 - 4 horas
                                        </span>

                                    </div>

                                    <div class="complejidad-card"
                                        data-value="2"
                                        data-horas="16">

                                        <i class="fa-solid fa-star-half-stroke text-yellow-500 text-2xl"></i>

                                        <h4 class="font-bold mt-2">
                                            Nivel 2
                                        </h4>

                                        <p class="text-sm text-gray-500">
                                            Diseño medio
                                        </p>

                                        <span class="text-blue-600 text-sm font-semibold">
                                            4 - 16 horas
                                        </span>

                                    </div>

                                    <div class="complejidad-card"
                                        data-value="3"
                                        data-horas="48">

                                        <i class="fa-solid fa-crown text-red-500 text-2xl"></i>

                                        <h4 class="font-bold mt-2">
                                            Nivel 3
                                        </h4>

                                        <p class="text-sm text-gray-500">
                                            Diseño complejo
                                        </p>

                                        <span class="text-blue-600 text-sm font-semibold">
                                            16 - 48 horas
                                        </span>

                                    </div>

                                </div>

                            </div>

                            <!-- TIEMPO -->

                            <div class="mb-5 rounded-xl bg-blue-50 border border-blue-200 p-4">

                                <span class="font-semibold">
                                    ⏱ Tiempo estimado:
                                </span>

                                <span id="horasEstimadas">
                                    4 horas
                                </span>

                            </div>

                            <!-- FECHA -->

                            <div class="mb-5">

                                <label class="block font-semibold mb-2">
                                    📅 Fecha de entrega
                                </label>

                                <input
                                    type="datetime-local"
                                    name="fecha_limite"
                                    min="{{ now()->format('Y-m-d\TH:i') }}"
                                    class="w-full rounded-xl border p-3">

                            </div>

                            <!-- DISEÑADOR -->

                            <div class="mb-5">

                                <label class="block font-semibold mb-2">
                                    👤 Diseñador
                                </label>

                                <select
                                    name="user_id"
                                    class="w-full rounded-xl border p-3">

                                    @foreach($disenadores as $disenador)

                                    <option value="{{ $disenador->id }}">
                                        {{ $disenador->name }}
                                    </option>

                                    @endforeach

                                </select>

                            </div>

                            <!-- ARCHIVOS -->

                            <div class="mb-5">

                                <label class="block font-semibold mb-2">
                                    📎 Archivos
                                </label>

                                <input
                                    type="file"
                                    name="archivos[]"
                                    multiple
                                    class="w-full rounded-xl border p-3">

                            </div>

                        </div>

                    </div>

                    <div class="border-t bg-white px-8 py-5 flex justify-end gap-4">

                        <button
                            type="button"
                            onclick="document.getElementById('modal').classList.add('hidden')"
                            class="px-6 py-3 rounded-xl bg-gray-200 hover:bg-gray-300">

                            Cancelar

                        </button>

                        <button
                            type="submit"
                            class="px-8 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold">

                            💾 Crear actividad

                        </button>

                    </div>


                </form>

            </div>

        </div>

    </div>

    <script>
        function abrirModal(actividad) {
            console.log(actividad);
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

    @include('modals.detalle-actividad')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</x-app-layout>
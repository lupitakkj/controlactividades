<!-- MODAL DETALLE -->
<div id="detalleModal"
    class="hidden fixed inset-0 z-50 bg-black/60">

    <div class="flex items-center justify-center h-full p-6">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl
                    max-h-[85vh] overflow-hidden flex flex-col">
            <div class="overflow-y-auto p-8">
                <!-- CERRAR -->
                <button
                    onclick="cerrarModal()"
                    class="absolute top-5 right-5 text-2xl text-gray-500 hover:text-black">

                    <i class="fa-solid fa-xmark"></i>

                </button>

                <!-- TITULO -->
                <div class="border-b pb-6 mb-6">

                    <input
                        id="detalleTitulo"
                        class="w-full text-3xl font-bold border-0 focus:ring-0 p-0">

                    <div class="flex flex-wrap gap-3 mt-4">

                        <select
                            id="detallePrioridad"
                            class="rounded-full border px-4 py-2">

                            <option value="baja">Baja</option>
                            <option value="media">Media</option>
                            <option value="alta">Alta</option>
                            <option value="urgente">Urgente</option>

                        </select>

                        <span
                            id="detalleEstado"
                            class="px-4 py-2 rounded-full bg-blue-100 text-blue-700 font-medium">
                        </span>

                        <span
                            id="detalleComplejidad"
                            class="px-4 py-2 rounded-full bg-yellow-100 text-yellow-700 font-medium">
                        </span>

                    </div>

                    <!-- DATOS -->
                    <div class="grid grid-cols-4 gap-5 mt-6">

                        <div>

                            <p class="text-gray-500 text-sm">
                                Diseñador
                            </p>

                            <p id="detalleDisenador"
                                class="font-semibold">
                            </p>

                        </div>

                        <div>

                            <p class="text-gray-500 text-sm">
                                Creación
                            </p>

                            <p id="detalleCreacion"
                                class="font-semibold">
                            </p>

                        </div>

                        <div>

                            <p class="text-gray-500 text-sm">
                                Inicio
                            </p>

                            <p id="detalleInicio"
                                class="font-semibold">
                            </p>

                        </div>

                        <div>

                            <p class="text-gray-500 text-sm">
                                Entrega
                            </p>

                            <p id="detalleEntrega"
                                class="font-semibold">
                            </p>

                        </div>

                    </div>

                </div>

                <!-- DESCRIPCIÓN -->
                <div class="mb-5">

                    <h3 class="font-bold text-lg mb-3">

                        Descripción

                    </h3>

                    <textarea
                        id="detalleDescripcion"
                        rows="4"
                        class="w-full border rounded-xl p-4 resize-none"></textarea>

                </div>

                <!-- TIEMPOS -->
                <div class="mb-5">

                    <h3 class="font-bold text-lg mb-4">

                        Tiempo

                    </h3>

                    <div class="grid grid-cols-3 gap-4">

                        <div class="bg-slate-100 rounded-xl p-5 text-center">

                            <div class="text-3xl mb-2">
                                ⏱
                            </div>

                            <div class="text-gray-500 text-sm">

                                Trabajado

                            </div>

                            <div
                                id="detalleTiempo"
                                class="text-2xl font-bold text-blue-600">

                            </div>

                        </div>

                        <div class="bg-slate-100 rounded-xl p-5 text-center">

                            <div class="text-3xl mb-2">
                                ⌛
                            </div>

                            <div class="text-gray-500 text-sm">

                                Estimado

                            </div>

                            <div
                                id="detalleEstimado"
                                class="text-2xl font-bold">

                            </div>

                        </div>

                        <div class="bg-slate-100 rounded-xl p-5 text-center">

                            <div class="text-3xl mb-2">
                                📊
                            </div>

                            <div class="text-gray-500 text-sm">

                                Diferencia

                            </div>

                            <div
                                id="detalleDiferencia"
                                class="text-2xl font-bold">

                            </div>

                        </div>

                    </div>

                </div>

                <!-- ARCHIVOS -->
                <div class="mb-5">

                    <h3 class="font-bold text-lg mb-4">

                        Archivos

                    </h3>

                    <div
                        id="archivosContainer"
                        class="space-y-2 mb-4">

                    </div>

                    <form
                        id="archivoForm"
                        method="POST"
                        enctype="multipart/form-data">

                        @csrf

                        <input
                            type="file"
                            name="archivo"
                            class="mb-3">

                        <button
                            class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg">

                            Agregar archivo

                        </button>

                    </form>

                </div>

                <!-- COMENTARIOS -->
                <div class="mb-5">

                    <h3 class="font-bold text-lg mb-4">

                        Comentarios

                    </h3>

                    <div
                        id="comentariosContainer"
                        class="space-y-4 max-h-72 overflow-y-auto mb-4">

                    </div>

                    <form
                        id="comentarioForm"
                        method="POST"
                        enctype="multipart/form-data">

                        @csrf

                        <textarea
                            name="comentario"
                            rows="4"
                            class="w-full border rounded-xl p-3 mb-3"
                            placeholder="Escribe un comentario..."
                            required></textarea>

                        <input
                            type="file"
                            name="archivos[]"
                            multiple
                            class="mb-3">

                        <button
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">

                            Enviar comentario

                        </button>

                    </form>

                </div>

                @role('Administrador|Supervisor')

                <!-- REASIGNAR -->
                <div class="mb-5">

                    <h3 class="font-bold text-lg mb-4">

                        Reasignar diseñador

                    </h3>

                    <form
                        id="reasignarForm"
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

                            Reasignar diseñador

                        </button>

                    </form>

                </div>

                @endrole

                <hr class="my-8">

                <!-- BOTONES -->
                <div class="flex justify-end gap-3">

                    <button
                        type="button"
                        onclick="cerrarModal()"
                        class="px-5 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">

                        Cancelar

                    </button>

                    <button
                        id="guardarActividad"
                        type="button"
                        class="px-6 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white">

                        <i class="fa-solid fa-floppy-disk mr-2"></i>

                        Guardar cambios

                    </button>

                </div>

            </div>
        </div>
    </div>

</div>
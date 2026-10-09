<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Nuevo Ticket de Calidad
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Registra una queja o problema reportado por el cliente.
            </p>
        </div>
    </x-slot>

    <div class="py-6">

        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-xl shadow-sm border border-gray-100">

                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Información del ticket
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Completa la información relacionada con la queja.
                    </p>
                </div>


                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- PEDIDO --}}
                        <div>
                            <label for="pedido_id"
                                   class="block text-sm font-medium text-gray-700 mb-2">
                                Pedido
                            </label>

                            <select
                                id="pedido_id"
                                name="pedido_id"
                                class="w-full rounded-lg border-gray-300
                                       focus:border-blue-500 focus:ring-blue-500">

                                <option value="">
                                    Selecciona un pedido
                                </option>

                                @foreach($pedidos as $pedido)

                                    <option value="{{ $pedido->id }}">
                                        Pedido {{ $pedido->pedido_no }}
                                        @if($pedido->cliente)
                                            — {{ $pedido->cliente }}
                                        @endif
                                    </option>

                                @endforeach

                            </select>
                        </div>


                        {{-- PARTIDA --}}
                        <div>
                            <label for="partida_id"
                                   class="block text-sm font-medium text-gray-700 mb-2">
                                Partida afectada
                            </label>

                            <select
                                id="partida_id"
                                name="partida_id"
                                class="w-full rounded-lg border-gray-300
                                       focus:border-blue-500 focus:ring-blue-500">

                                <option value="">
                                    Selecciona primero un pedido
                                </option>

                            </select>
                        </div>


                        {{-- NOMBRE --}}
                        <div>
                            <label for="nombre"
                                   class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre del cliente
                            </label>

                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                class="w-full rounded-lg border-gray-300
                                       focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Nombre del cliente">
                        </div>


                        {{-- CORREO --}}
                        <div>
                            <label for="correo"
                                   class="block text-sm font-medium text-gray-700 mb-2">
                                Correo electrónico
                            </label>

                            <input
                                type="email"
                                id="correo"
                                name="correo"
                                class="w-full rounded-lg border-gray-300
                                       focus:border-blue-500 focus:ring-blue-500"
                                placeholder="correo@cliente.com">
                        </div>


                        {{-- PRIORIDAD --}}
                        <div>
                            <label for="prioridad"
                                   class="block text-sm font-medium text-gray-700 mb-2">
                                Prioridad
                            </label>

                            <select
                                id="prioridad"
                                name="prioridad"
                                class="w-full rounded-lg border-gray-300
                                       focus:border-blue-500 focus:ring-blue-500">

                                <option value="Media">
                                    Media
                                </option>

                                <option value="Alta">
                                    Alta
                                </option>

                                <option value="Baja">
                                    Baja
                                </option>

                            </select>
                        </div>


                        {{-- CANTIDAD --}}
                        <div>
                            <label for="cantidad_afectada"
                                   class="block text-sm font-medium text-gray-700 mb-2">
                                Cantidad afectada
                            </label>

                            <input
                                type="number"
                                step="0.0001"
                                min="0"
                                id="cantidad_afectada"
                                name="cantidad_afectada"
                                class="w-full rounded-lg border-gray-300
                                       focus:border-blue-500 focus:ring-blue-500"
                                placeholder="0">
                        </div>


                        {{-- TIPO --}}
                        <div>
                            <label for="tipo"
                                   class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de problema
                            </label>

                            <input
                                type="text"
                                id="tipo"
                                name="tipo"
                                class="w-full rounded-lg border-gray-300
                                       focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Ej. Calidad, daño, faltante...">
                        </div>


                        {{-- SUBTIPO --}}
                        <div>
                            <label for="subtipo"
                                   class="block text-sm font-medium text-gray-700 mb-2">
                                Subtipo
                            </label>

                            <input
                                type="text"
                                id="subtipo"
                                name="subtipo"
                                class="w-full rounded-lg border-gray-300
                                       focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Detalle del tipo de problema">
                        </div>

                    </div>


                    {{-- PROBLEMA --}}
                    <div class="mt-6">

                        <label for="problema"
                               class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción del problema
                        </label>

                        <textarea
                            id="problema"
                            name="problema"
                            rows="6"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Describe detalladamente la queja o problema reportado por el cliente..."></textarea>

                    </div>


                    {{-- BOTONES --}}
                    <div class="flex items-center justify-end gap-3 mt-6">

                        <a
                            href="{{ route('tickets-calidad.index') }}"
                            class="px-4 py-2 rounded-lg border border-gray-300
                                   text-gray-700 hover:bg-gray-50 transition">
                            Cancelar
                        </a>

                        <button
                            type="button"
                            class="inline-flex items-center gap-2
                                   px-5 py-2 rounded-lg
                                   bg-blue-600 text-white
                                   hover:bg-blue-700 transition">

                            <i class="fa-solid fa-ticket"></i>

                            Registrar ticket

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- DATOS DE PEDIDOS/PARTIDAS PARA EL SELECT --}}
    <script>

        const pedidos = @json(
            $pedidos->map(function ($pedido) {
                return [
                    'id' => $pedido->id,
                    'cliente' => $pedido->cliente,
                    'partidas' => $pedido->partidas->map(function ($partida) {
                        return [
                            'id' => $partida->id,
                            'clave' => $partida->clave,
                            'descripcion' => $partida->descripcion,
                            'cantidad' => $partida->cantidad,
                        ];
                    })->values(),
                ];
            })->values()
        );


        const pedidoSelect = document.getElementById('pedido_id');
        const partidaSelect = document.getElementById('partida_id');
        const nombreInput = document.getElementById('nombre');


        pedidoSelect.addEventListener('change', function () {

            const pedidoId = parseInt(this.value);

            partidaSelect.innerHTML = '';

            if (!pedidoId) {

                partidaSelect.innerHTML = `
                    <option value="">
                        Selecciona primero un pedido
                    </option>
                `;

                nombreInput.value = '';

                return;
            }


            const pedido = pedidos.find(
                p => p.id === pedidoId
            );


            if (!pedido) {
                return;
            }


            // Cliente del pedido
            nombreInput.value = pedido.cliente ?? '';


            // Partidas
            partidaSelect.innerHTML = `
                <option value="">
                    Selecciona una partida
                </option>
            `;


            pedido.partidas.forEach(partida => {

                const option = document.createElement('option');

                option.value = partida.id;

                option.textContent =
                    `${partida.clave ?? ''} - ${partida.descripcion ?? ''} (${partida.cantidad ?? 0})`;

                partidaSelect.appendChild(option);

            });

        });

    </script>

</x-app-layout>
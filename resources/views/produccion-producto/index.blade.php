<x-app-layout>

    <div class="w-full px-4 sm:px-6 lg:px-8 py-6">

        {{-- =========================================================
             ENCABEZADO
        ========================================================== --}}

        <div class="mb-6">

            <h1 class="text-2xl font-bold text-white">
                Producción por Producto
            </h1>

            <p class="mt-1 text-sm text-slate-400">
                Consulta de productos actualmente en producción.
            </p>

        </div>


        {{-- =========================================================
             FILTRO
        ========================================================== --}}

        {{-- =========================================================
     FILTRO
========================================================== --}}

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6">

            <form
                method="GET"
                action="{{ route('produccion.producto') }}"
                class="flex flex-col sm:flex-row sm:items-end gap-4">

                <div class="flex-1">

                    <label
                        for="buscarProducto"
                        class="block text-xs font-semibold text-slate-600 mb-1">
                        Buscar producto
                    </label>

                    <input
                        id="buscarProducto"
                        name="buscar"
                        type="text"
                        value="{{ $buscar }}"
                        placeholder="Buscar por clave, descripción o pedido..."
                        class="w-full rounded-lg border-slate-300
                       focus:border-blue-500
                       focus:ring-blue-500
                       text-sm">

                    <p class="mt-1 text-xs text-slate-400">
                        Ejemplo: TPOS, TPOS0701110, POSTE o número de pedido
                    </p>

                </div>


                {{-- BUSCAR --}}

                <button
                    type="submit"
                    class="px-5 py-2.5 rounded-lg
                   bg-blue-600 hover:bg-blue-700
                   text-white
                   text-sm font-semibold
                   transition">

                    <i class="fa-solid fa-magnifying-glass mr-1"></i>

                    Buscar

                </button>


                {{-- LIMPIAR --}}

                @if($buscar !== '')

                <a
                    href="{{ route('produccion.producto') }}"
                    class="px-5 py-2.5 rounded-lg
                       bg-slate-100 hover:bg-slate-200
                       text-slate-700
                       text-sm font-semibold
                       transition text-center">

                    Limpiar

                </a>

                @endif

            </form>

        </div>


        {{-- =========================================================
             KPI PRINCIPAL
        ========================================================== --}}

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">


            {{-- POSTES --}}

            <div
                class="bg-white rounded-xl border border-slate-200
                       shadow-sm p-5">

                <div class="flex items-start justify-between">

                    <div>

                        <p class="text-xs font-semibold uppercase
                                  tracking-wider text-slate-500">
                            Productos en producción
                        </p>

                        <p
                            id="totalPiezas"
                            class="mt-2 text-3xl font-bold text-slate-900">
                            {{ number_format($cantidadProducto, 0, '.', ',') }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            piezas
                        </p>

                    </div>

                    <div
                        class="w-11 h-11 rounded-xl
                               bg-blue-50
                               flex items-center justify-center">

                        <i class="fa-solid fa-industry text-blue-600"></i>

                    </div>

                </div>

            </div>


            {{-- IMPORTE --}}

            <div
                class="bg-white rounded-xl border border-slate-200
                       shadow-sm p-5">

                <p class="text-xs font-semibold uppercase
                          tracking-wider text-slate-500">
                    Importe en producción
                </p>

                <p
                    id="totalImporte"
                    class="mt-2 text-2xl font-bold text-slate-900">

                    {{ number_format($importeProducto, 2, '.', ',') }}

                </p>

                <p class="mt-1 text-xs text-slate-500">
                    valor de las partidas
                </p>

            </div>


            {{-- PARTIDAS --}}

            <div
                class="bg-white rounded-xl border border-slate-200
                       shadow-sm p-5">

                <p class="text-xs font-semibold uppercase
                          tracking-wider text-slate-500">
                    Partidas
                </p>

                <p
                    id="totalPartidas"
                    class="mt-2 text-3xl font-bold text-slate-900">

                    {{ number_format($partidasProducto, 0, '.', ',') }}

                </p>

                <p class="mt-1 text-xs text-slate-500">
                    partidas encontradas 
                </p>

            </div>


            {{-- PEDIDOS --}}

            <div
                class="bg-white rounded-xl border border-slate-200
                       shadow-sm p-5">

                <p class="text-xs font-semibold uppercase
                          tracking-wider text-slate-500">
                    Pedidos
                </p>

                <p
                    id="totalPedidos"
                    class="mt-2 text-3xl font-bold text-slate-900">

                    {{ number_format($pedidosProducto, 0, '.', ',') }}

                </p>

                <p class="mt-1 text-xs text-slate-500">
                    pedidos encontrados
                </p>

            </div>

        </div>


        {{-- =========================================================
             DETALLE
        ========================================================== --}}

        <div
            class="bg-white rounded-xl border border-slate-200
                   shadow-sm overflow-hidden">

            {{-- ENCABEZADO --}}

            <div
                class="px-5 py-4 border-b border-slate-200
                       flex flex-col sm:flex-row
                       sm:items-center sm:justify-between gap-2">

                <div>

                    <h2 class="text-base font-bold text-slate-800">
                        Productos actualmente en producción
                    </h2>

                    <p class="text-xs text-slate-500 mt-1">
                        Detalle de las partidas consideradas en el cálculo.
                    </p>

                </div>

                <div
                    id="contadorResultados"
                    class="text-xs font-semibold text-slate-500">

                    {{ $partidasProducto }} partidas

                </div>

            </div>


            {{-- TABLA --}}

            <div class="overflow-x-auto">

                <table
                    class="w-full text-sm"
                    id="tablaProduccionProducto">

                    <thead class="bg-slate-50 border-b border-slate-200">

                        <tr>

                            <th
                                class="px-4 py-3 text-left
                                       text-xs font-semibold
                                       text-slate-500 uppercase">
                                Pedido
                            </th>

                            <th
                                class="px-4 py-3 text-left
                                       text-xs font-semibold
                                       text-slate-500 uppercase">
                                Clave
                            </th>

                            <th
                                class="px-4 py-3 text-left
                                       text-xs font-semibold
                                       text-slate-500 uppercase">
                                Descripción
                            </th>

                            <th
                                class="px-4 py-3 text-right
                                       text-xs font-semibold
                                       text-slate-500 uppercase">
                                Cantidad
                            </th>

                            <th
                                class="px-4 py-3 text-right
                                       text-xs font-semibold
                                       text-slate-500 uppercase">
                                Precio unitario
                            </th>

                            <th
                                class="px-4 py-3 text-right
                                       text-xs font-semibold
                                       text-slate-500 uppercase">
                                Importe
                            </th>

                            <th
                                class="px-4 py-3 text-center
                                    text-xs font-semibold
                                    text-slate-500 uppercase">
                                Avance
                            </th>

                            <th
                                class="px-4 py-3 text-center
                                    text-xs font-semibold
                                    text-slate-500 uppercase whitespace-nowrap">
                                Proceso actual
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse($partidasEnProduccion as $partida)

                        <tr
                            class="hover:bg-slate-50 transition
                                       fila-producto"
                            data-busqueda="
                                    {{ strtolower(
                                        $partida->pedido_no . ' ' .
                                        $partida->clave . ' ' .
                                        $partida->descripcion
                                    ) }}
                                "
                            data-cantidad="{{ $partida->cantidad }}"
                            data-importe="{{ $partida->importe_total }}"
                            data-pedido="{{ $partida->pedido_id }}">
                            <td
                                class="px-4 py-3
                                           font-semibold text-slate-800
                                           whitespace-nowrap">

                                {{ ltrim((string) $partida->pedido_no, '0') ?: '0' }}

                            </td>

                            <td
                                class="px-4 py-3
                                           font-semibold text-blue-600
                                           whitespace-nowrap">

                                {{ $partida->clave }}

                            </td>

                            <td
                                class="px-4 py-3
                                           text-slate-600">

                                {{ $partida->descripcion }}

                            </td>

                            <td
                                class="px-4 py-3
                                           text-right font-semibold
                                           text-slate-800">

                                {{ number_format(
                                        $partida->cantidad,
                                        0,
                                        '.',
                                        ','
                                    ) }}

                            </td>

                            <td
                                class="px-4 py-3
                                           text-right text-slate-600
                                           whitespace-nowrap">

                                ${{ number_format(
                                        $partida->precio_unitario,
                                        2,
                                        '.',
                                        ','
                                    ) }}

                            </td>

                            <td
                                class="px-4 py-3
                                           text-right font-semibold
                                           text-slate-800
                                           whitespace-nowrap">

                                ${{ number_format(
                                        $partida->importe_total,
                                        2,
                                        '.',
                                        ','
                                    ) }}

                            </td>

                            <td class="px-4 py-3 text-center">

                                <div class="flex items-center justify-center gap-2">

                                    <div
                                        class="w-20 h-2 bg-slate-200 rounded-full overflow-hidden">

                                        <div
                                            class="h-full rounded-full bg-blue-500"
                                            style="width: {{ $partida->avance * 100 }}%;">
                                        </div>

                                    </div>

                                    <span class="text-xs font-semibold text-slate-700 whitespace-nowrap">

                                        {{ number_format(
                $partida->avance * 100,
                0
            ) }}%

                                    </span>

                                </div>

                            </td>

                            {{-- PROCESO ACTUAL --}}
                            <td class="px-4 py-3 text-center">

                                <span
                                    class="inline-flex items-center
                                           px-2.5 py-1 rounded-full
                                           bg-blue-50 text-blue-700
                                           text-xs font-semibold
                                           whitespace-nowrap">

                                    {{ $partida->proceso_actual }}

                                </span>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td
                                colspan="8"
                                class="px-4 py-10
                                           text-center text-slate-500">

                                No hay productos actualmente en producción.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- =========================================================
         FILTRO DE TABLA
    ========================================================== --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const buscador =
                document.getElementById('buscarProducto');

            const filas =
                document.querySelectorAll('.fila-producto');

            const contador =
                document.getElementById('contadorResultados');


            function filtrar() {

                const texto =
                    buscador.value
                        .trim()
                        .toLowerCase();

                let visibles = 0;
                let cantidadTotal = 0;
                let importeTotal = 0;
                const pedidos = new Set();

                filas.forEach(function(fila) {

                    const contenido =
                        fila.dataset.busqueda || '';

                    const coincide =
                        contenido.includes(texto);

                    fila.style.display =
                        coincide ? '' : 'none';

                    if (coincide) {

                        visibles++;

                        cantidadTotal +=
                            parseFloat(fila.dataset.cantidad || 0);

                        importeTotal +=
                            parseFloat(fila.dataset.importe || 0);

                        if (fila.dataset.pedido) {
                            pedidos.add(fila.dataset.pedido);
                        }
                    }

                });


                // Actualizar KPI en tiempo real
                document.getElementById('totalPiezas').textContent =
                    cantidadTotal.toLocaleString('en-US', {
                        maximumFractionDigits: 0
                    });

                document.getElementById('totalImporte').textContent =
                    '$' + importeTotal.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                document.getElementById('totalPartidas').textContent =
                    visibles.toLocaleString('en-US');

                document.getElementById('totalPedidos').textContent =
                    pedidos.size.toLocaleString('en-US');


                contador.textContent =
                    visibles +
                    (visibles === 1
                        ? ' partida'
                        : ' partidas');
            }


            // Filtrado inmediato mientras escribes
            buscador.addEventListener(
                'input',
                filtrar
            );

        });
    </script>

</x-app-layout>
<x-app-layout>

    {{-- ============================================================
         CONTENIDO
    ============================================================= --}}
    <div class="py-6">

        <div class="w-full px-4 sm:px-6 lg:px-8">

            <div class="bg-white rounded-xl shadow-sm border border-gray-200">


                {{-- ====================================================
                     CABECERA
                ===================================================== --}}
                <div class="px-5 py-4 border-b border-gray-200">

                    <div class="flex flex-col lg:flex-row
                                lg:items-center
                                lg:justify-between
                                gap-4">

                        <div>

                            <h3 class="text-lg font-semibold text-gray-800">
                                Historial de producción
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Pedidos con avance de producción al 100%.
                            </p>

                        </div>


                        {{-- ====================================================
                             CONTADOR
                        ===================================================== --}}
                        <div
                            class="px-4 py-3 rounded-lg
                                   bg-gray-50
                                   border border-gray-200">

                            <div
                                class="text-[10px]
                                       uppercase
                                       tracking-wide
                                       text-gray-400">

                                Pedidos terminados

                            </div>

                            <div
                                id="contadorTotal"
                                class="text-lg
                                       font-bold
                                       text-gray-700">

                                {{ $pedidosTerminados->count() }}

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ====================================================
                     FILTROS
                ===================================================== --}}
                <div class="px-5 pt-5">

                    <div
                        class="bg-gray-50
                               border border-gray-200
                               rounded-xl
                               p-4">

                        <div
                            class="grid
                                   grid-cols-1
                                   md:grid-cols-2
                                   lg:grid-cols-4
                                   gap-4">


                            {{-- =================================================
                                 BUSCAR
                            ================================================== --}}
                            <div>

                                <label
                                    class="block
                                           text-xs
                                           font-semibold
                                           text-gray-600
                                           mb-1">

                                    Buscar

                                </label>

                                <input
                                    type="text"
                                    id="filtroBuscar"
                                    placeholder="Pedido, cliente o su pedido..."
                                    class="w-full
                                           rounded-lg
                                           border-gray-300
                                           text-sm
                                           focus:border-blue-500
                                           focus:ring-blue-500">

                            </div>


                            {{-- =================================================
                                 CLIENTE
                            ================================================== --}}
                            <div>

                                <label
                                    class="block
                                           text-xs
                                           font-semibold
                                           text-gray-600
                                           mb-1">

                                    Cliente

                                </label>

                                <select
                                    id="filtroCliente"
                                    class="w-full
                                           rounded-lg
                                           border-gray-300
                                           text-sm
                                           focus:border-blue-500
                                           focus:ring-blue-500">

                                    <option value="">
                                        Todos los clientes
                                    </option>

                                    @php

                                        $clientes = $pedidosTerminados
                                            ->pluck('cliente')
                                            ->filter()
                                            ->unique()
                                            ->sort()
                                            ->values();

                                    @endphp

                                    @foreach($clientes as $cliente)

                                        <option
                                            value="{{ strtolower($cliente) }}">

                                            {{ $cliente }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            {{-- =================================================
                                 FECHA DESDE
                            ================================================== --}}
                            <div>

                                <label
                                    class="block
                                           text-xs
                                           font-semibold
                                           text-gray-600
                                           mb-1">

                                    Terminado desde

                                </label>

                                <input
                                    type="date"
                                    id="filtroFechaDesde"
                                    class="w-full
                                           rounded-lg
                                           border-gray-300
                                           text-sm
                                           focus:border-blue-500
                                           focus:ring-blue-500">

                            </div>


                            {{-- =================================================
                                 FECHA HASTA
                            ================================================== --}}
                            <div>

                                <label
                                    class="block
                                           text-xs
                                           font-semibold
                                           text-gray-600
                                           mb-1">

                                    Terminado hasta

                                </label>

                                <input
                                    type="date"
                                    id="filtroFechaHasta"
                                    class="w-full
                                           rounded-lg
                                           border-gray-300
                                           text-sm
                                           focus:border-blue-500
                                           focus:ring-blue-500">

                            </div>

                        </div>


                        {{-- =================================================
                             PIE DE FILTROS
                        ================================================== --}}
                        <div
                            class="flex
                                   flex-col
                                   sm:flex-row
                                   sm:items-center
                                   sm:justify-between
                                   gap-3
                                   mt-4">


                            {{-- CONTADOR --}}
                            <div
                                id="contadorResultados"
                                class="text-xs
                                       text-gray-500">

                                Mostrando
                                {{ $pedidosTerminados->count() }}
                                pedidos

                            </div>


                            {{-- LIMPIAR --}}
                            <button
                                type="button"
                                id="btnLimpiarFiltros"
                                class="px-4
                                       py-2
                                       rounded-lg
                                       bg-white
                                       hover:bg-gray-100
                                       text-gray-700
                                       text-sm
                                       font-semibold
                                       border
                                       border-gray-300
                                       transition">

                                ↺ Limpiar filtros

                            </button>

                        </div>

                    </div>

                </div>


                {{-- ====================================================
                     TABLA
                ===================================================== --}}
                <div class="p-5">

                    <div
                        class="overflow-x-auto overflow-y-auto"
                        style="height: 550px;">

                        <table
                            id="tablaPedidosTerminados"
                            class="w-full
                                   text-sm
                                   border-collapse">


                            {{-- =================================================
                                 ENCABEZADOS
                            ================================================== --}}
                            <thead class="sticky top-0 z-20 bg-gray-50">

                                <tr
                                    class="bg-gray-50
                                           border-b
                                           border-gray-200">


                                    {{-- =================================================
                                         PEDIDO
                                    ================================================== --}}
                                    <th
                                        data-sort="0"
                                        class="px-4
                                               py-3
                                               text-center
                                               text-xs
                                               font-semibold
                                               text-gray-600
                                               whitespace-nowrap
                                               cursor-pointer
                                               hover:bg-gray-100
                                               select-none">

                                        <div
                                            class="flex
                                                   items-center
                                                   justify-center
                                                   gap-1">

                                            PEDIDO

                                            <span
                                                class="sort-arrow
                                                       text-gray-400">

                                                ↕
                                            </span>

                                        </div>

                                    </th>


                                    {{-- =================================================
                                         CLIENTE
                                    ================================================== --}}
                                    <th
                                        data-sort="1"
                                        class="px-4
                                               py-3
                                               text-left
                                               text-xs
                                               font-semibold
                                               text-gray-600
                                               whitespace-nowrap
                                               cursor-pointer
                                               hover:bg-gray-100
                                               select-none">

                                        <div
                                            class="flex
                                                   items-center
                                                   gap-1">

                                            CLIENTE

                                            <span
                                                class="sort-arrow
                                                       text-gray-400">

                                                ↕

                                            </span>

                                        </div>

                                    </th>


                                    {{-- =================================================
                                         SU PEDIDO
                                    ================================================== --}}
                                    <th
                                        data-sort="2"
                                        class="px-4
                                               py-3
                                               text-left
                                               text-xs
                                               font-semibold
                                               text-gray-600
                                               whitespace-nowrap
                                               cursor-pointer
                                               hover:bg-gray-100
                                               select-none">

                                        <div
                                            class="flex
                                                   items-center
                                                   gap-1">

                                            SU PEDIDO

                                            <span
                                                class="sort-arrow
                                                       text-gray-400">

                                                ↕

                                            </span>

                                        </div>

                                    </th>


                                    {{-- =================================================
                                         PARTIDAS
                                    ================================================== --}}
                                    <th
                                        data-sort="3"
                                        class="px-4
                                               py-3
                                               text-center
                                               text-xs
                                               font-semibold
                                               text-gray-600
                                               whitespace-nowrap
                                               cursor-pointer
                                               hover:bg-gray-100
                                               select-none">

                                        <div
                                            class="flex
                                                   items-center
                                                   justify-center
                                                   gap-1">

                                            PARTIDAS

                                            <span
                                                class="sort-arrow
                                                       text-gray-400">

                                                ↕

                                            </span>

                                        </div>

                                    </th>


                                    {{-- =================================================
                                         AVANCE
                                    ================================================== --}}
                                    <th
                                        data-sort="4"
                                        class="px-4
                                               py-3
                                               text-center
                                               text-xs
                                               font-semibold
                                               text-gray-600
                                               whitespace-nowrap
                                               cursor-pointer
                                               hover:bg-gray-100
                                               select-none">

                                        <div
                                            class="flex
                                                   items-center
                                                   justify-center
                                                   gap-1">

                                            AVANCE

                                            <span
                                                class="sort-arrow
                                                       text-gray-400">

                                                ↕

                                            </span>

                                        </div>

                                    </th>


                                    {{-- =================================================
                                         FECHA SOLICITUD
                                    ================================================== --}}
                                    <th
                                        data-sort="5"
                                        class="px-4
                                               py-3
                                               text-center
                                               text-xs
                                               font-semibold
                                               text-gray-600
                                               whitespace-nowrap
                                               cursor-pointer
                                               hover:bg-gray-100
                                               select-none">

                                        <div
                                            class="flex
                                                   items-center
                                                   justify-center
                                                   gap-1">

                                            FECHA SOLICITUD

                                            <span
                                                class="sort-arrow
                                                       text-gray-400">

                                                ↕

                                            </span>

                                        </div>

                                    </th>


                                    {{-- =================================================
                                         FECHA ENTREGA
                                    ================================================== --}}
                                    <th
                                        data-sort="6"
                                        class="px-4
                                               py-3
                                               text-center
                                               text-xs
                                               font-semibold
                                               text-gray-600
                                               whitespace-nowrap
                                               cursor-pointer
                                               hover:bg-gray-100
                                               select-none">

                                        <div
                                            class="flex
                                                   items-center
                                                   justify-center
                                                   gap-1">

                                            FECHA ENTREGA

                                            <span
                                                class="sort-arrow
                                                       text-gray-400">

                                                ↕

                                            </span>

                                        </div>

                                    </th>


                                    {{-- =================================================
                                         FECHA TERMINADO
                                    ================================================== --}}
                                    <th
                                        data-sort="7"
                                        class="px-4
                                               py-3
                                               text-center
                                               text-xs
                                               font-semibold
                                               text-gray-600
                                               whitespace-nowrap
                                               cursor-pointer
                                               hover:bg-gray-100
                                               select-none">

                                        <div
                                            class="flex
                                                   items-center
                                                   justify-center
                                                   gap-1">

                                            FECHA TERMINADO

                                            <span
                                                class="sort-arrow
                                                       text-gray-400">

                                                ↕

                                            </span>

                                        </div>

                                    </th>


                                    {{-- =================================================
                                         DÍAS
                                    ================================================== --}}
                                    <th
                                        data-sort="8"
                                        class="px-4
                                               py-3
                                               text-center
                                               text-xs
                                               font-semibold
                                               text-gray-600
                                               whitespace-nowrap
                                               cursor-pointer
                                               hover:bg-gray-100
                                               select-none">

                                        <div
                                            class="flex
                                                   items-center
                                                   justify-center
                                                   gap-1">

                                            DÍAS

                                            <span
                                                class="sort-arrow
                                                       text-gray-400">

                                                ↕

                                            </span>

                                        </div>

                                    </th>

                                </tr>

                            </thead>


                            {{-- =================================================
                                 CUERPO
                            ================================================== --}}
                            <tbody>

                                @forelse(
                                    $pedidosTerminados
                                    as $pedido
                                )


                                    <tr
                                        class="pedido-row
                                               border-b
                                               border-gray-100
                                               hover:bg-gray-50"

                                        data-pedido="{{ strtolower($pedido->pedido_no) }}"

                                        data-cliente="{{ strtolower($pedido->cliente ?? '') }}"

                                        data-su-pedido="{{ strtolower($pedido->su_pedido ?? '') }}"

                                        data-fecha-solicitud="{{ $pedido->fecha_solicitud ? $pedido->fecha_solicitud->format('Y-m-d') : '' }}"

                                        data-fecha-entrega="{{ $pedido->fecha_entrega ? $pedido->fecha_entrega->format('Y-m-d') : '' }}"

                                        data-fecha-terminado="{{ $pedido->fecha_terminado ? $pedido->fecha_terminado->format('Y-m-d') : '' }}"

                                        data-dias="{{ $pedido->dias_diferencia ?? '' }}">


                                        {{-- =================================================
                                             PEDIDO
                                        ================================================== --}}
                                        <td
                                            class="px-4
                                                   py-3
                                                   text-center
                                                   font-semibold
                                                   text-gray-800
                                                   whitespace-nowrap">

                                            {{ $pedido->pedido_no }}

                                        </td>


                                        {{-- =================================================
                                             CLIENTE
                                        ================================================== --}}
                                        <td
                                            class="px-4
                                                   py-3
                                                   text-gray-700">

                                            {{ $pedido->cliente ?: '—' }}

                                        </td>


                                        {{-- =================================================
                                             SU PEDIDO
                                        ================================================== --}}
                                        <td
                                            class="px-4
                                                   py-3
                                                   text-gray-700
                                                   whitespace-nowrap">

                                            {{ $pedido->su_pedido ?: '—' }}

                                        </td>


                                        {{-- =================================================
                                             PARTIDAS
                                        ================================================== --}}
                                        <td
                                            class="px-4
                                                   py-3
                                                   text-center
                                                   font-medium
                                                   text-gray-700">

                                            {{ $pedido->partidas->count() }}

                                        </td>


                                        {{-- =================================================
                                             AVANCE
                                        ================================================== --}}
                                        <td
                                            class="px-4
                                                   py-3
                                                   text-center">

                                            <span
                                                class="inline-flex
                                                       items-center
                                                       justify-center
                                                       min-w-[64px]
                                                       px-3
                                                       py-1
                                                       rounded-md
                                                       bg-green-100
                                                       text-green-700
                                                       font-bold
                                                       text-xs">

                                                100.0%

                                            </span>

                                        </td>


                                        {{-- =================================================
                                             FECHA SOLICITUD
                                        ================================================== --}}
                                        <td
                                            class="px-4
                                                   py-3
                                                   text-center
                                                   text-gray-700
                                                   whitespace-nowrap">

                                            @if($pedido->fecha_solicitud)

                                                {{ $pedido->fecha_solicitud->format('d/m/Y') }}

                                            @else

                                                —

                                            @endif

                                        </td>


                                        {{-- =================================================
                                             FECHA ENTREGA
                                        ================================================== --}}
                                        <td
                                            class="px-4
                                                   py-3
                                                   text-center
                                                   text-gray-700
                                                   whitespace-nowrap">

                                            @if($pedido->fecha_entrega)

                                                {{ $pedido->fecha_entrega->format('d/m/Y') }}

                                            @else

                                                —

                                            @endif

                                        </td>


                                        {{-- =================================================
                                             FECHA TERMINADO
                                        ================================================== --}}
                                        <td
                                            class="px-4
                                                   py-3
                                                   text-center
                                                   text-gray-700
                                                   whitespace-nowrap">

                                            @if($pedido->fecha_terminado)

                                                {{ $pedido->fecha_terminado->format('d/m/Y') }}

                                            @else

                                                —

                                            @endif

                                        </td>


                                        {{-- =================================================
                                             DÍAS
                                        ================================================== --}}
                                        <td
                                            class="px-4
                                                   py-3
                                                   text-center
                                                   whitespace-nowrap">

                                            @if($pedido->dias_diferencia !== null)

                                                <span
                                                    class="inline-flex
                                                           items-center
                                                           justify-center
                                                           min-w-[45px]
                                                           px-2
                                                           py-1
                                                           rounded-md
                                                           bg-blue-50
                                                           text-blue-700
                                                           font-bold
                                                           text-xs">

                                                    {{ $pedido->dias_diferencia }}

                                                </span>

                                            @else

                                                <span
                                                    class="text-gray-400">

                                                    —

                                                </span>

                                            @endif

                                        </td>

                                    </tr>


                                @empty


                                    <tr>

                                        <td
                                            colspan="9"
                                            class="px-4
                                                   py-12
                                                   text-center
                                                   text-gray-400">

                                            <div class="text-3xl mb-2">
                                                ✓
                                            </div>

                                            <div
                                                class="font-medium
                                                       text-gray-500">

                                                No hay pedidos terminados.

                                            </div>

                                            <div
                                                class="text-xs
                                                       mt-1">

                                                Los pedidos aparecerán aquí
                                                cuando alcancen el 100%.

                                            </div>

                                        </td>

                                    </tr>


                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>



<style>
    #tablaPedidosTerminados thead th {
        background-color: #f9fafb;
        position: sticky;
        top: 0;
        z-index: 20;
    }

    #tablaPedidosTerminados {
        min-width: 1100px;
    }
</style>

    {{-- ================================================================
         JAVASCRIPT
    ================================================================= --}}
    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const tabla =
                document.getElementById(
                    'tablaPedidosTerminados'
                );

            if (!tabla) {
                return;
            }


            const tbody =
                tabla.querySelector('tbody');


            const filtroBuscar =
                document.getElementById(
                    'filtroBuscar'
                );


            const filtroCliente =
                document.getElementById(
                    'filtroCliente'
                );


            const filtroFechaDesde =
                document.getElementById(
                    'filtroFechaDesde'
                );


            const filtroFechaHasta =
                document.getElementById(
                    'filtroFechaHasta'
                );


            const btnLimpiar =
                document.getElementById(
                    'btnLimpiarFiltros'
                );


            const contadorResultados =
                document.getElementById(
                    'contadorResultados'
                );


            const contadorTotal =
                document.getElementById(
                    'contadorTotal'
                );


            /*
            |--------------------------------------------------------------------------
            | FILTRAR
            |--------------------------------------------------------------------------
            */

            function aplicarFiltros() {

                const buscar =
                    filtroBuscar.value
                        .toLowerCase()
                        .trim();


                const cliente =
                    filtroCliente.value
                        .toLowerCase();


                const fechaDesde =
                    filtroFechaDesde.value;


                const fechaHasta =
                    filtroFechaHasta.value;


                const filas =
                    tbody.querySelectorAll(
                        '.pedido-row'
                    );


                let visibles = 0;


                filas.forEach(function (fila) {

                    const pedido =
                        fila.dataset.pedido || '';


                    const clienteFila =
                        fila.dataset.cliente || '';


                    const suPedido =
                        fila.dataset.suPedido || '';


                    const fechaTerminado =
                        fila.dataset.fechaTerminado || '';


                    /*
                    |--------------------------------------------------------------
                    | BUSCAR
                    |--------------------------------------------------------------
                    */

                    const coincideBusqueda =
                        buscar === '' ||

                        pedido.includes(buscar) ||

                        clienteFila.includes(buscar) ||

                        suPedido.includes(buscar);


                    /*
                    |--------------------------------------------------------------
                    | CLIENTE
                    |--------------------------------------------------------------
                    */

                    const coincideCliente =
                        cliente === '' ||

                        clienteFila === cliente;


                    /*
                    |--------------------------------------------------------------
                    | FECHA DESDE
                    |--------------------------------------------------------------
                    */

                    const coincideDesde =
                        fechaDesde === '' ||

                        (
                            fechaTerminado !== '' &&
                            fechaTerminado >= fechaDesde
                        );


                    /*
                    |--------------------------------------------------------------
                    | FECHA HASTA
                    |--------------------------------------------------------------
                    */

                    const coincideHasta =
                        fechaHasta === '' ||

                        (
                            fechaTerminado !== '' &&
                            fechaTerminado <= fechaHasta
                        );


                    /*
                    |--------------------------------------------------------------
                    | RESULTADO
                    |--------------------------------------------------------------
                    */

                    const mostrar =
                        coincideBusqueda &&
                        coincideCliente &&
                        coincideDesde &&
                        coincideHasta;


                    if (mostrar) {

                        fila.style.display = '';

                        visibles++;

                    } else {

                        fila.style.display = 'none';

                    }

                });


                contadorResultados.textContent =
                    `Mostrando ${visibles} pedidos`;

            }


            /*
            |--------------------------------------------------------------------------
            | EVENTOS DE FILTROS
            |--------------------------------------------------------------------------
            */

            filtroBuscar.addEventListener(
                'input',
                aplicarFiltros
            );


            filtroCliente.addEventListener(
                'change',
                aplicarFiltros
            );


            filtroFechaDesde.addEventListener(
                'change',
                aplicarFiltros
            );


            filtroFechaHasta.addEventListener(
                'change',
                aplicarFiltros
            );


            /*
            |--------------------------------------------------------------------------
            | LIMPIAR FILTROS
            |--------------------------------------------------------------------------
            */

            btnLimpiar.addEventListener(
                'click',
                function () {

                    filtroBuscar.value = '';

                    filtroCliente.value = '';

                    filtroFechaDesde.value = '';

                    filtroFechaHasta.value = '';

                    aplicarFiltros();

                }
            );


            /*
            |--------------------------------------------------------------------------
            | ORDENAMIENTO
            |--------------------------------------------------------------------------
            */

            const encabezados =
                tabla.querySelectorAll(
                    'th[data-sort]'
                );


            let columnaActual = null;

            let direccionActual = 'asc';


            encabezados.forEach(function (encabezado) {

                encabezado.addEventListener(
                    'click',
                    function () {

                        const columna =
                            parseInt(
                                encabezado.dataset.sort
                            );


                        /*
                        |----------------------------------------------------------
                        | CAMBIAR DIRECCIÓN
                        |----------------------------------------------------------
                        */

                        if (
                            columnaActual === columna
                        ) {

                            direccionActual =
                                direccionActual === 'asc'
                                    ? 'desc'
                                    : 'asc';

                        } else {

                            columnaActual = columna;

                            direccionActual = 'asc';

                        }


                        /*
                        |----------------------------------------------------------
                        | ACTUALIZAR FLECHAS
                        |----------------------------------------------------------
                        */

                        encabezados.forEach(
                            function (th) {

                                const flecha =
                                    th.querySelector(
                                        '.sort-arrow'
                                    );

                                if (flecha) {

                                    flecha.textContent = '↕';

                                    flecha.classList.remove(
                                        'text-blue-600'
                                    );

                                    flecha.classList.add(
                                        'text-gray-400'
                                    );

                                }

                            }
                        );


                        const flecha =
                            encabezado.querySelector(
                                '.sort-arrow'
                            );


                        if (flecha) {

                            flecha.textContent =
                                direccionActual === 'asc'
                                    ? '↑'
                                    : '↓';


                            flecha.classList.remove(
                                'text-gray-400'
                            );


                            flecha.classList.add(
                                'text-blue-600'
                            );

                        }


                        /*
                        |----------------------------------------------------------
                        | OBTENER FILAS
                        |----------------------------------------------------------
                        */

                        const filas =
                            Array.from(
                                tbody.querySelectorAll(
                                    '.pedido-row'
                                )
                            );


                        /*
                        |----------------------------------------------------------
                        | ORDENAR
                        |----------------------------------------------------------
                        */

                        filas.sort(
                            function (a, b) {

                                let valorA =
                                    obtenerValor(
                                        a,
                                        columna
                                    );


                                let valorB =
                                    obtenerValor(
                                        b,
                                        columna
                                    );


                                /*
                                |----------------------------------------------
                                | PEDIDO
                                |----------------------------------------------
                                */

                                if (columna === 0) {

                                    valorA =
                                        parseFloat(
                                            a.dataset.pedido
                                        ) || 0;


                                    valorB =
                                        parseFloat(
                                            b.dataset.pedido
                                        ) || 0;

                                }


                                /*
                                |----------------------------------------------
                                | PARTIDAS
                                |----------------------------------------------
                                */

                                else if (columna === 3) {

                                    valorA =
                                        parseFloat(
                                            valorA
                                        ) || 0;


                                    valorB =
                                        parseFloat(
                                            valorB
                                        ) || 0;

                                }


                                /*
                                |----------------------------------------------
                                | AVANCE
                                |----------------------------------------------
                                */

                                else if (columna === 4) {

                                    valorA = 100;

                                    valorB = 100;

                                }


                                /*
                                |----------------------------------------------
                                | FECHAS
                                |----------------------------------------------
                                */

                                else if (
                                    columna === 5 ||
                                    columna === 6 ||
                                    columna === 7
                                ) {

                                    const fechaA =
                                        obtenerFecha(
                                            a,
                                            columna
                                        );


                                    const fechaB =
                                        obtenerFecha(
                                            b,
                                            columna
                                        );


                                    valorA = fechaA;

                                    valorB = fechaB;

                                }


                                /*
                                |----------------------------------------------
                                | DÍAS
                                |----------------------------------------------
                                */

                                else if (columna === 8) {

                                    valorA =
                                        parseFloat(
                                            a.dataset.dias
                                        );

                                    valorB =
                                        parseFloat(
                                            b.dataset.dias
                                        );


                                    if (
                                        isNaN(valorA)
                                    ) {
                                        valorA = 0;
                                    }


                                    if (
                                        isNaN(valorB)
                                    ) {
                                        valorB = 0;
                                    }

                                }


                                /*
                                |----------------------------------------------
                                | TEXTO
                                |----------------------------------------------
                                */

                                else {

                                    valorA =
                                        valorA.toLowerCase();

                                    valorB =
                                        valorB.toLowerCase();

                                }


                                if (valorA < valorB) {

                                    return direccionActual === 'asc'
                                        ? -1
                                        : 1;

                                }


                                if (valorA > valorB) {

                                    return direccionActual === 'asc'
                                        ? 1
                                        : -1;

                                }


                                return 0;

                            }
                        );


                        /*
                        |----------------------------------------------------------
                        | REINSERTAR FILAS
                        |----------------------------------------------------------
                        */

                        filas.forEach(
                            function (fila) {

                                tbody.appendChild(
                                    fila
                                );

                            }
                        );

                    }
                );

            });


            /*
            |--------------------------------------------------------------------------
            | OBTENER VALOR
            |--------------------------------------------------------------------------
            */

            function obtenerValor(
                fila,
                columna
            ) {

                const celda =
                    fila.children[columna];


                if (!celda) {

                    return '';

                }


                return celda.textContent
                    .trim();

            }


            /*
            |--------------------------------------------------------------------------
            | OBTENER FECHA
            |--------------------------------------------------------------------------
            */

            function obtenerFecha(
                fila,
                columna
            ) {

                let fecha = '';


                /*
                |--------------------------------------------------------------
                | FECHA SOLICITUD
                |--------------------------------------------------------------
                */

                if (columna === 5) {

                    fecha =
                        fila.dataset.fechaSolicitud || '';

                }


                /*
                |--------------------------------------------------------------
                | FECHA ENTREGA
                |--------------------------------------------------------------
                */

                if (columna === 6) {

                    fecha =
                        fila.dataset.fechaEntrega || '';

                }


                /*
                |--------------------------------------------------------------
                | FECHA TERMINADO
                |--------------------------------------------------------------
                */

                if (columna === 7) {

                    fecha =
                        fila.dataset.fechaTerminado || '';

                }


                if (!fecha) {

                    return 0;

                }


                return new Date(
                    fecha + 'T00:00:00'
                ).getTime();

            }

        });

    </script>

</x-app-layout>
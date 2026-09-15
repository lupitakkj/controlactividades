<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Control Operativo') }}
        </h2>
    </x-slot>

    <div class="py-6">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ENCABEZADO --}}
            <div class="bg-white overflow-visible shadow-sm sm:rounded-lg mb-6 relative">

                <div class="p-6">

                    <div class="flex items-center justify-between">

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Control Operativo
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Seguimiento de pedidos y producción.
                            </p>
                        </div>

                        <div class="flex items-center gap-4">

                            <div class="text-sm text-gray-500">
                                {{ $pedidos->count() }} pedidos
                            </div>

                            {{-- BOTÓN COLUMNAS --}}
                            <div class="relative">

                                <button
                                    type="button"
                                    id="btnColumnas"
                                    class="inline-flex items-center gap-2 px-4 py-2
                                           bg-gray-100 hover:bg-gray-200
                                           text-gray-700 text-sm font-medium
                                           rounded-lg border border-gray-300">
                                    ⚙️ Columnas
                                </button>

                                {{-- PANEL DE COLUMNAS --}}
                                <div
                                    id="panelColumnas"
                                    class="hidden absolute right-0 mt-2 w-72
                                           bg-white border border-gray-200
                                           rounded-lg shadow-xl z-[100]">

                                    <div class="p-4">

                                        <div class="flex items-center justify-between mb-4">

                                            <h4 class="font-semibold text-gray-800">
                                                Mostrar columnas
                                            </h4>

                                            <button
                                                type="button"
                                                id="cerrarColumnas"
                                                class="text-gray-400 hover:text-gray-700 text-lg">
                                                ✕
                                            </button>

                                        </div>

                                        <div class="space-y-3">

                                            {{-- PEDIDO --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="pedido"
                                                    checked>

                                                <span class="text-sm text-gray-700">
                                                    Pedido
                                                </span>

                                            </label>


                                            {{-- CLIENTE --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="cliente"
                                                    checked>

                                                <span class="text-sm text-gray-700">
                                                    Cliente
                                                </span>

                                            </label>


                                            {{-- SU PEDIDO --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="su-pedido"
                                                    checked>

                                                <span class="text-sm text-gray-700">
                                                    Su Pedido
                                                </span>

                                            </label>


                                            {{-- ENVIAR A --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="enviar-a">

                                                <span class="text-sm text-gray-700">
                                                    Enviar a
                                                </span>

                                            </label>


                                            {{-- IMPORTE --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="importe"
                                                    checked>

                                                <span class="text-sm text-gray-700">
                                                    Importe
                                                </span>

                                            </label>


                                            {{-- FECHA ENTREGA --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="fecha-entrega"
                                                    checked>

                                                <span class="text-sm text-gray-700">
                                                    Fecha Entrega
                                                </span>

                                            </label>


                                            {{-- PRIORIDAD --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="prioridad"
                                                    checked>

                                                <span class="text-sm text-gray-700">
                                                    Prioridad
                                                </span>

                                            </label>


                                            {{-- FECHA PRODUCCIÓN --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="fecha-produccion"
                                                    checked>

                                                <span class="text-sm text-gray-700">
                                                    Fecha Producción
                                                </span>

                                            </label>


                                            {{-- ESTADO --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="estado"
                                                    checked>

                                                <span class="text-sm text-gray-700">
                                                    Estado
                                                </span>

                                            </label>


                                            {{-- SEMÁFORO --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="semaforo">

                                                <span class="text-sm text-gray-700">
                                                    Semáforo
                                                </span>

                                            </label>


                                            {{-- DÍAS RESTANTES --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="dias-restantes">

                                                <span class="text-sm text-gray-700">
                                                    Días Restantes
                                                </span>

                                            </label>


                                            {{-- DÍAS ATRASO --}}
                                            <label class="flex items-center gap-3 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    class="columna-check w-4 h-4 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                                                    style="appearance: auto; -webkit-appearance: checkbox;"
                                                    data-columna="dias-atraso">

                                                <span class="text-sm text-gray-700">
                                                    Días Atraso
                                                </span>

                                            </label>

                                        </div>


                                        {{-- BOTÓN APLICAR --}}
                                        <div class="mt-5 pt-4 border-t border-gray-200">

                                            <button
                                                type="button"
                                                id="aplicarColumnas"
                                                class="w-full px-4 py-2
                                                       bg-blue-600 hover:bg-blue-700
                                                       text-white text-sm font-medium
                                                       rounded-lg">
                                                Aplicar
                                            </button>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- TABLA --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6">

                    <div class="overflow-x-auto">

                        <table id="tablaPedidos" class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">

                                <tr>

                                    <th
                                        data-columna="pedido"
                                        class="col-pedido px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                        Pedido
                                    </th>

                                    <th
                                        data-columna="cliente"
                                        class="col-cliente px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                        Cliente
                                    </th>

                                    <th
                                        data-columna="su-pedido"
                                        class="col-su-pedido px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                        Su Pedido
                                    </th>

                                    <th
                                        data-columna="enviar-a"
                                        class="col-enviar-a px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                        Enviar a
                                    </th>

                                    <th
                                        data-columna="importe"
                                        class="col-importe px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                                        Importe
                                    </th>

                                    <th
                                        data-columna="fecha-entrega"
                                        class="col-fecha-entrega px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                        Fecha Entrega
                                    </th>

                                    <th
                                        data-columna="prioridad"
                                        class="col-prioridad px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                        Prioridad
                                    </th>

                                    <th
                                        data-columna="fecha-produccion"
                                        class="col-fecha-produccion px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                        Fecha Producción
                                    </th>

                                    <th
                                        data-columna="estado"
                                        class="col-estado px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                        Estado
                                    </th>

                                    <th
                                        data-columna="semaforo"
                                        class="col-semaforo px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                        Semáforo
                                    </th>

                                    <th
                                        data-columna="dias-restantes"
                                        class="col-dias-restantes px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                        Días Restantes
                                    </th>

                                    <th
                                        data-columna="dias-atraso"
                                        class="col-dias-atraso px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                        Días Atraso
                                    </th>

                                </tr>

                            </thead>


                            <tbody class="bg-white divide-y divide-gray-200">

                                @forelse($pedidos as $pedido)

                                @php

                                $control = $pedido->controlOperativo;

                                $prioridad = $control?->prioridad;

                                $fechaProduccion = $control?->fecha_produccion;

                                $estado = $control?->estado_operativo ?? 'Por Iniciar';

                                $diasRestantes = 0;

                                $diasAtraso = 0;


                                /*
                                |--------------------------------------------------------------------------
                                | SEMÁFORO
                                |--------------------------------------------------------------------------
                                */

                                if (strtoupper($estado) === 'TERMINADO') {

                                $semaforo = 'TERMINADO';

                                } elseif (strtoupper($estado) === 'CANCELADO') {

                                $semaforo = 'CANCELADO';

                                } elseif (!$fechaProduccion) {

                                $semaforo = 'SIN FECHA';

                                } else {

                                $dias = now()->startOfDay()
                                ->diffInDays(
                                $fechaProduccion,
                                false
                                );

                                if ($dias < 0) {

                                    $diasAtraso=abs($dias);

                                    $diasRestantes=0;

                                    $semaforo='ATRASADO' ;

                                    } else {

                                    $diasRestantes=$dias;

                                    $diasAtraso=0;

                                    if ($dias <=7) {

                                    $semaforo='EN RIESGO' ;

                                    } else {

                                    $semaforo='EN TIEMPO' ;

                                    }

                                    }

                                    }

                                    @endphp


                                    <tr class="hover:bg-gray-50">


                                    {{-- PEDIDO --}}
                                    <td
                                        data-columna="pedido"
                                        class="col-pedido px-4 py-4 whitespace-nowrap">

                                        <div class="font-semibold text-gray-900">
                                            {{ $pedido->pedido_no }}
                                        </div>

                                    </td>


                                    {{-- CLIENTE --}}
                                    <td
                                        data-columna="cliente"
                                        class="col-cliente px-4 py-4 whitespace-nowrap text-sm text-gray-700">

                                        {{ $pedido->cliente ?? '—' }}

                                    </td>


                                    {{-- SU PEDIDO --}}
                                    <td
                                        data-columna="su-pedido"
                                        class="col-su-pedido px-4 py-4 whitespace-nowrap text-sm text-gray-700">

                                        {{ $pedido->su_pedido ?? '—' }}

                                    </td>


                                    {{-- ENVIAR A --}}
                                    <td
                                        data-columna="enviar-a"
                                        class="col-enviar-a px-4 py-4 whitespace-nowrap text-sm text-gray-700">

                                        {{ $pedido->enviar_a ?? '—' }}

                                    </td>


                                    {{-- IMPORTE --}}
                                    <td
                                        data-columna="importe"
                                        class="col-importe px-4 py-4 whitespace-nowrap text-sm text-gray-700 text-right">

                                        ${{ number_format((float) $pedido->importe_total, 2) }}

                                    </td>


                                    {{-- FECHA ENTREGA --}}
                                    <td
                                        data-columna="fecha-entrega"
                                        class="col-fecha-entrega px-4 py-4 whitespace-nowrap text-sm text-gray-700 text-center">

                                        {{ $pedido->fecha_entrega
                                                ? $pedido->fecha_entrega->format('d/m/Y')
                                                : '—'
                                            }}

                                    </td>


                                    {{-- PRIORIDAD --}}
                                    <td
                                        data-columna="prioridad"
                                        class="col-prioridad px-4 py-4 whitespace-nowrap text-sm text-gray-700 text-center">

                                        {{ $prioridad ?? '—' }}

                                    </td>


                                    {{-- FECHA PRODUCCIÓN --}}
                                    <td
                                        data-columna="fecha-produccion"
                                        class="col-fecha-produccion px-4 py-4 whitespace-nowrap text-sm text-gray-700 text-center">

                                        {{ $fechaProduccion
                                                ? $fechaProduccion->format('d/m/Y')
                                                : '—'
                                            }}

                                    </td>


                                    {{-- ESTADO --}}
                                    <td
                                        data-columna="estado"
                                        class="col-estado px-4 py-4 whitespace-nowrap text-sm text-center">

                                        {{ $estado }}

                                    </td>


                                    {{-- SEMÁFORO --}}
                                    <td
                                        data-columna="semaforo"
                                        class="col-semaforo px-4 py-4 whitespace-nowrap text-sm text-center">

                                        @if($semaforo === 'TERMINADO')

                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            TERMINADO
                                        </span>

                                        @elseif($semaforo === 'CANCELADO')

                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                            CANCELADO
                                        </span>

                                        @elseif($semaforo === 'ATRASADO')

                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            ATRASADO
                                        </span>

                                        @elseif($semaforo === 'EN RIESGO')

                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                            EN RIESGO
                                        </span>

                                        @elseif($semaforo === 'EN TIEMPO')

                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            EN TIEMPO
                                        </span>

                                        @else

                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                            SIN FECHA
                                        </span>

                                        @endif

                                    </td>


                                    {{-- DÍAS RESTANTES --}}
                                    <td
                                        data-columna="dias-restantes"
                                        class="col-dias-restantes px-4 py-4 whitespace-nowrap text-sm text-center">

                                        {{ $diasRestantes }}

                                    </td>


                                    {{-- DÍAS ATRASO --}}
                                    <td
                                        data-columna="dias-atraso"
                                        class="col-dias-atraso px-4 py-4 whitespace-nowrap text-sm text-center">

                                        {{ $diasAtraso }}

                                    </td>

                                    </tr>

                                    @empty

                                    <tr>

                                        <td
                                            colspan="12"
                                            class="px-6 py-10 text-center text-gray-500">

                                            No hay pedidos registrados.

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


    {{-- JAVASCRIPT PARA MOSTRAR / OCULTAR COLUMNAS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const btnColumnas = document.getElementById('btnColumnas');

            const panelColumnas = document.getElementById('panelColumnas');

            const cerrarColumnas = document.getElementById('cerrarColumnas');

            const aplicarColumnas = document.getElementById('aplicarColumnas');

            const checks = document.querySelectorAll('.columna-check');


            /*
            |--------------------------------------------------------------------------
            | ABRIR PANEL
            |--------------------------------------------------------------------------
            */

            btnColumnas.addEventListener('click', function() {

                panelColumnas.classList.toggle('hidden');

            });


            /*
            |--------------------------------------------------------------------------
            | CERRAR PANEL
            |--------------------------------------------------------------------------
            */

            cerrarColumnas.addEventListener('click', function() {

                panelColumnas.classList.add('hidden');

            });


            /*
            |--------------------------------------------------------------------------
            | APLICAR COLUMNAS
            |--------------------------------------------------------------------------
            */ 

            aplicarColumnas.addEventListener('click', function() {

                checks.forEach(function(check) {

                    const columna = check.dataset.columna;

                    const elementos = document.querySelectorAll(
                        '#tablaPedidos [data-columna="' + columna + '"]'
                    );

                    elementos.forEach(function(elemento) {

                        elemento.hidden = !check.checked;

                    });

                });

                panelColumnas.classList.add('hidden');

            });


            /*
            |--------------------------------------------------------------------------
            | CERRAR AL HACER CLICK FUERA
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                if (
                    !panelColumnas.contains(event.target) &&
                    !btnColumnas.contains(event.target)
                ) {

                    panelColumnas.classList.add('hidden');

                }

            });


            /*
            |--------------------------------------------------------------------------
            | APLICAR CONFIGURACIÓN INICIAL
            |--------------------------------------------------------------------------
            */

            aplicarColumnas.click();

        });
    </script>

</x-app-layout>
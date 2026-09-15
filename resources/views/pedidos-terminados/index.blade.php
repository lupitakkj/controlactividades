<x-app-layout>

    {{-- ============================================================
         ENCABEZADO
    ============================================================= --}}
    <x-slot name="header">

        <div class="flex items-center justify-between gap-4">

            <div>

                <h2 class="font-semibold text-xl text-gray-600 leading-tight">
                    Pedidos Terminados
                </h2>

                <p class="text-sm text-gray-400 mt-1">
                    Historial de pedidos que han completado su avance.
                </p>

            </div>

        </div>

    </x-slot>


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
                                class="text-lg
                                       font-bold
                                       text-gray-700">

                                {{ $pedidosTerminados->count() }}

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ====================================================
                     TABLA
                ===================================================== --}}
                <div class="p-5">

                    <div class="overflow-x-auto">

                        <table
                            class="w-full
                                   text-sm
                                   border-collapse">

                            <thead>

                                <tr
                                    class="bg-gray-50
                                           border-b
                                           border-gray-200">

                                    <th
                                        class="px-4
                                               py-3
                                               text-center
                                               text-xs
                                               font-semibold
                                               text-gray-600
                                               whitespace-nowrap">

                                        PEDIDO

                                    </th>

                                    <th
                                        class="px-4
                                               py-3
                                               text-left
                                               text-xs
                                               font-semibold
                                               text-gray-600">

                                        CLIENTE

                                    </th>

                                    <th
                                        class="px-4
                                               py-3
                                               text-left
                                               text-xs
                                               font-semibold
                                               text-gray-600">

                                        SU PEDIDO

                                    </th>

                                    <th
                                        class="px-4
                                               py-3
                                               text-center
                                               text-xs
                                               font-semibold
                                               text-gray-600">

                                        PARTIDAS

                                    </th>

                                    <th
                                        class="px-4
                                               py-3
                                               text-center
                                               text-xs
                                               font-semibold
                                               text-gray-600">

                                        AVANCE

                                    </th>

                                    <th
                                        class="px-4
                                               py-3
                                               text-center
                                               text-xs
                                               font-semibold
                                               text-gray-600">

                                        FECHA ENTREGA

                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse(
                                    $pedidosTerminados
                                    as $pedido
                                )

                                    <tr
                                        class="border-b
                                               border-gray-100
                                               hover:bg-gray-50">

                                        {{-- PEDIDO --}}
                                        <td
                                            class="px-4
                                                   py-3
                                                   text-center
                                                   font-semibold
                                                   text-gray-800">

                                            {{ $pedido->pedido_no }}

                                        </td>


                                        {{-- CLIENTE --}}
                                        <td
                                            class="px-4
                                                   py-3
                                                   text-gray-700">

                                            {{ $pedido->cliente ?: '—' }}

                                        </td>


                                        {{-- SU PEDIDO --}}
                                        <td
                                            class="px-4
                                                   py-3
                                                   text-gray-700">

                                            {{ $pedido->su_pedido ?: '—' }}

                                        </td>


                                        {{-- PARTIDAS --}}
                                        <td
                                            class="px-4
                                                   py-3
                                                   text-center
                                                   font-medium
                                                   text-gray-700">

                                            {{ $pedido->partidas->count() }}

                                        </td>


                                        {{-- AVANCE --}}
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
                                                       bg-gray-100
                                                       text-gray-800
                                                       font-bold
                                                       text-xs">

                                                100.0%

                                            </span>

                                        </td>


                                        {{-- FECHA ENTREGA --}}
                                        <td
                                            class="px-4
                                                   py-3
                                                   text-center
                                                   text-gray-700">

                                            @if($pedido->fecha_entrega)

                                                {{ $pedido->fecha_entrega->format('d/m/Y') }}

                                            @else

                                                —

                                            @endif

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td
                                            colspan="6"
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

</x-app-layout>
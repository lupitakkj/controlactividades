<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Importación de Pedidos') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- MENSAJE DE ÉXITO GENERAL --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif


            {{-- MENSAJE DE ERROR GENERAL --}}
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif


            {{-- RESULTADOS DE LA IMPORTACIÓN --}}
            @if(session('resultados_importacion'))

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">

                    <div class="p-6">

                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            Resultado de la importación
                        </h3>

                        <div class="space-y-3">

                            @foreach(session('resultados_importacion') as $resultado)

                                {{-- IMPORTADO --}}
                                @if($resultado['estado'] === 'importado')

                                    <div class="p-4 rounded-lg bg-green-100 text-green-800">

                                        <div class="font-semibold">
                                            {{ $resultado['archivo'] }}
                                        </div>

                                        <div class="text-sm mt-1">
                                            ✅ {{ $resultado['mensaje'] }}
                                        </div>

                                    </div>


                                {{-- ACTUALIZADO --}}
                                @elseif($resultado['estado'] === 'actualizado')

                                    <div class="p-4 rounded-lg bg-green-100 text-green-800">

                                        <div class="font-semibold">
                                            {{ $resultado['archivo'] }}
                                        </div>

                                        <div class="text-sm mt-1">
                                            🔄 {{ $resultado['mensaje'] }}
                                        </div>

                                    </div>


                                {{-- YA EXISTE --}}
                                @elseif($resultado['estado'] === 'ya_existe')

                                    <div class="p-4 rounded-lg bg-yellow-100 text-yellow-800">

                                        <div class="font-semibold">
                                            {{ $resultado['archivo'] }}
                                        </div>

                                        <div class="text-sm mt-1">
                                            ⚠️ {{ $resultado['mensaje'] }}
                                        </div>

                                    </div>


                                {{-- ERROR --}}
                                @else

                                    <div class="p-4 rounded-lg bg-red-100 text-red-800">

                                        <div class="font-semibold">
                                            {{ $resultado['archivo'] }}
                                        </div>

                                        <div class="text-sm mt-1">
                                            ❌ {{ $resultado['mensaje'] }}
                                        </div>

                                    </div>

                                @endif

                            @endforeach

                        </div>

                    </div>

                </div>

            @endif


            {{-- FORMULARIO DE IMPORTACIÓN --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6">

                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        Importar pedidos desde Excel
                    </h3>


                    <form
                        action="{{ route('importaciones.importar') }}"
                        method="POST"
                        enctype="multipart/form-data"
                    >

                        @csrf


                        {{-- ARCHIVOS --}}
                        <div class="mb-5">

                            <label
                                for="archivos"
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Seleccionar archivos Excel
                            </label>


                            <input
                                type="file"
                                id="archivos"
                                name="archivos[]"
                                multiple
                                accept=".xlsx,.xls"
                                class="block w-full border border-gray-300 rounded-lg p-3"
                            >


                            <p class="text-sm text-gray-500 mt-2">
                                Puedes seleccionar uno o varios archivos Excel.
                            </p>

                        </div>


                        {{-- ERRORES DE VALIDACIÓN --}}
                        @if($errors->any())

                            <div class="mb-5 p-4 bg-red-100 text-red-800 rounded-lg">

                                <div class="font-semibold mb-2">
                                    No se pudo realizar la importación:
                                </div>


                                <ul class="list-disc list-inside text-sm">

                                    @foreach($errors->all() as $error)

                                        <li>
                                            {{ $error }}
                                        </li>

                                    @endforeach

                                </ul>

                            </div>

                        @endif


                        {{-- BOTÓN --}}
                        <button
                            type="submit"
                            class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                        >
                            Importar pedidos
                        </button>

                    </form>

                </div>

            </div>

        </div>
    </div>

</x-app-layout>
<x-app-layout>

    <div class="py-6">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- =====================================================
                 ENCABEZADO
            ====================================================== --}}

            <div class="bg-[#111827] rounded-xl shadow-lg p-5 mb-6">

                <div class="flex flex-col md:flex-row
                            md:items-center
                            md:justify-between
                            gap-3">

                    <div>

                        <h1 class="text-2xl font-bold text-white">
                            📦 Catálogo de Materiales
                        </h1>

                        <p class="text-sm text-gray-300 mt-1">
                            Clasificación de claves por familia de material
                        </p>

                    </div>

                    <div class="text-sm text-gray-300">

                        Claves clasificadas:
                        <span class="text-green-400 font-bold">
                            {{ $catalogo->count() }}
                        </span>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 MENSAJE DE ÉXITO
            ====================================================== --}}

            @if(session('success'))

            <div
                class="mb-5 bg-green-100
                           border border-green-300
                           text-green-800
                           px-4 py-3
                           rounded-lg">
                {{ session('success') }}
            </div>

            @endif


            {{-- =====================================================
                 ERRORES
            ====================================================== --}}

            @if($errors->any())

            <div
                class="mb-5 bg-red-100
                           border border-red-300
                           text-red-800
                           px-4 py-3
                           rounded-lg">

                <ul class="list-disc pl-5">

                    @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

            @endif


            {{-- =====================================================
                 CLAVES SIN CLASIFICAR
            ====================================================== --}}

            <div
                class="bg-white
                       rounded-xl
                       shadow-lg
                       border border-gray-200
                       overflow-hidden
                       mb-6">

                <div
                    class="bg-[#1f2937]
                           text-white
                           px-5 py-4
                           flex items-center
                           justify-between">

                    <div>

                        <h2 class="font-bold text-lg">
                            ⚠️ Claves sin clasificar
                        </h2>

                        <p class="text-xs text-gray-300 mt-1">
                            Asigna una familia a cada clave.
                        </p>

                    </div>

                    <div
                        class="contador-sin-clasificar
           bg-yellow-400
           text-gray-900
           font-bold
           px-3 py-1
           rounded-full
           text-sm">

                        {{ $clavesSinClasificar->count() }}

                    </div>

                </div>


                @if($clavesSinClasificar->isEmpty())

                <div class="p-8 text-center">

                    <div class="text-green-600 text-4xl mb-2">
                        ✓
                    </div>

                    <div class="font-semibold text-gray-700">
                        No hay claves sin clasificar.
                    </div>

                    <div class="text-sm text-gray-500 mt-1">
                        Todas las claves existentes ya tienen una familia.
                    </div>

                </div>

                @else

                <div class="overflow-x-auto">

                    <table class="min-w-full text-sm">

                        <thead class="bg-gray-100">

                            <tr>

                                <th
                                    class="px-4 py-3
                                               text-left
                                               font-bold
                                               text-gray-700">
                                    CLAVE
                                </th>

                                <th
                                    class="px-4 py-3
                                               text-left
                                               font-bold
                                               text-gray-700">
                                    DESCRIPCIÓN
                                </th>

                                <th
                                    class="px-4 py-3
                                               text-center
                                               font-bold
                                               text-gray-700
                                               w-64">
                                    FAMILIA
                                </th>

                                <th
                                    class="px-4 py-3
                                               text-center
                                               font-bold
                                               text-gray-700
                                               w-32">
                                    ACCIÓN
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-200">

                            @foreach($clavesSinClasificar as $partida)

                            <tr
                                class="hover:bg-gray-50">

                                {{-- CLAVE --}}

                                <td
                                    class="px-4 py-3
                                                   font-semibold
                                                   text-gray-900
                                                   whitespace-nowrap">
                                    {{ $partida->clave }}
                                </td>


                                {{-- DESCRIPCIÓN --}}

                                <td
                                    class="px-4 py-3
                                                   text-gray-700">
                                    {{ $partida->descripcion }}
                                </td>


                                {{-- FAMILIA --}}

                                <td class="px-4 py-3">

                                    <form
                                        class="form-clasificar flex items-center gap-2"
                                        data-url="{{ route('catalogo-materiales.guardar') }}">
                                        @csrf

                                        <input
                                            type="hidden"
                                            name="clave"
                                            value="{{ $partida->clave }}">

                                        <select
                                            name="familia_material_id"
                                            required
                                            class="familia-select
               w-full
               border-gray-300
               rounded-lg
               text-sm
               focus:ring-blue-500
               focus:border-blue-500">

                                            <option value="">
                                                Seleccionar familia
                                            </option>

                                            @foreach($familias as $familia)

                                            <option value="{{ $familia->id }}">
                                                {{ $familia->nombre }}
                                            </option>

                                            @endforeach

                                        </select>


                                        <button
                                            type="submit"
                                            class="btn-guardar
               bg-blue-500
               hover:bg-blue-600
               text-white
               font-semibold
               px-3 py-2
               rounded-lg
               transition
               whitespace-nowrap">
                                            Guardar
                                        </button>

                                    </form>

                                </td>


                                {{-- ACCIÓN --}}

                                <td class="px-4 py-3 text-center">

                                    <span
                                        class="estado-clasificacion
           text-xs
           font-semibold
           text-yellow-600">

                                        Pendiente

                                    </span>

                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                @endif

            </div>


            {{-- =====================================================
                 CATÁLOGO CLASIFICADO
            ====================================================== --}}

            <div
                class="bg-white
                       rounded-xl
                       shadow-lg
                       border border-gray-200
                       overflow-hidden">

                <div
                    class="bg-[#1f2937]
                           text-white
                           px-5 py-4
                           flex items-center
                           justify-between">

                    <div>

                        <h2 class="font-bold text-lg">
                            ✅ Materiales clasificados
                        </h2>

                        <p class="text-xs text-gray-300 mt-1">
                            Claves que ya tienen una familia asignada.
                        </p>

                    </div>

                    <div
                        class="bg-green-500
                               text-white
                               font-bold
                               px-3 py-1
                               rounded-full
                               text-sm">
                        {{ $catalogo->count() }}
                    </div>

                </div>


                @if($catalogo->isEmpty())

                <div class="p-8 text-center text-gray-500">

                    Todavía no hay materiales clasificados.

                </div>

                @else

                <div class="overflow-x-auto">

                    <table class="min-w-full text-sm">

                        <thead class="bg-gray-100">

                            <tr>

                                <th
                                    class="px-4 py-3 text-left
                                               font-bold text-gray-700">
                                    CLAVE
                                </th>

                                <th
                                    class="px-4 py-3 text-left
                                               font-bold text-gray-700">
                                    DESCRIPCIÓN
                                </th>

                                <th
                                    class="px-4 py-3 text-center
                                               font-bold text-gray-700">
                                    FAMILIA
                                </th>

                                <th
                                    class="px-4 py-3 text-center
                                               font-bold text-gray-700">
                                    ESTADO
                                </th>

                                <th
                                    class="px-4 py-3 text-center
                                               font-bold text-gray-700">
                                    ACCIÓN
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-200">

                            @foreach($catalogo as $material)

                            <tr class="hover:bg-gray-50">

                                {{-- CLAVE --}}

                                <td
                                    class="px-4 py-3
                                                   font-semibold
                                                   text-gray-900">
                                    {{ $material->clave }}
                                </td>


                                {{-- DESCRIPCIÓN --}}

                                <td
                                    class="px-4 py-3
                                                   text-gray-700">
                                    {{ $material->descripcion }}
                                </td>


                                {{-- FAMILIA --}}

                                <td class="px-4 py-3 text-center">

                                    @if($material->familia)

                                    <span
                                        class="inline-flex
                                                           items-center
                                                           px-3 py-1
                                                           rounded-full
                                                           bg-blue-100
                                                           text-blue-800
                                                           font-semibold
                                                           text-xs">
                                        {{ $material->familia->nombre }}
                                    </span>

                                    @else

                                    <span
                                        class="text-gray-400">
                                        Sin familia
                                    </span>

                                    @endif

                                </td>


                                {{-- ESTADO --}}

                                <td class="px-4 py-3 text-center">

                                    @if($material->activo)

                                    <span
                                        class="text-green-600
                                                           font-semibold
                                                           text-xs">
                                        ACTIVO
                                    </span>

                                    @else

                                    <span
                                        class="text-red-600
                                                           font-semibold
                                                           text-xs">
                                        INACTIVO
                                    </span>

                                    @endif

                                </td>


                                {{-- ACTUALIZAR --}}

                                <td class="px-4 py-3">

                                    <form
                                        method="POST"
                                        action="{{ route(
                                                    'catalogo-materiales.actualizar',
                                                    $material
                                                ) }}"
                                        class="flex items-center gap-2">

                                        @csrf

                                        @method('PATCH')


                                        <select
                                            name="familia_material_id"
                                            required
                                            class="border-gray-300
                                                           rounded-lg
                                                           text-xs
                                                           py-1">

                                            @foreach($familias as $familia)

                                            <option
                                                value="{{ $familia->id }}"
                                                {{ $material->familia_material_id == $familia->id ? 'selected' : '' }}>
                                                {{ $familia->nombre }}
                                            </option>

                                            @endforeach

                                        </select>


                                        <button
                                            type="submit"
                                            class="bg-gray-700
                                                           hover:bg-gray-800
                                                           text-white
                                                           text-xs
                                                           font-semibold
                                                           px-3 py-1.5
                                                           rounded-lg">
                                            Cambiar
                                        </button>

                                    </form>

                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                @endif

            </div>

        </div>

    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.form-clasificar').forEach(function(form) {

                form.addEventListener('submit', async function(e) {

                    e.preventDefault();

                    const boton = form.querySelector('.btn-guardar');
                    const select = form.querySelector('.familia-select');
                    const fila = form.closest('tr');

                    /*
                    |--------------------------------------------------------------------------
                    | VALIDAR FAMILIA
                    |--------------------------------------------------------------------------
                    */

                    if (!select.value) {

                        alert('Selecciona una familia antes de guardar.');

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DATOS
                    |--------------------------------------------------------------------------
                    */

                    const datos = new FormData(form);

                    console.log('Datos enviados:');
                    console.log('clave:', datos.get('clave'));
                    console.log(
                        'familia_material_id:',
                        datos.get('familia_material_id')
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | ESTADO DEL BOTÓN
                    |--------------------------------------------------------------------------
                    */

                    const textoOriginal = boton.textContent;

                    boton.disabled = true;
                    select.disabled = true;

                    boton.textContent = 'Guardando...';


                    try {

                        /*
                        |--------------------------------------------------------------------------
                        | ENVIAR A LARAVEL
                        |--------------------------------------------------------------------------
                        */

                        const response = await fetch(
                            form.dataset.url, {
                                method: 'POST',

                                headers: {

                                    'X-CSRF-TOKEN': document
                                        .querySelector(
                                            'meta[name="csrf-token"]'
                                        )
                                        .getAttribute('content'),

                                    'Accept': 'application/json',

                                    'X-Requested-With': 'XMLHttpRequest'

                                },

                                body: datos
                            }
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | LEER RESPUESTA
                        |--------------------------------------------------------------------------
                        */

                        const texto = await response.text();

                        console.log(
                            'Respuesta del servidor:',
                            texto
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | CONVERTIR RESPUESTA A JSON
                        |--------------------------------------------------------------------------
                        */

                        let data;

                        try {

                            data = JSON.parse(texto);

                        } catch (error) {

                            console.error(
                                'La respuesta NO es JSON:',
                                texto
                            );

                            throw new Error(
                                'El servidor no devolvió una respuesta JSON.'
                            );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | ERROR DEL SERVIDOR
                        |--------------------------------------------------------------------------
                        */

                        if (!response.ok) {

                            console.error(
                                'HTTP:',
                                response.status
                            );

                            console.error(
                                'Respuesta Laravel:',
                                data
                            );


                            /*
                            |--------------------------------------------------------------
                            | Mostrar errores de validación
                            |--------------------------------------------------------------
                            */

                            if (data.errors) {

                                console.error(
                                    'Errores de validación:',
                                    data.errors
                                );


                                let mensaje =
                                    data.message ||
                                    'Los datos enviados no son válidos.';


                                Object.keys(data.errors).forEach(
                                    function(campo) {

                                        data.errors[campo].forEach(
                                            function(error) {

                                                mensaje +=
                                                    '\n\n' +
                                                    campo +
                                                    ': ' +
                                                    error;

                                            }
                                        );

                                    }
                                );


                                throw new Error(mensaje);
                            }


                            throw new Error(
                                data.message ||
                                'No se pudo guardar la clasificación.'
                            );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | VALIDAR RESPUESTA EXITOSA
                        |--------------------------------------------------------------------------
                        */

                        if (!data.success) {

                            throw new Error(
                                data.message ||
                                'No se pudo guardar la clasificación.'
                            );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | GUARDADO CORRECTO
                        |--------------------------------------------------------------------------
                        */

                        console.log(
                            'Clasificación guardada correctamente:',
                            data
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | BOTÓN VERDE
                        |--------------------------------------------------------------------------
                        */

                        boton.textContent = '✓ Guardado';

                        boton.classList.remove(
                            'bg-blue-500',
                            'hover:bg-blue-600'
                        );

                        boton.classList.add(
                            'bg-green-500'
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | CAMBIAR ESTADO
                        |--------------------------------------------------------------------------
                        */

                        const estado =
                            fila.querySelector(
                                '.estado-clasificacion'
                            );

                        if (estado) {

                            estado.textContent =
                                'Clasificado';

                            estado.classList.remove(
                                'text-yellow-600'
                            );

                            estado.classList.add(
                                'text-green-600'
                            );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | RESALTAR FILA
                        |--------------------------------------------------------------------------
                        */

                        fila.style.transition =
                            'background-color 0.3s';

                        fila.style.backgroundColor =
                            '#dcfce7';


                        /*
                        |--------------------------------------------------------------------------
                        | ELIMINAR FILA
                        |--------------------------------------------------------------------------
                        */

                        setTimeout(function() {

                            fila.remove();


                            /*
                            |--------------------------------------------------------------
                            | ACTUALIZAR CONTADOR
                            |--------------------------------------------------------------
                            */

                            const contador =
                                document.querySelector(
                                    '.contador-sin-clasificar'
                                );

                            if (contador) {

                                let cantidad =
                                    parseInt(
                                        contador.textContent
                                    ) || 0;

                                if (cantidad > 0) {

                                    contador.textContent =
                                        cantidad - 1;
                                }
                            }

                        }, 500);


                    } catch (error) {

                        console.error(
                            'ERROR AL GUARDAR:',
                            error
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | MOSTRAR ERROR REAL
                        |--------------------------------------------------------------------------
                        */

                        alert(
                            error.message ||
                            'No se pudo guardar la clasificación.'
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | RESTAURAR CONTROLES
                        |--------------------------------------------------------------------------
                        */

                        boton.disabled = false;
                        select.disabled = false;

                        boton.textContent =
                            textoOriginal;

                    }

                });

            });

        });
    </script>

</x-app-layout>
<x-app-layout>

    {{-- ============================================================
         TABULATOR CSS
    ============================================================= --}}
    <link
        href="https://unpkg.com/tabulator-tables@6.5.0/dist/css/tabulator.min.css"
        rel="stylesheet">

    <script
        src="https://unpkg.com/tabulator-tables@6.5.0/dist/js/tabulator.min.js"></script>


    {{-- ============================================================
         CONTENIDO
    ============================================================= --}}
    <div class="py-6">

        <div class="w-full px-4 sm:px-6 lg:px-8">

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-visible">


                {{-- ====================================================
                     CABECERA DEL DESPIECE
                ===================================================== --}}
                <div class="px-5 py-4 border-b border-gray-200">

                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                        <div>

                            <h3 class="text-lg font-semibold text-gray-800">
                                Despiece de producción
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Captura la cantidad realizada en cada proceso.
                            </p>

                        </div>


                        {{-- ====================================================
                             INFORMACIÓN
                        ===================================================== --}}
                        <div class="flex items-center gap-3">

                            <div class="flex items-center gap-3">

                                {{-- PEDIDOS ACTIVOS --}}
                                <div class="px-3 py-2 rounded-lg bg-gray-50 border border-gray-200">

                                    <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                        Pedidos activos
                                    </div>

                                    <div
                                        id="contadorPedidos"
                                        class="text-sm font-semibold text-gray-700">
                                        0
                                    </div>

                                </div>


                                {{-- TOTAL DE PARTIDAS --}}
                                <div class="px-3 py-2 rounded-lg bg-gray-50 border border-gray-200">

                                    <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                        Partidas
                                    </div>

                                    <div
                                        id="contadorPartidas"
                                        class="text-sm font-semibold text-gray-700">
                                        0
                                    </div>

                                </div>


                                {{-- PROCESOS --}}
                                <div class="px-3 py-2 rounded-lg bg-gray-50 border border-gray-200">

                                    <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                        Procesos
                                    </div>

                                    <div
                                        id="contadorProcesos"
                                        class="text-sm font-semibold text-gray-700">
                                        0
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ====================================================
                     BARRA DE HERRAMIENTAS
                ===================================================== --}}
                {{-- ====================================================
     BARRA DE HERRAMIENTAS
===================================================== --}}
                <div class="px-5 py-3 border-b border-gray-200 bg-gray-50 relative z-50">

                    <div class="flex flex-col lg:flex-row lg:items-center gap-3">

                        {{-- BUSCADOR --}}
                        <div class="relative w-full lg:w-[520px]">

                            <span
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">
                                🔎
                            </span>

                            <input
                                type="text"
                                id="buscarDespiece"
                                placeholder="Buscar pedido, clave o descripción..."
                                class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                        </div>


                        {{-- LIMPIAR FILTRO --}}
                        <button
                            type="button"
                            id="limpiarBusqueda"
                            class="px-4 py-2 text-sm font-medium text-gray-600
                   bg-white border border-gray-300 rounded-lg
                   hover:bg-gray-100 transition whitespace-nowrap">
                            🧹 Limpiar filtro
                        </button>


                        {{-- CONFIGURACIÓN DE COLUMNAS --}}
                        <div class="relative ml-auto z-[100]">

                            <button
                                type="button"
                                id="btnColumnas"
                                class="px-4 py-2 text-sm font-medium text-gray-700
                       bg-white border border-gray-300 rounded-lg
                       hover:bg-gray-100 transition whitespace-nowrap">
                                ⚙️ Columnas
                            </button>


                            {{-- PANEL DE COLUMNAS --}}
                            <div
                                id="panelColumnas"
                                class="hidden absolute right-0 top-full mt-2 z-[100]
                       w-64 bg-white border border-gray-200
                       rounded-xl shadow-xl">

                                <div class="px-4 py-3 border-b border-gray-200">

                                    <div class="font-semibold text-gray-800 text-sm">
                                        Mostrar columnas
                                    </div>

                                    <div class="text-xs text-gray-500 mt-1">
                                        Selecciona las columnas que deseas visualizar.
                                    </div>

                                </div>


                                <div
                                    id="listaColumnas"
                                    class="p-3 max-h-80 overflow-y-auto">
                                </div>


                                <div class="px-3 py-3 border-t border-gray-200 bg-gray-50">

                                    <button
                                        type="button"
                                        id="mostrarTodasColumnas"
                                        class="w-full px-3 py-2 text-xs font-medium
                               text-gray-700 bg-white border border-gray-300
                               rounded-lg hover:bg-gray-100">
                                        Mostrar todas
                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ====================================================
                     TABLA
                ===================================================== --}}
                <div class="p-4">

                    <div
                        id="tablaDespiece"
                        class="despiece-grid"></div>

                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================
         DATOS DE LARAVEL
    ============================================================= --}}
    @php

    $filasDespiece = [];

    foreach ($pedidos as $pedido) {

    foreach ($pedido->partidas as $partida) {

    /*
    |--------------------------------------------------------------------------
    | INDEXAR PROCESOS DE LA PARTIDA
    |--------------------------------------------------------------------------
    | En lugar de buscar firstWhere() repetidamente,
    | convertimos la colección en un índice por proceso_id.
    */

    $despieces = $partida->despieceProcesos
    ->keyBy('proceso_id');

    $fila = [
    'pedido_id' => $pedido->id,
    'pedido_no' => $pedido->pedido_no,
    'partida_id' => $partida->id,
    'clave' => $partida->clave,
    'descripcion' => $partida->descripcion,
    'cantidad' => $partida->cantidad,
    ];

    foreach ($procesos as $proceso) {

    $despiece = $despieces->get($proceso->id);

    $fila['proceso_' . $proceso->id] =
    $despiece?->cantidad_realizada;

    $fila['proceso_aplica_' . $proceso->id] =
    $despiece?->aplica;
    }

    $filasDespiece[] = $fila;
    }
    }

    $procesosData = $procesos->map(function ($proceso) {

    return [
    'id' => $proceso->id,
    'nombre' => $proceso->nombre,
    ];

    })->values();

    @endphp


    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /* ========================================================
               DATOS
            ======================================================== */

            const datosDespiece = @json($filasDespiece);

            const procesos = @json($procesosData);

            console.log('DATOS DESPIECE:', datosDespiece);
            console.log('PROCESOS:', procesos);

            /* ========================================================
        GUARDAR PROCESO EN LARAVEL
        ======================================================== */

            async function guardarProcesoServidor(
                partidaId,
                procesoId,
                aplica,
                cantidadRealizada
            ) {

                const csrfToken =
                    document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content');


                try {

                    const respuesta =
                        await fetch(
                            '{{ route("despiece.proceso.guardar") }}', {
                                method: 'POST',

                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                },

                                body: JSON.stringify({

                                    partida_id: Number(partidaId),

                                    proceso_id: Number(procesoId),

                                    aplica: Boolean(aplica),

                                    cantidad_realizada: cantidadRealizada === null ||
                                        cantidadRealizada === '' ?
                                        null : Number(cantidadRealizada),

                                }),
                            }
                        );


                    const datos =
                        await respuesta.json();


                    if (!respuesta.ok || !datos.ok) {

                        throw new Error(
                            datos.mensaje ||
                            'No se pudo guardar el proceso.'
                        );

                    }


                    return datos;


                } catch (error) {

                    console.error(
                        'Error al guardar proceso:',
                        error
                    );


                    alert(
                        error.message ||
                        'Ocurrió un error al guardar el proceso.'
                    );


                    return null;
                }
            }

            /* ========================================================
               CALCULAR AVANCE DE LA PARTIDA
            ======================================================== */

            function calcularAvancePartida(fila) {

                const cantidadTotal = Number(fila.cantidad);

                if (
                    isNaN(cantidadTotal) ||
                    cantidadTotal <= 0
                ) {
                    return null;
                }

                let sumaPorcentajes = 0;
                let cantidadProcesosAplicables = 0;
                let hayProcesoPendiente = false;

                procesos.forEach(function(proceso) {

                    const procesoId = proceso.id;

                    const valor =
                        fila['proceso_' + procesoId];

                    const aplica =
                        fila['proceso_aplica_' + procesoId];

                    // =========================================================
                    // SIN CAPTURA = PROCESO PENDIENTE
                    // =========================================================
                    if (
                        valor === null ||
                        valor === undefined ||
                        valor === ''
                    ) {
                        hayProcesoPendiente = true;
                        cantidadProcesosAplicables++;
                        return;
                    }

                    // =========================================================
                    // N/A = PROCESO RESUELTO
                    // NO PARTICIPA EN EL PROMEDIO
                    // =========================================================
                    if (
                        aplica === false ||
                        aplica === 0
                    ) {
                        return;
                    }

                    // =========================================================
                    // CANTIDAD REALIZADA
                    // =========================================================
                    const cantidadRealizada =
                        Number(valor);

                    if (isNaN(cantidadRealizada)) {
                        hayProcesoPendiente = true;
                        return;
                    }

                    // =========================================================
                    // CALCULAR PORCENTAJE
                    // =========================================================
                    let porcentaje =
                        cantidadRealizada /
                        cantidadTotal;

                    porcentaje =
                        Math.max(
                            0,
                            Math.min(
                                1,
                                porcentaje
                            )
                        );

                    sumaPorcentajes += porcentaje;
                    cantidadProcesosAplicables++;
                });

                // =========================================================
                // NINGÚN PROCESO APLICABLE
                // =========================================================
                if (
                    cantidadProcesosAplicables === 0
                ) {
                    return 0;
                }

                // =========================================================
                // PROMEDIO
                // =========================================================
                let avance =
                    sumaPorcentajes /
                    cantidadProcesosAplicables;

                // =========================================================
                // SI HAY ALGÚN PROCESO VACÍO,
                // NO PERMITIR 100%
                // =========================================================
                if (
                    hayProcesoPendiente &&
                    avance >= 1
                ) {
                    avance = 0.9999;
                }

                return Math.max(
                    0,
                    Math.min(
                        1,
                        avance
                    )
                );
            }

            /* ========================================================
            FORMATEADOR AVANCE
            ======================================================== */

            function avanceFormatter(cell) {

                const valor = cell.getValue();


                if (
                    valor === null ||
                    valor === undefined ||
                    valor === ''
                ) {

                    return `
            <span class="avance-vacio">
                —
            </span>
        `;

                }


                const porcentaje =
                    Number(valor) * 100;


                return `
        <span class="avance-valor">
            ${porcentaje.toLocaleString('es-MX', {
                minimumFractionDigits: 1,
                maximumFractionDigits: 1
            })}%
        </span>
    `;
            }

            /* ========================================================
            CONTADORES
            ======================================================== */

            const pedidosUnicos = new Set(
                datosDespiece.map(function(fila) {
                    return fila.pedido_no;
                })
            );


            /* ========================================================
               PEDIDOS ACTIVOS
            ======================================================== */

            document.getElementById('contadorPedidos').textContent =
                pedidosUnicos.size;


            /* ========================================================
               TOTAL DE PARTIDAS
            ======================================================== */

            document.getElementById('contadorPartidas').textContent =
                datosDespiece.length;


            /* ========================================================
               TOTAL DE PROCESOS
            ======================================================== */

            document.getElementById('contadorProcesos').textContent =
                procesos.length;


            /* ========================================================
               FORMATEADOR DESCRIPCIÓN
            ======================================================== */

            function descripcionFormatter(cell) {

                const valor = cell.getValue() ?? '';

                const contenedor = document.createElement('div');

                contenedor.className = 'descripcion-celda';

                contenedor.textContent = valor;

                contenedor.title = valor;

                return contenedor;

            }


            /* ========================================================
               FORMATEADOR CANTIDAD
            ======================================================== */

            function cantidadFormatter(cell) {

                const valor = cell.getValue();

                const elemento = document.createElement('span');

                elemento.className = 'cantidad-principal';

                if (
                    valor === null ||
                    valor === undefined ||
                    valor === ''
                ) {

                    elemento.textContent = '-';

                } else {

                    const numero = Number(valor);

                    if (Number.isInteger(numero)) {

                        elemento.textContent = numero;

                    } else {

                        elemento.textContent =
                            numero.toLocaleString('es-MX', {
                                maximumFractionDigits: 4
                            });

                    }

                }

                return elemento;

            }


            /* ========================================================
               FORMATEADOR DE PROCESOS
            ======================================================== */

            /* ========================================================
               FORMATEADOR DE PROCESOS
            ======================================================== */

            function procesoFormatter(cell) {

                const valor =
                    cell.getValue();


                const fila =
                    cell.getRow().getData();


                const procesoId =
                    cell
                    .getColumn()
                    .getField()
                    .replace('proceso_', '');


                const cantidadTotal =
                    Number(fila.cantidad);


                const aplica =
                    fila['proceso_aplica_' + procesoId];


                /*
                ========================================================
                DETERMINAR SI ES N/A
                ========================================================
                */

                const esNA =
                    aplica === false ||
                    aplica === 0;


                /*
                ========================================================
                VALOR DEL INPUT
                ========================================================
                */

                const valorInput =
                    esNA ||
                    valor === null ||
                    valor === undefined ||
                    valor === '' ?
                    '' :
                    Number(valor);


                return `
                    <div class="proceso-control-tabulator">

                <input
                    type="number"
                    class="input-proceso-tabulator"
                    min="0"
                    max="${cantidadTotal}"
                    step="1"
                    placeholder="0"
                    value="${valorInput}"
                    data-partida="${fila.partida_id}"
                    data-proceso="${procesoId}"
                    data-cantidad-total="${cantidadTotal}"
                    ${esNA ? 'disabled' : ''}
                >

                <button
                    type="button"
                    class="btn-na-proceso ${esNA ? 'activo' : ''}"
                    data-partida="${fila.partida_id}"
                    data-proceso="${procesoId}"
                    data-cantidad-total="${cantidadTotal}"
                    data-na="${esNA ? '1' : '0'}"
                    title="${esNA ? 'Quitar N/A' : 'Marcar como N/A'}"
                >
                    N/A
                </button>

                    </div>
                    `;
            }


            /* ========================================================
            COLUMNAS BASE
            ======================================================== */

            const columnas = [

                {
                    title: 'PEDIDO',
                    field: 'pedido_no',
                    frozen: true,
                    width: 95,
                    minWidth: 95,
                    hozAlign: 'center',
                    headerHozAlign: 'center',
                    headerSort: true,
                    cssClass: 'columna-fija'
                },


                {
                    title: 'CLAVE',
                    field: 'clave',
                    frozen: true,
                    width: 85,
                    minWidth: 85,
                    hozAlign: 'center',
                    headerHozAlign: 'center',
                    headerSort: true,
                    cssClass: 'columna-fija'
                },


                {
                    title: 'DESCRIPCIÓN',
                    field: 'descripcion',
                    frozen: true,
                    width: 260,
                    minWidth: 260,
                    headerSort: true,
                    headerHozAlign: 'left',
                    formatter: descripcionFormatter,
                    cssClass: 'columna-descripcion columna-fija'
                },


                {
                    title: 'CANTIDAD',
                    field: 'cantidad',
                    frozen: true,
                    width: 85,
                    minWidth: 85,
                    hozAlign: 'center',
                    headerHozAlign: 'center',
                    headerSort: true,
                    formatter: cantidadFormatter,
                    cssClass: 'columna-cantidad columna-fija'
                },

                {
                    title: 'AVANCE',
                    field: 'avance',
                    frozen: true,
                    width: 85,
                    minWidth: 85,
                    hozAlign: 'center',
                    headerHozAlign: 'center',
                    headerSort: true,
                    formatter: avanceFormatter,
                    cssClass: 'columna-avance columna-fija'
                }

            ];


            /* ========================================================
            COLUMNAS DE PROCESOS
            ======================================================== */

            procesos.forEach(function(proceso) {

                columnas.push({

                    title: proceso.nombre,

                    field: 'proceso_' + proceso.id,

                    width: 115,

                    minWidth: 115,

                    hozAlign: 'center',

                    headerHozAlign: 'center',

                    headerSort: false,

                    formatter: procesoFormatter,

                    cssClass: 'columna-proceso'

                });

            });

            /* ========================================================
                CALCULAR AVANCE INICIAL
            ======================================================== */

            datosDespiece.forEach(function(fila) {

                fila.avance =
                    calcularAvancePartida(fila);

            });

            /* ========================================================
            CREAR TABULATOR
            ======================================================== */

            const tabla = new Tabulator(
                '#tablaDespiece', {

                    data: datosDespiece,

                    columns: columnas,

                    index: 'partida_id',

                    height: '65vh',

                    layout: 'fitData',

                    responsiveLayout: false,

                    movableColumns: false,

                    resizableColumns: true,

                    selectableRows: false,

                    placeholder: 'No hay partidas para mostrar.',


                    rowHeight: 48,

                    columnDefaults: {

                        headerSort: true,

                        resizable: true

                    },

                    langs: {

                        'es-mx': {

                            pagination: {

                                page_size: 'Filas por página',

                                page_title: 'Mostrar página',

                                first: 'Primera',

                                first_title: 'Primera página',

                                last: 'Última',

                                last_title: 'Última página',

                                prev: 'Anterior',

                                prev_title: 'Página anterior',

                                next: 'Siguiente',

                                next_title: 'Página siguiente'

                            },

                            headerFilters: {

                                default: 'filtrar...'

                            }

                        }

                    },

                    locale: 'es-mx'

                }
            );

            /* ========================================================
                CONFIGURACIÓN DE COLUMNAS
            ======================================================== */

            const btnColumnas =
                document.getElementById('btnColumnas');

            const panelColumnas =
                document.getElementById('panelColumnas');

            const listaColumnas =
                document.getElementById('listaColumnas');


            /* ========================================================
            CREAR LISTA DE COLUMNAS
            ======================================================== */

            columnas.forEach(function(columna) {

                const item = document.createElement('label');

                item.className =
                    'flex items-center gap-2 px-2 py-2 rounded-md ' +
                    'hover:bg-gray-50 cursor-pointer';


                const checkbox =
                    document.createElement('input');

                checkbox.type = 'checkbox';

                checkbox.checked = true;

                checkbox.className =
                    'checkbox-columna';


                const texto =
                    document.createElement('span');

                texto.className =
                    'text-xs text-gray-700';


                texto.textContent =
                    columna.title;


                checkbox.dataset.field =
                    columna.field;


                checkbox.addEventListener(
                    'change',
                    function() {

                        const columnaTabulator =
                            tabla.getColumn(
                                this.dataset.field
                            );


                        if (!columnaTabulator) {
                            return;
                        }


                        if (this.checked) {

                            columnaTabulator.show();

                        } else {

                            columnaTabulator.hide();

                        }

                    }
                );


                item.appendChild(checkbox);

                item.appendChild(texto);

                listaColumnas.appendChild(item);

            });


            /* ========================================================
            ABRIR / CERRAR PANEL
            ======================================================== */

            btnColumnas.addEventListener(
                'click',
                function(evento) {

                    evento.stopPropagation();

                    panelColumnas.classList.toggle('hidden');

                }
            );


            /* ========================================================
            NO CERRAR AL HACER CLICK DENTRO
            ======================================================== */

            panelColumnas.addEventListener(
                'click',
                function(evento) {

                    evento.stopPropagation();

                }
            );


            /* ========================================================
            CERRAR AL HACER CLICK FUERA
            ======================================================== */

            document.addEventListener(
                'click',
                function() {

                    panelColumnas.classList.add('hidden');

                }
            );


            /* ========================================================
            MOSTRAR TODAS
            ======================================================== */

            document
                .getElementById('mostrarTodasColumnas')
                .addEventListener(
                    'click',
                    function() {

                        columnas.forEach(function(columna) {

                            const columnaTabulator =
                                tabla.getColumn(
                                    columna.field
                                );


                            if (columnaTabulator) {

                                columnaTabulator.show();

                            }

                        });


                        document
                            .querySelectorAll('.checkbox-columna')
                            .forEach(function(checkbox) {

                                checkbox.checked = true;

                            });

                    }
                );

            /* ========================================================
               GUARDAR CANTIDAD DE PROCESO
            ======================================================== */

            document
                .getElementById('tablaDespiece')
                .addEventListener('change', async function(evento) {

                    const input =
                        evento.target.closest(
                            '.input-proceso-tabulator'
                        );


                    if (!input) {
                        return;
                    }


                    /*
                    ====================================================
                    SI ESTÁ VACÍO
                    ====================================================
                    */

                    if (input.value === '') {

                        const partidaId =
                            input.dataset.partida;

                        const procesoId =
                            input.dataset.proceso;

                        const fila =
                            tabla.getRow(partidaId);

                        if (!fila) {
                            return;
                        }

                        /*
                         * ============================================================
                         * CAMPO VACÍO = 0
                         * ============================================================
                         *
                         * Ya no guardamos NULL.
                         *
                         * Si el usuario borra el contenido del campo,
                         * el proceso vuelve a 0 piezas realizadas.
                         */

                        const datos =
                            await guardarProcesoServidor(
                                partidaId,
                                procesoId,
                                true,
                                0
                            );

                        if (!datos) {
                            return;
                        }

                        fila.update({

                            ['proceso_' + procesoId]: 0,

                            ['proceso_aplica_' + procesoId]: true,

                            avance: datos.avance

                        });

                        input.value = 0;

                        return;
                    }


                    /*
                    ====================================================
                    OBTENER CANTIDAD
                    ====================================================
                    */

                    let cantidad =
                        parseInt(
                            input.value,
                            10
                        );


                    const cantidadTotal =
                        parseInt(
                            input.dataset.cantidadTotal,
                            10
                        );


                    /*
                    ====================================================
                    VALIDAR NÚMERO
                    ====================================================
                    */

                    if (isNaN(cantidad)) {

                        input.value = '';

                        return;

                    }


                    /*
                    ====================================================
                    NO NEGATIVOS
                    ====================================================
                    */

                    if (cantidad < 0) {

                        cantidad = 0;

                    }


                    /*
                    ====================================================
                    NO SUPERAR CANTIDAD
                    ====================================================
                    */

                    if (
                        !isNaN(cantidadTotal) &&
                        cantidad > cantidadTotal
                    ) {

                        cantidad =
                            cantidadTotal;

                    }


                    input.value =
                        cantidad;


                    const partidaId =
                        input.dataset.partida;


                    const procesoId =
                        input.dataset.proceso;


                    const fila =
                        tabla.getRow(partidaId);


                    if (!fila) {
                        return;
                    }


                    /*
                    ====================================================
                    GUARDAR EN LARAVEL
                    ====================================================
                    */

                    const datos =
                        await guardarProcesoServidor(
                            partidaId,
                            procesoId,
                            true,
                            cantidad
                        );


                    if (!datos) {
                        return;
                    }


                    /*
                    ====================================================
                    ACTUALIZAR TABULATOR
                    ====================================================
                    */

                    fila.update({

                        ['proceso_' + procesoId]: cantidad,

                        ['proceso_aplica_' + procesoId]: true,

                        avance: datos.avance

                    });

                });

            /* ========================================================
               BOTÓN N/A
            ======================================================== */

            document
                .getElementById('tablaDespiece')
                .addEventListener(
                    'click',
                    async function(evento) {

                        const boton =
                            evento.target.closest(
                                '.btn-na-proceso'
                            );


                        if (!boton) {
                            return;
                        }


                        const partidaId =
                            boton.dataset.partida;


                        const procesoId =
                            boton.dataset.proceso;


                        const fila =
                            tabla.getRow(partidaId);


                        if (!fila) {
                            return;
                        }


                        const input =
                            boton
                            .closest(
                                '.proceso-control-tabulator'
                            )
                            .querySelector(
                                '.input-proceso-tabulator'
                            );


                        const actualmenteNA =
                            boton.dataset.na === '1';


                        /* ====================================================
                           QUITAR N/A
                        ==================================================== */

                        if (actualmenteNA) {

                            const datos =
                                await guardarProcesoServidor(
                                    partidaId,
                                    procesoId,
                                    true,
                                    null
                                );


                            if (!datos) {
                                return;
                            }


                            boton.dataset.na =
                                '0';


                            boton.classList.remove(
                                'activo'
                            );


                            boton.title =
                                'Marcar como N/A';


                            input.disabled =
                                false;


                            input.value =
                                '';


                            fila.update({

                                ['proceso_' + procesoId]: null,

                                ['proceso_aplica_' + procesoId]: null,

                                avance: datos.avance

                            });


                            input.focus();


                            return;
                        }


                        /* ====================================================
                           MARCAR N/A
                        ==================================================== */

                        const datos =
                            await guardarProcesoServidor(
                                partidaId,
                                procesoId,
                                false,
                                0
                            );


                        if (!datos) {
                            return;
                        }


                        boton.dataset.na =
                            '1';


                        boton.classList.add(
                            'activo'
                        );


                        boton.title =
                            'Quitar N/A';


                        input.disabled =
                            true;


                        input.value =
                            '';


                        fila.update({

                            ['proceso_' + procesoId]: 0,

                            ['proceso_aplica_' + procesoId]: false,

                            avance: datos.avance

                        });

                    }
                );



            /* ========================================================
            BUSCADOR
            ======================================================== */

            const buscador =
                document.getElementById('buscarDespiece');


            buscador.addEventListener('input', function() {

                const texto =
                    buscador.value
                    .trim()
                    .toLowerCase();


                if (texto === '') {

                    tabla.clearFilter();

                    return;

                }


                tabla.setFilter(function(data) {

                    const pedido =
                        String(data.pedido_no ?? '')
                        .trim()
                        .toLowerCase();

                    const clave =
                        String(data.clave ?? '')
                        .trim()
                        .toLowerCase();

                    const descripcion =
                        String(data.descripcion ?? '')
                        .trim()
                        .toLowerCase();


                    /*
                    |--------------------------------------------------------------------------
                    | SI EL USUARIO ESCRIBE SOLO NÚMEROS
                    | BUSCAR ÚNICAMENTE POR PEDIDO
                    |--------------------------------------------------------------------------
                    */

                    const esNumero =
                        /^\d+$/.test(texto);


                    if (esNumero) {

                        const pedidoBuscado =
                            texto.replace(/^0+/, '');

                        const pedidoReal =
                            pedido.replace(/^0+/, '');


                        return (
                            pedidoReal === pedidoBuscado
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | SI ES TEXTO
                    | BUSCAR EN CLAVE Y DESCRIPCIÓN
                    |--------------------------------------------------------------------------
                    */

                    return (
                        clave.includes(texto) ||
                        descripcion.includes(texto)
                    );

                });

            });


            /* ========================================================
            LIMPIAR BUSQUEDA
            ======================================================== */

            document
                .getElementById('limpiarBusqueda')
                .addEventListener('click', function() {

                    buscador.value = '';

                    tabla.clearFilter();

                    buscador.focus();

                });

        });
    </script>


    {{-- ============================================================
                    ESTILOS
                ============================================================= --}}
    <style>
        /* ============================================================
                    CONTENEDOR TABULATOR
                    ============================================================= */

        .despiece-grid {

            width: 100%;

            border: 1px solid #d1d5db;

            border-radius: 10px;

            overflow: hidden;

            background: #ffffff;

        }


        /* ============================================================
                    TABULATOR GENERAL
                    ============================================================= */

        .despiece-grid .tabulator {

            border: none;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            font-size: 12px;

            background: #ffffff;

        }


        /* ============================================================
                    ENCABEZADO
                    ============================================================= */

        .despiece-grid .tabulator-header {

            background: #f8fafc;

            border-bottom:
                1px solid #cbd5e1;

        }


        .despiece-grid .tabulator-col {

            background: #f8fafc !important;

            border-right:
                1px solid #e5e7eb;

        }


        .despiece-grid .tabulator-col-title {

            color: #374151;

            font-size: 10px;

            font-weight: 700;

            line-height: 1.15;

            white-space: normal;

            text-align: center;

            padding:
                5px 3px;

        }


        /* ============================================================
                    CUERPO
                    ============================================================= */

        .despiece-grid .tabulator-row {

            min-height: 48px;

            background: #ffffff;

            border-bottom:
                1px solid #e5e7eb;

        }


        .despiece-grid .tabulator-row:hover {

            background: #f8fafc;

        }


        .despiece-grid .tabulator-cell {

            border-right:
                1px solid #edf0f3;

            padding:
                4px 6px;

            vertical-align: middle;

        }


        /* ============================================================
                    COLUMNAS FIJAS
                    ============================================================= */

        .despiece-grid .tabulator-frozen {

            background: #ffffff !important;

            box-shadow:
                3px 0 6px rgba(15, 23, 42, 0.08);

            z-index: 5;

        }


        .despiece-grid .tabulator-header .tabulator-frozen {

            background: #f8fafc !important;

            z-index: 10;

        }


        /* ============================================================
                    PEDIDO
                    ============================================================= */

        .despiece-grid .columna-fija {

            font-size: 11px;

            font-weight: 500;

        }


        /* ============================================================
                    PEDIDO
                    ============================================================= */

        .despiece-grid .tabulator-cell[tabulator-field="pedido_no"] {

            font-weight: 700;

            color: #1f2937;

        }


        /* ============================================================
                    CLAVE
                    ============================================================= */

        .despiece-grid .tabulator-cell[tabulator-field="clave"] {

            font-weight: 600;

            color: #374151;

        }


        /* ============================================================
            DESCRIPCIÓN
            ============================================================= */

        .descripcion-celda {

            width: 100%;

            display: -webkit-box;

            -webkit-box-orient: vertical;

            -webkit-line-clamp: 2;

            overflow: hidden;

            line-height: 1.25;

            white-space: normal;

            word-break: break-word;

            color: #374151;

        }


        /* ============================================================
            CANTIDAD
            ============================================================= */

        .cantidad-principal {

            width: 100%;

            text-align: center;

            font-weight: 700;

            color: #111827;

        }


        /* ============================================================
            INPUTS DE PROCESO
            ============================================================= */

        .input-proceso-tabulator {

            display: block;

            box-sizing: border-box;

            width: 55px;

            height: 28px;

            flex-shrink: 0;

            padding: 2px 4px;

            border: 1px solid #cbd5e1;

            border-radius: 5px;

            background: #ffffff;

            color: #111827;

            font-size: 11px;

            font-weight: 600;

            text-align: center;

            outline: none;
        }

        .input-proceso-tabulator:hover {

            border-color: #94a3b8;

            background: #f8fafc;

        }


        .input-proceso-tabulator:focus {

            border-color: #3b82f6;

            box-shadow:
                0 0 0 2px rgba(59, 130, 246, .15);

            background: #ffffff;

        }


        .input-proceso-tabulator::placeholder {

            color: #9ca3af;

            opacity: 1;

        }

        .btn-na-proceso {

            flex-shrink: 0;

            height: 28px;

            min-width: 32px;

            padding: 0 4px;

            border: 1px solid #cbd5e1;

            border-radius: 5px;

            background: #ffffff;

            color: #64748b;

            font-size: 9px;

            font-weight: 700;

            cursor: pointer;

            white-space: nowrap;
        }

        .btn-na-proceso.activo {
            background: #e2e8f0;
            border-color: #64748b;
            color: #1e293b;
            font-weight: 800;
        }


        /* ============================================================
            QUITAR FLECHAS DEL INPUT NUMBER
            ============================================================= */

        .input-proceso-tabulator::-webkit-inner-spin-button,
        .input-proceso-tabulator::-webkit-outer-spin-button {

            -webkit-appearance: none;

            margin: 0;

        }


        .input-proceso-tabulator {

            -moz-appearance: textfield;

        }


        /* ============================================================
            SCROLL HORIZONTAL
            ============================================================= */

        .despiece-grid .tabulator-tableholder {

            overflow-x: auto !important;

            overflow-y: auto !important;

        }


        /* ============================================================
            SCROLLBAR
            ============================================================= */

        .despiece-grid .tabulator-tableholder::-webkit-scrollbar {

            width: 8px;

            height: 9px;

        }


        .despiece-grid .tabulator-tableholder::-webkit-scrollbar-track {

            background: #f1f5f9;

        }


        .despiece-grid .tabulator-tableholder::-webkit-scrollbar-thumb {

            background: #cbd5e1;

            border-radius: 5px;

        }


        .despiece-grid .tabulator-tableholder::-webkit-scrollbar-thumb:hover {

            background: #94a3b8;

        }


        /* ============================================================
            MENSAJE SIN DATOS
            ============================================================= */

        .despiece-grid .tabulator-placeholder {

            color: #6b7280;

            font-size: 13px;

        }


        /* ============================================================
            DISPOSITIVOS PEQUEÑOS
            ============================================================= */

        @media (max-width: 768px) {

            .despiece-grid .tabulator {

                font-size: 11px;

            }


            .despiece-grid .tabulator-col-title {

                font-size: 9px;

            }

        }

        /* ============================================================
   PANEL CONFIGURACIÓN DE COLUMNAS
============================================================ */

        #panelColumnas {

            width: 280px;

            max-height: 480px;

            overflow: hidden;

        }

        #listaColumnas {

            max-height: 330px;

            overflow-y: auto;

            padding: 8px;

        }

        #listaColumnas::-webkit-scrollbar {

            width: 6px;

        }

        #listaColumnas::-webkit-scrollbar-track {

            background: #f1f5f9;

            border-radius: 4px;

        }

        #listaColumnas::-webkit-scrollbar-thumb {

            background: #cbd5e1;

            border-radius: 4px;

        }

        #listaColumnas::-webkit-scrollbar-thumb:hover {

            background: #94a3b8;

        }


        /* ============================================================
            CHECKBOX COLUMNAS
        ============================================================ */

        .checkbox-columna {

            width: 15px;

            height: 15px;

            accent-color: #2563eb;

            cursor: pointer;

        }


        /* ============================================================
        BOTÓN COLUMNAS
        ============================================================ */

        #btnColumnas {

            min-width: 120px;

        }


        /* ============================================================
            PANEL SCROLL
        ============================================================ */

        #listaColumnas::-webkit-scrollbar {

            width: 6px;

        }

        #listaColumnas::-webkit-scrollbar-track {

            background: #f1f5f9;

        }

        #listaColumnas::-webkit-scrollbar-thumb {

            background: #cbd5e1;

            border-radius: 4px;

        }

        .proceso-control-tabulator {

            display: flex;

            flex-wrap: nowrap;

            align-items: center;

            justify-content: center;

            gap: 4px;

            width: 100%;

            white-space: nowrap;
        }

        /* ============================================================
   AVANCE
============================================================ */

        .avance-valor {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            min-width: 58px;

            padding: 4px 6px;

            border-radius: 5px;

            background: #f1f5f9;

            color: #111827;

            font-size: 11px;

            font-weight: 700;

        }


        .avance-vacio {

            color: #9ca3af;

            font-size: 12px;

        }
    </style>


    {{-- ============================================================
         TABULATOR JS
    ============================================================= --}}



</x-app-layout>
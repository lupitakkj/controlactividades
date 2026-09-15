<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Consulta de inventario</title>


    <style>

        * {
            box-sizing: border-box;
        }


        html,
        body {

            width: 100%;

            min-height: 100%;
        }


        body {

            margin: 0;

            min-height: 100vh;

            background: #f3f4f6;

            font-family: Arial, sans-serif;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 16px;
        }


        /* =========================================
           TARJETA PRINCIPAL
           ========================================= */

        .card {

            width: 100%;

            max-width: 450px;

            background: white;

            border-radius: 20px;

            padding: 28px 22px;

            box-shadow:
                0 10px 35px
                rgba(0, 0, 0, .10);
        }


        /* =========================================
           TÍTULO
           ========================================= */

        .titulo {

            text-align: center;

            font-size: 24px;

            font-weight: 700;

            margin-bottom: 24px;
        }


        /* =========================================
           ETIQUETAS
           ========================================= */

        .label {

            text-align: center;

            color: #6b7280;

            font-size: 14px;
        }


        /* =========================================
           CÓDIGO
           ========================================= */

        .clave {

            text-align: center;

            font-size: 28px;

            font-weight: 700;

            margin:
                8px 0 26px;

            word-break: break-word;
        }


        /* =========================================
           DESCRIPCIÓN
           ========================================= */

        .descripcion {

            text-align: center;

            font-size: 21px;

            font-weight: 600;

            line-height: 1.3;

            margin-bottom: 26px;

            word-break: break-word;
        }


        /* =========================================
           ALMACENES
           ========================================= */

        .almacenes-titulo {

            text-align: center;

            color: #6b7280;

            font-size: 14px;

            margin-bottom: 10px;
        }


        .almacen {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 16px;

            padding: 12px 0;

            border-bottom:
                1px solid #e5e7eb;
        }


        .almacen:last-child {

            border-bottom: none;
        }


        /* -----------------------------------------
           NOMBRE DEL ALMACÉN
           ----------------------------------------- */

        .almacen-nombre {

            flex: 1;

            min-width: 0;

            font-size: 16px;

            font-weight: 600;

            line-height: 1.3;

            overflow-wrap: break-word;

            word-break: normal;
        }


        /* -----------------------------------------
           EXISTENCIA
           ----------------------------------------- */

        .almacen-existencia {

            flex: 0 0 58px;

            text-align: right;

            font-size: 28px;

            font-weight: 800;

            line-height: 1;
        }


        /* =========================================
           ALMACÉN SIN EXISTENCIA
           ========================================= */

        .almacen-nombre-sin-existencia {

            color: #9ca3af;

            font-weight: 500;
        }


        .almacen-sin-existencia {

            color: #9ca3af;

            font-weight: 600;
        }


        /* =========================================
           SIN ALMACENES
           ========================================= */

        .sin-almacenes {

            text-align: center;

            color: #6b7280;

            font-size: 14px;

            padding: 10px 0;
        }


        /* =========================================
           PRECIO
           ========================================= */

        .precio-label {

            text-align: center;

            color: #6b7280;

            font-size: 14px;

            margin-top: 24px;
        }


        .precio {

            text-align: center;

            font-size: 34px;

            font-weight: 800;

            margin-top: 4px;

            line-height: 1.1;
        }


        /* =========================================
           CONSULTA SAE
           ========================================= */

        .estado {

            text-align: center;

            margin-top: 24px;

            color: #6b7280;

            font-size: 14px;

            line-height: 1.5;
        }


        .fecha strong {

            color: #374151;
        }


        /* =========================================
           ERROR
           ========================================= */

        .error {

            text-align: center;

            color: #b91c1c;

            font-weight: 600;

            line-height: 1.4;
        }


        /* =========================================
           PROCESANDO
           ========================================= */

        .procesando {

            text-align: center;

            color: #6b7280;

            font-size: 15px;
        }


        .cargando {

            margin:
                0 auto 12px;

            width: 32px;

            height: 32px;

            border:
                4px solid #e5e7eb;

            border-top:
                4px solid #374151;

            border-radius: 50%;

            animation:
                girar .8s linear infinite;
        }


        @keyframes girar {

            to {

                transform:
                    rotate(360deg);
            }
        }


        /* =========================================
           CELULARES PEQUEÑOS
           ========================================= */

        @media (max-width: 380px) {

            body {

                padding: 10px;
            }


            .card {

                padding:
                    24px 18px;

                border-radius: 18px;
            }


            .titulo {

                font-size: 22px;

                margin-bottom: 22px;
            }


            .clave {

                font-size: 26px;

                margin-bottom: 24px;
            }


            .descripcion {

                font-size: 20px;

                margin-bottom: 24px;
            }


            .almacen {

                gap: 10px;

                padding: 11px 0;
            }


            .almacen-nombre {

                font-size: 15px;
            }


            .almacen-existencia {

                flex-basis: 52px;

                font-size: 26px;
            }


            .precio {

                font-size: 32px;
            }


            .estado {

                font-size: 13px;
            }

        }

    </style>

</head>


<body>


<div class="card">


    <!-- ========================================= -->
    <!-- TÍTULO -->
    <!-- ========================================= -->

    <div class="titulo">
        Consulta de inventario
    </div>


    <!-- ========================================= -->
    <!-- CÓDIGO -->
    <!-- ========================================= -->

    <div class="label">
        Código
    </div>


    <div class="clave">
        {{ $clave }}
    </div>


    <!-- ========================================= -->
    <!-- CONTENIDO DINÁMICO -->
    <!-- ========================================= -->

    <div id="contenido">

        <div class="procesando">

            <div class="cargando"></div>

            Consultando inventario...

        </div>

    </div>


</div>


<script>


// =====================================================
// CONFIGURACIÓN
// =====================================================

const clave =
    @json($clave);


const API =
    "https://rijaya-inventario-api.netlify.app/api/inventario";


let uuid = null;

let intervalo = null;


// =====================================================
// CREAR CONSULTA
// =====================================================

async function crearConsulta() {

    try {

        const respuesta =
            await fetch(

                API +
                "?accion=crear",

                {

                    method: "POST",

                    headers: {

                        "Content-Type":
                            "application/json"

                    },

                    body: JSON.stringify({

                        clave: clave

                    })

                }

            );


        if (!respuesta.ok) {

            throw new Error(

                "HTTP " +
                respuesta.status

            );
        }


        const datos =
            await respuesta.json();


        if (!datos.ok) {

            throw new Error(

                datos.mensaje ??

                "No se pudo crear la consulta."

            );
        }


        // =========================================
        // GUARDAR UUID
        // =========================================

        uuid =
            datos.consulta.uuid;


        console.log(

            "Consulta creada:",

            uuid

        );


        // =========================================
        // CONSULTAR ESTADO
        // =========================================

        consultarEstado();


        // =========================================
        // CONSULTAR CADA SEGUNDO
        // =========================================

        intervalo =

            setInterval(

                consultarEstado,

                1000

            );

    }

    catch (error) {

        console.error(

            "Error creando consulta:",

            error

        );


        mostrarError(

            error.message

        );

    }

}


// =====================================================
// CONSULTAR ESTADO
// =====================================================

async function consultarEstado() {

    if (!uuid) {

        return;
    }


    try {

        const respuesta =

            await fetch(

                API +
                "?accion=estado&uuid=" +
                encodeURIComponent(uuid)

            );


        if (!respuesta.ok) {

            throw new Error(

                "HTTP " +
                respuesta.status

            );
        }


        const datos =

            await respuesta.json();


        if (!datos.ok) {

            throw new Error(

                datos.mensaje ??

                "Error consultando estado."

            );
        }


        const consulta =

            datos.consulta;


        // =========================================
        // PENDIENTE / PROCESANDO
        // =========================================

        if (

            consulta.estado ===
                "pendiente"

            ||

            consulta.estado ===
                "procesando"

        ) {

            return;
        }


        // =========================================
        // DETENER INTERVALO
        // =========================================

        if (intervalo) {

            clearInterval(intervalo);

            intervalo = null;
        }


        // =========================================
        // ERROR
        // =========================================

        if (

            consulta.estado ===
                "error"

        ) {

            mostrarError(

                consulta.error ??

                "No se pudo consultar el artículo."

            );

            return;
        }


        // =========================================
        // COMPLETADO
        // =========================================

        if (

            consulta.estado ===
                "completado"

        ) {

            mostrarResultado(
                consulta
            );

            return;
        }


        // =========================================
        // ESTADO DESCONOCIDO
        // =========================================

        mostrarError(

            "Estado de consulta desconocido."

        );

    }

    catch (error) {

        console.error(

            "Error consultando estado:",

            error

        );

        /*
         * Los errores temporales de red
         * no detienen la consulta.
         *
         * El navegador seguirá intentando.
         */

    }

}


// =====================================================
// MOSTRAR RESULTADO
// =====================================================

function mostrarResultado(
    consulta
) {


    // =================================================
    // FECHA DE CONSULTA SAE
    // =================================================

    const fechaRespuesta =

        formatearFecha(

            consulta.actualizada

        );


    // =================================================
    // OBTENER ALMACENES
    // =================================================

    let almacenes =

        Array.isArray(

            consulta.almacenes

        )

            ? consulta.almacenes

            : [];


    // =================================================
    // ORDENAR POR CLAVE DE ALMACÉN
    // =================================================

    almacenes =

        [...almacenes].sort(

            (a, b) => {

                return (

                    Number(
                        a.clave ?? 0
                    )

                    -

                    Number(
                        b.clave ?? 0
                    )

                );

            }

        );


    // =================================================
    // GENERAR HTML DE ALMACENES
    // =================================================

    let htmlAlmacenes = "";


    if (

        almacenes.length === 0

    ) {

        htmlAlmacenes = `

            <div class="sin-almacenes">

                No hay existencias
                por almacén disponibles.

            </div>

        `;

    }

    else {

        htmlAlmacenes =

            almacenes.map(

                almacen => {


                    // =================================
                    // EXISTENCIA NUMÉRICA
                    // =================================

                    const numeroExistencia =

                        Number(

                            almacen.existencia ?? 0

                        );


                    // =================================
                    // ¿ESTÁ EN CERO?
                    // =================================

                    const sinExistencia =

                        numeroExistencia === 0;


                    // =================================
                    // CLASE DEL NOMBRE
                    // =================================

                    const claseNombre =

                        sinExistencia

                            ? "almacen-nombre almacen-nombre-sin-existencia"

                            : "almacen-nombre";


                    // =================================
                    // CLASE DE EXISTENCIA
                    // =================================

                    const claseExistencia =

                        sinExistencia

                            ? "almacen-existencia almacen-sin-existencia"

                            : "almacen-existencia";


                    // =================================
                    // NOMBRE
                    // =================================

                    const nombre =

                        escapeHtml(

                            almacen.nombre ??

                            "Almacén sin nombre"

                        );


                    // =================================
                    // EXISTENCIA
                    // =================================

                    const existencia =

                        formatearExistencia(

                            numeroExistencia

                        );


                    // =================================
                    // HTML
                    // =================================

                    return `

                        <div class="almacen">

                            <div
                                class="${claseNombre}"
                            >

                                ${nombre}

                            </div>


                            <div
                                class="${claseExistencia}"
                            >

                                ${existencia}

                            </div>

                        </div>

                    `;

                }

            ).join("");

    }


    // =================================================
    // PRECIO
    // =================================================

    const precio =

        formatearPrecio(

            consulta.precio

        );


    // =================================================
    // ACTUALIZAR PANTALLA
    // =================================================

    document

        .getElementById(
            "contenido"
        )

        .innerHTML = `


            <!-- ===================================== -->
            <!-- DESCRIPCIÓN -->
            <!-- ===================================== -->

            <div class="descripcion">

                ${
                    escapeHtml(

                        consulta.descripcion ??

                        "Sin descripción"

                    )
                }

            </div>


            <!-- ===================================== -->
            <!-- ALMACENES -->
            <!-- ===================================== -->

            <div class="almacenes-titulo">

                Existencias por almacén

            </div>


            <div>

                ${htmlAlmacenes}

            </div>


            <!-- ===================================== -->
            <!-- PRECIO -->
            <!-- ===================================== -->

            <div class="precio-label">

                Precio

            </div>


            <div class="precio">

                ${precio}

            </div>


            <!-- ===================================== -->
            <!-- CONSULTA SAE -->
            <!-- ===================================== -->

            <div class="estado">

                <div class="fecha">

                    Consulta SAE:

                    <strong>

                        ${fechaRespuesta}

                    </strong>

                </div>

            </div>

        `;

}


// =====================================================
// FORMATEAR FECHA
// =====================================================

function formatearFecha(
    fecha
) {

    if (!fecha) {

        return "Sin fecha";
    }


    const fechaObj =
        new Date(fecha);


    if (

        Number.isNaN(

            fechaObj.getTime()

        )

    ) {

        return "Fecha inválida";
    }


    return fechaObj.toLocaleString(

        "es-MX",

        {

            dateStyle: "short",

            timeStyle: "medium"

        }

    );

}


// =====================================================
// FORMATEAR EXISTENCIA
// =====================================================

function formatearExistencia(
    valor
) {

    if (

        valor === null ||

        valor === undefined

    ) {

        return "0";
    }


    const numero =
        Number(valor);


    if (

        Number.isNaN(numero)

    ) {

        return "0";
    }


    // =========================================
    // ENTERO
    // =========================================

    if (

        Number.isInteger(numero)

    ) {

        return numero.toString();
    }


    // =========================================
    // DECIMAL
    // =========================================

    return numero

        .toFixed(4)

        .replace(
            /0+$/,
            ""
        )

        .replace(
            /\.$/,
            ""
        );

}


// =====================================================
// FORMATEAR PRECIO
// =====================================================

function formatearPrecio(
    valor
) {

    if (

        valor === null ||

        valor === undefined

    ) {

        return "No disponible";
    }


    const numero =
        Number(valor);


    if (

        Number.isNaN(numero)

    ) {

        return "No disponible";
    }


    return numero.toLocaleString(

        "es-MX",

        {

            style: "currency",

            currency: "MXN",

            minimumFractionDigits: 2,

            maximumFractionDigits: 2

        }

    );

}


// =====================================================
// MOSTRAR ERROR
// =====================================================

function mostrarError(
    mensaje
) {

    if (intervalo) {

        clearInterval(
            intervalo
        );

        intervalo = null;
    }


    document

        .getElementById(
            "contenido"
        )

        .innerHTML = `

            <div class="error">

                ${escapeHtml(mensaje)}

            </div>

        `;

}


// =====================================================
// PROTEGER HTML
// =====================================================

function escapeHtml(
    texto
) {

    const div =

        document.createElement(
            "div"
        );


    div.textContent =
        texto ?? "";


    return div.innerHTML;

}


// =====================================================
// INICIAR
// =====================================================

crearConsulta();


</script>


</body>

</html>
let actividadActual = null;

window.abrirModal = function (actividad) {
    console.log(actividad);
    actividadActual = actividad.id;
    const totalMinutos = Number(actividad.tiempo_total ?? 0);

    const totalSegundos = totalMinutos * 60;

    const horas = Math.floor(totalSegundos / 3600);
    const minutos = Math.floor((totalSegundos % 3600) / 60);
    const segundos = totalSegundos % 60;

    document.getElementById('detalleTitulo').value =
        actividad.titulo;

    document.getElementById('detalleDescripcion').value =
        actividad.descripcion ?? '';

    document.getElementById('detallePrioridad').value =
        actividad.prioridad.toLowerCase();

    document.getElementById('detalleEstado')
        .innerText =
        actividad.estado.replace('_', ' ').toUpperCase();

    document.getElementById('detalleDisenador').textContent =
        actividad.user?.name ?? 'Sin asignar';

    document.getElementById('detalleCreacion').textContent =
        new Date(actividad.created_at).toLocaleString('es-MX');

    document.getElementById("detalleCliente").textContent =
        actividad.cliente ?? "Sin cliente";

    document.getElementById('detalleTiempo').innerText =
        `${String(horas).padStart(2, '0')}:${String(minutos).padStart(2, '0')}:${String(segundos).padStart(2, '0')}`;

    const diferencia = document.getElementById('detalleDiferencia');

    diferencia.innerText = actividad.diferencia_tiempo_texto;

    diferencia.classList.remove(
        'text-red-600',
        'text-green-600',
        'text-yellow-600'
    );

    if (actividad.diferencia_tiempo_texto.includes('retraso')) {

        diferencia.classList.add('text-red-600');

    } else if (actividad.diferencia_tiempo_texto.includes('antes')) {

        diferencia.classList.add('text-green-600');

    } else {

        diferencia.classList.add('text-yellow-600');

    }

    const estimadoHoras = actividad.tiempo_estimado ?? 0;

    document.getElementById('detalleEstimado').innerText =
        `${estimadoHoras.toString().padStart(2, '0')}:00:00`;

    if (actividad.fecha_inicio) {

        document.getElementById('detalleInicio').textContent =
            new Date(actividad.fecha_inicio).toLocaleString('es-MX');

    } else {

        document.getElementById('detalleInicio').textContent = 'Sin iniciar';

    }
    if (actividad.fecha_limite) {

        const fechaEntrega = new Date(actividad.fecha_limite);

        document.getElementById('detalleEntrega').textContent =
            fechaEntrega.toLocaleDateString('es-MX') + ' ' +
            fechaEntrega.toLocaleTimeString('es-MX', {
                hour: '2-digit',
                minute: '2-digit'
            });

    } else {

        document.getElementById('detalleEntrega').textContent = 'Sin fecha';

    }

    // ==============================
    // ARCHIVOS
    // ==============================

    let archivosContainer =
        document.getElementById("archivosContainer");

    archivosContainer.innerHTML = "";

    const archivos = Array.isArray(actividad.archivos)
        ? actividad.archivos
        : (Array.isArray(actividad.archivos?.data) ? actividad.archivos.data : []);

    if (archivos.length === 0) {
        archivosContainer.innerHTML = `
            <div class="estado-vacio-actividad flex flex-col items-center justify-center text-center px-5 py-10 rounded-xl border border-dashed border-slate-200 bg-white">
                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 text-blue-600 mb-4">
                    <i class="fa-solid fa-paperclip text-2xl"></i>
                </div>
                <h4 class="font-semibold text-slate-800 mb-1">Aún no hay archivos</h4>
                <p class="text-sm text-slate-500 max-w-sm">Adjunta documentos o imágenes relacionados con esta actividad y aparecerán aquí.</p>
            </div>
        `;
    } else {

        archivos.forEach(archivo => {

            const fecha = new Date(archivo.created_at);

            archivosContainer.innerHTML += `

            <div class="flex items-center justify-between bg-slate-50 border rounded-xl p-3">

                <div>

                    <div class="font-semibold text-slate-800">

                        📄 ${archivo.nombre_original}

                    </div>

                    <div class="text-xs text-gray-500 mt-1">

                        👤 ${archivo.user?.name ?? 'Usuario'}

                        <br>

                        📅 ${fecha.toLocaleDateString('es-MX')}
                        ${fecha.toLocaleTimeString('es-MX', {
                hour: '2-digit',
                minute: '2-digit'
            })}

                    </div>

                </div>

                <a
                    href="/archivo/${archivo.id}/descargar"
                    download="${archivo.nombre_original}"
                    title="Descargar archivo"
                    class="w-11 h-11 rounded-full bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center transition-all duration-200 hover:scale-110 shadow-md">

                    <i class="fa-solid fa-download text-lg"></i>

                </a>

            </div>

        `;

        });

    }
    // FORM COMENTARIOS

    document.getElementById('comentarioForm')
        .action =
        `/actividad/${actividad.id}/comentario`;

    // FORM REASIGNAR

    let reasignarForm =
        document.getElementById('reasignarForm');

    if (reasignarForm) {

        reasignarForm.action =
            `/actividad/${actividad.id}/reasignar`;
    }
    // FORM ARCHIVOS

    const archivoForm = document.getElementById('archivoForm');

    if (archivoForm) {

        archivoForm.action =
            `/actividad/${actividad.id}/archivo`;

    }
    // COMENTARIOS

    let container =
        document.getElementById('comentariosContainer');

    container.innerHTML = '';

    const comentarios = Array.isArray(actividad.comentarios)
        ? actividad.comentarios
        : (Array.isArray(actividad.comentarios?.data) ? actividad.comentarios.data : []);

    if (comentarios.length === 0) {
        container.innerHTML = `
            <div class="estado-vacio-actividad flex flex-col items-center justify-center text-center px-5 py-10 rounded-xl border border-dashed border-slate-200 bg-white">
                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 text-blue-600 mb-4">
                    <i class="fa-regular fa-comments text-2xl"></i>
                </div>
                <h4 class="font-semibold text-slate-800 mb-1">Aún no hay comentarios</h4>
                <p class="text-sm text-slate-500 max-w-sm">Escribe un comentario para compartir avances, indicaciones o información importante sobre esta actividad.</p>
            </div>
        `;
    } else {
        comentarios.forEach(comentario => {
            const tarjeta = document.createElement('article');
            tarjeta.className = 'rounded-xl border border-slate-200 bg-white p-4 shadow-sm';

            const encabezado = document.createElement('div');
            encabezado.className = 'font-semibold text-sm text-slate-800 mb-2';
            encabezado.textContent = comentario.user?.name ?? 'Usuario';

            const texto = document.createElement('p');
            texto.className = 'text-sm leading-relaxed text-slate-600 whitespace-pre-wrap break-words';
            texto.textContent = comentario.comentario ?? '';

            tarjeta.append(encabezado, texto);
            container.appendChild(tarjeta);
        });
    }

    document.getElementById('detalleModal')
        .classList.remove('hidden');

    cargarHistorialPausas(actividad.id);
}

window.cerrarModal = function () {

    document.getElementById('detalleModal')
        .classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', () => {

    const botonGuardar = document.getElementById('guardarActividad');

    if (!botonGuardar) return;

    botonGuardar.addEventListener('click', guardarActividad);

});

async function guardarActividad() {

    const titulo = document.getElementById('detalleTitulo').value;
    const descripcion = document.getElementById('detalleDescripcion').value;
    const prioridad = document.getElementById('detallePrioridad').value;

    const token = document.querySelector('meta[name="csrf-token"]').content;

    try {

        const respuesta = await fetch(`/actividad/${actividadActual}`, {

            method: 'PUT',

            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },

            body: JSON.stringify({

                titulo,
                descripcion,
                prioridad

            })

        });

        const datos = await respuesta.json();

        if (datos.success) {

            alert(datos.message);

            cerrarModal();

            location.reload();

        } else {

            alert('No fue posible guardar los cambios.');

        }

    } catch (error) {

        console.error(error);

        alert('Ocurrió un error al guardar.');

    }

}

function mostrarLottie(nombre, texto) {

    const modal = document.getElementById("animationModal");
    const player = document.getElementById("animationPlayer");
    const titulo = document.getElementById("animationText");

    titulo.textContent = texto;

    player.load("/lottie/" + nombre);

    modal.classList.remove("hidden");

    setTimeout(() => {

        modal.classList.add("hidden");

    }, 1800);

}

function iniciarActividad(id) {

    mostrarLottie(
        "Startup.json",
        "🚀 Iniciando actividad..."
    );

    setTimeout(() => {

        document.getElementById("formIniciar" + id).submit();

    }, 1800);

}
let actividadPendienteDePausa = null;

function pausarActividad(id) {
    const formulario = document.getElementById("formPausar" + id);
    const modal = document.getElementById('modalPausaActividad');
    const campo = document.getElementById('motivoPausaActividad');
    const error = document.getElementById('errorMotivoPausa');

    if (!formulario) {
        window.alert('No se encontró el formulario para pausar esta actividad.');
        return;
    }
    if (!modal || !campo) {
        window.alert('No se encontró el modal de pausa. Actualiza la página e inténtalo de nuevo.');
        return;
    }

    actividadPendienteDePausa = id;
    campo.value = '';
    if (error) error.classList.add('hidden');
    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
    setTimeout(() => campo.focus(), 100);
}

function cerrarModalPausa() {
    const modal = document.getElementById('modalPausaActividad');
    if (modal) {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
    }
    document.body.classList.remove('overflow-hidden');
    actividadPendienteDePausa = null;
}

function confirmarPausaActividad() {
    const id = actividadPendienteDePausa;
    const campo = document.getElementById('motivoPausaActividad');
    const error = document.getElementById('errorMotivoPausa');
    const formulario = id ? document.getElementById('formPausar' + id) : null;
    const motivo = (campo?.value || '').trim();

    if (motivo.length < 3) {
        if (error) {
            error.textContent = 'Escribe un motivo de al menos 3 caracteres.';
            error.classList.remove('hidden');
        }
        campo?.focus();
        return;
    }
    if (!formulario) {
        if (error) {
            error.textContent = 'No se encontró el formulario para pausar esta actividad.';
            error.classList.remove('hidden');
        }
        return;
    }

    let campoMotivo = formulario.querySelector('input[name="motivo_pausa"]');
    if (!campoMotivo) {
        campoMotivo = document.createElement('input');
        campoMotivo.type = 'hidden';
        campoMotivo.name = 'motivo_pausa';
        formulario.appendChild(campoMotivo);
    }
    campoMotivo.value = motivo;
    cerrarModalPausa();
    mostrarLottie('Coffee Time.json', '☕ Pausando actividad...');
    setTimeout(() => formulario.submit(), 1800);
}

window.pausarActividad = pausarActividad;
window.cerrarModalPausa = cerrarModalPausa;
window.confirmarPausaActividad = confirmarPausaActividad;

async function cargarHistorialPausas(id) {
    const contenedor = document.getElementById('historialPausasContainer');
    const contador = document.getElementById('contadorPausas');
    if (!contenedor || !contador) return;

    contenedor.innerHTML = '<p class="text-sm text-slate-500">Cargando historial...</p>';
    contador.textContent = '…';

    try {
        const respuesta = await fetch(`/actividad/${id}/bitacora-pausas`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            cache: 'no-store'
        });
        const datos = await respuesta.json();
        if (!respuesta.ok || !datos.success) throw new Error('No se pudo cargar el historial.');

        const pausas = Array.isArray(datos.pausas) ? datos.pausas : [];
        contador.textContent = String(pausas.length);
        contenedor.innerHTML = '';

        if (!pausas.length) {
            contenedor.innerHTML = `
                <div class="estado-vacio-actividad flex flex-col items-center justify-center text-center px-5 py-8 rounded-xl border border-dashed border-amber-200 bg-white">
                    <div class="flex items-center justify-center w-14 h-14 rounded-full mb-3" style="background:#fff7ed;color:#d97706">
                        <i class="fa-solid fa-mug-hot text-xl"></i>
                    </div>
                    <h4 class="font-semibold text-slate-800 mb-1">Sin pausas registradas</h4>
                    <p class="text-sm text-slate-500 max-w-sm">Cuando se pause esta actividad, aquí verás el motivo, quién la pausó y la fecha.</p>
                </div>
            `;
            return;
        }

        pausas.forEach(pausa => {
            const tarjeta = document.createElement('article');
            tarjeta.className = 'rounded-xl border border-amber-100 bg-amber-50/60 p-4';

            const encabezado = document.createElement('div');
            encabezado.className = 'flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-2';

            const usuario = document.createElement('p');
            usuario.className = 'text-sm font-semibold text-slate-800';
            usuario.textContent = pausa.usuario || 'Usuario';

            const fecha = document.createElement('p');
            fecha.className = 'text-xs text-slate-500';
            fecha.textContent = pausa.fecha ? new Date(pausa.fecha).toLocaleString('es-MX') : 'Fecha no disponible';
            encabezado.append(usuario, fecha);

            const motivo = document.createElement('p');
            motivo.className = 'text-sm text-slate-700 whitespace-pre-wrap break-words';
            const descripcion = String(pausa.descripcion || 'Sin descripción');
            motivo.textContent = descripcion.replace(/^Motivo de pausa:\s*/i, '');

            tarjeta.append(encabezado, motivo);
            contenedor.appendChild(tarjeta);
        });
    } catch (error) {
        console.error('Error al cargar historial de pausas:', error);
        contador.textContent = 'Error';
        contenedor.innerHTML = '<p class="text-sm text-red-600">No se pudo cargar el historial de pausas. Intenta cerrar y abrir el detalle nuevamente.</p>';
    }
}
function reanudarActividad(id) {

    mostrarLottie(
        "Rotate arrow.json",
        "🔄 Reanudando actividad..."
    );

    setTimeout(() => {

        document.getElementById("formReanudar" + id).submit();

    }, 1800);

}
function terminarActividad(id) {

    mostrarLottie(
        "Confeti.json",
        "🎉 Terminando actividad..."
    );

    setTimeout(() => {

        document.getElementById("formTerminar" + id).submit();

    }, 1800);

}

window.mostrarLottie = mostrarLottie;
window.iniciarActividad = iniciarActividad;
window.pausarActividad = pausarActividad;
window.reanudarActividad = reanudarActividad;
window.terminarActividad = terminarActividad;

// ==============================
// PREVISUALIZAR ARCHIVOS
// ==============================

let archivosSeleccionados = [];

document.addEventListener("DOMContentLoaded", () => {

    const inputArchivos = document.getElementById("archivos");

    if (!inputArchivos) return;

    inputArchivos.addEventListener("change", function () {

        archivosSeleccionados = Array.from(this.files);

        actualizarLista();

    });

});

function actualizarLista() {

    const lista = document.getElementById("listaArchivos");

    const input = document.getElementById("archivos");

    if (!lista || !input) return;

    lista.innerHTML = "";

    archivosSeleccionados.forEach((archivo, index) => {

        const tamaño = archivo.size < 1024 * 1024
            ? (archivo.size / 1024).toFixed(1) + " KB"
            : (archivo.size / (1024 * 1024)).toFixed(2) + " MB";

        lista.innerHTML += `
            <div class="flex items-center justify-between rounded-xl border bg-slate-50 p-3">

                <div class="flex items-center gap-3">

                    <span class="text-xl">📄</span>

                    <div>

                        <div class="font-medium">
                            ${archivo.name}
                        </div>

                        <div class="text-xs text-gray-500">
                            ${tamaño}
                        </div>

                    </div>

                </div>

                <button
                    type="button"
                    onclick="eliminarArchivo(${index})"
                    class="text-red-600 hover:text-red-800 transition">

                    <i class="fa-solid fa-trash"></i>

                </button>

            </div>
        `;

    });

    const dt = new DataTransfer();

    archivosSeleccionados.forEach(file => dt.items.add(file));

    input.files = dt.files;

}

window.eliminarArchivo = function(index){

    archivosSeleccionados.splice(index, 1);

    actualizarLista();

}
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

    if (!actividad.archivos || actividad.archivos.length === 0) {

        archivosContainer.innerHTML = `
        <div class="text-gray-400 italic">
            No hay archivos adjuntos.
        </div>
    `;

    } else {

        actividad.archivos.forEach(archivo => {

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
                    href="/storage/app/public/archivos/${archivo.archivo}"
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

    if (actividad.comentarios) {

        actividad.comentarios.forEach(comentario => {

            container.innerHTML += `

                    <div class="bg-gray-100 rounded-xl p-3">

                        <div class="font-bold mb-1">
                            ${comentario.user.name}
                        </div>

                        <div class="text-gray-700">
                            ${comentario.comentario}
                        </div>

                    </div>

                `;
        });
    }

    document.getElementById('detalleModal')
        .classList.remove('hidden');
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
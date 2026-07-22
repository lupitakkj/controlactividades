let actividadActual = null;

window.abrirModal = function (actividad) {
    console.log(actividad);
    actividadActual = actividad.id;
    const minutos = actividad.tiempo_total ?? 0;

    const horas = Math.floor(minutos / 60);
    const mins = minutos % 60;
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

    document.getElementById('detalleTiempo').innerText =
        `${horas.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}`;
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
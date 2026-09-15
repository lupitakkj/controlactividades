import { Calendar } from '@fullcalendar/core';

import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';

import esLocale from '@fullcalendar/core/locales/es';

document.addEventListener('DOMContentLoaded', () => {

    const calendarEl = document.getElementById('calendar');

    if (!calendarEl) return;

    const calendar = new Calendar(calendarEl, {

        // ==========================================
        // PLUGINS
        // ==========================================

        plugins: [
            dayGridPlugin,
            timeGridPlugin,
            interactionPlugin,
            listPlugin
        ],

        // ==========================================
        // IDIOMA
        // ==========================================

        locale: esLocale,

        // ==========================================
        // VISTA INICIAL
        // ==========================================

        initialView: 'dayGridMonth',

        // ==========================================
        // ENCABEZADO
        // ==========================================

        headerToolbar: {
            left: 'prev,next today title',
            center: '',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },

        // ==========================================
        // TEXTOS DE BOTONES
        // ==========================================

        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día',
            list: 'Agenda'
        },

        // ==========================================
        // CONFIGURACIÓN
        // ==========================================

        firstDay: 1,

        navLinks: true,

        selectable: true,

        editable: true,

        dayMaxEvents: true,

        weekends: true,

        nowIndicator: true,

        expandRows: true,

        height: 'auto',

        contentHeight: 'auto',

        eventDisplay: 'block',

        // ==========================================
        // EVENTOS DESDE LARAVEL
        // ==========================================

        events: '/agenda/eventos',

        // ==========================================
        // CLIC EN UN DÍA
        // ==========================================

        dateClick: function (info) {

            abrirModal(info);

        },

        // ==========================================
        // CLIC EN UNA RESERVACIÓN
        // ==========================================

        eventClick: function (info) {

            console.log('CLICK EN EVENTO');

            console.log(info.event);

            abrirModalEvento(info.event);

        }

    });

    // ==========================================
    // MOSTRAR CALENDARIO
    // ==========================================

    calendar.render();


    // ==========================================
    // ABRIR MODAL
    // ==========================================

    function abrirModal(info) {

        // ------------------------------------------
        // Abrir modal
        // ------------------------------------------

        document
            .getElementById('modalReserva')
            ?.classList.remove('hidden');


        // ------------------------------------------
        // Campo fecha
        // ------------------------------------------

        const inputFecha =
            document.getElementById('fechaEvento');

        if (inputFecha) {

            inputFecha.value =
                info.dateStr.split('T')[0];

        }


        // ------------------------------------------
        // Campos hora
        // ------------------------------------------

        const horaInicio =
            document.getElementById('horaInicio');

        const horaFin =
            document.getElementById('horaFin');


        // ------------------------------------------
        // Si estamos en Semana o Día
        // ------------------------------------------

        if (
            info.view.type === 'timeGridWeek' ||
            info.view.type === 'timeGridDay'
        ) {

            const inicio =
                new Date(info.date);

            const fin =
                new Date(info.date);

            fin.setHours(
                fin.getHours() + 1
            );


            if (horaInicio) {

                horaInicio.value =
                    inicio
                        .toTimeString()
                        .substring(0, 5);

            }


            if (horaFin) {

                horaFin.value =
                    fin
                        .toTimeString()
                        .substring(0, 5);

            }

        } else {

            // --------------------------------------
            // Vista mensual
            // --------------------------------------

            if (horaInicio) {

                horaInicio.value = '';

            }

            if (horaFin) {

                horaFin.value = '';

            }

        }


        // ------------------------------------------
        // Cursor en asunto
        // ------------------------------------------

        const asunto =
            document.getElementById('asuntoEvento');

        if (asunto) {

            asunto.focus();

        }

    }

});

function abrirModalEvento(evento) {

    console.log('ABRIENDO MODAL');
    console.log(evento.extendedProps);

    const modal = document.getElementById('modalEvento');

    if (!modal) {

        console.error('NO EXISTE #modalEvento');

        return;
    }

    const props = evento.extendedProps || {};

    document.getElementById('detalleTitulo').textContent =
        evento.title || 'Sin título';

    document.getElementById('detalleAsunto').textContent =
        evento.title || 'Sin asunto';

    document.getElementById('detalleTipo').textContent =
        props.tipo || 'Sin especificar';

    document.getElementById('detalleRecurso').textContent =
        props.recurso_id || 'Sin recurso';

    document.getElementById('detallePersonas').textContent =
        props.personas || 'Sin especificar';

    document.getElementById('detalleDescripcion').textContent =
        props.descripcion || 'Sin descripción';

    if (evento.start) {

        const fecha = new Date(evento.start);

        document.getElementById('detalleFecha').textContent =
            fecha.toLocaleDateString('es-MX', {
                weekday: 'long',
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });

        document.getElementById('detalleHorario').textContent =
            obtenerHora(evento.start) +
            ' - ' +
            (evento.end
                ? obtenerHora(evento.end)
                : 'Sin hora');

    }

    modal.classList.remove('hidden');
}

function obtenerHora(fecha) {

    const fechaObj = new Date(fecha);

    return fechaObj.toLocaleTimeString('es-MX', {
        hour: '2-digit',
        minute: '2-digit'
    });

}

window.cerrarModalEvento = function () {

    const modal = document.getElementById('modalEvento');

    if (modal) {

        modal.classList.add('hidden');

    }

};
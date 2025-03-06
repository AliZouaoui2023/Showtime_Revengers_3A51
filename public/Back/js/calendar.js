import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';

document.addEventListener('DOMContentLoaded', function() {
    let calendarEl = document.getElementById('calendar');

    if (!calendarEl) {
        console.error("Élément #calendar introuvable !");
        return;
    }

    fetch('/api/projections')
        .then(response => response.json())
        .then(events => {
            let calendar = new Calendar(calendarEl, {
                plugins: [dayGridPlugin],
                initialView: 'dayGridMonth',
                locale: 'fr',
                events: events.map(event => ({
                    title: `Projection`,
                    start: event.date,
                    color: event.color
                }))
            });

            calendar.render();
        })
        .catch(error => console.error('Erreur lors du chargement des projections:', error));
});

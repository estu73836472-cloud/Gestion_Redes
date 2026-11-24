document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.getElementById('tablaIncidencias');
    const form = document.getElementById('incidentForm');
    if (!tableBody || !form) return;

    async function loadDevices() {
        const select = document.getElementById('incidentDevice');
        try {
            const devices = await apiRequest('/api/dispositivos');
            select.innerHTML = '<option value="">Selecciona un dispositivo</option>' + devices.map((device) =>
                `<option value="${escapeHtml(device.id)}">${escapeHtml(device.nombre)} (${escapeHtml(device.ip)})</option>`
            ).join('');
        } catch (error) {
            select.innerHTML = '<option value="">No se pudieron cargar los dispositivos</option>';
            showAlert('incidentAlerts', 'danger', error.message);
        }
    }

    async function loadIncidents() {
        tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-secondary py-4">Cargando incidencias...</td></tr>';
        try {
            const incidents = await apiRequest('/api/incidencias');
            renderIncidents(incidents);
        } catch (error) {
            tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-danger py-4">No se pudieron cargar las incidencias.</td></tr>';
            showAlert('incidentAlerts', 'danger', error.message);
        }
    }

    function renderIncidents(incidents) {
        if (!incidents.length) {
            tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-secondary py-4">No hay incidencias registradas.</td></tr>';
            return;
        }
        tableBody.innerHTML = incidents.map((incident) => {
            const severity = String(incident.gravedad || 'baja');
            const resolveButton = incident.estado === 'resuelta'
                ? '<span class="text-secondary">—</span>'
                : `<button class="btn btn-sm btn-outline-success" type="button" data-action="resolve" data-id="${escapeHtml(incident.id)}">Marcar resuelta</button>`;
            return `<tr>
                <td>${escapeHtml(incident.id)}</td>
                <td>${escapeHtml(incident.dispositivo_nombre || `#${incident.dispositivo_id}`)}${incident.dispositivo_ip ? `<br><small class="text-secondary">${escapeHtml(incident.dispositivo_ip)}</small>` : ''}</td>
                <td><span title="${escapeHtml(incident.descripcion)}">${escapeHtml(incident.titulo)}</span></td>
                <td><span class="badge bg-${badgeGravedadClass(severity)}">${escapeHtml(severity)}</span></td>
                <td>${badgeEstadoIncidencia(incident.estado)}</td>
                <td>${formatDateTime(incident.fecha_reporte)}</td>
                <td>${resolveButton}</td>
            </tr>`;
        }).join('');
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        try {
            const result = await apiRequest('/api/incidencias', {
                method: 'POST',
                body: {
                    dispositivo_id: document.getElementById('incidentDevice').value,
                    titulo: document.getElementById('incidentTitle').value.trim(),
                    descripcion: document.getElementById('incidentDescription').value.trim(),
                    gravedad: document.getElementById('incidentSeverity').value
                }
            });
            showAlert('incidentAlerts', 'success', result.mensaje || 'Incidencia registrada.');
            form.reset();
            await Promise.all([loadIncidents(), loadDevices()]);
        } catch (error) {
            showAlert('incidentAlerts', 'danger', error.message);
        } finally {
            submitButton.disabled = false;
        }
    });

    tableBody.addEventListener('click', async (event) => {
        const button = event.target.closest('button[data-action="resolve"]');
        if (!button) return;
        button.disabled = true;
        try {
            await apiRequest(`/api/incidencias/${encodeURIComponent(button.dataset.id)}/estado`, {
                method: 'PATCH',
                body: { estado: 'resuelta' }
            });
            showAlert('incidentAlerts', 'success', 'Incidencia marcada como resuelta.');
            await loadIncidents();
        } catch (error) {
            button.disabled = false;
            showAlert('incidentAlerts', 'danger', error.message);
        }
    });

    document.getElementById('refreshIncidents').addEventListener('click', loadIncidents);
    loadDevices();
    loadIncidents();
});

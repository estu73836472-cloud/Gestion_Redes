document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.getElementById('tablaMonitoreo');
    const refreshButton = document.getElementById('refreshMonitoring');
    if (!tableBody || !refreshButton) return;

    async function loadHistory() {
        refreshButton.disabled = true;
        tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-secondary py-4">Cargando historial...</td></tr>';
        try {
            const records = await apiRequest('/api/monitoreo');
            renderHistory(records);
        } catch (error) {
            tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-danger py-4">No se pudo cargar el historial.</td></tr>';
            showAlert('monitoringAlerts', 'danger', error.message);
        } finally {
            refreshButton.disabled = false;
        }
    }

    function renderHistory(records) {
        if (!records.length) {
            tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-secondary py-4">No hay registros de monitoreo.</td></tr>';
            return;
        }
        tableBody.innerHTML = records.map((record) => `<tr>
            <td>${escapeHtml(record.id)}</td>
            <td>${escapeHtml(record.dispositivo_nombre || `#${record.dispositivo_id}`)}${record.dispositivo_ip ? `<br><small class="text-secondary">${escapeHtml(record.dispositivo_ip)}</small>` : ''}</td>
            <td>${badgeEstado(record.estado)}</td>
            <td>${record.latencia_ms === null || record.latencia_ms === undefined ? '—' : escapeHtml(record.latencia_ms)}</td>
            <td>${record.uso_cpu === null || record.uso_cpu === undefined ? '—' : escapeHtml(record.uso_cpu)}</td>
            <td>${record.uso_memoria === null || record.uso_memoria === undefined ? '—' : escapeHtml(record.uso_memoria)}</td>
            <td>${formatDateTime(record.fecha_monitoreo)}</td>
        </tr>`).join('');
    }

    refreshButton.addEventListener('click', loadHistory);
    loadHistory();
});

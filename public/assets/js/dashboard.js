document.addEventListener('DOMContentLoaded', () => {
    const refreshButton = document.getElementById('refreshDashboard');
    if (!refreshButton) return;

    async function loadDashboard() {
        refreshButton.disabled = true;
        try {
            const [deviceMetrics, incidentMetrics, history] = await Promise.all([
                apiRequest('/api/dispositivos/metricas'),
                apiRequest('/api/incidencias/metricas'),
                apiRequest('/api/monitoreo?limit=1')
            ]);

            document.getElementById('deviceTotal').textContent = Number(deviceMetrics.total || 0);
            document.getElementById('deviceOnline').textContent = Number(deviceMetrics.en_linea || 0);
            document.getElementById('deviceOffline').textContent = Number(deviceMetrics.fuera_de_linea || 0);
            document.getElementById('openIncidents').textContent = Number(incidentMetrics.abiertas || 0);

            const latest = history[0];
            document.getElementById('lastScanAt').textContent = latest
                ? `${formatDateTime(latest.fecha_monitoreo)} · ${latest.dispositivo_nombre || `Dispositivo #${latest.dispositivo_id}`}`
                : 'Todavía no hay registros de monitoreo.';
        } catch (error) {
            showAlert('dashboardAlerts', 'danger', error.message);
        } finally {
            refreshButton.disabled = false;
        }
    }

    refreshButton.addEventListener('click', loadDashboard);
    loadDashboard();
});

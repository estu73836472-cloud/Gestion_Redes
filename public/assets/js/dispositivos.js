document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.getElementById('tablaDispositivos');
    const form = document.getElementById('deviceForm');
    if (!tableBody || !form) return;

    let devices = [];
    const lastLatencies = new Map();

    async function loadDevices() {
        tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-secondary py-4">Cargando dispositivos...</td></tr>';
        try {
            devices = await apiRequest('/api/dispositivos');
            renderDevices();
        } catch (error) {
            tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-danger py-4">No se pudieron cargar los dispositivos.</td></tr>';
            showAlert('deviceAlerts', 'danger', error.message);
        }
    }

    function renderDevices() {
        if (!devices.length) {
            tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-secondary py-4">No hay dispositivos registrados.</td></tr>';
            return;
        }

        tableBody.innerHTML = devices.map((device) => {
            const id = escapeHtml(device.id);
            const latency = lastLatencies.has(String(device.id))
                ? `${escapeHtml(lastLatencies.get(String(device.id)))} ms`
                : '—';
            return `<tr id="device-row-${id}">
                <td>${id}</td>
                <td>${escapeHtml(device.nombre)}</td>
                <td>${escapeHtml(device.ip)}</td>
                <td>${escapeHtml(device.tipo)}</td>
                <td>${escapeHtml(device.ubicacion || '—')}</td>
                <td id="device-state-${id}">${badgeEstado(device.estado)}</td>
                <td id="device-latency-${id}">${latency}</td>
                <td class="text-nowrap">
                    <button class="btn btn-sm btn-outline-primary" type="button" data-action="ping" data-id="${id}">Comprobar Ping</button>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-action="edit" data-id="${id}">Editar</button>
                    <button class="btn btn-sm btn-outline-danger" type="button" data-action="delete" data-id="${id}">Eliminar</button>
                </td>
            </tr>`;
        }).join('');
    }

    function clearDeviceForm() {
        form.reset();
        form.dataset.editingId = '';
        document.getElementById('deviceFormTitle').textContent = 'Registrar dispositivo';
        document.getElementById('saveDevice').innerHTML = '<i class="bi bi-check-lg" aria-hidden="true"></i>';
        document.getElementById('saveDevice').setAttribute('aria-label', 'Guardar dispositivo');
        document.getElementById('cancelDeviceEdit').classList.add('d-none');
    }

    function updateDeviceStatus(result) {
        const device = devices.find((item) => String(item.id) === String(result.dispositivo_id));
        if (device) device.estado = result.estado;

        const stateCell = document.getElementById(`device-state-${result.dispositivo_id}`);
        const latencyCell = document.getElementById(`device-latency-${result.dispositivo_id}`);
        if (stateCell) stateCell.innerHTML = badgeEstado(result.estado);
        if (latencyCell) {
            const latency = result.latencia_ms;
            lastLatencies.set(String(result.dispositivo_id), latency);
            latencyCell.textContent = latency === null || latency === undefined ? '—' : `${latency} ms`;
        }
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const editingId = form.dataset.editingId;
        const payload = {
            nombre: document.getElementById('deviceName').value.trim(),
            ip: document.getElementById('deviceIp').value.trim(),
            tipo: document.getElementById('deviceType').value.trim(),
            ubicacion: document.getElementById('deviceLocation').value.trim()
        };
        const button = document.getElementById('saveDevice');
        button.disabled = true;

        try {
            const result = await apiRequest(
                editingId ? `/api/dispositivos/${encodeURIComponent(editingId)}` : '/api/dispositivos',
                { method: editingId ? 'PUT' : 'POST', body: payload }
            );
            showAlert('deviceAlerts', 'success', result.mensaje || (editingId ? 'Dispositivo actualizado.' : 'Dispositivo registrado.'));
            clearDeviceForm();
            await loadDevices();
        } catch (error) {
            showAlert('deviceAlerts', 'danger', error.message);
        } finally {
            button.disabled = false;
        }
    });

    tableBody.addEventListener('click', async (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;

        const device = devices.find((item) => String(item.id) === button.dataset.id);
        if (!device) return;

        if (button.dataset.action === 'edit') {
            form.dataset.editingId = String(device.id);
            document.getElementById('deviceName').value = device.nombre;
            document.getElementById('deviceIp').value = device.ip;
            document.getElementById('deviceType').value = device.tipo;
            document.getElementById('deviceLocation').value = device.ubicacion || '';
            document.getElementById('deviceFormTitle').textContent = `Editar dispositivo #${device.id}`;
            document.getElementById('saveDevice').innerHTML = '<i class="bi bi-save" aria-hidden="true"></i>';
            document.getElementById('saveDevice').setAttribute('aria-label', 'Guardar cambios');
            document.getElementById('cancelDeviceEdit').classList.remove('d-none');
            document.getElementById('deviceName').focus();
            return;
        }

        if (button.dataset.action === 'delete') {
            if (!window.confirm(`¿Eliminar el dispositivo "${device.nombre}"?`)) return;
            button.disabled = true;
            try {
                const result = await apiRequest(`/api/dispositivos/${encodeURIComponent(device.id)}`, { method: 'DELETE' });
                showAlert('deviceAlerts', 'success', result.mensaje || 'Dispositivo eliminado.');
                await loadDevices();
            } catch (error) {
                showAlert('deviceAlerts', 'danger', error.message);
                button.disabled = false;
            }
            return;
        }

        if (button.dataset.action === 'ping') {
            const originalText = button.textContent;
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-label="Comprobando"></span>';
            try {
                const result = await apiRequest(`/api/monitoreo/ping/${encodeURIComponent(device.id)}`, { method: 'POST' });
                updateDeviceStatus(result);
                showAlert('deviceAlerts', 'success', `${device.nombre}: ${result.estado}${result.latencia_ms === null ? '' : `, ${result.latencia_ms} ms`}.`);
            } catch (error) {
                showAlert('deviceAlerts', 'danger', error.message);
            } finally {
                button.disabled = false;
                button.textContent = originalText;
            }
        }
    });

    document.getElementById('cancelDeviceEdit').addEventListener('click', clearDeviceForm);
    document.getElementById('refreshDevices').addEventListener('click', loadDevices);

    document.getElementById('scanNetwork').addEventListener('click', async (event) => {
        const button = event.currentTarget;
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Escaneando...';
        try {
            const result = await apiRequest('/api/monitoreo/ping-all', { method: 'POST' });
            (result.dispositivos || []).forEach(updateDeviceStatus);
            showAlert('deviceAlerts', 'success', `Escaneo completado: ${result.total_verificados} dispositivo(s) revisado(s).`);
        } catch (error) {
            showAlert('deviceAlerts', 'danger', error.message);
        } finally {
            button.disabled = false;
            button.innerHTML = originalText;
        }
    });

    loadDevices();
});

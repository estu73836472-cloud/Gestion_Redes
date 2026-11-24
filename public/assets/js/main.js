function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function deviceCode(device) {
    return `DEV-${String(device.id).padStart(3, '0')}`;
}

function statusDetails(status) {
    const values = {
        en_linea: ['Activo', 'online'],
        fuera_de_linea: ['En falla', 'offline'],
        advertencia: ['Advertencia', 'warning'],
        desconocido: ['Sin monitorear', 'unknown']
    };
    return values[status] || ['Desconocido', 'unknown'];
}

function statusBadge(status) {
    const [label, className] = statusDetails(status);
    return `<span class="status-badge ${className}">${label}</span>`;
}

function showMessage(element, message, kind = 'error') {
    if (!element) return;
    element.className = `notice notice-${kind}`;
    element.textContent = message;
    element.hidden = false;
}

async function apiRequest(url, options = {}) {
    const response = await fetch(url, {
        ...options,
        headers: {
            Accept: 'application/json',
            ...(options.body ? { 'Content-Type': 'application/json' } : {}),
            ...options.headers
        }
    });
    let payload;
    try {
        payload = await response.json();
    } catch (error) {
        throw new Error(`El servidor devolvió una respuesta no válida (${response.status}).`);
    }
    if (!response.ok || payload.success === false) {
        throw new Error(payload.message || `La solicitud falló (${response.status}).`);
    }
    return Object.prototype.hasOwnProperty.call(payload, 'data') ? payload.data : payload;
}

function formatDate(value) {
    if (!value) return 'Sin registro';
    const date = new Date(String(value).replace(' ', 'T'));
    if (Number.isNaN(date.getTime())) return String(value);
    return new Intl.DateTimeFormat('es', { dateStyle: 'medium', timeStyle: 'short' }).format(date);
}

function deviceIcon(type) {
    const icons = { router: '⌁', switch: '▤', firewall: '⛨', 'punto de acceso': '⌁', servidor: '▣' };
    return icons[String(type || '').toLocaleLowerCase()] || '▣';
}

function deviceTypeLabel(type) {
    const labels = { router: 'Router', switch: 'Switch', firewall: 'Firewall', 'punto de acceso': 'Punto de acceso', servidor: 'Servidor', otro: 'Otro' };
    const key = String(type || '').toLocaleLowerCase();
    return labels[key] || String(type || 'Otro');
}

async function loadDevices() {
    const devices = await apiRequest('/api/dispositivos');
    if (!Array.isArray(devices)) throw new Error('La API devolvió un inventario con formato inesperado.');
    return devices;
}

function renderDashboardDevices(devices) {
    const target = document.getElementById('dashboard-devices');
    if (!target) return;
    if (!devices.length) {
        target.innerHTML = '<tr><td class="empty-cell" colspan="4">Aún no hay dispositivos en el inventario.</td></tr>';
        return;
    }
    target.innerHTML = devices.slice(0, 5).map((device) => `
        <tr>
            <td><a href="/dispositivos/${Number(device.id)}"><strong>${escapeHtml(device.nombre)}</strong><small>${escapeHtml(deviceCode(device))}</small></a></td>
            <td class="mono">${escapeHtml(device.ip)}</td>
            <td>${escapeHtml(deviceTypeLabel(device.tipo))}</td>
            <td>${statusBadge(device.estado)}</td>
        </tr>`).join('');
}

async function initDashboard() {
    const devicesTarget = document.getElementById('dashboard-devices');
    const incidentsTarget = document.getElementById('dashboard-incidents');
    const tasks = [];
    if (devicesTarget) {
        tasks.push(loadDevices().then((devices) => {
            renderDashboardDevices(devices);
            const values = {
                total: devices.length,
                online: devices.filter((device) => device.estado === 'en_linea').length,
                offline: devices.filter((device) => device.estado === 'fuera_de_linea').length
            };
            Object.entries(values).forEach(([key, value]) => {
                const node = document.querySelector(`[data-metric="${key}"]`);
                if (node) node.textContent = String(value);
            });
        }).catch((error) => {
            devicesTarget.innerHTML = `<tr><td class="empty-cell" colspan="4">${escapeHtml(error.message)}</td></tr>`;
        }));
    }
    if (incidentsTarget) {
        tasks.push(apiRequest('/api/incidencias').then((incidents) => {
            if (!Array.isArray(incidents)) throw new Error('La API devolvió incidencias con formato inesperado.');
            const openIncidents = incidents.filter((incident) => incident.estado === 'abierta');
            const metric = document.querySelector('[data-metric="incidents"]');
            if (metric) metric.textContent = String(openIncidents.length);
            incidentsTarget.innerHTML = openIncidents.length ? openIncidents.slice(0, 4).map((incident) => `
                <article class="activity-item"><span class="activity-dot"></span><div>
                    <strong>${escapeHtml(incident.titulo)}</strong>
                    <p>${escapeHtml(incident.dispositivo_nombre || `Dispositivo #${incident.dispositivo_id}`)} · Gravedad ${escapeHtml(incident.gravedad)}</p>
                    <small>${escapeHtml(formatDate(incident.fecha_reporte))}</small>
                </div></article>`).join('') : '<p class="loading-cell">No hay incidencias abiertas.</p>';
        }).catch((error) => {
            incidentsTarget.innerHTML = `<p class="loading-cell">${escapeHtml(error.message)}</p>`;
        }));
    }
    await Promise.all(tasks);
}

function renderDeviceCards(devices) {
    const grid = document.getElementById('device-grid');
    const count = document.getElementById('device-count');
    if (!grid) return;
    if (count) count.textContent = `${devices.length} dispositivo${devices.length === 1 ? '' : 's'} registrado${devices.length === 1 ? '' : 's'}`;
    if (!devices.length) {
        grid.innerHTML = '<div class="panel empty-state"><strong>No se encontraron dispositivos</strong><p>Ajusta los filtros o registra un nuevo equipo.</p></div>';
        return;
    }
    grid.innerHTML = devices.map((device) => `
        <article class="device-card">
            <div class="device-card-top"><span class="metric-icon blue">${escapeHtml(deviceIcon(device.tipo))}</span>${statusBadge(device.estado)}</div>
            <p class="device-code">${escapeHtml(deviceCode(device))}</p>
            <h2 title="${escapeHtml(device.nombre)}">${escapeHtml(device.nombre)}</h2>
            <span class="device-ip">${escapeHtml(device.ip)}</span>
            <div class="device-meta"><span>⌖ ${escapeHtml(device.ubicacion || 'Ubicación no especificada')}</span><span>▤ ${escapeHtml(deviceTypeLabel(device.tipo))}</span></div>
            <div class="device-card-footer"><span>${escapeHtml(device.mac || 'Sin MAC registrada')}</span><a class="text-link" href="/dispositivos/${Number(device.id)}">Ver dispositivo ↗</a></div>
        </article>`).join('');
}

function initDevices() {
    const grid = document.getElementById('device-grid');
    if (!grid) return;
    const search = document.getElementById('device-search');
    const typeFilter = document.getElementById('device-type-filter');
    const statusFilter = document.getElementById('device-status-filter');
    const errorBox = document.getElementById('devices-error');
    const form = document.getElementById('device-quick-form');
    const dialog = document.getElementById('device-dialog');
    let devices = [];
    const refresh = () => {
        const query = search.value.trim().toLocaleLowerCase();
        const type = typeFilter.value;
        const status = statusFilter.value;
        const filtered = devices.filter((device) => {
            const text = `${deviceCode(device)} ${device.nombre} ${device.ip}`.toLocaleLowerCase();
            return (!query || text.includes(query)) && (!type || device.tipo === type) && (!status || device.estado === status);
        });
        renderDeviceCards(filtered);
    };
    [search, typeFilter, statusFilter].forEach((input) => input.addEventListener(input === search ? 'input' : 'change', refresh));
    const refreshInventory = async () => {
        try {
            const data = await loadDevices();
            devices = data;
            const existingTypes = new Set([...typeFilter.options].map((option) => option.value));
            [...new Set(devices.map((device) => device.tipo).filter(Boolean))].sort().forEach((type) => {
                if (!existingTypes.has(type)) typeFilter.add(new Option(deviceTypeLabel(type), type));
            });
            errorBox.hidden = true;
            refresh();
            return true;
        } catch (error) {
            grid.innerHTML = '';
            showMessage(errorBox, `No fue posible cargar el inventario: ${error.message}`);
            document.getElementById('device-count').textContent = 'Inventario no disponible';
            return false;
        }
    };
    const openDialog = () => {
        document.getElementById('device-dialog-message').hidden = true;
        if (typeof dialog.showModal === 'function') dialog.showModal();
        else dialog.setAttribute('open', '');
    };
    const closeDialog = () => {
        if (typeof dialog.close === 'function') dialog.close();
        else dialog.removeAttribute('open');
    };
    document.getElementById('open-device-dialog').addEventListener('click', openDialog);
    document.getElementById('close-device-dialog').addEventListener('click', closeDialog);
    document.getElementById('cancel-device-dialog').addEventListener('click', closeDialog);
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const submit = form.querySelector('[type="submit"]');
        const message = document.getElementById('device-dialog-message');
        const values = Object.fromEntries(new FormData(form).entries());
        Object.keys(values).forEach((key) => {
            if (typeof values[key] === 'string') values[key] = values[key].trim();
        });
        submit.disabled = true;
        message.hidden = true;
        try {
            const result = await apiRequest('/api/dispositivos', {
                method: 'POST',
                body: JSON.stringify(values)
            });
            const device = result.dispositivo || result;
            form.reset();
            closeDialog();
            showMessage(document.getElementById('device-success'), `${deviceCode(device)} · ${device.nombre} se registró correctamente.`, 'success');
            await refreshInventory();
        } catch (error) {
            showMessage(message, error.message);
        } finally {
            submit.disabled = false;
        }
    });
    const registeredId = new URLSearchParams(location.search).get('registrado');
    refreshInventory().then((loaded) => {
        if (loaded && registeredId) {
            const device = devices.find((item) => String(item.id) === registeredId);
            if (device) showMessage(document.getElementById('device-success'), `${deviceCode(device)} · ${device.nombre} se registró correctamente.`, 'success');
        }
    });
}

function initDeviceForm() {
    const form = document.getElementById('device-form');
    if (!form) return;
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const submit = form.querySelector('[type="submit"]');
        const message = document.getElementById('device-form-message');
        const values = Object.fromEntries(new FormData(form).entries());
        Object.keys(values).forEach((key) => {
            if (typeof values[key] === 'string') values[key] = values[key].trim();
        });
        submit.disabled = true;
        message.hidden = true;
        try {
            const id = form.dataset.deviceId;
            const result = await apiRequest(id ? `/api/dispositivos/${encodeURIComponent(id)}` : '/api/dispositivos', {
                method: id ? 'PUT' : 'POST',
                body: JSON.stringify(values)
            });
            const device = result.dispositivo || result;
            location.href = id ? `/dispositivos/${encodeURIComponent(id)}?actualizado=1` : `/dispositivos?registrado=${encodeURIComponent(device.id)}`;
        } catch (error) {
            showMessage(message, error.message);
            message.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } finally {
            submit.disabled = false;
        }
    });
}

function initDeviceDetail() {
    const button = document.querySelector('[data-ping-device]');
    if (!button) return;
    button.addEventListener('click', async () => {
        const result = document.getElementById('detail-ping-result');
        button.disabled = true;
        result.textContent = 'Consultando el dispositivo…';
        try {
            const data = await apiRequest(`/api/monitoreo/ping/${encodeURIComponent(button.dataset.pingDevice)}`, { method: 'POST' });
            const [label] = statusDetails(data.estado);
            result.textContent = `${label}${data.latencia_ms == null ? '' : ` · ${data.latencia_ms} ms`}. ${data.detalles || ''}`;
            const badge = document.querySelector('.status-card .status-badge');
            if (badge) {
                badge.outerHTML = statusBadge(data.estado);
            }
        } catch (error) {
            result.textContent = `No fue posible consultar el dispositivo: ${error.message}`;
        } finally {
            button.disabled = false;
        }
    });
}

function initMonitor() {
    const table = document.getElementById('monitor-table');
    if (!table) return;
    const codeInput = document.getElementById('monitor-code');
    const message = document.getElementById('monitor-message');
    let devices = [];
    const render = (items) => {
        document.getElementById('monitor-heading').textContent = items.length === devices.length ? 'Dispositivos en monitoreo' : 'Resultados de búsqueda';
        document.getElementById('monitor-count').textContent = `${items.length} dispositivo${items.length === 1 ? '' : 's'} mostrado${items.length === 1 ? '' : 's'}`;
        document.getElementById('monitor-online').textContent = `${items.filter((d) => d.estado === 'en_linea').length} activos`;
        document.getElementById('monitor-offline').textContent = `${items.filter((d) => d.estado === 'fuera_de_linea').length} en falla`;
        if (!items.length) {
            table.innerHTML = '<tr><td colspan="6" class="empty-cell">No existe un dispositivo con ese código. Revisa el código o vuelve a mostrar todos.</td></tr>';
            return;
        }
        table.innerHTML = items.map((device) => `
            <tr>
                <td><strong class="mono">${escapeHtml(deviceCode(device))}</strong></td>
                <td><a href="/dispositivos/${Number(device.id)}"><strong>${escapeHtml(device.nombre)}</strong><small>${escapeHtml(deviceTypeLabel(device.tipo))}</small></a></td>
                <td class="mono">${escapeHtml(device.ip)}</td><td>${statusBadge(device.estado)}</td>
                <td>${escapeHtml(formatDate(device.ultimo_monitoreo))}</td>
                <td><button class="inline-action" type="button" data-ping="${Number(device.id)}">⌁ Consultar</button></td>
            </tr>`).join('');
    };
    const filterByCode = () => {
        const query = codeInput.value.trim().toLocaleLowerCase();
        if (!query) return render(devices);
        const results = devices.filter((device) => deviceCode(device).toLocaleLowerCase() === query);
        render(results);
        if (!results.length) showMessage(message, `No existe un dispositivo con el código “${codeInput.value.trim()}”.`);
        else message.hidden = true;
    };
    document.getElementById('monitor-search-form').addEventListener('submit', (event) => {
        event.preventDefault();
        filterByCode();
    });
    document.getElementById('monitor-clear').addEventListener('click', () => {
        codeInput.value = '';
        message.hidden = true;
        render(devices);
    });
    table.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-ping]');
        if (!button) return;
        button.disabled = true;
        button.textContent = 'Consultando…';
        try {
            const result = await apiRequest(`/api/monitoreo/ping/${encodeURIComponent(button.dataset.ping)}`, { method: 'POST' });
            showMessage(message, `${result.nombre}: ${statusDetails(result.estado)[0]}${result.latencia_ms == null ? '' : ` · ${result.latencia_ms} ms`}.`, 'success');
            devices = await loadDevices();
            filterByCode();
        } catch (error) {
            showMessage(message, `No fue posible completar la consulta: ${error.message}`);
            button.disabled = false;
            button.textContent = '⌁ Consultar';
        }
    });
    document.getElementById('ping-all').addEventListener('click', async (event) => {
        const button = event.currentTarget;
        button.disabled = true;
        button.textContent = 'Consultando…';
        message.hidden = true;
        try {
            const result = await apiRequest('/api/monitoreo/ping-all', { method: 'POST' });
            showMessage(message, `Consulta completada. ${result.total_verificados} dispositivos verificados.`, 'success');
            devices = await loadDevices();
            filterByCode();
        } catch (error) {
            showMessage(message, `No fue posible consultar la red: ${error.message}`);
        } finally {
            button.disabled = false;
            button.textContent = '⌁ Consultar todos';
        }
    });
    apiRequest('/api/dispositivos').then((data) => {
        if (!Array.isArray(data)) throw new Error('La API devolvió un inventario con formato inesperado.');
        devices = data;
        const initialCode = new URLSearchParams(location.search).get('codigo');
        if (initialCode) codeInput.value = initialCode;
        filterByCode();
    }).catch((error) => {
        table.innerHTML = `<tr><td colspan="6" class="empty-cell">No fue posible cargar los dispositivos: ${escapeHtml(error.message)}</td></tr>`;
        document.getElementById('monitor-count').textContent = 'Inventario no disponible';
    });
}

function initIncidents() {
    const table = document.getElementById('incident-table');
    if (!table) return;
    const filter = document.getElementById('incident-status-filter');
    const pageMessage = document.getElementById('incident-page-message');
    const panel = document.getElementById('incident-form-panel');
    const form = document.getElementById('incident-form');
    let incidents = [];
    const render = () => {
        const visible = incidents.filter((incident) => !filter.value || incident.estado === filter.value);
        if (!visible.length) {
            table.innerHTML = '<tr><td colspan="6" class="empty-cell">No hay incidencias para mostrar con este filtro.</td></tr>';
            return;
        }
        table.innerHTML = visible.map((incident) => `
            <tr>
                <td><strong>${escapeHtml(incident.titulo)}</strong><small>#${Number(incident.id)} · ${escapeHtml(incident.descripcion)}</small></td>
                <td><a href="/dispositivos/${Number(incident.dispositivo_id)}"><strong>${escapeHtml(incident.dispositivo_nombre || `Dispositivo #${incident.dispositivo_id}`)}</strong><small class="mono">${escapeHtml(incident.dispositivo_ip || '')}</small></a></td>
                <td><span class="severity-badge ${escapeHtml(incident.gravedad)}">${escapeHtml(incident.gravedad)}</span></td>
                <td>${escapeHtml(formatDate(incident.fecha_reporte))}</td>
                <td>${escapeHtml(incident.estado.replace('_', ' '))}</td>
                <td><label class="sr-only" for="incident-state-${Number(incident.id)}">Estado de ${escapeHtml(incident.titulo)}</label><select class="state-select" id="incident-state-${Number(incident.id)}" data-incident-state="${Number(incident.id)}">
                    <option value="abierta" ${incident.estado === 'abierta' ? 'selected' : ''}>Abierta</option>
                    <option value="en_proceso" ${incident.estado === 'en_proceso' ? 'selected' : ''}>En proceso</option>
                    <option value="resuelta" ${incident.estado === 'resuelta' ? 'selected' : ''}>Resuelta</option>
                </select></td>
            </tr>`).join('');
    };
    const refresh = async () => {
        try {
            const results = await Promise.all([
                apiRequest('/api/incidencias'),
                apiRequest('/api/incidencias/metricas')
            ]);
            incidents = results[0];
            if (!Array.isArray(incidents)) throw new Error('La API devolvió incidencias con formato inesperado.');
            Object.entries(results[1]).forEach(([key, value]) => {
                const node = document.querySelector(`[data-incident-metric="${key}"]`);
                if (node) node.textContent = String(value);
            });
            render();
        } catch (error) {
            table.innerHTML = `<tr><td colspan="6" class="empty-cell">No fue posible cargar las incidencias: ${escapeHtml(error.message)}</td></tr>`;
            showMessage(pageMessage, error.message);
        }
    };
    const setFormOpen = (open) => {
        panel.hidden = !open;
        if (open) panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };
    document.getElementById('toggle-incident-form').addEventListener('click', () => setFormOpen(true));
    document.getElementById('close-incident-form').addEventListener('click', () => setFormOpen(false));
    document.getElementById('cancel-incident-form').addEventListener('click', () => setFormOpen(false));
    filter.addEventListener('change', render);
    table.addEventListener('change', async (event) => {
        const select = event.target.closest('[data-incident-state]');
        if (!select) return;
        select.disabled = true;
        try {
            await apiRequest(`/api/incidencias/${encodeURIComponent(select.dataset.incidentState)}/estado`, {
                method: 'PATCH',
                body: JSON.stringify({ estado: select.value })
            });
            showMessage(pageMessage, 'El estado de la incidencia se actualizó correctamente.', 'success');
            await refresh();
        } catch (error) {
            showMessage(pageMessage, `No fue posible actualizar el estado: ${error.message}`);
            await refresh();
        }
    });
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const button = form.querySelector('[type="submit"]');
        const message = document.getElementById('incident-form-message');
        button.disabled = true;
        message.hidden = true;
        const data = Object.fromEntries(new FormData(form).entries());
        try {
            await apiRequest('/api/incidencias', { method: 'POST', body: JSON.stringify(data) });
            form.reset();
            showMessage(message, 'La incidencia se registró correctamente.', 'success');
            await refresh();
        } catch (error) {
            showMessage(message, error.message);
        } finally {
            button.disabled = false;
        }
    });
    apiRequest('/api/dispositivos').then((devices) => {
        if (!Array.isArray(devices)) throw new Error('La API devolvió un inventario con formato inesperado.');
        const select = document.getElementById('incident-device');
        devices.forEach((device) => select.add(new Option(`${deviceCode(device)} · ${device.nombre}`, device.id)));
        const selectedId = new URLSearchParams(location.search).get('dispositivo_id');
        if (selectedId) {
            select.value = selectedId;
            setFormOpen(true);
        }
    }).catch((error) => {
        const select = document.getElementById('incident-device');
        select.disabled = true;
        showMessage(document.getElementById('incident-form-message'), `No se pudieron cargar los dispositivos: ${error.message}`);
    });
    refresh();
}

document.addEventListener('DOMContentLoaded', () => {
    initDashboard();
    initDevices();
    initDeviceForm();
    initDeviceDetail();
    initMonitor();
    initIncidents();
});

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

async function apiRequest(url, options = {}) {
    const requestOptions = { ...options };
    requestOptions.headers = {
        Accept: 'application/json',
        ...(options.headers || {})
    };

    if (options.body && typeof options.body !== 'string') {
        requestOptions.headers['Content-Type'] = 'application/json';
        requestOptions.body = JSON.stringify(options.body);
    }

    let response;
    try {
        response = await fetch(url, requestOptions);
    } catch (error) {
        throw new Error('No fue posible conectar con la API. Verifica que el servidor esté activo.');
    }

    let payload;
    try {
        payload = await response.json();
    } catch (error) {
        throw new Error('La API devolvió una respuesta que no es JSON válido.');
    }

    if (!response.ok || payload.success === false) {
        throw new Error(payload.message || `La solicitud falló (${response.status}).`);
    }

    return payload.data;
}

function showAlert(containerId, type, message) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.replaceChildren();
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.setAttribute('role', type === 'danger' ? 'alert' : 'status');
    alert.append(document.createTextNode(message));

    const close = document.createElement('button');
    close.type = 'button';
    close.className = 'btn-close';
    close.setAttribute('data-bs-dismiss', 'alert');
    close.setAttribute('aria-label', 'Cerrar');
    alert.append(close);
    container.append(alert);
}

function badgeEstado(estado) {
    const labels = {
        en_linea: ['success', 'En línea'],
        fuera_de_linea: ['danger', 'Fuera de línea'],
        advertencia: ['warning text-dark', 'Advertencia'],
        desconocido: ['secondary', 'Desconocido']
    };
    const [style, label] = labels[estado] || labels.desconocido;
    return `<span class="badge bg-${style}">${label}</span>`;
}

function badgeGravedadClass(gravedad) {
    const classes = {
        baja: 'secondary',
        media: 'primary',
        alta: 'danger'
    };
    return classes[gravedad] || 'secondary';
}

function badgeEstadoIncidencia(estado) {
    const statuses = {
        abierta: ['danger', 'Abierta'],
        en_proceso: ['warning text-dark', 'En proceso'],
        resuelta: ['success', 'Resuelta']
    };
    const [style, label] = statuses[estado] || ['secondary', 'Desconocido'];
    return `<span class="badge bg-${style}">${label}</span>`;
}

function formatDateTime(value) {
    if (!value) return '—';
    const date = new Date(String(value).replace(' ', 'T'));
    return Number.isNaN(date.getTime()) ? escapeHtml(value) : escapeHtml(date.toLocaleString('es'));
}

document.addEventListener('DOMContentLoaded', async () => {
    const healthBadge = document.getElementById('apiHealthBadge');
    if (!healthBadge) return;

    try {
        const response = await fetch('/api/health', { headers: { Accept: 'application/json' } });
        const data = await response.json();
        if (response.ok && data.status === 'online' && data.database?.connected) {
            healthBadge.className = 'badge text-bg-success';
            healthBadge.textContent = 'API y base de datos conectadas';
        } else {
            healthBadge.className = 'badge text-bg-warning';
            healthBadge.textContent = 'Base de datos desconectada';
            healthBadge.title = data.database?.message || 'Revisa la configuración de la base de datos.';
        }
    } catch (error) {
        healthBadge.className = 'badge text-bg-danger';
        healthBadge.textContent = 'API no disponible';
    }
});

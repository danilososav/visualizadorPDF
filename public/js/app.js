    const API_URL = '/api';
    let currentPage = 1;

    // Cargar agencias al iniciar
    document.addEventListener('DOMContentLoaded', async () => {
    await cargarAgencias();
});

    async function cargarAgencias() {
    try {
        const response = await fetch('/api/agencias');
        const agencias = await response.json();

        const select = document.getElementById('agencia');
        agencias.forEach(agencia => {
            const option = document.createElement('option');
            option.value = agencia;
            option.textContent = agencia;
            select.appendChild(option);
        });
    } catch (error) {
        console.error('Error cargando agencias:', error);
    }
}

    async function buscar(page = 1) {
        const agencia = document.getElementById('agencia').value;
        const numeroFactura = document.getElementById('buscar').value;

        if (!agencia && !numeroFactura) {
            mostrarError('Por favor selecciona una agencia o ingresa un número de factura');
            return;
        }

        currentPage = page;
        mostrarLoading(true);
        ocultarError();

        try {
            let url = `${API_URL}/facturas`;
            const params = new URLSearchParams();

            if (agencia) params.append('empresa', agencia);
            if (numeroFactura) params.append('q', numeroFactura);
            params.append('page', page);

            if (params.toString()) {
                url += '?' + params.toString();
            }

            const response = await fetch(url);
            if (!response.ok) throw new Error('Error en la solicitud');

            const data = await response.json();

            mostrarResultados(data.data);
            mostrarPaginacion(data);
        } catch (error) {
            mostrarError('Error al buscar facturas: ' + error.message);
        } finally {
            mostrarLoading(false);
        }
    }

    function mostrarResultados(facturas) {
        const tbody = document.getElementById('tbody');
        const tabla = document.getElementById('resultados');
        const noResults = document.getElementById('noResults');

        tbody.innerHTML = '';

        if (facturas.length === 0) {
            tabla.style.display = 'none';
            noResults.style.display = 'block';
            return;
        }

        tabla.style.display = 'table';
        noResults.style.display = 'none';

        facturas.forEach(factura => {
            const fecha = new Date(factura.fecha_emision).toLocaleDateString('es-PY');
            const row = tbody.insertRow();
            row.innerHTML = `
                <td>${factura.numero}</td>
                <td>${factura.empresa.nombre}</td>
                <td>${factura.cliente.nombre}</td>
                <td>${fecha}</td>
                <td>${parseFloat(factura.total).toLocaleString('es-PY', {style: 'currency', currency: 'USD'})}</td>
                <td>
                    <a href="/api/facturas/${factura.id}" class="btn-descargar" target="_blank">
                        👁️ Ver PDF
                    </a>
                </td>
            `;
        });
    }

    function mostrarPaginacion(data) {
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';

        if (data.last_page <= 1) return;

        for (let i = 1; i <= data.last_page; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.onclick = () => buscar(i);
            if (i === data.current_page) btn.classList.add('active');
            pagination.appendChild(btn);
        }
    }

    function mostrarLoading(show) {
        document.getElementById('loading').className = show ? 'loading active' : 'loading';
    }

    function mostrarError(mensaje) {
        const error = document.getElementById('error');
        error.textContent = mensaje;
        error.classList.add('active');
    }

    function ocultarError() {
        document.getElementById('error').classList.remove('active');
    }

    // Enter en el input de búsqueda
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('buscar').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') buscar();
        });
    });

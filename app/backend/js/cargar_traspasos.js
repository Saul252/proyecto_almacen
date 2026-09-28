function cargarTraspasos() {
    const almacenId = document.getElementById('admin_filtro_almacen')?.value || '';
    const contenedorArribos = document.getElementById('contenedor-arribos');
    const contenedorEnvios = document.getElementById('contenedor-envios');

    contenedorArribos.innerHTML = '<tr><td colspan="6" class="text-center">Buscando movimientos...</td></tr>';
fetch(`/myvet/app/controllers/traspasosController.php?action=resumen&almacen_id=${almacenId}`)
    
     .then(response => response.json())
        .then(data => {
            
            // --- FUNCIÓN INTERNA PARA CONVERSIÓN VISUAL ---
            const formatearCantidad = (cant, factor, unidad) => {
                const c = parseFloat(cant);
                const f = parseFloat(factor) || 1;
                const u = unidad || 'Unid.';

                if (f > 1 && c >= f) {
                    const entero = Math.floor(c / f);
                    const resto = Math.round((c % f) * 100) / 100;
                    let texto = `<strong>${entero} ${u}</strong>`;
                    if (resto > 0) texto += ` + ${resto} pzas`;
                    return texto;
                }
                return `<strong>${c}</strong> pzas`;
            };

            // 1. LLENAR ARRIBOS
            contenedorArribos.innerHTML = '';
            if (data.arribos.length === 0) {
                contenedorArribos.innerHTML = '<tr><td colspan="6" class="text-center text-body-secondary">No hay mercancía pendiente de recibir.</td></tr>';
            } else {
                data.arribos.forEach(mov => {
                    const cantDisplay = formatearCantidad(mov.cantidad, mov.factor_conversion, mov.unidad_reporte);
                    
                    contenedorArribos.innerHTML += `
                        <tr>
                            <td>${mov.fecha}</td>
                            <td><small class="fw-bold">${mov.sku}</small><br>${mov.producto}</td>
                            <td class="text-primary">
                                ${cantDisplay}<br>
                                <small class="text-body-secondary" style="font-size:0.7rem">Total: ${mov.cantidad} pzas</small>
                            </td>
                            <td>${mov.origen}</td>
                            <td>${mov.enviado_por}</td>
                            <td>
                                <button class="btn btn-success btn-sm" onclick="autorizarRecibo(${mov.id})">
                                    <i class="bi bi-check-circle"></i> Recibir
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }

            // 2. LLENAR ENVÍOS
            contenedorEnvios.innerHTML = '';
            if (data.envios.length === 0) {
                contenedorEnvios.innerHTML = '<tr><td colspan="5" class="text-center text-body-secondary">No se encontraron envíos realizados.</td></tr>';
            } else {
                data.envios.forEach(mov => {
                    const cantDisplay = formatearCantidad(mov.cantidad, mov.factor_conversion, mov.unidad_reporte);
                    const badgeClass = mov.estado === 'Completado' ? 'bg-success' : 'bg-warning text-dark';
                    
                    contenedorEnvios.innerHTML += `
                        <tr>
                            <td>${mov.fecha}</td>
                            <td>${mov.producto}</td>
                            <td>
                                ${cantDisplay}<br>
                                <small class="text-body-secondary" style="font-size:0.7rem">Total: ${mov.cantidad} pzas</small>
                            </td>
                            <td>${mov.destino}</td>
                            <td><span class="badge ${badgeClass}">${mov.estado}</span></td>
                        </tr>
                    `;
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            contenedorArribos.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error al cargar datos.</td></tr>';
        });
}
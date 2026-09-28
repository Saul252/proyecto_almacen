<style>
    /* Reset y base */
    .margen {
        /* Usamos calc para restar el sidebar del ancho total y evitar el desborde */
       
        padding: 20px; /* Reducido de 40px a 20px para dar más espacio al contenido */
        width: auto; 
        max-width: 100%;
        box-sizing: border-box; /* Asegura que padding no sume al ancho total */
        overflow-x: hidden;
    }
    
    .card-ios {
       
        border-radius: 12px;
        
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        margin-bottom: 15px;
        width: 100%; /* Forzamos a que no exceda el padre */
        overflow: hidden;
    }

    .table-monitor {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
        table-layout: auto; /* Cambiado a auto para que las columnas se ajusten dinámicamente */
    }

    /* Estilos Desktop */
    @media (min-width: 768px) {
        .table-monitor thead th {
            font-size: 0.65rem;
            color: #8e8e93;
            text-transform: uppercase;
            padding: 10px 5px;
           
        }

        .table-monitor tbody td {
            padding: 12px 8px;
            vertical-align: middle;
            border-top: 1px solid #f2f2f7;
            font-size: 0.85rem;
            /* Control de desborde interno de celdas */
            max-width: 150px; 
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    }

    /* --- SOLUCIÓN AL DESBORDE MÓVIL --- */
    @media (max-width: 767px) {
        .margen {
            margin-left: 0 !important; 
            padding: 10px;
            width: 100vw; /* Aseguramos que use el ancho de la ventana */
        }

        .table-monitor thead {
            display: none;
        }

        .table-monitor tbody tr {
            display: block;
            padding: 12px;
            border-bottom: 8px solid #f2f2f7;
            width: 100%;
            box-sizing: border-box;
        }

        .table-monitor tbody td {
            display: flex; /* Cambiado a flex para mejor control de espacio */
            justify-content: space-between;
            width: 100% !important;
            padding: 6px 0;
            
            font-size: 0.85rem;
        }

        .table-monitor td::before {
            content: attr(data-label);
            font-weight: 700;
            color: #8e8e93;
            font-size: 0.7rem;
            text-transform: uppercase;
            margin-right: 15px;
            flex-shrink: 0; /* Que la etiqueta no se encoga */
        }
        
        /* Alineamos el contenido a la derecha en móvil para que se vea como ficha */
        .table-monitor td > * {
            text-align: right;
            word-break: break-word; /* Romper palabras largas si es necesario */
        }
    }
</style>

<div class="container-fluid mt-3 margen">
    <div class="card-ios">
        <div class="d-flex justify-content-between align-items-center p-3 header-movil">
            <h6 class="m-0 fw-bold">Monitor de Entregas</h6>
           
        </div>
    </div>

    <div class="card-ios">
        <table class="table-monitor">
            <thead>
                <tr>
                    <th class="col-m text-center">M</th>
                    <th class="col-ref">Folio</th>
                    <th class="col-cli">Cliente</th>
                    <th class="col-prod">Producto</th>
                    <th class="col-cant">Cantidad</th>
                    <th class="col-resp">Responsable</th>
                    <th class="col-fecha text-center">Fecha</th>
                    <th class="col-ver"></th>
                </tr>
            </thead>
            <tbody id="tbodyMonitor">
                </tbody>
        </table>
        <div class="p-3 text-center border-top">
            <button class="btn btn-sm btn-link text-decoration-none w-100" id="btnCargarMas" onclick="cargarMas()" style="font-size: 0.75rem; font-weight: 600;">MOSTRAR MÁS REGISTROS</button>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/detalleMonitor.php' ?>
<?php require_once __DIR__ . '/detalleRuta.php' ?>

<script>
let offsetActual = 0;
const limiteCarga = 25;

$(document).ready(function() { 
    cargarMonitor(); 
});

function cargarMonitor() {
    offsetActual = 0;
    const idAlmacen = $('#filtroAlmacen').val();
    $('#tbodyMonitor').html('<tr><td colspan="8" class="text-center py-5"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>');

    $.ajax({
        url: '/myvet/app/controllers/repartosController.php',
        type: 'GET',
        data: { action: 'get_monitor_entregas', almacen_id: idAlmacen, inicio: offsetActual, limite: limiteCarga },
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data.length > 0) { 
                renderizarFilas(response.data, false); 
            }
            else { 
                $('#tbodyMonitor').html('<tr><td colspan="8" class="text-center text-muted py-5"><i class="bi bi-patch-check d-block fs-2 mb-2"></i>No hay movimientos pendientes.</td></tr>'); 
                $('#btnCargarMas').hide();
            }
        }
    });
}
function renderizarFilas(data, append) {
    let html = '';
    const tbody = $('#tbodyMonitor');

    data.forEach(row => {
        // --- 1. LÓGICA DE IDENTIFICACIÓN ---
        const tipo = row.tipo_salida; // 'RUTA' o 'MOSTRADOR'
        const idParaModal = (tipo === 'RUTA') ? (row.reparto_id || 0) : (row.movimiento_id || 0);

        // --- 2. PREPARACIÓN DE VARIABLES (UI) ---
        const numRuta = (row.numero_ruta && row.numero_ruta != '0') ? row.numero_ruta : row.reparto_id;
        const rawCliente = (row.cliente_display || '').toLowerCase().trim();
        const esRutaEspecial = (rawCliente === '' || rawCliente === 'null' || rawCliente.includes('varios clientes'));
        
        const folioHTML = (!esRutaEspecial && row.identificador_visual && row.identificador_visual != '0') 
            ? `#${row.identificador_visual}` 
            : `<span class="text-primary fw-bold">R-${numRuta}</span>`;

        const clienteHTML = esRutaEspecial 
            ? `<span class="text-primary fw-bold">RUTA #${numRuta}</span>` 
            : (row.cliente_display || 'VENTA GENERAL');
        
        const iconModo = (tipo === 'RUTA') 
            ? '<i class="bi bi-truck text-primary fs-5"></i>' 
            : '<i class="bi bi-shop text-success fs-5"></i>';

        // --- 3. SELECCIÓN DE BOTÓN Y FUNCIÓN DESTINO ---
        let botonAccion = '';
        
        if (tipo === 'RUTA') {
            // Botón Azul para Rutas -> Llama a abrirModalRuta
            botonAccion = `
                <button type="button" 
        class="btn shadow-sm btn-sm rounded-circle " 
        style="background: #e7f1ff; width: 32px; height: 32px;"
        onclick="verDetalleViaje('${row.numero_ruta}')"> 
    <i class="bi bi-geo-alt-fill text-primary"></i>
</button>`;
        } else {
            // Botón Gris estándar para Movimientos -> Llama a verDetalleEntrega (la que ya tienes)
            botonAccion = `
                <button type="button" 
                        class="btn shadow-sm btn-sm rounded-circle " 
                        style="background: #f0f0f5; width: 32px; height: 32px;"
                        onclick="verDetalleEntrega('${tipo}', ${idParaModal})">
                    <i class="bi bi-chevron-right text-primary"></i>
                </button>`;
        }

        // --- 4. CONSTRUCCIÓN DE LA FILA ---
        html += `
            <tr class="align-middle">
                <td class="text-center">${iconModo}</td>
                <td>
                    <span class="txt-bold d-block">${folioHTML}</span>
                    <small class="badge ${tipo === 'RUTA' ? 'bg-primary-subtle text-primary' : 'bg-success-subtle text-success'}" style="font-size: 0.6rem;">${tipo}</small>
                </td>
                <td>
                    <div title="${row.cliente_display}" class="txt-bold text-truncate" style="max-width: 180px;">
                        ${clienteHTML}
                    </div>
                </td>
                <td>
                    <div title="${row.producto_nombre}" class="txt-sub text-truncate" style="max-width: 200px;">
                        ${row.producto_nombre}
                    </div>
                </td>
                <td>
                    <span class="txt-bold d-block">${row.lectura_fisica || '0'}</span>
                    <small class="text-muted" style="font-size: 0.6rem;">LOTES: ${row.lotes_involucrados || 'S/L'}</small>
                </td>
                <td>
                    <span class="txt-bold d-block" style="font-size: 0.75rem;">${row.responsable || 'POR ASIGNAR'}</span>
                    <span class="txt-sub" style="font-size: 0.65rem;">${row.vehiculo || ''}</span>
                </td>
                <td class="text-center">
                    <span class="txt-sub fw-bold">${row.fecha_evento ? row.fecha_evento.split(' ')[0] : '---'}</span>
                    <small class="d-block text-muted" style="font-size: 0.6rem;">${row.fecha_evento ? row.fecha_evento.split(' ')[1] : ''}</small>
                </td>
                <td class="text-center">
                   ${botonAccion}
                </td>
            </tr>`;
    });

    append ? tbody.append(html) : tbody.html(html);
    (data.length < limiteCarga) ? $('#btnCargarMas').hide() : $('#btnCargarMas').show();
}
function cargarMas() {
    offsetActual += limiteCarga;
    const idAlmacen = $('#filtroAlmacen').val();
    $.ajax({
        url: '/myvet/app/controllers/repartosController.php',
        type: 'GET',
        data: { action: 'get_monitor_entregas', almacen_id: idAlmacen, inicio: offsetActual, limite: limiteCarga },
        dataType: 'json',
        success: function(response) { 
            if(response.success) { renderizarFilas(response.data, true); } 
        }
    });
}

/**
 * Función que abre el modal iOS con bandera de tipo
 * @param {string} tipo - 'RUTA' o 'MOSTRADOR'
 * @param {number} id - ID del reparto o del movimiento
 */
async function verDetalleEntrega(tipo, id) {
    if (!id || id === 0) {
        console.error("ID inválido recibido.");
        return;
    }

    // Loader inicial
    $('#contenedor_detalle_ios').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="text-muted small mt-2">Cargando trazabilidad...</p></div>');
    
    const modalEl = document.getElementById('modalVerDetalle');
    const instance = bootstrap.Modal.getOrCreateInstance(modalEl);
    instance.show();

    try {
        // Enviamos la bandera 'tipo' al controlador para que sepa qué modelo consultar
        const url = `/myvet/app/controllers/repartosController.php?action=get_detalle_trazabilidad&tipo=${tipo}&id=${id}`;
        const response = await fetch(url);
        const res = await response.json();

        if (!res.success) throw new Error(res.message);

        // Esta función debe estar definida para pintar el HTML según el tipo
        pintarHTMLDetalle(tipo, res.data);

    } catch (error) {
        console.error("Error Fetch:", error);
        $('#contenedor_detalle_ios').html('<div class="alert alert-danger mx-3 mt-3 small">Hubo un error al obtener el detalle de la entrega.</div>');
    }
}
</script>
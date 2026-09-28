  <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
<?php 

renderizarLayout($paginaActual);

$id_venta = intval($_GET['id'] ?? 0);
?>

<style>
    :root { --sidebar-width: 0px; --navbar-height: 65px; }
   
    .main-content {  padding: 40px;transition: all 0.3s ease; }
    .card-edit {  border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; }
    .resumen-cabecera { background: #4e73df; color: white; border-radius: 12px 12px 0 0; padding: 20px; }
    .input-group-text { min-width: 85px; font-size: 0.75rem; justify-content: center; background-color: #f8f9fc; }
    .select-precio { font-size: 0.8rem; padding: 2px 5px; border-radius: 5px; border: 1px solid #d1d3e2; }
    @media (max-width: 768px) { .main-content { margin-left: 0; padding: 20px; padding-top: 90px; } }
</style>

<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0 text-dark">Edición de Venta</h2>
            <p class="text-body-secondary">Ajuste de precios y unidades para Folio: <span id="folio_titulo" class="fw-bold text-primary">...</span></p>
        </div>
        <button class="btn btn-light rounded-pill shadow-sm" onclick="history.back()">
            <i class="bi bi-arrow-left"></i> Volver
        </button>
    </div>

    <div class="card card-edit">
        <div class="resumen-cabecera">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <small class="text-uppercase opacity-75">Cliente</small>
                    <h5 id="lbl_cliente" class="fw-bold m-0">Cargando...</h5>
                </div>
                <div class="col-md-4 text-center">
                    <small class="text-uppercase opacity-75">Fecha</small>
                    <h5 id="lbl_fecha" class="m-0">--/--/----</h5>
                </div>
                <div class="col-md-4 text-md-end">
                    <small class="text-uppercase opacity-75">ID Interno</small>
                    <h5 class="m-0">#<?= $id_venta ?></h5>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <form id="formEditarVenta">
                <!-- <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white"><i class="bi bi-search"></i></span>
                            <input type="text" id="buscarProducto" class="form-control" placeholder="Buscar producto para agregar...">
                            <div id="resultadosBusqueda" class="list-group position-absolute w-100" style="top: 40px; z-index: 1000; display: none;"></div>
                        </div>
                    </div>
                </div> -->
                
                <input type="hidden" id="edit_venta_id" value="<?= $id_venta ?>">
                <input type="hidden" id="edit_cliente_id"> 
                
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="text-body-secondary small fw-bold text-uppercase">
                            <tr>
                                <th style="width: 25%;">Producto</th>
                                <th class="text-center">Tarifa / Precio</th>
                                <th class="text-center">Cant. Original</th>
                                <th class="text-center text-success">Cantidad total</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="detallesEdicion"></tbody>
                    </table>
                </div>

                <div class="row mt-4 justify-content-end">
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-light border">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span class="fw-bold" id="txt_subtotal">$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between text-primary">
                                <span class="h5 fw-bold">Total Final:</span>
                                <span class="h5 fw-bold" id="txt_total">$0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="button" class="btn btn-primary btn-lg rounded-pill px-5 shadow" onclick="enviarEdicion()">
                        <i class="bi bi-save me-2"></i> Actualizar Venta e Inventario
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const VENTA_ID = <?= $id_venta ?>;
const URL_API = '/myvet/app/controllers/editarVentaController.php';
let catalogoProductos = [];

// Variables de control para lógica financiera
let totalOriginal = 0;
let huboEliminacion = false;

$(document).ready(() => { 
    if(VENTA_ID > 0) cargarDatosVenta(); 
    $(document).on('click', (e) => {
        if (!$(e.target).closest('.input-group').length) $('#resultadosBusqueda').hide();
    });
});

function cargarDatosVenta() {
    $.get(URL_API, { action: 'obtenerDetalle', id: VENTA_ID }, function(res) {
        if(res.status === 'error') return Swal.fire('Error', res.message, 'error');

        $('#folio_titulo').text(res.info.folio);
        $('#lbl_cliente').text(res.info.nombre_comercial);
        $('#lbl_fecha').text(res.info.fecha);
        $('#edit_cliente_id').val(res.info.id_cliente);
        $('#formEditarVenta').data('almacen-id', res.info.almacen_id);
        
        // Guardamos el total original para comparar al final
        totalOriginal = parseFloat(res.info.total) || 0;
        
        cargarCatalogo(res.info.almacen_id);
        
        let html = '';
        res.productos.forEach(p => {
            html += crearFilaProducto({
                id: p.id,
                prod_id: p.producto_id,
                nombre: p.producto,
                precio_actual: p.precio_unitario,
                tipo_actual: p.tipo_precio,
                factor: parseFloat(p.factor_conversion),
                u_mayor: p.u_menor, 
                u_menor: p.u_mayor, 
                entregado_prev: parseFloat(p.cantidad_entregada) || 0, 
                cantidad_ref: p.cantidad,   
                stock: parseFloat(p.stock_actual) || 0,
                precios: {
                    minorista: p.precio_minorista,
                    mayorista: p.precio_mayorista,
                    distribuidor: p.precio_distribuidor
                },
                nuevo: false
            });
        });
        $('#detallesEdicion').html(html);
        recalculateAll();
    }, 'json');
}

function crearFilaProducto(d) {
    let factor = d.factor || 1;
    let m_vta = Math.floor(d.cantidad_ref / factor);
    let s_vta = (d.cantidad_ref % factor).toFixed(2).replace(/\.00$/, '');
    
    return `
        <tr data-id="${d.id}" data-prod-id="${d.prod_id}" 
            data-factor="${factor}" data-entregado="${d.entregado_prev}" 
            data-stock="${d.stock}" 
            data-p-minorista="${d.precios.minorista}" 
            data-p-mayorista="${d.precios.mayorista}" 
            data-p-distribuidor="${d.precios.distribuidor}"
            class="${d.nuevo ? 'table-info' : ''}">
            <td>
                <div class="fw-bold text-dark">${d.nombre}</div>
                <small class="text-primary">1 ${d.u_mayor} = ${factor} ${d.u_menor}</small>
            </td>
            <td class="text-center">
                <select class="select-precio w-100 mb-1" onchange="cambiarTarifa(this)">
                    <option value="minorista" data-p="${d.precios.minorista}" ${d.tipo_actual=='minorista'?'selected':''}>Minorista</option>
                    <option value="mayorista" data-p="${d.precios.mayorista}" ${d.tipo_actual=='mayorista'?'selected':''}>Mayorista</option>
                    <option value="distribuidor" data-p="${d.precios.distribuidor}" ${d.tipo_actual=='distribuidor'?'selected':''}>Distribuidor</option>
                </select>
                <input type="number" class="form-control form-control-sm text-center input-precio-unitario" value="${d.precio_actual}" step="0.01" oninput="recalculateAll()">
            </td>
            <td class="text-center bg-light">
                <span class="badge bg-secondary">${d.nuevo ? 'NUEVO' : d.cantidad_ref + ' ' + d.u_menor}</span>
                <div class="small text-success">Entregado: ${d.entregado_prev}</div>
            </td>
            <td>
                <div class="d-flex flex-column gap-1">
                    <div class="input-group input-group-sm">
                        <input type="number" class="form-control text-center input-vta-mayor" value="${m_vta}" min="0" oninput="sincronizar(this, 'vta')">
                        <span class="input-group-text">${d.u_mayor}</span>
                    </div>
                    <div class="input-group input-group-sm">
                        <input type="number" class="form-control text-center input-vta-sueltas" value="${s_vta}" min="0" step="0.01" oninput="sincronizar(this, 'vta')">
                        <span class="input-group-text">${d.u_menor}</span>
                    </div>
                </div>
                <input type="hidden" class="input-cantidad-total" value="${d.cantidad_ref}">
            </td>
           
            <td class="text-end fw-bold subtotal-fila">$0.00</td>
            <td class="text-center">
                 <button type="button" class="btn btn-sm text-danger" onclick="eliminarFila(this)"><i class="bi bi-trash"></i></button>
            </td>
        </tr>`;
}

function cambiarTarifa(select) {
    let precio = $(select).find(':selected').data('p');
    $(select).closest('tr').find('.input-precio-unitario').val(precio);
    recalculateAll();
}

function sincronizar(el, tipo) {
    let tr = $(el).closest('tr');
    let factor = parseFloat(tr.data('factor')) || 1;
    let m = parseInt(tr.find(`.input-${tipo}-mayor`).val()) || 0;
    let s = parseFloat(tr.find(`.input-${tipo}-sueltas`).val()) || 0;

    if (s >= factor) {
        m += Math.floor(s / factor);
        s = s % factor;
        tr.find(`.input-${tipo}-mayor`).val(m);
        tr.find(`.input-${tipo}-sueltas`).val(s.toFixed(2).replace(/\.00$/, ''));
    }
    
    tr.find('.input-cantidad-total').val(((m * factor) + s).toFixed(2));
    corregirAlMinimo(el);
}

function corregirAlMinimo(el) {
    let tr = $(el).closest('tr');
    let stock = parseFloat(tr.data('stock')) || 0;
    let entregadoPrev = parseFloat(tr.data('entregado')) || 0;
    let vtaTotal = parseFloat(tr.find('.input-cantidad-total').val()) || 0;
    let entHoy = parseFloat(tr.find('.input-entrega-hoy-total').val()) || 0;

    if (vtaTotal < entregadoPrev) {
        vtaTotal = entregadoPrev;
    }

    if (entHoy > stock) entHoy = stock;
    if (entHoy > (vtaTotal - entregadoPrev)) entHoy = Math.max(0, vtaTotal - entregadoPrev);

    tr.find('.input-entrega-hoy-total').val(entHoy);
    recalculateAll();
}

function recalculateAll() {
    let granTotal = 0;
    $('#detallesEdicion tr').each(function() {
        let tr = $(this);
        let vtaTotal = parseFloat(tr.find('.input-cantidad-total').val()) || 0;
        let precio = parseFloat(tr.find('.input-precio-unitario').val()) || 0;
        let sub = precio * vtaTotal;
        tr.find('.subtotal-fila').text('$' + sub.toLocaleString('es-MX', {minimumFractionDigits: 2}));
        granTotal += sub;
    });
    $('#txt_subtotal, #txt_total').text('$' + granTotal.toLocaleString('es-MX', {minimumFractionDigits: 2}));
}

function cargarCatalogo(almacenId) {
    $.get(URL_API, { action: 'obtenerProductos', almacen_id: almacenId }, function(res) {
        if(res.status === 'success') catalogoProductos = res.data;
    });
}

$('#buscarProducto').on('input', function() {
    let busqueda = $(this).val().toLowerCase();
    let resultados = $('#resultadosBusqueda');
    if (busqueda.length < 2) return resultados.hide();
    
    let filtrados = catalogoProductos.filter(p => p.nombre.toLowerCase().includes(busqueda)).slice(0, 5);
    let html = '';
    filtrados.forEach(p => {
        html += `<a href="javascript:void(0)" class="list-group-item list-group-item-action" onclick='agregarFilaNueva(${JSON.stringify(p)})'>
            <strong>${p.nombre}</strong> <span class="badge bg-primary float-end">$${p.precio_minorista}</span>
        </a>`;
    });
    resultados.html(html).show();
});

function agregarFilaNueva(p) {
    $('#resultadosBusqueda').hide(); $('#buscarProducto').val('');
    let html = crearFilaProducto({
        id: 0, prod_id: p.id, nombre: p.nombre, precio_actual: p.precio_minorista, tipo_actual: 'minorista',
        factor: p.factor_conversion, u_mayor: p.unidad_reporte, u_menor: p.unidad_medida,
        entregado_prev: 0, cantidad_ref: 0, stock: p.stock,
        precios: { minorista: p.precio_minorista, mayorista: p.precio_mayorista, distribuidor: p.precio_distribuidor },
        nuevo: true
    });
    $('#detallesEdicion').append(html);
    recalculateAll();
}

let filasEliminadas = []; 

// --- Reemplaza tu función eliminarFila por esta ---
function eliminarFila(btn) {
    let tr = $(btn).closest('tr');
    let idDetalle = tr.data('id');

    // Si la fila ya existía en la base de datos (id > 0), guardamos el ID
    if (idDetalle > 0) {
        filasEliminadas.push(idDetalle);
    }

    huboEliminacion = true;
    tr.remove(); 
    recalculateAll();
}

function enviarEdicion() {
    const nuevoTotal = parseFloat($('#txt_total').text().replace(/[^0-9.-]+/g, ""));
    
    const data = {
        venta_id: VENTA_ID,
        id_cliente: $('#edit_cliente_id').val(),
        nuevo_total: nuevoTotal,
        almacen_id: $('#formEditarVenta').data('almacen-id'),
        productos: [],
        eliminados: filasEliminadas // <--- Agregamos los IDs a eliminar
    };
    
    $('#detallesEdicion tr').each(function() {
        data.productos.push({
            detalle_id: $(this).data('id'),
            producto_id: $(this).data('prod-id'),
            nueva_cantidad: $(this).find('.input-cantidad-total').val(),
            entrega_hoy:0,
            precio_unitario: $(this).find('.input-precio-unitario').val(),
            tipo_precio: $(this).find('.select-precio').val()
        });
    });

    if (nuevoTotal < totalOriginal || huboEliminacion) {
        Swal.fire({
            title: '¿Detectamos un saldo a favor?',
            text: `El total ha disminuido (Original: $${totalOriginal.toFixed(2)} -> Nuevo: $${nuevoTotal.toFixed(2)}). ¿Deseas aplicar la diferencia al saldo del cliente?`,
            icon: 'warning',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonColor: '#28a745',
            denyButtonColor: '#3085d6',
            confirmButtonText: '<i class="bi bi-piggy-bank"></i> Sí, Saldo a Favor',
            denyButtonText: '<i class="bi bi-x-circle"></i> No, solo editar',
            cancelButtonText: 'Regresar',
            customClass: { confirmButton: 'rounded-pill px-3', denyButton: 'rounded-pill px-3', cancelButton: 'rounded-pill' }
        }).then((result) => {
            if (result.isConfirmed) ejecutarPeticion('guardarEdicionVentaSaldoAFavor', data);
            else if (result.isDenied) ejecutarPeticion('guardarEdicion', data);
        });
    } else {
        Swal.fire({
            title: '¿Confirmar actualización?',
            text: "Se guardarán los cambios y se actualizarán existencias.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Actualizar Venta',
            cancelButtonText: 'Cancelar',
            customClass: { confirmButton: 'rounded-pill px-3', cancelButton: 'rounded-pill' }
        }).then((result) => {
            if (result.isConfirmed) ejecutarPeticion('guardarEdicionVentaSaldoAFavor', data);
        });
    }
}

function ejecutarPeticion(accion, data) {
    Swal.fire({ title: 'Procesando...', didOpen: () => { Swal.showLoading(); }, allowOutsideClick: false });
    $.ajax({
        url: `${URL_API}?action=${accion}`, 
        type: 'POST',
        data: JSON.stringify(data),
        contentType: 'application/json',
        success: (res) => {
            if (res.status === 'success') {
                let infoExtra = res.mensaje_financiero ? `<br><small class="text-body-secondary">${res.mensaje_financiero}</small>` : '';
                Swal.fire({ icon: 'success', title: '¡Éxito!', html: `La operación se completó con éxito.${infoExtra}`, timer: 2500, showConfirmButton: false })
                .then(() => location.reload());
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        },
        error: () => { Swal.fire('Error', 'Error de red o servidor no disponible', 'error'); }
    });
}
</script>

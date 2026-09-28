<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas | Sistema</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <style>
        body {
            background-color: #f8fafc;
            
            padding-bottom: 40px;
        }
.main-content{
    padding-top: 65px!important;

}
        .main-card {
            background: #ffffff;
            border-radius: 20px;
            
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .page-header-gradient {
            background: linear-gradient(135deg, #696969 0%, #b1b3b6 100%);
            color: #ffffff;
            padding: 1.5rem 2rem;
        }

        .card-filter-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
        }

        .form-label-custom {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: #64748b;
            text-transform: uppercase;
        }

        .btn-action-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
            color: #ffffff;
            transition: all 0.2s ease;
        }

        .btn-action-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
            color: #ffffff;
        }

        .table-custom-header {
            background-color: #062347 !important;
            color: #ffffff !important;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        .table-custom-header th {
            background-color: transparent !important;
            color: #ffffff !important;
            border-bottom: none !important;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        .total-badge-card {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #ffffff;
            border-radius: 16px;
            padding: 14px 28px;
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
        }

        .entregado-tooltip {
            position: relative;
            display: inline-flex;
            align-items: center;
            cursor: help;
        }

        .tooltip-custom {
            position: absolute;
            bottom: 130%;
            left: 50%;
            transform: translateX(-50%);
            min-width: 240px;
            max-width: 300px;
            padding: .6rem .8rem;
            background: #212529;
            color: #fff;
            border-radius: .6rem;
            font-size: .82rem;
            line-height: 1.3;
            text-align: center;
            opacity: 0;
            visibility: hidden;
            transition: .2s ease;
            z-index: 9999;
            box-shadow: 0 8px 25px rgba(0, 0, 0, .25);
        }

        .tooltip-custom::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 6px;
            border-style: solid;
            border-color: #9e9e9e transparent transparent transparent;
        }

        .entregado-tooltip:hover .tooltip-custom {
            opacity: 1;
            visibility: visible;
        }
        .fixed-top {
    
    z-index: 2050;
}
    </style>
</head>

<body>

    <?php renderizarLayout($paginaActual); ?>

    <div class=" main-content">
        <div class="main-card">
            
            <form id="formEditarSolicitud">
                <input type="hidden" id="editar_venta_id" name="cotizacion_id" value="">

                <!-- Encabezado de la Página -->
                <div class="page-header-gradient d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold mb-0 text-white"><i class="bi bi-cart-check me-2"></i> Módulo de Remisiones</h4>
                       
                    </div>
                </div>

                <div class="p-4">
                    <!-- Controles / Filtros de Edición -->
                    <div class="card-filter-box p-4 mb-4 shadow-sm">
                        <div class="row g-3 align-items-end">

                            <!-- 1. Almacén de Cargo -->
                            <div class="col-md-4 col-lg-3">
                                <label class="form-label form-label-custom mb-2">
                                    <i class="bi bi-box-seam me-1 text-indigo"></i> Almacén de Cargo
                                </label>
                                <select name="almacen_id_editar" id="almacen_id_editar"
                                    class="form-select border-slate-300 rounded-3" required>
                                    <option value="">Seleccionar ubicación...</option>
                                    <?php foreach($almacenes as $a): ?>
                                        <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- 2. Cliente -->
                            <div class="col-md-4 col-lg-3">
                                <label class="form-label form-label-custom mb-2">
                                    <i class="bi bi-person me-1 text-indigo"></i> Cliente
                                </label>
                                <div class="input-group">
                                    <select name="cliente_id_editar" id="cliente_id_editar"
                                        class="form-select select2-pagina border-slate-300" required>
                                        <option value="">Seleccionar cliente...</option>
                                        <?php foreach($clientes as $p): ?>
                                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre_comercial']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-outline-secondary px-3" type="button"
                                        onclick="abrirModalNuevoCliente()" title="Nuevo Cliente">
                                        <i class="bi bi-person-plus-fill"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- 3. Vendedor -->
                            <div class="col-md-4 col-lg-3">
                                <label class="form-label form-label-custom mb-2">
                                    <i class="bi bi-person-badge me-1 text-indigo"></i> Vendedor
                                </label>
                                <select name="select-vendedor1" id="select-vendedor1"
                                    class="form-select select2-pagina border-slate-300" required>
                                    <option value="">Seleccionar vendedor...</option>
                                </select>
                            </div>

                            <!-- 4. Añadir Producto -->
                            <div class="col-12 col-lg-3">
                                <label class="form-label form-label-custom mb-2">
                                    <i class="bi bi-search me-1 text-indigo"></i> Añadir Producto
                                </label>
                                <div class="input-group">
                                    <select id="buscadorProductosEditar"
                                        class="form-select select2-pagina border-slate-300">
                                        <option value="">Escribe SKU o nombre...</option>
                                        <?php foreach($productos as $pr): ?>
                                            <option value="<?= $pr['producto_id'] ?>"
                                                data-nombre="<?= htmlspecialchars($pr['nombre']) ?>"
                                                data-sku="<?= htmlspecialchars($pr['sku']) ?>"
                                                data-um="<?= htmlspecialchars($pr['unidad_medida']) ?>"
                                                data-ur="<?= htmlspecialchars($pr['unidad_reporte']) ?>"
                                                data-premin="<?= $pr['precio_minorista'] ?? 0 ?>"
                                                data-premat="<?= $pr['precio_mayorista'] ?? 0 ?>"
                                                data-predis="<?= $pr['precio_distribuidor'] ?? 0 ?>"
                                                data-factor="<?= $pr['factor_conversion'] ?? 1 ?>">
                                                [<?= htmlspecialchars($pr['sku']) ?>] <?= htmlspecialchars($pr['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="btn btn-action-primary d-flex align-items-center px-3"
                                        onclick="abrirModalProducto()" title="Agregar nuevo producto">
                                        <i class="bi bi-plus-lg me-1"></i>
                                        <span>Nuevo</span>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Tabla de Artículos -->
                    <div class="table-responsive border rounded-4 bg-white shadow-sm"
                        style="max-height: 420px; overflow-y: auto;">
                        <table class="table align-middle mb-0" id="tablaDetalleEditar">
                            <thead>
                                <tr class="table-custom-header text-uppercase">
                                    <th class="ps-4" style="width: 30%;">Producto</th>
                                    <th style="width: 12%;">Cantidad</th>
                                    <th style="width: 18%;">Presentación / Unidad</th>
                                    <th style="width: 18%;">Tipo de precio</th>
                                    <th style="width: 12%;">Precio Unit.</th>
                                    <th style="width: 15%;">TOTAL</th>
                                    <th style="width: 5%;" class="text-center pe-4">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                            </tbody>
                        </table>

                        <!-- Estado Vacío -->
                        <div id="emptyStateEditar" class="text-center py-5 text-body-secondary">
                            <div class="mb-2">
                                <i class="bi bi-cart-x text-slate-300 opacity-50" style="font-size: 3.5rem;"></i>
                            </div>
                            <p class="fw-semibold text-slate-600 mb-1">La lista está vacía</p>
                            <small class="text-slate-400">Utiliza el buscador superior para agregar productos a esta venta</small>
                        </div>
                    </div>

                    <!-- Resumen del Total y Botones de Acción -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="/myvet/app/controllers/ventasController2.php" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                             Reiniciar
                        </a>

                        <div class="d-flex align-items-center gap-4">
                            <div class="total-badge-card text-end">
                                <small class="d-block text-white-50 fw-semibold text-uppercase tracking-wider mb-1"
                                    style="font-size:0.7rem;">
                                    Costo Total de Compra
                                </small>
                                <div id="costoTotalCompraEditar" class="fw-bold" style="font-size:2.2rem; line-height:1;">
                                    $0.00
                                </div>
                                <input type="hidden" id="totalCotizacionEditar" name="totalCotizacionEditar" value="0">
                            </div>

                            <button type="submit"
                                class="btn btn-action-primary rounded-pill px-5 py-3 fw-bold shadow-sm d-flex align-items-center fs-5">
                                <i class="bi bi-check2-circle me-2"></i> Finalizar Venta
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <?php require_once __DIR__ . '/egresosComponets/agregarPoductoModal.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const URL_CONTROLADOR_EDITAR = '/myvet/app/controllers/cotizacionesController.php';
        let total_inicial = 0;
        let cliente_nombre = '';
        let dataEdicion = null;
        let recalculandoFilaEditar = false;

        // Inicializar Select2 en la página
        $('.select2-pagina').select2({
            theme: 'bootstrap-5'
        });

        $(document).ready(async function() {
            try {
                await recargarProductosEditar();
                await cargarVendedores3();
            } catch (error) {
                console.error("Error en inicialización:", error);
            }
        });

        // =====================================================
        // CARGAR VENDEDORES
        // =====================================================
        async function cargarVendedores3(vendedor_id = null) {
            const select = document.getElementById('select-vendedor1');
            if (!select) return;

            try {
                const url = '/myvet/app/controllers/accesoController.php?action=obtenerUsuarios';
                const respuesta = await fetch(url);

                if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

                const resultado = await respuesta.json();

                if (resultado.success && Array.isArray(resultado.data)) {
                    resultado.data.forEach(usuario => {
                        const opcion = document.createElement('option');
                        opcion.value = usuario.id;
                        opcion.textContent = `${usuario.nombre}`;
                        select.appendChild(opcion);
                    });

                    if (vendedor_id) {
                        $('#select-vendedor1').val(vendedor_id).trigger('change.select2');
                    }
                } else {
                    select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
                }
            } catch (error) {
                select.innerHTML = '<option value="">Error al cargar la lista</option>';
                console.error('Error al ejecutar cargarVendedores3:', error);
            }
        }

        // =====================================================
        // CALCULAR TOTAL EDITAR
        // =====================================================
        function calcularTotalSolEditar(input) {
            if (recalculandoFilaEditar) return;
            recalculandoFilaEditar = true;

            try {
                const fila = input ? input.closest('tr') : null;
                if (fila) {
                    const cantidad = parseFloat(fila.querySelector('.cantidad-editar')?.value) || 0;
                    const precioUnitarioOriginal = parseFloat(fila.querySelector('.precio-unitario-editar')?.value) || 0;

                    const precioTotal = cantidad * precioUnitarioOriginal;
                    fila.querySelector('.precio-total-editar').value = precioTotal.toFixed(2);
                }

                // SUMA GENERAL
                let totalCompraEditar = 0;
                document.querySelectorAll('#tablaDetalleEditar .precio-total-editar').forEach(el => {
                    totalCompraEditar += parseFloat(el.value) || 0;
                });

                document.getElementById('costoTotalCompraEditar').textContent = totalCompraEditar.toLocaleString('es-MX', {
                    style: 'currency',
                    currency: 'MXN'
                });
                document.getElementById('totalCotizacionEditar').value = totalCompraEditar.toFixed(2);

            } finally {
                recalculandoFilaEditar = false;
            }
        }

        // =====================================================
        // RECARGAR PRODUCTOS
        // =====================================================
        async function recargarProductosEditar() {
            try {
                const resp = await fetch(`/myvet/app/controllers/accesoController.php?action=obtenerProductos`);
                const res = await resp.json();

                if (!res.success) {
                    throw new Error(res.message);
                }

                const select = document.getElementById('buscadorProductosEditar');
                select.innerHTML = `<option value="">Escribe para buscar...</option>`;

                res.data.forEach(pr => {
                    const option = document.createElement('option');
                    option.value = pr.producto_id;
                    option.dataset.nombre = pr.nombre;
                    option.dataset.medidas = JSON.stringify(pr.medidas_adicionales || []);
                    option.dataset.sku = pr.sku;
                    option.dataset.um = pr.unidad_medida;
                    option.dataset.ur = pr.unidad_reporte;
                    option.dataset.premin = pr.precio_minorista || 0;
                    option.dataset.premat = pr.precio_mayorista || 0;
                    option.dataset.predis = pr.precio_distribuidor || 0;
                    option.dataset.factor = pr.factor_conversion || 1;

                    option.textContent = `[${pr.sku}] ${pr.nombre}`;
                    select.appendChild(option);
                });

            } catch (e) {
                console.error(e);
                Swal.fire('Error', 'No se pudo actualizar la lista de productos', 'error');
            }
            $('#buscadorProductosEditar').trigger('change.select2');
        }

        // =====================================================
        // EVENTO SELECT2: AGREGAR PRODUCTO
        // =====================================================
        $('#buscadorProductosEditar').on('select2:select', function(e) {
            const d = e.params.data.element.dataset;
            const id = $(this).val();

            if ($(`#filaEditar-${id}`).length) {
                Swal.fire('Aviso', 'El producto ya está en la lista', 'info');
                return;
            }

            $('#emptyStateEditar').addClass('d-none');
            const medidas = JSON.parse(d.medidas || '[]');

            let opcionesUnidad = ``;
            medidas.forEach(m => {
                opcionesUnidad += `
                <option value="${m.id}" data-equivalencia="${m.equivalencia}" data-medida-id="${m.id}">
                    ${m.nombre}
                </option>`;
            });

            const preMin = parseFloat(d.premin) || 0;
            const preMat = parseFloat(d.premat) || 0;
            const preDis = parseFloat(d.predis) || 0;
            const factor = parseFloat(d.factor) || 1;

            $('#tablaDetalleEditar tbody').append(`
            <tr id="filaEditar-${id}">
                <td class="ps-4">
                    <b>${d.nombre}</b><br>
                    <small class="text-body-secondary">${d.sku}</small>
                </td>

                <td>
                    <input 
                        type="number"
                        name="itemsEditar[${id}][cant]"
                        class="form-control cantidad-editar"
                        step="0.01"
                        value="1"
                        min="0.01"
                        required
                        oninput="actualizarEquivalencia(this);calcularTotalSolEditar(this)">
                    <input 
                        type="hidden"
                        name="itemsEditar[${id}][equivalencia]"
                        class="form-control equivalencia"
                        value="1">
                </td>

                <td>
                    <select 
                        name="itemsEditar[${id}][unidad]" 
                        class="form-select unidad-select-editar"
                        onchange="actualizarEquivalencia(this);calcularPrecioSugeridoEditar(this)">
                        <option value="0" data-equivalencia="1" data-medida-id="0">Seleccione</option>
                        ${opcionesUnidad}
                    </select>
                </td>
                
                <td>
                    <select 
                        name="itemsEditar[${id}][tipoPrecio]" 
                        class="form-select tipoPrecio-select-editar"
                        onchange="calcularPrecioSugeridoEditar(this)">
                        <option value="seleccionar" data-precio="0">Seleccione</option>
                        <option value="minorista" data-precio="${preMin}">Min $${(preMin * factor).toFixed(2)} x ${d.ur}</option>
                        <option value="mayorista" data-precio="${preMat}">May $${(preMat * factor).toFixed(2)} x ${d.ur}</option>
                        <option value="distribuidor" data-precio="${preDis}">Dis $${(preDis * factor).toFixed(2)} x ${d.ur}</option>
                    </select>
                </td>

                <td>
                    <input 
                        type="number"
                        lang="en-US"
                        name="itemsEditar[${id}][precioUnitario]"
                        class="form-control precio-unitario-editar"
                        step="0.01"
                        min="0"
                        placeholder="0.00"
                        required
                        oninput="calcularTotalSolEditar(this)">
                </td>

                <td style="min-width:140px;">
                    <input 
                        type="number"
                        lang="en-US"
                        name="itemsEditar[${id}][precio]"
                        class="form-control precio-total-editar fw-bold text-success bg-light"
                        step="0.01"
                        min="0"
                        placeholder="0.00"
                        oninput="calcularTotalSolEditar(this)"
                        style="font-size:1.1rem; height:40px;">
                </td>

                <td class="text-center">
                    <button type="button" class="btn btn-link text-danger p-0" onclick="quitarFilaEditar('${id}')">
                        <i class="bi bi-trash fs-5"></i>
                    </button>
                </td>
            </tr>
            `);

            $(this).val(null).trigger('change');
        });

        function actualizarEquivalencia(input) {
            const fila = $(input).closest('tr');
            const cantidad = parseFloat(fila.find('.cantidad-editar').val()) || 0;
            const equivalencia = parseFloat(fila.find('.unidad-select-editar option:selected').data('equivalencia')) || 1;
            let cantidadTotal = (1 / equivalencia).toFixed(2);
            fila.find('.equivalencia').val(cantidadTotal);
        }

        function calcularPrecioSugeridoEditar(select) {
            const fila = select.closest('tr');
            const inputPrecio = fila.querySelector('.precio-unitario-editar');
            const unidadSelect = fila.querySelector('.unidad-select-editar');
            const tipoSelect = fila.querySelector('.tipoPrecio-select-editar');
            const inputtotal = fila.querySelector('.precio-total-editar');
            
            const unidadOption = unidadSelect.options[unidadSelect.selectedIndex];
            const tipoOption = tipoSelect.options[tipoSelect.selectedIndex];

            const equivalencia = Number(unidadOption?.dataset.equivalencia || 1);
            const precioBase = Number(tipoOption?.dataset.precio || 0);

            const sugerido = equivalencia > 0 ? (precioBase / equivalencia) : precioBase;
            inputPrecio.value = sugerido.toFixed(2);
            
            const cantidad = parseFloat(fila.querySelector('.cantidad-editar')?.value) || 0;
            inputtotal.value = (sugerido * cantidad).toFixed(2);

            calcularTotalSolEditar(null);
        }

        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('precio-unitario-editar')) {
                e.target.dataset.editado = "1";
            }
        });

        // =====================================================
        // GUARDAR ACTUALIZACIÓN (SUBMIT FORM)
        // =====================================================
        // =====================================================
        $('#formEditarSolicitud').on('submit', async function(e) {
    e.preventDefault();

    if (!$('#tablaDetalleEditar tbody tr').length) {
        Swal.fire('Error', 'Agregue al menos un producto a la venta', 'warning');
        return;
    }

    const $btnFinalizar = $(this).find('button[type="submit"]');
    $btnFinalizar.prop('disabled', true);

    const totalVenta = parseFloat($('#totalCotizacionEditar').val()) || 0;
    const montoPagado = 0; // Dinero real recibido

    const payload = {
        accion: 'guardar_venta',
        id_cliente: $('#cliente_id_editar').val(),
        id_vendedor: $('#select-vendedor1').val(),
        monto_pagado: montoPagado,
        monto_usado_favor: 0,
        total_venta: totalVenta,
        almacen_id: $('#almacen_id_editar').val(),
        metodo_pago: 'efectivo',
        referencia: '',
        observaciones: '',
        usar_saldo_favor: 0,
        carrito: []
    };

    $('#tablaDetalleEditar tbody tr').each(function() {
        const fila = $(this);
        const id = fila.attr('id').replace('filaEditar-', '');

        const unidadSelect = fila.find('.unidad-select-editar option:selected');
        const tipoPrecioSelect = fila.find('.tipoPrecio-select-editar option:selected');
        let cantidadInicial = parseFloat(fila.find('.cantidad-editar').val()) || 0;
        let equivalencia = parseFloat(fila.find('.equivalencia').val()) || 1;
        
        
        let cantidadT = (cantidadInicial * equivalencia);
        let cantidadTotal = (cantidadT % 1 > 0) ? cantidadT.toFixed(2) : cantidadT;
        console.log(cantidadInicial);
        console.log('equivalencia',equivalencia);
      
        payload.carrito.push({
            producto_id: id,
            almacen_id: $('#almacen_id_editar').val(),
            cantidad: cantidadTotal,
            unidad_base: unidadSelect.val(),
           
            noEliminar: fila.find('.noEliminar').val(),
            idunidadMedida: unidadSelect.data('medida-id'),
            unidadEquivalencia: unidadSelect.data('equivalencia'),
            tipo_precio: tipoPrecioSelect.val(),
            precio_unitario: fila.find('.precio-unitario-editar').val(),
            subtotal: fila.find('.precio-total-editar').val()
        });
    });

    Swal.fire({
        title: 'Actualizando venta...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        const resp = await fetch(`/myvet/app/controllers/ventasController.php?action=guardar_venta`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const res = await resp.json();

        if (res.status === 'success') {
            // CORREGIDO: Se compara usando las variables definidas en este scope
            const tieneDeuda = payload.monto_pagado < payload.total_venta;
            const esEntregaTotal = (res.total_entregado ?? 0) >= (res.total_pedido ?? 0);
            const iconoFinal = esEntregaTotal ? 'success' : 'warning';

            let htmlExtra = `<p class="mb-2">Folio: <span class="badge bg-light text-dark border">${res.folio || 'N/A'}</span></p>`;

            if (tieneDeuda) {
                htmlExtra += `
                <div class="alert alert-danger py-1 px-2  mb-2" style="font-size:0.75rem; border-radius:10px;">
                    <i class="bi bi-exclamation-circle-fill me-1"></i> Saldo pendiente registrado en cuenta
                </div>`;
            }

            Swal.fire({
                title: esEntregaTotal ? '¡Venta Exitosa!' : 'Entrega Parcial Registrada',
                html: `
                    <div class="alert bg-body-tertiary text-body  small text-start py-2 mb-3" style="background:var(--bs-tertiary-bg);; border-radius:12px;">
                        ${res.message || 'Operación realizada correctamente.'}
                    </div>
                    ${htmlExtra}
                    <p class="text-body-secondary small mb-0">¿Deseas imprimir el comprobante?</p>
                `,
                icon: iconoFinal,
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: '<i class="bi bi-receipt"></i> Con Precios',
                denyButtonText: '<i class="bi bi-receipt"></i> Ticket Formal',
                cancelButtonText: 'Cerrar',
                confirmButtonColor: '#34c759',
                denyButtonColor: '#5856d6',
                customClass: {
                    popup: 'rounded-4  shadow-lg'
                }
            }).then((result) => {
                let url = '';
                if (result.isConfirmed) {
                    url = `/myvet/app/backend/ventas/ticket_venta.php?id=${res.id_venta}`;
                } else if (result.isDenied) {
                    url = `/myvet/app/backend/ventas/ticketFormal.php?id=${res.id_venta}`;
                }

                if (url !== '') window.open(url, '_blank');
                location.reload();
            });
        } else {
            Swal.fire({
                title: 'Error',
                text: res.message || 'Error desconocido',
                icon: 'error',
                customClass: { popup: 'rounded-4' }
            });
            $btnFinalizar.prop('disabled', false);
        }

    } catch (e) {
        console.error(e);
        Swal.fire('Error', 'Fallo de conexión con el servidor', 'error');
        $btnFinalizar.prop('disabled', false);
    }
});
        // ELIMINAR FILA
        // =====================================================
        function quitarFilaEditar(id) {
            $(`#filaEditar-${id}`).remove();

            if (!$('#tablaDetalleEditar tbody tr').length) {
                $('#emptyStateEditar').removeClass('d-none');
            }

            calcularTotalSolEditar(null);
        }

        // =====================================================
        // GESTIÓN DE SALDOS Y EGRESOS
        // =====================================================
        function mostrarModalSaldo() {
            let total = parseFloat($('#totalCotizacionEditar').val()) || 0;
            const diferencia = total_inicial - total;

            $('#txtTotalOriginal').text(total_inicial.toFixed(2));
            $('#txtNuevoTotal').text(total.toFixed(2));
            $('#txtDiferencia').text(diferencia.toFixed(2));

            const elemModal = document.getElementById('modalSaldoFavor');
            if (elemModal) {
                new bootstrap.Modal(elemModal).show();
            }
        }

        $('#btnSaldoFavor').click(async function () {
            const elemModal = document.getElementById('modalSaldoFavor');
            if (elemModal) {
                const modalInst = bootstrap.Modal.getInstance(elemModal);
                if (modalInst) modalInst.hide();
            }

            try {
                const id = $('#editar_venta_id').val();
                const cliente = $('#cliente_id_editar').val();
                const total = parseFloat($('#totalCotizacionEditar').val()) || 0;
                const diferencia = total_inicial - total;

                const fd = new FormData();
                fd.append('venta_id', id);
                fd.append('cliente_id', cliente);
                fd.append('diferencia', diferencia);

                const resp = await fetch('/myvet/app/controllers/editarVentaController.php?action=guardarComoABono', {
                    method: 'POST',
                    body: fd
                });

                const res = await resp.json();

                if (!res.success && res.status !== 'success') {
                    throw new Error(res.message);
                }

                await Swal.fire({
                    icon: 'success',
                    title: 'Saldo aplicado',
                    text: res.message || 'El saldo a favor fue registrado correctamente.'
                });

                window.location.reload();

            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: e.message
                });
            }
        });

        async function guardarComoGastoSalidadeDinero() {
            try {
                const respFolio = await fetch('/myvet/app/controllers/egresosController.php?action=getSiguienteFolioGasto');
                const folioRes = await respFolio.json();

                if (!folioRes.success) {
                    throw new Error("No fue posible obtener el folio.");
                }

                const folio = folioRes.folio;
                const id = $('#editar_venta_id').val();
                const total = parseFloat($('#totalCotizacionEditar').val()) || 0;
                const diferencia = total_inicial - total;
                const observaciones = `SALIDA DE DINERO POR EDICIÓN DE VENTA ${id}`;
                const fecha = new Date().toISOString().split('T')[0];

                const fd = new FormData();
                fd.append('folio', folio);
                fd.append('monto', diferencia);
                fd.append('fecha', fecha);
                fd.append('observaciones', observaciones);
                fd.append('venta_id', id);

                const resp = await fetch('/myvet/app/controllers/egresosController.php?action=guardarSalidaDinero', {
                    method: 'POST',
                    body: fd
                });

                const res = await resp.json();

                if (res.success || res.status === 'success') {
                    await Swal.fire('Éxito', 'Salida de dinero registrada correctamente.', 'success');
                    window.location.reload();
                } else {
                    throw new Error(res.message || 'Error al procesar el egreso');
                }

            } catch (e) {
                Swal.fire('Error', e.message, 'error');
            }
        }
    </script>
</body>

</html>
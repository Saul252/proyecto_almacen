<?php
$almacen_usuario = intval($_SESSION['almacen_id'] ?? 0); // 0 = admin
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Lotes</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <?php require_once __DIR__ . '/layout/icono.php' ?>

    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>

    <style>
    :root {
        --sidebar-width: 0px;
        --navbar-height: 65px;
        --apple-bg: #f5f5f7;
        --accent-blue: #007aff;
    }

    body {
     
        font-family: 'SF Pro Display', -apple-system, sans-serif;
       
    }

    .main-content {
        
        padding: 40px;
        padding-top: calc(var(--navbar-height) + 20px);
    }

    .card-premium {
        
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04);
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
    }

    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            padding: 20px;
            padding-top: 90px;
        }
    }
    </style>
</head>

<body>
    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>

    <main class="main-content">
        <h3 class="mb-3">📦 Historial Compras</h3>

        <div class="card p-3 mb-3">
            <div class="row g-3">

                <div class="col-md-3">
                    <label class="form-label small fw-bold">Almacén</label>
                    <select id="filtroAlmacen" class="form-select">
                        <option value="0">Todos</option>
                        <?php foreach ($almacenes as $a): ?>
                        <option value="<?= $a['id'] ?>">
                            <?= htmlspecialchars($a['nombre']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>




                <div class="col-md-2">
                    <label class="form-label small fw-bold">Desde</label>
                    <input type="date" id="fecha_inicio" class="form-control" value="<?= date('Y-m-01') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Hasta</label>
                    <input type="date" id="fecha_fin" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>

                <div class="col-md-2 d-grid">
                    <label class="invisible">.</label>
                    <button class="btn btn-success" onclick="cargarHistorial()">
                        Consultar
                    </button>
                </div>
            </div>
        </div>


        <div class="card p-3">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Almacén</th>
                            <th>id</th>
                            <th>Folio</th>
                            <th>Proveedor</th>
                            <th>Fecha</th>
                            <th>Productos</th>



                            <th>Estado</th>
                            <th>Costo</th>

                        </tr>
                    </thead>
                    <tbody id="tablaHistorial">
                        <tr>
                            <td colspan="10" class="text-center text-body-secondary">Selecciona un producto</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>




    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    function cargarHistorial() {
        const producto = $('#filtroProducto').val();
        const almacen = $('#filtroAlmacen').val() ?? 0;
        // NUEVO: Captura de fechas
        const f_ini = $('#fecha_inicio').val();
        const f_fin = $('#fecha_fin').val();
        //cargarTraspasos(producto, almacen);





        //cargarTraspasos(producto, almacen);
        // cargarConsumoLotes(producto, almacen);

        $.ajax({
            url: '/myvet/app/controllers/comprasHistorialController.php',
            type: 'GET',
            data: {
                action: 'obtenerCompras',

                almacen_id: almacen,
                periodo: 'personalizado', // Enviamos el periodo para que el controller sepa usar f_inicio
                f_inicio: f_ini,
                f_fin: f_fin
            },
            dataType: 'json',
            success: function(res) {
                // $('#total_inicial').text(res.totales.total_cantidad_inicial || 0);
                // $('#total_actual').text(res.totales.total_cantidad_actual || 0);
                console.log(res.data);

                let html = '';
                if (!res.success || !res.data.length) {
                    $('#tablaHistorial').html(
                        '<tr><td colspan="10" class="text-center">Sin datos</td></tr>');
                    return;
                }

                // 1. Agrupamos usando Map para mantener el ORDEN EXACTO en el que vienen los datos
                const comprasMap = new Map();

                res.data.forEach(item => {
                    if (!comprasMap.has(item.id)) {
                        comprasMap.set(item.id, {
                            id: item.id,
                            folio: item.folio,
                            almacen: item.almacen,
                            fecha_compra: item.fecha_compra,
                            estado: item.estado,
                            estado_compra: item.estado_compra,
                            total: item.total,
                            proveedor:item.proveedor,

                            productosList: [] // Aquí acumulamos los productos de esta compra
                        });
                    }
                    comprasMap.get(item.id).productosList.push({
                        id: item.producto_id,
                        nombre: item.productos,
                        cantidad: item.cantidadProd,
                        faltante: item.faltante,
                        sobrante: item.sobrante,
                        factor: item.factor_conversion,
                        uMedida: item.unidad_medida,
                        uReporte: item.unidad_reporte,

                    });
                });

                // 2. Generamos el HTML manteniendo el orden original
                comprasMap.forEach(compra => {
                    let color = compra.estado === 'activo' ? 'success' : (compra.estado_compra ===
                        'agotado' ? 'danger' : 'secondary');

                    // En lugar del .join(', '), creamos una lista limpia de Bootstrap
                    let nombresProductos =
                        '<ul class="list-unstyled m-0 p-0" style="font-size: 11px; line-height: 1.3;">';
                    compra.productosList.forEach(p => {
                        let cantidad = p.cantidad / p.factor;
                        let totalC = cantidad >= 1 ? p.nombre + ' ' + cantidad + ' ' + p
                            .uReporte : p.nombre + ' ' + p.cantidad + ' ' + p.uMedida;

                        nombresProductos +=
                            `<li class="text-truncate" style="max-width: 250px;" title="${totalC}">• ${totalC}</li>`;
                    });
                    nombresProductos += '</ul>';
                    let columnaAccion = '';

                    // Si es un solo producto, botón directo. Si son más, Dropdown de Bootstrap
                   if (compra.productosList.length === 1) {
    let prod = compra.productosList[0];

    columnaAccion = `
        <button
            class="btn btn-sm btn-outline-primary"
            onclick='verMovimientos(
                ${prod.id},
                ${compra.id},
                ${JSON.stringify(compra.folio)},
                ${JSON.stringify(prod)}
            )'>
            Ver
        </button>
    `;
} else {
                        let opciones = '';
                        compra.productosList.forEach(prod => {
                            let cantidad = prod.cantidad / prod.factor;
                            let totalC = cantidad >= 1 ? prod.nombre + ' ' + cantidad +
                                ' ' + prod.uReporte : prod.nombre + ' ' + prod.cantidad +
                                ' ' + prod.uMedida;
                            opciones += `
               <li>
    <a class="dropdown-item d-flex justify-content-between align-items-center gap-2"
       href="#"
       onclick='event.preventDefault(); verMovimientos(
           ${prod.id},
           ${compra.id},
           ${JSON.stringify(compra.folio)},
           ${JSON.stringify(prod)}
       );'>
        <span class="text-truncate" style="max-width: 150px;">${totalC}</span>
        <small class="text-primary fw-bold">Ver</small>
    </a>
</li>
`;
                        });

                        columnaAccion = `
            <div class="dropdown">
                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Opciones (${compra.productosList.length})
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="font-size: 11px;">
                    ${opciones}
                </ul>
            </div>
        `;
                    }


                   const color2 =
    compra.estado === 'confirmada' ? 'success' :
    compra.estado === 'pendiente'  ? 'primary' :
    compra.estado === 'cancelada'  ? 'danger' :
    'secondary';
    (compra.faltante>0&& compra.estado!='cancelada')?compra.estado='pendiente':'';

html += `
    <tr>
        <td>${compra.almacen}</td>
        <td class="fw-bold">#${compra.id}</td>
        <td class="text-body-secondary">${compra.folio}</td>
        <td class="text-body-secondary">${compra.proveedor}</td>
        <td>${compra.fecha_compra}</td>
        <td style="max-width: 220px;" class="text-truncate">${nombresProductos}</td>
        <td>
        
            <span class="badge bg-${color2}">
                ${compra.estado}
            </span>
        </td>
        <td class="text-end fw-bold">$${parseFloat(compra.total || 0).toFixed(2)}</td>
        <td class="text-center">${compra.estado != 'Cancelada' ? columnaAccion : ''}</td>
    </tr>`;;
                });
                $('#tablaHistorial').html(html);
            }
        });
    }



    function verMovimientos(producto_id, compra_id, compra_folio,prod) {

        console.log("Compra enviada:", compra_folio);

        $.ajax({
            url: '/myvet/app/controllers/comprasHistorialController.php',
            type: 'GET',
            data: {
                action: 'obtenerVentasCompra',
                producto_id: producto_id,
                compra_id: compra_id
            },
            dataType: 'json',

            success: function(res) {
                console.log("RESPUESTA:", res);

                if (!res.success) {
                    console.error(res.message);
                    alert(res.message);
                    return;
                }
                generarReporteMovimientos(res.reparto, res.data, compra_folio,prod);

                let html = '';

                if (res.data.length > 0) {
                    console.log(res.data);
                } else {
                    html = '<tr><td colspan="14" class="text-center">Sin movimientos</td></tr>';
                }

                $('#tablaMovimientosLote').html(html);
            },

            error: function(xhr, status, error) {

                console.log(xhr);
                console.log(xhr.responseText);

                Swal.fire({
                    icon: 'error',
                    title: 'Error AJAX',
                    text: typeof xhr.responseText === 'string' ?
                        xhr.responseText : JSON.stringify(xhr.responseText)
                });

            }
        });
    }

    function generarReporteMovimientos(reparto, data, compra_folio,prod) {
        let contenidoReporte = '';
        let repartosAlmacen='';
        let totalGeneralGanancia = 0;
        let totalGeneralRegistros = 0;
        let pintarTraspasos = '';
        let productoC='';
        let nuevoreparto = [];
        let movimientosTraspaso = [];
        let proveedor='';
        // 1. Recorremos el arreglo de distribución primero
        reparto.forEach(rep => {
            proveedor=rep.nombre_comercial;

        productoC=rep.producto;
            const movimientosAlmacen = data.filter(
                mov =>
                Number(mov.almacen_id) === Number(rep.almacen_id) &&
                String(mov.codigo_lote || '').trim() === String(rep.codigo_lote || '').trim()
            );
            const traspasos = data.filter(
                movi =>
                (Number(movi.almacen_id) === Number(rep.almacen_id) &&
                    String(movi.codigo_lote || '').trim() === String(rep.codigo_lote || '').trim()) &&
                movi.tipo_movimiento === 'TRASPASO'
            );

            console.log(traspasos.length);
            let filasAlmacen = '';
            let gananciaAlmacen = 0;

            // Variable para acumular los bloques de los nuevos repartos (traspasos)
            let bloquesTraspasosHtml = '';

            if (traspasos.length > 0) {

                console.log('Traspasos encontrados:', traspasos);

                traspasos.forEach(t => {
                    nuevoreparto.push({
                        almacen_id: t.almacen_destino,
                        almacen: t.cliente_proveedor,
                        lote_id: parseInt(t.referencia_extra),
                        codigo_lote: t.lote_destino_traspaso,
                        cantidad_inicial: t.cantidad_salida,
                        producto: t.producto
                    });

                    console.log('nuev reparto', nuevoreparto);

                    let filasTraspaso = '';

                    $.ajax({
                        url: '/myvet/app/controllers/lotesHistorialController.php',
                        type: 'GET',
                        async: false, // Forzamos sincronía para que el HTML espere los datos del servidor
                        data: {
                            action: 'obtenerVentasLote',
                            lote_id: parseInt(t.referencia_extra)
                        },
                        dataType: 'json',
                        success: function(res) {

                            if (res.success && res.data && res.data.length > 0) {

                                console.log('Información del traspaso:', res.data);
                                let cantidadReal = t.cantidad_salida / t.factor;
                                let cantidad = cantidadReal >= 1 ? cantidadReal + ' ' + t
                                    .unidad_reporte : t.cantidad_salida + ' ' + t
                                    .unidad_medida;
                                let costo = cantidadReal >= 1 ? (parseFloat((t
                                        .costo_unitario) * t.factor)).toFixed(1) + ' X ' + t
                                    .unidad_reporte : (parseFloat(t.costo_unitario))
                                    .toFixed(2) + ' X ' + rep.unidad_medida;

                                filasTraspaso = `
                            
                        
                   
                               
                                   <tr style="
    background:#fff7ed;
    border-left:4px solid #f59e0b;
    color:#92400e;
    font-weight:500;
">
    <td>
        <i class="bi bi-arrow-left-right"></i>
        Traspaso
    </td>

    <td colspan="2">
        ${t.cliente_proveedor}
        <small class="d-block text-body-secondary">
            Material traspasado
        </small>
    </td>

    <td>${t.producto}</td>

    <td>Traspaso</td>

    <td colspan="3">
        Lote destino:
        <b>${t.lote_destino_traspaso}</b>
    </td>

    <td class="num fw-bold">
        ${cantidad}
    </td>

    <td class="num text-body-secondary">
        —
    </td>

    <td class="num fw-bold">
        ${cantidad}
    </td>

    <td class="money">
        ${costo}
    </td>

    <td colspan="2" class="text-center text-body-secondary">
        Sin venta
    </td>

                                
                            </tr>
                        `;

                                res.data.forEach(tj => {
                                    console.log('tj',tj);

                                    movimientosTraspaso.push({
                                        tipo_movimiento: tj.tipo_movimiento,
                                        almacen_id: tj.almacen_id,
                                        producto_id: tj.producto_id,
                                        nombre: tj.nombre,
                                        producto: tj.producto,
                                        documento: tj.documento,
                                        cliente_proveedor: tj
                                            .cliente_proveedor,
                                        codigo_lote: tj.codigo_lote,
                                        fecha_lote: tj.fecha_lote,
                                        fecha_movimiento: tj
                                            .fecha_movimiento,
                                        cantidad_inicial: tj
                                            .cantidad_inicial,
                                        cantidad_actual: tj.cantidad_actual,
                                        cantidad_salida: tj.cantidad_salida,
                                        saldo_final: tj.saldo_final,
                                        costo_unitario: tj.costo_unitario,
                                        precio_venta: tj.precio_venta,
                                        ganancia: tj.ganancia,
                                        referencia_extra: tj
                                            .referencia_extra
                                    });

                                    const gananciaTJ = parseFloat((tj.precio_venta *
                                        tj.cantidad_salida) - (tj
                                        .costo_unitario * tj.cantidad_salida
                                        ) || 0);
                                    let cantidadReal = 
                                    tj.cantidad_inicial / tj.factor;
                                       
                                    let cantidad = cantidadReal >= 1 ?
                                        cantidadReal + ' ' + tj.unidad_reporte :
                                         tj.cantidad_salida + ' ' + tj.unidad_medida;
                                        
                                    let costo = cantidadReal >= 1 ? (parseFloat((tj
                                                .costo_unitario) * tj
                                            .factor)).toFixed(1) +
                                        ' X ' + tj.unidad_reporte : (parseFloat(tj
                                            .costo_unitario)).toFixed(2) + ' X ' +
                                        rep.unidad_medida;




                                        let saldo=tj.saldo_final/tj.factor;
                                        let saldoReal=saldo >= 1 ?
                                        saldo + ' ' + tj.unidad_reporte :
                                         tj.saldo_final + ' ' + tj.unidad_medida;
                                         let salida=tj.cantidad_salida/tj.factor;
                                        let salidaReal=saldo >= 1 ?
                                        saldo + ' ' + tj.unidad_reporte :
                                         tj.cantidad_salida + ' ' + tj.unidad_medida;

                                    filasTraspaso += `
                                <tr>
                                <td>Traspaso </td>
                                <td>${tj.documento}</td>
                                 <td>${tj.fecha_movimiento}</td>
                                  <td>${tj.codigo_lote}</td>
                                <td> ${t.cliente_proveedor}(material traspasado)</td>
                                    <td>${tj.tipo_movimiento}</td>
                                    <td>${tj.producto}</td>
                                    
                                    <td>${tj.cliente_proveedor}</td>
                                   
                                   
                                    <td class="num">${cantidad}</td>
                                    <td class="num">${salidaReal}</td>
                                    <td class="num">${saldoReal}</td>
                                    <td class="money">${(cantidadReal>=1?(tj.costo_unitario*tj.factor).toFixed(1)+'0 X '+tj.unidad_reporte:tj.costo_unitario+' X '+tj.unidad_medida|| 0)}</td>
                                    <td class="money">$${(cantidadReal>=1?(tj.precio_venta*tj.factor).toFixed(1)+'0 X '+tj.unidad_reporte:tj.precio_venta+' X '+tj.unidad_medida|| 0)}</td>
                                    <td class="money gain">$${gananciaTJ.toFixed(2)}</td>
                                </tr>
                            `;
                                });
                            } else {

                                movimientosTraspaso.push({
                                    tipo_movimiento: 'SIN_MOVIMIENTOS',
                                    almacen_id: t?.almacen_destino ||
                                        0, // Corregido tj por t
                                    producto: '',
                                    documento: '',
                                    cliente_proveedor: '',
                                    codigo_lote: '',
                                    fecha_lote: '',
                                    fecha_movimiento: '',
                                    cantidad_inicial: 0,
                                    cantidad_actual: 0,
                                    cantidad_salida: 0,
                                    saldo_final: 0,
                                    costo_unitario: 0,
                                    precio_venta: 0,
                                    ganancia: 0,
                                    referencia_extra: '-'
                                });
                                console.log('datos', t);
                                let cantidadReal = t.cantidad_salida / t.factor;
                                let cantidad = cantidadReal >= 1 ? cantidadReal + ' ' + t
                                    .unidad_reporte : t.cantidad_salida + ' ' + t
                                    .unidad_medida;
                                let costo = cantidadReal >= 1 ? (parseFloat((t
                                        .costo_unitario) * t.factor)).toFixed(1) + ' X ' + t
                                    .unidad_reporte : (parseFloat(t.costo_unitario))
                                    .toFixed(2) + ' X ' + rep.unidad_medida;

                                filasTraspaso = `
                           
                               
                                   <tr style="
    background:#fff7ed;
    border-left:4px solid #f59e0b;
    color:#92400e;
    font-weight:500;
">
    <td>
        <i class="bi bi-arrow-left-right"></i>
        Traspaso
    </td>

    <td colspan="2">
        ${t.cliente_proveedor}
        <small class="d-block text-body-secondary">
            Material traspasado
        </small>
    </td>

    <td>${t.producto}</td>

    
    <td colspan="4">
    Recepcion de Traspaso 
        Lote creado:
        <b>${t.lote_destino_traspaso}</b>
    </td>

    <td class="num fw-bold">
        ${cantidad}
    </td>

    <td class="num text-body-secondary">
        —
    </td>

    <td class="num fw-bold">
        ${cantidad}
    </td>

    <td class="money">
        ${costo}
    </td>

    <td colspan="2" class="text-center text-body-secondary">
        Sin venta
    </td>

                                
                            </tr>
                        `;
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error AJAX:', error);
                            console.error(xhr.responseText);
                        }
                    });

                    // Construimos la sección de "nuevoreparto" con el mismo diseño que el almacén principal
                    bloquesTraspasosHtml += `
              
                            ${filasTraspaso}
                            
                           
                      
            `;
                });
            }

            if (movimientosAlmacen.length > 0) {
                let cantidadReal = rep.cantidad_inicial / rep.factor_conversion;
                let cantidad = cantidadReal >= 1 ? cantidadReal + ' ' + rep.unidad_reporte : rep
                    .cantidad_salida + ' ' + rep.unidad_medida;
                let costo = cantidadReal >= 1 ? (parseFloat((rep.costo_unitario) * rep.factor_conversion))
                    .toFixed(1) + ' X ' + rep.unidad_reporte : (parseFloat(rep.costo_unitario)).toFixed(2) +
                    ' X ' + rep.unidad_medida;

                repartosAlmacen += `
            <tr style="
    background:#f0fdf4;
    border-left:4px solid #22c55e;
">
    <td>
        📦 Compra ${compra_folio}
    </td>

    <td >
        <strong>${rep.almacen}</strong>
      
    </td>
    <td  class="text-center text-body-secondary">
        Entrada de mercancía
    </td>

    <td>
        ${rep.producto}
    </td>

    

    <td>
        <strong>${rep.codigo_lote}</strong>
    </td>

    <td>
        ${rep.fecha_ingreso}
    </td>

    <td class="num fw-bold text-success">
        ${cantidad}
    </td>

    

    <td class="money">
        ${costo}
    </td>

    <td  class="text-center text-body-secondary">
        Inventario inicial
    </td>
</tr>
        `;
                


                movimientosAlmacen.forEach(mov => {
                    // 1. Forzar conversión a números para evitar caídas de .toFixed() y NaN
                    const subtotal = parseFloat(mov.subtotal || 0);
                    const costoUnitario = parseFloat(mov.costo_unitario || 0);
                    const precioVenta = parseFloat(mov.precio_venta || 0);
                    const cantInicial = parseFloat(mov.cantidad_inicial || 0);
                    const cantSalidaBase = parseFloat(mov.cantidad_salida || 0);
                    const salFinalBase = parseFloat(mov.saldo_final || 0);
                    let ganancia = 0;
                    // Evita división entre cero: si no hay factor o es 0, por defecto usa 1
                    const factor = parseFloat(mov.factor || 1) || 1;

                    // 2. Operaciones matemáticas seguras
                    if (mov.tipo_movimiento != 'TRASPASO') {
                        ganancia = parseFloat((mov.precio_venta * mov.cantidad_salida) - (mov
                            .costo_unitario * mov.cantidad_salida) || 0);


                    }


                    ;
                    gananciaAlmacen += ganancia;
                    totalGeneralGanancia += ganancia;
                   

                    const cantidadTotal = cantInicial / factor;
                    const cantidadSalida = cantSalidaBase / factor;
                    const saldoFinal = salFinalBase / factor;

                    const uReporte = mov.unidad_reporte || '';
                    const uMedida = mov.unidad_medida || '';

                    // 3. Preparar los textos de las celdas de forma limpia
                    const txtInicial = cantidadTotal >= 1 ?
                        `${cantidadTotal.toLocaleString()} ${uReporte} (${cantInicial.toLocaleString()} ${uMedida})` :
                        `${cantInicial.toLocaleString()} ${uMedida}`;

                    const txtSalida = cantidadSalida >= 1 ?
                        `${cantidadSalida.toLocaleString()} ${uReporte}` :
                        `${cantSalidaBase.toLocaleString()} ${uMedida}`;

                    const txtSaldo = saldoFinal >= 1 ?
                        `${saldoFinal.toLocaleString()} ${uReporte}` :
                        `${salFinalBase.toLocaleString()} ${uMedida}`;

                    const txtCosto = cantidadSalida >= 1 ?
                        `$${(costoUnitario * factor).toFixed(2)} x ${uReporte}` :
                        `$${costoUnitario.toFixed(2)} x ${uMedida}`;

                    const txtVenta = cantidadSalida >= 1 ?
                        `$${(precioVenta * factor).toFixed(1)}0 x ${uReporte}` :
                        `$${precioVenta.toFixed(1)}0 x ${uMedida}`;
                    let cantidadReal = rep.cantidad_inicial / rep.factor_conversion;
                    let cantidad = cantidadReal >= 1 ? cantidadReal + ' ' + rep.unidad_reporte : rep
                        .cantidad_salida + ' ' + rep.unidad_medida;
                    let costo = cantidadReal >= 1 ? (parseFloat((rep.costo_unitario) * rep
                        .factor_conversion)).toFixed(1) + ' X ' + rep.unidad_reporte : (parseFloat(
                        rep.costo_unitario)).toFixed(2) + ' X ' + rep.unidad_medida;

                    // 4. Inyección en la tabla libre de lógica compleja
                    filasAlmacen += `
        <tr>
           <td>${mov.documento}</td>
             <td>${mov.fecha_movimiento}</td>
        <td>${mov.codigo_lote}</td>
          
        <td>Compra ${compra_folio}</td>
        <td>   ${rep.almacen} </td>
            <td>${mov.tipo_movimiento}</td>
            <td>${mov.producto}</td>
         
            <td>${mov.tipo_movimiento === 'TRASPASO'
                ? 'Traspaso a sucursal de ' + mov.cliente_proveedor
                : mov.cliente_proveedor}
            </td>
            
            <td class="num">${txtInicial}</td>
            <td class="num">${txtSalida}</td>
            <td class="num">${txtSaldo}</td>
            <td class="money">${costo}</td>
            <td>${mov.tipo_movimiento === 'TRASPASO'
                ? costo+' traspaso'
                : txtVenta}
            </td>
          
            <td class="money gain">$${ganancia.toFixed(2)}</td>
        </tr>
    `;
                });

            } else {
                let cantidadReal = rep.cantidad_inicial / rep.factor_conversion;
                let cantidad = cantidadReal >= 1 ? cantidadReal + ' ' + rep.unidad_reporte : rep
                    .cantidad_salida + ' ' + rep.unidad_medida;
                let costo = cantidadReal >= 1 ? (parseFloat((rep.costo_unitario) * rep.factor_conversion))
                    .toFixed(1) + ' X ' + rep.unidad_reporte : (parseFloat(rep.costo_unitario)).toFixed(2) +
                    ' X ' + rep.unidad_medida;

                repartosAlmacen += `
           <tr style="
    background:#f0fdf4;
    border-left:4px solid #22c55e;
">
    <td>
        📦 Compra ${compra_folio}
    </td>

    <td >
        <strong>${rep.almacen}</strong>
        <br>
        
    </td>
    <td>
            Entrada de mercancía
        </td>

    <td>
        ${rep.producto}
    </td>

    

    <td>
        <strong>${rep.codigo_lote}</strong>
    </td>

    <td>
        ${rep.fecha_ingreso}
    </td>

    <td class="num fw-bold text-success">
        ${cantidad}
    </td>

    

    <td class="money">
        ${costo}
    </td>

    <td  class="text-center text-body-secondary">
        Inventario inicial
    </td>
</tr>
        `;
            }
            let cantidadTotal = rep.cantidad_inicial / rep.factor_conversion;
            

            // Inyección en el reporte principal
            contenidoReporte += `
        
                    ${filasAlmacen}
              
        
        ${bloquesTraspasosHtml}
    `;
        });
        let cantidad = prod.cantidad / prod.factor;
                            let totalC = cantidad >= 1 ? prod.nombre + ' cantidad: ' + cantidad +
                                ' ' + prod.uReporte +' ('+ ' ' + prod.cantidad +
                                ' ' + prod.uMedida+')'
                                : prod.nombre + ' ' + prod.cantidad +
                                ' (' + prod.uMedida+')';
                                console.log(prod);
        // 4. Abrimos la ventana y renderizamos todo el reporte
        let cantidadFaltante = prod.faltante / prod.factor;
                            let totalFaltante = cantidadFaltante >= 1 ? prod.nombre + ' cantidad: ' + cantidadFaltante +
                                ' ' + prod.uReporte +' ('+ ' ' + prod.faltante +
                                ' ' + prod.uMedida+')'
                                : prod.nombre + ' ' + prod.faltante +
                                ' (' + prod.uMedida+')';
                                console.log(prod);
        // 4. Abrimos la ventana y renderizamos todo el reporte
        const ventana = window.open('', '_blank', 'width=1700,height=950');

        ventana.document.write(`
        <html>
        <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Movimientos</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

 <style>
        /* --- Estructura Base Compacta --- */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            background: #fff; 
            color: #111; 
            padding: 15px; 
            font-size: 11px; 
        }
        .container { width: 100%; }

        /* --- Encabezado Plano y Simple --- */
        .header { 
            border-bottom: 2px solid #111; 
            padding-bottom: 8px; 
            margin-bottom: 15px; 
        }
        .header h1 { font-size: 18px; font-weight: bold; color: #111; }
        .header p { font-size: 11px; color: #555; margin-top: 2px; }

        /* --- Barra de Resumen --- */
        .summary { 
            display: flex; 
            gap: 10px; 
            margin-bottom: 15px; 
        }
        .card { 
            flex: 1; 
            border: 1px solid #ccc; 
            padding: 8px 12px; 
            background: #f9f9f9; 
        }
        .label { font-size: 9px; color: #000000; text-transform: uppercase; font-weight: bold; }
        .value { font-size: 15px; font-weight: bold; margin-top: 2px; }

        /* --- Tabla Unificada con Distribución Fija --- */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px;
            table-layout: fixed; /* Fuerza al navegador a respetar los anchos asignados por CSS */
        }
        thead { 
            background: #a3a5aa; 
        }
        th { 
            padding: 6px 4px; 
            font-size: 10px; 
            color: #020202; 
            text-transform: uppercase; 
            font-weight: bold;
            border: 1px solid #aaa;
            text-align: left; 
            white-space: normal; /* Permite saltos de línea si el encabezado es largo */
            word-wrap: break-word;
        }
        td { 
            padding: 5px 6px; 
            border: 1px solid #ccc; 
            font-size: 10.5px; 
            color: #000;
            white-space: normal; /* Cambiado de nowrap a normal para que brinque de línea */
            word-wrap: break-word;  /* Fuerza el salto de línea si el texto es una sola palabra muy larga */
            vertical-align: top;  /* Alinea el texto arriba para que se vea ordenado si una fila se vuelve más alta */
        }

        /* --- CONTROL ESTRICTO DE ANCHOS POR COLUMNA (14 Columnas) --- */
        th:nth-child(1), td:nth-child(1)   { width: 6%; }  /* Origen */
        th:nth-child(2), td:nth-child(2)   { width: 10%; } /* Almacén Ref */
        th:nth-child(3), td:nth-child(3)   { width: 7%; }  /* Tipo Mov */
        th:nth-child(4), td:nth-child(4)   { width: 12%; } /* Producto */
        th:nth-child(5), td:nth-child(5)   { width: 7%; }  /* Documento */
        th:nth-child(6), td:nth-child(6)   { width: 11%; } /* Cliente / Detalle */
        th:nth-child(7), td:nth-child(7)   { width: 7%; }  /* Lote */
        th:nth-child(8), td:nth-child(8)   { width: 7%; }  /* Fecha */
        th:nth-child(9), td:nth-child(9)   { width: 6%; }  /* Inicial */
        th:nth-child(10), td:nth-child(10) { width: 5%; }  /* Salida */
        th:nth-child(11), td:nth-child(11) { width: 5%; }  /* Saldo */
        th:nth-child(12), td:nth-child(12) { width: 5%; }  /* Costo */
        th:nth-child(13), td:nth-child(13) { width: 6%; }  /* Venta */
        th:nth-child(14), td:nth-child(14) { width: 6%; }  /* Ganancia */

        tbody tr:nth-child(even) { 
            background: #f9f9f9; 
        }
        tbody tr:hover { 
            background: #edf2f7; 
        }

        /* Formatos numéricos rápidos */
        .num, .money { 
            text-align: right; 
            font-variant-numeric: tabular-nums; 
        }
        .gain { 
            color: #007a48; 
            font-weight: bold;
        }

        /* --- Footer General --- */
        .footer-general { 
            background: #111; 
            color: #fff; 
            padding: 10px 15px; 
            text-align: right; 
            font-size: 14px; 
            font-weight: bold; 
            margin-top: 15px; 
        }

        /* --- Optimización de Impresión --- */
        @media print { 
            body { padding: 0; }
            th { background: #eee !important; color: #000 !important; border: 1px solid #000 !important; }
            td { border: 1px solid #000 !important; }
            .footer-general { background: #fff !important; color: #000 !important; border: 2px solid #000; }
        }
    </style>
</head>
<body>

   <div class="container">

    <div class="card shadow-sm  mb-4">
        <div class="card-body">

            <div class="text-center mb-4">
                <h2 class="fw-bold text-primary mb-1">
                    <i class="bi bi-box-seam"></i>
                    Compra ${compra_folio}
                </h2>

                
            </div>

            <div class="row g-3">

                <div class="col-md-6">
                    <div class="border rounded p-3 bg-light h-100">
                        <div class="small text-body-secondary text-uppercase fw-bold">
                          <h3 class="fw-bold text-success mb-1">   Proveedor:<//h3>
                        </div>
                        <div class="fs-5 fw-semibold">
                          <h4 class="fw-bold text-success mb-1">   ${proveedor}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="border rounded p-3 bg-light text-center h-100">
                        <div class="small text-body-secondary text-uppercase fw-bold">
                            <h3 class="fw-bold text-primary mb-1"> Producto:</h3>
                        </div>
                        <div class="fs-4 fw-bold text-success">
                          <h4 class="fw-bold text-success mb-1" style="color: #007a48;">  ${totalC}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="border rounded p-3 bg-light text-center h-100">
                        <div class="small text-body-secondary text-uppercase fw-bold">
                         <h3 class="fw-bold text-primary mb-1">    Faltante:</h3>
                        </div>
                        <div class="fs-4 fw-bold text-danger">
                        <h4 class="fw-bold text-danger mb-1" style="color: #f00c0c;">
                            ${cantidadFaltante}
                            </h4>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>
        
            <div class="card">
                <div class="label">Ganancia Total General</div>
                <div class="value" style="color: #007a48;">$${parseFloat(totalGeneralGanancia || 0).toFixed(2)}</div>
            </div>
        </div>
      

        <div class="almacen-block">

            <h1 style="
               
                
               
                border-radius:10px;
                font-size:18px;
                margin-bottom:15px;
                box-shadow:0 3px 10px rgba(0,0,0,.05);
            ">
                Lotes generados por compra
            </h1>

            <table style="
                width:100%;
                border-collapse:collapse;
                background:white;
                border-radius:12px;
                overflow:hidden;
                box-shadow:0 5px 20px rgba(0,0,0,.08);
                margin-bottom:30px;
            ">
                <thead>
                    <tr style="
                        background:linear-gradient(90deg,#0164f7,#2684ff);
                        color:white;
                    ">
                        <th>Origen</th>
                        <th>Sucursal</th>
                        <th>Razón</th>
                        <th>Producto</th>
                        <th>Lote</th>
                        <th>Fecha</th>
                        <th>Inicial</th>
                        <th>Costo</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    ${repartosAlmacen}
                </tbody>
            </table>

            <h1 style="
                
                font-size:18px;
                margin-bottom:15px;
                box-shadow:0 3px 10px rgba(0,0,0,.05);
            ">
                Movimientos de material
            </h1>

            <table style="
                width:100%;
                border-collapse:collapse;
                background:white;
                border-radius:12px;
                overflow:hidden;
                box-shadow:0 5px 20px rgba(0,0,0,.08);
            ">
                <thead>
                    <tr style="
                        background:linear-gradient(90deg,#00a86b,#12c17b);
                        color:white;
                    ">
                    <th>Documento</th>
                    <th>Fecha</th>
                     <th>Lote</th>
                        <th>Origen</th>
                        <th>Sucursal</th>
                        <th>Tipo</th>
                        <th>Producto</th>
                        
                        <th>Cliente</th>
                       
                        
                        <th>Inicial</th>
                        <th>Salida</th>
                        <th>Saldo</th>
                        <th>Costo</th>
                        <th>Venta</th>
                        <th>Ganancia</th>
                    </tr>
                </thead>
                <tbody>
                    ${contenidoReporte}
                </tbody>
            </table>

        </div>
       

        

        <div class="footer-general">
            TOTAL UTILIDAD GLOBAL: $${parseFloat(totalGeneralGanancia || 0).toFixed(2)}
        </div>

    </div>

</body>
</html>
    `);

        ventana.document.close();
    }









    function cargarTraspasos(lote_id) {
        const f_ini = $('#fecha_inicio').val();
        const f_fin = $('#fecha_fin').val();

        $.ajax({
            url: '/myvet/app/controllers/lotesHistorialController.php',
            type: 'GET',
            data: {
                action: 'obtenerTraspasos',
                lote_id: lote_id,

                f_inicio: f_ini,
                f_fin: f_fin
            },
            dataType: 'json',
            success: function(res) {
                let html = '';
                if (res.data && res.data.length > 0) {
                    res.data.forEach(t => {
                        html +=
                            `<tr><td>TRASPASO</td><td>${t.fecha}</td><td>${t.movimiento_id}</td><td>${t.nombreOrigen}</td><td>${t.codigo_lote_origen}</td><td>${t.nombreDestino}</td><td>${t.codigo_lote_destino}</td><td>${t.cantidad}</td></tr>`;
                    });
                } else {
                    html = '<tr><td colspan="9" class="text-center">Sin traspasos</td></tr>';
                }
                $('#tablaTraspasosLote').html(html);
            }
        });
    }

    // $('#filtroAlmacen').on('change', function() {
    //     verMovimientos(0);
    //     let almacen = $(this).val();
    //     $.ajax({
    //         url: '/myvet/app/controllers/lotesHistorialController.php',
    //         type: 'GET',
    //         data: {
    //             action: 'productos',
    //             almacen_id: almacen
    //         },
    //         dataType: 'json',
    //         success: function(res) {
    //             let html = '<option value="">Selecciona producto</option>';
    //             if (res.success) {
    //                 res.data.forEach(p => {
    //                     html += `<option value="${p.id}">${p.nombre}</option>`;
    //                 });
    //             }
    //             $('#filtroProducto').html(html);
    //         }
    //     });
    // });
    </script>
</body>

</html>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotizaciones| cfsistem</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    <link href="/myvet/css/solicitudesCompra.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>   
</head>

<body>
    <?php renderizarLayout($paginaActual); ?>

    <main class="main-content">

        <div class="glass-card p-4 mb-4">

            <div class="row align-items-center ">
                <div class="col-md-8">
                    <h1 class="h3 fw-bold mb-1 card-title-text">Cotizaciones</h1>
                    <p class="text-body-secondary small card-title-text">Gestión de cotizaciones de materiales</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <button class="btn btn-add" onclick="nuevaCotizacion()">
                        <i class="bi bi-plus-lg me-2"></i> Crear Cotizacion
                    </button>
                <button class="btn btn-outline-secondary" onclick="descargarProductos()" title="Descargar Excel de Productos">
    <i class="bi bi-download"></i>
</button>
                </div>
            </div>

          <div class="glass-card p-4 mb-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1 card-title-text">
                <i class="bi bi-funnel-fill text-primary me-2"></i>
                Filtros de búsqueda
            </h5>
            <small class="text-body-secondary card-title-text">
                Filtra las cotizaciones por fecha, almacén, estado o cliente.
            </small>
        </div>
    </div>

    <div class="row g-3 align-items-end">

        <!-- Fecha Inicio -->
        <div class="col-lg-2 col-md-6">
            <label class="form-label small fw-bold text-body-secondary text-uppercase">
                Inicio
            </label>

            <input
                type="date"
                id="fechaInicio"
                value="<?= date('Y-m-01') ?>"
                class="form-control   shadow-sm"
                style="border-radius:12px;">
        </div>

        <!-- Fecha Fin -->
        <div class="col-lg-2 col-md-6">
            <label class="form-label small fw-bold text-body-secondary text-uppercase">
                Fin
            </label>

            <input
                type="date"
                id="fechaFin"
                value="<?= date('Y-m-d') ?>"
                class="form-control   shadow-sm"
                style="border-radius:12px;">
        </div>

        <!-- Almacén -->
        <div class="col-lg-3 col-md-6">
            <label class="form-label small fw-bold text-body-secondary text-uppercase">
                Almacén
            </label>

            <select id="filtroAlmacen" class="form-select   shadow-sm"
                style="border-radius:12px;">

                <?php if (isset($es_admin) && $es_admin): ?>

                <option value="">Todos los almacenes</option>
<?php endif ;?>
                <?php foreach ($almacenes as $alm): ?>

                <option value="<?= htmlspecialchars($alm['id']) ?>">
                    <?= htmlspecialchars($alm['nombre']) ?>
                </option>

                <?php endforeach; ?>

            </select>
        </div>

        <!-- Estado -->
        <div class="col-lg-2 col-md-6">
            <label class="form-label small fw-bold text-body-secondary text-uppercase">
                Estado
            </label>

            <select id="filtroEstado" class="form-select   shadow-sm"
                style="border-radius:12px;">

                <option value="">Todos</option>
                <option value="PENDIENTE">Pendiente</option>
                <option value="COMPLETADO">Completado</option>
                <option value="CANCELADO">Cancelado</option>

            </select>
        </div>
               <?php if ($puede == true): ?>
<div class="col-md-2">
    <label for="select-usuarios" class="form-label fw-bold small text-body-secondary text-uppercase">Vendedor</label>
    <select class="form-select rounded-pill" id="select-usuarios" name="usuario_id" onchange="getVentas()">
     <option value="" > Seleccione vendedor</option>
    </select>
</div>
<?php endif; ?>

        <!-- Buscador -->
        <div class="col-lg-3 col-md-12">
            <label class="form-label small fw-bold text-body-secondary text-uppercase">
                Buscar
            </label> 

            <div class="input-group shadow-sm">

                <span class="input-group-text  ">
                    <i class="bi bi-search text-secondary"></i>
                </span>

                <input
                    type="text"
                    id="buscadorGeneral"
                    class="form-control  "
                    placeholder="Folio o Cliente">

            </div>
        </div>

    </div>

</div>

        <div class="glass-card p-4">
            <div class="table-responsive">
                <table id="tablaSolicitudes" class="table align-middle w-100">
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Almacén</th>
                            <th>Vendedor</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaCotizacionesListar">
                        
                    </tbody>
                </table>
            </div>
        </div>
    </main>


 
   
    <div class="modal fade" id="modalPago" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  shadow-lg rounded-4">

            <div class="modal-header text-white" style="background: linear-gradient(135deg, #1f2a37, #334155);">
                <h6 class="modal-title fw-semibold">
                    <i class="bi bi-wallet2 me-2"></i> Registrar pago
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <input type="hidden" id="idC" name="idC">
            
            <div class="modal-body p-4" >
                <div class="table-responsive mb-3">
                    <table class="table align-middle">
                        <thead >
                            <tr>
                                
                             
                                
                            </tr>
                        </thead>
                        <tbody id="print-productos"></tbody>
                    </table>
                </div>

                <div class="text-center mb-4">
                    <small class="text-body-secondary d-block">Total a pagar</small>
                    <h3 id="pagoTotal" class="fw-bold card-title-text m-0">$0.00</h3>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-body-secondary">Monto recibido</label>
                    <input type="number" id="montoPago"
                        class="form-control form-control-lg   rounded-3" placeholder="0.00">
                </div>

                <div class="mb-3">
                    <label class="form-label small text-body-secondary">Método de pago</label>
                    <select id="metodoPago" class="form-select form-select-lg   rounded-3">
                        <option value="">Seleccione</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="deposito">Depósito</option>
                        <option data-metodo="credito" value="">Compra a credito</option>
                    </select>
                </div>

                <div class="mb-3 d-none" id="refBox">
                    <label class="form-label small text-body-secondary">Referencia</label>
                    <input type="text" id="referenciaPago"
                        class="form-control form-control-lg   rounded-3"
                        placeholder="Número de referencia">
                </div>
                 
            </div>

            <div class="modal-footer  px-4 pb-4 pt-0" id="boton">
                </div>

        </div>
    </div>
</div> 
    <style>
    /* =========================
   MODAL BASE
========================= */
    #modalImprimirSolicitud .modal-content {
        border-radius: 10px;
        
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12);
        overflow: hidden;
        background: #fff;
    }

    #modalImprimirSolicitud .modal-header {
        background: #ffffff02;
        border-bottom: 1px solid #e5e7eb;
        color: #111827;
        padding: 1.2rem 1.5rem;
    }

    #modalImprimirSolicitud .modal-footer {
        background: #ffffff;
        border-top: 1px solid #e5e7eb;
        padding: 1rem 1.5rem;
    }

    /* =========================
   ÁREA DE IMPRESIÓN
========================= */
    #areaImpresion {
        background: #ffffff02;
        padding: 2rem;
        background: #ffffff;
        color: #111827;
        font-family: "Segoe UI", system-ui, sans-serif;
    }

    /* =========================
   ENCABEZADOS
========================= */
    #areaImpresion h2 {
        font-weight: 700;
        font-size: 1.6rem;
        color: #1f2937;
        margin-bottom: 0.3rem;
    }

    #areaImpresion h5 {
        font-weight: 500;
        color: #4b5563;
    }

    /* =========================
   BLOQUES INFO
========================= */
    .card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: none;
        background: #ffffff;
    }

    .card:hover {
        transform: none;
        box-shadow: none;
    }

    /* =========================
   TABLA ESTILO DOCUMENTO
========================= */
    .table {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
    }

    .table thead th {
        background: #f3f4f6;
        color: #111827;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e5e7eb;
        padding: 12px;
    }

    .table tbody td {
        border-color: #0000006b;
        padding: 10px;
        font-size: 0.95rem;
    }

    .table tbody tr:nth-child(even) {
        background: #03030392;
    }

    /* =========================
   DIVISOR LIMPIO
========================= */
    .divider {
        height: 1px;
        background: #e5e7eb;
        margin: 1.5rem 0;
    }

    /* =========================
   FIRMAS
========================= */
    .signature-line {
        width: 180px;
        height: 1px;
        background: #111827;
        margin-bottom: 6px;
    }

    .signature-label {
        font-size: 0.75rem;
        color: #6b7280;
        text-transform: uppercase;
    }

    /* =========================
   BOTONES / ACCIONES
========================= */
    .btn-primary {
        background: #2563eb;
        
    }

    .btn-primary:hover {
        background: #1d4ed8;
    }

    /* =========================
   PRINT MODE
========================= */
    @media print {

        body * {
            visibility: hidden;
        }

        #modalImprimirSolicitud,
        #modalImprimirSolicitud * {
            visibility: visible;
        }

        #modalImprimirSolicitud {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .modal-header,
        .modal-footer {
            display: none !important;
        }

        .modal-content {
            box-shadow: none !important;
            border: none !important;
        }

        #areaImpresion {
            padding: 1rem;
        }

        .table thead th {
            background: #f0f0f0 !important;
            color: #000 !important;
        }

        .table tbody tr:nth-child(even) {
            background: #fff !important;
        }
    }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>




 <?php require_once __DIR__ . '/cotizacionesModales/editarCotizacion.php'; ?>
    <?php require_once __DIR__ . '/cotizacionesModales/ModalCotizacion.php'; ?>
    <?php require_once __DIR__ . '/cotizacionesModales/nuevoClienteModal.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/agregarPoductoModal.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/modalProveedoresCompra.php'; ?>
     <?php require_once __DIR__ . '/cotizacionesModales/imprimirCotizacion.php'; ?>
    <script>
    let totalGlobalPago = 0;
    let datost=0;
            
$(document).ready(function () {
    cargarCotizaciones();
    cargarCotizaciones();
});

 cargarUsuariosSelect();
    async function cargarUsuariosSelect() {
    const select = document.getElementById('select-usuarios');
    if (!select) return; // Seguridad por si el select no está en la vista actual

    try {
        // 1. Realizar la petición a tu controlador de Cf System
        const url = '/myvet/app/controllers/accesoController.php?action=obtenerUsuarios';
        const respuesta = await fetch(url);
        
        if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');
        
        const resultado = await respuesta.json();

        // 2. Verificar que la respuesta sea exitosa y contenga los datos
        if (resultado.success && Array.isArray(resultado.data)) {
            
            // Limpiamos el select y dejamos una opción inicial neutra
           // select.innerHTML = '<option value="" selected disabled> Seleccione vendedor</option>';

            // 3. Recorrer los usuarios y crear las opciones
            resultado.data.forEach(usuario => {
                const opcion = document.createElement('option');
                opcion.value = usuario.id; // El ID que se enviará en el formulario
                
                // Formateamos el texto: "Nombre (Almacén - Rol)" para que sea súper descriptivo
                const almacen = usuario.almacen_nombre || 'Sin Almacén';
                opcion.textContent = `${usuario.nombre}`;
                
                // Agregamos la opción al select
                select.appendChild(opcion);
            });

        } else {
            select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
            console.error('El backend no devolvió success:true o la estructura cambió');
        }

    } catch (error) {
        select.innerHTML = '<option value="">Error al cargar la lista</option>';
        console.error('Error al ejecutar cargarUsuariosSelect:', error);
    }
}

 
    async function cargarUsuariosSelectPago() {
    const select = document.getElementById('select-usuarios2');
    if (!select) return; // Seguridad por si el select no está en la vista actual

    try {
        // 1. Realizar la petición a tu controlador de Cf System
        const url = '/myvet/app/controllers/accesoController.php?action=obtenerUsuarios';
        const respuesta = await fetch(url);
        
        if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');
        
        const resultado = await respuesta.json();

        // 2. Verificar que la respuesta sea exitosa y contenga los datos
        if (resultado.success && Array.isArray(resultado.data)) {
            
            // Limpiamos el select y dejamos una opción inicial neutra
           // select.innerHTML = '<option value="" selected disabled> Seleccione vendedor</option>';

            // 3. Recorrer los usuarios y crear las opciones
            resultado.data.forEach(usuario => {
                const opcion = document.createElement('option');
                opcion.value = usuario.id; // El ID que se enviará en el formulario
                
                // Formateamos el texto: "Nombre (Almacén - Rol)" para que sea súper descriptivo
                const almacen = usuario.almacen_nombre || 'Sin Almacén';
                opcion.textContent = `${usuario.nombre}`;
                
                // Agregamos la opción al select
                select.appendChild(opcion);
            });

        } else {
            select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
            console.error('El backend no devolvió success:true o la estructura cambió');
        }

    } catch (error) {
        select.innerHTML = '<option value="">Error al cargar la lista</option>';
        console.error('Error al ejecutar cargarUsuariosSelect:', error);
    }
}
async function descargarProductos() {
   
        // 1. Realizar la petición a tu controlador de Cf System
         const resp = await fetch(
            `/myvet/app/controllers/cotizacionesController.php?action=obtenerProductos`
        );


        const res = await resp.json();
        console.log(res.data);
exportarProductosAExcel(res.data)
   
}


async function exportarProductosAExcel(productos) {
    // 1. Crear un nuevo libro de trabajo
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Inventario Productos');

    // 2. Definir las columnas de la tabla y sus anchos
    worksheet.columns = [
        { header: 'ID Producto', key: 'producto_id', width: 12 },
        { header: 'SKU', key: 'sku', width: 15 },
        { header: 'Nombre del Producto', key: 'nombre', width: 30 },
        { header: 'U. Medida', key: 'unidad_medida', width: 12 },
        { header: 'U. Reporte', key: 'unidad_reporte', width: 12 },
        { header: 'Factor Conv.', key: 'factor_conversion', width: 15 },
        { header: 'ID Cat.', key: 'categoria_id', width: 10 },
        { header: 'P. Minorista', key: 'precio_minorista', width: 15 },
        { header: 'P. Mayorista', key: 'precio_mayorista', width: 15 },
        { header: 'P. Distribuidor', key: 'precio_distribuidor', width: 15 },
        { header: 'Medidas Adicionales (Nombre: Equiv.)', key: 'medidas_adicionales', width: 40 }
    ];

    // Estilo para la cabecera (Fondo azul desaturado profesional, texto blanco en negrita)
    const headerRow = worksheet.getRow(1);
    headerRow.font = { name: 'Segoe UI', size: 11, bold: true, color: { argb: 'FFFFFF' } };
    headerRow.fill = {
        type: 'pattern',
        pattern: 'solid',
        fgColor: { argb: '2E5B82' }
    };
    headerRow.alignment = { vertical: 'middle', horizontal: 'center' };
    headerRow.height = 25;

    // 3. Iterar con un forEach sobre los datos para ir agregando las filas
    productos.forEach((prod, index) => {
        
        // Mapeamos el sub-array de 'medidas_adicionales' a un string entendible en una sola celda
        const medidasTexto = prod.medidas_adicionales
            ? prod.medidas_adicionales.map(m => `${m.nombre}: ${parseFloat(m.equivalencia).toFixed(4)}`).join(' | ')
            : 'Ninguna';

        // Añadimos la fila al worksheet mapeando los tipos numéricos correctos
        const row = worksheet.addRow({
            producto_id: prod.producto_id,
            sku: prod.sku,
            nombre: prod.nombre,
            unidad_medida: prod.unidad_medida,
            unidad_reporte: prod.unidad_reporte,
            factor_conversion: parseFloat(prod.factor_conversion || 0),
            categoria_id: prod.categoria_id,
            precio_minorista: parseFloat(prod.precio_minorista || 0),
            precio_mayorista: parseFloat(prod.precio_mayorista || 0),
            precio_distribuidor: parseFloat(prod.precio_distribuidor || 0),
            medidas_adicionales: medidasTexto
        });

        // Formatear celdas de números y precios para que Excel los reconozca nativamente
        row.getCell('producto_id').alignment = { horizontal: 'center' };
        row.getCell('categoria_id').alignment = { horizontal: 'center' };
        row.getCell('sku').alignment = { horizontal: 'center' };
        row.getCell('unidad_medida').alignment = { horizontal: 'center' };
        row.getCell('unidad_reporte').alignment = { horizontal: 'center' };
        
        // Formatos numéricos monetarios y decimales
        row.getCell('factor_conversion').numFmt = '#,##0.00';
        row.getCell('precio_minorista').numFmt = '$#,##0.00';
        row.getCell('precio_mayorista').numFmt = '$#,##0.00';
        row.getCell('precio_distribuidor').numFmt = '$#,##0.00';

        // Aplicar bordes delgados gris claro a las filas
        row.eachCell((cell) => {
            cell.font = { name: 'Segoe UI', size: 10 };
            cell.border = {
                top: { style: 'thin', color: { argb: 'E0E0E0' } },
                bottom: { style: 'thin', color: { argb: 'E0E0E0' } },
                left: { style: 'thin', color: { argb: 'E0E0E0' } },
                right: { style: 'thin', color: { argb: 'E0E0E0' } }
            };
            
            // Zebra striping alternado (efecto visual gris claro en filas impares)
            if (index % 2 !== 0) {
                cell.fill = {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: { argb: 'F9FAFB' }
                };
            }
        });
        
        row.height = 20;
    });

    const filename = 'Reporte_Productos_Inventario.xlsx';

    // --- NUEVO: Procesamiento y descarga en el Navegador ---
    
    // Generar el buffer del archivo Excel
    const buffer = await workbook.xlsx.writeBuffer();
    
    // Crear un objeto Blob con el tipo MIME correcto para archivos XLSX
    const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
    
    // Crear un enlace temporal en el DOM
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    
    // Añadir al documento, hacer click de forma virtual y removerlo
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Liberar memoria del objeto URL creado
    URL.revokeObjectURL(link.href);
}
async function cargarCotizaciones() {
   let almacen= $('#filtroAlmacen').val();
   let fechaInicio= $('#fechaInicio').val();
    let fechaFin=$('#fechaFin').val();
console.log(almacen);

   const params = new URLSearchParams({
    action: 'listarCotizaciones',
    almacen: $('#filtroAlmacen').val(),
    fechaInicio: $('#fechaInicio').val(),
    fechaFin:$('#fechaFin').val(),
    estado:$('#filtroEstado').val(),
    buscador:$('#buscadorGeneral').val(),
    vendedor:$('#select-usuarios').val()
});

    let rol = <?= isset($_SESSION['rol_id']) ? (int)$_SESSION['rol_id'] : 0 ?>;
let tablahtml = '';
const res = await fetch(
    `/myvet/app/controllers/cotizacionesController.php?${params.toString()}`
);

let data=await res.json();
    


    data.data.forEach(s => {

        const id = String(s.id).padStart(5, '0');

        const fecha = new Date(s.fecha);
        const fechaFormateada =
            fecha.toLocaleDateString('es-MX') + ' ' +
            fecha.toLocaleTimeString('es-MX', {
                hour: '2-digit',
                minute: '2-digit'
            });

        const status = (s.estado || 'PENDIENTE').toUpperCase();

        let clase = 'bg-secondary text-white';

        switch (status) {
            case 'PENDIENTE':
                clase = 'bg-warning card-title-text';
                break;
            case 'COMPLETADO':
                clase = 'bg-primary text-white';
                break;
            case 'CANCELADO':
                clase = 'bg-danger text-white';
                break;
        }

        tablahtml += `
            <tr>

                <td>
                    <span class="card-title-text fw-bold">#${id}</span>
                </td>

                <td class="text-body-secondary small">
                    ${fechaFormateada}
                </td>

                <td class="fw-medium">
                    ${s.cliente_nombre ?? 'Sin asignar'}
                </td>

                <td>
                    <span class=" card-title-text border">
                        ${s.almacen_nombre}
                    </span>
                </td>
 <td>
                    <span class="  card-title-text border">
                        ${s.vendedor}
                    </span>
                </td>

                <td>
                    <span class="badge badge-status ${clase} rounded-pill">
                        ${status}
                    </span>
                </td>

                <td class="text-end">
        `;

        // Pendiente
        if (status === 'PENDIENTE' && rol < 3) {

            tablahtml += `
                <button class="btn btn-sm btn-white border shadow-sm"
                    onclick="gestionarSolicitud(${s.id})">
                    <i class="bi bi-eye text-primary"></i> Gestionar
                </button>

                <button class="btn btn-sm btn-white border shadow-sm"
                    onclick="eliminarSolicitud(${s.id})">
                    <i class="bi bi-trash text-danger"></i>
                </button>
            `;
        }

        // Completado
        if (status === 'COMPLETADO' && rol < 3) {

            tablahtml += `
                <button class="btn btn-sm btn-white border shadow-sm"
                    onclick="gestionarSolicitud(${s.id})">
                    <i class="bi bi-eye text-primary"></i> REUTILIZAR
                </button>
            `;
        }

        // Imprimir
        tablahtml += `
                    <button class="btn btn-sm btn-white border shadow-sm rounded-pill px-3"
                        onclick="prepararImpresion(${s.id})"
                        title="Imprimir solicitud">

                        <i class="bi bi-printer text-primary me-1"></i>
                        <span class="card-title-text fw-medium">Imprimir</span>

                    </button>

                </td>

            </tr>
        `;

    });

   $('#tablaCotizacionesListar').html(tablahtml);
}         
      

    $(document).ready(function() {
       
       
$('#buscadorGeneral').on('keyup', cargarCotizaciones);
$('#filtroAlmacen').on('change', cargarCotizaciones);
$('#filtroEstado').on('change', cargarCotizaciones);
$('#fechaInicio').on('change', cargarCotizaciones);
$('#fechaFin').on('change', cargarCotizaciones);
$('#select-usuarios').on('change', cargarCotizaciones);
        

     });
      async function eliminarSolicitud(id) {
        const r = await Swal.fire({
            title: '¿Eliminar?',
            text: 'No podrás revertir esto',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, borrar'
        });
        if (r.isConfirmed) {
            const fd = new FormData();
            fd.append('id', id);
            const resp = await fetch(`${URL_CONTROLADOR}?action=eliminar`, {
                method: 'POST',
                body: fd
            });
            const res = await resp.json();
            if (res.status === 'success') location.reload();
            else Swal.fire('Error', res.message, 'error');
        }
    }
    </script>
    <script>
    // Selecciona todos los inputs de texto y también los textareas
    document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
        elemento.addEventListener('input', function() {
            // Convierte el valor a mayúsculas en tiempo real
            this.value = this.value.toUpperCase();
        });
    });
</script>
</body>

</html>
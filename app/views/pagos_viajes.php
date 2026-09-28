<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago por viajes | Cf System</title>
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

    <style>
       /* Glassmorphism & Contenedores */
.glass-card {
 
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid rgba(225, 230, 240, 0.7);
}

/* Form Labels Finos */
.form-label-subtle {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #6c757d;
  margin-bottom: 0.4rem;
  display: block;
}

/* Inputs Personalizados (iOS Style) */
.custom-input {
  
  border-radius: 10px;
  padding: 0.55rem 0.85rem;
  font-size: 0.9rem;
  transition: all 0.2s ease;
 
}

.custom-input:focus {
 
  border-color: #0d6efd;
  box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12);
}

/* Tabla Elegante */
.custom-table {
  border-collapse: separate;
  border-spacing: 0;
}

.custom-table thead th {
 
 
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  font-weight: 700;
  padding: 12px 16px;
  border-bottom: 1px solid #e2e8f0;
}

.custom-table tbody td {
  padding: 14px 16px;
  
  font-size: 0.9rem;
  border-bottom: 1px solid #f1f5f9;
}

.custom-table tbody tr:last-child td {
  border-bottom: none;
}

.custom-table tbody tr:hover {
  background-color: rgba(241, 245, 249, 0.6);
}

/* Modal Styling */
.icon-shape {
  width: 42px;
  height: 42px;
  display: flex;
  align-items: center;
  justify-content: center;
}
    </style>
</head>

<body>

<?php renderizarLayout($paginaActual); ?>
<main class="main-content">
  <div class="container-fluid px-4 py-3">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="fw-bold m-0  tracking-tight">Pagos por viaje</h2>
        <p class="text-body-secondary small m-0">Gestión de rutas, choferes y viáticos en tiempo real</p>
      </div>
      <div>
        <span class="badge text-primary  shadow-sm px-3 py-2 rounded-pill border">
          <i class="bi bi-shield-check text-success me-1"></i> Sistema Activo
        </span>
      </div>
    </div>

    <!-- FILTROS CARD -->
    <div class="glass-card p-4 mb-4  shadow-sm rounded-4">
      <form id="formFiltros" class="row g-3 align-items-end">

       <div class="col-md-2">
    <label class="form-label-subtle">
        <i class="bi bi-calendar3 me-1"></i>Inicio
    </label>
    <?php
$hoy = date('Y-m-d');
$inicioSemana = date('Y-m-d', strtotime('monday this week'));
?>
    <input
        type="date"
        id="fecha_inicio"
        value="<?= $inicioSemana ?>"
        class="form-control custom-input">
</div>

<div class="col-md-2">
    <label class="form-label-subtle">
        <i class="bi bi-calendar3-event me-1"></i>Fin
    </label>
    <input
        type="date"
        id="fecha_fin"
        value="<?= $hoy ?>"
        class="form-control custom-input">
</div>

        <div class="col-md-2">
          <label class="form-label-subtle">
            <i class="bi bi-building me-1"></i>Sucursal
          </label>
          <select id="almacen_select" class="form-select custom-input" <?= ($almacen_sesion != 0) ? 'disabled' : '' ?>>
            <?php if ($almacen_sesion == 0): ?>
              <option value="0">Todas las Sucursales</option>
            <?php endif; ?>

            <?php if(isset($listaAlmacenes)) foreach($listaAlmacenes as $alm): ?>
              <option value="<?= $alm['id'] ?>" <?= ($almacen_sesion == $alm['id']) ? 'selected' : '' ?>>
                <?= $alm['nombre'] ?>
              </option>
            <?php endforeach; ?>
          </select>

          <?php if ($almacen_sesion != 0): ?>
            <input type="hidden" id="almacen" value="<?= $almacen_sesion ?>">
          <?php endif; ?>
        </div>

        <div class="col-md-2">
          <label class="form-label-subtle">
            <i class="bi bi-person-badge me-1"></i>Chofer
          </label>
       <select id="chofer" class="form-select">
    <option value="">Seleccione un trabajador</option>
</select>   </div>

       
          <input type="hidden" id="ayudante" class="form-control custom-input" placeholder="Buscar ayudante...">
        

        <div class="col-md-2">
          <label class="form-label-subtle">
            <i class="bi bi-flag me-1"></i>Estado
          </label>
          <select id="estado" class="form-select custom-input">
            <option value="">Todos</option>
            <option value="PENDIENTE">Pendiente</option>
            <option value="FINALIZADO">Finalizado</option>
          </select>
        </div>

        <div class="col-12 text-end pt-2">
          <button type="button" onclick="AppLogistica.cargar()" class="btn btn-primary px-4 fw-semibold rounded-3 shadow-sm btn-action">
            <i class="bi bi-search me-2"></i>Consultar Viajes
          </button>
        </div>

      </form>
    </div>

    <!-- TABLA DE VIAJES -->
    <div class="glass-card p-4  shadow-sm rounded-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold m-0 ">
          <i class="bi bi-truck me-2 text-primary"></i>Listado de Viajes
        </h6>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle custom-table">
          <thead>
            <tr>
              <th>Fecha</th>
              <th>Almacén</th>
              <th>Dirección</th>
              <th>Chofer</th>
              <th>Unidad</th>
              <th class="text-end">Monto Pagado</th>
              <th class="text-center">Acción</th>
            </tr>
          </thead>
          <tbody id="tabla_viajes">
            <!-- Renderizado dinámico -->
          </tbody>
        </table>
      </div>
    </div>

  </div>
</main>

<!-- MODAL REGISTRAR PAGO -->
<div class="modal fade" id="modalPagoViaje" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content  shadow-lg rounded-4 overflow-hidden">

      <div class="modal-header bg-dark text-white p-4">
        <div class="d-flex align-items-center">
          <div class="icon-shape bg-primary text-white rounded-3 me-3 p-2">
            <i class="bi bi-cash-stack fs-5"></i>
          </div>
          <div>
            <h5 class="modal-title fw-bold m-0">Registrar Pago de Viaje</h5>
            <small class="text-white-50">Ingrese los detalles del viático/pago</small>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-4">

        <!-- Ficha Resumen -->
        <div class=" p-3 rounded-3 mb-3 border">
          <div class="row g-2">
            <div class="col-6">
              <span class="text-body-secondary small d-block">Chofer</span>
              <strong id="mpago_chofer" class="">-</strong>
            </div>
            <div class="col-6">
              <span class="text-body-secondary small d-block">Fecha</span>
              <strong id="mpago_fecha" class="">-</strong>
            </div>
            <div class="col-12 mt-2 pt-2 border-top">
              <span class="text-body-secondary small d-block">Destino / Viaje</span>
              <span id="mpago_viaje" class=" fw-medium">-</span>
            </div>
          </div>
        </div>

        <!-- Campo Monto -->
        <div class="mb-3">
          <label for="mpago_monto" class="form-label fw-semibold  small">
            Monto Acreditar
          </label>
          <div class="input-group input-group-lg">
            <span class="input-group-text bg-white border-end-0 fw-bold text-body-secondary">$</span>
            <input type="number" class="form-control border-start-0 ps-0 fw-bold text-primary" id="mpago_monto" placeholder="0.00" step="0.01" min="0">
          </div>
        </div>

        <!-- Ocultos -->
        <input type="hidden" id="mpago_idViaje">
        <input type="hidden" id="mpago_idChofer">

      </div>

      <div class="modal-footer  px-4 py-3 ">
        <button type="button" class="btn btn-link text-secondary text-decoration-none me-2" data-bs-dismiss="modal">
          Cancelar
        </button>
        <button type="button" class="btn btn-primary px-4 fw-semibold rounded-3 shadow-sm" id="mpago_btnGuardar" onclick="guardarPago()">
          <i class="bi bi-check2-circle me-1"></i> Guardar Pago
        </button>
      </div>

    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>

const AppLogistica = {

    url: '/myvet/app/controllers/pagos_viajesController.php',

    cargar: function() {
        let almacen = 0;

// 🔥 si existe hidden (cuando está bloqueado)
if ($('#almacen').length) {
    almacen = $('#almacen').val();
} else {
    almacen = $('#almacen_select').val();
}

       const params = {
    action: 'listarViajes',
    fecha_inicio: $('#fecha_inicio').val(),
    fecha_fin: $('#fecha_fin').val(),
    almacen: almacen,
    chofer: $('#chofer').val(),
    ayudante: $('#ayudante').val(),
    estado: $('#estado').val()
};

        $.getJSON(this.url, params, (res) => {

            if (res.success) {
                this.renderViajes(res.data);
                console.log(res.data);
            }

        });
        const parametros2 = {
            action: 'getEstadisticas',
            
    fecha_inicio: $('#fecha_inicio').val(),
    fecha_fin: $('#fecha_fin').val(),
    almacen: almacen,
    chofer: $('#chofer').val(),
    ayudante: $('#ayudante').val(),
    estado: $('#estado').val()
        };

        // 🔥 segunda consulta
        $.getJSON(this.url, parametros2, (res) => {

            if (res.success) {
                this.renderPersonal(res.data);
            }

        });
    },

    renderViajes: function(data) {
      console.log(data);

        let html = '';
        

        if (!data.length) {
            html = `<tr><td colspan="6" class="text-center text-body-secondary">Sin datos</td></tr>`;
        }
data.forEach(v => {

    let boton = Number(v.monto) > 0
? `
<button
    type="button"
    class="btn btn-outline-danger btn-sm rounded-pill px-3 shadow-sm"
    onclick="eliminarPago(${v.viaje_id})">

    <i class="bi bi-trash me-1"></i>
    Eliminar Pago

</button>
`
: `
<button
    type="button"
    class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm"
    onclick="abrirModalPago(
         ${v.viaje_id},
                '${v.nombre_chofer || ''}',
                '${v.chofer_id || ''}',
                '${v.direccion || ''}',
                '${v.fecha_viaje || ''}'
    )">

    <i class="bi bi-cash-coin me-1"></i>
    Agregar Pago

</button>
`;
     
    html += `
    <tr>

        <td class="text-nowrap">
            <i class="bi bi-calendar-event text-primary me-1"></i>
            ${v.fecha_viaje}
        </td>

        <td>
            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                <i class="bi bi-building me-1"></i>
                ${v.almacenOrigen}
            </span>
        </td>

        <td style="max-width:280px;">
            <div class="fw-semibold ">
                <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                ${v.direccion}
            </div>
        </td>

        <td>
            <i class="bi bi-person-circle text-secondary me-1"></i>
            ${v.nombre_chofer || '<span class="text-body-secondary">Sin asignar</span>'}
        </td>

        <td>
            <span class="badge  text-success border">
                <i class="bi bi-truck me-1"></i>
                ${v.unidad_nombre || '-'}
            </span>
        </td>

        <td class="text-end">
            <span class="fw-bold text-success fs-6">
                $${Number(v.monto || 0).toLocaleString('es-MX', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })}
            </span>
        </td>

        <td class="text-center">
${boton}
           
        </td>

    </tr>
    `;
});

        $('#tabla_viajes').html(html);
    },

    renderPersonal: function(data) {

        let html = '';

        data.forEach(p => {
            html += `
                <tr>
                    <td>${p.nombre}</td>
                    <td>${p.almacenes}</td>
                    <td class="fw-bold">${p.total_viajes}</td>
                </tr>
            `;
        });

        $('#tabla_personal').html(html);
    }

};

$(document).ready(() => {
    cargarTrabajadores();
    
    AppLogistica.cargar();
});
async function cargarTrabajadores() {
    try {

        const res = await fetch('/myvet/app/controllers/trabajadoresController.php?action=getTrabajadores');
        const data = await res.json();


        const select = document.getElementById('chofer');

        select.innerHTML = '<option value="">Seleccione un trabajador</option>';

        data.data.forEach(trabajador => {

            select.innerHTML += `
                <option value="${trabajador.id}">
                    ${trabajador.nombre}
                </option>
            `;

        });

    } catch (error) {
        console.error(error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No fue posible cargar los trabajadores.'
        });
    }
}
function abrirModalPago(idViaje, chofer,chofer_id, viaje, fecha) {

    document.getElementById("mpago_idViaje").value = idViaje;
    document.getElementById("mpago_idChofer").value = chofer_id;
    document.getElementById("mpago_chofer").textContent = chofer;
    document.getElementById("mpago_viaje").textContent = viaje;
    document.getElementById("mpago_fecha").textContent = fecha;
    document.getElementById("mpago_monto").value = "";

    const modal = new bootstrap.Modal(document.getElementById("modalPagoViaje"));
    modal.show();
}

async function guardarPago() {

    try {

        const viaje_id = $('#mpago_idViaje').val();
        const chofer_id = $('#mpago_idChofer').val();
        const monto = $('#mpago_monto').val();
        const mpago_fecha = $('#mpago_fecha').text();

        const fd = new FormData();
        fd.append('id', viaje_id);
        fd.append('chofer_id', chofer_id);
        fd.append('monto', monto);
        

        const res = await fetch('/myvet/app/controllers/pagos_viajesController.php?action=aplicarPagoPorViaje', {
            method: 'POST',
            body: fd
        });

        const data = await res.json();

        if (!data.success) {
            throw new Error(data.message);
        }

        await Swal.fire({
            icon: 'success',
            title: 'Pago registrado',
            text: data.message,
            timer: 1500,
            showConfirmButton: false
        });

        bootstrap.Modal.getInstance(document.getElementById('modalPagoViaje')).hide();
AppLogistica.cargar();
        

    } catch (error) {
        console.error(error);

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message
        });
    }
}
async function eliminarPago(id) {

    const confirmacion = await Swal.fire({
        title: '¿Eliminar pago?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545'
    });

    if (!confirmacion.isConfirmed) return;

    try {

        const fd = new FormData();
        fd.append('id', id);

        const res = await fetch('/myvet/app/controllers/pagos_viajesController.php?action=eliminarPagoPorViaje', {
            method: 'POST',
            body: fd
        });

        const data = await res.json();

        if (!data.success) {
            throw new Error(data.message);
        }

        await Swal.fire({
            icon: 'success',
            title: 'Pago eliminado',
            text: data.message,
            timer: 1500,
            showConfirmButton: false
        });

        AppLogistica.cargar();
    

    } catch (error) {

        console.error(error);

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message
        });
    }
}
</script>
</body>
</html>
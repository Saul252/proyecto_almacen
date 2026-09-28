<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logística | Cf System</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>

    <style>
        :root { --apple-bg: #f5f5f7; --apple-blue: #007aff; }

        body {
           
            font-family: -apple-system, sans-serif;
        }

        .main-content {
            margin-left: 0px;
            padding: 80px 20px;
        }

        .glass-card {
           
            backdrop-filter: blur(15px);
            border-radius: 20px;
            
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .ios-input {
            
           
            border-radius: 10px;
            padding: 10px;
        }

        .table thead th {
           
            font-size: 11px;
            color: #8e8e93;
            
        }

        @media (max-width: 992px) {
            .main-content { margin-left: 0; }
        }
    </style>
</head>

<body>

<?php renderizarLayout($paginaActual); ?>

<main class="main-content">
<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h1 class="fw-bold m-0">Logística</h1>
            <p class="text-secondary m-0">Control de viajes y personal</p>
        </div>
    </div>

    <!-- FILTROS -->
    <div class="glass-card p-4 mb-4">
        <form id="formFiltros" class="row g-3">

            <div class="col-md-2">
                <label class="small fw-bold text-body-secondary">Inicio</label>
                <input type="date" id="fecha_inicio" class="form-control ios-input">
            </div>

            <div class="col-md-2">
                <label class="small fw-bold text-body-secondary">Fin</label>
                <input type="date" id="fecha_fin" class="form-control ios-input">
            </div>

           <div class="col-md-2">

    <label class="small fw-bold text-body-secondary text-uppercase">
        Almacén / Sucursal
    </label>

    <select id="almacen_select" class="form-select ios-input"
        <?= ($almacen_sesion != 0) ? 'disabled' : '' ?>>

        <?php if ($almacen_sesion == 0): ?>
            <option value="0">🌐 Todas las Sucursales</option>
        <?php endif; ?>

        <?php if(isset($listaAlmacenes)) foreach($listaAlmacenes as $alm): ?>
            <option value="<?= $alm['id'] ?>"
                <?= ($almacen_sesion == $alm['id']) ? 'selected' : '' ?>>
                📍 <?= $alm['nombre'] ?>
            </option>
        <?php endforeach; ?>

    </select>

    <!-- 🔥 hidden SI está bloqueado -->
    <?php if ($almacen_sesion != 0): ?>
        <input type="hidden" id="almacen" value="<?= $almacen_sesion ?>">
    <?php endif; ?>

</div>
            <div class="col-md-2">
                <label class="small fw-bold text-body-secondary">Chofer</label>
                <input type="text" id="chofer" class="form-control ios-input">
            </div>

            <div class="col-md-2">
                <label class="small fw-bold text-body-secondary">Ayudante</label>
                <input type="text" id="ayudante" class="form-control ios-input">
            </div>

            <div class="col-md-2">
                <label class="small fw-bold text-body-secondary">Estado</label>
                <select id="estado" class="form-select ios-input">
                    <option value="">Todos</option>
                    <option value="PENDIENTE">Pendiente</option>
                    <option value="FINALIZADO">Finalizado</option>
                </select>
            </div>

            <div class="col-md-2">
                <button type="button" onclick="AppLogistica.cargar()" 
                    class="btn btn-primary w-100 fw-bold">
                    Consultar
                </button>
            </div>

        </form>
    </div>

    <!-- RESUMEN PERSONAL -->
    <div class="glass-card p-4 mb-4">
        <h6 class="fw-bold mb-3">Viajes por Personal</h6>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Empleado</th>
                        <th>Almacen</th>
                        <th>Total Viajes</th>
                    </tr>
                </thead>
                <tbody id="tabla_personal"></tbody>
            </table>
        </div>
    </div>

    <!-- VIAJES -->
    <div class="glass-card p-4">
        <h6 class="fw-bold mb-3">Listado de Viajes</h6>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Almacen</th>
                        <th>Folio</th>
                        <th>Fecha</th>
                        <th>Chofer</th>
                        <th>Ayudantes</th>
                        <th>Unidad</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody id="tabla_viajes"></tbody>
            </table>
        </div>
    </div>

</div>
</main>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

const AppLogistica = {

    url: '/myvet/app/controllers/viajesTrabajadoresController.php',

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

        let html = '';

        if (!data.length) {
            html = `<tr><td colspan="6" class="text-center text-body-secondary">Sin datos</td></tr>`;
        }

        data.forEach(v => {
            html += `
                <tr>
                                    <td class="fw-bold">${v.almacenOrigen}</td>
                    <td class="fw-bold">${v.folio_viaje}</td>
                    <td>${v.fecha_viaje}</td>
                    <td>${v.nombre_chofer || '-'}</td>
                    <td>${v.ayudantes || '-'}</td>
                    <td>${v.unidad_nombre || '-'}</td>
                    <td>${v.estatus_logistico}</td>
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
    AppLogistica.cargar();
});

</script>

</body>
</html>
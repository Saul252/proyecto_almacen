<?php
date_default_timezone_set('America/Mexico_City');

// Evita warnings si el archivo se incluye sin definir la variable
$archivoActual = $archivoActual ?? '';

$modulos = [

    [
        'id_grupo' => 'ventas_clientes',
        'titulo' => 'Ventas',
        'icono' => 'bi-bag-heart-fill',
        'color' => 'text-success',
        'submodulos' => [
            ['id' => 'ventas', 'url' => '/myvet/ventas', 'icon' => 'bi-cash-coin', 'label' => 'Ventas', 'active' => ($archivoActual == 'ventasController.php')],
            ['id' => 'remisiones', 'url' => '/myvet/remisiones', 'icon' => 'bi-file-earmark-diff-fill', 'label' => 'Remisiones', 'active' => ($archivoActual == 'requisicionesController.php')],
            ['id' => 'cajaRapida', 'url' => '/myvet/cajaRapida', 'icon' => 'bi-reception-4', 'label' => 'Caja Rápida', 'active' => ($archivoActual == 'cajaRapidaController.php')],
            ['id' => 'cotizaciones', 'url' => '/myvet/cotizaciones', 'icon' => 'bi-file-earmark-spreadsheet-fill', 'label' => 'Cotizaciones', 'active' => ($archivoActual == 'cotizacionesController.php')],
            ['id' => 'clientes', 'url' => '/myvet/clientes', 'icon' => 'bi-person-rolodex', 'label' => 'Clientes', 'active' => ($archivoActual == 'clientesController.php')],
            ['id' => 'clientesEstatus', 'url' => '/myvet/clientesEstatus', 'icon' => 'bi-person-bounding-box', 'label' => 'Estatus Clientes', 'active' => ($archivoActual == 'clientesEstatus.php')],
            ['id' => 'ventasVendedor', 'url' => '/myvet/ventasVendedor', 'icon' => 'bi-award-fill', 'label' => 'Ventas Vendedor', 'active' => ($archivoActual == 'historialPedidosVendedorController.php')],
            ['id' => 'ventashistorial', 'url' => '/myvet/ventashistorial', 'icon' => 'bi-journal-text', 'label' => 'Historial de Ventas', 'active' => ($archivoActual == 'ventasHistorialController.php')],
            ['id' => 'comprobantes', 'url' => '/myvet/comprobantes', 'icon' => 'bi-patch-check-fill', 'label' => 'Crear Comprobantes', 'active' => ($archivoActual == 'comprobantesPagoController.php')],
            ['id' => 'registrarPagos', 'url' => '/myvet/registrarPagos', 'icon' => 'bi-wallet-fill', 'label' => 'Registrar Pagos', 'active' => ($archivoActual == 'registrarPagosController.php')],
            ['id' => 'historialPagos', 'url' => '/myvet/historialPagos', 'icon' => 'bi-coin', 'label' => 'Historial Pagos', 'active' => ($archivoActual == 'historialPagosController.php')],
        ]
    ],
    [
        'id_grupo' => 'compras_proveedores',
        'titulo' => 'Egresos',
        'icono' => 'bi-cart-dash-fill',
        'color' => 'text-danger',
        'submodulos' => [
            ['id' => 'compras', 'url' => '/myvet/compras', 'icon' => 'bi-credit-card-fill', 'label' => 'Compras y Gastos', 'active' => ($archivoActual == 'egresosController.php' || $archivoActual == 'gastos.php')],
            ['id' => 'proveedores', 'url' => '/myvet/proveedores', 'icon' => 'bi-building-up', 'label' => 'Proveedores', 'active' => ($archivoActual == 'proveedoresController.php')],
            ['id' => 'solicitudesCompra', 'url' => '/myvet/solicitudesCompra', 'icon' => 'bi-file-earmark-plus-fill', 'label' => 'Solicitudes Compra', 'active' => ($archivoActual == 'solicitudesCompraController.php')],
        ]
    ],
    [
        'id_grupo' => 'inventario_almacen',
        'titulo' => 'Inventarios',
        'icono' => 'bi-archive-fill',
        'color' => 'text-warning',
        'submodulos' => [
            ['id' => 'almacenes', 'url' => '/myvet/almacenes', 'icon' => 'bi-building-fill', 'label' => 'Almacén', 'active' => ($archivoActual == 'almacenes.php' || $archivoActual == 'almacen.php')],
            ['id' => 'movimientos', 'url' => '/myvet/movimientos', 'icon' => 'bi-shuffle', 'label' => 'Movimientos', 'active' => ($archivoActual == 'movimientosController.php')],
            ['id' => 'Mermas', 'url' => '/myvet/Mermas', 'icon' => 'bi-trash3-fill', 'label' => 'Mermas', 'active' => ($archivoActual == 'mermasController.php')],
            ['id' => 'transmutaciones', 'url' => '/myvet/transmutaciones', 'icon' => 'bi-signpost-split-fill', 'label' => 'Conversiones', 'active' => ($archivoActual == 'transmutacionesController.php')],
            ['id' => 'historialLotes', 'url' => '/myvet/historialLotes', 'icon' => 'bi-upc-scan', 'label' => 'Historial de Lotes', 'active' => ($archivoActual == 'lotesHistorialController.php')],
            ['id' => 'comprasHistorial', 'url' => '/myvet/comprasHistorial', 'icon' => 'bi-bookshelf', 'label' => 'Historial de Compras', 'active' => ($archivoActual == 'comprasHistorialController.php')],
        ]
    ],
    [
        'id_grupo' => 'finanzas_tesoreria',
        'titulo' => 'Finanzas',
        'icono' => 'bi-coin',
        'color' => 'text-info',
        'submodulos' => [
            ['id' => 'finanzas', 'url' => '/myvet/finanzas', 'icon' => 'bi-activity', 'label' => 'Finanzas', 'active' => ($archivoActual == 'finanzasController.php')],
            ['id' => 'finanzas_admin', 'url' => '/myvet/finanzas_admin', 'icon' => 'bi-kanban-fill', 'label' => 'Finanzas Admin', 'active' => ($archivoActual == 'finanzasAdmController.php')],
            ['id' => 'corteCaja', 'url' => '/myvet/corteCaja', 'icon' => 'bi-receipt', 'label' => 'Corte de Caja', 'active' => ($archivoActual == 'corteCajaController.php')],
            ['id' => 'tesoreria', 'url' => '/myvet/tesoreria', 'icon' => 'bi-bank', 'label' => 'Tesorería', 'active' => ($archivoActual == 'tesoreriaController.php')],
        ]
    ],
    [
        'id_grupo' => 'logistica_distribucion',
        'titulo' => 'Logística',
        'icono' => 'bi-pin-map-fill',
        'color' => 'text-primary',
        'submodulos' => [
            ['id' => 'entregas', 'url' => '/myvet/entregas', 'icon' => 'bi-send-check-fill', 'label' => 'Despachos', 'active' => ($archivoActual == 'entregasController.php')],
            ['id' => 'vehiculos', 'url' => '/myvet/vehiculos', 'icon' => 'bi-car-front-fill', 'label' => 'Vehículos', 'active' => ($archivoActual == 'vehiculosController.php')],
            ['id' => 'repartos', 'url' => '/myvet/repartos', 'icon' => 'bi-compass-fill', 'label' => 'Repartos', 'active' => ($archivoActual == 'repartosController.php')],
            ['id' => 'misRepartos', 'url' => '/myvet/misRepartos', 'icon' => 'bi-sign-turn-right-fill', 'label' => 'Mis Repartos', 'active' => ($archivoActual == 'misRepartosController.php')],
            ['id' => 'viajesTrabajadores', 'url' => '/myvet/viajesTrabajadores', 'icon' => 'bi-pass-fill', 'label' => 'Viajes Trabajadores', 'active' => ($archivoActual == 'viajesTrabajadoresController.php')],
            ['id' => 'mantenimientos', 'url' => '/myvet/mantenimientos', 'icon' => 'bi-tools', 'label' => 'Mantenimientos', 'active' => ($archivoActual == 'mantenimientosController.php')],
            ['id' => 'verificaciones', 'url' => '/myvet/verificaciones', 'icon' => 'bi-check-all', 'label' => 'Verificaciones', 'active' => ($archivoActual == 'verificacionesController.php')],
        ]
    ],
    // 🦷 ODONTOLOGÍA
    [
        'id_grupo' => 'odontologia',
        'titulo' => 'Odontología',
        'icono' => 'bi-emoji-smile-fill',
        'color' => 'text-info',
        'submodulos' => [
            ['id' => 'clientesPacientes', 'url' => '/myvet/misPacientesDental', 'class' => 'text-info', 'icon' => 'bi-person-vcard-fill', 'label' => 'Pacientes Dentales', 'active' => ($archivoActual == 'clientesPacientesDentales.php')],
            ['id' => 'citasDentales', 'url' => '/myvet/citasDental', 'class' => 'text-info', 'icon' => 'bi-calendar-check-fill', 'label' => 'Citas Dentales', 'active' => ($archivoActual == 'citasController.php')],
            ['id' => 'consultaDental', 'url' => '/myvet/consultaDental', 'class' => 'text-info', 'icon' => 'bi-clipboard2-pulse-fill', 'label' => 'Consulta Dental', 'active' => ($archivoActual == 'consultaDental')],
        ]
    ],
    // 🩺 MEDICINA
    [
        'id_grupo' => 'medicina',
        'titulo' => 'Medicina',
        'icono' => 'bi-heart-pulse-fill',
        'color' => 'text-danger',
        'submodulos' => [
            ['id' => 'misPacientesMedicina', 'url' => '/myvet/misPacientesMedicina', 'class' => 'text-danger', 'icon' => 'bi-person-vcard-fill', 'label' => 'Pacientes Medicina', 'active' => ($archivoActual == 'clientesPacientesMedicina.php')],
            ['id' => 'citasMedicas', 'url' => '/myvet/citasMedicas', 'class' => 'text-danger', 'icon' => 'bi-calendar-plus-fill', 'label' => 'Citas Médicas', 'active' => ($archivoActual == 'citasMedicasController.php')],
            ['id' => 'consultaMedica', 'url' => '/myvet/consultaMedica', 'class' => 'text-danger', 'icon' => 'bi-file-medical-fill', 'label' => 'Consulta Médica', 'active' => ($archivoActual == 'consultaMedica')]
        ]
    ],
    [
        'id_grupo' => 'atencion_veterinaria',
        'titulo' => 'Atención Veterinaria',
        'icono' => 'bi-hospital-fill',
        'color' => 'text-success',
        'submodulos' => [
            ['id' => 'pacientes', 'url' => '/myvet/pacientes', 'icon' => 'bi-paw-fill', 'label' => 'Mascotas / Pacientes', 'active' => ($archivoActual == 'mascotasController.php' || $archivoActual == 'pacientesController.php')],
            ['id' => 'expedientes', 'url' => '/myvet/expedientes', 'icon' => 'bi-folder2-open', 'label' => 'Expedientes Médicos', 'active' => ($archivoActual == 'historialExpedienteController.php')],
            ['id' => 'consultas', 'url' => '/myvet/consultas', 'icon' => 'bi-journal-medical', 'label' => 'Consultas Médicas', 'active' => ($archivoActual == 'consultasController.php' || $archivoActual == 'consultaController.php')],
        ]
    ],
    [
        'id_grupo' => 'recursos_humanos',
        'titulo' => 'RH',
        'icono' => 'bi-person-vcard',
        'color' => 'text-warning',
        'submodulos' => [
            ['id' => 'trabajadores', 'url' => '/myvet/trabajadores', 'icon' => 'bi-person-badge', 'label' => 'Trabajadores', 'active' => ($archivoActual == 'trabajadoresController.php')],
            ['id' => 'nomina', 'url' => '/myvet/nomina', 'icon' => 'bi-currency-dollar', 'label' => 'Nómina', 'active' => ($archivoActual == 'nominaController.php')],
            ['id' => 'prestamos', 'url' => '/myvet/prestamos', 'icon' => 'bi-piggy-bank-fill', 'label' => 'Préstamos', 'active' => ($archivoActual == 'prestamosController.php')],
            ['id' => 'faltas', 'url' => '/myvet/faltas', 'icon' => 'bi-person-x-fill', 'label' => 'Faltas', 'active' => ($archivoActual == 'faltasController.php')],
            ['id' => 'vacaciones', 'url' => '/myvet/vacaciones', 'icon' => 'bi-umbrella-fill', 'label' => 'Vacaciones', 'active' => ($archivoActual == 'vacacionesController.php')],
            ['id' => 'pagos_viajes', 'url' => '/myvet/pagos_viajes', 'icon' => 'bi-credit-card', 'label' => 'Pagos Viajes', 'active' => ($archivoActual == 'pagos_viajesController.php')],
        ]
    ],
    [
        'id_grupo' => 'administracion',
        'titulo' => 'Administración',
        'icono' => 'bi-shield-lock-fill',
        'color' => 'text-info',
        'submodulos' => [
            ['id' => 'usuarios', 'url' => '/myvet/usuarios', 'icon' => 'bi-people-fill', 'label' => 'Usuarios', 'active' => ($archivoActual == 'usuariosController.php')],
            ['id' => 'miAcceso', 'url' => '/myvet/miAcceso', 'icon' => 'bi-person-circle', 'label' => 'Mi Cuenta', 'active' => ($archivoActual == 'miAccesoController.php')],
            ['id' => 'Configuracion', 'url' => '/myvet/configuracionSistema', 'icon' => 'bi-gear-fill', 'label' => 'Configuración', 'active' => ($archivoActual == 'configuracionController.php')],
        ]
    ]
];

// Estado del enlace INICIO (antes usaba $grupoActivo sin definir)
$inicioActivo = ($archivoActual == 'inicio.php');

// Identificador del usuario para guardar sus preferencias por separado en el equipo
$idUsuarioPrefs = $_SESSION['id_usuario'] ?? $_SESSION['id'] ?? $_SESSION['nombre'] ?? 'anon';
?>

<?php if (!empty($_SESSION['sidebar_bg'])): ?>
    <style>
        /* Color personalizado SOLO cuando NO se está en modo oscuro */
        :root:not([data-bs-theme="dark"]),
        [data-bs-theme="light"] {
            --navbar-dark:
                <?= htmlspecialchars($_SESSION['sidebar_bg'], ENT_QUOTES) ?>
                !important;
        }
    </style>
<?php endif; ?>

<style>
    .navbar-premium {
        background: var(--navbar-dark) !important;
        color: #0b0b0b !important;
    }

    .navbar-premium .active-nav-item {
        background: var(--navbar-dark) !important;
        border-radius: 8px;
        color: #0b0b0b !important;
    }

    @media (min-width: 992px) {
        .navbar .dropdown:hover>.dropdown-menu {
            display: block;
        }
    }

    .dropdown-item {
        color: #000000 !important;
    }

    @media (max-width: 991.98px) {
        .navbar-collapse {
            background: var(--navbar-dark) !important;
            padding: 1rem;
            color: #ffffff !important;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            max-height: 80vh;
            overflow-y: auto;
        }

        .main-conent {
            padding-top: 70px !important;
        }

        .navbar-nav .dropdown-menu {
            background: var(--navbar-dark) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            margin-top: 0.25rem;
        }

        .navbar-nav .dropdown-item:hover,
        .navbar-nav .dropdown-item:focus {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
        }
    }

    .modulo-item.modulo-oculto {
        display: none !important;
    }

    #contenedorMasModulos .mas-grupo-titulo {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--bs-secondary-color, #94a3b8);
        padding: 0.4rem 0.8rem 0.2rem 0.8rem;
        margin-top: 0.2rem;
    }

    /* Puente invisible para que el hover no se rompa */
    .dropdown-menu::before {
        content: "";
        position: absolute;
        top: -12px;
        left: 0;
        right: 0;
        height: 12px;
        background: transparent;
    }

    .dropdown-menu-end {
        right: 0 !important;
        left: auto !important;
        transform: none !important;
    }

    /* Notificaciones */
    .hover-notif:hover {
        background-color: var(--bs-secondary-bg) !important;
    }

    #menuNotif .dropdown-item,
    #menuNotif label {
        color: var(--bs-body-color) !important;
    }

    .notif-config-item {
        cursor: pointer;
        border-radius: 8px;
        padding: 6px 8px;
    }

    .notif-config-item:hover {
        background-color: var(--bs-secondary-bg);
    }
</style>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

<nav class="navbar fixed-top navbar-expand-lg navbar-dark navbar-premium shadow-sm rounded-bottom-5">
    <div class="container-fluid px-2 px-md-4">

        <!-- BOTÓN MÓVIL -->
        <div class="d-flex align-items-center gap-2 me-lg-3">
            <button class="navbar-toggler shadow-none p-1 ms-2 border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarMainContent" aria-controls="navbarMainContent" aria-expanded="false"
                aria-label="Abrir Menú">
                <i class="bi bi-list fs-2 text-body-emphasis"></i>
            </button>
        </div>

        <!-- MÓDULOS -->
        <div class="collapse navbar-collapse" id="navbarMainContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-1 d-flex flex-wrap flex-lg-nowrap" id="mainNavList">

                <li class="nav-item modulo-item" data-titulo="">
                    <a class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-dark <?= $inicioActivo ? 'active-nav-item fw-bold' : '' ?>"
                        href="/myvet/inicio">
                        <i class="bi-house-heart-fill fs-5 icono text-info"></i>
                        <span class="text-nowrap acceso">INICIO</span>
                    </a>
                </li>

                <?php foreach ($modulos as $index => $grupo): ?>
                    <?php
                    $submodulosPermitidos = array_filter($grupo['submodulos'], function ($sub) {
                        return puedeVerModulo($sub['id']);
                    });
                    if (empty($submodulosPermitidos))
                        continue;

                    $grupoActivo = false;
                    foreach ($submodulosPermitidos as $sub) {
                        if ($sub['active']) {
                            $grupoActivo = true;
                            break;
                        }
                    }
                    ?>

                    <li class="nav-item dropdown modulo-item" data-titulo="<?= htmlspecialchars($grupo['titulo']) ?>">
                        <a class="nav-link dropdown-toggle no-arrow d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-dark <?= $grupoActivo ? 'active-nav-item fw-bold' : '' ?>"
                            href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="<?= $grupo['icono'] ?> fs-5 icono <?= $grupo['color'] ?? 'text-info' ?>"></i>
                            <span class="text-nowrap acceso"><?= $grupo['titulo'] ?></span>
                            <i class="bi bi-chevron-down ms-1 icono" style="font-size: 0.7rem;"></i>
                        </a>

                        <ul class="dropdown-menu shadow-lg border p-2 rounded-3">
                            <?php foreach ($submodulosPermitidos as $m): ?>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 rounded-2 py-2 px-3 text-dark <?= $m['active'] ? 'active shadow-sm' : '' ?>"
                                        href="<?= $m['url'] ?>">
                                        <i class="<?= $m['icon'] ?> fs-6 <?= $m['class'] ?? $grupo['color'] ?? '' ?>"></i>
                                        <span><?= $m['label'] ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endforeach; ?>

                <!-- BOTÓN "MÁS" -->
                <li class="nav-item dropdown d-none" id="dropdownMasModulos">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-dark bg-body-secondary bg-opacity-25"
                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots fs-5 text-warning"></i>
                        <span>Más</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-start shadow-lg border p-2 rounded-3"
                        id="contenedorMasModulos" style="min-width: 240px; max-height: 80vh; overflow-y: auto;">
                    </ul>
                </li>
            </ul>
        </div>

        <!-- CONTROLES DERECHOS -->
        <div class="d-flex align-items-center gap-2 gap-md-3">

            <div class="d-flex align-items-center rounded-pill p-1 shadow-sm
                bg-white bg-opacity-25 border border-white border-opacity-50
                bg-md-white bg-opacity-md-25
                text-body-emphasis">

                <!-- SOPORTE -->
                <a href="https://wa.me/525523789029?text=Hola,%20necesito%20ayuda%20o%20reportar%20un%20problema%20con%20el%20sistema."
                    target="_blank"
                    class="btn btn-sm btn-link d-flex align-items-center gap-2 px-3 py-1 rounded-pill shadow-none border-0 bg-transparent text-success fw-semibold">
                    <i class="bi bi-whatsapp fs-6"></i>
                    <span class="small d-none d-lg-inline">Soporte / Ayuda</span>
                </a>

                <div class="vr opacity-50 mx-1"></div>

                <!-- TEMA -->
                <button type="button"
                    class="btn btn-sm btn-link text-decoration-none d-flex align-items-center gap-2 px-3 py-1 rounded-pill shadow-none border-0 bg-transparent text-body-emphasis"
                    id="btnThemeToggle" onclick="alternarModoOscuro()">
                    <i class="bi bi-moon-stars-fill fs-6" id="themeIcon"></i>
                    <span class="small d-none d-lg-inline" id="themeLabel">Tema</span>
                </button>

                <div class="vr opacity-50 mx-1"></div>

                <!-- NOTIFICACIONES -->
                <div class="dropdown">
                    <!-- data-bs-auto-close="outside": el menú no se cierra al hacer clic dentro (necesario para los checks) -->
                    <button
                        class="btn btn-sm btn-link text-decoration-none position-relative px-3 py-1 rounded-pill shadow-none border-0 bg-transparent text-body-emphasis"
                        type="button" id="btnNotif" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                        aria-expanded="false">
                        <i class="bi bi-bell fs-6"></i>
                        <span id="notif-badge"
                            class="position-absolute top-25 start-75 translate-middle badge rounded-pill bg-danger d-none"
                            style="font-size: 0.6rem;">0</span>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow-lg mt-2 rounded-4 p-0 overflow-hidden"
                        id="menuNotif" style="width: 340px; max-width: 90vw; max-height: 480px; overflow-y: auto;">

                        <!-- Encabezado con engranaje -->
                        <li
                            class="p-3 border-bottom bg-body-tertiary d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-body small">
                                <i class="bi bi-bell-fill text-primary me-2"></i>Notificaciones
                            </h6>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill"
                                    style="font-size: 0.7rem;">Alertas</span>
                                <button type="button" id="btnConfigNotif"
                                    class="btn btn-sm btn-light rounded-circle border shadow-sm p-0 d-flex align-items-center justify-content-center"
                                    style="width:28px;height:28px;" title="Configurar notificaciones">
                                    <i class="bi bi-gear-fill small"></i>
                                </button>
                            </div>
                        </li>

                        <!-- Vista: lista de notificaciones -->
                        <li id="lista-notificaciones">
                            <div class="p-3 text-center text-body-secondary small">Sin notificaciones</div>
                        </li>

                        <!-- Vista: configuración (se llena por JS) -->
                        <li id="panelConfigNotif" class="d-none p-3">
                            <div class="fw-bold small mb-2 text-body">
                                <i class="bi bi-sliders me-1"></i> Elige qué notificaciones recibir
                            </div>

                            <label
                                class="notif-config-item d-flex align-items-center gap-2 w-100 border-bottom pb-2 mb-2">
                                <input type="checkbox" class="form-check-input m-0" id="chkNotifTodas">
                                <span class="small fw-semibold">Seleccionar todas</span>
                            </label>

                            <div id="listaChecksNotif" class="d-flex flex-column gap-1"></div>

                            <div class="d-flex justify-content-between gap-2 mt-3">
                                <button type="button" id="btnCancelarConfigNotif"
                                    class="btn btn-sm btn-outline-secondary flex-fill">
                                    <i class="bi bi-x-lg"></i> Cancelar
                                </button>
                                <button type="button" id="btnGuardarConfigNotif"
                                    class="btn btn-sm btn-success flex-fill">
                                    <i class="bi bi-save-fill"></i> Guardar
                                </button>
                            </div>
                            <div class="text-body-secondary mt-2" style="font-size: 0.68rem;">
                                Se guarda solo en este equipo y navegador.
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="vr opacity-50 mx-1"></div>

                <!-- USUARIO -->
                <div class="dropdown">
                    <button
                        class="btn user-dropdown-btn d-flex align-items-center gap-2 p-1 pe-3 rounded-pill shadow-sm border-0 bg-transparent text-body-emphasis"
                        type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">

                        <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0 bg-primary text-white"
                            style="width:34px;height:34px;">
                            <i class="bi bi-person-fill"></i>
                        </div>

                        <div class="text-center flex-grow-1 d-none d-md-block me-1">
                            <span class="d-block lh-1 small fw-semibold text-body-emphasis text-truncate"
                                style="max-width: 120px;">
                                <?= htmlspecialchars($_SESSION['nombre'] ?? 'Usuario') ?>
                            </span>
                        </div>

                        <i class="bi bi-chevron-down flex-shrink-0 text-body-emphasis"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow-lg mt-2 rounded-3" style="min-width: 200px;">
                        <li class="px-3 py-2 border-bottom">
                            <p class="mb-0 small text-body-secondary">Sesión activa</p>
                            <p class="mb-0 fw-bold text-body-emphasis text-truncate" style="max-width: 170px;">
                                <?= htmlspecialchars($_SESSION['nombre'] ?? 'Usuario') ?>
                            </p>
                        </li>
                        <li>
                            <a class="dropdown-item text-danger d-flex align-items-center gap-2 py-2 mt-1 rounded-2"
                                href="/myvet/logout.php">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Cerrar Sesión</span>
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
            <!-- FIN CONTENEDOR DERECHO -->

        </div>

    </div>
</nav>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<div class="modal fade" id="modalConfirmarCancelacion" tabindex="-1" aria-labelledby="modalConfirmarCancelacionLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarCancelacionLabel">Confirmar Cancelación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="modalTextoDetalle" class="fw-semibold mb-2"></p>
                <p id="modalTextoSub" class="text-muted small"></p>
                <div id="wrapperMotivoSinPago" class="d-none mt-3">
                    <label class="form-label small fw-bold">Motivo de la cancelación:</label>
                    <input type="text" id="inputMotivoSinPago" class="form-field form-control"
                        placeholder="Escriba el motivo...">
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i> Regresar
                </button>
                <div id="contenedorBotonesAccion" class="d-flex gap-2"></div>
            </div>
        </div>
    </div>
</div>

<script>
    const usuario = <?= intval($_SESSION['rol'] ?? 0) ?>;
    const esAdminNotif = usuario <= 2;
    const TOAST_DURATION = 6000; // ms (antes 500, desaparecía casi al instante)

    /* ==========================================================================
       UTILIDADES
       ========================================================================== */
    function esc(valor) {
        return String(valor ?? '').replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));
    }

    function mostrarToast({ texto, style = {}, className = '', onClick = null, close = true }) {
        if (typeof Toastify !== 'function') return;
        Toastify({
            text: texto,
            duration: TOAST_DURATION,
            close: close,
            gravity: 'top',
            position: 'right',
            stopOnFocus: true,
            className: className,
            style: Object.assign({ borderRadius: '14px', padding: '16px 20px' }, style),
            onClick: onClick || function () { }
        }).showToast();
    }

    /* ==========================================================================
       PREFERENCIAS DE NOTIFICACIONES (localStorage)
       ========================================================================== */
    const NOTIF_STORAGE_KEY = 'myvet_notif_prefs_<?= htmlspecialchars((string) $idUsuarioPrefs, ENT_QUOTES) ?>';

    // Catálogo de tipos de notificación.
    // almacen / toast = llaves internas de los objetos de más abajo
    const NOTIF_TIPOS = [
        { id: 'traspasos', label: 'Traspasos recibidos', icon: 'bi-box-seam-fill', color: 'text-success', soloAdmin: false, almacen: 'traspasos', toast: 'traspaso' },
        { id: 'cancelacionesRecientes', label: 'Avisos de ventas eliminadas', icon: 'bi-trash3-fill', color: 'text-danger', soloAdmin: false, almacen: null, toast: null },
        { id: 'cancelaciones', label: 'Solicitudes de cancelación', icon: 'bi-x-octagon-fill', color: 'text-danger', soloAdmin: true, almacen: 'cancelaciones', toast: 'cancelacion' },
        { id: 'mantenimientos', label: 'Próximos mantenimientos', icon: 'bi-tools', color: 'text-warning', soloAdmin: true, almacen: 'mantenimientos', toast: 'mantenimiento' },
        { id: 'verificaciones', label: 'Próximas verificaciones', icon: 'bi-clipboard-check-fill', color: 'text-primary', soloAdmin: true, almacen: 'verificaciones', toast: 'verificacion' }
    ];

    const tiposVisibles = () => NOTIF_TIPOS.filter(t => !t.soloAdmin || esAdminNotif);

    function cargarPreferenciasNotif() {
        const defaults = {};
        NOTIF_TIPOS.forEach(t => defaults[t.id] = true);
        try {
            const guardado = JSON.parse(localStorage.getItem(NOTIF_STORAGE_KEY) || '{}');
            return Object.assign(defaults, guardado);
        } catch (e) {
            console.warn('No se pudieron leer las preferencias de notificaciones:', e);
            return defaults;
        }
    }

    let prefsNotif = cargarPreferenciasNotif();

    function notifActiva(tipo) {
        return prefsNotif[tipo] !== false;
    }

    /* ==========================================================================
       ALMACÉN GLOBAL Y ESTADO DE TOASTS
       ========================================================================== */
    const almacenNotificaciones = {
        mantenimientos: [],
        cancelaciones: [],
        traspasos: [],
        verificaciones: []
    };

    const estadoToasts = {
        verificacion: { primeraCarga: true, ultimoConteo: 0 },
        mantenimiento: { primeraCarga: true, ultimoConteo: 0 },
        cancelacion: { primeraCarga: true, ultimoConteo: 0 },
        traspaso: { primeraCarga: true, ultimoConteo: 0 }
    };

    // Datos de solicitudes pendientes (evita pasar textos con comillas por onclick)
    const cancelacionesPendientes = {};
    let cancelacionActual = { idVenta: 0, id: 0, razon: '' };

    /* ==========================================================================
       RENDERIZADOR CENTRALIZADO
       ========================================================================== */
    function renderizarListaNotificaciones() {
        const lista = document.getElementById('lista-notificaciones');
        const badge = document.getElementById('notif-badge');

        const todosLosHTML = [
            ...(notifActiva('traspasos') ? almacenNotificaciones.traspasos : []),
            ...(notifActiva('cancelaciones') ? almacenNotificaciones.cancelaciones : []),
            ...(notifActiva('mantenimientos') ? almacenNotificaciones.mantenimientos : []),
            ...(notifActiva('verificaciones') ? almacenNotificaciones.verificaciones : [])
        ];

        const total = todosLosHTML.length;

        if (badge) {
            if (total > 0) {
                badge.innerText = total;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        }

        if (lista) {
            if (total === 0) {
                const hayActivas = tiposVisibles().some(t => notifActiva(t.id));
                lista.innerHTML = hayActivas
                    ? '<div class="p-4 text-center text-body-secondary small">Sin notificaciones pendientes</div>'
                    : '<div class="p-4 text-center text-body-secondary small"><i class="bi bi-bell-slash fs-4 d-block mb-1"></i>Tienes todas las notificaciones desactivadas</div>';
            } else {
                lista.innerHTML = todosLosHTML.join('');
            }
        }
    }

    /* ==========================================================================
       PANEL DE CONFIGURACIÓN DE NOTIFICACIONES
       ========================================================================== */
    function construirPanelConfigNotif() {
        const cont = document.getElementById('listaChecksNotif');
        if (!cont) return;

        cont.innerHTML = tiposVisibles().map(t => `
            <label class="notif-config-item d-flex align-items-center gap-2 w-100">
                <input type="checkbox" class="form-check-input m-0 chk-notif-tipo" id="chkNotif_${t.id}" data-tipo="${t.id}">
                <i class="bi ${t.icon} ${t.color}"></i>
                <span class="small">${esc(t.label)}</span>
            </label>
        `).join('');

        cont.querySelectorAll('.chk-notif-tipo').forEach(chk => {
            chk.addEventListener('change', sincronizarCheckTodas);
        });

        const chkTodas = document.getElementById('chkNotifTodas');
        if (chkTodas) {
            chkTodas.addEventListener('change', () => {
                cont.querySelectorAll('.chk-notif-tipo').forEach(c => c.checked = chkTodas.checked);
                chkTodas.indeterminate = false;
            });
        }
    }

    function sincronizarCheckTodas() {
        const checks = Array.from(document.querySelectorAll('.chk-notif-tipo'));
        const chkTodas = document.getElementById('chkNotifTodas');
        if (!chkTodas || checks.length === 0) return;
        const marcados = checks.filter(c => c.checked).length;
        chkTodas.checked = marcados === checks.length;
        chkTodas.indeterminate = marcados > 0 && marcados < checks.length;
    }

    function cargarChecksDesdePrefs() {
        document.querySelectorAll('.chk-notif-tipo').forEach(chk => {
            chk.checked = notifActiva(chk.dataset.tipo);
        });
        sincronizarCheckTodas();
    }

    function mostrarPanelConfig(mostrar) {
        const panel = document.getElementById('panelConfigNotif');
        const lista = document.getElementById('lista-notificaciones');
        if (!panel || !lista) return;

        if (mostrar) {
            cargarChecksDesdePrefs();
            panel.classList.remove('d-none');
            lista.classList.add('d-none');
        } else {
            panel.classList.add('d-none');
            lista.classList.remove('d-none');
        }
    }

    function guardarPreferenciasNotif() {
        const anteriores = Object.assign({}, prefsNotif);
        const nuevas = Object.assign({}, prefsNotif);

        document.querySelectorAll('.chk-notif-tipo').forEach(chk => {
            nuevas[chk.dataset.tipo] = chk.checked;
        });

        try {
            localStorage.setItem(NOTIF_STORAGE_KEY, JSON.stringify(nuevas));
        } catch (e) {
            console.error('Error guardando preferencias:', e);
            mostrarToast({
                texto: '❌ No se pudieron guardar las preferencias en este equipo',
                style: { background: '#dc3545', color: '#fff' }
            });
            return;
        }

        prefsNotif = nuevas;

        NOTIF_TIPOS.forEach(t => {
            const antes = anteriores[t.id] !== false;
            const ahora = prefsNotif[t.id] !== false;

            // Se desactivó: limpiar lo acumulado
            if (antes && !ahora && t.almacen) {
                almacenNotificaciones[t.almacen] = [];
            }
            // Se activó: que vuelva a avisar con toast en la siguiente consulta
            if (!antes && ahora && t.toast) {
                estadoToasts[t.toast].primeraCarga = true;
                estadoToasts[t.toast].ultimoConteo = 0;
            }
        });

        renderizarListaNotificaciones();
        mostrarPanelConfig(false);

        mostrarToast({
            texto: '✅ Preferencias de notificaciones guardadas',
            style: { background: '#198754', color: '#fff', fontWeight: '600' }
        });

        // Refrescar de inmediato lo que se acaba de activar
        mantenimientoSistema();
    }

    /* ==========================================================================
       CONSULTAS AL BACKEND (cada una respeta las preferencias)
       ========================================================================== */
    function mantenimientoSistema() {
        if (notifActiva('cancelacionesRecientes')) verificarCancelacionesRecientes();
        if (notifActiva('traspasos')) verificarNotificaciones();

        if (esAdminNotif) {
            if (notifActiva('mantenimientos')) verificarMantenimientos();
            if (notifActiva('cancelaciones')) verificarSolicitudesCancelacion();
            if (notifActiva('verificaciones')) verificarVerificaciones();
        }
    }

    /* ---------- Cancelaciones recientes (aviso tipo toast, 5 min) ---------- */
    const idsCancelacionesNotificadas = new Set();

    function verificarCancelacionesRecientes() {
        fetch('/myvet/app/controllers/ventasHistorialController.php?action=obtenerCancelacionesRecientes')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success' && Array.isArray(res.data) && res.data.length > 0) {
                    res.data.forEach(item => {
                        if (!idsCancelacionesNotificadas.has(item.id)) {
                            idsCancelacionesNotificadas.add(item.id);

                            mostrarToast({
                                texto: `🗑️ VENTA ELIMINADA\nSe canceló la venta #${item.id_venta}\nMotivo: ${item.razon || 'Sin motivo especificado'}`,
                                style: {
                                    background: '#dc3545', color: '#ffffff', borderRadius: '12px',
                                    fontWeight: '600', padding: '14px 18px',
                                    boxShadow: '0 4px 12px rgba(0,0,0,0.15)'
                                }
                            });

                            if (typeof getVentas === 'function') getVentas();
                        }
                    });
                }
            })
            .catch(err => console.error('Error cancelaciones recientes:', err));
    }

    /* ---------- Solicitudes de cancelación pendientes ---------- */
    function verificarSolicitudesCancelacion() {
        fetch('/myvet/app/controllers/ventasHistorialController.php?action=obtenerSolicitudesPendientes')
            .then(res => res.json())
            .then(res => {
                if (!notifActiva('cancelaciones')) return; // pudo desactivarse mientras llegaba la respuesta

                const items = res.data || res.items || [];
                const cantidad = items.length;
                const estado = estadoToasts.cancelacion;

                if (cantidad > 0 && (estado.primeraCarga || cantidad > estado.ultimoConteo)) {
                    const u = items[0] || {};
                    mostrarToast({
                        texto: `⚠️ CANCELACIÓN SOLICITADA\nVenta #${u.id_venta || ''}\nMotivo: ${u.razon || 'Sin motivo'}`,
                        className: 'toast-cancelacion toast-cancel-close',
                        style: { background: '#ffffff', color: '#000' },
                        onClick: () => window.location.href = '/myvet/ventashistorial'
                    });
                    estado.primeraCarga = false;
                }
                estado.ultimoConteo = cantidad;

                // Guardar los datos para usarlos por id (sin pasar texto por onclick)
                Object.keys(cancelacionesPendientes).forEach(k => delete cancelacionesPendientes[k]);
                items.forEach(item => cancelacionesPendientes[item.id] = item);

                almacenNotificaciones.cancelaciones = items.map(item => `
                <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-body-tertiary hover-notif">
                    <div style="flex: 1; line-height: 1.4;">
                        <b class="text-danger d-block small text-uppercase">Venta #${esc(item.id_venta)}</b>
                        <b class="d-block text-body-secondary" style="font-size: 0.75rem;">Motivo: ${esc(item.razon || 'Sin especificación')}</b>
                        <div class="mt-1"><small class="text-body-tertiary" style="font-size: 0.70rem;">Por: ${esc(item.usuario_nombre || 'Usuario')}</small></div>
                    </div>
                    <div class="d-flex gap-1 ms-2">
                        <button onclick="aceptarCancelacionPorId(${Number(item.id)})" class="btn btn-success btn-sm rounded-circle shadow-sm" style="width:32px; height:32px;" title="Aceptar"><i class="bi bi-check-lg"></i></button>
                        <button onclick="procesarEliminarCancelacion(${Number(item.id)})" class="btn btn-danger btn-sm rounded-circle shadow-sm" style="width:32px; height:32px;" title="Eliminar"><i class="bi bi-trash"></i></button>
                    </div>
                </div>`);

                renderizarListaNotificaciones();
            })
            .catch(err => console.error('Error cancelaciones:', err));
    }

    /* ---------- Traspasos ---------- */
    function verificarNotificaciones() {
        fetch('/myvet/app/backend/movimientos/get_notificaciones_traspaso.php?t=' + Date.now())
            .then(res => res.json())
            .then(data => {
                if (!notifActiva('traspasos')) return;

                const items = data.items || [];
                const cantidad = parseInt(data.cantidad) || 0;
                const estado = estadoToasts.traspaso;

                if (cantidad > 0 && (estado.primeraCarga || cantidad > estado.ultimoConteo)) {
                    const u = items[0] || {};
                    mostrarToast({
                        texto: `📦 TRASPASO RECIBIDO\n${u.emisor} envió ${u.cantidad_texto || u.cantidad} de ${u.producto}`,
                        className: 'toast-traspaso toast-red-close',
                        style: { background: '#ffffff', color: '#000' },
                        onClick: () => window.location.href = '/myvet/almacenes'
                    });
                    estado.primeraCarga = false;
                }
                estado.ultimoConteo = cantidad;

                almacenNotificaciones.traspasos = items.map(item => `
                <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-body hover-notif">
                    <div style="flex: 1; line-height: 1.4;">
                        <b class="d-block small text-uppercase text-success">${esc(item.producto)}</b>
                        <b class="d-block text-success" style="font-size: 0.75rem;">De: ${esc(item.emisor)}</b>
                        <div class="mt-1"><b class="d-block small text-uppercase text-success">${esc(item.cantidad_texto || (item.cantidad + ' PZA'))}</b></div>
                    </div>
                    <button onclick="procesarRecepcionRapida(${Number(item.id)})" class="btn btn-success btn-sm rounded-circle shadow-sm" style="width:32px; height:32px;"><i class="bi bi-check-lg"></i></button>
                </div>`);

                renderizarListaNotificaciones();
            })
            .catch(err => console.error('Error traspasos:', err));
    }

    /* ---------- Mantenimientos ---------- */
    function verificarMantenimientos() {
        fetch('/myvet/app/controllers/mantenimientosController.php?action=listarProximoMantenimiento')
            .then(r => r.json())
            .then(data => {
                if (!notifActiva('mantenimientos')) return;

                const items = Array.isArray(data) ? data : [];
                const cantidad = items.length;
                const estado = estadoToasts.mantenimiento;

                if (cantidad > 0 && (estado.primeraCarga || cantidad > estado.ultimoConteo)) {
                    const item = items[0];
                    mostrarToast({
                        texto: `🚗 PRÓXIMO MANTENIMIENTO\n${item.estado}\n\n${item.vehiculo} ${item.placas}`,
                        style: { background: '#ffffff', color: '#111', borderLeft: '5px solid #ffc107', borderRadius: '15px', padding: '18px' },
                        onClick: () => window.location.href = '/myvet/mantenimientos'
                    });
                    estado.primeraCarga = false;
                }
                estado.ultimoConteo = cantidad;

                almacenNotificaciones.mantenimientos = items.map(item => {
                    const dias = parseInt(item.dias_restantes);
                    const color = dias <= 0 ? 'danger' : (dias <= 3 ? 'warning' : 'success');
                    return `
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom hover-notif">
                        <div>
                            <div class="small"><b>${esc(item.estado)}</b></div>
                            <div class="fw-bold text-${color}">Vehículo: ${esc(item.vehiculo)}</div>
                            <div class="small text-secondary">${esc(item.placas)}</div>
                        </div>
                        <button class="btn btn-outline-primary btn-sm rounded-circle" onclick="window.location='/myvet/mantenimientos'">
                            <i class="bi bi-arrow-right text-danger"></i>
                        </button>
                    </div>`;
                });

                renderizarListaNotificaciones();
            })
            .catch(err => console.error('Error mantenimientos:', err));
    }

    /* ---------- Verificaciones (ahora SÍ entran a la lista unificada) ---------- */
    function verificarVerificaciones() {
        fetch('/myvet/app/controllers/verificacionesController.php?action=listarProximaVerificacion')
            .then(r => r.json())
            .then(data => {
                if (!notifActiva('verificaciones')) return;

                const items = Array.isArray(data) ? data : [];
                const cantidad = items.length;
                const estado = estadoToasts.verificacion;

                const badgeVerif = document.getElementById('badge-verificaciones');
                if (badgeVerif) {
                    badgeVerif.innerText = cantidad;
                    badgeVerif.classList.toggle('d-none', cantidad === 0);
                }

                if (cantidad > 0 && (estado.primeraCarga || cantidad > estado.ultimoConteo)) {
                    const item = items[0];
                    mostrarToast({
                        texto: `📋 PRÓXIMA VERIFICACIÓN\n${item.estado}\n\n${item.vehiculo} - ${item.placas}`,
                        style: { background: '#fff', color: '#111', borderLeft: '5px solid #0d6efd', borderRadius: '15px', padding: '18px' },
                        onClick: () => window.location.href = '/myvet/verificaciones'
                    });
                    estado.primeraCarga = false;
                }
                estado.ultimoConteo = cantidad;

                const construirItem = item => {
                    const dias = parseInt(item.dias_restantes);
                    const color = dias <= 0 ? 'danger' : (dias <= 3 ? 'warning' : 'success');
                    return `
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom hover-notif">
                        <div>
                            <div class="small fw-bold">${esc(item.estado)}</div>
                            <div class="fw-bold text-${color}">Vehículo: ${esc(item.vehiculo)}</div>
                            <div class="small text-secondary">Placas: ${esc(item.placas)}</div>
                        </div>
                        <button class="btn btn-outline-primary btn-sm rounded-circle" onclick="window.location='/myvet/verificaciones'">
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>`;
                };

                almacenNotificaciones.verificaciones = items.map(construirItem);

                // Compatibilidad: si alguna pantalla tiene su propio contenedor, también se llena
                const listaVerif = document.getElementById('lista-verificaciones');
                if (listaVerif) {
                    listaVerif.innerHTML = cantidad === 0
                        ? '<div class="p-4 text-center text-body-secondary">No hay verificaciones próximas.</div>'
                        : items.map(construirItem).join('');
                }

                renderizarListaNotificaciones();
            })
            .catch(err => console.error('Error verificaciones:', err));
    }

    /* ---------- Ventas eliminadas globales (opcional, usa la misma preferencia) ---------- */
    let ultimoIdVentaEliminadaNotificada = 0;

    function verificarVentasEliminadasGlobales() {
        if (!notifActiva('cancelacionesRecientes')) return;

        fetch('/myvet/app/controllers/ventasHistorialController.php?action=obtenerUltimaVentaEliminada')
            .then(res => res.json())
            .then(data => {
                if (data && data.id_venta && data.id_venta !== ultimoIdVentaEliminadaNotificada) {
                    mostrarToast({
                        texto: `🚨 ATENCIÓN GLOBAL\nSe ha cancelado/eliminado la Venta #${data.folio || data.id_venta}`,
                        style: { background: '#dc3545', color: '#ffffff', borderRadius: '12px', fontWeight: '600', padding: '16px' }
                    });
                    ultimoIdVentaEliminadaNotificada = data.id_venta;
                    if (typeof getVentas === 'function') getVentas();
                }
            })
            .catch(err => console.error('Error al consultar ventas eliminadas:', err));
    }

    /* ==========================================================================
       HANDLERS Y ESTILOS AUXILIARES
       ========================================================================== */
    function inyectarEstilosToast() {
        if (!document.getElementById('style-toast-custom')) {
            const style = document.createElement('style');
            style.id = 'style-toast-custom';
            style.innerHTML = `
            .toast-close { opacity: 1 !important; font-weight: bold; font-size: 20px; margin-left: 10px; }
            .toast-red-close .toast-close { color: #ff0000 !important; }
            .toast-cancel-close .toast-close { color: #dc3545 !important; }
        `;
            document.head.appendChild(style);
        }
    }

    // Wrapper que toma los datos del almacén por id
    function aceptarCancelacionPorId(id) {
        const item = cancelacionesPendientes[id];
        if (!item) return;
        procesarAceptarCancelacion(item.idVenta, item.id, item.pagado, item.venta_total, item.folio, item.razon);
    }

    function procesarAceptarCancelacion(idVenta, id, pagado, total, folio, razon) {
        const montoPagado = parseFloat(pagado) || 0;
        const modalElem = document.getElementById('modalConfirmarCancelacion');
        const modalBs = bootstrap.Modal.getOrCreateInstance(modalElem);

        const textoDetalle = document.getElementById('modalTextoDetalle');
        const textoSub = document.getElementById('modalTextoSub');
        const wrapperMotivo = document.getElementById('wrapperMotivoSinPago');
        const contenedorBotones = document.getElementById('contenedorBotonesAccion');

        cancelacionActual = { idVenta: idVenta, id: id, razon: razon || '' };

        textoDetalle.textContent = `¿Aceptar y Cancelar Venta ${folio || '#' + idVenta}?`;

        if (montoPagado > 0) {
            textoSub.textContent = `Motivo reportado: "${razon}". Selecciona si deseas reintegrar el dinero al saldo del cliente o solo anular la venta.`;
            wrapperMotivo.classList.add('d-none');

            contenedorBotones.innerHTML = `
            <button class="btn btn-danger" onclick="ejecutarCancelacionEncadenada(cancelacionActual.idVenta, cancelacionActual.id, false, cancelacionActual.razon)">
                <i class="bi bi-x-circle"></i> Sin Saldo
            </button>
            <button class="btn btn-success" onclick="ejecutarCancelacionEncadenada(cancelacionActual.idVenta, cancelacionActual.id, true, cancelacionActual.razon)">
                <i class="bi bi-cash-stack"></i> Con Saldo a Favor
            </button>
        `;
        } else {
            textoSub.textContent = 'Esta venta no tiene pagos registrados. Se procederá a cancelarla sin saldo.';
            wrapperMotivo.classList.remove('d-none');
            document.getElementById('inputMotivoSinPago').value = razon || '';

            contenedorBotones.innerHTML = `
            <button class="btn btn-danger" onclick="confirmarSinPago(cancelacionActual.idVenta, cancelacionActual.id)">
                <i class="bi bi-check-lg"></i> Sí, cancelar venta
            </button>
        `;
        }

        modalBs.show();
    }

    function confirmarSinPago(idVenta, id) {
        const motivoInput = document.getElementById('inputMotivoSinPago').value.trim();
        if (!motivoInput) {
            alert('¡El motivo es obligatorio!');
            return;
        }
        ejecutarCancelacionEncadenada(idVenta, id, false, motivoInput);
    }

    async function ejecutarCancelacionEncadenada(idVenta, id, conSaldo, motivo) {
        const modalElem = document.getElementById('modalConfirmarCancelacion');
        const modalBs = bootstrap.Modal.getInstance(modalElem);
        if (modalBs) modalBs.hide();

        const esOscuro = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        const esModoOscuroObj = {
            background: esOscuro ? '#1e293b' : '#ffffff',
            color: esOscuro ? '#f8fafc' : '#1e2022'
        };

        Swal.fire({
            title: 'Procesando cancelación...',
            text: 'Por favor espere...',
            didOpen: () => Swal.showLoading(),
            allowOutsideClick: false,
            ...esModoOscuroObj
        });

        try {
            // 1. Aceptar solicitud
            const formData = new FormData();
            formData.append('id', id);

            const respSolicitud = await fetch('/myvet/app/controllers/ventasHistorialController.php?action=aceptarSolicitudCancelacion', {
                method: 'POST',
                body: formData
            });
            const dataSolicitud = await respSolicitud.json();

            if (dataSolicitud.status !== 'success' && !dataSolicitud.success) {
                Swal.fire({ icon: 'error', title: 'Error', text: dataSolicitud.message || 'Error al aceptar solicitud.', ...esModoOscuroObj });
                return;
            }

            // 2. Anular / Reintegrar
            const accion = conSaldo ? 'cancelarVenta' : 'cancelarVentaSinSaldo';
            const urlController = typeof URL_CONTROLLER !== 'undefined' ? URL_CONTROLLER : '/myvet/app/controllers/ventasHistorialController.php';

            const respCancelacion = await fetch(`${urlController}?action=${accion}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_venta: idVenta, motivo: motivo })
            });
            const resCancelacion = await respCancelacion.json();

            if (resCancelacion.status === 'success' || resCancelacion.success) {
                await Swal.fire({
                    title: '¡Venta Cancelada!',
                    text: resCancelacion.message || 'Procesado correctamente.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                    ...esModoOscuroObj
                });

                if (typeof verificarSolicitudesCancelacion === 'function') verificarSolicitudesCancelacion();
                if (typeof getVentas === 'function') getVentas();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: resCancelacion.message, ...esModoOscuroObj });
            }

        } catch (error) {
            console.error('Error:', error);
            Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo conectar con el servidor.', ...esModoOscuroObj });
        }
    }

    async function procesarEliminarCancelacion(id) {
        const esOscuro = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        const confirm = await Swal.fire({
            title: '¿Eliminar solicitud?',
            text: 'Esta acción borrará físicamente el registro.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545',
            background: esOscuro ? '#1e293b' : '#ffffff',
            color: esOscuro ? '#f8fafc' : '#1e2022'
        });

        if (!confirm.isConfirmed) return;

        const formData = new FormData();
        formData.append('id', id);

        try {
            const response = await fetch('/myvet/app/controllers/ventasHistorialController.php?action=eliminarSolicitudCancelacion', { method: 'POST', body: formData });
            const data = await response.json();
            if (data.status === 'success') verificarSolicitudesCancelacion();
            else Swal.fire({ icon: 'error', title: 'Error', text: data.message });
        } catch (err) { console.error(err); }
    }

    window.procesarRecepcionRapida = function (id) {
        if (!confirm('¿Confirmar recepción de material?')) return;
        const formData = new FormData();
        formData.append('id', id);

        fetch('/myvet/app/controllers/traspasosController.php?action=recibirTraspaso', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success || data.status === 'success') location.reload();
                else alert('Error: ' + (data.message || 'No se pudo procesar'));
            })
            .catch(err => console.error(err));
    };
</script>

<script>
    /* ==========================================================================
       TEMA (se aplica lo antes posible para evitar parpadeo)
       ========================================================================== */
    (function () {
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
    })();

    function actualizarBotonTema(tema) {
        const icon = document.getElementById('themeIcon');
        const label = document.getElementById('themeLabel');
        if (!icon || !label) return;

        if (tema === 'dark') {
            icon.className = 'bi bi-sun-fill fs-6 text-warning';
            label.textContent = 'Modo Claro';
        } else {
            icon.className = 'bi bi-moon-stars-fill fs-6';
            label.textContent = 'Modo Oscuro';
        }
    }

    function alternarModoOscuro() {
        const html = document.documentElement;
        const nuevoTema = html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';

        html.setAttribute('data-bs-theme', nuevoTema);
        localStorage.setItem('theme', nuevoTema);

        actualizarBotonTema(nuevoTema);
        if (typeof seleccionarFondoInicio === 'function') {
            seleccionarFondoInicio();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {

        inyectarEstilosToast();
        actualizarBotonTema(document.documentElement.getAttribute('data-bs-theme'));

        /* ---------- Sidebar (solo si existe en la página) ---------- */
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');

        if (sidebar) {
            let overlay = document.querySelector('.sidebar-overlay');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.className = 'sidebar-overlay';
                document.body.appendChild(overlay);
            }

            if (window.innerWidth <= 992) {
                sidebar.classList.remove('show');
                overlay.classList.remove('active');
            } else {
                sidebar.classList.add('hidden');
                document.body.classList.add('sidebar-hidden');
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    if (window.innerWidth <= 992) {
                        sidebar.classList.toggle('show');
                        overlay.classList.toggle('active');
                    } else {
                        sidebar.classList.toggle('hidden');
                        document.body.classList.toggle('sidebar-hidden');
                    }
                });
            }
        }

        /* ---------- Panel de configuración de notificaciones ---------- */
        // Nota: ya NO hay handler manual de click en btnNotif; Bootstrap abre/cierra
        // el dropdown (antes había doble control que causaba conflictos).
        construirPanelConfigNotif();

        const btnConfig = document.getElementById('btnConfigNotif');
        const btnGuardar = document.getElementById('btnGuardarConfigNotif');
        const btnCancelar = document.getElementById('btnCancelarConfigNotif');
        const menuNotif = document.getElementById('menuNotif');

        if (btnConfig) {
            btnConfig.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const panel = document.getElementById('panelConfigNotif');
                mostrarPanelConfig(panel.classList.contains('d-none'));
            });
        }
        if (btnGuardar) btnGuardar.addEventListener('click', guardarPreferenciasNotif);
        if (btnCancelar) btnCancelar.addEventListener('click', () => mostrarPanelConfig(false));

        // Al cerrar el dropdown, volver siempre a la vista de lista
        const dropdownNotif = document.getElementById('btnNotif');
        if (dropdownNotif) {
            dropdownNotif.addEventListener('hidden.bs.dropdown', () => mostrarPanelConfig(false));
        }
        if (menuNotif) {
            menuNotif.addEventListener('click', (e) => e.stopPropagation());
        }

        renderizarListaNotificaciones();

        /* ---------- Agrupamiento dinámico del menú ("Más") ---------- */
        const mainList = document.getElementById('mainNavList');
        const masBtnContainer = document.getElementById('dropdownMasModulos');
        const masUl = document.getElementById('contenedorMasModulos');

        let itemWidths = [];
        let isMeasuring = false;

        function guardarAnchosBase() {
            const items = Array.from(document.querySelectorAll('.modulo-item'));
            itemWidths = items.map(item => item.getBoundingClientRect().width || 120);
        }

        function recalcularMenuMasFluid() {
            if (!mainList || !masBtnContainer || !masUl || isMeasuring) return;

            if (window.innerWidth < 992) {
                document.querySelectorAll('.modulo-item').forEach(el => el.classList.remove('modulo-oculto'));
                masBtnContainer.classList.add('d-none');
                masUl.innerHTML = '';
                return;
            }

            if (itemWidths.length === 0) {
                guardarAnchosBase();
            }

            isMeasuring = true;

            requestAnimationFrame(() => {
                const containerWidth = mainList.parentElement.clientWidth;
                const items = Array.from(document.querySelectorAll('.modulo-item'));

                let totalAcumulado = 0;
                let overflowIndex = -1;
                const reservaMas = 100;

                for (let i = 0; i < itemWidths.length; i++) {
                    totalAcumulado += itemWidths[i] + 4;
                    if (totalAcumulado > (containerWidth - reservaMas) && overflowIndex === -1) {
                        overflowIndex = i;
                    }
                }

                if (overflowIndex !== -1) {
                    // Retroceder 1 para garantizar al menos 2 módulos en "Más"
                    if (overflowIndex > 0) {
                        overflowIndex = overflowIndex - 1;
                    }

                    masBtnContainer.classList.remove('d-none');
                    masUl.innerHTML = '';

                    const fragment = document.createDocumentFragment();

                    items.forEach((item, index) => {
                        if (index >= overflowIndex) {
                            item.classList.add('modulo-oculto');

                            const titulo = item.getAttribute('data-titulo') || '';
                            const subitems = item.querySelectorAll('.dropdown-menu li');

                            // El enlace INICIO no tiene submenú: se agrega directo
                            if (subitems.length === 0) {
                                const link = item.querySelector('a');
                                if (link) {
                                    const li = document.createElement('li');
                                    li.innerHTML = `<a class="dropdown-item d-flex align-items-center gap-2 rounded-2 py-2 px-3" href="${link.getAttribute('href')}"><i class="bi-house-heart-fill text-info"></i><span>Inicio</span></a>`;
                                    fragment.appendChild(li);
                                }
                                return;
                            }

                            const headerLi = document.createElement('li');
                            headerLi.className = 'mas-grupo-titulo';
                            headerLi.textContent = titulo;
                            fragment.appendChild(headerLi);

                            subitems.forEach(subLi => fragment.appendChild(subLi.cloneNode(true)));

                            const dividerLi = document.createElement('li');
                            dividerLi.innerHTML = '<hr class="dropdown-divider my-1">';
                            fragment.appendChild(dividerLi);
                        } else {
                            item.classList.remove('modulo-oculto');
                        }
                    });

                    masUl.appendChild(fragment);
                } else {
                    items.forEach(item => item.classList.remove('modulo-oculto'));
                    masBtnContainer.classList.add('d-none');
                    masUl.innerHTML = '';
                }

                isMeasuring = false;
            });
        }

        const resizeObserver = new ResizeObserver(() => recalcularMenuMasFluid());
        if (mainList && mainList.parentElement) {
            resizeObserver.observe(mainList.parentElement);
        }

        // Re-medir cuando terminen de cargar fuentes/iconos (los anchos cambian)


        /* ---------- Polling de notificaciones ---------- */
        mantenimientoSistema();
        setInterval(mantenimientoSistema, 35000);
    });
</script>
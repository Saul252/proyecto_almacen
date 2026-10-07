<?php
date_default_timezone_set('America/Mexico_City');
//[
//     'id_grupo' => 'general',
//   'titulo' => 'General',
// 'icono' => 'bi-compass-fill',
//'submodulos' => [
//     ['id' => 'inicio', 'url' => '/myvet/inicio', 'icon' => 'bi-house-heart-fill', 'label' => 'Inicio', 'active' => ($archivoActual == 'inicio.php')],
//]
//],
$modulos = [

    [
        'id_grupo' => 'ventas_clientes',
        'titulo' => 'Ventas',
        'icono' => 'bi-bag-heart-fill',
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
    [
        'id_grupo' => 'pacientes_consultas',
        'titulo' => 'Atención Médica',
        'icono' => 'bi-heart-pulse-fill',
        'submodulos' => [
            [
                'id' => 'clientesPacientes',
                'url' => '/myvet/misPacientesDentales',
                'class' => 'text-primary',
                'icon' => 'bi-heart-pulse-fill',
                'label' => 'Pacientes Dentales',
                'active' => ($archivoActual == 'clientesPacientesDentales.php')
            ],
            [
                'id' => 'citasDentales',
                'url' => '/myvet/citasDentales',
                'class' => 'text-warning',
                'icon' => 'bi-calendar-check-fill',
                'label' => 'Citas Dentales',
                'active' => ($archivoActual == 'citasController.php')
            ],
            [
                'id' => 'misPacientesMedicina',
                'url' => '/myvet/misPacientesMedicina',
                'class' => 'text-info',
                'icon' => 'bi-file-medical-fill',
                'label' => 'Pacientes Medicina',
                'active' => ($archivoActual == 'clientesPacientesMedicina.php')
            ],
            [
                'id' => 'citasMedicas',
                'url' => '/myvet/citasMedicas',
                'class' => 'text-success',
                'icon' => 'bi-calendar-plus-fill',
                'label' => 'Citas Médicas',
                'active' => ($archivoActual == 'citasMedicasController.php')
            ]
        ]
    ],
    [
        'id_grupo' => 'atencion_veterinaria',
        'titulo' => 'Atención Veterinaria',
        'icono' => 'bi-hospital-fill', // Cruz de hospital / clínica veterinaria
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
        'submodulos' => [
            ['id' => 'usuarios', 'url' => '/myvet/usuarios', 'icon' => 'bi-person-lock', 'label' => 'Usuarios', 'active' => ($archivoActual == 'usuariosController.php')],
            ['id' => 'miAcceso', 'url' => '/myvet/miAcceso', 'icon' => 'bi-person', 'label' => 'MI Cuenta', 'active' => ($archivoActual == 'miAccesoController.php')],
            ['id' => 'Configuracion', 'url' => '/myvet/configuracionSistema', 'icon' => 'bi-person', 'label' => 'configuracion', 'active' => ($archivoActual == 'configuracionController.php')],
        ]
    ]
];
?>


<style>
    /* Aplica el color personalizado SOLO cuando NO se está en modo oscuro */
    :root:not([data-bs-theme="dark"]),
    [data-bs-theme="light"] {
        <?php if (!empty($_SESSION['sidebar_bg'])): ?>
            --navbar-dark:
                <?= $_SESSION['sidebar_bg'] ?>
                !important;
        <?php endif; ?>
    }

    /* Si existe --sidebar-bg-user (la de sesión) la usa, si no, mantiene la predeterminada */
</style>
<style>
    /* Espaciado para evitar superposición con la Navbar */

    /* Fondo degradado Premium */
    .navbar-premium {
        background: var(--navbar-dark) !important;
    }

    /* Elemento activo */
    .navbar-premium .active-nav-item {
        background: var(--navbar-dark) !important;
        border-radius: 8px;
    }

    /* Hover en escritorios */
    @media (min-width: 992px) {
        .navbar .dropdown:hover>.dropdown-menu {
            display: block;

        }
    }

    /* Estilos adaptativos para móvil */
    @media (max-width: 991.98px) {
        .navbar-collapse {
            background: var(--navbar-dark) !important;
            padding: 1rem;
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

        .navbar-nav .dropdown-item {
            color: #000000 !important;
        }

        .navbar-nav .dropdown-item:hover,
        .navbar-nav .dropdown-item:focus {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
        }
    }

    /* Manejo eficiente de elementos ocultos en escritorio */
    .modulo-item.modulo-oculto {
        display: none !important;
    }

    /* Estilos de encabezado para módulos agrupados en "Más" */
    #contenedorMasModulos .mas-grupo-titulo {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--bs-secondary-color, #94a3b8);
        padding: 0.4rem 0.8rem 0.2rem 0.8rem;
        margin-top: 0.2rem;
    }

    /* Puente invisible para evitar que el hover se rompa por la distancia (mt-2) */
    .dropdown-menu::before {
        content: "";
        position: absolute;
        top: -12px;
        /* Cubre el espacio del mt-2 */
        left: 0;
        right: 0;
        height: 12px;
        background: transparent;
    }

    /* Ancho fijo mínimo para que el menú siempre mantenga un área de interacción amplia */
    .dropdown-menu-end {
        right: 0 !important;
        left: auto !important;
        transform: none !important;
        /* Invalida desplazamientos automáticos incompletos */
    }
</style>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

<nav class="navbar fixed-top navbar-expand-lg navbar-dark navbar-premium shadow-sm">
    <div class="container-fluid px-2 px-md-4">

        <!-- BRAND / LOGO Y BOTÓN MÓVIL -->
        <div class="d-flex align-items-center gap-2 me-lg-3">
            <button class="navbar-toggler  shadow-none p-1 ms-2" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarMainContent" aria-controls="navbarMainContent" aria-expanded="false"
                aria-label="Abrir Menú">
                <i class="bi bi-list fs-2 icono"></i>
            </button>
        </div>

        <!-- CONTENIDO COLAPSABLE DE MÓDULOS -->
        <div class="collapse navbar-collapse" id="navbarMainContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-1 d-flex flex-wrap flex-lg-nowrap" id="mainNavList">
                <li class="nav-item  modulo-item" data-titulo="">
                    <a class="nav-link  d-flex align-items-center gap-2 px-3 py-2 rounded-3  <?= $grupoActivo ? 'active-nav-item fw-bold' : '' ?>"
                        href="/myvet/inicio">
                        <i class="bi-house-heart-fill fs-5 icono"></i>
                        <span class="text-nowrap acceso">INICIO</span>
                    </a>

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
                        <a class="nav-link dropdown-toggle no-arrow d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-white <?= $grupoActivo ? 'active-nav-item fw-bold' : '' ?>"
                            href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="<?= $grupo['icono'] ?> fs-5 icono"></i>
                            <span class="text-nowrap acceso"><?= $grupo['titulo'] ?></span>

                            <!-- Flechita discreta -->
                            <i class="bi bi-chevron-down ms-1 icono"
                                style="font-size: 0.7rem; transition: transform 0.2s ease;"></i>
                        </a>

                        <ul class="dropdown-menu shadow-lg border p-2 rounded-3">
                            <?php foreach ($submodulosPermitidos as $m): ?>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 rounded-2 py-2 px-3 <?= $m['active'] ? 'active shadow-sm' : '' ?>"
                                        href="<?= $m['url'] ?>">
                                        <i class="<?= $m['icon'] ?> fs-6"></i>
                                        <span><?= $m['label'] ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endforeach; ?>

                <!-- BOTÓN / CONTENEDOR PARA MÓDULOS AGRUPADOS (MÁS) -->
                <li class="nav-item dropdown d-none" id="dropdownMasModulos">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-white bg-white bg-opacity-10"
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

            <!-- MODO OSCURO -->
            <!-- CONTENEDOR FLOTANTE UNIFICADO -->
            <div class="d-flex align-items-center  bg-opacity-10 border border-opacity-10 rounded-pill p-1 shadow-sm">
                <a href="https://wa.me/525523789029?text=Hola,%20necesito%20ayuda%20o%20reportar%20un%20problema%20con%20el%20sistema."
                    target="_blank"
                    class="btn btn-sm btn-link text-success d-flex align-items-center gap-2 px-3 py-1 rounded-pill transition-all ">
                    <i class="bi bi-phone fs-6"></i>
                    <span class="small d-none d-lg-inline">Soporte / Ayuda</span>
                </a>
                <div class="vr  opacity-25 mx-1" style="height: 18px; width: 1px;"></div>

                <!-- BOTÓN TEMA -->
                <button type="button"
                    class="btn btn-sm btn-link  text-decoration-none d-flex align-items-center gap-2 px-3 py-1 rounded-pill transition-all"
                    id="btnThemeToggle" onclick="alternarModoOscuro()">
                    <i class="bi bi-moon-stars-fill fs-6" id="themeIcon"></i>
                    <span class="small d-none d-lg-inline" id="themeLabel">Tema</span>
                </button>


                <!-- SEPARADOR SUTIL -->
                <div class="vr  opacity-25 mx-1" style="height: 18px; width: 1px;"></div>
                <!-- NOTIFICACIONES -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-link  text-decoration-none position-relative px-3 py-1 rounded-pill"
                        type="button" id="btnNotif" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell fs-6"></i>
                        <span id="notif-badge"
                            class="position-absolute top-25 start-75 translate-middle badge rounded-pill bg-danger d-none"
                            style="font-size: 0.6rem;">0</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg  mt-2 rounded-4 p-0 overflow-hidden"
                        id="menuNotif" style="width: 320px; max-width: 90vw; max-height: 400px; overflow-y: auto;">
                        <li
                            class="p-3 border-bottom bg-body-tertiary d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-body small"><i
                                    class="bi bi-bell-fill text-primary me-2"></i>Traspasos Pendientes</h6>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill"
                                style="font-size: 0.7rem;">Alertas</span>
                        </li>
                        <div id="lista-notificaciones">
                            <li class="p-3 text-center text-body-secondary small">Cargando...</li>
                        </div>
                    </ul>
                </div>

                <div class="vr  opacity-25 mx-1" style="height: 18px; width: 1px;"></div>
                <div class="dropdown">
                    <button
                        class="btn user-dropdown-btn d-flex align-items-center gap-2 p-1 pe-3 rounded-pill shadow-sm"
                        type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">

                        <!-- Avatar (flex-shrink-0 evita que se aplaste) -->
                        <div
                            class=" user-avatar-circle d-flex align-items-center justify-content-center rounded-circle flex-shrink-0">
                            <i class="bi bi-person-fill"></i>
                        </div>

                        <!-- Nombre de usuario CENTRADO (flex-grow-1 toma el espacio libre y text-center lo alinea) -->
                        <div class="text-center flex-grow-1 d-none d-md-block me-1">
                            <span class="d-block lh-1 small fw-semibold user-name-text text-truncate"
                                style="max-width: 120px;">
                                <?= htmlspecialchars($_SESSION['nombre'] ?? 'Usuario') ?>
                            </span>
                        </div>

                        <!-- Flecha (flex-shrink-0) -->
                        <i class="bi bi-chevron-down user-chevron  flex-shrink-0"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow-lg mt-2 rounded-3" style="min-width: 200px;">
                        <li class="px-3 py-2 border-bottom">
                            <p class="mb-0 small text-muted">Sesión activa</p>
                            <p class="mb-0 fw-bold text-truncate" style="max-width: 170px;">
                                <?= $_SESSION['nombre'] ?? 'Usuario' ?>
                            </p>
                        </li>
                        <li>
                            <a class="dropdown-item text-danger d-flex align-items-center gap-2 py-2 mt-1"
                                href="/myvet/logout.php">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Cerrar Sesión</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- MENÚ DESPLEGABLE DE USUARIO -->

        </div>

    </div>
</nav>

<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<?php require_once __DIR__ . '/notificaciones.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        if (typeof inyectarEstilosToast === 'function') {
            inyectarEstilosToast();
        }

        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const btnNotif = document.getElementById('btnNotif');
        const menuNotif = document.getElementById('menuNotif');

        let overlay = document.querySelector('.sidebar-overlay') || document.createElement('div');
        if (!overlay.parentElement) {
            overlay.className = 'sidebar-overlay';
            document.body.appendChild(overlay);
        }

        if (sidebar) {
            if (window.innerWidth <= 992) {
                sidebar.classList.remove('show');
                if (overlay) overlay.classList.remove('active');
            } else {
                sidebar.classList.add('hidden');
                document.body.classList.add('sidebar-hidden');
            }
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (window.innerWidth <= 992) {
                    sidebar.classList.toggle('show');
                    if (overlay) overlay.classList.toggle('active');
                } else {
                    sidebar.classList.toggle('hidden');
                    document.body.classList.toggle('sidebar-hidden');
                }
            });
        }

        if (btnNotif && menuNotif) {
            btnNotif.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                menuNotif.style.display = (menuNotif.style.display === 'block') ? 'none' : 'block';
            });
        }

        document.addEventListener('click', (e) => {
            if (menuNotif && btnNotif && !menuNotif.contains(e.target) && !btnNotif.contains(e.target)) {
                menuNotif.style.display = 'none';
            }
        });

        // --- AGRUPAMIENTO DINÁMICO (MÍNIMO 2 MÓDULOS EN "MÁS") ---
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

            // En vista móvil (< 992px) desactivar agrupamiento
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
                    // AJUSTE CLAVE: Retroceder 1 índice para garantizar al menos 2 módulos en "Más"
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

                            const headerLi = document.createElement('li');
                            headerLi.className = 'mas-grupo-titulo';
                            headerLi.textContent = titulo;
                            fragment.appendChild(headerLi);

                            subitems.forEach(subLi => {
                                fragment.appendChild(subLi.cloneNode(true));
                            });

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

        const resizeObserver = new ResizeObserver(() => {
            recalcularMenuMasFluid();
        });

        if (mainList && mainList.parentElement) {
            resizeObserver.observe(mainList.parentElement);
        }

        // Polling de Notificaciones
        if (typeof mantenimientoSistema === 'function') {
            mantenimientoSistema();
            setInterval(mantenimientoSistema, 35000);
        }
    });

    function actualizarBotonTema(tema) {
        const icon = document.getElementById('themeIcon');
        const label = document.getElementById('themeLabel');
        if (!icon || !label) return;

        if (tema === 'dark') {
            icon.className = 'bi bi-sun-fill text-warning';
            label.textContent = 'Modo Claro';
        } else {
            icon.className = 'bi bi-moon-stars-fill';
            label.textContent = 'Modo Oscuro';
        }
    }

    function alternarModoOscuro() {
        const html = document.documentElement;
        const nuevoTema = html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';

        html.setAttribute('data-bs-theme', nuevoTema);
        localStorage.setItem('theme', nuevoTema);

        if (typeof actualizarBotonTema === 'function') {
            actualizarBotonTema(nuevoTema);
        }
    }

    (function () {
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
    })();
</script>
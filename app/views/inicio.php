<?php
require_once __DIR__ . '/../../includes/auth.php';
protegerPagina();
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../../includes/permisos.php';

$paginaActual = 'Inicio';
$archivoActual = basename($_SERVER['PHP_SELF']);

// Estructura de módulos por categoría
$gruposModulos = [
    [
        'id_grupo' => 'ventas_clientes',
        'titulo' => 'Ventas y Clientes',
        'icono' => 'bi-bag-heart-fill',
        'theme' => 'emerald',
        'submodulos' => [
            ['id' => 'ventas', 'url' => '/myvet/ventas', 'icon' => 'bi-cash-coin', 'class' => 'text-success', 'label' => 'Ventas'],
            ['id' => 'remisiones', 'url' => '/myvet/remisiones', 'icon' => 'bi-file-earmark-diff-fill', 'class' => 'text-success', 'label' => 'Remisiones'],
            ['id' => 'cajaRapida', 'url' => '/myvet/cajaRapida', 'icon' => 'bi-reception-4', 'class' => 'text-warning', 'label' => 'Caja Rápida'],
            ['id' => 'cotizaciones', 'url' => '/myvet/cotizaciones', 'icon' => 'bi-file-earmark-spreadsheet-fill', 'class' => 'text-info', 'label' => 'Cotizaciones'],
            ['id' => 'clientes', 'url' => '/myvet/clientes', 'icon' => 'bi-person-rolodex', 'class' => 'text-primary', 'label' => 'Clientes'],
            ['id' => 'clientesEstatus', 'url' => '/myvet/clientesEstatus', 'icon' => 'bi-person-bounding-box', 'class' => 'text-success', 'label' => 'Estatus Clientes'],
            ['id' => 'ventasVendedor', 'url' => '/myvet/ventasVendedor', 'icon' => 'bi-award-fill', 'class' => 'text-primary', 'label' => 'Ventas Vendedor'],
            ['id' => 'ventashistorial', 'url' => '/myvet/ventashistorial', 'icon' => 'bi-journal-text', 'class' => 'text-secondary', 'label' => 'Historial Ventas'],
            ['id' => 'comprobantes', 'url' => '/myvet/comprobantes', 'icon' => 'bi-patch-check-fill', 'class' => 'text-info', 'label' => 'Comprobantes'],
            ['id' => 'registrarPagos', 'url' => '/myvet/registrarPagos', 'icon' => 'bi-wallet-fill', 'class' => 'text-success', 'label' => 'Registrar Pagos'],
            ['id' => 'historialPagos', 'url' => '/myvet/historialPagos', 'icon' => 'bi-coin', 'class' => 'text-danger', 'label' => 'Historial Pagos'],

        ]
    ],
    [
        'id_grupo' => 'compras_proveedores',
        'titulo' => 'Compras y Proveedores',
        'icono' => 'bi-cart-dash-fill',
        'theme' => 'sky',
        'submodulos' => [
            ['id' => 'compras', 'url' => '/myvet/compras', 'icon' => 'bi-credit-card-fill', 'class' => 'text-danger', 'label' => 'Compras y Gastos'],
            ['id' => 'proveedores', 'url' => '/myvet/proveedores', 'icon' => 'bi-building-up', 'class' => 'text-dark', 'label' => 'Proveedores'],
            ['id' => 'solicitudesCompra', 'url' => '/myvet/solicitudesCompra', 'icon' => 'bi-file-earmark-plus-fill', 'class' => 'text-info', 'label' => 'Solicitudes Compra'],
        ]
    ],
    [
        'id_grupo' => 'inventario_almacen',
        'titulo' => 'Inventario y Almacén',
        'icono' => 'bi-archive-fill',
        'theme' => 'amber',
        'submodulos' => [
            ['id' => 'almacenes', 'url' => '/myvet/almacenes', 'icon' => 'bi-building-fill', 'class' => 'text-warning', 'label' => 'Almacén'],
            ['id' => 'movimientos', 'url' => '/myvet/movimientos', 'icon' => 'bi-shuffle', 'class' => 'text-primary', 'label' => 'Movimientos'],
            ['id' => 'Mermas', 'url' => '/myvet/Mermas', 'icon' => 'bi-trash3-fill', 'class' => 'text-danger', 'label' => 'Mermas'],
            ['id' => 'transmutaciones', 'url' => '/myvet/transmutaciones', 'icon' => 'bi-signpost-split-fill', 'class' => 'text-secondary', 'label' => 'Conversiones'],
            ['id' => 'historialLotes', 'url' => '/myvet/historialLotes', 'icon' => 'bi-upc-scan', 'class' => 'text-body-secondary', 'label' => 'Historial Lotes'],
            ['id' => 'comprasHistorial', 'url' => '/myvet/comprasHistorial', 'icon' => 'bi-bookshelf', 'class' => 'text-dark', 'label' => 'Historial Compras'],
        ]
    ],
    [
        'id_grupo' => 'finanzas_tesoreria',
        'titulo' => 'Finanzas y Tesorería',
        'icono' => 'bi-coin',
        'theme' => 'indigo',
        'submodulos' => [
            ['id' => 'finanzas', 'url' => '/myvet/finanzas', 'icon' => 'bi-activity', 'class' => 'text-primary', 'label' => 'Finanzas'],
            ['id' => 'finanzas_admin', 'url' => '/myvet/finanzas_admin', 'icon' => 'bi-kanban-fill', 'class' => 'text-dark', 'label' => 'Finanzas Admin'],
            ['id' => 'corteCaja', 'url' => '/myvet/corteCaja', 'icon' => 'bi-receipt', 'class' => 'text-success', 'label' => 'Corte de Caja'],
            ['id' => 'tesoreria', 'url' => '/myvet/tesoreria', 'icon' => 'bi-bank', 'class' => 'text-secondary', 'label' => 'Tesorería'],
        ]
    ],
    [
        'id_grupo' => 'atencion_dental',
        'titulo' => 'Atención Dental',
        'icono' => 'bi-emoji-smile-fill',
        'theme' => 'sky',
        'submodulos' => [
            ['id' => 'clientesPacientes', 'url' => '/myvet/misPacientesDental', 'class' => 'text-primary', 'icon' => 'bi-heart-pulse-fill', 'label' => 'Mis Pacientes Dentales'],
            ['id' => 'citasDentales', 'url' => '/myvet/citasDental', 'class' => 'text-warning', 'icon' => 'bi-calendar-check-fill', 'label' => 'Citas Dentales'],
            ['id' => 'consultaDental', 'url' => '/myvet/consultaDental', 'class' => 'text-dark', 'icon' => 'bi-emoji-smile-fill', 'label' => 'Consulta Dental'],
        ]
    ],
    [
        'id_grupo' => 'atencion_medica',
        'titulo' => 'Atención Médica',
        'icono' => 'bi-heart-pulse-fill',
        'theme' => 'mint',
        'submodulos' => [
            ['id' => 'misPacientesMedicina', 'url' => '/myvet/misPacientesMedicina', 'class' => 'text-info', 'icon' => 'bi-file-medical-fill', 'label' => 'Mis Pacientes Medicina'],
            ['id' => 'citasMedicas', 'url' => '/myvet/citasMedicas', 'class' => 'text-success', 'icon' => 'bi-calendar-plus-fill', 'label' => 'Citas Médicas'],
            ['id' => 'consultaMedica', 'url' => '/myvet/consultaMedica', 'class' => 'text-dark', 'icon' => 'bi-clipboard2-pulse-fill', 'label' => 'Consulta Médica'],
        ]
    ],
    [
        'id_grupo' => 'atencion_veterinaria',
        'titulo' => 'Atención Veterinaria',
        'icono' => 'bi-hospital-fill', // Cruz de hospital / clínica veterinaria
        'theme' => 'teal',
        'submodulos' => [
            ['id' => 'pacientes', 'url' => '/myvet/pacientes', 'icon' => 'bi-paw-fill', 'class' => 'text-primary', 'label' => 'Mascotas / Pacientes'],
            ['id' => 'expedientes', 'url' => '/myvet/expedientes', 'icon' => 'bi-folder2-open', 'class' => 'text-info', 'label' => 'Expedientes Médicos'],
            ['id' => 'consultas', 'url' => '/myvet/consultas', 'icon' => 'bi-journal-medical', 'class' => 'text-danger', 'label' => 'Consultas Médicas'],
        ]
    ],
    [
        'id_grupo' => 'logistica_distribucion',
        'titulo' => 'Logística y Distribución',
        'icono' => 'bi-pin-map-fill',
        'theme' => 'slate',
        'submodulos' => [
            ['id' => 'entregas', 'url' => '/myvet/entregas', 'icon' => 'bi-send-check-fill', 'class' => 'text-warning', 'label' => 'Despachos'],
            ['id' => 'vehiculos', 'url' => '/myvet/vehiculos', 'icon' => 'bi-car-front-fill', 'class' => 'text-secondary', 'label' => 'Vehículos'],
            ['id' => 'verificaciones', 'url' => '/myvet/verificaciones', 'icon' => 'bi-check-all', 'class' => 'text-success', 'label' => 'Verificaciones'],
            ['id' => 'mantenimientos', 'url' => '/myvet/mantenimientos', 'icon' => 'bi-tools', 'class' => 'text-danger', 'label' => 'Mantenimientos'],
            ['id' => 'repartos', 'url' => '/myvet/repartos', 'icon' => 'bi-compass-fill', 'class' => 'text-info', 'label' => 'Repartos'],
            ['id' => 'misRepartos', 'url' => '/myvet/misRepartos', 'icon' => 'bi-sign-turn-right-fill', 'class' => 'text-primary', 'label' => 'Mis Repartos'],
            ['id' => 'viajesTrabajadores', 'url' => '/myvet/viajesTrabajadores', 'icon' => 'bi-pass-fill', 'class' => 'text-primary', 'label' => 'Viajes Personal'],
        ]
    ],
    [
        'id_grupo' => 'recursos_humanos',
        'titulo' => 'Recursos Humanos',
        'icono' => 'bi-person-vcard',
        'theme' => 'rose',
        'submodulos' => [
            ['id' => 'trabajadores', 'url' => '/myvet/trabajadores', 'icon' => 'bi-person-badge', 'class' => 'text-primary', 'label' => 'Trabajadores'],
            ['id' => 'nomina', 'url' => '/myvet/nomina', 'icon' => 'bi-currency-dollar', 'class' => 'text-success', 'label' => 'Nómina'],
            ['id' => 'prestamos', 'url' => '/myvet/prestamos', 'icon' => 'bi-piggy-bank-fill', 'class' => 'text-warning', 'label' => 'Préstamos'],
            ['id' => 'faltas', 'url' => '/myvet/faltas', 'icon' => 'bi-person-x-fill', 'class' => 'text-danger', 'label' => 'Faltas'],
            ['id' => 'pagos_viajes', 'url' => '/myvet/pagos_viajes', 'icon' => 'bi-credit-card', 'class' => 'text-success', 'label' => 'Pagos Viajes'],
            ['id' => 'vacaciones', 'url' => '/myvet/vacaciones', 'icon' => 'bi-umbrella-fill', 'class' => 'text-dark', 'label' => 'Vacaciones'],
        ]
    ],
    [
        'id_grupo' => 'administracion',
        'titulo' => 'Administración',
        'icono' => 'bi-shield-lock-fill',
        'theme' => 'red',
        'submodulos' => [
            ['id' => 'usuarios', 'url' => '/myvet/usuarios', 'icon' => 'bi-people-fill', 'class' => 'text-danger', 'label' => 'Usuarios'],
            ['id' => 'miAcceso', 'url' => '/myvet/miAcceso', 'icon' => 'bi-person-circle', 'class' => 'text-success', 'label' => 'Mi Cuenta'],
            ['id' => 'Configuracion', 'url' => '/myvet/configuracionSistema', 'icon' => 'bi-gear-fill', 'class' => 'text-success', 'label' => 'Configuración'],
        ]
    ]
];
?><!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Bienvenido - Sistema JSEA</title>

    <!-- Script Anti-Parpadeo de Tema -->
    <script>
        (function () {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/myvet/css/inicio.css" rel="stylesheet">

    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) {
        cargarEstilos();
    } ?>


</head>

<body>

    <?php if (function_exists('renderizarLayout')) {
        renderizarLayout($paginaActual);
    } ?>

    <main class="main-content">

        <!-- SECCIÓN SUPERIOR: HERO HUB -->
        <div class="row g-3 mb-4">

            <!-- BANNER PRINCIPAL BIENVENIDA + RELOJ -->
            <!-- BANNER CON RELOJ CENTRAL HERO -->
            <div class="col-12">
                <div class="hero-welcome">

                    <!-- HEADER SUPERIOR (Distribuido) -->
                    <div class="hero-header d-flex align-items-center justify-content-between">
                        <div class="badge-live">
                            <span class="badge-live-dot"></span>
                            Sistema JSEA Activo
                        </div>
                        <div class="system-status-pill d-none d-sm-flex align-items-center gap-2">
                            <i class="bi bi-shield-check text-emerald"></i>
                            <span>En línea</span>
                        </div>
                    </div>

                    <!-- CENTRO: SALUDO Y RELOJ PRINCIPAL -->
                    <div class="hero-body text-center my-3">
                        <h1 class="fw-bold mb-1 hero-title" id="saludoTexto">¡Bienvenido al Sistema!</h1>
                        <p class="hero-subtitle mb-4">Centro de mando y control centralizado</p>

                        <!-- RELOJ CENTRAL CON EFECTO GLOW -->
                        <div class="clock-card-wrapper d-inline-block">
                            <div class="clock-display-main">
                                <span id="relojHora">00:00</span><span class="clock-seconds"
                                    id="relojSegundos">:00</span>
                            </div>
                        </div>

                        <!-- FECHA CENTRAL -->
                        <div class="date-display-center mt-3" id="relojFecha">
                            Cargando fecha...
                        </div>
                    </div>

                    <!-- FOOTER INFERIOR (Detalle estético) -->
                    <div
                        class="hero-footer d-flex align-items-center justify-content-center gap-3 opacity-75 pt-2 border-top border-white border-opacity-10">
                        <span class="small d-flex align-items-center gap-1">
                            <i class="bi bi-clock-history"></i> Tiempo Real (UTC-6)
                        </span>
                        <span class="opacity-25">•</span>
                        <span class="small d-flex align-items-center gap-1">
                            <i class="bi bi-cpu"></i> Servidor Normal
                        </span>
                    </div>

                </div>
            </div>

            <!-- TARJETA TUTORIALES -->
            <div class="col-12 col-md-6 ">
                <a href="tutoriales.php" class="top-action-card card-tutorials">
                    <div class="icon-box-sm">
                        <i class="bi bi-play-btn-fill"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold fs-6 mb-1">Videos Tutoriales</h3>
                        <p class="small opacity-80 mb-0">Aprende a usar los módulos paso a paso.</p>
                    </div>
                </a>
            </div>

            <!-- TARJETA SOPORTE TÉCNICO -->
            <div class="col-12 col-md-6 ">
                <a href="https://wa.me/5215523789029" target="_blank" class="top-action-card">
                    <div class="icon-box-sm bg-success bg-opacity-10 text-success">
                        <i class="bi bi-headset"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold fs-6 mb-1">Soporte Técnico</h3>
                        <p class="small text-body-secondary mb-0">Atención directa del equipo JSEA.</p>
                    </div>
                </a>
            </div>

        </div>

        <!-- TÍTULO APLICACIONES -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="fw-bold m-0" style="font-size: 1.2rem; color: var(--text-primary);">
                <i class="bi bi-grid-fill text-primary me-2"></i>Aplicaciones del Sistema
            </h2>
            <span class="badge bg-secondary bg-opacity-10 text-body-secondary rounded-pill px-3 py-1 small">Acceso
                directo</span>
        </div>

        <!-- GRID BENTO BOX DE APLICACIONES -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">

            <?php foreach ($gruposModulos as $index => $grupo): ?>

                <?php
                $submodulosPermitidos = array_filter($grupo['submodulos'], function ($m) {
                    return puedeVerModulo($m['id']);
                });

                if (empty($submodulosPermitidos))
                    continue;

                $estaAbierto = ($index < 2);
                $idCollapse = "desplegable_" . $grupo['id_grupo'];
                $tema = $grupo['theme'] ?? 'indigo';
                ?>

                <div class="col">
                    <div class="bento-card theme-<?= $tema ?>">

                        <div class="bento-header <?= $estaAbierto ? '' : 'collapsed' ?>" data-bs-toggle="collapse"
                            data-bs-target="#<?= $idCollapse ?>" aria-expanded="<?= $estaAbierto ? 'true' : 'false' ?>"
                            aria-controls="<?= $idCollapse ?>">

                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-square">
                                    <i class="bi <?= $grupo['icono'] ?>"></i>
                                </div>
                                <div>
                                    <h3 class="bento-title"><?= $grupo['titulo'] ?></h3>
                                    <span class="bento-badge mt-1 d-inline-block">
                                        <?= count($submodulosPermitidos) ?> accesos
                                    </span>
                                </div>
                            </div>

                            <div class="toggle-circle">
                                <i class="bi bi-chevron-down"></i>
                            </div>
                        </div>

                        <div id="<?= $idCollapse ?>" class="collapse <?= $estaAbierto ? 'show' : '' ?>">
                            <div class="bento-body">
                                <div
                                    class="row row-cols-1 row-cols-sm-2 g-2 pt-2 border-top border-secondary border-opacity-10">
                                    <?php foreach ($submodulosPermitidos as $m): ?>
                                        <div class="col">
                                            <a href="<?= $m['url'] ?>" class="chip-module">
                                                <i class="bi <?= $m['icon'] ?> <?= $m['class'] ?? '' ?>"></i>
                                                <span class="text-truncate"><?= $m['label'] ?></span>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            <?php endforeach; ?>

        </div>

    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SCRIPT DE RELOJ Y SALUDO ANIMADO -->
    <script>
        function actualizarReloj() {
            const ahora = new Date();
            const horas = String(ahora.getHours()).padStart(2, '0');
            const minutos = String(ahora.getMinutes()).padStart(2, '0');
            const segundos = String(ahora.getSeconds()).padStart(2, '0');

            document.getElementById('relojHora').textContent = `${horas}:${minutos}`;
            document.getElementById('relojSegundos').textContent = `:${segundos}`;

            const opcionesFecha = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('relojFecha').textContent = ahora.toLocaleDateString('es-ES', opcionesFecha);

            const saludoEl = document.getElementById('saludoTexto');
            const horaNum = ahora.getHours();

            if (horaNum >= 6 && horaNum < 12) {
                saludoEl.textContent = "¡Buenos días! Bienvenido a JSEA";
            } else if (horaNum >= 12 && horaNum < 19) {
                saludoEl.textContent = "¡Buenas tardes! Bienvenido a JSEA";
            } else {
                saludoEl.textContent = "¡Buenas noches! Bienvenido a JSEA";
            }
        }

        actualizarReloj();
        setInterval(actualizarReloj, 1000);
    </script>
    <script>
        seleccionarFondoInicio();
        function seleccionarFondoInicio() {
            const html = document.documentElement;
            const body = document.body;

            if (!body) return;

            const tema = html.getAttribute('data-bs-theme') || 'light';
            const hora = new Date().getHours();

            let periodo;

            if (hora >= 6 && hora < 12) {
                periodo = 'morning';
            } else if (hora >= 12 && hora < 14) {
                periodo = 'noon';
            } else {
                periodo = 'morning';
            }

            const nombreVariable = `--bg-${tema}-${periodo}`;
            const fondo = getComputedStyle(html)
                .getPropertyValue(nombreVariable)
                .trim();

            if (!fondo) {
                console.error('No existe la variable CSS:', nombreVariable);
                return;
            }

            html.style.setProperty('--bg-gradient', fondo);

            body.style.setProperty('background', fondo, 'important');
            body.style.setProperty('background-size', '600% 600%');
        }

        // Ejecutar automáticamente al iniciar
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', seleccionarFondoInicio, {
                once: true
            });
        } else {
            seleccionarFondoInicio();
        }

    </script>
</body>

</html>
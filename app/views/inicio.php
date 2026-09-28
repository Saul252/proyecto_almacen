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
        'id_grupo' => 'atencion_medica',
        'titulo' => 'Atención Medica',
        'icono' => 'bi-heart-pulse-fill',
        'theme' => 'sky',
        'submodulos' => [

            ['id' => 'clientesPacientes', 'url' => '/myvet/misPacientesDental', 'class' => 'text-primary', 'icon' => 'bi-heart-pulse-fill', 'label' => 'Mis Pacientes Dentales'],
            ['id' => 'citasDentales', 'url' => '/myvet/citasDental', 'class' => 'text-warning', 'icon' => 'bi-calendar-check-fill', 'label' => 'Citas dentales'],
            ['id' => 'misPacientesMedicina', 'url' => '/myvet/misPacientesMedicina', 'class' => 'text-info', 'icon' => 'bi-file-medical-fill', 'label' => 'Mis Pacientes Medicina'],
            ['id' => 'citasMedicas', 'url' => '/myvet/citasMedicas', 'class' => 'text-success', 'icon' => 'bi-calendar-plus-fill', 'label' => 'Citas Medicas']
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
            ['id' => 'usuarios', 'url' => '/myvet/usuarios', 'icon' => 'bi-person-lock', 'class' => 'text-danger', 'label' => 'Usuarios'],
            ['id' => 'miAcceso', 'url' => '/myvet/miAcceso', 'icon' => 'bi-person', 'class' => 'text-success', 'label' => 'MI Cuenta'],

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

    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) {
        cargarEstilos();
    } ?>

    <style>
        :root {
            --navbar-height: 70px;
            --radius-main: 20px;
            --dock-radius: 20px;
            --transition-spring: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            --transition-bounce: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);

            /* Tema Claro */
            --bg-app: #f1f5f9;
            --card-bg: #ffffff;
            --card-glass: rgba(255, 255, 255, 0.85);
            --card-border: rgba(226, 232, 240, 0.8);
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --shadow-soft: 0 10px 25px rgba(0, 0, 0, 0.04);

            /* Banner Clock */
            --clock-bg: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            --clock-text: #ffffff;

            /* Píldoras / Chips */
            --pill-bg: #f8fafc;
            --pill-hover-bg: #4361ee;
            --pill-hover-text: #ffffff;
            --pill-border: rgba(226, 232, 240, 0.9);
        }

        [data-bs-theme="dark"] {
            /* Tema Oscuro */
            --bg-app: #080c14;
            --card-bg: #111827;
            --card-glass: rgba(17, 24, 39, 0.75);
            --card-border: rgba(255, 255, 255, 0.07);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --shadow-soft: 0 20px 40px -15px rgba(0, 0, 0, 0.5);

            --clock-bg: linear-gradient(135deg, #0f172a 0%, #030712 100%);
            --clock-text: #ffffff;

            --pill-bg: rgba(255, 255, 255, 0.03);
            --pill-hover-bg: #4361ee;
            --pill-hover-text: #ffffff;
            --pill-border: rgba(255, 255, 255, 0.08);
        }

        /* PALETA DE ACENTOS Y TEMAS DE CATEGORÍA */
        .theme-emerald {
            --accent-color: #10b981;
            --accent-bg: rgba(16, 185, 129, 0.12);
        }

        .theme-sky {
            --accent-color: #0284c7;
            --accent-bg: rgba(2, 132, 199, 0.12);
        }

        .theme-amber {
            --accent-color: #f59e0b;
            --accent-bg: rgba(245, 158, 11, 0.12);
        }

        .theme-indigo {
            --accent-color: #6366f1;
            --accent-bg: rgba(99, 102, 241, 0.12);
        }

        .theme-slate {
            --accent-color: #64748b;
            --accent-bg: rgba(100, 116, 139, 0.12);
        }

        .theme-rose {
            --accent-color: #f43f5e;
            --accent-bg: rgba(244, 63, 94, 0.12);
        }

        .theme-red {
            --accent-color: #ef4444;
            --accent-bg: rgba(239, 68, 68, 0.12);
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-app) !important;
            transition: background 0.3s ease;
        }

        .main-content {
            padding: 24px;
            padding-top: calc(var(--navbar-height) + 15px);

            margin: 0 auto;
        }

        /* BANNER DE BIENVENIDA Y RELOJ */
        .hero-welcome {
            background: var(--clock-bg);
            border: 1px solid var(--card-border);
            border-radius: var(--radius-main);
            padding: 24px 28px;
            color: var(--clock-text);
            box-shadow: var(--shadow-soft);
            position: relative;
            overflow: hidden;
        }

        .hero-welcome::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.25) 0%, rgba(0, 0, 0, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .clock-display {
            font-size: 2.3rem;
            font-weight: 800;
            letter-spacing: -1px;
            line-height: 1;
            font-variant-numeric: tabular-nums;
            background: linear-gradient(180deg, #ffffff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .clock-seconds {
            font-size: 1.05rem;
            opacity: 0.7;
            margin-left: 2px;
            animation: pulseSeconds 1s infinite;
        }

        @keyframes pulseSeconds {

            0%,
            100% {
                opacity: 0.3;
            }

            50% {
                opacity: 1;
            }
        }

        .date-display {
            font-size: 0.82rem;
            text-transform: capitalize;
            opacity: 0.85;
            font-weight: 500;
        }

        /* TARJETAS SUPERIORES */
        .top-action-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: var(--radius-main);
            padding: 20px 22px;
            box-shadow: var(--shadow-soft);
            transition: var(--transition-spring);
            display: flex;
            align-items: center;
            gap: 16px;
            text-decoration: none;
            color: var(--text-primary);
            height: 100%;
        }

        .top-action-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 30px rgba(67, 97, 238, 0.12);
            border-color: #4361ee;
            color: var(--text-primary);
        }

        .top-action-card.card-tutorials {
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            color: #ffffff !important;

        }

        .top-action-card.card-tutorials:hover {
            box-shadow: 0 15px 30px rgba(67, 97, 238, 0.35);
        }

        .icon-box-sm {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
            transition: var(--transition-spring);
        }

        .card-tutorials .icon-box-sm {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        .top-action-card:hover .icon-box-sm {
            transform: scale(1.1) rotate(-5deg);
        }

        .badge-live {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .badge-live-dot {
            width: 6px;
            height: 6px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 8px #10b981;
        }

        /* TARJETAS BENTO BOX */
        .bento-card {
            background: var(--card-glass);
            backdrop-filter: blur(12px);
            border: 1px solid var(--card-border);
            border-radius: var(--dock-radius);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            transition: var(--transition-bounce);
        }

        .bento-card:hover {
            transform: translateY(-4px);
        }

        .bento-header {
            padding: 18px 20px;
            cursor: pointer;
            user-select: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .icon-square {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: var(--accent-bg);
            color: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            transition: var(--transition-bounce);
        }

        .bento-card:hover .icon-square {
            transform: rotate(-6deg) scale(1.1);
        }

        .bento-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        .bento-badge {
            font-size: 0.7rem;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 30px;
            background: var(--accent-bg);
            color: var(--accent-color);
        }

        .toggle-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--pill-bg);
            border: 1px solid var(--pill-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            color: var(--text-secondary);
            transition: transform 0.3s ease, background 0.3s ease;
        }

        .bento-header:not(.collapsed) .toggle-circle {
            transform: rotate(180deg);
            background: var(--accent-color);
            color: #ffffff;
            border-color: var(--accent-color);
        }

        .bento-body {
            padding: 0 18px 18px 18px;
        }

        /* PÍLDORA CHIP DE MÓDULO */
        .chip-module {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 14px;
            background: var(--pill-bg);
            border: 1px solid var(--pill-border);
            color: var(--text-primary);
            text-decoration: none;
            font-size: 0.83rem;
            font-weight: 600;
            transition: all 0.25s ease;
        }

        .chip-module i {
            font-size: 1.1rem;
            color: var(--accent-color);
            transition: transform 0.25s ease, color 0.25s ease;
        }

        .chip-module:hover {
            background: var(--pill-hover-bg);
            color: var(--pill-hover-text);
            border-color: var(--pill-hover-bg);
            box-shadow: 0 8px 20px -6px rgba(67, 97, 238, 0.4);
            transform: translateY(-2px);
        }

        .chip-module:hover i {
            color: #ffffff;
            transform: scale(1.15);
        }

        /* BANNER DE BIENVENIDA CENTRADO Y GLASSMORPHISM */
        .hero-welcome {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            padding: 28px 32px;
            color: #ffffff;
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.4);
            position: relative;
            overflow: hidden;
        }

        /* Destellos Neón de fondo */
        .hero-welcome::before {
            content: '';
            position: absolute;
            top: -50%;
            left: 50%;
            transform: translateX(-50%);
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.22) 0%, rgba(0, 0, 0, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        [data-bs-theme="dark"] .hero-welcome {
            background: linear-gradient(135deg, #090d16 0%, #111827 50%, #090d16 100%);
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
        }

        .hero-title {
            font-size: 1.6rem;
            letter-spacing: -0.5px;
            background: linear-gradient(180deg, #ffffff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 0.9rem;
            color: #94a3b8;
        }

        /* TARJETA Y FUENTE DEL RELOJ CENTRAL */
        .clock-card-wrapper {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            padding: 12px 36px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.15);
        }

        .clock-display-main {
            font-size: 3.2rem;
            font-weight: 800;
            letter-spacing: -1.5px;
            line-height: 1;
            font-variant-numeric: tabular-nums;
            background: linear-gradient(180deg, #ffffff 30%, #a5b4fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 30px rgba(99, 102, 241, 0.3);
        }

        .clock-seconds {
            font-size: 1.3rem;
            color: #818cf8;
            -webkit-text-fill-color: #818cf8;
            margin-left: 2px;
            animation: pulseSeconds 1s infinite;
        }

        .date-display-center {
            font-size: 0.92rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            color: #cbd5e1;
            text-transform: capitalize;
        }

        /* BADGES Y DETALLES */
        .badge-live {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .system-status-pill {
            background: rgba(255, 255, 255, 0.06);
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .text-emerald {
            color: #34d399 !important;
        }

        @keyframes pulseSeconds {

            0%,
            100% {
                opacity: 0.3;
            }

            50% {
                opacity: 1;
            }
        }

        @media (max-width: 576px) {
            .hero-welcome {
                padding: 20px 16px;
            }

            .clock-display-main {
                font-size: 2.3rem;
            }

            .clock-card-wrapper {
                padding: 10px 20px;
            }

            .hero-title {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 16px;
            }

            .hero-welcome {
                padding: 20px;
            }

            .clock-display {
                font-size: 1.9rem;
            }

            .chip-module {
                font-size: 0.78rem;
                padding: 8px 10px;
            }
        }
    </style>
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
</body>

</html>
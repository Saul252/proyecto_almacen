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
            /* =========================================
   TEMA CLARO: MAÑANA
   Cielo azul, sol dorado y nubes luminosas
========================================= */
            --bg-light-morning:
                /* Sol matutino brillante */
                radial-gradient(circle 280px at 82% 14%,
                    rgba(255, 245, 200, 0.98) 0%,
                    rgba(255, 215, 130, 0.65) 28%,
                    rgba(255, 190, 110, 0.22) 55%,
                    transparent 75%),

                /* Resplandor del amanecer extendido */
                radial-gradient(ellipse 70% 40% at 80% 8%,
                    rgba(255, 230, 190, 0.55) 0%,
                    rgba(255, 210, 170, 0.2) 55%,
                    transparent 80%),

                /* Nubes blancas suaves */
                radial-gradient(ellipse 52% 30% at 22% 20%,
                    rgba(255, 255, 255, 0.88) 0%,
                    rgba(255, 250, 240, 0.5) 55%,
                    transparent 78%),
                radial-gradient(ellipse 46% 26% at 62% 13%,
                    rgba(255, 250, 235, 0.75) 0%,
                    transparent 75%),

                /* Colinas cálidas */
                radial-gradient(ellipse 82% 50% at 12% 100%,
                    rgba(180, 90, 60, 0.38) 0%,
                    rgba(215, 135, 90, 0.2) 45%,
                    transparent 78%),
                radial-gradient(ellipse 80% 50% at 95% 100%,
                    rgba(120, 80, 60, 0.32) 0%,
                    rgba(175, 120, 85, 0.18) 48%,
                    transparent 78%),

                /* Hexágonos elegantes */
                conic-gradient(from 30deg at 50% 50%,
                    transparent 0deg 59deg,
                    rgba(255, 255, 255, 0.35) 59deg 61deg,
                    transparent 61deg 119deg,
                    rgba(255, 255, 255, 0.35) 119deg 121deg,
                    transparent 121deg 179deg,
                    rgba(255, 255, 255, 0.35) 179deg 181deg,
                    transparent 181deg 239deg,
                    rgba(255, 255, 255, 0.35) 239deg 241deg,
                    transparent 241deg 299deg,
                    rgba(255, 255, 255, 0.35) 299deg 301deg,
                    transparent 301deg 360deg),

                /* Cielo base con degradado más rico */
                linear-gradient(180deg,
                    #7FB8DD 0%,
                    #A8D4E8 30%,
                    #DCE8E0 55%,
                    #E8D3B8 78%,
                    #C58B6B 100%);
            --bg-light-noon:
                /* ☀️ Sol brillante centrado arriba */
                radial-gradient(circle 320px at 50% 8%,
                    rgba(255, 250, 200, 0.98) 0%,
                    rgba(255, 230, 140, 0.7) 25%,
                    rgba(255, 210, 120, 0.3) 50%,
                    rgba(255, 190, 100, 0.08) 70%,
                    transparent 82%),

                /* Resplandor difuso del sol (centrado) */
                radial-gradient(ellipse 60% 40% at 50% 6%,
                    rgba(255, 245, 210, 0.55) 0%,
                    rgba(255, 235, 190, 0.2) 55%,
                    transparent 80%),

                /* ☁️ Nubes a los lados del sol */
                radial-gradient(ellipse 42% 26% at 15% 18%,
                    rgba(255, 255, 255, 0.95) 0%,
                    rgba(255, 255, 255, 0.5) 55%,
                    transparent 78%),
                radial-gradient(ellipse 38% 22% at 85% 16%,
                    rgba(255, 255, 255, 0.9) 0%,
                    rgba(255, 255, 255, 0.45) 55%,
                    transparent 78%),
                radial-gradient(ellipse 40% 22% at 30% 32%,
                    rgba(255, 255, 255, 0.8) 0%,
                    transparent 75%),
                radial-gradient(ellipse 42% 24% at 72% 35%,
                    rgba(255, 255, 255, 0.75) 0%,
                    transparent 75%),

                /* 🌿 Colinas verdes brillantes */
                radial-gradient(ellipse 85% 52% at 12% 100%,
                    rgba(120, 200, 100, 0.75) 0%,
                    rgba(90, 175, 80, 0.42) 45%,
                    transparent 80%),
                radial-gradient(ellipse 82% 50% at 95% 100%,
                    rgba(140, 215, 110, 0.7) 0%,
                    rgba(105, 185, 90, 0.38) 48%,
                    transparent 78%),

                /* 🔷 Hexágonos sutiles */
                conic-gradient(from 30deg at 50% 50%,
                    transparent 0deg 59deg,
                    rgba(255, 255, 255, 0.4) 59deg 61deg,
                    transparent 61deg 119deg,
                    rgba(255, 255, 255, 0.4) 119deg 121deg,
                    transparent 121deg 179deg,
                    rgba(255, 255, 255, 0.4) 179deg 181deg,
                    transparent 181deg 239deg,
                    rgba(255, 255, 255, 0.4) 239deg 241deg,
                    transparent 241deg 299deg,
                    rgba(255, 255, 255, 0.4) 299deg 301deg,
                    transparent 301deg 360deg),

                /* 🌤️ Cielo azul brillante */
                linear-gradient(180deg,
                    #4FA8E0 0%,
                    #7CC5ED 30%,
                    #B8E0F5 55%,
                    #DFF0E8 78%,
                    #C8E0B8 100%);
            --bg-light-night:
                /* Última luz del sol */
                radial-gradient(circle 300px at 78% 18%,
                    rgba(255, 195, 130, 0.58) 0%,
                    rgba(210, 120, 100, 0.28) 38%,
                    rgba(170, 85, 95, 0.08) 65%,
                    transparent 80%),

                /* Estrellas brillantes */
                radial-gradient(circle at 12% 16%, rgba(255, 255, 255, 0.98) 0 1.2px, transparent 2.5px),
                radial-gradient(circle at 28% 31%, rgba(255, 248, 225, 0.95) 0 1px, transparent 2px),
                radial-gradient(circle at 43% 12%, rgba(255, 255, 255, 0.92) 0 1.2px, transparent 2.5px),
                radial-gradient(circle at 59% 25%, rgba(255, 248, 225, 0.98) 0 1.5px, transparent 2.8px),
                radial-gradient(circle at 72% 38%, rgba(255, 255, 255, 0.88) 0 1px, transparent 2px),
                radial-gradient(circle at 89% 20%, rgba(255, 248, 225, 0.95) 0 1.2px, transparent 2.5px),
                radial-gradient(circle at 19% 48%, rgba(255, 255, 255, 0.85) 0 1px, transparent 2px),
                radial-gradient(circle at 81% 55%, rgba(255, 255, 255, 0.9) 0 1px, transparent 2px),
                radial-gradient(circle at 35% 62%, rgba(255, 240, 220, 0.85) 0 1px, transparent 2px),
                radial-gradient(circle at 68% 75%, rgba(255, 255, 255, 0.8) 0 1px, transparent 2px),

                /* Colinas rojizas y violáceas */
                radial-gradient(ellipse 82% 50% at 10% 100%,
                    rgba(150, 55, 55, 0.48) 0%,
                    rgba(120, 45, 65, 0.25) 45%,
                    transparent 72%),
                radial-gradient(ellipse 80% 50% at 95% 100%,
                    rgba(75, 45, 55, 0.5) 0%,
                    rgba(90, 55, 70, 0.28) 48%,
                    transparent 75%),

                /* Hexágonos sutiles */
                conic-gradient(from 30deg at 50% 50%,
                    transparent 0deg 59deg,
                    rgba(255, 220, 200, 0.18) 59deg 61deg,
                    transparent 61deg 119deg,
                    rgba(255, 220, 200, 0.18) 119deg 121deg,
                    transparent 121deg 179deg,
                    rgba(255, 220, 200, 0.18) 179deg 181deg,
                    transparent 181deg 239deg,
                    rgba(255, 220, 200, 0.18) 239deg 241deg,
                    transparent 241deg 299deg,
                    rgba(255, 220, 200, 0.18) 299deg 301deg,
                    transparent 301deg 360deg),

                /* Cielo del crepúsculo */
                linear-gradient(180deg,
                    #4A4E6E 0%,
                    #7A6E85 30%,
                    #B887A0 52%,
                    #C68478 75%,
                    #6E4A4D 100%);

            --bg-dark-morning:
                /* Sol frío, luz dorada tenue */
                radial-gradient(circle 300px at 82% 15%,
                    rgba(255, 210, 140, 0.42) 0%,
                    rgba(230, 155, 95, 0.18) 45%,
                    transparent 78%),

                /* Resplandor extendido */
                radial-gradient(ellipse 60% 32% at 80% 10%,
                    rgba(210, 170, 130, 0.2) 0%,
                    transparent 78%),

                /* Nubes azuladas */
                radial-gradient(ellipse 55% 32% at 22% 20%,
                    rgba(130, 165, 195, 0.28) 0%,
                    transparent 75%),
                radial-gradient(ellipse 44% 25% at 62% 15%,
                    rgba(110, 145, 180, 0.2) 0%,
                    transparent 75%),

                /* Colinas */
                radial-gradient(ellipse 82% 50% at 10% 100%,
                    rgba(120, 60, 60, 0.45) 0%,
                    rgba(85, 45, 50, 0.22) 45%,
                    transparent 78%),
                radial-gradient(ellipse 80% 50% at 95% 100%,
                    rgba(50, 35, 45, 0.5) 0%,
                    transparent 75%),

                /* Hexágonos */
                conic-gradient(from 30deg at 50% 50%,
                    transparent 0deg 59deg,
                    rgba(202, 222, 238, 0.18) 59deg 61deg,
                    transparent 61deg 119deg,
                    rgba(202, 222, 238, 0.18) 119deg 121deg,
                    transparent 121deg 179deg,
                    rgba(202, 222, 238, 0.18) 179deg 181deg,
                    transparent 181deg 239deg,
                    rgba(202, 222, 238, 0.18) 239deg 241deg,
                    transparent 241deg 299deg,
                    rgba(202, 222, 238, 0.18) 299deg 301deg,
                    transparent 301deg 360deg),

                /* Cielo nocturno-amanecer */
                linear-gradient(180deg,
                    #1E3348 0%,
                    #2C4459 35%,
                    #4A4550 65%,
                    #2A2632 100%);

            --bg-dark-noon:
                /* Sol cobre */
                radial-gradient(circle 340px at 82% 14%,
                    rgba(235, 145, 80, 0.36) 0%,
                    rgba(175, 80, 60, 0.16) 45%,
                    transparent 78%),

                /* Resplandor cálido */
                radial-gradient(ellipse 60% 32% at 80% 10%,
                    rgba(200, 120, 80, 0.18) 0%,
                    transparent 78%),

                /* Nubes oscuras */
                radial-gradient(ellipse 52% 30% at 22% 20%,
                    rgba(110, 70, 80, 0.32) 0%,
                    transparent 75%),
                radial-gradient(ellipse 46% 26% at 62% 15%,
                    rgba(95, 60, 70, 0.24) 0%,
                    transparent 75%),

                /* Colinas granate */
                radial-gradient(ellipse 82% 50% at 12% 100%,
                    rgba(140, 45, 45, 0.48) 0%,
                    rgba(110, 35, 40, 0.25) 45%,
                    transparent 78%),
                radial-gradient(ellipse 80% 50% at 95% 100%,
                    rgba(55, 40, 40, 0.55) 0%,
                    transparent 75%),

                /* Hexágonos */
                conic-gradient(from 30deg at 50% 50%,
                    transparent 0deg 59deg,
                    rgba(255, 224, 198, 0.16) 59deg 61deg,
                    transparent 61deg 119deg,
                    rgba(255, 224, 198, 0.16) 119deg 121deg,
                    transparent 121deg 179deg,
                    rgba(255, 224, 198, 0.16) 179deg 181deg,
                    transparent 181deg 239deg,
                    rgba(255, 224, 198, 0.16) 239deg 241deg,
                    transparent 241deg 299deg,
                    rgba(255, 224, 198, 0.16) 299deg 301deg,
                    transparent 301deg 360deg),

                /* Cielo oscuro cálido */
                linear-gradient(180deg,
                    #2A3546 0%,
                    #48404B 35%,
                    #654540 65%,
                    #261F26 100%);

            --bg-dark-night:
                /* Bruma violeta profunda */
                radial-gradient(ellipse 65% 48% at 78% 18%,
                    rgba(120, 90, 160, 0.28) 0%,
                    rgba(90, 70, 130, 0.12) 50%,
                    transparent 78%),

                /* Segunda bruma azulada */
                radial-gradient(ellipse 55% 40% at 22% 25%,
                    rgba(70, 90, 140, 0.22) 0%,
                    transparent 75%),

                /* Estrellas brillantes */
                radial-gradient(circle at 8% 12%, rgba(255, 255, 255, 1) 0 1.3px, transparent 2.5px),
                radial-gradient(circle at 17% 27%, rgba(205, 224, 255, 0.95) 0 1px, transparent 2px),
                radial-gradient(circle at 29% 9%, rgba(255, 255, 255, 0.92) 0 1.2px, transparent 2.5px),
                radial-gradient(circle at 37% 37%, rgba(255, 255, 255, 0.98) 0 1.5px, transparent 2.8px),
                radial-gradient(circle at 48% 18%, rgba(205, 224, 255, 0.92) 0 1px, transparent 2px),
                radial-gradient(circle at 57% 8%, rgba(255, 255, 255, 0.95) 0 1.2px, transparent 2.5px),
                radial-gradient(circle at 68% 30%, rgba(255, 255, 255, 1) 0 1.3px, transparent 2.5px),
                radial-gradient(circle at 78% 12%, rgba(205, 224, 255, 0.95) 0 1px, transparent 2px),
                radial-gradient(circle at 91% 29%, rgba(255, 255, 255, 0.92) 0 1.2px, transparent 2.5px),
                radial-gradient(circle at 13% 48%, rgba(255, 255, 255, 0.85) 0 1px, transparent 2px),
                radial-gradient(circle at 84% 48%, rgba(205, 224, 255, 0.9) 0 1px, transparent 2px),
                radial-gradient(circle at 61% 45%, rgba(255, 255, 255, 0.88) 0 1px, transparent 2px),
                radial-gradient(circle at 25% 68%, rgba(255, 255, 255, 0.85) 0 1px, transparent 2px),
                radial-gradient(circle at 72% 62%, rgba(205, 224, 255, 0.9) 0 1px, transparent 2px),

                /* Colinas rojo vino */
                radial-gradient(ellipse 82% 50% at 10% 100%,
                    rgba(130, 42, 55, 0.45) 0%,
                    rgba(95, 30, 45, 0.22) 45%,
                    transparent 75%),
                radial-gradient(ellipse 80% 50% at 95% 100%,
                    rgba(55, 38, 55, 0.6) 0%,
                    rgba(70, 45, 65, 0.3) 48%,
                    transparent 75%),

                /* Hexágonos oscuros */
                conic-gradient(from 30deg at 50% 50%,
                    transparent 0deg 59deg,
                    rgba(200, 180, 220, 0.12) 59deg 61deg,
                    transparent 61deg 119deg,
                    rgba(200, 180, 220, 0.12) 119deg 121deg,
                    transparent 121deg 179deg,
                    rgba(200, 180, 220, 0.12) 179deg 181deg,
                    transparent 181deg 239deg,
                    rgba(200, 180, 220, 0.12) 239deg 241deg,
                    transparent 241deg 299deg,
                    rgba(200, 180, 220, 0.12) 299deg 301deg,
                    transparent 301deg 360deg),

                /* Cielo nocturno con más profundidad */
                linear-gradient(180deg,
                    #0D1220 0%,
                    #1A1F35 30%,
                    #29223A 58%,
                    #3A2A3F 82%,
                    #141824 100%);
            --bg-app: (--bg-light-morning:);
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
            background: #12121294;
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
            background: #12121294;
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
            background: #12121294;
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
            background: #12121294;
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
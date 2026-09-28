<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecosistema Integral Multi-Almacén | MYVET SISTEM</title>
    <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">
    
    <!-- Bootstrap 5, Icons & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
  <style>
    :root {
        --primary-vivid: #8b5cf6;
        --accent-cyan: #06b6d4;
        --accent-pink: #ec4899;
        --accent-emerald: #10b981;
        --dark-bg: #0b0f19;
        --glass-card: rgba(255, 255, 255, 0.04);
        --glass-border: rgba(255, 255, 255, 0.12);
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: var(--dark-bg);
        color: #f8fafc;
        overflow-x: hidden;
    }

    /* FONDO DINÁMICO CROMÁTICO */
    .dynamic-bg {
        position: fixed;
        inset: 0;
        z-index: -1;
        overflow: hidden;
        background: radial-gradient(circle at 50% 50%, #111827, #0b0f19);
    }

    .orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(110px);
        opacity: 0.45;
        animation: floatOrb 18s infinite alternate ease-in-out;
    }

    .orb-1 { width: 500px; height: 500px; background: #7c3aed; top: -10%; left: -5%; }
    .orb-2 { width: 550px; height: 550px; background: #06b6d4; bottom: -15%; right: -5%; animation-duration: 24s; }
    .orb-3 { width: 350px; height: 350px; background: #10b981; top: 40%; left: 40%; animation-duration: 20s; }

    @keyframes floatOrb {
        0% { transform: translate(0, 0) scale(1); }
        100% { transform: translate(60px, 50px) scale(1.15); }
    }

    /* TARJETAS GLASSMORPHISM */
    .glass-box {
        background: var(--glass-card);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .glass-box:hover {
        border-color: rgba(56, 189, 248, 0.4);
        transform: translateY(-6px);
        box-shadow: 0 25px 60px rgba(6, 182, 212, 0.15);
    }

    /* TEXTOS & ILUMINACIÓN */
    .hero-title {
        font-size: 3.5rem;
        font-weight: 800;
        letter-spacing: -1px;
        background: linear-gradient(135deg, #ffffff 30%, #38bdf8 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .img-preview {
        width: 100%;
        height: 240px;
        object-fit: cover;
        border-radius: 16px;
        border: 1px solid var(--glass-border);
        transition: transform 0.5s ease;
    }

    .glass-box:hover .img-preview {
        transform: scale(1.02);
    }

    /* BADGES DE SUBMÓDULOS */
    .submodulo-badge {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 8px 14px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #e2e8f0;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .submodulo-badge:hover {
        background: rgba(56, 189, 248, 0.15);
        border-color: #38bdf8;
        color: #fff;
    }

    /* COMPARATIVA DESARROLLO */
    .vs-card {
        border-radius: 20px;
        padding: 2.2rem;
        height: 100%;
    }

    .vs-saul {
        background: rgba(139, 92, 246, 0.08);
        border: 1px solid rgba(139, 92, 246, 0.35);
        box-shadow: 0 10px 30px rgba(139, 92, 246, 0.1);
    }

    .vs-jesus {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.08);
        opacity: 0.85;
    }

    .alert-dark {
        color: white !important;
    }

    /* ACCORDION FAQ */
    .accordion-item {
        background: transparent;
        border: 1px solid var(--glass-border);
        border-radius: 18px !important;
        margin-bottom: 1rem;
        overflow: hidden;
    }

    .accordion-button {
        background: rgba(255, 255, 255, 0.02);
        color: #fff;
        font-weight: 700;
        box-shadow: none !important;
        padding: 1.2rem 1.5rem;
    }

    .accordion-button:not(.collapsed) {
        background: rgba(56, 189, 248, 0.08);
        color: #38bdf8;
    }

    .accordion-body {
        color: #94a3b8;
        background: rgba(0, 0, 0, 0.25);
        padding: 1.2rem 1.5rem;
        line-height: 1.7;
    }

    .btn-custom {
        background: linear-gradient(135deg, #06b6d4, #8b5cf6);
        color: white;
        font-weight: 700;
        border-radius: 14px;
        padding: 0.9rem 2.2rem;
        border: none;
        box-shadow: 0 10px 25px rgba(6, 182, 212, 0.3);
        transition: all 0.3s;
    }

    .btn-custom:hover {
        transform: translateY(-2px);
        color: white;
        box-shadow: 0 15px 35px rgba(139, 92, 246, 0.5);
    }

    .tech-badge {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.15);
        padding: 8px 16px;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #38bdf8;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    /* NAVEGACIÓN Y SCROLLSPY */
    section[id] {
        scroll-margin-top: 100px;
    }

    .custom-nav-link {
        transition: all 0.25s ease-in-out;
        border: 1px solid transparent;
    }

    .custom-nav-link:hover {
        color: #ffffff !important;
        background: rgba(255, 255, 255, 0.08);
    }

    /* EFECTO DE ILUMINACIÓN NEÓN PARA SECCIÓN ACTIVA */
    .navbar-nav .nav-link.custom-nav-link.active {
        color: #ffffff !important;
        background-color: rgba(6, 186, 212, 0.2) !important;
        border-color: rgba(6, 186, 212, 0.45) !important;
        box-shadow: 0 0 16px rgba(6, 186, 212, 0.35) !important;
        font-weight: 600;
    }
</style>
</head>
<body data-bs-spy="scroll" data-bs-target="#navbarContent" data-bs-offset="100" tabindex="0">
   <!-- FONDO DINÁMICO -->
    <div class="dynamic-bg">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <!-- NAVBAR -->
 <!-- ESTILOS ADICIONALES PARA LA NAVBAR (Colocar en el <head> o archivo CSS) -->
<style>
    /* Navbar Glassmorphism Ultra Delgado */
    .navbar-glass {
        background: rgba(11, 15, 25, 0.75) !important;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        transition: all 0.3s ease;
    }

    /* Badge del ERP junto al Logo */
    .brand-badge {
        font-size: 0.68rem;
        letter-spacing: 1px;
        background: rgba(6, 182, 212, 0.12);
        color: #38bdf8;
        border: 1px solid rgba(6, 182, 212, 0.3);
        padding: 3px 8px;
        border-radius: 20px;
        font-weight: 700;
    }

    /* Links de Navegación */
    .custom-nav-link {
        color: rgba(255, 255, 255, 0.65) !important;
        font-size: 0.9rem;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid transparent;
    }

    .custom-nav-link:hover {
        color: #ffffff !important;
        background: rgba(255, 255, 255, 0.08);
    }

    /* Estado Activo (Scrollspy & Clic) con Neón */
    .navbar-nav .nav-link.custom-nav-link.active {
        color: #ffffff !important;
        background: rgba(6, 182, 212, 0.18) !important;
        border-color: rgba(6, 182, 212, 0.4) !important;
        box-shadow: 0 0 15px rgba(6, 182, 212, 0.3) !important;
        font-weight: 600;
    }

    /* Botón de Acción Principal */
    .btn-glow {
        background: linear-gradient(135deg, #06b6d4, #8b5cf6);
        color: #ffffff !important;
        font-weight: 600;
        font-size: 0.85rem;
        border: none;
        box-shadow: 0 4px 15px rgba(6, 182, 212, 0.3);
        transition: all 0.3s ease;
    }

    .btn-glow:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(139, 92, 246, 0.5);
    }
</style>

<!-- NAVBAR REDISEÑADA -->
<nav class="navbar navbar-expand-xl fixed-top navbar-dark navbar-glass py-2 py-lg-3">
    <div class="container">
        
        <!-- LOGO Y MARCA -->
        <a class="navbar-brand text-white fw-extrabold d-flex align-items-center gap-2 m-0 fs-5" href="#inicio">
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" 
                 style="background: rgba(6, 182, 212, 0.15); border: 1px solid rgba(6, 182, 212, 0.3);">
                <i class="bi bi-box-seam-fill text-info fs-5"></i>
            </div>
            <span class="tracking-tight fw-bold">MYVET</span>
            <span class="brand-badge text-uppercase d-none d-sm-inline-block">ERP Multi-Almacén</span>
        </a>

        <!-- BOTÓN TOGGLE MÓVIL -->
        <button class="navbar-toggler border-0 p-2 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list text-white fs-2"></i>
        </button>

        <!-- CONTENIDO DEL MENÚ -->
        <div class="collapse navbar-collapse mt-3 mt-xl-0" id="navbarContent">
            
            <!-- ENLACES DE NAVEGACIÓN -->
            <ul class="navbar-nav mx-auto gap-1 gap-xxl-2 mb-3 mb-xl-0" id="mainNavbar">
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-pill custom-nav-link" href="#inicio">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-pill custom-nav-link" href="#modulos">Módulos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-pill custom-nav-link" href="#planes">Planes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-pill custom-nav-link" href="#ambientes">Ambientes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-pill custom-nav-link" href="#adicionalesF">Adicionales</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-pill custom-nav-link" href="#preguntas">FAQ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-pill custom-nav-link" href="#contacto">Contacto</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-pill custom-nav-link text-white-50 opacity-75" href="terminos_condiciones.php">Términos</a>
                </li>
            </ul>

            <!-- BOTÓN DE ACCESO AL SISTEMA -->
            <div class="d-flex align-items-center gap-2 pt-2 pt-xl-0 border-top border-xl-0 border-white border-opacity-10">
                <a href="index.php" class="btn btn-glow rounded-pill px-4 py-2 w-100 w-xl-auto text-center d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-box-arrow-in-right"></i> Ingresar al Sistema
                </a>
            </div>

        </div>
    </div>
</nav>
    <div class="container py-5">
    <section id="inicio">
        <!-- HERO PRINCIPAL -->
        <div class="text-center py-4 my-5">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-4 py-2 mb-3 fs-7 fw-bold">
                El ERP Modular que Tu Empresa Necesita
            </span>
            <h1 class="hero-title display-3 mb-3">Toma el Control Absoluto de tu Negocio y Escala Sin Límites</h1>
            <p class="lead mx-auto" style="max-width: 820px;">
                Simplifica tu administración diaria, elimina fugas de inventario y conecta todas tus sucursales en tiempo real. La plataforma integral que centraliza tus operaciones para que te enfoques en lo más importante: <strong>hacer crecer tu negocio</strong>.
            </p>
        </div>

        <!-- MÉTRICAS DE ALTO RENDIMIENTO -->
        <div class="row g-4 my-4 text-center">
            <div class="col-6 col-md-3">
                <div class="glass-box p-3 h-100 d-flex flex-column justify-content-center">
                    <h2 class="fw-bold text-info mb-0 display-6">100%</h2>
                    <small class="fw-semibold mt-1">Visibilidad y Control Operativo</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="glass-box p-3 h-100 d-flex flex-column justify-content-center">
                    <h2 class="fw-bold text-success mb-0 display-6">+360°</h2>
                    <small class="fw-semibold mt-1">Conexión Multi-Sucursal</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="glass-box p-3 h-100 d-flex flex-column justify-content-center">
                    <h2 class="fw-bold text-warning mb-0 display-6">100%</h2>
                    <small class="fw-semibold mt-1">Adaptable a tu Crecimiento</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="glass-box p-3 h-100 d-flex flex-column justify-content-center">
                    <h2 class="fw-bold text-danger mb-0 display-6">0%</h2>
                    <small class="fw-semibold mt-1">Pérdidas y Fugas de Stock</small>
                </div>
            </div>
        </div>

        <!-- PILARES DESTACADOS (FILA PRINCIPAL: 3 TARJETAS GRANDES) -->
        <div class="row g-4 my-4">
            <!-- 1. GESTIÓN MULTI-ALMACÉN -->
            <div class="col-lg-4 col-md-6">
                <div class="glass-box p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=800&q=80" class="img-preview mb-3 w-100 rounded" style="height: 220px; object-fit: cover;" alt="Gestión Multi-Almacén">
                        <h4 class="fw-bold mb-3 text-white"><i class="bi bi-buildings-fill text-info me-2"></i>Gestión Multi-Almacén</h4>
                        <p class="lh-base">
                            Sincronización inmediata entre múltiples sucursales físicas y depósitos centralizados. Permite la transferencia directa de existencias y consulta de disponibilidad de stock en tiempo real desde cualquier ubicación.
                        </p>
                    </div>
                    <div class="mt-3 pt-3 border-top border-secondary border-opacity-25 text-info style-italic" style="font-size: 0.85rem;">
                        <i class="bi bi-check-circle me-1"></i> Control total de existencias inter-sucursal
                    </div>
                </div>
            </div>

            <!-- 2. VENTAS & PAGOS DIFERIDOS -->
            <div class="col-lg-4 col-md-6">
                <div class="glass-box p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <img src="https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=800&q=80" class="img-preview mb-3 w-100 rounded" style="height: 220px; object-fit: cover;" alt="Pagos Diferidos">
                        <h4 class="fw-bold mb-3 text-white"><i class="bi bi-wallet-fill text-success me-2"></i>Ventas & Pagos Diferidos</h4>
                        <p class="lh-base">
                            Módulo comercial completo preparado para cobro al contado, emisión de remisiones, cotizaciones y gestión de cuentas por cobrar con un estricto registro de abonos y historial de pagos diferidos.
                        </p>
                    </div>
                    <div class="mt-3 pt-3 border-top border-secondary border-opacity-25 text-success style-italic" style="font-size: 0.85rem;">
                        <i class="bi bi-check-circle me-1"></i> Seguimiento de cartera y abonados
                    </div>
                </div>
            </div>

            <!-- 3. LOTES Y MERMAS -->
            <div class="col-lg-4 col-md-12">
                <div class="glass-box p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <img src="https://images.unsplash.com/photo-1616401784845-180882ba9ba8?auto=format&fit=crop&w=800&q=80" class="img-preview mb-3 w-100 rounded" style="height: 220px; object-fit: cover;" alt="Lotes y Caducidades">
                        <h4 class="fw-bold mb-3 text-white"><i class="bi bi-upc-scan text-primary me-2"></i>Lotes, Mermas y Conversiones</h4>
                        <p class="lh-base">
                            Rotación FIFO indexada por lotes y caducidades, registro justificado de mermas e incidencias de almacén, así como desglose/conversión de empaques o mayoreo a unidades con recálculo automático de costo.
                        </p>
                    </div>
                    <div class="mt-3 pt-3 border-top border-secondary border-opacity-25 text-primary-subtle style-italic" style="font-size: 0.85rem;">
                        <i class="bi bi-check-circle me-1"></i> Trazabilidad FIFO y empaquetado
                    </div>
                </div>
            </div>
        </div>

        <!-- TARJETA DESTACADA (ACCESO EN LA NUBE Y MULTI-DISPOSITIVO) -->
        <div class="row my-4">
            <div class="col-12">
                <div class="glass-box p-4 border-warning border-opacity-30">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-5 col-md-6">
                            <img src="https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?auto=format&fit=crop&w=800&q=80" class="img-preview w-100 rounded shadow" style="height: 250px; object-fit: cover;" alt="Acceso Multi-dispositivo y Nube">
                        </div>
                        <div class="col-lg-7 col-md-6">
                            <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1 mb-2 fs-8">
                                Portabilidad Operativa en la Nube
                            </span>
                            <h3 class="fw-bold text-white mb-3">
                                <i class="bi bi-cloud-check-fill text-warning me-2"></i>Sistema 100% Cloud Multi-Dispositivo
                            </h3>
                            <p class="lh-base mb-3">
                                El sistema <strong>vive en la nube</strong> y es accesible desde cualquier dispositivo conectado a internet (smartphones, tabletas, laptops o PCs de escritorio). No requiere instalaciones complejas ni servidores locales.
                            </p>
                            <div class="p-3 rounded bg-black bg-opacity-20 border border-warning border-opacity-25 text-secondary" style="font-size: 0.9rem;">
                                <i class="bi bi-info-circle-fill text-warning me-2"></i>
                                <strong>Planes de Almacén Único:</strong> Para los planes iniciales diseñados para un solo almacén (sin multi-almacén), el sistema funciona en la nube con la misma fluidez y disponibilidad 24/7 desde cualquier navegador.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

  <section id="modulos" class="my-5 pt-4">
   

  
        <!-- MÓDULOS DEL SISTEMA -->
        <div class="my-5 pt-4">
            <h2 class="text-center fw-bold mb-2">Módulos del Sistema</h2>
            <p class="text-center  mb-5">Arquitectura modular integral para la gestión administrativa, comercial y médica.</p>

            <div class="row g-4">
                <?php
                $modulos = [
                    [
                        'id_grupo' => 'ventas_clientes',
                        'titulo'   => 'Ventas y Clientes',
                        'icono'    => 'bi-bag-heart-fill',
                        'color'    => 'text-emerald',
                        'desc'     => 'Administración completa del flujo de ingresos, cotizaciones, caja rápida y control de abonado con soporte para pagos diferidos.',
                        'submodulos' => [
                            ['icon' => 'bi-cash-coin', 'label' => 'Ventas'],
                            ['icon' => 'bi-file-earmark-diff-fill', 'label' => 'Remisiones'],
                            ['icon' => 'bi-reception-4', 'label' => 'Caja Rápida'],
                            ['icon' => 'bi-file-earmark-spreadsheet-fill', 'label' => 'Cotizaciones'],
                            ['icon' => 'bi-person-rolodex', 'label' => 'Clientes'],
                            ['icon' => 'bi-person-bounding-box', 'label' => 'Estatus Clientes'],
                            ['icon' => 'bi-award-fill', 'label' => 'Ventas Vendedor'],
                            ['icon' => 'bi-journal-text', 'label' => 'Historial Ventas'],
                            ['icon' => 'bi-patch-check-fill', 'label' => 'Comprobantes'],
                            ['icon' => 'bi-wallet-fill', 'label' => 'Registrar Pagos'],
                        ]
                    ],
                    [
                        'id_grupo' => 'compras_proveedores',
                        'titulo'   => 'Compras y Proveedores',
                        'icono'    => 'bi-cart-dash-fill',
                        'color'    => 'text-info',
                        'desc'     => 'Registro de proveedores, ordenamiento de solicitudes de abastecimiento y gestión centralizada de compras y gastos operativos.',
                        'submodulos' => [
                            ['icon' => 'bi-credit-card-fill', 'label' => 'Compras y Gastos'],
                            ['icon' => 'bi-building-up', 'label' => 'Proveedores'],
                            ['icon' => 'bi-file-earmark-plus-fill', 'label' => 'Solicitudes Compra'],
                        ]
                    ],
                    [
                        'id_grupo' => 'inventario_almacen',
                        'titulo'   => 'Inventario y Almacén',
                        'icono'    => 'bi-archive-fill',
                        'color'    => 'text-warning',
                        'desc'     => 'Control muti-almacén en tiempo real, movimientos inter-sucursales, baja de mermas, conversión de insumos e historial de lotes.',
                        'submodulos' => [
                            ['icon' => 'bi-building-fill', 'label' => 'Almacén'],
                            ['icon' => 'bi-shuffle', 'label' => 'Movimientos'],
                            ['icon' => 'bi-trash3-fill', 'label' => 'Mermas'],
                            ['icon' => 'bi-signpost-split-fill', 'label' => 'Conversiones'],
                            ['icon' => 'bi-upc-scan', 'label' => 'Historial Lotes'],
                            ['icon' => 'bi-bookshelf', 'label' => 'Historial Compras'],
                        ]
                    ],
                    [
                        'id_grupo' => 'finanzas_tesoreria',
                        'titulo'   => 'Finanzas y Tesorería',
                        'icono'    => 'bi-coin',
                        'color'    => 'text-primary',
                        'desc'     => 'Supervisión en tiempo real del flujo de caja, arqueos diario de valores, cortes de caja y balances financieros ejecutivos.',
                        'submodulos' => [
                            ['icon' => 'bi-activity', 'label' => 'Finanzas'],
                            ['icon' => 'bi-kanban-fill', 'label' => 'Finanzas Admin'],
                            ['icon' => 'bi-receipt', 'label' => 'Corte de Caja'],
                            ['icon' => 'bi-vault', 'label' => 'Tesorería'],
                        ]
                    ],
                    [
                        'id_grupo' => 'atencion_medica',
                        'titulo'   => 'Atención Médica',
                        'icono'    => 'bi-heart-pulse-fill',
                        'color'    => 'text-danger',
                        'desc'     => 'Expedientes clínicos digitales, registro parametrizado de mascotas/pacientes y control integral de consultas médicas.',
                        'submodulos' => [
                            ['icon' => 'bi-paw-fill', 'label' => 'Mascotas / Pacientes'],
                            ['icon' => 'bi-folder2-open', 'label' => 'Expedientes Médicos'],
                            ['icon' => 'bi-journal-medical', 'label' => 'Consultas Médicas'],
                        ]
                    ],
                    [
                        'id_grupo' => 'logistica_distribucion',
                        'titulo'   => 'Logística y Distribución',
                        'icono'    => 'bi-pin-map-fill',
                        'color'    => 'text-info',
                        'desc'     => 'Planificación de rutas de reparto, gestión vehicular, órdenes de despacho y seguimiento de asignaciones para personal.',
                        'submodulos' => [
                            ['icon' => 'bi-send-check-fill', 'label' => 'Despachos'],
                            ['icon' => 'bi-car-front-fill', 'label' => 'Vehículos'],
                            ['icon' => 'bi-check-all', 'label' => 'Verificaciones'],
                            ['icon' => 'bi-tools', 'label' => 'Mantenimientos'],
                            ['icon' => 'bi-compass-fill', 'label' => 'Repartos'],
                            ['icon' => 'bi-sign-turn-right-fill', 'label' => 'Mis Repartos'],
                            ['icon' => 'bi-pass-fill', 'label' => 'Viajes Personal'],
                        ]
                    ],
                    [
                        'id_grupo' => 'recursos_humanos',
                        'titulo'   => 'Recursos Humanos',
                        'icono'    => 'bi-person-vcard',
                        'color'    => 'text-pink',
                        'desc'     => 'Control de plantilla de personal, cálculo de nómina, registro de asistencias/faltas, asignación de préstamos y viáticos.',
                        'submodulos' => [
                            ['icon' => 'bi-person-badge', 'label' => 'Trabajadores'],
                            ['icon' => 'bi-currency-dollar', 'label' => 'Nómina'],
                            ['icon' => 'bi-piggy-bank-fill', 'label' => 'Préstamos'],
                            ['icon' => 'bi-person-x-fill', 'label' => 'Faltas'],
                            ['icon' => 'bi-cash-front', 'label' => 'Pagos Viajes'],
                            ['icon' => 'bi-umbrella-fill', 'label' => 'Vacaciones'],
                        ]
                    ],
                    [
                        'id_grupo' => 'administracion',
                        'titulo'   => 'Administración',
                        'icono'    => 'bi-shield-lock-fill',
                        'color'    => 'text-danger',
                        'desc'     => 'Gestión de usuarios, permisos por perfil de acceso, auditoría de sesiones y configuración de la plataforma.',
                        'submodulos' => [
                            ['icon' => 'bi-person-lock', 'label' => 'Usuarios'],
                        ]
                    ],
                ];

                foreach ($modulos as $mod) {
                ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="glass-box p-4 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi <?php echo $mod['icono']; ?> <?php echo $mod['color']; ?> fs-3 me-3"></i>
                                    <h4 class="fw-bold mb-0 text-white"><?php echo $mod['titulo']; ?></h4>
                                </div>
                                <p class=" small mb-3"><?php echo $mod['desc']; ?></p>
                            </div>
                            <div class="d-flex flex-wrap gap-2 pt-2 border-top border-secondary border-opacity-10">
                                <?php foreach ($mod['submodulos'] as $sub) { ?>
                                    <span class="submodulo-badge">
                                        <i class="bi <?php echo $sub['icon']; ?>"></i> <?php echo $sub['label']; ?>
                                    </span>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
</section>
        <!-- SECCIÓN DE PLANES Y PRECIOS (100% RENTA) -->
        <!-- SECCIÓN DE PLANES Y PRECIOS -->
<!-- SECCIÓN DE PLANES EN TARJETAS AMPLIAS Y LEGIBLES -->
  <section id="planes">
  
<div class="my-5 pt-4">
    <div class="text-center mb-5">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-4 py-2 mb-3 fs-7 fw-bold">
            Modelos de Suscripción Flexibles
        </span>
        <h2 class="fw-bold display-6 mb-2">Planes Diseñados para Escalar tu Operación</h2>
        <p class=" mx-auto" style="max-width: 750px;">
            Elige la estructura que mejor se adapte a tu ritmo de trabajo. Cada nivel amplía tus capacidades operativas y de control gerencial.
        </p>
    </div>

    <!-- NOTA SOBRE MÓDULOS LIGADOS -->
    <div class="alert alert-dark border-secondary border-opacity-25 glass-box p-3 mb-5 d-flex align-items-center gap-3">
        <i class="bi bi-link-45deg text-info fs-3"></i>
        <div class="small ">
            <strong class="text-white">Nota sobre la arquitectura de módulos:</strong> Existen funcionalidades nativas que dependen operativamente entre sí (módulos ligados). Al seleccionar o personalizar un plan, ciertos submódulos clave se activan en conjunto para garantizar la integridad de los flujos de datos.
        </div>
    </div>

    <!-- TARJETAS AMPLIAS EN GRID MÁS ESPACIOSO -->
    <div class="row g-4 align-items-stretch">
        
        <!-- PLAN STARTER -->
        <div class="col-xl-3 col-md-6">
            <div class="glass-box p-4 h-100 d-flex flex-column justify-content-between border-secondary border-opacity-25">
                <div>
                    <span class="badge bg-secondary-subtle  border border-secondary border-opacity-25 rounded-pill px-3 py-1 mb-3">Súper Básico</span>
                    <h4 class="fw-bold text-white mb-1">Plan Starter</h4>
                    <p class=" small">Para cobro express en mostrador sin complicaciones administrativas.</p>
                    
                    <div class="my-3">
                        <span class="display-6 fw-bold text-white">$300</span>
                        <span class=" small">MXN / mes</span>
                    </div>

                    <div class="p-2 rounded bg-white bg-opacity-10 mb-3 text-center">
                        <small class="text-info fw-semibold"><i class="bi bi-person-fill me-1"></i>1 Usuario único</small>
                        <div class="" style="font-size: 0.75rem;">Operación individual en caja</div>
                    </div>

                    <p class="fw-bold text-white fs-7 mb-2">Módulos incluidos:</p>
                    <ul class="list-unstyled  small lh-base mb-3" style="font-size: 0.85rem;">
                        <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i>Caja Rápida / Mostrador</li>
                        <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i>Historial de Ventas</li>
                        <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i>Almacén Base (Sin Movimientos)</li>
                        <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i>Compras y Gastos</li>
                    </ul>

                    <div class="p-2 rounded bg-warning bg-opacity-10 border border-warning border-opacity-25 text-warning mb-4 style-italic" style="font-size: 0.78rem;">
                        <i class="bi bi-info-circle-fill me-1"></i>Ventas y compras exclusivas para <strong>Público y Proveedor en General</strong> (sin catálogos).
                    </div>
                </div>

                <div>
                    <button class="btn btn-outline-light w-100 rounded-3 fw-semibold">Elegir Plan</button>
                </div>
            </div>
        </div>

        <!-- PLAN BÁSICO -->
        <div class="col-xl-3 col-md-6">
            <div class="glass-box p-4 h-100 d-flex flex-column justify-content-between border-secondary border-opacity-25">
                <div>
                    <span class="badge bg-secondary-subtle  border border-secondary border-opacity-25 rounded-pill px-3 py-1 mb-3">Esencial</span>
                    <h4 class="fw-bold text-white mb-1">Plan Básico</h4>
                    <p class=" small">Gestión comercial básica con registro formal de catálogos.</p>
                    
                    <div class="my-3">
                        <span class="display-6 fw-bold text-white">$450</span>
                        <span class=" small">MXN / mes</span>
                    </div>

                    <div class="p-2 rounded bg-white bg-opacity-10 mb-3 text-center">
                        <small class="text-info fw-semibold"><i class="bi bi-people-fill me-1"></i>2 Usuarios incluidos</small>
                        <div class="" style="font-size: 0.75rem;">1 Principal + 1 Trabajador</div>
                    </div>

                    <p class="fw-bold text-white fs-7 mb-2">Añade al plan Starter:</p>
                    <ul class="list-unstyled  small lh-base mb-4" style="font-size: 0.85rem;">
                        <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i><strong>Registro de Clientes</strong></li>
                        <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i><strong>Registro de Proveedores</strong></li>
                        <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i>Movimientos de Inventario</li>
                        <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i>Corte de Caja</li>
                        <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i>Entregas y Despachos</li>
                    </ul>
                </div>

                <div>
                    <div class="border-top border-secondary border-opacity-25 pt-3 mb-3 " style="font-size: 0.75rem;">
                        <i class="bi bi-person-plus-fill me-1 text-warning"></i>Usuario extra: <strong>+$200 MXN/mes</strong>
                    </div>
                    <button class="btn btn-outline-light w-100 rounded-3 fw-semibold">Elegir Plan</button>
                </div>
            </div>
        </div>

        <!-- PLAN CRECIMIENTO PROLONGADO -->
        <div class="col-xl-3 col-md-6">
            <div class="glass-box p-4 h-100 d-flex flex-column justify-content-between border-info border-opacity-25">
                <div>
                    <span class="badge bg-info-subtle text-info border border-info border-opacity-25 rounded-pill px-3 py-1 mb-3">Aceleración</span>
                    <h4 class="fw-bold text-white mb-1">Crecimiento</h4>
                    <p class=" small">Para negocios con ventas frecuentes, mermas y personal.</p>
                    
                    <div class="my-3">
                        <span class="display-6 fw-bold text-info">$600</span>
                        <span class=" small">MXN / mes</span>
                    </div>

                    <div class="p-2 rounded bg-info bg-opacity-10 mb-3 text-center border border-info border-opacity-25">
                        <small class="text-info fw-semibold"><i class="bi bi-people-fill me-1"></i>3 Usuarios incluidos</small>
                        <div class="" style="font-size: 0.75rem;">1 Principal + 2 Trabajadores</div>
                    </div>

                    <p class="fw-bold text-white fs-7 mb-2">Añade al plan anterior:</p>
                    <ul class="list-unstyled  small lh-base mb-4" style="font-size: 0.85rem;">
                        <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i>Ventas Completas & Comprobantes</li>
                        <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i>Registrar Pagos de Ventas</li>
                        <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i>Mermas de Almacén</li>
                        <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i>Finanzas Generales</li>
                        <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i>Vehículos & Trabajadores</li>
                    </ul>
                </div>

                <div>
                    <div class="border-top border-secondary border-opacity-25 pt-3 mb-3 " style="font-size: 0.75rem;">
                        <i class="bi bi-person-plus-fill me-1 text-warning"></i>Usuario extra: <strong>+$200 MXN/mes</strong>
                    </div>
                    <button class="btn btn-info text-dark w-100 rounded-3 fw-bold">Elegir Plan</button>
                </div>
            </div>
        </div>

        <!-- PLAN EXPANSIÓN FINANCIERA -->
        <div class="col-xl-3 col-md-6">
            <div class="glass-box p-4 h-100 d-flex flex-column justify-content-between border-primary border-opacity-50 position-relative" style="background: rgba(139, 92, 246, 0.05);">
                <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-primary text-white font-semibold px-3 py-1 fs-8">MÁS RECOMENDADO</span>
                <div>
                    <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 mb-3 mt-2">Avanzado</span>
                    <h4 class="fw-bold text-white mb-1">Expansión</h4>
                    <p class=" small">Control de lotes, remisiones y tesorería integral.</p>
                    
                    <div class="my-3">
                        <span class="display-6 fw-bold" style="color: #a78bfa;">$1,000</span>
                        <span class=" small">MXN / mes</span>
                    </div>

                    <div class="p-2 rounded bg-primary bg-opacity-10 mb-3 text-center border border-primary border-opacity-25">
                        <small class="text-primary-subtle fw-semibold"><i class="bi bi-people-fill me-1"></i>4 Usuarios incluidos</small>
                        <div class="" style="font-size: 0.75rem;">1 Principal + 3 Trabajadores</div>
                    </div>

                    <p class="fw-bold text-white fs-7 mb-2">Añade al plan anterior:</p>
                    <ul class="list-unstyled  small lh-base mb-4" style="font-size: 0.85rem;">
                        <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i>Remisiones & Cotizaciones</li>
                        <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i>Solicitudes de Compra</li>
                        <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i>Transmutaciones & Lotes</li>
                        <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i>Tesorería & Permisos de Usuarios</li>
                        <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i>Faltas y Vacaciones</li>
                    </ul>
                </div>

                <div>
                    <div class="border-top border-secondary border-opacity-25 pt-3 mb-3 " style="font-size: 0.75rem;">
                        <i class="bi bi-person-plus-fill me-1 text-warning"></i>Usuario extra: <strong>+$300 MXN/mes</strong>
                    </div>
                    <button class="btn btn-custom w-100 rounded-3 fw-bold">Elegir Plan</button>
                </div>
            </div>
        </div>

        <!-- PLAN CONTROL TOTAL (SEGUNDA FILA CENTRADA / DESTACADA) -->
        <div class="col-xl-6 col-md-12 mx-auto">
            <div class="glass-box p-4 h-100 d-flex flex-column justify-content-between border-emerald border-opacity-25">
                <div>
                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-1 mb-3">Suite Completa</span>
                    <h4 class="fw-bold text-white mb-1">Corporate Total</h4>
                    <p class=" small">Logística pesada, flota vehicular, gestión de personal y administración global.</p>
                    
                    <div class="my-3">
                        <span class="display-6 fw-bold text-emerald">$1,500</span>
                        <span class=" small">MXN / mes</span>
                    </div>

                    <div class="p-2 rounded bg-success bg-opacity-10 mb-3 text-center border border-success border-opacity-25">
                        <small class="text-emerald fw-semibold"><i class="bi bi-people-fill me-1"></i>4 Usuarios incluidos</small>
                        <div class="" style="font-size: 0.75rem;">1 Principal + 3 Trabajadores</div>
                    </div>

                    <p class="fw-bold text-white fs-7 mb-2">Acceso Total a Módulos +</p>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled  small lh-base mb-2" style="font-size: 0.85rem;">
                              
                                <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i><strong>Logística y Distribución</strong> (Rutas)</li>
                                <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i>Mantenimiento de Vehículos</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled  small lh-base mb-2" style="font-size: 0.85rem;">
                                <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i>Trabajadores & Nómina</li>
                                <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i>Préstamos, Viáticos y Viajes</li>
                                <li class="mb-1"><i class="bi bi-check2 text-info me-2"></i>Administración General y Permisos</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="border-top border-secondary border-opacity-25 pt-3 mb-3 " style="font-size: 0.75rem;">
                        <i class="bi bi-person-plus-fill me-1 text-warning"></i>Usuario extra: <strong>+$300 MXN/mes</strong>
                    </div>
                    <button class="btn btn-outline-light w-100 rounded-3 fw-semibold">Elegir Plan</button>
                </div>
            </div>
        </div>

    </div>

    <!-- CONFIGURACIONES Y DESARROLLOS A LA MEDIDA -->
    <div class="row g-4 mt-2">
        
        <!-- PLAN PERSONALIZADO -->
        <div class="col-md-6">
            <div class="glass-box p-4 h-100 border-info border-opacity-25">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-sliders text-info fs-2 me-3"></i>
                    <div>
                        <h5 class="fw-bold text-white mb-0">Planes Personalizados A la Medida</h5>
                        <small class="">Ajusta los módulos de acuerdo a tu flujo exacto</small>
                    </div>
                </div>
                <p class=" small mb-2">
                    Si ningún plan predeterminado encaja con la estructura de tu empresa, podemos armar una propuesta a medida seleccionando únicamente los bloques que requieres. 
                </p>
                <div class="p-2 rounded bg-black bg-opacity-25  small border border-secondary border-opacity-10">
                    <i class="bi bi-info-circle text-info me-1"></i> Considera que los módulos que dependen directamente entre sí se incluirán conjuntamente para garantizar el correcto funcionamiento.
                </div>
            </div>
        </div>

        <!-- MÓDULOS CUSTOM Y DESARROLLO -->
        <div class="col-md-6">
            <div class="glass-box p-4 h-100 border-warning border-opacity-25">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-code-slash text-warning fs-2 me-3"></i>
                    <div>
                        <h5 class="fw-bold text-white mb-0">Desarrollo de Módulos Adicionales Custom</h5>
                        <small class="">Especialmente diseñados para tu empresa</small>
                    </div>
                </div>
                <p class=" small mb-3">
                    Desarrollamos soluciones personalizadas que se integran nativamente a tu plataforma por un costo único de creación de <strong>$5,000 MXN</strong>.
                </p>
                <div class="p-2 rounded bg-warning bg-opacity-10  small border border-warning border-opacity-25">
                    <i class="bi bi-clock-history text-warning me-1"></i> <strong>Esquema de Renta:</strong> El desarrollo incluye las <strong>primeras 2 rentas sin costo</strong> adicional. A partir del 3° mes, se suma una cuota mensual de <strong>$300 MXN</strong> por concepto de mantenimiento, soporte y hosting del módulo.
                </div>
            </div>
        </div>

    </div>
</div>
</section>
<section id="ambientes">
<!-- SECCIÓN ARQUITECTURA MULTI-ALMACÉN Y DESPLIEGUE -->
<div class="my-5 pt-4">
    <div class="text-center mb-5">
        <span class="badge bg-info-subtle text-info border border-info border-opacity-25 rounded-pill px-4 py-2 mb-3 fs-7 fw-bold">
            Infraestructura & Servidores
        </span>
        <h2 class="fw-bold display-6 mb-2">Modalidades de Despliegue Multi-Almacén</h2>
        <p class=" mx-auto" style="max-width: 800px;">
            Habilite la sincronización de inventarios, traspasos entre sucursales y control logístico eligiendo el esquema técnico que mejor se adapte a las necesidades físicas de su negocio.
        </p>
    </div>

    <!-- REGLA CRÍTICA DE HOMOLOGACIÓN Y TRASPASOS -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="glass-box p-4 h-100 border-warning border-opacity-25">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-diagram-3-fill text-warning fs-3 me-3"></i>
                    <h5 class="fw-bold text-white mb-0">Homologación de Planes</h5>
                </div>
                <p class=" small mb-0">
                    Para operar la red Multi-Almacén, todas las sucursales vinculadas deben <strong>contratar exactamente el mismo nivel de plan</strong> (Plan 3 con Plan 3, o Plan 4 con Plan 4). No es posible combinar planes distintos entre almacenes, ya que la plataforma requiere compartir de forma simétrica la misma estructura de módulos e inventario.
                </p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="glass-box p-4 h-100 border-info border-opacity-25">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-arrow-left-right text-info fs-3 me-3"></i>
                    <h5 class="fw-bold text-white mb-0">Traspasos de Inventario</h5>
                </div>
                <p class=" small mb-0">
                    Las funciones exclusivas de <strong>traspaso de mercancía, solicitudes de stock y movimientos inter-sucursales</strong> se habilitan únicamente en entornos configurados con la arquitectura Multi-Almacén. Estas herramientas quedan inactivas en instalaciones de Almacén Simple o Almacén General único.
                </p>
            </div>
        </div>
    </div>

    <!-- TARJETAS DE AMBIENTES DE INSTALACIÓN -->
    <div class="row g-4 align-items-stretch py-4">
        
        <!-- AMBIENTE 1: LOCAL (ON-PREMISE) -->
      <div class="col-lg-6">
    <div class="glass-box p-4 h-100 d-flex flex-column justify-content-between border-secondary border-opacity-25">
        <div>
            <div class="d-flex justify-content-between align-items-start mb-3">
                <span class="badge bg-secondary-subtle  border border-secondary border-opacity-25 rounded-pill px-3 py-1">Ambiente On-Premise</span>
                <i class="bi bi-pc-display-horizontal  fs-2"></i>
            </div>
            <h4 class="fw-bold text-white mb-2">Despliegue Local</h4>
            <p class=" small mb-4">
                Se instala directamente en el equipo principal o servidor del cliente para trabajar dentro de su red.
            </p>

            <div class="p-3 rounded bg-black bg-opacity-30 border border-secondary border-opacity-20 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-white small fw-bold">
                        <i class="bi bi-gear-wide-connected text-info me-2"></i>Configuración DNS para Red de Trabajo
                    </span>
                    <span class="text-info fw-bold">$600 MXN <small class=" fw-normal">(Pago único)</small></span>
                </div>
                <p class=" style-italic mb-0" style="font-size: 0.78rem;">
                    * <strong>Instalación en 1 solo equipo:</strong> <strong>$0 MXN</strong> (Sin costo adicional de DNS).<br>
                    * <strong>Red de trabajo local (Planes 1 y 2):</strong> Aplica el costo de $600 MXN para enrutamiento de puertos y configuración de DNS que permita enlazar múltiples equipos.
                </p>
            </div>

            <p class="fw-bold text-white fs-7 mb-2">Costo por Almacén en Red Local:</p>
            <ul class="list-unstyled  small lh-lg mb-0" style="font-size: 0.85rem;">
                <li><i class="bi bi-check-circle-fill text-info me-2"></i><strong>Con Plan Expansión Financiera (Plan 3):</strong> $1,000 MXN / mes por cada almacén enlazado.</li>
                <li><i class="bi bi-check-circle-fill text-info me-2"></i><strong>Con Plan Corporate Total (Plan 4):</strong> $1,500 MXN / mes por cada almacén enlazado.</li>
            </ul>
        </div>
    </div>
</div>

        <!-- AMBIENTE 2: PERSONALIZADO (CLOUD HOSTING) -->
        <div class="col-lg-6">
            <div class="glass-box p-4 h-100 d-flex flex-column justify-content-between border-primary border-opacity-25" style="background: rgba(6, 182, 212, 0.03);">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge bg-info-subtle text-info border border-info border-opacity-25 rounded-pill px-3 py-1">Ambiente Cloud</span>
                        <i class="bi bi-cloud-check-fill text-info fs-2"></i>
                    </div>
                    <h4 class="fw-bold text-white mb-2">Despliegue Personalizado en Hosting</h4>
                    <p class=" small mb-4">
                        Instalación centralizada en la nube con disponibilidad 24/7. Puede operarse en infraestructura propia proporcionada por el cliente o administrada totalmente por nosotros.
                    </p>

                    <!-- OPCIÓN A: PROPORCIONA CLIENTE -->
                    <div class="p-3 rounded bg-black bg-opacity-30 border border-info border-opacity-20 mb-3">
                        <h6 class="fw-bold text-info mb-1" style="font-size: 0.85rem;"><i class="bi bi-server me-2"></i>Opción A: El Cliente proporciona el Hosting</h6>
                        <p class=" mb-0" style="font-size: 0.78rem;">
                            Proporcionamos los requerimientos técnicos del servidor. El cliente abona únicamente el plan contratado por almacén: 
                            <strong>$1,000 MXN/mes</strong> (Plan 3) o <strong>$1,500 MXN/mes</strong> (Plan 4) por cada punto de almacén.
                        </p>
                    </div>

                    <!-- OPCIÓN B: PROPORCIONADO POR NOSOTROS -->
                    <div class="p-3 rounded bg-black bg-opacity-30 border border-primary border-opacity-20 mb-3">
                        <h6 class="fw-bold text-primary-subtle mb-1" style="font-size: 0.85rem;"><i class="bi bi-hdd-stack-fill me-2"></i>Opción B: Nosotros proporcionamos e incluimos el Hosting</h6>
                        <ul class="list-unstyled  style-italic mb-0" style="font-size: 0.78rem;">
                            <li class="mb-1">• <strong>Bajo Plan 3 ($1,000/mes):</strong> 1° Almacén en $2,000 MXN/mes ($1,000 Plan + $1,000 Host) + $1,000 MXN/mes por cada almacén secundario.</li>
                            <li>• <strong>Bajo Plan 4 ($1,500/mes):</strong> 1° Almacén en $2,500 MXN/mes ($1,500 Plan + $1,000 Host) + $1,500 MXN/mes por cada almacén secundario.</li>
                        </ul>
                    </div>

                    <div class="p-2 rounded  bg-opacity-5 " style="font-size: 0.75rem;">
                        <i class="bi bi-sliders text-warning me-1"></i> <strong>Planes Personalizados:</strong> Se calcula sumando el costo del plan base a medida + costo de almacenamiento Host ($1,000) + costo individual de cada almacén adicional.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</section>
<section id="adicionalesF" class="py-4">
<!-- SECCIÓN: FUNCIONALIDADES ADICIONALES Y REGLAS DE NEGOCIO -->
<div class="my-5 pt-2">
    <div class="text-center mb-5">
        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-4 py-2 mb-3 fs-7 fw-bold">
            Ecosistema de Trabajo
        </span>
        <h2 class="fw-bold display-6 mb-2">Funcionalidades Adicionales y Capacidades Operativas</h2>
        <p class=" mx-auto" style="max-width: 800px;">
            Descubre cómo se adaptan los roles, accesos, entregas en ruta y la gestión documental dentro de la plataforma para agilizar el trabajo diario de tu equipo.
        </p>
    </div>

    <div class="row g-4">
        
        <!-- TARJETA 1: ROLES, VENDEDORES Y VENTA EN CAMPO -->
        <div class="col-md-6 col-xl-4">
            <div class="glass-box p-4 h-100 border-secondary border-opacity-25 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="p-2 rounded bg-info bg-opacity-10 text-info me-3">
                            <i class="bi bi-person-badge fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-white mb-0">Vendedores en Campo y Pedidos</h5>
                            <small class="">Venta móvil fuera de sucursal</small>
                        </div>
                    </div>
                    <p class=" small mb-3">
                        El módulo de roles permite dar de alta <strong>vendedores de campo</strong> para que levanten pedidos (cotizaciones y remisiones) de forma remota sin requerir estar físicamente en la sucursal, programando así entregas y despachos posteriores.
                    </p>
                </div>
                <div class="p-2 rounded bg-black bg-opacity-20 border border-secondary border-opacity-10 " style="font-size: 0.78rem;">
                    <i class="bi bi-geo-alt text-info me-1"></i> Flexible para personal itinerante, preventistas y agentes comerciales.
                </div>
            </div>
        </div>

        <!-- TARJETA 2: ENTREGAS, REPARTOS Y FLOTA VEHICULAR -->
        <div class="col-md-6 col-xl-4">
            <div class="glass-box p-4 h-100 border-primary border-opacity-25 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="p-2 rounded bg-primary bg-opacity-10 me-3" style="color: #a78bfa;">
                            <i class="bi bi-truck fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-white mb-0">Reparto de Mercancía y Flota</h5>
                            <small class="">Población de rutas desde historial</small>
                        </div>
                    </div>
                    <p class=" small mb-3">
                        El historial de ventas habilita la gestión de <strong>entregas y reparto de mercancía</strong>. Esta función requiere contar con el <strong>Plan 3 (Crecimiento) o superior</strong>, ya que está directamente vinculada al control de la flota vehicular.
                    </p>
                </div>
                <div class="p-2 rounded bg-primary bg-opacity-10 border border-primary border-opacity-25 text-primary-subtle" style="font-size: 0.78rem;">
                    <i class="bi bi-shield-check me-1"></i> Disponible a partir del Plan Crecimiento ($600 MXN/mes).
                </div>
            </div>
        </div>

        <!-- TARJETA 3: EXPEDIENTE DE TRABAJADORES Y VEHÍCULOS -->
        <div class="col-md-6 col-xl-4">
            <div class="glass-box p-4 h-100 border-success border-opacity-25 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="p-2 rounded bg-success bg-opacity-10 text-emerald me-3">
                            <i class="bi bi-folder-symlink fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-white mb-0">Expediente Digital del Personal</h5>
                            <small class="">Información y acceso simplificado</small>
                        </div>
                    </div>
                    <p class=" small mb-3">
                        En las secciones de <strong>vehículos y trabajadores</strong> es posible cargar el expediente completo de cada empleado (datos de contacto, documentación personal y registros asociados) para agilizar su asignación a rutas y simplificar su acceso al sistema.
                    </p>
                </div>
                <div class="p-2 rounded bg-black bg-opacity-20 border border-secondary border-opacity-10 " style="font-size: 0.78rem;">
                    <i class="bi bi-file-earmark-person text-success me-1"></i> Centraliza la información operativa de choferes y operadores.
                </div>
            </div>
        </div>

        <!-- TARJETA 4: GESTIÓN DE USUARIOS Y CONTROL DE SESIONES -->
        <div class="col-md-6 col-xl-6">
            <div class="glass-box p-4 h-100 border-warning border-opacity-25 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="p-2 rounded bg-warning bg-opacity-10 text-warning me-3">
                            <i class="bi bi-shield-lock fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-white mb-0">Políticas de Roles y Múltiple Sesión</h5>
                            <small class="">Tipos de cuenta y límites de acceso</small>
                        </div>
                    </div>
                    <p class=" small mb-2">
                        Cada rol dispone de permisos delimitados y restricciones según su función en la empresa:
                    </p>
                    <ul class="list-unstyled  small mb-3" style="font-size: 0.85rem;">
                        <li class="mb-1"><i class="bi bi-check2-square text-warning me-2"></i><strong>Planes Iniciales (1 y 2):</strong> Incluyen roles predeterminados como <i>Usuario Básico</i>, <i>Vendedor</i> o <i>Cajero</i>.</li>
                        <li class="mb-1"><i class="bi bi-check2-square text-warning me-2"></i><strong>Planes Avanzados (3, 4 y 5):</strong> Permiten libre asignación y personalización de perfiles según la necesidad.</li>
                        <li class="mb-1"><i class="bi bi-check2-square text-warning me-2"></i><strong>Sesión simultánea:</strong> Un mismo usuario puede mantener la sesión activa hasta en <strong>2 dispositivos al mismo tiempo</strong>.</li>
                    </ul>
                </div>
                <div class="p-2 rounded bg-warning bg-opacity-10 border border-warning border-opacity-25 text-warning" style="font-size: 0.78rem;">
                    <i class="bi bi-laptop me-1"></i> Permite trabajar simultáneamente desde una terminal fija de cobro y un dispositivo móvil.
                </div>
            </div>
        </div>

        <!-- TARJETA 5: GESTIÓN DOCUMENTAL Y EVIDENCIAS DE ENTREGA -->
        <div class="col-md-12 col-xl-6">
            <div class="glass-box p-4 h-100 border-info border-opacity-25 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="p-2 rounded bg-info bg-opacity-10 text-info me-3">
                            <i class="bi bi-cloud-arrow-down fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-white mb-0">Descarga Documental y Evidencia de Entregas</h5>
                            <small class="">Respaldo operativo e historial de reparto</small>
                        </div>
                    </div>
                    <p class=" small mb-2">
                        La plataforma integra herramientas para la descarga inmediata de expedientes y comprobantes clave:
                    </p>
                    <div class="row g-2  small" style="font-size: 0.85rem;">
                        <div class="col-md-6">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-1"><i class="bi bi-file-earmark-arrow-down text-info me-2"></i>Comprobantes de pago.</li>
                                <li class="mb-1"><i class="bi bi-file-earmark-arrow-down text-info me-2"></i>Hojas de entrega de mercancía.</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-1"><i class="bi bi-file-earmark-arrow-down text-info me-2"></i>Expedientes y documentos de trabajadores.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="mt-3 p-2 rounded bg-black bg-opacity-20 border border-secondary border-opacity-10">
                        <p class="text-white small mb-0 fw-semibold">
                            <i class="bi bi-camera me-1 text-info"></i> Módulo "Mis Repartos":
                        </p>
                        <p class=" small mb-0" style="font-size: 0.8rem;">
                            Permite al repartidor subir <strong>evidencia fotográfica o firma digital</strong> al momento de entregar el material en destino.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
                        </section>
<section id="preguntas" class="py-4">
        <!-- TECNOLOGÍAS USADAS -->
     

        <!-- PREGUNTAS FRECUENTES (FAQ) -->
        <div class="my-5 pt-4 mx-auto  " style="max-width: 850px;">
            <h2 class="text-center fw-bold mb-4">Preguntas Frecuentes del Sistema</h2>
            
            <div class="accordion" id="faqAccordion">
                
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            ¿Cómo funciona la modalidad de renta mensual?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Accedes a la plataforma pagando una suscripción mensual fija ($400, $600 o $1,000 MXN según el plan elegido). La renta incluye soporte técnico continuo, mantenimiento, actualizaciones de la plataforma y el número de usuarios base estipulados en tu paquete.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            ¿Puedo agregar módulos o usuarios individuales si mi plan no los incluye?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            ¡Sí! Puedes armar un paquete personalizado. Cada usuario adicional tiene un costo de <strong>$200 MXN/mes</strong> y cualquier módulo extra fuera de tu plan puede integrarse por <strong>$300 MXN/mes</strong> adicionales.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            ¿Cómo funciona el desarrollo de un módulo personalizado ?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Si requieres una función exclusiva para tu negocio, la desarrollamos por un costo base, Para tu comodidad, este monto se puede diluir o segmentar directamente en el precio de la renta mensual de tu plan, quedando activo como un módulo en arrendamiento con mantenimiento garantizado.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            ¿Cómo opera la gestión multi-almacén en tiempo real?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            El sistema asigna la sesión activa al almacén del usuario y actualiza existencias inmediatamente ante cualquier salida, traspaso o ingreso. Además, permite parametrizar peticiones filtradas mediante `almacen_id` para consultar el stock de cualquier sucursal al instante.
                        </div>
                    </div>
                </div>

            </div>
        </div>
 </section>
<!-- SECCIÓN: CONTACTO & ASESORÍA -->
<section id="contacto" class="py-5 position-relative">
    <div class="container py-4">
        <div class="glass-box p-4 p-md-5 mx-auto position-relative overflow-hidden" style="max-width: 950px; border-color: rgba(56, 189, 248, 0.25);">
            
            <!-- Resplandor ambiental decorativo -->
            <div class="position-absolute top-0 start-50 translate-middle-x rounded-circle" 
                 style="width: 300px; height: 120px; background: radial-gradient(circle, rgba(139, 92, 246, 0.25) 0%, transparent 70%); filter: blur(35px); pointer-events: none;">
            </div>

            <!-- Encabezado de Sección -->
            <div class="text-center mb-5 position-relative z-1">
                <span class="submodulo-badge mb-3 px-3 py-2" style="background: rgba(139, 92, 246, 0.12); border-color: rgba(139, 92, 246, 0.35); color: #c084fc;">
                    <i class="bi bi-calendar2-check-fill fs-6"></i> Atención Personalizada & Soporte
                </span>
                <h2 class="fw-extrabold text-white mb-2 fs-2">¿Necesitas Asesoría o una Demostración?</h2>
                <p class="text-white-50 mx-auto fs-6" style="max-width: 620px;">
                    Agenda una sesión guiada con nosotros o solicita consultoría técnica para la implementación de tu sistema multi-almacén.
                </p>
            </div>

            <!-- Grilla Principal: Agendar Cita y Asesoría -->
            <div class="row g-4 mb-4 position-relative z-1">
                
                <!-- Tarjeta: Agendar Cita / Demo -->
                <div class="col-12 col-md-6">
                    <div class="p-4 rounded-4 h-100 d-flex flex-column justify-content-between transition-all" 
                         style="background: rgba(139, 92, 246, 0.05); border: 1px solid rgba(139, 92, 246, 0.25); backdrop-filter: blur(10px);">
                        <div>
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="rounded-3 p-3 text-white" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9); box-shadow: 0 8px 20px rgba(139, 92, 246, 0.3);">
                                    <i class="bi bi-calendar-event fs-4"></i>
                                </div>
                                <div>
                                    <h3 class="fs-5 fw-bold text-white mb-0">Agendar Cita / Demo</h3>
                                    <span class="text-white-50 small">Sesión en vivo de 30 min</span>
                                </div>
                            </div>
                            <p class="text-white-50 small mb-4">
                                Revisa el funcionamiento del sistema en tiempo real, resuelve dudas específicas de tu negocio y evalúa la integración.
                            </p>
                        </div>
                        <a href="https://wa.me/525523789029?text=Hola,%20me%20gustar%C3%ADa%20agendar%20una%20cita%20o%20demostraci%C3%B3n%20del%20sistema." 
                           target="_blank" 
                           class="btn btn-custom w-100 d-flex align-items-center justify-content-center gap-2"
                           style="background: linear-gradient(135deg, #8b5cf6, #6366f1);">
                            <i class="bi bi-calendar-plus"></i> Agendar Sesión
                        </a>
                    </div>
                </div>

                <!-- Tarjeta: Solicitar Asesoría Técnica -->
                <div class="col-12 col-md-6">
                    <div class="p-4 rounded-4 h-100 d-flex flex-column justify-content-between transition-all" 
                         style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.25); backdrop-filter: blur(10px);">
                        <div>
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="rounded-3 p-3 text-white" style="background: linear-gradient(135deg, #06b6d4, #0284c7); box-shadow: 0 8px 20px rgba(6, 182, 212, 0.3);">
                                    <i class="bi bi-headset fs-4"></i>
                                </div>
                                <div>
                                    <h3 class="fs-5 fw-bold text-white mb-0">Solicitar Asesoría</h3>
                                    <span class="text-white-50 small">Consultoría en desarrollo</span>
                                </div>
                            </div>
                            <p class="text-white-50 small mb-4">
                                Análisis de arquitectura, requerimientos a medida, nuevos módulos o migración de bases de datos existentes.
                            </p>
                        </div>
                        <a href="https://wa.me/525523789029?text=Hola,%20requiero%20asesor%C3%ADa%20t%C3%A9cnica%20para%20un%20proyecto/m%C3%B3dulo%20especial." 
                           target="_blank" 
                           class="btn btn-custom w-100 d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-chat-left-dots"></i> Solicitar Asesoría
                        </a>
                    </div>
                </div>

            </div>

            <!-- Canales Rápido de Comunicación Directa -->
            <div class="row g-3 justify-content-center align-items-center position-relative z-1 pt-2">
                <div class="col-12 col-sm-6 text-center text-sm-start">
                    <span class="text-white-50 small d-block">¿Contacto rápido?</span>
                    <a href="tel:525523789029" class="text-white text-decoration-none fw-bold fs-6">
                        <i class="bi bi-telephone text-info me-2"></i>55 23 78 90 29
                    </a>
                </div>
                <div class="col-12 col-sm-6 text-center text-sm-end">
                    <a href="https://wa.me/525523789029" target="_blank" class="btn btn-sm btn-outline-light rounded-pill px-4 border-white-50">
                        <i class="bi bi-whatsapp text-success me-1"></i> Mensaje Directo
                    </a>
                </div>
            </div>

            <!-- Banner Inferior de Disponibilidad -->
            <div class="mt-4 pt-3 border-top border-white border-opacity-10 text-center position-relative z-1">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08);">
                    <span class="spinner-grow spinner-grow-sm text-success" style="width: 8px; height: 8px;" role="status"></span>
                    <span class="text-white-50 small">Soporte y atención directa con el desarrollador principal</span>
                </div>
            </div>

        </div>
    </div>
</section>

        <!-- FOOTER -->
        <footer class="text-center  border-top border-secondary border-opacity-10 pt-4 mt-5 fs-7">
            © <?php echo date('Y'); ?> <span class="text-white fw-semibold">MYVET SISTEM</span> — Arquitectura y Desarrollo por J y Saúl.
        </footer>

    </div>

    <!-- Bootstrap JS -->
     
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
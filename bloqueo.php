<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Suspendido - MYVET ERP</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts (Plus Jakarta Sans) -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <?php
// Capturar el ID de la URL y sanitizarlo como entero
$almacen_id = isset($_GET['almacen']) ? intval($_GET['almacen']) : 0;
?>
    <style>

        :root {
            --bg-deep: #050811;
            --glass-bg: rgba(15, 23, 42, 0.65);
            --glass-border: rgba(255, 255, 255, 0.12);
            --glass-highlight: rgba(255, 255, 255, 0.05);
            --accent-red: #f43f5e;
            --accent-cyan: #06b6d4;
            --accent-gold: #f59e0b;
        }

        body {
            background-color: var(--bg-deep);
            color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        /* ----- FONDO ANIMADO SUTIL DE ULTRA LUJO ----- */
        .background-glows {
            position: fixed;
            width: 100vw;
            height: 100vh;
            top: 0;
            left: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.35;
            animation: floatOrb 20s infinite ease-in-out alternate;
        }

        .orb-1 {
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, #e11d48, transparent 70%);
            top: -10%;
            left: -10%;
            animation-duration: 18s;
        }

        .orb-2 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, #0284c7, transparent 70%);
            bottom: -15%;
            right: -10%;
            animation-duration: 22s;
        }

        .orb-3 {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, #4f46e5, transparent 70%);
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-duration: 15s;
        }

        @keyframes floatOrb {
            0% { transform: translate(0px, 0px) scale(1); }
            50% { transform: translate(60px, -40px) scale(1.15); }
            100% { transform: translate(-40px, 50px) scale(0.95); }
        }

        /* ----- TARJETA DE CRISTAL (CRYSTAL GLASS) ----- */
        .crystal-card {
            position: relative;
            z-index: 10;
            background: var(--glass-bg);
            backdrop-filter: blur(30px) saturate(190%);
            -webkit-backdrop-filter: blur(30px) saturate(190%);
            border: 1px solid var(--glass-border);
            border-radius: 28px;
            box-shadow: 
                0 30px 60px -12px rgba(0, 0, 0, 0.7),
                inset 0 1px 0 0 rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }

        /* Reflejo superior del cristal */
        .crystal-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -50%;
            width: 200%;
            height: 100%;
            background: linear-gradient(
                115deg,
                transparent 40%,
                rgba(255, 255, 255, 0.03) 45%,
                rgba(255, 255, 255, 0.08) 50%,
                transparent 55%
            );
            pointer-events: none;
        }

        /* Badge de Alerta */
        .alert-badge {
            background: rgba(244, 63, 94, 0.12);
            border: 1px solid rgba(244, 63, 94, 0.3);
            color: #fda4af;
            font-size: 0.75rem;
            letter-spacing: 1.5px;
            font-weight: 700;
            padding: 8px 18px;
            border-radius: 50px;
            box-shadow: 0 0 20px rgba(244, 63, 94, 0.15);
        }

        /* Módulos de Pago Cristalizados */
        .payment-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .payment-card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.18);
            transform: translateY(-2px);
        }

        /* Código de Barras OXXO Estilizado */
        .barcode-box {
            background: #ffffff;
            border-radius: 12px;
            padding: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .barcode-lines {
            height: 48px;
            background: repeating-linear-gradient(
                90deg,
                #0f172a,
                #0f172a 2px,
                #ffffff 2px,
                #ffffff 5px,
                #0f172a 5px,
                #0f172a 9px,
                #ffffff 9px,
                #ffffff 10px
            );
            border-radius: 4px;
        }

        /* Botón Oficial WhatsApp */
        .btn-whatsapp {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff !important;
            font-weight: 700;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
            transition: all 0.3s ease;
        }

        .btn-whatsapp:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(16, 185, 129, 0.6);
            background: linear-gradient(135deg, #34d399 0%, #059669 100%);
        }
    </style>
</head>
<body class="p-3 p-md-4">

    <!-- FONDO ANIMADO SUTIL -->
    <div class="background-glows">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <!-- TARJETA PRINCIPAL DE CRISTAL -->
    <div class="container" style="max-width: 650px;">
        <div class="crystal-card p-4 p-md-5">
            
            <!-- LOGO Y CABECERA -->
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center" 
                         style="background: rgba(244, 63, 94, 0.15); border: 1px solid rgba(244, 63, 94, 0.3);">
                        <i class="bi bi-shield-lock-fill text-danger fs-4"></i>
                    </div>
                    <span class="fs-4 fw-extrabold tracking-tight text-white">MYVET <span class="fw-light text-secondary">ERP</span></span>
                </div>

                <div>
                    <span class="badge alert-badge text-uppercase">
                        <i class="bi bi-exclamation-octagon-fill me-1"></i> Suspensión Administrativa
                    </span>
                </div>

                <h3 class="fw-bold mt-3 text-white">Acceso al sistema suspendido</h3>
                <p class="text-secondary small mb-0 fs-6">
                    Estimado usuario, el servicio de su plataforma ha sido pausado por falta de pago. Por favor realice su liquidación para reactivar sus almacenes e información de inmediato.
                </p>
            </div>

            <!-- MÓDULO 1: TRANSFERENCIA / DÉBITO -->
            <div class="payment-card p-3 p-md-4 mb-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-bank text-info fs-5"></i>
                        <h6 class="fw-semibold mb-0 text-white">Transferencia / Depósito Bancario</h6>
                    </div>
                    <span class="badge bg-primary bg-opacity-20 text-info border border-info border-opacity-20 px-2 py-1 fs-7">BBVA</span>
                </div>

                <div class="row g-2">
                    <div class="col-12 col-sm-7">
                        <span class="text-secondary d-block fs-7">Tarjeta / CLABE Interbancaria:</span>
                        <span class="font-monospace text-white fw-bold fs-6 tracking-wide">4152 3138 9029 4510</span>
                    </div>
                    <div class="col-12 col-sm-5 text-sm-end">
                        <span class="text-secondary d-block fs-7">Titular: JSEA</span>
                        <span class="text-white fw-semibold small">Pago ref <?=$almacen_id ?> </span>
                    </div>
                </div>
            </div>

            <!-- MÓDULO 2: PAGO OXXO -->
            <div class="payment-card p-3 p-md-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-shop text-warning fs-5"></i>
                        <h6 class="fw-semibold mb-0 text-white">Pago OXXO / OXXO Pay</h6>
                    </div>
                    <span class="badge bg-warning bg-opacity-20 text-warning border border-warning border-opacity-20 px-2 py-1 fs-7">OXXO</span>
                </div>

                <div class="barcode-box text-center mb-2">
                    <div class="barcode-lines mb-2"></div>
                    <span class="font-monospace text-dark fw-bold tracking-widest fs-5">9324 - 1029 - 8472 - 0012</span>
                </div>
                <small class="text-secondary d-block text-center fs-7">Mencione al cajero que realizará un pago de servicio con esta referencia.</small>
            </div>

            <!-- PIE DE PÁGINA Y WHATSAPP -->
            <div class="text-center pt-2 border-top border-white border-opacity-10">
                <p class="text-secondary small mb-3">
                    Una vez realizado el depósito o transferencia, envíe la captura o foto del ticket para habilitar su sistema.
                </p>

                <a href="https://wa.me/525523789029?text=Hola,%20adjunto%20mi%20comprobante%20de%20pago%20para%20la%20reactivaci%C3%B3n%20de%20mi%20cuenta." 
                   target="_blank" 
                   class="btn btn-whatsapp rounded-pill px-4 py-3 w-100 d-flex align-items-center justify-content-center gap-2 fs-6">
                    <i class="bi bi-whatsapp fs-5"></i> Enviar Comprobante de Pago
                </a>
            </div>

        </div>
    </div>

</body>
</html>
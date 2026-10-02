<?php
session_start();
$_SESSION = [];
session_destroy();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerrando sesión | myvet</title>

    <link rel="icon" type="image/png" href="/myvet/public/assets/logo.png">
    <link rel="shortcut icon" href="/myvet/public/assets/logo.ico" type="image/x-icon">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ============================================================
           RESET
           ============================================================ */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            /* Colores tipo Microsoft / Apple vivos */
            --c-red: #ff3b30;
            --c-red-2: #d70015;
            --c-green: #34c759;
            --c-green-2: #248a3d;
            --c-blue: #007aff;
            --c-blue-2: #0051d5;
            --c-yellow: #ffcc00;
            --c-yellow-2: #d9a800;
        }

        html,
        body {
            height: 100%;
            width: 100%;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', sans-serif;
            background: #000;
            color: #fff;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            letter-spacing: -0.011em;
        }

        /* ============================================================
           FONDO (auroras de colores)
           ============================================================ */
        .bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(ellipse at 15% 20%, rgba(255, 59, 48, 0.18) 0%, transparent 45%),
                radial-gradient(ellipse at 85% 25%, rgba(255, 204, 0, 0.15) 0%, transparent 45%),
                radial-gradient(ellipse at 15% 85%, rgba(0, 122, 255, 0.18) 0%, transparent 45%),
                radial-gradient(ellipse at 85% 85%, rgba(52, 199, 89, 0.15) 0%, transparent 45%),
                radial-gradient(ellipse at 50% 50%, rgba(30, 30, 40, 1) 0%, #05050a 100%);
        }

        /* ============================================================
           ESCENA
           ============================================================ */
        .scene {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 46px;
            animation: sceneIn 1s cubic-bezier(0.22, 1, 0.36, 1);
        }

        @keyframes sceneIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* ============================================================
           CONTENEDOR DE LA RULETA
           ============================================================ */
        .wheel-wrap {
            position: relative;
            width: 200px;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            perspective: 1000px;
        }

        /* Halo multicolor detrás */
        .wheel-wrap::before {
            content: '';
            position: absolute;
            inset: -40px;
            border-radius: 50%;
            background:
                conic-gradient(from 0deg,
                    rgba(255, 59, 48, 0.4),
                    rgba(255, 204, 0, 0.4),
                    rgba(52, 199, 89, 0.4),
                    rgba(0, 122, 255, 0.4),
                    rgba(255, 59, 48, 0.4));
            filter: blur(40px);
            opacity: 0.6;
            animation: haloSpin 6s linear infinite, haloPulse 3s ease-in-out infinite;
            z-index: 0;
            pointer-events: none;
        }

        @keyframes haloSpin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes haloPulse {

            0%,
            100% {
                opacity: 0.5;
            }

            50% {
                opacity: 0.85;
            }
        }

        /* ============================================================
           RULETA — 4 CUADRANTES DE COLORES
           ============================================================ */
        .wheel {
            position: relative;
            width: 180px;
            height: 180px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: 8px;
            transform-style: preserve-3d;
            animation: wheelSpin 4.5s cubic-bezier(0.6, 0, 0.4, 1) forwards;
            animation-delay: 0.4s;
            will-change: transform;
        }

        @keyframes wheelSpin {
            0% {
                transform: rotate(0deg) scale(1);
            }

            15% {
                transform: rotate(-15deg) scale(1.05);
            }

            70% {
                transform: rotate(540deg) scale(1);
            }

            85% {
                transform: rotate(680deg) scale(0.95);
            }

            100% {
                transform: rotate(720deg) scale(0.9);
            }
        }

        /* ============================================================
           CADA CUADRANTE (colores distintos)
           ============================================================ */
        .quad {
            position: relative;
            border-radius: 14px;
            overflow: hidden;
            animation: quadClose 0.9s cubic-bezier(0.6, 0, 0.4, 1) forwards;
            will-change: opacity, transform, filter;
        }

        /* ---- Cuadrante 1: ROJO (arriba izquierda) ---- */
        .quad:nth-child(1) {
            background: linear-gradient(145deg, var(--c-red) 0%, var(--c-red-2) 100%);
            box-shadow:
                0 8px 24px rgba(255, 59, 48, 0.45),
                inset 0 1px 0 rgba(255, 255, 255, 0.3),
                inset 0 -2px 6px rgba(0, 0, 0, 0.2);
            animation-delay: 3.2s;
            transform-origin: top left;
        }

        /* ---- Cuadrante 2: AMARILLO (arriba derecha) ---- */
        .quad:nth-child(2) {
            background: linear-gradient(145deg, var(--c-yellow) 0%, var(--c-yellow-2) 100%);
            box-shadow:
                0 8px 24px rgba(255, 204, 0, 0.45),
                inset 0 1px 0 rgba(255, 255, 255, 0.3),
                inset 0 -2px 6px rgba(0, 0, 0, 0.2);
            animation-delay: 3.5s;
            transform-origin: top right;
        }

        /* ---- Cuadrante 3: AZUL (abajo izquierda) ---- */
        .quad:nth-child(3) {
            background: linear-gradient(145deg, var(--c-blue) 0%, var(--c-blue-2) 100%);
            box-shadow:
                0 8px 24px rgba(0, 122, 255, 0.45),
                inset 0 1px 0 rgba(255, 255, 255, 0.3),
                inset 0 -2px 6px rgba(0, 0, 0, 0.2);
            animation-delay: 4.1s;
            transform-origin: bottom left;
        }

        /* ---- Cuadrante 4: VERDE (abajo derecha) ---- */
        .quad:nth-child(4) {
            background: linear-gradient(145deg, var(--c-green) 0%, var(--c-green-2) 100%);
            box-shadow:
                0 8px 24px rgba(52, 199, 89, 0.45),
                inset 0 1px 0 rgba(255, 255, 255, 0.3),
                inset 0 -2px 6px rgba(0, 0, 0, 0.2);
            animation-delay: 3.8s;
            transform-origin: bottom right;
        }

        /* Animación de cierre */
        @keyframes quadClose {
            0% {
                opacity: 1;
                transform: scale(1) rotate(0deg);
                filter: blur(0);
            }

            40% {
                opacity: 1;
                transform: scale(1.05) rotate(3deg);
                filter: blur(0);
            }

            100% {
                opacity: 0;
                transform: scale(0.15) rotate(-25deg);
                filter: blur(12px);
            }
        }

        /* Brillo superior cristal */
        .quad::before {
            content: '';
            position: absolute;
            top: 0;
            left: 10%;
            right: 10%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.95), transparent);
            z-index: 2;
        }

        /* Reflejo diagonal interno */
        .quad::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg,
                    rgba(255, 255, 255, 0.25) 0%,
                    transparent 40%,
                    transparent 60%,
                    rgba(0, 0, 0, 0.2) 100%);
            z-index: 1;
        }

        /* ============================================================
           CENTRO BRILLANTE
           ============================================================ */
        .wheel-center {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 18px;
            height: 18px;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            background: radial-gradient(circle at 35% 35%, #ffffff 0%, #e0e0e0 40%, #999 100%);
            box-shadow:
                0 0 16px rgba(255, 255, 255, 0.9),
                0 0 32px rgba(255, 255, 255, 0.5),
                inset 0 1px 2px rgba(0, 0, 0, 0.15);
            z-index: 10;
            animation: centerGlow 1.5s ease-in-out infinite;
        }

        @keyframes centerGlow {

            0%,
            100% {
                box-shadow:
                    0 0 16px rgba(255, 255, 255, 0.9),
                    0 0 32px rgba(255, 255, 255, 0.5),
                    inset 0 1px 2px rgba(0, 0, 0, 0.15);
            }

            50% {
                box-shadow:
                    0 0 24px rgba(255, 255, 255, 1),
                    0 0 48px rgba(255, 255, 255, 0.7),
                    inset 0 1px 2px rgba(0, 0, 0, 0.15);
            }
        }

        /* ============================================================
           TEXTO
           ============================================================ */
        .status {
            text-align: center;
            animation: fadeIn 1s ease-out 0.4s backwards;
        }

        .status-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #fff;
            letter-spacing: -0.025em;
            margin-bottom: 8px;
            background: linear-gradient(90deg,
                    #ff3b30 0%,
                    #ffcc00 33%,
                    #34c759 66%,
                    #007aff 100%);
            background-size: 200% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: textShine 4s linear infinite;
        }

        @keyframes textShine {
            0% {
                background-position: 0% 50%;
            }

            100% {
                background-position: 200% 50%;
            }
        }

        .status-subtitle {
            font-size: 0.88rem;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.55);
            letter-spacing: -0.01em;
        }

        .status-subtitle .dot {
            display: inline-block;
            animation: dotBlink 1.4s ease-in-out infinite;
        }

        .status-subtitle .dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .status-subtitle .dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes dotBlink {

            0%,
            60%,
            100% {
                opacity: 0.25;
            }

            30% {
                opacity: 1;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ============================================================
           BARRA DE PROGRESO MULTICOLOR
           ============================================================ */
        .progress {
            width: 220px;
            height: 3px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            overflow: hidden;
            animation: fadeIn 1s ease-out 0.6s backwards;
        }

        .progress-bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg,
                    #ff3b30 0%,
                    #ffcc00 33%,
                    #34c759 66%,
                    #007aff 100%);
            background-size: 200% 100%;
            border-radius: 3px;
            animation:
                progressFill 6s linear forwards,
                barShine 2s linear infinite;
            animation-delay: 0.4s, 0.4s;
            box-shadow: 0 0 12px rgba(255, 255, 255, 0.4);
        }

        @keyframes progressFill {
            from {
                width: 0%;
            }

            to {
                width: 100%;
            }
        }

        @keyframes barShine {
            0% {
                background-position: 0% 50%;
            }

            100% {
                background-position: 200% 50%;
            }
        }

        /* ============================================================
           MARCA
           ============================================================ */
        .brand {
            position: fixed;
            bottom: 28px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.72rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.4);
            letter-spacing: 0.5px;
            text-transform: uppercase;
            animation: fadeIn 1s ease-out 0.8s backwards;
        }

        .brand-dot {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
        }

        /* ============================================================
           FUNDIDO FINAL
           ============================================================ */
        .curtain {
            position: fixed;
            inset: 0;
            z-index: 100;
            background: #000;
            opacity: 0;
            pointer-events: none;
            animation: curtainFade 1s ease-in forwards;
            animation-delay: 5.8s;
        }

        @keyframes curtainFade {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* ============================================================
           RESPONSIVE
           ============================================================ */
        @media (max-width: 480px) {
            .wheel-wrap {
                width: 160px;
                height: 160px;
            }

            .wheel {
                width: 150px;
                height: 150px;
                gap: 6px;
            }

            .quad {
                border-radius: 12px;
            }

            .status-title {
                font-size: 1.25rem;
            }

            .scene {
                gap: 38px;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>

<body>

    <div class="bg"></div>

    <div class="scene">

        <!-- RULETA -->
        <div class="wheel-wrap">
            <div class="wheel">
                <div class="quad"></div>
                <div class="quad"></div>
                <div class="quad"></div>
                <div class="quad"></div>
            </div>
            <div class="wheel-center"></div>
        </div>

        <!-- TEXTO -->
        <div class="status">
            <div class="status-title">Cerrando sesión</div>
            <div class="status-subtitle">
                Finalizando de forma segura
                <span class="dot">.</span><span class="dot">.</span><span class="dot">.</span>
            </div>
        </div>

        <!-- BARRA -->
        <div class="progress">
            <div class="progress-bar"></div>
        </div>

    </div>

    <!-- Marca -->
    <div class="brand">
        <span>myvet</span>
        <span class="brand-dot"></span>
        <span><?php echo date('Y'); ?></span>
    </div>

    <div class="curtain"></div>

    <script>
        setTimeout(() => {
            window.location.href = 'index.php';
        }, 6800);
    </script>

</body>

</html>
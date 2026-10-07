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

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

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
            --c-accent: #0a84ff;
            --c-accent-soft: #5ac8fa;
            --c-accent-deep: #003d99;
            --c-glow: rgba(10, 132, 255, 0.55);
        }

        html,
        body {
            height: 100%;
            width: 100%;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', sans-serif;
            background: #05060a;
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
           FONDO — auroras azules suaves
           ============================================================ */
        .bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(ellipse at 25% 30%, rgba(10, 132, 255, 0.15) 0%, transparent 55%),
                radial-gradient(ellipse at 75% 25%, rgba(90, 200, 250, 0.10) 0%, transparent 55%),
                radial-gradient(ellipse at 30% 75%, rgba(0, 61, 153, 0.18) 0%, transparent 55%),
                radial-gradient(ellipse at 75% 80%, rgba(10, 132, 255, 0.10) 0%, transparent 55%),
                radial-gradient(ellipse at 50% 50%, #0a0d18 0%, #05060a 100%);
            animation: bgBreath 8s ease-in-out infinite;
        }

        @keyframes bgBreath {

            0%,
            100% {
                filter: brightness(1);
            }

            50% {
                filter: brightness(1.15);
            }
        }

        /* ============================================================
           ESCENA CENTRAL
           ============================================================ */
        .scene {
            position: relative;
            z-index: 2;
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
                transform: scale(0.94);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* ============================================================
           NÚCLEO DE ONDAS + PARTÍCULAS
           ============================================================ */
        .pulse-wrap {
            position: relative;
            width: 220px;
            height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* --- Núcleo central (esfera luminosa) --- */
        .core {
            position: absolute;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 30%, #ffffff 0%, #5ac8fa 40%, #0a84ff 80%, #003d99 100%);
            box-shadow:
                0 0 20px rgba(90, 200, 250, 0.9),
                0 0 40px rgba(10, 132, 255, 0.7),
                0 0 80px rgba(10, 132, 255, 0.4),
                inset 0 0 8px rgba(255, 255, 255, 0.6);
            animation: corePulse 1.6s ease-in-out infinite;
            z-index: 5;
        }

        @keyframes corePulse {

            0%,
            100% {
                transform: scale(1);
                box-shadow:
                    0 0 20px rgba(90, 200, 250, 0.9),
                    0 0 40px rgba(10, 132, 255, 0.7),
                    0 0 80px rgba(10, 132, 255, 0.4),
                    inset 0 0 8px rgba(255, 255, 255, 0.6);
            }

            50% {
                transform: scale(1.15);
                box-shadow:
                    0 0 28px rgba(90, 200, 250, 1),
                    0 0 60px rgba(10, 132, 255, 0.9),
                    0 0 110px rgba(10, 132, 255, 0.55),
                    inset 0 0 12px rgba(255, 255, 255, 0.8);
            }
        }

        /* --- Anillos orbitales girando --- */
        .orbit {
            position: absolute;
            border-radius: 50%;
            border: 1px solid transparent;
            border-top-color: rgba(90, 200, 250, 0.7);
            border-right-color: rgba(10, 132, 255, 0.35);
            pointer-events: none;
        }

        .orbit--1 {
            width: 70px;
            height: 70px;
            animation: orbitSpin 2.4s linear infinite;
        }

        .orbit--2 {
            width: 110px;
            height: 110px;
            border-top-color: rgba(10, 132, 255, 0.6);
            border-right-color: rgba(90, 200, 250, 0.25);
            animation: orbitSpin 3.6s linear infinite reverse;
        }

        .orbit--3 {
            width: 160px;
            height: 160px;
            border-top-color: rgba(90, 200, 250, 0.35);
            border-right-color: rgba(10, 132, 255, 0.15);
            animation: orbitSpin 5.2s linear infinite;
        }

        @keyframes orbitSpin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* --- Ondas expansivas (los "cuadros" reemplazados) --- */
        .wave {
            position: absolute;
            border-radius: 50%;
            border: 1.5px solid rgba(10, 132, 255, 0.7);
            opacity: 0;
            animation: waveExpand 2.8s cubic-bezier(0.15, 0.6, 0.35, 1) infinite;
            pointer-events: none;
        }

        .wave:nth-child(4) {
            animation-delay: 0s;
        }

        .wave:nth-child(5) {
            animation-delay: 0.7s;
        }

        .wave:nth-child(6) {
            animation-delay: 1.4s;
        }

        .wave:nth-child(7) {
            animation-delay: 2.1s;
        }

        @keyframes waveExpand {
            0% {
                width: 26px;
                height: 26px;
                opacity: 0.9;
                border-width: 2px;
                border-color: rgba(90, 200, 250, 0.9);
            }

            70% {
                opacity: 0.35;
                border-color: rgba(10, 132, 255, 0.5);
            }

            100% {
                width: 220px;
                height: 220px;
                opacity: 0;
                border-width: 0.5px;
                border-color: rgba(0, 61, 153, 0);
            }
        }

        /* --- Partículas ascendentes --- */
        .particles {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            bottom: 50%;
            left: 50%;
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: rgba(90, 200, 250, 0.9);
            box-shadow: 0 0 6px rgba(90, 200, 250, 0.8);
            opacity: 0;
            animation: particleRise 3.2s ease-out infinite;
        }

        @keyframes particleRise {
            0% {
                opacity: 0;
                transform: translate(0, 0) scale(0.5);
            }

            15% {
                opacity: 1;
            }

            100% {
                opacity: 0;
                transform:
                    translate(var(--px, 0), calc(-1 * var(--py, 100px))) scale(0.2);
            }
        }

        /* Generamos posiciones dispersas con nth-child */
        .particle:nth-child(1) {
            --px: -90px;
            --py: 130px;
            animation-delay: 0.0s;
        }

        .particle:nth-child(2) {
            --px: 70px;
            --py: 140px;
            animation-delay: 0.3s;
        }

        .particle:nth-child(3) {
            --px: -40px;
            --py: 160px;
            animation-delay: 0.6s;
        }

        .particle:nth-child(4) {
            --px: 100px;
            --py: 120px;
            animation-delay: 0.9s;
        }

        .particle:nth-child(5) {
            --px: -110px;
            --py: 150px;
            animation-delay: 1.2s;
        }

        .particle:nth-child(6) {
            --px: 20px;
            --py: 170px;
            animation-delay: 1.5s;
        }

        .particle:nth-child(7) {
            --px: -70px;
            --py: 110px;
            animation-delay: 1.8s;
        }

        .particle:nth-child(8) {
            --px: 90px;
            --py: 160px;
            animation-delay: 2.1s;
        }

        .particle:nth-child(9) {
            --px: -20px;
            --py: 140px;
            animation-delay: 2.4s;
        }

        .particle:nth-child(10) {
            --px: 50px;
            --py: 130px;
            animation-delay: 2.7s;
        }

        .particle:nth-child(11) {
            --px: -100px;
            --py: 120px;
            animation-delay: 3.0s;
        }

        .particle:nth-child(12) {
            --px: 80px;
            --py: 150px;
            animation-delay: 3.3s;
        }

        .particle:nth-child(13) {
            --px: -55px;
            --py: 170px;
            animation-delay: 3.6s;
        }

        .particle:nth-child(14) {
            --px: 40px;
            --py: 110px;
            animation-delay: 3.9s;
        }

        .particle:nth-child(15) {
            --px: -85px;
            --py: 140px;
            animation-delay: 4.2s;
        }

        .particle:nth-child(16) {
            --px: 65px;
            --py: 130px;
            animation-delay: 4.5s;
        }

        /* Colores alternos suaves para dar vida */
        .particle:nth-child(3n) {
            background: rgba(10, 132, 255, 0.9);
            box-shadow: 0 0 8px rgba(10, 132, 255, 0.8);
        }

        .particle:nth-child(3n+2) {
            background: rgba(255, 255, 255, 0.85);
            box-shadow: 0 0 6px rgba(255, 255, 255, 0.7);
        }

        /* ============================================================
           TEXTO DE ESTADO
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
                    #ffffff 0%,
                    #5ac8fa 35%,
                    #0a84ff 70%,
                    #ffffff 100%);
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
           BARRA DE PROGRESO
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
                    #5ac8fa 0%,
                    #0a84ff 50%,
                    #0066cc 100%);
            background-size: 200% 100%;
            border-radius: 3px;
            animation:
                progressFill 6s linear forwards,
                barShine 2s linear infinite;
            animation-delay: 0.4s, 0.4s;
            box-shadow: 0 0 12px rgba(10, 132, 255, 0.6);
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
           MARCA (pie)
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
           TEXTO METÁLICO FINAL — JSEA / Nos vemos pronto
           ============================================================ */
        .farewell {
            position: fixed;
            inset: 0;
            z-index: 90;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 14px;
            pointer-events: none;
            opacity: 0;
            animation: farewellIn 1.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            animation-delay: 4.2s;
        }

        @keyframes farewellIn {
            0% {
                opacity: 0;
                transform: translateY(14px) scale(0.96);
                filter: blur(6px);
            }

            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
                filter: blur(0);
            }
        }

        .farewell-brand {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', sans-serif;
            font-size: clamp(2.4rem, 6vw, 4.2rem);
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            line-height: 1;
            text-align: center;

            background: linear-gradient(100deg,
                    #6b6b6b 0%,
                    #b8b8b8 18%,
                    #ffffff 30%,
                    #e8e8e8 40%,
                    #8a8a8a 55%,
                    #ffffff 68%,
                    #c8c8c8 82%,
                    #6b6b6b 100%);
            background-size: 250% 100%;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;

            filter:
                drop-shadow(0 1px 0 rgba(255, 255, 255, 0.15)) drop-shadow(0 2px 8px rgba(0, 0, 0, 0.7)) drop-shadow(0 6px 24px rgba(10, 132, 255, 0.15));

            animation: metalShine 3.2s linear infinite;
            animation-delay: 5.4s;
        }

        @keyframes metalShine {
            0% {
                background-position: 250% 50%;
            }

            100% {
                background-position: -50% 50%;
            }
        }

        .farewell-brand::after {
            content: '';
            display: block;
            width: 60%;
            height: 1px;
            margin: 14px auto 0;
            background: linear-gradient(90deg,
                    transparent 0%,
                    rgba(255, 255, 255, 0.4) 50%,
                    transparent 100%);
            opacity: 0.6;
        }

        .farewell-msg {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', sans-serif;
            font-size: clamp(0.8rem, 1.6vw, 1rem);
            font-weight: 400;
            letter-spacing: 0.42em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.55);
            text-align: center;

            opacity: 0;
            animation: msgIn 1.2s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            animation-delay: 4.9s;
        }

        @keyframes msgIn {
            0% {
                opacity: 0;
                transform: translateY(8px);
                letter-spacing: 0.6em;
            }

            100% {
                opacity: 1;
                transform: translateY(0);
                letter-spacing: 0.42em;
            }
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
            animation-delay: 6.4s;
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
            .pulse-wrap {
                width: 180px;
                height: 180px;
            }

            .orbit--1 {
                width: 60px;
                height: 60px;
            }

            .orbit--2 {
                width: 95px;
                height: 95px;
            }

            .orbit--3 {
                width: 140px;
                height: 140px;
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

        <!-- NÚCLEO + ONDAS + PARTÍCULAS -->
        <div class="pulse-wrap">
            <!-- Ondas expansivas -->
            <span class="wave"></span>
            <span class="wave"></span>
            <span class="wave"></span>
            <span class="wave"></span>

            <!-- Anillos orbitales -->
            <span class="orbit orbit--1"></span>
            <span class="orbit orbit--2"></span>
            <span class="orbit orbit--3"></span>

            <!-- Núcleo -->
            <span class="core"></span>

            <!-- Partículas ascendentes -->
            <div class="particles">
                <span class="particle"></span>
                <span class="particle"></span>
                <span class="particle"></span>
                <span class="particle"></span>
                <span class="particle"></span>
                <span class="particle"></span>
                <span class="particle"></span>
                <span class="particle"></span>
                <span class="particle"></span>
                <span class="particle"></span>
                <span class="particle"></span>
                <span class="particle"></span>
                <span class="particle"></span>
                <span class="particle"></span>
                <span class="particle"></span>
                <span class="particle"></span>
            </div>
        </div>

        <!-- TEXTO -->
        <div class="status">
            <div class="status-title">Cerrando sesión</div>
            <div class="status-subtitle">
                Finalizando de forma segura
                <span class="dot">.</span><span class="dot">.</span><span class="dot"></span>
            </div>
        </div>

        <!-- BARRA -->
        <div class="progress">
            <div class="progress-bar"></div>
        </div>

    </div>

    <!-- TEXTO METÁLICO FINAL -->
    <div class="farewell">
        <div class="farewell-brand">JSEA</div>
        <div class="farewell-msg">Nos vemos pronto</div>
    </div>

    <!-- Marca (pie) -->
    <div class="brand">
        <span>myvet</span>
        <span class="brand-dot"></span>
        <span><?php echo date('Y'); ?></span>
    </div>

    <div class="curtain"></div>

    <script>
        setTimeout(() => {
            window.location.href = 'index.php';
        }, 7400);
    </script>

</body>

</html>
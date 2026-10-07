<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Acceso Restringido | MYVET SISTEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        @import url('https://fonts.googleapis.com/css?family=IBM+Plex+Mono|Sedgwick+Ave+Display');

        :root {
            --font-display: 'Sedgwick Ave Display';
            --font-sans-serif: 'IBM Plex Mono';
            --box-shadow: 0px 21px 34px 0px rgba(0, 0, 0, 0.89);
            --color-bg: linear-gradient(to bottom, rgba(35, 37, 38, 1) 0%, rgba(32, 38, 40, 1) 100%);
            --delay-base: 500ms;
            --delay-added: 100ms;
            --acc-back: cubic-bezier(0.390, 0.575, 0.565, 1.000);

            /* Tamaños adaptables */
            --scene-size: clamp(260px, 82vw, 400px);
            --font-403: clamp(180px, 62vw, 440px);
            --font-msg: clamp(22px, 6vw, 34px);
            --font-support: clamp(13px, 3.6vw, 21px);
        }

        *,
        *:before,
        *:after {
            box-sizing: border-box;
            -webkit-tap-highlight-color: rgba(255, 255, 255, 0);
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100vh;
            min-height: 100dvh;
            /* para móviles con barra dinámica */
            background: var(--color-bg);
            color: #fff;
            overflow: hidden;
            font-family: var(--font-sans-serif);
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .scene {
            position: relative;
            width: var(--scene-size);
            height: var(--scene-size);
            transition: transform 600ms var(--acc-back);
            display: flex;
            align-items: center;
        }

        /* Hover 3D solo en dispositivos con mouse */
        @media (hover: hover) and (pointer: fine) {
            .scene:hover {
                transform: scale(.98) skewY(-1deg);
            }

            .scene:hover .text {
                opacity: 1;
                transform: scale(.91);
            }
        }

        .scene>* {
            transition: transform 600ms var(--acc-back);
        }

        .text {
            transition: transform 600ms var(--acc-back), opacity 100ms ease-in;
            height: 100%;
            width: 100%;
            z-index: 7;
            position: relative;
            pointer-events: none;
        }

        @keyframes popInImg {
            0% {
                transform: skewY(5deg) scaleX(.89) scaleY(.89);
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .text span {
            display: block;
            font-family: var(--font-sans-serif);
            text-align: center;
            text-shadow: var(--box-shadow);
            animation: popIn 600ms var(--acc-back) 1 forwards;
            opacity: 0;
        }

        @keyframes popIn {

            0%,
            13% {
                transform: scaleX(.89) scaleY(.75);
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        /* ============================================================
           EL "403" GIGANTE DE FONDO
           ============================================================ */
        .bg-403 {
            font-size: var(--font-403);
            font-family: var(--font-display);
            line-height: 0.85;
            animation-delay: calc(var(--delay-base) + 2 * var(--delay-added));
            z-index: 0;
            background: linear-gradient(to top, rgba(32, 38, 40, 0) 25%, rgba(49, 57, 61, 1) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            transform: translateX(-22%) skewY(-3deg) translateZ(-100px);
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
            transition: transform 1200ms var(--acc-back);
            white-space: nowrap;
        }

        /* ============================================================
           TEXTOS
           ============================================================ */
        .msg {
            font-size: var(--font-msg);
            animation-delay: calc(var(--delay-base) + 3 * var(--delay-added));
            color: #8b8b8b;
            margin-top: 22vh;
            letter-spacing: 2px;
            line-height: 1.2;
        }

        .msg span {
            transform: skewX(-13deg);
            display: inline-block;
            color: #fff;
            letter-spacing: -1px;
        }

        .support {
            font-size: var(--font-support);
            animation-delay: calc(var(--delay-base) + 4 * var(--delay-added));
            display: block;
            margin-top: 6vh;
            color: #686a6b;
            line-height: 1.5;
            padding: 0 8px;
        }

        .support span {
            margin-bottom: 8px;
        }

        .support a {
            display: inline-block;
            color: #b2b3b4;
            text-decoration: none;
            pointer-events: auto;
            /* IMPORTANTE: reactiva interacción */
            transition: color .2s ease;
            word-break: break-word;
        }

        .support a:hover,
        .support a:active {
            color: #fff;
        }

        .support a:after {
            content: '';
            width: 100%;
            height: 3px;
            display: block;
            background: #fff;
            opacity: .35;
            margin-top: 8px;
            transition: opacity .2s ease;
        }

        .support a:hover:after,
        .support a:active:after {
            opacity: .9;
        }

        .support a:focus,
        .support a:active {
            outline: none;
        }

        /* ============================================================
           OVERLAYS — solo activos en desktop con mouse
           ============================================================ */
        .overlay {
            display: none;
            /* ocultos por defecto (móvil) */
            position: absolute;
            cursor: pointer;
            width: 50%;
            height: 50%;
            z-index: 1;
            transform: translateZ(34px);
        }

        .overlay:nth-of-type(1) {
            left: 0;
            top: 0;
        }

        .overlay:nth-of-type(2) {
            right: 0;
            top: 0;
        }

        .overlay:nth-of-type(3) {
            bottom: 0;
            right: 0;
        }

        .overlay:nth-of-type(4) {
            bottom: 0;
            left: 0;
        }

        @media (hover: hover) and (pointer: fine) {
            .overlay {
                display: block;
            }

            .overlay:nth-of-type(1):hover~.lock,
            .overlay:nth-of-type(1):focus~.lock {
                transform-origin: right top;
                transform: translateY(-3px) translateX(5px) rotateX(-13deg) rotateY(3deg) rotateZ(-2deg) translateZ(0) scale(.89);
            }

            .overlay:nth-of-type(1):hover~.bg-403,
            .overlay:nth-of-type(1):focus~.bg-403 {
                transform: translateX(-24%) skewY(-3deg) rotateX(-13deg) rotateY(3deg) translateZ(-100px) scale(.89);
            }

            .overlay:nth-of-type(2):hover~.lock,
            .overlay:nth-of-type(2):focus~.lock {
                transform-origin: left top;
                transform: translateY(-3px) translateX(5px) rotateX(13deg) rotateY(3deg) rotateZ(2deg) translateZ(0) scale(1.03);
            }

            .overlay:nth-of-type(2):hover~.bg-403,
            .overlay:nth-of-type(2):focus~.bg-403 {
                transform: translateX(-18%) skewY(-3deg) rotateX(13deg) rotateY(3deg) translateZ(-100px);
            }

            .overlay:nth-of-type(3):hover~.lock,
            .overlay:nth-of-type(3):focus~.lock {
                transform-origin: left bottom;
                transform: translateY(3px) translateX(-5px) rotateX(-13deg) rotateY(3deg) rotateZ(-2deg) scale(.96);
            }

            .overlay:nth-of-type(3):hover~.bg-403,
            .overlay:nth-of-type(3):focus~.bg-403 {
                transform: translateX(-20%) rotateX(-13deg) rotateY(3deg) translateZ(-100px);
            }

            .overlay:nth-of-type(4):hover~.lock,
            .overlay:nth-of-type(4):focus~.lock {
                transform-origin: right bottom;
                transform: translateY(3px) translateX(5px) rotateX(-13deg) rotateY(-3deg) rotateZ(2deg) translateZ(0) scale(.89);
            }

            .overlay:nth-of-type(4):hover~.bg-403,
            .overlay:nth-of-type(4):focus~.bg-403 {
                transform: translateX(-16%) rotateX(-13deg) rotateY(-3deg) translateZ(-100px);
            }
        }

        /* ============================================================
           CANDADO (pixel art)
           ============================================================ */
        .lock {
            box-shadow:
                32px 8px 0 0 #e4e4e4, 40px 8px 0 0 #e4e4e4, 48px 8px 0 0 #e4e4e4, 56px 8px 0 0 #e4e4e4,
                24px 16px 0 0 #cbcbcb, 32px 16px 0 0 #cbcbcb, 40px 16px 0 0 #909090, 48px 16px 0 0 #909090, 56px 16px 0 0 #cbcbcb, 64px 16px 0 0 #e4e4e4,
                16px 24px 0 0 #cbcbcb, 24px 24px 0 0 #cbcbcb, 32px 24px 0 0 #909090, 56px 24px 0 0 #909090, 64px 24px 0 0 #cbcbcb, 72px 24px 0 0 #e4e4e4,
                16px 32px 0 0 #cbcbcb, 24px 32px 0 0 #909090, 64px 32px 0 0 #909090, 72px 32px 0 0 #cbcbcb,
                16px 40px 0 0 #cbcbcb, 24px 40px 0 0 #909090, 64px 40px 0 0 #909090, 72px 40px 0 0 #cbcbcb,
                16px 48px 0 0 #909090, 24px 48px 0 0 #909090, 64px 48px 0 0 #909090, 72px 48px 0 0 #909090,
                8px 56px 0 0 #fbec79, 16px 56px 0 0 #fbec79, 24px 56px 0 0 #fbec79, 32px 56px 0 0 #fbec79, 40px 56px 0 0 #fbec79, 48px 56px 0 0 #fbec79, 56px 56px 0 0 #fbec79, 64px 56px 0 0 #fbec79, 72px 56px 0 0 #fbec79, 80px 56px 0 0 #fbec79,
                8px 64px 0 0 #ffc107, 16px 64px 0 0 #ffc107, 24px 64px 0 0 #ffc107, 32px 64px 0 0 #ffc107, 40px 64px 0 0 #ffc107, 48px 64px 0 0 #ffc107, 56px 64px 0 0 #ffc107, 64px 64px 0 0 #ffc107, 72px 64px 0 0 #ffc107, 80px 64px 0 0 #ffc107,
                8px 72px 0 0 #ffc107, 16px 72px 0 0 #ffc107, 24px 72px 0 0 #ffc107, 32px 72px 0 0 #ffc107, 40px 72px 0 0 #ffc107, 48px 72px 0 0 #ffc107, 56px 72px 0 0 #ffc107, 64px 72px 0 0 #ffc107, 72px 72px 0 0 #ffc107, 80px 72px 0 0 #ffc107,
                8px 80px 0 0 #ff9800, 16px 80px 0 0 #ffc107, 24px 80px 0 0 #ffc107, 32px 80px 0 0 #ffc107, 40px 80px 0 0 #ffc107, 48px 80px 0 0 #ff9800, 56px 80px 0 0 #ff9800, 64px 80px 0 0 #ff9800, 72px 80px 0 0 #ff9800,
                16px 88px 0 0 #ff9800, 24px 88px 0 0 #ff9800, 32px 88px 0 0 #ff9800, 40px 88px 0 0 #ff9800, 48px 88px 0 0 #ff9800, 56px 88px 0 0 #ff9800, 64px 88px 0 0 #ff9800, 72px 88px 0 0 #ff9800,
                24px 96px 0 0 #ff9800, 32px 96px 0 0 #ff9800, 40px 96px 0 0 #ff9800, 48px 96px 0 0 #ff9800, 56px 96px 0 0 #ff9800, 64px 96px 0 0 #ff9800;

            height: 8px;
            width: 8px;
            position: absolute;
            left: calc(50% - 44px);
            /* centrar según el ancho (88px de ancho total ÷ 2) */
            top: 6%;

            /* Escala el candado junto con la escena */
            transform: scale(clamp(0.55, 0.6vw + 0.4, 1));
            transform-origin: center top;

            transform-style: preserve-3d;
            backface-visibility: hidden;
            pointer-events: none;
            outline: 1px solid transparent;
            z-index: 5;
        }

        /* ============================================================
           AJUSTES PARA MÓVIL
           ============================================================ */
        @media (max-width: 540px) {
            body {
                padding: 16px;
                align-items: flex-start;
                padding-top: 8vh;
            }

            .scene {
                width: 100%;
                max-width: 360px;
                height: auto;
                min-height: 70vh;
                flex-direction: column;
                justify-content: flex-start;
            }

            .bg-403 {
                position: relative;
                /* deja de ser absoluto */
                transform: none;
                text-align: center;
                margin-bottom: -12vw;
                margin-top: 4vh;
                width: 100%;
            }

            .text {
                height: auto;
                display: flex;
                flex-direction: column;
            }

            .msg {
                margin-top: 12px;
                font-size: clamp(24px, 7vw, 32px);
            }

            .support {
                margin-top: 28px;
                padding: 0 12px;
            }

            .support a:after {
                height: 2px;
            }

            .lock {
                /* Ocultar el candado en pantallas pequeñas — ya hay un 403 grande */
                display: none;
            }
        }

        /* Pantallas muy pequeñas */
        @media (max-width: 360px) {
            :root {
                --font-403: clamp(140px, 55vw, 220px);
            }

            .support {
                font-size: 12px;
            }
        }

        /* Reducir animaciones si el usuario lo prefiere */
        @media (prefers-reduced-motion: reduce) {

            *,
            *:before,
            *:after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>

<body>

    <div class="scene">
        <div class="overlay"></div>
        <div class="overlay"></div>
        <div class="overlay"></div>
        <div class="overlay"></div>
        <span class="bg-403">403</span>
        <div class="text">
            <span class="hero-text"></span>
            <span class="msg">Sin <span>Acceso</span></span>
            <span class="support">
                <span>¿Deberías poder entrar?</span>
                <a href="javascript:void(0);" onclick="history.back(); return false;">Contacta con tu administrador</a>
            </span>
        </div>
        <div class="lock"></div>
    </div>

</body>

</html>
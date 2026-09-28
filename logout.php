<?php
session_start();
$_SESSION = [];
session_destroy();
?>
<!DOCTYPE html>
<html lang="es">
     <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
<head>
    <meta charset="UTF-8">
    <title>Saliendo del Sistema | MYVET</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --cat-color: #fca311;
            --cat-dark: #e85d04;
            --pink-accent: #ff85a1;
            --box-color: #8d5b4c;
            --box-dark: #633e34;
        }

        body {
            background-color: #f4f7fb;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* ESCENARIO */
        .stage {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        /* GLOBO DE TEXTO */
        .bubble {
            background: #ffffff;
            color: #2b2d42;
            padding: 12px 24px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 1.1rem;
            position: absolute;
            top: -70px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            opacity: 0;
            transform: translateY(10px) scale(0.8);
            animation: pop-text 0.4s 1s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            z-index: 100;
            border: 2px solid #e2e8f0;
        }

        .bubble::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-top: 10px solid #ffffff;
        }

        /* CONTENEDOR GATO EN CAJA */
        .cat-wrapper {
            position: relative;
            width: 180px;
            height: 190px;
            animation: bounce-gentle 2s ease-in-out infinite;
        }

        /* --- GATITO --- */
        .cat {
            position: absolute;
            width: 130px;
            height: 120px;
            top: 20px;
            left: 25px;
        }

        /* OREJAS */
        .ear {
            position: absolute;
            top: 0;
            width: 35px;
            height: 45px;
            background: var(--cat-color);
            border-radius: 80% 20% 0 0;
        }

        .ear::after {
            content: '';
            position: absolute;
            top: 8px;
            left: 6px;
            width: 20px;
            height: 30px;
            background: var(--pink-accent);
            border-radius: 80% 20% 0 0;
            opacity: 0.7;
        }

        .ear.left {
            left: 5px;
            transform: rotate(-15deg);
            animation: twitch-left 3s infinite;
        }

        .ear.right {
            right: 5px;
            transform: rotate(15deg) scaleX(-1);
            animation: twitch-right 3s infinite;
        }

        /* CABEZA */
        .head {
            position: absolute;
            top: 15px;
            width: 130px;
            height: 95px;
            background: var(--cat-color);
            border-radius: 60px 60px 45px 45px;
        }

        /* CARA */
        .eyes {
            position: absolute;
            top: 38px;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 28px;
            box-sizing: border-box;
        }

        .eye {
            width: 14px;
            height: 14px;
            background: #2b2d42;
            border-radius: 50%;
            animation: blink 4s infinite;
        }

        .nose {
            position: absolute;
            top: 52px;
            left: 50%;
            transform: translateX(-50%);
            width: 10px;
            height: 7px;
            background: var(--pink-accent);
            border-radius: 4px 4px 8px 8px;
        }

        .mouth {
            position: absolute;
            top: 60px;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 8px;
            border-bottom: 2px solid #2b2d42;
            border-radius: 50%;
        }

        /* MEJILLAS */
        .blush {
            position: absolute;
            top: 48px;
            width: 16px;
            height: 10px;
            background: var(--pink-accent);
            border-radius: 50%;
            opacity: 0.5;
        }
        .blush.left { left: 18px; }
        .blush.right { right: 18px; }

        /* PATITA QUE SALUDA */
        .paw-wave {
            position: absolute;
            top: 60px;
            right: 0px;
            width: 26px;
            height: 45px;
            background: var(--cat-color);
            border-radius: 15px;
            z-index: 20;
            transform-origin: bottom center;
            animation: wave 1.2s ease-in-out infinite;
            border-top: 3px solid #ffb703;
        }

        /* --- CAJA --- */
        .box {
            position: absolute;
            bottom: 0;
            width: 180px;
            height: 100px;
            background: var(--box-color);
            border-radius: 8px;
            z-index: 10;
            box-shadow: 0 15px 30px rgba(0,0,0,0.12);
        }

        .box-lid {
            position: absolute;
            top: -12px;
            width: 190px;
            left: -5px;
            height: 16px;
            background: var(--box-dark);
            border-radius: 4px;
        }

        .box-label {
            position: absolute;
            top: 35px;
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            padding: 4px 14px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            color: var(--box-dark);
            letter-spacing: 1px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        /* TEXTO INFERIOR */
        .status-text {
            margin-top: 40px;
            color: #6c757d;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
        }

        /* --- ANIMACIONES --- */
        @keyframes wave {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(-35deg); }
        }

        @keyframes blink {
            0%, 90%, 100% { transform: scaleY(1); }
            95% { transform: scaleY(0.1); }
        }

        @keyframes bounce-gentle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        @keyframes twitch-left {
            0%, 80%, 100% { transform: rotate(-15deg); }
            85% { transform: rotate(-25deg); }
        }

        @keyframes twitch-right {
            0%, 80%, 100% { transform: rotate(15deg) scaleX(-1); }
            85% { transform: rotate(25deg) scaleX(-1); }
        }

        @keyframes pop-text {
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* FUNDIDO FINAL */
        .curtain {
            position: fixed;
            inset: 0;
            background: #ffffff;
            z-index: 999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.8s ease;
        }

        .curtain.active {
            opacity: 1;
        }
    </style>
</head>
<body>

    <div class="curtain" id="curtain"></div>

    <div class="stage">
        <div class="bubble">¡Nos vemos pronto, adiós! 🐾</div>

        <div class="cat-wrapper">
            <!-- GATITO -->
            <div class="cat">
                <div class="ear left"></div>
                <div class="ear right"></div>
                <div class="head">
                    <div class="eyes">
                        <div class="eye"></div>
                        <div class="eye"></div>
                    </div>
                    <div class="blush left"></div>
                    <div class="blush right"></div>
                    <div class="nose"></div>
                    <div class="mouth"></div>
                </div>
                <div class="paw-wave"></div>
            </div>

            <!-- CAJA DE CARTÓN -->
            <div class="box">
                <div class="box-lid"></div>
                <div class="box-label">MYVET</div>
            </div>
        </div>

        <p class="status-text">Cerrando sesión de forma segura...</p>
    </div>

    <script>
        // Transición suave antes de redirigir
        setTimeout(() => {
            document.getElementById('curtain').classList.add('active');
        }, 3200);

        setTimeout(() => {
            window.location.href = 'index.php';
        }, 4000);
    </script>
</body>
</html>
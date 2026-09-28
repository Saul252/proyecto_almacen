<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso Restringido | CF SISTEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --truck-body: #a80909; /* El rojo de tu sistema */
            --truck-cab: #333;
            --wheel-color: #222;
            --road-color: #dee2e6;
            --police-blue: #0d6efd;
            --warning-bg: #fff3cd;
            --warning-border: #ffeeba;
            --warning-text: #856404;
        }

        body {
            background-color: #f4f7fb;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
        }

        .container-403 {
            text-align: center;
            width: 100%;
            max-width: 800px;
            padding: 20px;
        }

        /* --- ESCENARIO --- */
        .stage {
            position: relative;
            height: 150px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            margin-bottom: 30px;
        }

        .bubble-police {
            background: #dc3545;
            color: white;
            padding: 10px 20px;
            border-radius: 15px;
            font-weight: bold;
            position: absolute;
            top: -20px;
            right: 15%;
            opacity: 0;
            transform: translateY(20px);
            animation: pop-text 0.5s 2.2s forwards;
            z-index: 100;
        }

        .bubble-police::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-top: 10px solid #dc3545;
        }

        /* --- CAMIÓN --- */
        .truck-container {
            position: absolute;
            width: 200px;
            height: 100px;
            left: -250px;
            animation: drive-and-stop 2.5s cubic-bezier(0.18, 0.89, 0.32, 1) forwards;
        }

        .cab {
            position: absolute;
            width: 60px;
            height: 70px;
            background: var(--truck-cab);
            right: 0;
            bottom: 25px;
            border-radius: 10px 15px 5px 5px;
        }

        .window {
            position: absolute;
            width: 35px;
            height: 25px;
            background: #87CEEB;
            top: 10px;
            right: 5px;
            border-radius: 5px;
        }

        .trailer {
            position: absolute;
            width: 135px;
            height: 80px;
            background: var(--truck-body);
            left: 0;
            bottom: 25px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            font-weight: bold;
            border-right: 3px solid rgba(0,0,0,0.1);
        }

        .wheel {
            position: absolute;
            width: 25px;
            height: 25px;
            background: var(--wheel-color);
            border-radius: 50%;
            bottom: 12px;
            border: 3px dashed #555;
            animation: spin 0.5s 5 linear;
        }
        .w1 { left: 15px; }
        .w2 { left: 45px; }
        .w3 { right: 8px; }

        /* --- POLICÍA --- */
        .police-officer {
            position: absolute;
            right: 100px;
            bottom: 25px;
            font-size: 80px;
            color: var(--police-blue);
            opacity: 0;
            transform: scale(0.5);
            animation: appear-police 0.5s 1.8s forwards;
        }

        .siren {
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 15px;
            background: red;
            border-radius: 10px;
            animation: blink 0.2s infinite;
        }

        .road {
            width: 80%;
            height: 4px;
            background: var(--road-color);
            position: absolute;
            bottom: 15px;
            border-radius: 2px;
        }

        /* --- TEXTO Y BOTONES --- */
        .text-content h1 { font-size: 100px; margin: 0; color: #333; opacity: 0.1; line-height: 1; }
        .text-content h2 { color: #a80909; margin-top: -25px; text-transform: uppercase; letter-spacing: 2px; font-weight: 800; }
        
        /* Cuadro de aviso animado */
        .admin-contact {
            display: inline-block;
            margin-top: 15px;
            padding: 12px 25px;
            background: var(--warning-bg);
            border: 1px solid var(--warning-border);
            color: var(--warning-text);
            border-radius: 10px;
            font-size: 0.95rem;
            animation: shake-gentle 3s ease-in-out infinite;
        }

        .btn-back {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 30px;
            background: var(--truck-cab);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .btn-back:hover {
            background: transparent;
            color: var(--truck-cab);
            border-color: var(--truck-cab);
            transform: translateY(-3px);
        }

        /* --- ANIMACIONES --- */
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        
        @keyframes drive-and-stop {
            0% { left: -250px; }
            80% { left: 30%; }
            90% { left: 28%; }
            100% { left: 29%; }
        }

        @keyframes appear-police { to { opacity: 1; transform: scale(1); } }
        @keyframes pop-text { to { opacity: 1; transform: translateY(0); } }
        @keyframes blink {
            0%, 100% { background: red; box-shadow: 0 0 15px red; }
            50% { background: blue; box-shadow: 0 0 15px blue; }
        }

        @keyframes shake-gentle {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-2px); }
            20%, 40%, 60%, 80% { transform: translateX(2px); }
        }

    </style>
</head>
<body>

    <div class="container-403">
        <div class="stage">
            <div class="bubble-police">¡ALTO! Zona restringida ✋</div>

            <div class="truck-container">
                <div class="trailer"><b>CF SISTEM</b></div>
                <div class="cab">
                    <div class="window"></div>
                </div>
                <div class="wheel w1"></div>
                <div class="wheel w2"></div>
                <div class="wheel w3"></div>
            </div>

            <div class="police-officer">
                <div class="siren"></div>
                <i class="bi bi-person-fill-lock"></i>
            </div>
            
            <div class="road"></div>
        </div>

        <div class="text-content">
            <h1>403</h1>
            <h2>Acceso Denegado</h2>
            <p class="text-muted">Tu usuario no tiene los permisos necesarios para circular por esta ruta.</p>
            
            <div class="admin-contact">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                ¿Crees que esto es un error? <strong>Contacta a tu administrador.</strong>
            </div>

            <br>

            <a href="javascript:void(0);" onclick="history.back();" class="btn-back">
                <i class="bi bi-arrow-left-circle-fill me-2"></i> Regresar a ruta segura
            </a>
        </div>
    </div>

    <script>
        // Detener la rotación de las ruedas después del frenazo (2.5s)
        setTimeout(() => {
            const wheels = document.querySelectorAll('.wheel');
            wheels.forEach(w => w.style.animation = 'none');
        }, 2500);
    </script>
</body>
</html>
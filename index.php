<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MYVET SISTEM | Acceso</title>
    <link rel="icon" type="image/png" href="/myvet/public/assets/logo.png') ?>">
    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
        type="image/x-icon">

    <!-- Frameworks & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-vivid: #8b5cf6;
            --accent-cyan: #06b6d4;
            --accent-pink: #ec4899;
            --glass-bg: rgba(255, 255, 255, 0.12);
            --glass-border: rgba(255, 255, 255, 0.28);
            --glass-input: rgba(255, 255, 255, 0.18);
        }

        * {
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            background: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* --- FONDO DINÁMICO ANIMADO CON ORBES CROMÁTICOS --- */
        .dynamic-bg {
            position: fixed;
            inset: 0;
            z-index: 1;
            overflow: hidden;
            background: radial-gradient(circle at 50% 50%, #1e1b4b, #0f172a);
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.75;
            animation: floatOrb 18s infinite alternate ease-in-out;
        }

        .orb-1 {
            width: 450px;
            height: 450px;
            background: linear-gradient(135deg, #7c3aed, #db2777);
            top: -10%;
            left: -10%;
            animation-duration: 14s;
        }

        .orb-2 {
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, #0284c7, #0d9488);
            bottom: -15%;
            right: -10%;
            animation-duration: 20s;
        }

        .orb-3 {
            width: 350px;
            height: 350px;
            background: linear-gradient(135deg, #f43f5e, #8b5cf6);
            top: 40%;
            left: 55%;
            animation-duration: 16s;
        }

        @keyframes floatOrb {
            0% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(60px, 80px) scale(1.15);
            }

            100% {
                transform: translate(-50px, 40px) scale(0.9);
            }
        }

        /* --- LAYOUT SPLIT CON CRISTAL --- */
        .glass-container {
            position: relative;
            z-index: 10;
            width: 92%;
            max-width: 1050px;
            height: 600px;
            display: flex;
            border-radius: 28px;
            background: var(--glass-bg);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid var(--glass-border);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.45),
                inset 0 1px 0 rgba(255, 255, 255, 0.35);
            overflow: hidden;
        }

        /* LADO IZQUIERDO (CAROUSEL / BANNER) */
        .left-side {
            flex: 1.1;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 3rem;
            overflow: hidden;
            border-right: 1px solid rgba(255, 255, 255, 0.15);
        }

        .carousel-bg {
            position: absolute;
            inset: 0;
            z-index: 1;
        }

        .carousel-inner,
        .carousel-item,
        .carousel-item img {
            height: 100%;
            object-fit: cover;
        }

        .carousel-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(15, 23, 42, 0.95) 15%, rgba(15, 23, 42, 0.25));
            z-index: 2;
        }

        .brand-content {
            position: relative;
            z-index: 3;
            color: #fff;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
            color: #38bdf8;
        }

        /* LADO DERECHO (FORMULARIO CRISTAL) */
        .right-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem;
            background: rgba(15, 23, 42, 0.25);
        }

        .login-card {
            width: 100%;
            max-width: 360px;
            text-align: center;
        }

        .logo-title {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 30%, #38bdf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
            margin-bottom: 2px;
        }

        .logo-subtitle {
            color: #94a3b8;
            font-size: 0.88rem;
            margin-bottom: 2.2rem;
            font-weight: 500;
        }

        /* INPUTS ESTILO CRISTAL VÍVIDO */
        .form-label {
            color: #e2e8f0;
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 6px;
            letter-spacing: 0.3px;
        }

        .input-group {
            background: var(--glass-input);
            border-radius: 14px;
            border: 1px solid var(--glass-border);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .input-group:focus-within {
            border-color: #38bdf8;
            box-shadow: 0 0 20px rgba(56, 189, 248, 0.35);
            background: rgba(255, 255, 255, 0.22);
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: #38bdf8;
            padding-left: 1.1rem;
            font-size: 1.1rem;
        }

        .form-control {
            background: transparent !important;
            border: none !important;
            color: #ffffff !important;
            padding: 0.75rem 1rem 0.75rem 0.5rem;
            font-size: 0.95rem;
            box-shadow: none !important;
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        .btn-show-pass {
            background: transparent;
            border: none;
            color: #94a3b8;
            padding-right: 1.1rem;
            transition: color 0.2s;
        }

        .btn-show-pass:hover {
            color: #38bdf8;
        }

        /* BOTÓN VÍVIDO GLOW */
        .btn-login {
            margin-top: 1rem;
            padding: 0.85rem;
            border-radius: 14px;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            border: none;
            background: linear-gradient(135deg, #06b6d4, #8b5cf6, #ec4899);
            background-size: 200% 200%;
            color: #ffffff;
            box-shadow: 0 10px 25px rgba(139, 92, 246, 0.4);
            transition: all 0.4s ease;
        }

        .btn-login:hover {
            background-position: right center;
            box-shadow: 0 15px 35px rgba(236, 72, 153, 0.5);
            transform: translateY(-2px);
            color: #fff;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .login-footer {
            margin-top: 2rem;
            color: #64748b;
            font-size: 0.78rem;
        }

        /* RESPONSIVE */
        @media (max-width: 850px) {
            .left-side {
                display: none;
            }

            .glass-container {
                max-width: 440px;
                height: auto;
            }

            .right-side {
                padding: 3rem 2rem;
            }
        }
    </style>
</head>

<body>

    <!-- FONDO ANIMADO DE CRISTAL VÍVIDO -->
    <div class="dynamic-bg">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <!-- CONTENEDOR PRINCIPAL GLASSMORPHISM -->
    <div class="glass-container">

        <!-- BANNER IZQUIERDO -->
        <div class="left-side">
            <div class="carousel-bg">
                <div id="labCarousel" class="carousel slide carousel-fade h-100" data-bs-ride="carousel"
                    data-bs-interval="4000">
                    <div class="carousel-inner">
                        <div class="carousel-item active"><img src="public/assets/almacen3.jpg" class="d-block w-100">
                        </div>
                        <div class="carousel-item"><img src="public/assets/almacen2.jpg" class="d-block w-100"></div>
                    </div>
                </div>
            </div>
            <div class="carousel-overlay"></div>

            <div class="brand-content">
                <div class="brand-badge">
                    <i class="bi bi-shield-check"></i> v2.0
                </div>
                <h1 class="fw-extrabold display-6 text-white mb-2">Eficiencia y velocidad</h1>
                <p class="text-light opacity-75 mb-0">Gestión e inventarios de alta precisión.</p>
            </div>
        </div>

        <!-- FORMULARIO DERECHO -->
        <div class="right-side">
            <div class="login-card">
                <div class="logo-title">MYVET</div>
                <div class="logo-subtitle">Gestión Inteligente de Negocio</div>

                <form id="formLogin">
                    <div class="mb-3 text-start">
                        <label class="form-label">USUARIO</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                            <input type="text" name="usuario" class="form-control" placeholder="Ej: admin" required
                                autocomplete="off">
                        </div>
                    </div>

                    <div class="mb-4 text-start">
                        <label class="form-label">CONTRASEÑA</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                            <input type="password" name="password" id="passwordField" class="form-control"
                                placeholder="••••••••" required>
                            <button type="button" class="btn btn-show-pass" id="togglePassword">
                                <i class="bi bi-eye-fill" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" id="btnIngresar" class="btn btn-login w-100">
                        <span>INGRESAR AL SISTEMA</span>
                    </button>
                </form>

                <div class="login-footer">
                    © <?php echo date('Y'); ?> <span class="text-light fw-semibold">MYVET SISTEM</span><br>
                    <span>Desarollado por JSEA Todos los derechos reservados</span> <span>Todos los derechos
                        reservados</span>
                </div>
            </div>
        </div>

    </div>

    <!-- JS Bootstrap & Lógica -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // VER/OCULTAR CONTRASEÑA
        const togglePassword = document.querySelector('#togglePassword');
        const passwordField = document.querySelector('#passwordField');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function () {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            eyeIcon.classList.toggle('bi-eye-fill');
            eyeIcon.classList.toggle('bi-eye-slash-fill');
        });

        // LÓGICA DE LOGIN
        document.getElementById('formLogin').addEventListener('submit', async (e) => {
            e.preventDefault();

            const btn = document.getElementById('btnIngresar');
            const originalText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Validando...`;

            const formData = new FormData(e.target);

            try {
                const response = await fetch('/myvet/app/controllers/authController.php?action=login', {
                    method: 'POST',
                    body: formData
                });

                const res = await response.json();

                if (res.status === 'success' || res.status === 'info') {
                    localStorage.setItem('config_hora_cierre', res.hora_cierre || '18:00');

                    Swal.fire({
                        icon: res.status,
                        title: res.menssage,
                        text: res.message,
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true,
                        background: '#0f172a',
                        color: '#fff'
                    }).then(() => {
                        window.location.href = res.redirect;
                    });
                } else {
                    Swal.fire({
                        icon: res.status,
                        title: 'Atención',
                        text: res.message,
                        confirmButtonColor: '#8b5cf6',
                        background: '#0f172a',
                        color: '#fff'
                    });
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor. Inténtalo más tarde.',
                    confirmButtonColor: '#8b5cf6',
                    background: '#0f172a',
                    color: '#fff'
                });
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    </script>
</body>

</html>
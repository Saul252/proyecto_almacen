<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MYVET | Portal del Paciente</title>
    <link rel="icon" type="image/png" href="/myvet/public/assets/logo.png">
    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
        type="image/x-icon">

    <!-- Frameworks & Fuentes Médicas -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --medical-blue: #0284c7;
            --medical-blue-dark: #0369a1;
            --medical-indigo: #4f46e5;
            --medical-purple: #7c3aed;
            --medical-cyan: #06b6d4;
            --bg-light: #f8fafc;
            --card-border: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }

        * {
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        /* --- FONDO CLÍNICO AMBIENTAL CON AURA AZUL Y VIOLETA SUAVE --- */
        .dynamic-bg {
            position: fixed;
            inset: 0;
            z-index: 1;
            overflow: hidden;
            background: linear-gradient(135deg, #f8fafc 0%, #e0f2fe 50%, #f3e8ff 100%);
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.45;
            animation: floatOrb 20s infinite alternate ease-in-out;
        }

        .orb-1 {
            width: 480px;
            height: 480px;
            background: linear-gradient(135deg, #38bdf8, #818cf8);
            top: -10%;
            left: -5%;
            animation-duration: 16s;
        }

        .orb-2 {
            width: 520px;
            height: 520px;
            background: linear-gradient(135deg, #c084fc, #0ea5e9);
            bottom: -15%;
            right: -8%;
            animation-duration: 22s;
        }

        .orb-3 {
            width: 380px;
            height: 380px;
            background: linear-gradient(135deg, #a5b4fc, #67e8f9);
            top: 35%;
            left: 50%;
            animation-duration: 18s;
        }

        @keyframes floatOrb {
            0% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(50px, 60px) scale(1.12);
            }

            100% {
                transform: translate(-40px, 30px) scale(0.92);
            }
        }

        /* --- TARJETA CLÍNICA ELEVADA --- */
        .glass-container {
            position: relative;
            z-index: 10;
            width: 94%;
            max-width: 1060px;
            min-height: 620px;
            display: flex;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.90);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 25px 60px -15px rgba(30, 41, 59, 0.12),
                0 0 0 1px rgba(226, 232, 240, 0.8);
            overflow: hidden;
            margin: 25px 0;
        }

        /* LADO IZQUIERDO: BANNER MÉDICO */
        .left-side {
            flex: 1.1;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 3.2rem;
            overflow: hidden;
            border-right: 1px solid rgba(226, 232, 240, 0.8);
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
            background: linear-gradient(to top, rgba(15, 23, 42, 0.92) 15%, rgba(14, 116, 144, 0.4) 60%, rgba(255, 255, 255, 0) 100%);
            z-index: 2;
        }

        .brand-content {
            position: relative;
            z-index: 3;
            color: #ffffff;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.35);
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 1.2rem;
            color: #e0f2fe;
        }

        /* LADO DERECHO: FORMULARIO CLÍNICO */
        .right-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2.5rem;
            background: #ffffff;
        }

        .login-card {
            width: 100%;
            max-width: 370px;
            text-align: center;
        }

        /* LOGO CON DEGRADADO AZUL A MORADO MÉDICO */
        .logo-title {
            font-size: 2.1rem;
            font-weight: 800;
            background: linear-gradient(135deg, #0284c7 0%, #4f46e5 60%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.8px;
            margin-bottom: 3px;
        }

        .logo-subtitle {
            color: var(--text-muted);
            font-size: 0.88rem;
            margin-bottom: 2rem;
            font-weight: 500;
        }

        /* INPUTS ESTILO MÉDICO / LIMPIO */
        .form-label {
            color: #334155;
            font-size: 0.78rem;
            font-weight: 700;
            margin-bottom: 6px;
            letter-spacing: 0.4px;
        }

        .input-group {
            background: #f8fafc;
            border-radius: 14px;
            border: 1.5px solid #e2e8f0;
            transition: all 0.25s ease;
            overflow: hidden;
        }

        .input-group:focus-within {
            border-color: #0284c7;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.12);
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: #0284c7;
            padding-left: 1.1rem;
            font-size: 1.1rem;
        }

        .form-control {
            background: transparent !important;
            border: none !important;
            color: var(--text-dark) !important;
            padding: 0.75rem 1rem 0.75rem 0.6rem;
            font-size: 0.92rem;
            box-shadow: none !important;
            font-weight: 500;
        }

        .form-control::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        /* Soporte para el calendario médico */
        input[type="date"] {
            color-scheme: light;
            color: #334155 !important;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            opacity: 0.65;
            padding-right: 0.5rem;
            filter: brightness(0.4) sepia(1) hue-rotate(170deg) saturate(3);
        }

        input[type="date"]::-webkit-calendar-picker-indicator:hover {
            opacity: 1;
        }

        /* BOTÓN MÉDICO EN DEGRADADO AZUL-INDIGO-MORADO */
        .btn-consultar {
            margin-top: 1.3rem;
            padding: 0.85rem;
            border-radius: 14px;
            font-weight: 700;
            font-size: 0.92rem;
            letter-spacing: 0.4px;
            border: none;
            background: linear-gradient(135deg, #0284c7 0%, #2563eb 50%, #7c3aed 100%);
            background-size: 200% 200%;
            color: #ffffff;
            box-shadow: 0 10px 22px -5px rgba(37, 99, 235, 0.4);
            transition: all 0.35s ease;
        }

        .btn-consultar:hover {
            background-position: right center;
            box-shadow: 0 14px 28px -5px rgba(124, 58, 237, 0.45);
            transform: translateY(-2px);
            color: #ffffff;
        }

        .btn-consultar:active {
            transform: translateY(0);
        }

        .login-footer {
            margin-top: 2rem;
            color: #94a3b8;
            font-size: 0.78rem;
        }

        /* RESPONSIVE */
        @media (max-width: 850px) {
            .left-side {
                display: none;
            }

            .glass-container {
                max-width: 440px;
                min-height: auto;
            }

            .right-side {
                padding: 3rem 2rem;
            }
        }
    </style>
</head>

<body>

    <!-- FONDO CLÍNICO AMBIENTAL -->
    <div class="dynamic-bg">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <!-- TARJETA ELEVADA DEL PACIENTE -->
    <div class="glass-container">

        <!-- LADO IZQUIERDO: BANNER MÉDICO -->
        <div class="left-side">
            <div class="carousel-bg">
                <div id="medicalCarousel" class="carousel slide carousel-fade h-100" data-bs-ride="carousel"
                    data-bs-interval="4500">
                    <div class="carousel-inner">
                        <div class="carousel-item active"><img src="/myvet/public/assets/almacen3.jpg"
                                class="d-block w-100" alt="Atención Veterinaria"></div>
                        <div class="carousel-item"><img src="/myvet/public/assets/almacen2.jpg" class="d-block w-100"
                                alt="Cuidado Animal"></div>
                    </div>
                </div>
            </div>
            <div class="carousel-overlay"></div>

            <div class="brand-content">
                <div class="brand-badge">
                    <i class="bi bi-shield-check text-cyan"></i> Portal Médico Seguro
                </div>
                <h1 class="fw-bold display-6 text-white mb-2">Expediente Clínico Digital</h1>
                <p class="text-light opacity-90 mb-0">Consulta recetas, tratamientos, vacunas y el historial completo de
                    tus pacientes en cualquier momento.</p>
            </div>
        </div>

        <!-- LADO DERECHO: FORMULARIO -->
        <div class="right-side">
            <div class="login-card">

                <!-- Ícono de Cruz Médica con aura azul -->
                <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle mb-3"
                    style="background: linear-gradient(135deg, rgba(2, 132, 199, 0.12), rgba(124, 58, 237, 0.12)); color: #0284c7;">
                    <i class="bi bi-heart-pulse-fill fs-3" style="color: #2563eb;"></i>
                </div>

                <div class="logo-title">MYVET</div>
                <div class="logo-subtitle">Portal de Consulta para Pacientes</div>

                <form id="formConsultaHistorial" autocomplete="off">

                    <!-- 1. Nombre Comercial o Titular -->
                    <div class="mb-3 text-start">
                        <label class="form-label">NOMBRE DEL TITULAR O COMERCIAL</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                            <input type="text" name="nombre_comercial" id="nombre_comercial" class="form-control"
                                placeholder="Ej: Juan Pérez o Veterinaria Luna" required>
                        </div>
                    </div>

                    <!-- 2. Fecha de Nacimiento -->
                    <div class="mb-3 text-start">
                        <label class="form-label">FECHA DE NACIMIENTO</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control"
                                required>
                        </div>
                    </div>

                    <!-- 3. Teléfono Registrado -->
                    <div class="mb-4 text-start">
                        <label class="form-label">TELÉFONO REGISTRADO</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="tel" name="telefono" id="telefono" class="form-control"
                                placeholder="Ej: 5512345678" maxlength="15" required>
                        </div>
                    </div>

                    <button type="submit" id="btnConsultar" class="btn btn-consultar w-100">
                        <span><i class="bi bi-search me-1"></i> CONSULTAR EXPEDIENTE</span>
                    </button>
                </form>

                <div class="login-footer">
                    <i class="bi bi-lock-fill me-1 text-primary"></i> Acceso protegido por cifrado SSL<br>
                    <span>© <?= date('Y'); ?> MYVET SISTEM. Todos los derechos reservados.</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('formConsultaHistorial').addEventListener('submit', async (e) => {
            e.preventDefault();

            const btn = document.getElementById('btnConsultar');
            const textoOriginal = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Verificando expediente...`;

            const formData = new FormData(e.target);

            try {
                const response = await fetch('/myvet/app/controllers/consultaHIstorialClienteController.php?action=obtenerClientePorDatos', {
                    method: 'POST',
                    body: formData
                });

                const res = await response.json();

                if (res.success) {
                    // Guardar datos del paciente en sesión para la siguiente pantalla
                    sessionStorage.setItem('cliente_actual', JSON.stringify(res.data));
                    if (res.data.api_token) {
                        sessionStorage.setItem('api_token', res.data.api_token);
                    }
                    console.log(res);

                    // Alerta SweetAlert médica clara y profesional
                    Swal.fire({
                        icon: 'success',
                        title: '¡Expediente Localizado!',
                        text: `Bienvenido(a), ${res.data.nombre_comercial || 'Paciente'}`,
                        showConfirmButton: false,
                        timer: 1600,
                        timerProgressBar: true,
                        confirmButtonColor: '#0284c7'
                    }).then(() => {
                        window.location.href = res.redirect || `/myvet/expedienteMedico?api_token=${res.api_token}`;
                    });

                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Datos no encontrados',
                        text: res.message || 'No se localizó un expediente con los datos proporcionados. Por favor verifica tu información.',
                        confirmButtonColor: '#0284c7'
                    });
                    btn.disabled = false;
                    btn.innerHTML = textoOriginal;
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No fue posible conectar con el servidor médico. Inténtalo más tarde.',
                    confirmButtonColor: '#0284c7'
                });
                btn.disabled = false;
                btn.innerHTML = textoOriginal;
            }
        });
    </script>
</body>

</html>
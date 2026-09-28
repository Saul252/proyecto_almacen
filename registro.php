<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Nuevo Negocio</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            min-height: 100vh;
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

        .hero-title {
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #ffffff 30%, #38bdf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* ESTILOS DE CONTROLES DE FORMULARIO NEÓN */
        .form-label-custom {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #94a3b8;
            margin-bottom: 6px;
        }

        .form-control-dark {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--glass-border);
            color: #f8fafc;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            transition: all 0.25s ease;
        }

        .form-control-dark:focus {
            background: rgba(15, 23, 42, 0.85);
            border-color: var(--accent-cyan);
            color: #ffffff;
            box-shadow: 0 0 15px rgba(6, 182, 212, 0.25);
            outline: none;
        }

        .form-control-dark::placeholder {
            color: #475569;
        }

        /* SELECCIÓN DE PLANES INTERACTIVA */
        .plan-card-option {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.25s ease;
            position: relative;
            height: 100%;
        }

        .plan-card-option:hover {
            border-color: rgba(56, 189, 248, 0.4);
            background: rgba(255, 255, 255, 0.05);
            transform: translateY(-2px);
        }

        .plan-card-option.active {
            background: rgba(6, 182, 212, 0.12);
            border-color: var(--accent-cyan);
            box-shadow: 0 0 20px rgba(6, 182, 212, 0.2);
        }

        .plan-card-option .badge-popular {
            position: absolute;
            top: -10px;
            right: 12px;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            font-size: 0.65rem;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 20px;
            text-transform: uppercase;
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

        .btn-whatsapp {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            font-weight: 700;
            border-radius: 14px;
            padding: 0.9rem 2rem;
            border: none;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-whatsapp:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.5);
        }
    </style>
</head>
<body>

    <!-- FONDO DE ORBES CROMÁTICOS -->
    <div class="dynamic-bg">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <div class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="glass-box p-4 p-md-5 my-auto" style="max-width: 780px; width: 100%;">
            
            <!-- CONTENEDOR FORMULARIO -->
            <div id="contenedorFormulario">
                <div class="text-center mb-4">
                    <h1 class="hero-title mb-2">¡Bienvenido al Sistema! 🚀</h1>
                    <p class="text-secondary small">Completa los datos de tu negocio para registrar tu nueva cuenta.</p>
                </div>

                <form id="formRegistroAlmacen" autocomplete="off">
                    
                    <!-- SECCIÓN 1: DATOS GENERALES -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-shop text-info fs-5"></i>
                            <h6 class="fw-bold mb-0 text-white">1. Información del negocio</h6>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">NOMBRE DEL RESPONSABLE *</label>
                                <input type="text" name="nombre_responsable" class="form-control form-control-dark" placeholder="Ej. Juan Pérez" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">CÓDIGO / CLAVE CORTA *</label>
                                <input type="text" name="codigo" class="form-control form-control-dark text-uppercase" placeholder="Ej. MATRIZ01" required>
                            </div>

                            <div class="col-md-7">
                                <label class="form-label-custom">NOMBRE DEL NEGOCIO / ALMACÉN *</label>
                                <input type="text" name="nombre" class="form-control form-control-dark text-uppercase" placeholder="Ej. ABARROTES CENTRAL" required>
                            </div>

                            <div class="col-md-5">
                                <label class="form-label-custom">HORA DE CIERRE</label>
                                <input type="time" name="hora_cierre_programada" value="22:00" class="form-control form-control-dark">
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom">UBICACIÓN / DIRECCIÓN</label>
                                <input type="text" name="ubicacion" class="form-control form-control-dark text-uppercase" placeholder="Ej. AV. PRINCIPAL #123, COL. CENTRO">
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: PLANES -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-box-seam text-info fs-5"></i>
                            <h6 class="fw-bold mb-0 text-white">2. Selecciona un plan de suscripción</h6>
                        </div>

                        <input type="hidden" name="tipo_plan" id="input_tipo_plan" value="1" required>

                        <div class="row g-3">
                            <!-- PLAN 1: TODO FULL -->
                            <div class="col-6 col-md-3">
                                <div class="plan-card-option active" onclick="seleccionarPlan(1, this)">
                                    <span class="badge-popular">Top</span>
                                    <div class="fw-bold text-white mb-1">Todo Full</div>
                                    <div class="text-info small fw-bold mb-1">Plan #1</div>
                                    <p class="text-secondary mb-0" style="font-size: 0.72rem;">Acceso total ilimitado a todos los módulos.</p>
                                </div>
                            </div>

                            <!-- PLAN 2: COMPLETO -->
                            <div class="col-6 col-md-3">
                                <div class="plan-card-option" onclick="seleccionarPlan(2, this)">
                                    <div class="fw-bold text-white mb-1">Completo</div>
                                    <div class="text-info small fw-bold mb-1">Plan #2</div>
                                    <p class="text-secondary mb-0" style="font-size: 0.72rem;">Ideal para negocios medianos en crecimiento.</p>
                                </div>
                            </div>

                            <!-- PLAN 3: EMPEZAR -->
                            <div class="col-6 col-md-3">
                                <div class="plan-card-option" onclick="seleccionarPlan(3, this)">
                                    <div class="fw-bold text-white mb-1">Empezar</div>
                                    <div class="text-info small fw-bold mb-1">Plan #3</div>
                                    <p class="text-secondary mb-0" style="font-size: 0.72rem;">Herramientas clave para arrancar operaciones.</p>
                                </div>
                            </div>

                            <!-- PLAN 4: BÁSICO -->
                            <div class="col-6 col-md-3">
                                <div class="plan-card-option" onclick="seleccionarPlan(4, this)">
                                    <div class="fw-bold text-white mb-1">Básico</div>
                                    <div class="text-info small fw-bold mb-1">Plan #4</div>
                                    <p class="text-secondary mb-0" style="font-size: 0.72rem;">Funcionalidades esenciales de inventario.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                   

                    <!-- BOTÓN DE ENVÍO -->
                    <button type="submit" id="btnRegistrar" class="btn btn-custom w-100 py-3 mt-2">
                        COMPLETAR REGISTRO <i class="bi bi-arrow-right-short ms-1 fs-5"></i>
                    </button>
                </form>
            </div>

            <!-- CONTENEDOR ÉXITO Y CONTACTO ACTIVACIÓN (Oculto inicialmente) -->
            <div id="contenedorExito" class="text-center py-4 d-none">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-info" style="font-size: 4rem;"></i>
                </div>
                
                <h2 class="hero-title mb-3">¡Registro Recibido! 🎉</h2>
                <p class="text-slate-300 fs-6 mb-4">
                    Hola <strong id="txtResponsable" class="text-white"></strong>, se ha registrado con éxito el negocio <strong id="txtNegocio" class="text-info"></strong>.
                </p>

                <div class="p-4 rounded-4 mb-4 text-start" style="background: rgba(255, 255, 255, 0.03); border: 1px solid var(--glass-border);">
                    <div class="d-flex align-items-center gap-2 text-warning mb-2 fw-bold">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span>Siguiente paso: Activación de Licencia</span>
                    </div>
                    <p class="text-secondary small mb-0">
                        Tu cuenta ha sido creada exitosamente con estado <strong>Pendiente (Inactiva)</strong>. Para completar la configuración y activar tu acceso al sistema, por favor ponte en contacto con nosotros por WhatsApp.
                    </p>
                </div>

                <a id="btnWhatsApp" href="#" target="_blank" class="btn btn-whatsapp py-3 px-4">
                    <i class="bi bi-whatsapp fs-5"></i>
                    <span>CONTACTAR PARA ACTIVACIÓN</span>
                </a>
            </div>

        </div>
    </div>

    <script>
        // Selección interactiva de Plan de Suscripción
        function seleccionarPlan(idPlan, elemento) {
            document.querySelectorAll('.plan-card-option').forEach(el => el.classList.remove('active'));
            elemento.classList.add('active');
            document.getElementById('input_tipo_plan').value = idPlan;
        }

        // Envío AJAX via Fetch
        document.getElementById('formRegistroAlmacen').addEventListener('submit', function(e) {
            e.preventDefault();

            const pass = document.getElementById('password').value;
            const confirmPass = document.getElementById('confirm_password').value;

            if (pass !== confirmPass) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Verifica tu contraseña',
                    text: 'Las contraseñas ingresadas no coinciden.',
                    background: '#0b0f19',
                    color: '#fff',
                    confirmButtonColor: '#8b5cf6'
                });
                return;
            }

            const formData = new FormData(this);
            const btn = document.getElementById('btnRegistrar');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> PROCESANDO...';

            fetch('app/controllers/registroController.php?action=registrar', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = 'COMPLETAR REGISTRO <i class="bi bi-arrow-right-short ms-1 fs-5"></i>';

                if (data.success) {
                    document.getElementById('contenedorFormulario').classList.add('d-none');
                    document.getElementById('txtResponsable').innerText = data.nombre_responsable;
                    document.getElementById('txtNegocio').innerText = data.nombre_negocio;

                    // Teléfono de contacto
                    const numeroWhatsApp = "525500000000"; // Reemplaza con tu número
                    const mensaje = encodeURIComponent(`Hola, acabo de registrar mi negocio "${data.nombre_negocio}" en el sistema y me gustaría solicitar la activación de mi cuenta.`);
                    
                    document.getElementById('btnWhatsApp').href = `https://wa.me/${numeroWhatsApp}?text=${mensaje}`;
                    document.getElementById('contenedorExito').classList.remove('d-none');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de registro',
                        text: data.message || 'Ocurrió un error al procesar el registro.',
                        background: '#0b0f19',
                        color: '#fff',
                        confirmButtonColor: '#8b5cf6'
                    });
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = 'COMPLETAR REGISTRO <i class="bi bi-arrow-right-short ms-1 fs-5"></i>';
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo comunicar con el servidor.',
                    background: '#0b0f19',
                    color: '#fff',
                    confirmButtonColor: '#8b5cf6'
                });
            });
        });
    </script>
</body>
</html>
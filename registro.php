<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Nuevo Negocio</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            /* Acentos vibrantes pero elegantes */
            --primary-vivid: #7c3aed;
            --accent-cyan: #0891b2;
            --accent-pink: #db2777;
            --accent-emerald: #059669;

            /* Fondo profesional con tonos slate claros */
            --bg-1: #f1f5f9;
            --bg-2: #e2e8f0;
            --bg-3: #cbd5e1;

            /* Glass adaptado a fondo claro */
            --glass-card: rgba(255, 255, 255, 0.75);
            --glass-border: rgba(15, 23, 42, 0.08);
            --glass-highlight: rgba(255, 255, 255, 0.6);

            /* Colores de texto profesionales */
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-1);
            color: var(--text-primary);
            overflow-x: hidden;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* FONDO DINÁMICO CLARO Y PROFESIONAL */
        .dynamic-bg {
            position: fixed;
            inset: 0;
            z-index: -1;
            overflow: hidden;
            background: radial-gradient(circle at 50% 40%, #f8fafc 0%, #e2e8f0 45%, #cbd5e1 100%);
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(140px);
            opacity: 0.35;
            animation: floatOrb 24s infinite alternate ease-in-out;
        }

        .orb-1 {
            width: 520px;
            height: 520px;
            background: #a78bfa;
            top: -12%;
            left: -6%;
            opacity: 0.4;
        }

        .orb-2 {
            width: 580px;
            height: 580px;
            background: #67e8f9;
            bottom: -18%;
            right: -8%;
            animation-duration: 28s;
            opacity: 0.35;
        }

        .orb-3 {
            width: 380px;
            height: 380px;
            background: #6ee7b7;
            top: 45%;
            left: 42%;
            animation-duration: 22s;
            opacity: 0.28;
        }

        .orb-4 {
            width: 320px;
            height: 320px;
            background: #f9a8d4;
            top: 15%;
            right: 25%;
            animation-duration: 32s;
            opacity: 0.25;
        }

        @keyframes floatOrb {
            0% {
                transform: translate(0, 0) scale(1);
            }

            100% {
                transform: translate(70px, 55px) scale(1.12);
            }
        }

        /* TARJETAS GLASSMORPHISM PROFESIONAL */
        .glass-box {
            background: var(--glass-card);
            backdrop-filter: blur(30px) saturate(180%);
            -webkit-backdrop-filter: blur(30px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: 26px;
            box-shadow:
                0 25px 60px rgba(15, 23, 42, 0.12),
                0 8px 20px rgba(15, 23, 42, 0.06),
                inset 0 1px 0 rgba(255, 255, 255, 0.7);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-box:hover {
            border-color: rgba(15, 23, 42, 0.12);
            box-shadow:
                0 30px 70px rgba(15, 23, 42, 0.15),
                0 10px 25px rgba(15, 23, 42, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.85);
        }

        .hero-title {
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #0f172a 10%, #7c3aed 60%, #0891b2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ESTILOS DE CONTROLES DE FORMULARIO PROFESIONAL */
        .form-label-custom {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.6px;
            color: var(--text-secondary);
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .form-control-dark {
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(15, 23, 42, 0.1);
            color: var(--text-primary);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            transition: all 0.25s ease;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
        }

        .form-control-dark:focus {
            background: #ffffff;
            border-color: var(--accent-cyan);
            color: var(--text-primary);
            box-shadow: 0 0 0 4px rgba(8, 145, 178, 0.12);
            outline: none;
        }

        .form-control-dark::placeholder {
            color: var(--text-muted);
        }

        /* Input group iconos */
        .input-group-text-glass {
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(15, 23, 42, 0.1);
            border-right: none;
            color: var(--accent-cyan);
            border-radius: 12px 0 0 12px;
        }

        /* SELECCIÓN DE PLANES INTERACTIVA */
        .plan-card-option {
            background: rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 16px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.25s ease;
            position: relative;
            height: 100%;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        }

        .plan-card-option:hover {
            border-color: rgba(8, 145, 178, 0.4);
            background: rgba(255, 255, 255, 0.85);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
        }

        .plan-card-option.active {
            background: rgba(8, 145, 178, 0.08);
            border-color: var(--accent-cyan);
            box-shadow:
                0 0 0 3px rgba(8, 145, 178, 0.12),
                0 8px 20px rgba(8, 145, 178, 0.15);
        }

        .plan-card-option .badge-popular {
            position: absolute;
            top: -10px;
            right: 12px;
            background: linear-gradient(135deg, #7c3aed, #db2777);
            font-size: 0.62rem;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            color: #fff;
            box-shadow: 0 4px 10px rgba(124, 58, 237, 0.3);
        }

        /* BOTONES PROFESIONALES */
        .btn-custom {
            background: linear-gradient(135deg, #0891b2, #7c3aed);
            color: white;
            font-weight: 700;
            border-radius: 14px;
            padding: 0.9rem 2.2rem;
            border: none;
            box-shadow: 0 10px 25px rgba(8, 145, 178, 0.25);
            transition: all 0.3s;
            letter-spacing: 0.3px;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 15px 38px rgba(124, 58, 237, 0.35);
        }

        .btn-whatsapp {
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
            font-weight: 700;
            border-radius: 14px;
            padding: 0.9rem 2rem;
            border: none;
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.28);
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-whatsapp:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(5, 150, 105, 0.45);
        }

        /* TEXTO SECUNDARIO AJUSTADO */
        .text-secondary {
            color: var(--text-secondary) !important;
        }

        /* Ajuste del contenedor de éxito */
        .success-box {
            background: rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 18px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
        }

        /* Ajustes tipográficos para fondo claro */
        .text-white {
            color: var(--text-primary) !important;
        }

        .text-info {
            color: var(--accent-cyan) !important;
        }

        .text-warning {
            color: #d97706 !important;
        }
    </style>
</head>

<body>

    <!-- FONDO DE ORBES CROMÁTICOS -->
    <div class="dynamic-bg">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <div class="orb orb-4"></div>
    </div>

    <div class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="glass-box p-4 p-md-5 my-auto" style="max-width: 780px; width: 100%;">

            <!-- CONTENEDOR FORMULARIO -->
            <div id="contenedorFormulario">
                <div class="text-center mb-4">
                    <h1 class="hero-title mb-2">¡Bienvenido al Sistema! 🚀</h1>
                    <p class="text-secondary small">Completa los datos de tu negocio para registrar tu nueva cuenta.</p>
                </div>

                <form id="formRegistroAlmacen" autocomplete="on">

                    <!-- SECCIÓN 1: DATOS GENERALES -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-shop text-info fs-5"></i>
                            <h6 class="fw-bold mb-0 text-white">1. Información del negocio</h6>
                        </div>

                        <div class="row g-3">
                            <!-- Fila 1: Responsable + Nombre del negocio -->
                            <div class="col-md-6">
                                <label class="form-label-custom">NOMBRE DEL RESPONSABLE *</label>
                                <input type="text" name="nombre_responsable" class="form-control form-control-dark"
                                    placeholder="Ej. Juan Pérez" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">NOMBRE DEL NEGOCIO / ALMACÉN *</label>
                                <input type="text" name="nombre" class="form-control form-control-dark text-uppercase"
                                    placeholder="Ej. ABARROTES CENTRAL" required>
                            </div>

                            <!-- Fila 2: Correo + Teléfono -->
                            <div class="col-md-6">
                                <label class="form-label-custom">CORREO ELECTRÓNICO *</label>
                                <div class="input-group">
                                    <span class="input-group-text input-group-text-glass">
                                        <i class="bi bi-envelope-fill"></i>
                                    </span>
                                    <input type="email" name="correo" class="form-control form-control-dark"
                                        placeholder="correo@ejemplo.com" required style="border-radius: 0 12px 12px 0;">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">TELÉFONO DE CONTACTO *</label>
                                <div class="input-group">
                                    <span class="input-group-text input-group-text-glass">
                                        <i class="bi bi-telephone-fill"></i>
                                    </span>
                                    <input type="tel" name="telefono" class="form-control form-control-dark"
                                        placeholder="+52 55 1234 5678" required style="border-radius: 0 12px 12px 0;">
                                </div>
                            </div>

                            <!-- Fila 3: Ubicación (ancho completo) -->
                            <div class="col-12">
                                <label class="form-label-custom">UBICACIÓN / DIRECCIÓN</label>
                                <input type="text" name="ubicacion"
                                    class="form-control form-control-dark text-uppercase"
                                    placeholder="Ej. AV. PRINCIPAL #123, COL. CENTRO">
                            </div>

                            <!-- Fila 4: Hora de cierre (más pequeña, a la izquierda) -->
                            <div class="col-md-5">
                                <label class="form-label-custom">HORA DE CIERRE</label>
                                <input type="time" name="hora_cierre_programada" value="22:00"
                                    class="form-control form-control-dark">
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
                                    <p class="text-secondary mb-0" style="font-size: 0.72rem;">Acceso total ilimitado a
                                        todos los módulos.</p>
                                </div>
                            </div>

                            <!-- PLAN 2: COMPLETO -->
                            <div class="col-6 col-md-3">
                                <div class="plan-card-option" onclick="seleccionarPlan(2, this)">
                                    <div class="fw-bold text-white mb-1">Completo</div>
                                    <div class="text-info small fw-bold mb-1">Plan #2</div>
                                    <p class="text-secondary mb-0" style="font-size: 0.72rem;">Ideal para negocios
                                        medianos en crecimiento.</p>
                                </div>
                            </div>

                            <!-- PLAN 3: EMPEZAR -->
                            <div class="col-6 col-md-3">
                                <div class="plan-card-option" onclick="seleccionarPlan(3, this)">
                                    <div class="fw-bold text-white mb-1">Empezar</div>
                                    <div class="text-info small fw-bold mb-1">Plan #3</div>
                                    <p class="text-secondary mb-0" style="font-size: 0.72rem;">Herramientas clave para
                                        arrancar operaciones.</p>
                                </div>
                            </div>

                            <!-- PLAN 4: BÁSICO -->
                            <div class="col-6 col-md-3">
                                <div class="plan-card-option" onclick="seleccionarPlan(4, this)">
                                    <div class="fw-bold text-white mb-1">Básico</div>
                                    <div class="text-info small fw-bold mb-1">Plan #4</div>
                                    <p class="text-secondary mb-0" style="font-size: 0.72rem;">Funcionalidades
                                        esenciales de inventario.</p>
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
                <p class="text-secondary fs-6 mb-4">
                    Hola <strong id="txtResponsable" class="text-white"></strong>, se ha registrado con éxito el negocio
                    <strong id="txtNegocio" class="text-info"></strong> tu id de cuenta es <strong id="txt_id_cuenta"
                        class="text-info"></strong>.
                </p>

                <div class="success-box p-4 mb-4 text-start">
                    <div class="d-flex align-items-center gap-2 text-warning mb-2 fw-bold">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span>Siguiente paso: Activación de Licencia</span>
                    </div>
                    <p class="text-secondary small mb-0">
                        Tu cuenta ha sido creada exitosamente con estado <strong>Pendiente (Inactiva)</strong>. Para
                        completar la configuración y activar tu acceso al sistema, por favor ponte en contacto con
                        nosotros por WhatsApp.
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

        // ============================================
        // CONFIGURACIÓN
        // ============================================
        const CORREO_ADMIN = 'saulenriquealbatapia252@gmail.com';
        const URL_CORREO_CONTROLLER = '/myvet/app/controllers/correoController.php';
        const WHATSAPP_SOPORTE = '+525523789029';
        let id_nuevo = 0;


        // ============================================
        // FUNCIÓN 1: Correo al CLIENTE
        // ============================================
        async function enviarCorreoBienvenidaCliente() {

            const form = document.getElementById('formRegistroAlmacen');

            if (!form) return false;

            const correo = form.querySelector('input[name="correo"]')?.value?.trim();
            const nombreResponsable =
                form.querySelector('input[name="nombre_responsable"]')?.value?.trim() || '';

            const nombreNegocio =
                form.querySelector('input[name="nombre"]')?.value?.trim() || '';
            const id = id_nuevo;

            if (!correo) {
                console.warn('No se encontró el correo del cliente.');
                return false;
            }

            const mensaje = `Hola ${nombreResponsable},

¡Gracias por registrarte en MYVET SISTEM!

Nos alegra muchísimo darte la bienvenida. Hemos recibido la solicitud para tu negocio "${nombreNegocio}" con id : "${id}".

Para poder activar tu cuenta, necesitamos agendar una entrevista contigo. Esto nos permite ajustar el sistema a las necesidades específicas de tu negocio y asegurarnos de que todo quede perfecto para ti.

Por favor, contáctanos por WhatsApp para coordinar la activación:

📱 WhatsApp: ${WHATSAPP_SOPORTE}

Gracias por tu tiempo y por confiar en nosotros. ¡Estamos ansiosos por ayudarte a crecer!

Con cariño,
El equipo de JSEA`;

            try {

                return await enviarCorreo({
                    correo: correo,
                    titulo: '¡Bienvenido a MYVET SISTEM! 🎉',
                    descripcion: mensaje,
                    urlBackend: URL_CORREO_CONTROLLER,
                    remitente: 'MYVET SISTEM'
                });

            } catch (error) {

                console.error('Error enviando correo al cliente:', error);
                return false;
            }
        }


        // ============================================
        // FUNCIÓN 2: Correo al ADMIN
        // ============================================
        async function enviarCorreoNotificacionAdmin() {

            const form = document.getElementById('formRegistroAlmacen');

            if (!form || !CORREO_ADMIN) {
                return false;
            }

            const correo =
                form.querySelector('input[name="correo"]')?.value?.trim() || '';

            const nombreResponsable =
                form.querySelector('input[name="nombre_responsable"]')?.value?.trim() || '';

            const nombreNegocio =
                form.querySelector('input[name="nombre"]')?.value?.trim() || '';

            const telefono =
                form.querySelector('input[name="telefono"]')?.value?.trim() || '';

            const ubicacion =
                form.querySelector('input[name="ubicacion"]')?.value?.trim() || '';

            const horaCierre =
                form.querySelector('input[name="hora_cierre_programada"]')?.value?.trim() || '';

            const tipoPlan =
                form.querySelector('input[name="tipo_plan"]')?.value?.trim() || '';
            const id = id_nuevo;

            const mensaje = `Nuevo negocio registrado en MYVET SISTEM.

Datos del registro:

  • Responsable: ${nombreResponsable}
  • Negocio:     ${nombreNegocio}
  • Correo:      ${correo}
  • Id:          ${id}
  
  • Teléfono:    ${telefono || 'No especificado'}
  • Ubicación:   ${ubicacion || 'No especificada'}
  • Hora cierre: ${horaCierre || 'No especificada'}
  • Plan:        ${tipoPlan || 'No especificado'}

Acciones pendientes:

  → Contactar al cliente vía WhatsApp (${WHATSAPP_SOPORTE})
  → Agendar entrevista
  → Activar la cuenta en el panel de administración.`;

            try {

                return await enviarCorreo({
                    correo: CORREO_ADMIN,
                    titulo: `🆕 Nuevo registro: ${nombreNegocio}`,
                    descripcion: mensaje,
                    urlBackend: URL_CORREO_CONTROLLER,
                    remitente: 'MYVET SISTEM'
                });

            } catch (error) {

                console.error('Error enviando correo al administrador:', error);
                return false;
            }
        }


        // ============================================
        // SELECCIÓN DE PLAN
        // ============================================
        function seleccionarPlan(idPlan, elemento) {

            document
                .querySelectorAll('.plan-card-option')
                .forEach(el => el.classList.remove('active'));

            elemento.classList.add('active');

            document.getElementById('input_tipo_plan').value = idPlan;
        }


        // ============================================
        // REGISTRO
        // ============================================
        const formulario = document.getElementById('formRegistroAlmacen');

        if (formulario) {

            formulario.addEventListener('submit', async function (e) {

                e.preventDefault();

                const form = this;
                const formData = new FormData(form);

                const btn = document.getElementById('btnRegistrar');

                btn.disabled = true;

                btn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span> PROCESANDO...';


                try {

                    // ============================================
                    // 1. REGISTRAR NEGOCIO
                    // ============================================
                    const respuesta = await fetch(
                        'registrate?action=registrar',
                        {
                            method: 'POST',
                            body: formData
                        }
                    );

                    const data = await respuesta.json();


                    // ============================================
                    // 2. VALIDAR REGISTRO
                    // ============================================
                    if (!data.success) {

                        throw new Error(
                            data.message || 'Ocurrió un error al registrar el negocio.'
                        );
                    }

                    id_nuevo = data.id_almacen;
                    // ============================================
                    // 3. ENVIAR CORREOS
                    // ============================================

                    // Se ejecutan los dos al mismo tiempo
                    const [correoCliente, correoAdmin] = await Promise.all([
                        enviarCorreoBienvenidaCliente(),
                        enviarCorreoNotificacionAdmin()
                    ]);

                    console.log('Correo cliente:', correoCliente);
                    console.log('Correo admin:', correoAdmin);


                    // ============================================
                    // 4. MOSTRAR PANTALLA DE ÉXITO
                    // ============================================
                    document
                        .getElementById('contenedorFormulario')
                        .classList.add('d-none');

                    document.getElementById('txtResponsable').innerText =
                        data.nombre_responsable;

                    document.getElementById('txtNegocio').innerText =
                        data.nombre_negocio;
                    document.getElementById('txt_id_cuenta').innerText =
                        data.id_almacen;


                    // ============================================
                    // 5. WHATSAPP
                    // ============================================
                    const numeroWhatsApp = "+525523789029";

                    const mensaje = encodeURIComponent(
                        `Hola, acabo de registrar mi negocio "${data.nombre_negocio}" con id "${data.id_almacen}" en el sistema y me gustaría solicitar la activación de mi cuenta.`
                    );

                    document.getElementById('btnWhatsApp').href =
                        `https://wa.me/${numeroWhatsApp}?text=${mensaje}`;


                    // ============================================
                    // 6. MOSTRAR ÉXITO
                    // ============================================
                    document
                        .getElementById('contenedorExito')
                        .classList.remove('d-none');


                } catch (error) {

                    console.error('Error en el registro:', error);

                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: error.message ||
                            'No se pudo comunicar con el servidor.',
                        background: '#151a2e',
                        color: '#e8edf7',
                        confirmButtonColor: '#8b5cf6'
                    });

                } finally {

                    btn.disabled = false;

                    btn.innerHTML =
                        'COMPLETAR REGISTRO <i class="bi bi-arrow-right-short ms-1 fs-5"></i>';
                }

            });
        }

    </script>
    <script>

        // ============================================
        // ESCAPAR HTML
        // ============================================
        function escaparHtml(texto) {
            const div = document.createElement('div');
            div.textContent = texto ?? '';
            return div.innerHTML;
        }


        // ============================================
        // FUNCIÓN GENERAL PARA ENVIAR CORREOS
        // ============================================
        async function enviarCorreo({
            correo,
            titulo,
            descripcion,
            urlBackend = '/myvet/correo',
            remitente = 'MYVET SISTEM'
        }) {

            // --------------------------------------------
            // Validar correo
            // --------------------------------------------
            if (!correo || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
                throw new Error('Correo inválido');
            }


            // --------------------------------------------
            // Validar título
            // --------------------------------------------
            if (!titulo || !titulo.trim()) {
                throw new Error('El título es obligatorio');
            }


            // --------------------------------------------
            // Validar descripción
            // --------------------------------------------
            if (!descripcion || !descripcion.trim()) {
                throw new Error('La descripción es obligatoria');
            }


            // --------------------------------------------
            // Construir HTML del correo
            // --------------------------------------------
            const cuerpoHtml = `
            <div style="
                font-family: Arial, sans-serif;
                color:#333;
                max-width:600px;
                margin:auto;
            ">

                <div style="
                    background:#1e293b;
                    color:#fff;
                    padding:20px;
                    text-align:center;
                    border-radius:8px 8px 0 0;
                ">
                    <h2 style="margin:0;">
                        ${escaparHtml(titulo)}
                    </h2>
                </div>

                <div style="
                    padding:20px;
                    background:#f8f9fa;
                    border:1px solid #e5e7eb;
                    border-top:none;
                    border-radius:0 0 8px 8px;
                ">

                    <p style="
                        white-space:pre-line;
                        line-height:1.6;
                    ">
                        ${escaparHtml(descripcion)}
                    </p>

                    <hr style="
                        margin:25px 0;
                        border:none;
                        border-top:1px solid #ddd;
                    ">

                    <p style="
                        font-size:12px;
                        color:#888;
                        text-align:center;
                    ">
                        ${escaparHtml(remitente)}
                        &copy;
                        ${new Date().getFullYear()}
                    </p>

                </div>

            </div>
        `;


            // --------------------------------------------
            // Datos para correoController.php
            // --------------------------------------------
            const datos = {

                modo: 'archivos',

                para: correo,

                asunto: titulo,

                contenido: cuerpoHtml,

                // No enviamos archivos porque estos
                // correos no llevan documentos.
                adjuntos: []

            };


            // --------------------------------------------
            // Enviar al backend
            // --------------------------------------------
            const respuesta = await fetch(urlBackend, {

                method: 'POST',

                headers: {
                    'Content-Type': 'application/json'
                },

                body: JSON.stringify(datos)

            });


            // --------------------------------------------
            // Verificar respuesta HTTP
            // --------------------------------------------
            if (!respuesta.ok) {
                throw new Error(
                    `Error HTTP ${respuesta.status}`
                );
            }


            // --------------------------------------------
            // Convertir respuesta a JSON
            // --------------------------------------------
            const data = await respuesta.json();


            // --------------------------------------------
            // Verificar respuesta del controlador
            // --------------------------------------------
            if (!data.ok) {

                throw new Error(
                    data.error ||
                    data.mensaje ||
                    'Error al enviar el correo'
                );

            }


            // --------------------------------------------
            // Resultado
            // --------------------------------------------
            return {

                enviado: true,

                mensaje:
                    data.mensaje ||
                    'Correo enviado correctamente'

            };
        }

    </script>
</body>

</html>
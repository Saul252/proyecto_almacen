<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña | MYVET SISTEM</title>

    <!-- Fonts & Bootstrap -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --accent-cyan: #0891b2;
            --accent-violet: #7c3aed;
            --accent-emerald: #059669;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background: radial-gradient(circle at 50% 40%, #f8fafc 0%, #e2e8f0 45%, #cbd5e1 100%);
            color: #0f172a;
            overflow-x: hidden;
        }

        /* ORBES DECORATIVOS */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(140px);
            z-index: -1;
            opacity: 0.35;
            animation: floatOrb 24s infinite alternate ease-in-out;
        }

        .orb-1 {
            width: 520px;
            height: 520px;
            background: #a78bfa;
            top: -12%;
            left: -6%;
        }

        .orb-2 {
            width: 580px;
            height: 580px;
            background: #67e8f9;
            bottom: -18%;
            right: -8%;
            animation-duration: 28s;
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

        /* TARJETA GLASS */
        .glass-box {
            background: rgba(255, 255, 255, 0.78);
            backdrop-filter: blur(30px) saturate(180%);
            -webkit-backdrop-filter: blur(30px) saturate(180%);
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 26px;
            box-shadow:
                0 25px 60px rgba(15, 23, 42, 0.12),
                0 8px 20px rgba(15, 23, 42, 0.06),
                inset 0 1px 0 rgba(255, 255, 255, 0.7);
        }

        .hero-title {
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #0f172a 10%, #7c3aed 60%, #0891b2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ICONO SUPERIOR */
        .icon-circle {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, rgba(8, 145, 178, 0.15), rgba(124, 58, 237, 0.15));
            border: 1px solid rgba(8, 145, 178, 0.25);
            color: var(--accent-cyan);
            font-size: 2rem;
            box-shadow: 0 8px 20px rgba(8, 145, 178, 0.12);
        }

        /* BOTÓN PRINCIPAL */
        .btn-custom {
            background: linear-gradient(135deg, #0891b2, #7c3aed);
            color: #fff;
            font-weight: 700;
            border: none;
            border-radius: 14px;
            letter-spacing: 0.3px;
            box-shadow: 0 10px 25px rgba(8, 145, 178, 0.25);
            transition: all 0.3s;
        }

        .btn-custom:hover:not(:disabled) {
            transform: translateY(-2px);
            color: #fff;
            box-shadow: 0 15px 38px rgba(124, 58, 237, 0.35);
        }

        /* ICONO DE ÉXITO */
        .success-icon {
            width: 84px;
            height: 84px;
            background: linear-gradient(135deg, rgba(5, 150, 105, 0.15), rgba(8, 145, 178, 0.15));
            border: 1px solid rgba(5, 150, 105, 0.25);
            color: var(--accent-emerald);
            font-size: 2.6rem;
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.15);
        }

        .link-back {
            color: #475569;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .link-back:hover {
            color: var(--accent-cyan);
        }

        .input-group-text {
            background: rgba(255, 255, 255, 0.85);
            border-right: none;
            color: var(--accent-cyan);
        }

        .form-control:focus {
            border-color: var(--accent-cyan);
            box-shadow: 0 0 0 4px rgba(8, 145, 178, 0.12);
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center py-5">

    <!-- ORBES DE FONDO -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="orb orb-4"></div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6" style="max-width: 560px;">

                <div class="glass-box p-4 p-md-5">

                    <!-- ============================================ -->
                    <!-- FORMULARIO                                    -->
                    <!-- ============================================ -->
                    <div id="contenedorFormulario">

                        <div class="text-center mb-4">
                            <div
                                class="icon-circle rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>
                            <h1 class="hero-title h3 mb-2">Recuperar Contraseña</h1>
                            <p class="text-secondary small mb-0">
                                Ingresa tus datos para verificar tu identidad. Te enviaremos tu nueva contraseña al
                                correo.
                            </p>
                        </div>

                        <form id="formRecuperacion" autocomplete="off" novalidate>

                            <!-- TELÉFONO -->
                            <div class="mb-3">
                                <label for="telefono" class="form-label small fw-bold text-uppercase text-secondary">
                                    Teléfono Registrado *
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text border-end-0">
                                        <i class="bi bi-telephone-fill"></i>
                                    </span>
                                    <input type="tel" id="telefono" name="telefono" class="form-control border-start-0"
                                        placeholder="+52 55 1234 5678" autocomplete="tel" required>
                                </div>
                            </div>

                            <!-- USUARIO -->
                            <div class="mb-3">
                                <label for="username" class="form-label small fw-bold text-uppercase text-secondary">
                                    Nombre de Usuario *
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text border-end-0">
                                        <i class="bi bi-person-fill"></i>
                                    </span>
                                    <input type="text" id="username" name="username" class="form-control border-start-0"
                                        placeholder="Ej. juan.perez" autocomplete="username" required>
                                </div>
                            </div>

                            <!-- CORREO -->
                            <div class="mb-4">
                                <label for="correo" class="form-label small fw-bold text-uppercase text-secondary">
                                    Correo Electrónico *
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text border-end-0">
                                        <i class="bi bi-envelope-fill"></i>
                                    </span>
                                    <input type="email" id="correo" name="correo" class="form-control border-start-0"
                                        placeholder="correo@ejemplo.com" autocomplete="email" required>
                                </div>
                            </div>

                            <!-- BOTÓN -->
                            <button type="submit" id="btnRecuperar" class="btn btn-custom w-100 py-3">
                                RECUPERAR CONTRASEÑA
                                <i class="bi bi-arrow-right-short ms-1 fs-5"></i>
                            </button>

                            <!-- VOLVER -->
                            <div class="text-center mt-3">
                                <a href="/myvet/login" class="link-back">
                                    <i class="bi bi-arrow-left"></i> Volver al inicio de sesión
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- ============================================ -->
                    <!-- PANTALLA DE ÉXITO                             -->
                    <!-- ============================================ -->
                    <div id="contenedorExito" class="text-center py-3 d-none">

                        <div
                            class="success-icon rounded-circle d-inline-flex align-items-center justify-content-center mb-4">
                            <i class="bi bi-envelope-check-fill"></i>
                        </div>

                        <h2 class="hero-title h3 mb-3">¡Contraseña Restablecida! 🎉</h2>
                        <p class="text-secondary fs-6 mb-4">
                            Hemos enviado tu nueva contraseña a
                            <strong id="txtCorreo" class="text-info"></strong>.
                            Revísala e inicia sesión con ella.
                        </p>

                        <div class="bg-white bg-opacity-50 border rounded-4 p-4 mb-4 text-start">
                            <div class="d-flex align-items-center gap-2 text-info mb-2 fw-bold">
                                <i class="bi bi-info-circle-fill"></i>
                                <span>¿Qué sigue?</span>
                            </div>
                            <ol class="text-secondary small mb-0 ps-3">
                                <li>Revisa tu bandeja de entrada (y la carpeta de spam).</li>
                                <li>Copia la nueva contraseña que te enviamos.</li>
                                <li>Inicia sesión con tu usuario y esa contraseña.</li>
                                <li>Cambia la contraseña desde tu perfil.</li>
                            </ol>
                        </div>

                        <a href="/myvet/login" class="btn btn-custom w-100 py-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            IR AL INICIO DE SESIÓN
                        </a>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        // ============================================================
        // CONFIGURACIÓN
        // ============================================================
        const URL_BACKEND = '/myvet/registraCuenta?action=recuperarPassword';

        // ============================================================
        // REFERENCIAS
        // ============================================================
        const form = document.getElementById('formRecuperacion');
        const btnRecuperar = document.getElementById('btnRecuperar');
        const contenedorForm = document.getElementById('contenedorFormulario');
        const contenedorExito = document.getElementById('contenedorExito');
        const inputTelefono = document.getElementById('telefono');
        const inputUsername = document.getElementById('username');
        const inputCorreo = document.getElementById('correo');

        // ============================================================
        // ALERTA
        // ============================================================
        const alerta = (icon, title, text) => Swal.fire({
            icon, title, text,
            confirmButtonColor: '#0891b2',
            customClass: { popup: 'rounded-4' }
        });

        // ============================================================
        // VALIDACIONES
        // ============================================================
        const validarCorreo = c => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(c);
        const validarTelefono = t => t.replace(/\D/g, '').length >= 8 && /^[+\d\s\-()]+$/.test(t);

        function validarFormulario() {
            const telefono = inputTelefono.value.trim();
            const username = inputUsername.value.trim();
            const correo = inputCorreo.value.trim();

            if (!telefono) return 'Ingresa tu teléfono registrado.';
            if (!validarTelefono(telefono)) return 'El teléfono no tiene un formato válido.';
            if (!username) return 'Ingresa tu nombre de usuario.';
            if (username.length < 3) return 'El nombre de usuario es demasiado corto.';
            if (!correo) return 'Ingresa tu correo electrónico.';
            if (!validarCorreo(correo)) return 'El correo electrónico no es válido.';

            return null;
        }

        // ============================================================
        // SUBMIT
        // ============================================================
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const error = validarFormulario();
            if (error) return alerta('warning', 'Datos incompletos', error);

            btnRecuperar.disabled = true;
            btnRecuperar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> PROCESANDO...';

            const formData = new FormData();
            formData.append('telefono', inputTelefono.value.trim());
            formData.append('username', inputUsername.value.trim());
            formData.append('correo', inputCorreo.value.trim());

            try {
                const res = await fetch(
                    '/myvet/app/controllers/authController.php?action=recuperarPassword',
                    { method: 'POST', body: formData }
                );

                const contentType = res.headers.get('content-type') || '';
                if (!contentType.includes('application/json')) {
                    const texto = await res.text();
                    console.error('Respuesta NO-JSON:', texto);
                    throw new Error(`Error del servidor (${res.status}).`);
                }

                const data = await res.json();

                if (data.status !== 'success') {
                    throw new Error(data.message || 'No se pudo verificar tu identidad.');
                }

                // ✅ Datos para el correo
                const datosCorreo = {
                    correo: data.usuario?.correo || inputCorreo.value.trim(),
                    nombre: data.usuario?.nombre || '',
                    password: data.password_nueva,
                    username: data.usuario?.username || inputUsername.value.trim()
                };

                // 🔍 DEBUG 1: Ver qué datos se van a enviar
                console.log('🔍 [1] datosCorreo:', datosCorreo);
                console.log('🔍 [2] password_nueva:', data.password_nueva);
                console.log('🔍 [3] typeof enviarCorreo:', typeof enviarCorreo);
                console.log('🔍 [4] typeof enviarCorreoRecuperacion:', typeof enviarCorreoRecuperacion);

                // Enviar correo
                if (datosCorreo.password && typeof enviarCorreo === 'function') {
                    console.log('🔍 [5] Entrando a enviar correo...');

                    try {
                        const resultadoCorreo = await enviarCorreoRecuperacion(datosCorreo);
                        console.log('🔍 [6] Resultado del correo:', resultadoCorreo);
                    } catch (errCorreo) {
                        console.error('❌ Error enviando correo de recuperación:', errCorreo);
                    }
                } else {
                    console.warn('⚠️ No se envió correo. Causas:');
                    console.warn('   - password_nueva:', datosCorreo.password);
                    console.warn('   - enviarCorreo existe:', typeof enviarCorreo === 'function');
                }

                // Mostrar pantalla de éxito
                contenedorForm.classList.add('d-none');
                contenedorExito.classList.remove('d-none');
                document.getElementById('txtCorreo').textContent = datosCorreo.correo;

            } catch (err) {
                console.error('❌ Error:', err);
                alerta('error', 'No se pudo procesar', err.message);
            } finally {
                btnRecuperar.disabled = false;
                btnRecuperar.innerHTML = 'RECUPERAR CONTRASEÑA <i class="bi bi-arrow-right-short ms-1 fs-5"></i>';
            }
        });
        /**
 * Envía el correo con la nueva contraseña al usuario.
 * Reutiliza la función global enviarCorreo().
 */
        async function enviarCorreoRecuperacion({ correo, nombre, password, username }) {

            if (!correo) {
                console.warn('No se encontró el correo del usuario.');
                return false;
            }

            if (!password) {
                console.warn('No hay contraseña nueva para enviar.');
                return false;
            }

            const mensaje = `Hola ${nombre || 'usuario'},

Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en MYVET SISTEM.

━━━━━━━━━━━━━━━━━━━━━━━━━━━
  🔐  TUS NUEVOS DATOS DE ACCESO
━━━━━━━━━━━━━━━━━━━━━━━━━━━

  Usuario:    ${username}
  Contraseña: ${password}

━━━━━━━━━━━━━━━━━━━━━━━━━━━

⚠️  IMPORTANTE:
  • Guarda esta contraseña en un lugar seguro.
  • Te recomendamos cambiarla al iniciar sesión.
  • Si tú NO solicitaste este cambio, contacta
    inmediatamente con el administrador.

Para ingresar al sistema:
  https://tudominio.com/myvet/login

Gracias por confiar en nosotros.

— Equipo JSEA SISTEM`;

            try {
                return await enviarCorreo({
                    correo: correo,
                    titulo: '🔐 Nueva contraseña - MYVET SISTEM',
                    descripcion: mensaje,
                    urlBackend: '/myvet/app/controllers/correoController.php',
                    remitente: 'MYVET SISTEM'
                });

            } catch (error) {
                console.error('Error enviando correo de recuperación:', error);
                return false;
            }
        }
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
        // ============================================
        // ESCAPAR HTML (necesaria para enviarCorreo)
        // ============================================
        function escaparHtml(texto) {
            const div = document.createElement('div');
            div.textContent = texto ?? '';
            return div.innerHTML;
        }
    </script>
</body>

</html>
<?php
session_start();

// ============================================
// ESCANEO DINÁMICO DE IMÁGENES
// ============================================
$imgDir = __DIR__ . '/public/assets/img/';
$imgUrl = 'public/assets/img/';
$allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

$images = [];
if (is_dir($imgDir)) {
    $files = glob($imgDir . '*.{jpg,jpeg,png,webp,gif}', GLOB_BRACE);
    if ($files && count($files) > 0) {
        usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
        $files = array_slice($files, 0, 15);
        foreach ($files as $file) {
            $images[] = $imgUrl . basename($file);
        }
    }
}

if (empty($images)) {
    $images = [
        $imgUrl . 'almacen3.jpg',
        $imgUrl . 'almacen2.jpg'
    ];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MYVET SISTEM | Acceso</title>
    <link rel="icon" type="image/png" href="/myvet/public/assets/logo.png">
    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
        type="image/x-icon">

    <!-- Frameworks & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="index.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<style>
    /* ===== PEGA AQUÍ TU CSS ACTUAL ===== */

    /* ============================================
       BOTÓN "CREAR CUENTA" (estilo cristal iOS)
       ============================================ */
    .signup-divider {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 1.5rem 0 1rem;
        color: rgba(90, 90, 110, 0.7);
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 1.5px;
        text-transform: uppercase;
    }

    .signup-divider::before,
    .signup-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg,
                transparent,
                rgba(10, 10, 20, 0.12),
                transparent);
    }

    .btn-signup {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 0.85rem;
        border-radius: 16px;
        font-weight: 600;
        font-size: 0.82rem;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        text-decoration: none;
        color: var(--text-blue, #0060df);
        background: rgba(255, 255, 255, 0.35);
        border: 1.5px solid rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        box-shadow:
            0 4px 15px rgba(10, 132, 255, 0.08),
            0 1px 0 rgba(255, 255, 255, 0.95) inset,
            0 -1px 0 rgba(10, 132, 255, 0.06) inset;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        overflow: hidden;
        cursor: pointer;
    }

    .btn-signup::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(105deg,
                transparent 40%,
                rgba(168, 208, 255, 0.4) 50%,
                transparent 60%);
        transition: left 0.8s ease;
    }

    .btn-signup:hover::before {
        left: 100%;
    }

    .btn-signup:hover {
        background: rgba(255, 255, 255, 0.55);
        border-color: rgba(10, 132, 255, 0.5);
        color: var(--blue-metal, #0a84ff);
        transform: translateY(-2px);
        box-shadow:
            0 10px 25px rgba(10, 132, 255, 0.2),
            0 1px 0 rgba(255, 255, 255, 1) inset,
            0 -1px 0 rgba(10, 132, 255, 0.1) inset;
    }

    .btn-signup:active {
        transform: translateY(0) scale(0.99);
    }

    .btn-signup i {
        font-size: 1.05rem;
        transition: transform 0.4s ease;
    }

    .btn-signup:hover i {
        transform: scale(1.15) rotate(-6deg);
    }
</style>

<body>

    <!-- FONDO PARALLAX CON CARRUSEL DINÁMICO -->
    <div class="hero-bg" id="heroBg">
        <div id="heroCarousel" class="carousel slide carousel-fade h-100" data-bs-ride="carousel"
            data-bs-interval="5000">
            <div class="carousel-inner h-100">
                <?php foreach ($images as $index => $img): ?>
                    <div class="carousel-item h-100 <?= $index === 0 ? 'active' : '' ?>">
                        <img src="<?= htmlspecialchars($img) ?>" class="d-block w-100 h-100" alt="Imagen <?= $index + 1 ?>"
                            loading="<?= $index === 0 ? 'eager' : 'lazy' ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Partículas flotantes -->
    <div class="particles" id="particles"></div>

    <!-- Marca superior izquierda -->
    <div class="corner-brand">
        <i class="bi bi-shield-check"></i> MYVET
    </div>

    <!-- Versión superior derecha -->
    <div class="corner-version">
        <span class="dot"></span> v2.0 PRO
    </div>

    <!-- Texto lateral decorativo -->
    <div class="side-text">Sistema de Gestión Inteligente</div>

    <!-- CONTENEDOR CENTRAL DEL FORMULARIO -->
    <div class="center-stage">
        <div class="glass-card" id="glassCard">
            <div class="shine"></div>

            <!-- Icono de marca -->
            <div class="brand-mark">
                <i class="bi bi-shield-lock-fill"></i>
            </div>

            <h1 class="logo-title">MYVET SISTEM</h1>
            <p class="logo-subtitle">Gestión Inteligente</p>

            <form id="formLogin">
                <div class="mb-3 text-start">
                    <label class="form-label">Usuario</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="usuario" class="form-control" placeholder="Ingresa tu usuario" required
                            autocomplete="off">
                    </div>
                </div>

                <div class="mb-2 text-start">
                    <label class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-key"></i></span>
                        <input type="password" name="password" id="passwordField" class="form-control"
                            placeholder="••••••••" required>
                        <button type="button" class="btn btn-show-pass" id="togglePassword">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" id="btnIngresar" class="btn btn-login w-100">
                    <span>Ingresar al Sistema</span>
                </button>
            </form>

            <!-- ============================================
                 SEPARADOR + BOTÓN CREAR CUENTA
                 ============================================ -->
            <div class="signup-divider">
                <span class="text-white">¿Nuevo por aquí?</span>
            </div>

            <a href="/myvet/registro.php" class="btn-signup">
                <i class="bi bi-person-plus-fill"></i>
                <span>¿No tienes cuenta? Crear una</span>
            </a>

            <div class="card-footer-text">
                © <?php echo date('Y'); ?> <span class="accent">MYVET SISTEM</span><br>
                Desarrollado por JSEA — Todos los derechos reservados
            </div>

        </div>
    </div>

    <!-- JS Bootstrap & Lógica -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ============================================
        // PARALLAX DEL FONDO CON EL MOUSE
        // ============================================
        const heroBg = document.getElementById('heroBg');
        const glassCard = document.getElementById('glassCard');
        const isMobile = window.matchMedia('(max-width: 576px)').matches;

        if (!isMobile) {
            let mouseX = 0, mouseY = 0;
            let currentX = 0, currentY = 0;
            const strength = 25;
            const cardStrength = 10;

            document.addEventListener('mousemove', (e) => {
                mouseX = (e.clientX / window.innerWidth - 0.5) * 2;
                mouseY = (e.clientY / window.innerHeight - 0.5) * 2;
            });

            function animateParallax() {
                currentX += (mouseX - currentX) * 0.06;
                currentY += (mouseY - currentY) * 0.06;

                heroBg.style.transform =
                    `translate(${currentX * strength}px, ${currentY * strength}px)`;

                if (glassCard) {
                    glassCard.style.transform =
                        `translate(${currentX * cardStrength}px, ${currentY * cardStrength}px)`;
                }

                requestAnimationFrame(animateParallax);
            }

            animateParallax();
        }

        // ============================================
        // PARTÍCULAS FLOTANTES
        // ============================================
        const particlesContainer = document.getElementById('particles');
        const particleCount = 18;

        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.classList.add('particle');
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDuration = (Math.random() * 15 + 15) + 's';
            particle.style.animationDelay = (Math.random() * 15) + 's';
            particle.style.width = particle.style.height = (Math.random() * 3 + 2) + 'px';
            particle.style.opacity = Math.random() * 0.6 + 0.3;
            particlesContainer.appendChild(particle);
        }

        // ============================================
        // TILT 3D DE LA TARJETA AL MOVER EL MOUSE
        // ============================================
        if (!isMobile && glassCard) {
            glassCard.addEventListener('mousemove', (e) => {
                const rect = glassCard.getBoundingClientRect();
                const x = (e.clientX - rect.left) / rect.width - 0.5;
                const y = (e.clientY - rect.top) / rect.height - 0.5;

                const rotateX = -y * 6;
                const rotateY = x * 6;

                glassCard.style.transform =
                    `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-4px) scale(1.01)`;
            });

            glassCard.addEventListener('mouseleave', () => {
                glassCard.style.transform = '';
            });
        }

        // ============================================
        // VER/OCULTAR CONTRASEÑA
        // ============================================
        const togglePassword = document.querySelector('#togglePassword');
        const passwordField = document.querySelector('#passwordField');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function () {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            eyeIcon.classList.toggle('bi-eye');
            eyeIcon.classList.toggle('bi-eye-slash');
        });

        // ============================================
        // LÓGICA DE LOGIN
        // ============================================
        document.getElementById('formLogin').addEventListener('submit', async (e) => {
            e.preventDefault();

            const btn = document.getElementById('btnIngresar');
            const originalText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Validando...`;

            const formData = new FormData(e.target);

            try {
                const response = await fetch('/myvet/registraCuenta?action=login', {
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
                        background: '#1e1235',
                        color: '#fff'
                    }).then(() => {
                        window.location.href = res.redirect;
                    });
                } else {
                    Swal.fire({
                        icon: res.status,
                        title: 'Atención',
                        text: res.message,
                        confirmButtonColor: '#7c3aed',
                        background: '#1e1235',
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
                    confirmButtonColor: '#7c3aed',
                    background: '#1e1235',
                    color: '#fff'
                });
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    </script>
</body>

</html>
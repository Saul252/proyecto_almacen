<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    
    header("Location: /myvet/index.php");
    exit;
}

require_once __DIR__ . '/permisos.php';

function renderSidebar(string $paginaActual = '')
{
    $archivoActual = basename($_SERVER['PHP_SELF']);
?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    date.timezone = "America/Mexico_City"
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    if (!toggleBtn || !sidebar) return;

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('hidden');
        document.body.classList.toggle('sidebar-hidden');
    });
});
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
/* ===== NAVBAR PREMIUM ===== */
.navbar-premium {
    background: #111827;
    height: 65px;
    border-bottom: 1px solid rgba(255, 255, 255, .05);
    z-index: 1050;
}

.btn-toggle { background: transparent;  }

.user-badge {
    display: flex;
    align-items: center;
    background: rgba(255, 255, 255, .08);
    padding: 6px 14px;
    border-radius: 50px;
    color: #ffffff;
    font-weight: 500;
    font-size: 14px;
}

.btn-logout {
    background: transparent;
    
    color: #ffffff;
    font-size: 20px;
    transition: .2s ease;
}

.btn-logout:hover { color: #dc3545; transform: scale(1.1); }

/* ===== SIDEBAR ===== */
#sidebar {
    width: 0px;
    height: 100vh;
    position: fixed;
    top: 65px;
    left: 0;
    background: #f9fafb;
    border-right: 1px solid #e5e7eb;
    box-shadow: 0 10px 30px rgba(0, 0, 0, .05);
    transition: .3s ease;
    z-index: 1040;
}

#sidebar.hidden { transform: translateX(-100%); }
body.sidebar-hidden #sidebar { transform: translateX(-100%); }
#sidebar h5 { font-weight: 600; color: #111827; }

/* Links */
#sidebar .nav-link {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #4b5563 !important;
    border-radius: 12px;
    padding: 11px 16px;
    font-weight: 500;
    transition: all .2s ease;
}

#sidebar .nav-link:hover { background: #f3f4f6; color: #111827 !important; }
#sidebar .nav-link.active {
    background: linear-gradient(90deg, #e0edff, #f0f7ff);
    color: #0d6efd !important;
    font-weight: 600;
}

/* Estilo para el Badge de Notificaciones */
#notif-badge {
    font-size: 0.65rem;
    padding: 0.25em 0.45em;
    transform: translate(20%, -20%);
}
.notif-item {
    transition: background 0.2s;
    border-bottom: 1px solid #f0f0f0;
}
.notif-item:hover { background: #f8f9fa; }
.btn-recibir-fast {
    padding: 2px 8px;
    font-size: 0.75rem;
    border-radius: 6px;
}
</style>

<nav class="navbar fixed-top navbar-expand-lg navbar-dark navbar-premium">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-toggle" id="toggleSidebar">
                <i class="bi bi-list fs-3 text-white"></i>
            </button>
            <span class="fw-semibold text-white"><?= $paginaActual ?></span>
        </div>

        <div class="d-flex align-items-center gap-3">
           <div class="dropdown me-2">
    <a href="javascript:void(0);" class="text-white position-relative" id="btnNotif">
        <i class="bi bi-bell fs-4"></i>
        <span id="notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">0</span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end shadow-lg  p-0" id="menuNotif" style="width: 320px; max-height: 400px; overflow-y: auto; display: none; position: absolute; right: 0;">
        <li class="p-3 border-bottom bg-light">
            <h6 class="mb-0 fw-bold text-dark">Traspasos Pendientes</h6>
        </li>
        <div id="lista-notificaciones">
            <li class="p-3 text-center text-muted small">Cargando...</li>
        </div>
        <li><hr class="dropdown-divider m-0"></li>
        <li><a class="dropdown-item text-center py-2 small text-primary fw-bold" href="/myvet/vistas/almacen/traspasos.php">Ver todos</a></li>
    </ul>
</div>

            <div class="user-badge">
                <i class="bi bi-person-circle me-2"></i>
                <span><?= $_SESSION['nombre'] ?? 'Usuario' ?></span>
            </div>
            <a href="/myvet/logout.php" class="btn btn-logout"><i class="bi bi-box-arrow-right"></i></a>
        </div>
    </div>
</nav>
<aside id="sidebar">
    <div class="p-3">
        <h5 class="text-center mb-4">Menú</h5>
        <?php if (!empty($_SESSION['rol'])): ?>
            <div class="text-center small text-secondary mb-3">Rol: <?= ucfirst($_SESSION['rol']) ?></div>
        <?php endif; ?>

        <ul class="nav nav-pills flex-column gap-1">
            <?php if (puedeVerModulo('inicio')): ?>
            <li class="nav-item">
                <a href="/myvet/app/views/inicio.php" class="nav-link <?= $archivoActual == 'inicio.php' ? 'active' : '' ?>">
                    <i class="bi bi-house-door"></i><span>Inicio</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('ventas')): ?>
            <li class="nav-item">
                <a href="/myvet/app/controllers/ventasController.php" class="nav-link <?= $archivoActual == 'ventasController.php' ? 'active' : '' ?>">
                    <i class="bi bi-cart-check"></i><span>Ventas</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('almacenes')): ?>
            <li class="nav-item">
                <a href="/myvet/app/views/almacenes.php" class="nav-link <?= ($archivoActual == 'almacenes.php' || $archivoActual == 'almacen.php') ? 'active' : '' ?>">
                    <i class="bi bi-box-seam"></i><span>Almacén</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('movimientos')): ?>
            <li class="nav-item">
                <a href="/myvet/app/views/movimientos.php" class="nav-link <?= $archivoActual == 'movimientos.php' ? 'active' : '' ?>">
                    <i class="bi bi-arrow-left-right"></i><span>Movimientos</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('ventashistorial')): ?>
            <li class="nav-item">
                <a href="/myvet/app/views/ventashistorial.php" class="nav-link <?= $archivoActual == 'ventashistorial.php' ? 'active' : '' ?>">
                    <i class="bi bi-receipt"></i><span>Historial</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('caja')): ?>
            <li class="nav-item">
                <a href="/myvet/pantallas/caja.php" class="nav-link <?= $archivoActual == 'caja.php' ? 'active' : '' ?>">
                    <i class="bi bi-cash-stack"></i><span>Caja</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('usuarios')): ?>
            <li class="nav-item">
                <a href="/myvet/app/views/usuarios.php" class="nav-link <?= $archivoActual == 'usuarios.php' ? 'active' : '' ?>">
                    <i class="bi bi-people"></i><span>Usuarios</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('compras')): ?>
            <li class="nav-item">
                <a href="/myvet/app/controllers/egresosController.php" class="nav-link <?= ($archivoActual == 'compras.php' || $archivoActual == 'gastos.php') ? 'active' : '' ?>">
                    <i class="bi bi-bag-check"></i><span>Compras</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('clientes')): ?>
            <li class="nav-item">
                <a href="/myvet/app/controllers/clientes.php" class="nav-link <?= $archivoActual == 'clientes.php' ? 'active' : '' ?>">
                    <i class="bi bi-person-lines-fill"></i><span>Clientes</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('mermas')): ?>
            <li class="nav-item">
                <a href="/myvet/pantallas/mermas.php" class="nav-link <?= $archivoActual == 'mermas.php' ? 'active' : '' ?>">
                    <i class="bi bi-exclamation-triangle"></i><span>Mermas</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('finanzas')): ?>
            <li class="nav-item">
                <a href="/myvet/app/views/finanzas.php" class="nav-link <?= $archivoActual == 'finanzas.php' ? 'active' : '' ?>">
                    <i class="bi bi-graph-up-arrow"></i><span>Finanzas</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</aside>
<script>
let ultimoConteoTraspasos = 0;

// --- NUEVO: Solicitar permisos de notificación apenas cargue la página ---
if (Notification.permission !== "granted" && Notification.permission !== "denied") {
    Notification.requestPermission();
}

// 1. Lógica para abrir/cerrar el menú manualmente
document.addEventListener('click', function(e) {
    const btn = document.getElementById('btnNotif');
    const menu = document.getElementById('menuNotif');
    
    if (!btn || !menu) return;

    if (btn.contains(e.target)) {
        menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
        e.preventDefault();
    } 
    else if (!menu.contains(e.target)) {
        menu.style.display = 'none';
    }
});

// 2. Función para aceptar traspaso
function aceptarTraspasoRapido(idMovimiento) {
    if (!confirm("¿Confirmas la recepción? El stock se actualizará y la página se recargará.")) return;

    const formData = new FormData();
    formData.append('id', idMovimiento);

    fetch('/myvet/app/backend/movimientos/procesar_transaccion_rapida.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success || data.status === 'success') {
            location.reload(); 
        } else {
            alert("❌ Error: " + (data.error || data.message));
        }
    })
    .catch(err => {
        console.error("Error:", err);
        alert("Error de conexión.");
    });
}

// 3. Consulta de datos y Disparo de Notificación
function verificarNotificaciones() {
    fetch('/myvet/app/backend/movimientos/get_notificaciones_traspaso.php?t=' + Date.now())
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notif-badge');
            const lista = document.getElementById('lista-notificaciones');
            const cantidadActual = parseInt(data.cantidad) || 0;
            
            if (badge) {
                badge.innerText = cantidadActual;
                cantidadActual > 0 ? badge.classList.remove('d-none') : badge.classList.add('d-none');
            }

            // --- NUEVO: Disparar notificación de escritorio si el número subió ---
            if (cantidadActual > ultimoConteoTraspasos) {
                if (Notification.permission === "granted") {
                    new Notification("📦 Nuevo Traspaso", {
                        body: `Tienes ${cantidadActual} producto(s) pendientes de recibir en tu almacén.`,
                        icon: '/myvet/assets/img/logo.png' // Verifica que esta ruta exista
                    });
                }
            }
            // Actualizamos el contador para la siguiente revisión
            ultimoConteoTraspasos = cantidadActual;

            if (lista && data.items) {
                if (cantidadActual === 0) {
                    lista.innerHTML = '<li class="p-3 text-center text-muted small">Sin pendientes</li>';
                } else {
                    lista.innerHTML = data.items.map(item => `
                        <li class="p-2 border-bottom d-flex justify-content-between align-items-center mx-2">
                            <div style="font-size: 0.8rem; max-width: 80%">
                                <b>${item.producto}</b><br>
                                <span class="text-muted">Cant: ${item.cantidad}</span>
                            </div>
                            <button onclick="aceptarTraspasoRapido(${item.id})" class="btn btn-sm btn-success p-1">
                                <i class="bi bi-check2"></i>
                            </button>
                        </li>
                    `).join('');
                }
            }
        });
}

// Iniciar
document.addEventListener('DOMContentLoaded', () => {
    verificarNotificaciones();
    setInterval(verificarNotificaciones, 30000);
});
</script>
<?php
}
?>
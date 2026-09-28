console.log("✅ notificaciones.js cargado correctamente");

let ultimoConteoTraspasos = 0;

function verificarNotificaciones() {
    const url = '/myvet/app/backend/movimientos/get_notificaciones_traspaso.php?t=' + Date.now();
    console.log("🔍 Intentando consultar:", url);

    fetch(url)
        .then(response => {
            console.log("📡 Respuesta del servidor recibida. Status:", response.status);
            if (!response.ok) throw new Error("Error en la red: " + response.status);
            return response.json();
        })
        .then(data => {
            console.log("📦 Datos recibidos:", data);
            
            const badge = document.getElementById('notif-badge');
            const lista = document.getElementById('lista-notificaciones');
            const cantidadActual = parseInt(data.cantidad) || 0;
            
            if (badge) {
                console.log("🔴 Actualizando badge a:", cantidadActual);
                badge.innerText = cantidadActual;
                cantidadActual > 0 ? badge.classList.remove('d-none') : badge.classList.add('d-none');
            } else {
                console.error("❌ No se encontró el elemento 'notif-badge' en el HTML");
            }

            if (cantidadActual > ultimoConteoTraspasos) {
                console.log("🔔 ¡Nuevas notificaciones detectadas! Disparando Toastify...");
                if (typeof Toastify === "function") {
                    Toastify({
                        text: `📦 NUEVO TRASPASO: Tienes ${cantidadActual} pendientes.`,
                        duration: 7000,
                        style: { background: "linear-gradient(to right, #00b09b, #96c93d)" }
                    }).showToast();
                } else {
                    console.error("❌ Librería Toastify no detectada. Revisa el orden de carga en el layout.");
                }
            }

            ultimoConteoTraspasos = cantidadActual;

            if (lista && data.items) {
                console.log("📝 Llenando lista de notificaciones...");
                if (cantidadActual === 0) {
                    lista.innerHTML = '<li class="p-3 text-center text-body-secondary small">Sin pendientes</li>';
                } else {
                    lista.innerHTML = data.items.map(item => `
                        <li class="p-2 border-bottom d-flex justify-content-between align-items-center mx-2">
                            <div style="font-size: 0.8rem; max-width: 75%">
                                <b>${item.producto}</b><br>
                                <span class="text-body-secondary">Cant: ${item.cantidad}</span>
                            </div>
                            <button onclick="procesarRecepcion(${item.id})" class="btn btn-sm btn-success p-1">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        </li>
                    `).join('');
                }
            }
        })
        .catch(err => {
            console.error("❌ Error en verificarNotificaciones:", err);
        });
}

// Lógica del Clic del Menú
document.addEventListener('click', function(e) {
    const btn = document.getElementById('btnNotif');
    const menu = document.getElementById('menuNotif');
    
    if (!btn || !menu) {
        console.warn("⚠️ Advertencia: No se encontró 'btnNotif' o 'menuNotif' en esta página.");
        return;
    }

    if (btn.contains(e.target)) {
        console.log("🖱️ Clic en campana. Estado actual menu:", menu.style.display);
        menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
        console.log("🖱️ Nuevo estado menu:", menu.style.display);
        e.preventDefault();
    } else if (!menu.contains(e.target)) {
        menu.style.display = 'none';
    }
});

document.addEventListener('DOMContentLoaded', () => {
    console.log("🚀 DOM cargado. Iniciando verificación cada 30s...");
    verificarNotificaciones();
    setInterval(verificarNotificaciones, 30000);
});
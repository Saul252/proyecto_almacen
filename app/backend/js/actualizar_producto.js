      // Esperar a que el DOM esté cargado
document.addEventListener('DOMContentLoaded', function() {
    const formEditar = document.getElementById('formEditarProducto');

    if (formEditar) {
        formEditar.addEventListener('submit', function(e) {
            e.preventDefault(); // Evita que la página se refresque
            
            // 1. Recopilar datos del formulario
            const formData = new FormData(this);

            // 2. Alerta visual de "Procesando"
            Swal.fire({
                title: 'Guardando cambios...',
                text: 'Actualizando información del producto',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // 3. Petición al servidor (Controlador)
            fetch('/myvet/app/backend/almacen/actualizar_producto.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Error en la red o servidor');
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    // Éxito: Notificar y recargar para ver cambios
                    Swal.fire({
                        icon: 'success',
                        title: '¡Actualizado!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload(); 
                    });
                } else {
                    // Error reportado por el PHP
                    Swal.fire('Error al guardar', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error Crítico', 'No se pudo conectar con el servidor', 'error');
            });
        });
    }
});
  
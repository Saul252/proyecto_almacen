 /**
     * Procesa la autorización/recepción de un traspaso
     * @param {number} movId - ID del registro en la tabla movimientos
     */function autorizarRecibo(movId) {
    // 1. Pedir confirmación al usuario
    Swal.fire({
        title: '¿Confirmar recepción?',
        text: "La mercancía se sumará al stock de este almacén y el movimiento quedará cerrado.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754', 
        cancelButtonColor: '#d33',
        confirmButtonText: '<i class="bi bi-check-lg"></i> Sí, recibir',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {

            // 2. Mostrar estado de carga
            Swal.fire({
                title: 'Procesando...',
                text: 'Actualizando inventario y folios',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // 3. Preparar los datos
            const formData = new FormData();
            formData.append('id', movId);

            // 4. Petición al Backend
           fetch('/myvet/app/controllers/traspasosController.php?action=recibirTraspaso', {
                     method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error('Error en la respuesta del servidor');
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        // 5. Éxito: Notificar y recargar
                        Swal.fire({
                            title: '¡Recibido!',
                            text: data.message,
                            icon: 'success',
                            timer: 1500, // Bajé un poco el tiempo para que sea más fluido
                            showConfirmButton: false
                        }).then(() => {
                            // ESTA ES LA LÍNEA CLAVE:
                            // Recarga la página completa para actualizar stocks en la tabla principal
                            location.reload(); 
                        });
                    } else {
                        // Error controlado (ej: ya recibido)
                        Swal.fire('Atención', data.message, 'warning');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error Crítico',
                        'No se pudo conectar con el servidor o hubo un error en el proceso.',
                        'error');
                });
        }
    });
}  
   
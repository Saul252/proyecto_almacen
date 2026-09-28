function abrirModalNuevoCliente() {
    new bootstrap.Modal(document.getElementById('modalNuevoCliente')).show();
}

document.getElementById('formNuevoCliente').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);

    Swal.fire({
        title: 'Guardando cliente...',
        allowOutsideClick: false,
        
        didOpen: () => { Swal.showLoading(); },
         customClass: {
        popup: 'swal-zindex'
    }
    });

    fetch('/myvet/app/controllers/clientesController.php?action=guardar', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if(res.success === true) {
           Swal.fire({
    title: '¡Éxito!',
    text: res.message,
    icon: 'success',
    customClass: {
        popup: 'swal-zindex'
    }
});
            
            const selectCliente = document.getElementById('selectCliente');
            
            // --- LÓGICA DE ACTUALIZACIÓN DINÁMICA ---
            if (selectCliente) {
                // Obtenemos el almacén que se está visualizando actualmente en la pantalla de ventas
                // Si tienes un input que guarda el ID del almacén actual, úsalo aquí.
                // Si no, usamos el que se acaba de enviar en el formulario.
                const idAlmacenDestino = formData.get('almacen_id');
                const idAlmacenActualVentas = document.getElementById('almacen_id_actual')?.value || idAlmacenDestino;

                // Solo agregamos al select si el cliente pertenece al almacén que estamos operando
                if (idAlmacenDestino == idAlmacenActualVentas) {
                    const nombre = formData.get('nombre_comercial');
                    const option = new Option(nombre, res.id, true, true);
                    
                    // Inyectar metadatos (VITAL para facturación)
                    option.setAttribute('data-rfc', formData.get('rfc'));
                    option.setAttribute('data-rs', formData.get('razon_social'));
                    option.setAttribute('data-cp', formData.get('codigo_postal'));
                    option.setAttribute('data-regimen', formData.get('regimen_fiscal'));
                    
                    selectCliente.add(option);
                    selectCliente.dispatchEvent(new Event('change'));
                } else {
                    console.log("Cliente guardado en otro almacén. No se agrega al select actual.");
                }
            }
            
            // --- CERRAR Y LIMPIAR ---
            const modalElement = document.getElementById('modalNuevoCliente');
            const modal = bootstrap.Modal.getInstance(modalElement);
            if(modal) modal.hide();
            
            this.reset();
            
            // Si el usuario es admin y tiene la tabla general de clientes abierta, se refresca
            if (typeof fetchData === 'function') {
                const filtro = document.getElementById('filtroAlmacen')?.value || 0;
                fetchData(filtro);
            }

        } else {
           Swal.fire({
    title: 'Error',
    text: res.message || 'Error desconocido',
    icon: 'error',
    customClass: {
        popup: 'swal-zindex'
    }
});
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
    });
});
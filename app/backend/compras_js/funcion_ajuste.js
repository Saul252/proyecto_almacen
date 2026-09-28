function abrirAjuste(id) {
    // 1. Preparar el modal y limpiar datos previos
    $('#ajuste_compra_id').val(id);
    $('#tablaAjuste').html(`
        <tr>
            <td colspan="4" class="text-center p-4">
                <div class="spinner-border text-primary spinner-border-sm"></div> 
                <span class="ms-2">Consultando pendientes en base de datos...</span>
            </td>
        </tr>
    `);
    modalAjusteForm.show();

    // 2. Cargar datos desde el backend corregido
    $.getJSON('/myvet/app/backend/compras/obtener_pendientes.php', { id: id }, function(data) {
        let html = '';
        if (data && data.length > 0) {
            data.forEach(item => {
                // El campo 'almacen_original' viene de la consulta SQL que ajustamos antes
                html += `
                <tr class="align-middle">
                    <td>
                        <div class="fw-bold">${item.nombre}</div>
                        <small class="text-body-secondary">SKU: ${item.sku}</small>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-danger fs-6">${item.cantidad_faltante}</span>
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.01" min="0" 
                                   class="form-control fw-bold border-primary input-cantidad-ajuste" 
                                   name="ajuste[${item.id}][cantidad]" 
                                   max="${item.cantidad_faltante}" 
                                   value="${item.cantidad_faltante}"
                                   oninput="validarCantidadInput(this, ${item.cantidad_faltante})">
                        </div>
                        <input type="hidden" name="ajuste[${item.id}][producto_id]" value="${item.producto_id}">
                        <input type="hidden" name="ajuste[${item.id}][detalle_id]" value="${item.id}">
                    </td>
                    <td>
                        <select class="form-select form-select-sm" name="ajuste[${item.id}][almacen_id]" required>
                            ${almacenes.map(a => `
                                <option value="${a.id}" ${a.id == item.almacen_original ? 'selected' : ''}>
                                    ${a.nombre}
                                </option>
                            `).join('')}
                        </select>
                    </td>
                </tr>`;
            });
        } else {
            html = `
                <tr>
                    <td colspan="4" class="text-center p-4">
                        <i class="bi bi-check-circle-fill text-success fs-2"></i>
                        <p class="mb-0 mt-2 text-body-secondary">No se encontraron productos con faltantes para esta compra.</p>
                    </td>
                </tr>`;
        }
        $('#tablaAjuste').html(html);
    }).fail(function() {
        $('#tablaAjuste').html('<tr><td colspan="4" class="text-center text-danger">Error al cargar datos. Verifique conexión.</td></tr>');
    });
}

/**
 * Función de seguridad para evitar ingresos superiores al faltante real
 */
function validarCantidadInput(input, max) {
    let valor = parseFloat(input.value);
    if (valor > max) {
        input.classList.add('is-invalid');
        input.value = max;
        // Pequeña notificación visual opcional
        console.warn("Se intentó ingresar más de lo pendiente.");
    } else {
        input.classList.remove('is-invalid');
    }
}

/**
 * Procesamiento del formulario con SweetAlert2
 */
$('#formAjuste').on('submit', function(e) {
    e.preventDefault();
    
    const btn = $(this).find('button[type="submit"]');
    const originalText = btn.text();

    Swal.fire({
        title: '¿Confirmar Recepción?',
        text: "Se actualizará el inventario y se reducirá la lista de faltantes.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, recibir ahora',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Procesando...');

            $.ajax({
                url: '/myvet/app/backend/compras/procesar_ajuste.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    if(res.status === 'success') {
                        modalAjusteForm.hide();
                        Swal.fire({
                            title: "¡Actualizado!",
                            text: res.message,
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire("Error", res.message, "error");
                        btn.prop('disabled', false).text(originalText);
                    }
                },
                // Reemplaza tu bloque error en el $.ajax por este:
error: function(xhr, status, error) {
    console.error("Status: " + status);
    console.error("Error: " + error);
    console.error("Respuesta del servidor: " + xhr.responseText); // ESTO TE DIRÁ EL ERROR REAL
    
    Swal.fire({
        icon: 'error',
        title: 'Error en el servidor',
        text: 'El servidor respondió con un error. Revisa la consola (F12) para más detalles.'
    });
    btn.prop('disabled', false).text(originalText);
}
            });
        }
    });
});
 // GUARDADO PRODUCTO NUEVO (MANTENIDO)
    $('#formNuevoProducto').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '/myvet/app/backend/almacen/guardar_producto_simple.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(r) {
                let res = JSON.parse(r);
                if (res.status === 'success') {
                    $('.select-prod').append(
                        `<option value="${res.id}">${res.nombre} (${res.sku})</option>`);
                    modalProd.hide();
                    Swal.fire('Éxito', 'Producto creado', 'success');
                }
            }
        });
    });

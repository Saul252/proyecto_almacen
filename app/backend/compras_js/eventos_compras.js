// Manejo de Nueva Categoría
document.getElementById('formNuevaCategoria').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form);
    const nombreCategoria = formData.get('nombre');

    Swal.fire({ title: 'Guardando...', didOpen: () => { Swal.showLoading(); } });

    fetch('/myvet/app/backend/almacen/guardar_categoria.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            modalCat.hide();
            const selects = document.querySelectorAll('select[name="categoria_id"]');
            selects.forEach(select => {
                select.add(new Option(nombreCategoria, res.id, true, true));
            });
            form.reset();
            Swal.fire({ icon: 'success', title: '¡Listo!', timer: 1500, showConfirmButton: false });
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    });
});

// Manejo de Producto Nuevo Simple
$('#formNuevoProducto').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: '/myvet/app/backend/almacen/guardar_producto_simple.php',
        type: 'POST',
        data: $(this).serialize(),
        success: function(r) {
            let res = JSON.parse(r);
            if (res.status === 'success') {
                $('.select-prod').append(`<option value="${res.id}">${res.nombre} (${res.sku})</option>`);
                modalProd.hide();
                Swal.fire('Éxito', 'Producto creado', 'success');
            }
        }
    });
});
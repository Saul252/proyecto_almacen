 

        let instanciaModalCat;
        function abrirModalCategoria() {
            if (!instanciaModalCat) {
                instanciaModalCat = new bootstrap.Modal(document.getElementById('modalCategoria'));
            }
            instanciaModalCat.show();
        }

        // CONFIRMACIÓN DE ENVÍO
        function confirmarEnvio(event) {
            event.preventDefault();
            const formulario = event.target;
            const checkboxes = formulario.querySelectorAll('input[type="checkbox"]:checked');

            if (checkboxes.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Atención', text: 'Debes seleccionar al menos un almacén.' });
                return;
            }

            Swal.fire({
                title: '¿Guardar producto?',
                text: "Se registrará el producto y sus stocks iniciales.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                confirmButtonText: 'Sí, guardar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                    formulario.submit();
                }
            });
        }
 function abrirModal(tipo) {
        $('#tipo_egreso').val(tipo);
        $('#formEgreso')[0].reset();
        $('#contenedorItems').empty();
        $('#txtDiferencia').text('$ 0.00').removeClass('text-danger text-success');

        if (tipo === 'compra') {
            $('#modalHeader').addClass('bg-gradient-primary').removeClass('bg-gradient-warning');
            $('#lblEntidad').text('PROVEEDOR');
        } else {
            $('#modalHeader').addClass('bg-gradient-warning').removeClass('bg-gradient-primary');
            $('#lblEntidad').text('BENEFICIARIO (PAGO A)');
        }
        agregarFila();
        modalRegistro.show();
    }

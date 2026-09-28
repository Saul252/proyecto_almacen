  document.addEventListener("DOMContentLoaded", function() {
    const selectCliente = document.getElementById('selectCliente');
    
    // Función para actualizar los textos de datos fiscales
    function actualizarDatosFiscales() {
        const selected = selectCliente.options[selectCliente.selectedIndex];
        
        document.getElementById('f_razon_social').innerText = selected.getAttribute('data-rs') || '-';
        document.getElementById('f_rfc').innerText = selected.getAttribute('data-rfc') || '-';
        document.getElementById('f_cp').innerText = selected.getAttribute('data-cp') || '-';
        document.getElementById('f_regimen').innerText = selected.getAttribute('data-regimen') || '-';
    }

    // Escuchar el cambio
    if(selectCliente) {
        selectCliente.addEventListener('change', actualizarDatosFiscales);
        // Cargar datos por defecto al abrir modal
        actualizarDatosFiscales(); 
    }
});
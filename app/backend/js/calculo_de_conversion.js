/**
 * Cálculos de conversión de unidades y validación de stock
 */

function actualizarLimiteMaestro() {
    const inputCant = document.getElementById('inputLlegadaMaestra');
    const inputFactor = document.getElementById('inputFactor');
    const displayBultos = document.getElementById('displayLimiteBultos');

    // Verificación de seguridad: si los elementos no existen en el DOM actual, salir.
    if (!inputCant || !inputFactor) return;

    const cant = parseFloat(inputCant.value) || 0;
    const factor = parseFloat(inputFactor.value) || 1;

    // Lógica: Cantidad (ej. 1 Ton) * Factor (ej. 40) = 40 unidades base
    const limiteFinal = cant * factor;

    window.limiteGlobalPiezas = limiteFinal;
    
    if (displayBultos) {
        displayBultos.innerText = limiteFinal.toFixed(2);
    }
    
    validarReparto();
}

function validarReparto() {
    let sumaAsignada = 0;
    const inputs = document.querySelectorAll('.input-calculo');
    
    inputs.forEach(input => {
        sumaAsignada += parseFloat(input.value) || 0;
    });

    const displayAsignado = document.getElementById('displayAsignado');
    const displayRestante = document.getElementById('displayRestante');
    const btnGuardar = document.getElementById('btnGuardarProducto');
    const limite = window.limiteGlobalPiezas || 0;
    
    let restante = limite - sumaAsignada;
    
    if (displayAsignado) displayAsignado.innerText = sumaAsignada.toFixed(2);
    if (displayRestante) displayRestante.innerText = restante.toFixed(2);

    if (!btnGuardar) return;

    if (sumaAsignada > (limite + 0.001) && limite > 0) {
        if (displayAsignado) displayAsignado.classList.add('text-danger');
        btnGuardar.disabled = true;
        btnGuardar.classList.remove('btn-success');
        btnGuardar.classList.add('btn-danger');
        btnGuardar.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i> EXCESO DE UNIDADES';
    } else {
        if (displayAsignado) displayAsignado.classList.remove('text-danger');
        btnGuardar.disabled = false;
        btnGuardar.classList.remove('btn-danger');
        btnGuardar.classList.add('btn-success');
        btnGuardar.innerHTML = '<i class="bi bi-save me-2"></i> GUARDAR PRODUCTO';
    }
}
  <div class="modal fade modalc" id="modalNuevoCliente" tabindex="-1" aria-labelledby="modalNuevoClienteLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formNuevoCliente">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalNuevoClienteLabel">
                            <i class="fas fa-user-plus me-2"></i>Registrar Nuevo Cliente
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="almacen_id" value="<?= $almacen_usuario ?>">

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Nombre Comercial *</label>
                                <input type="text" name="nombre_comercial" class="form-control"
                                    placeholder="Ej. Materiales El Centro" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Razón Social</label>
                                <input type="text" name="razon_social" class="form-control"
                                    placeholder="Nombre legal completo">
                            </div>

 <div class="col-md-12">
                                <label class="form-label fw-bold">Contacto *</label>
                                <input type="text" name="contacto" class="form-control"
                                    placeholder="Contacto" >
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">RFC *</label>
                                <input type="text" name="rfc" class="form-control text-uppercase" maxlength="13"
                                    placeholder="ABCD000000XXX" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Código Postal *</label>
                                <input type="text" name="codigo_postal" class="form-control" maxlength="5"
                                    placeholder="00000" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Régimen Fiscal</label>
                                <input type="text" name="regimen_fiscal" class="form-control" maxlength="3"
                                    placeholder="Ej. 601">
                                <small class="text-body-secondary">Clave del catálogo del SAT</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Uso de CFDI</label>
                                <select name="uso_cfdi" class="form-select">
                                    <option value="G03" selected>G03 - Gastos en general</option>
                                    <option value="S01">S01 - Sin efectos fiscales</option>
                                    <option value="G01">G01 - Adquisición de mercancías</option>
                                    <option value="P01">P01 - Por definir</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Correo Electrónico</label>
                                <input type="email" name="correo" class="form-control" placeholder="cliente@correo.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Teléfono</label>
                                <input type="tel" name="telefono" class="form-control text-uppercase" placeholder="55 0000 0000">
                            </div>

                           <div class="col-md-12">
                                <label class="form-label fw-bold">Calle</label>
                                <textarea name="calle" class="form-control text-uppercase" rows="2"
                                    placeholder="Calle y número"></textarea>
                            </div>
                             <div class="col-md-12">
                                <label class="form-label fw-bold">Colonia</label>
                                <textarea name="colonia" class="form-control text-uppercase" rows="2"
                                    placeholder="Colonia..."></textarea>
                            </div>
                             <div class="col-md-12">
                                <label class="form-label fw-bold">Pueblo</label>
                                <textarea name="pueblo" class="form-control text-uppercase" rows="2"
                                    placeholder="Pueblo"></textarea>
                            </div>
                             <div class="col-md-12">
                                <label class="form-label fw-bold">Ciudad</label>
                                <textarea name="ciudad" class="form-control text-uppercase" rows="2"
                                    placeholder="Ciudad"></textarea>
                            </div>
                            <div class="row g-3">
                                <?php if ($_SESSION['almacen_id'] == 0): ?>
                                <div class="col-md-12 mb-2">
                                    <label class="form-label fw-bold text-primary">Asignar a Almacén *</label>
                                    <select name="almacen_id" class="form-select border-primary" required>
                                        <option value="">-- Selecciona un almacén --</option>
                                        <?php foreach ($almacenes as $alm): ?>
                                        <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-body-secondary">Como administrador, debes elegir a qué sucursal pertenece
                                        este cliente.</small>
                                </div>
                                <?php else: ?>
                                <input type="hidden" name="almacen_id" value="<?= $almacen_usuario ?>">
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnGuardarCliente">
                            <i class="fas fa-save me-1"></i> Guardar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
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
            
            const selectCliente = document.getElementById('cliente_id');
            
            // --- LÓGICA DE ACTUALIZACIÓN DINÁMICA ---
            if (selectCliente) {
       
               
                    const nombre = formData.get('nombre_comercial');
                    const option = new Option(nombre, res.id, true, true);
                    
                    // Inyectar metadatos (VITAL para facturación)
                    option.setAttribute('data-rfc', formData.get('rfc'));
                    option.setAttribute('data-rs', formData.get('razon_social'));
                    option.setAttribute('data-cp', formData.get('codigo_postal'));
                    option.setAttribute('data-regimen', formData.get('regimen_fiscal'));
                    
                   $('#cliente_id')
    .append(option)
    .val(res.id)
    .trigger('change');
              
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
    </script>
<div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-dental-content">
            <form id="formCliente">
                <div class="modal-dental-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-person-vcard fs-4"></i>
                        <h5 class="modal-title fw-bold m-0" id="modalTitulo">Registro de Paciente</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4" style="background-color: var(--bg-card);">
                    <input type="hidden" name="cliente_id" id="cliente_id" value="0">

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted">NOMBRE COMPLETO / COMERCIAL *</label>
                            <input type="text" name="nombre_comercial" id="nombre_comercial"
                                class="form-control form-control-dental" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted">FECHA NACIMIENTO</label>
                            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"
                                class="form-control form-control-dental" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted">Responsable</label>
                            <input type="text" name="razon_social" id="razon_social"
                                class="form-control form-control-dental">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted">Sexo</label>
                            <select name="sexo" id="sexo" class="form-select form-control-dental text-uppercase"
                                required>
                                <option value="" disabled selected>-- SELECCIONA --</option>
                                <option value="HOMBRE">HOMBRE</option>
                                <option value="MUJER">MUJER</option>
                                <option value="HUMANO">HUMANO</option>

                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">CONTACTO / TUTOR *</label>
                            <input type="text" name="contacto" id="contacto" class="form-control form-control-dental"
                                placeholder="Nombre de contacto">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">TELÉFONO</label>
                            <input type="text" name="telefono" id="telefono" class="form-control form-control-dental">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">RFC *</label>
                            <input type="text" name="rfc" id="rfc"
                                class="form-control form-control-dental text-uppercase" maxlength="13">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">CORREO ELECTRÓNICO</label>
                            <input type="email" name="correo" id="correo" class="form-control form-control-dental">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted">CALLE Y NÚMERO</label>
                            <textarea name="calle" id="calle" class="form-control form-control-dental text-uppercase"
                                rows="2" placeholder="Calle, número exterior/interior..."></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">COLONIA</label>
                            <input type="text" name="colonia" id="colonia"
                                class="form-control form-control-dental text-uppercase">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">PUEBLO / SECTOR</label>
                            <input type="text" name="pueblo" id="pueblo"
                                class="form-control form-control-dental text-uppercase">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">CIUDAD / MUNICIPIO</label>
                            <input type="text" name="ciudad" id="ciudad"
                                class="form-control form-control-dental text-uppercase">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">CÓDIGO POSTAL</label>
                            <input type="text" name="codigo_postal" id="codigo_postal"
                                class="form-control form-control-dental text-uppercase" maxlength="5">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">USO CFDI</label>
                            <select name="uso_cfdi" id="uso_cfdi" class="form-select form-select-dental">
                                <?php foreach ($usosCFDI as $key => $val): ?>
                                    <option value="<?= $key ?>"><?= $key ?> - <?= $val ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if ($almacen_usuario == 0): ?>
                            <div class="col-md-12">
                                <div class="p-3 rounded-4 mt-2 border border-dashed"
                                    style="background-color: var(--bg-input); border-color: var(--border-color);">
                                    <label class="form-label small fw-bold text-info">ASIGNAR A SUCURSAL *</label>
                                    <select name="almacen_id" id="almacen_id_modal" class="form-select form-select-dental"
                                        required>
                                        <option value="">-- Seleccionar Almacén --</option>
                                        <?php foreach ($almacenes as $alm): ?>
                                            <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="almacen_id" value="<?= $almacen_usuario ?>">
                        <?php endif; ?>
                    </div>
                </div>

                <div class="modal-footer modal-footer-dental px-4 py-3">
                    <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none"
                        data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-dental-primary px-4">GUARDAR REGISTRO</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function nuevoCliente() {
        $('#formCliente')[0].reset();
        $('#cliente_id').val('0');
        $('#modalTitulo').text('Nuevo Registro de Paciente');

        const filtro = $('#filtroAlmacenVista').val();
        if (filtro) $('#almacen_id_modal').val(filtro);

        $('#modalCliente').modal('show');
    }
    async function editarCliente(id) {
        try {
            const resp = await fetch(`/myvet/app/controllers/accesoMedicController.php?action=obtenerPorId&id=${id}`);
            const res = await resp.json();
            if (res.success) {
                const c = res.data;
                console.log(c);
                $('#modalTitulo').text('Actualizar Expediente de Paciente');
                $('#cliente_id').val(c.id);
                $('#contacto').val(c.contacto);
                $('#nombre_comercial').val(c.nombre_comercial);
                $('#razon_social').val(c.razon_social);

                // 1. Limpiar la fecha para quitar la hora si es que la trae (ej: "2023-05-12 00:00:00" -> "2023-05-12")
                if (c.fecha_nacimiento) {
                    const fechaLimpiar = c.fecha_nacimiento.split(' ')[0];
                    $('#fecha_nacimiento').val(fechaLimpiar);
                } else {
                    $('#fecha_nacimiento').val('');
                }

                // 2. Forzar mayúsculas para que coincida exactamente con las opciones del select
                // Dentro de tu función editarCliente(id)
                if (c.sexo) {
                    $('#sexo').val(c.sexo.trim().toUpperCase());
                } else {
                    $('#sexo').val('');
                }
                $('#rfc').val(c.rfc);
                $('#telefono').val(c.telefono);

                const direccion = c.direccion || '';
                const calle = (direccion.match(/calle\s(.*?)(?=,\scol|$)/i) || [, ''])[1];
                const colonia = (direccion.match(/col\s(.*?)(?=,\spueblo|$)/i) || [, ''])[1];
                const pueblo = (direccion.match(/pueblo\s(.*?)(?=,\sciudad|$)/i) || [, ''])[1];
                const ciudad = (direccion.match(/ciudad\s(.*)$/i) || [, ''])[1];

                $('#calle').val(calle.trim());
                $('#colonia').val(colonia.trim());
                $('#pueblo').val(pueblo.trim());
                $('#ciudad').val(ciudad.trim());
                $('#correo').val(c.correo);
                $('#codigo_postal').val(c.codigo_postal);
                $('#almacen_id_modal').val(c.almacen_id);
                $('#modalCliente').modal('show');
            }
        } catch (e) { console.error(e); }
    }
    $('#formCliente').on('submit', async function (e) {
        e.preventDefault();
        try {
            obtenerOGenerarRFC();
            const resp = await fetch('/myvet/app/controllers/clientesPacientesDental.php?action=guardar', {
                method: 'POST',
                body: new FormData(this)
            });
            const res = await resp.json();
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-4' }
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        } catch (e) { console.error(e); }
    });
    function obtenerOGenerarRFC() {
        let rfc = $('#rfc').val() ? $('#rfc').val().trim() : '';
        let nombre = $('#nombre_comercial').val() ? $('#nombre_comercial').val().trim() : '';

        // Si el RFC tiene valor, lo limpiamos a mayúsculas y lo regresamos
        if (rfc !== '') {
            return rfc.toUpperCase();
        }

        // Si el RFC está vacío, lo generamos a partir del nombre comercial
        let limpio = nombre
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "") // quita acentos
            .toUpperCase()
            .replace(/[^A-Z\s]/g, "");     // solo letras y espacios

        let letras = limpio.replace(/\s+/g, '');

        let rfcGenerado = '';
        if (letras.length >= 4) {
            rfcGenerado = letras.substring(0, 4) + '010101XXX';
        } else if (letras.length > 0) {
            rfcGenerado = letras.padEnd(4, 'X') + '010101XXX';
        } else {
            // RFC genérico si no hay ni RFC ni nombre válido
            rfcGenerado = 'XAXX010101000';
        }
        $('#rfc').val(rfcGenerado);
        return rfcGenerado;
    }

</script>
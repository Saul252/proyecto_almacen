<!-- ============================================================= -->
<!-- MODAL: REGISTRO / EDICIÓN DE PACIENTE                         -->
<!-- IDs renombrados con prefijo modal_ — names intactos           -->
<!-- ============================================================= -->
<style>
    /* ===== Modal elegante ===== */
    #modalCliente .modal-content {
        border: 0;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15),
            0 4px 14px rgba(0, 0, 0, 0.05);
    }

    [data-bs-theme="dark"] #modalCliente .modal-content {
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.55),
            0 4px 14px rgba(0, 0, 0, 0.35);
    }

    /* Header */
    #modalCliente .modal-dental-header {
        background: linear-gradient(135deg, #376bd8, #2855b8);
        color: #fff;
        padding: 1.1rem 1.5rem;
        border-bottom: 0;
    }

    [data-bs-theme="dark"] #modalCliente .modal-dental-header {
        background: linear-gradient(135deg, #2a4fa8, #1d3c85);
    }

    /* Body */
    #modalCliente .modal-body {
        padding: 1.5rem;
        background-color: var(--bs-body-bg);
        color: var(--bs-body-color);
    }

    /* Labels */
    #modalCliente .form-label {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: var(--bs-secondary-color);
        margin-bottom: 0.4rem;
    }

    /* Inputs redondeados */
    #modalCliente .form-control,
    #modalCliente .form-select {
        border-radius: 12px;
        padding: 0.6rem 0.9rem;
        font-size: 0.9rem;
        border: 1px solid var(--bs-border-color);
        background-color: var(--bs-body-bg);
        color: var(--bs-body-color);
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    #modalCliente .form-control:focus,
    #modalCliente .form-select:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.15);
        background-color: var(--bs-body-bg);
    }

    #modalCliente .form-control::placeholder {
        color: var(--bs-secondary-color);
        opacity: 0.6;
    }

    #modalCliente textarea.form-control {
        min-height: 60px;
        resize: vertical;
    }

    /* Caja de sucursal */
    #modalCliente .sucursal-box {
        background-color: var(--bs-tertiary-bg);
        border: 1px dashed var(--bs-border-color);
        border-radius: 16px;
        padding: 1rem;
    }

    #modalCliente .sucursal-box .form-label {
        color: var(--bs-info-text-emphasis);
    }

    /* Footer */
    #modalCliente .modal-footer {
        border-top: 1px solid var(--bs-border-color);
        padding: 1rem 1.5rem;
        background-color: var(--bs-body-bg);
        gap: 0.5rem;
    }

    #modalCliente .btn {
        border-radius: 12px;
        padding: 0.55rem 1.3rem;
        font-size: 0.9rem;
        font-weight: 600;
    }

    #modalCliente .btn-dental-primary {
        background: linear-gradient(135deg, #376bd8, #2855b8);
        border: 0;
        color: #fff;
    }

    #modalCliente .btn-dental-primary:hover {
        filter: brightness(1.08);
        color: #fff;
    }

    /* Backdrop con blur suave */
    .modal-backdrop.show {
        background: rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        opacity: 1 !important;
    }
</style>

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

                <div class="modal-body p-4">
                    <!-- IDs renombrados con prefijo modal_ -->
                    <input type="hidden" name="cliente_id" id="modal_cliente_id" value="0">

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Nombre completo / comercial *</label>
                            <input type="text" name="nombre_comercial" id="modal_nombre_comercial"
                                class="form-control form-control-dental" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Fecha nacimiento</label>
                            <input type="date" name="fecha_nacimiento" id="modal_fecha_nacimiento"
                                class="form-control form-control-dental" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Responsable</label>
                            <input type="text" name="razon_social" id="modal_razon_social"
                                class="form-control form-control-dental">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Sexo</label>
                            <select name="sexo" id="modal_sexo" class="form-select form-control-dental text-uppercase"
                                required>
                                <option value="" disabled selected>-- Selecciona --</option>
                                <option value="HOMBRE">HOMBRE</option>
                                <option value="MUJER">MUJER</option>
                                <option value="HUMANO">HUMANO</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Contacto / tutor *</label>
                            <input type="text" name="contacto" id="modal_contacto"
                                class="form-control form-control-dental" placeholder="Nombre de contacto">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" id="modal_telefono"
                                class="form-control form-control-dental">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">RFC *</label>
                            <input type="text" name="rfc" id="modal_rfc"
                                class="form-control form-control-dental text-uppercase" maxlength="13">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" name="correo" id="modal_correo"
                                class="form-control form-control-dental">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Calle y número</label>
                            <textarea name="calle" id="modal_calle"
                                class="form-control form-control-dental text-uppercase" rows="2"
                                placeholder="Calle, número exterior/interior..."></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Colonia</label>
                            <input type="text" name="colonia" id="modal_colonia"
                                class="form-control form-control-dental text-uppercase">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pueblo / sector</label>
                            <input type="text" name="pueblo" id="modal_pueblo"
                                class="form-control form-control-dental text-uppercase">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ciudad / municipio</label>
                            <input type="text" name="ciudad" id="modal_ciudad"
                                class="form-control form-control-dental text-uppercase">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Código postal</label>
                            <input type="text" name="codigo_postal" id="modal_codigo_postal"
                                class="form-control form-control-dental text-uppercase" maxlength="5">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Uso CFDI</label>
                            <select name="uso_cfdi" id="modal_uso_cfdi" class="form-select form-select-dental">
                                <?php foreach ($usosCFDI as $key => $val): ?>
                                    <option value="<?= $key ?>"><?= $key ?> - <?= $val ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if ($almacen_usuario == 0): ?>
                            <div class="col-md-12">
                                <div class="sucursal-box mt-2">
                                    <label class="form-label">Asignar a sucursal *</label>
                                    <select name="almacen_id" id="modal_almacen_id" class="form-select form-select-dental"
                                        required>
                                        <option value="">-- Seleccionar almacén --</option>
                                        <?php foreach ($almacenes as $alm): ?>
                                            <option value="<?= $alm['id'] ?>">
                                                <?= htmlspecialchars($alm['nombre']) ?>
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
    /* =========================================================
       MODAL CLIENTE — JS con IDs renombrados (modal_)
       ========================================================= */

    function nuevoCliente() {
        $('#formCliente')[0].reset();
        $('#modal_cliente_id').val('0');
        $('#modalTitulo').text('Nuevo Registro de Paciente');

        const filtro = $('#filtroAlmacenVista').val();
        if (filtro) $('#modal_almacen_id').val(filtro);

        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCliente')).show();
    }

    async function editarCliente(id) {
        try {
            const resp = await fetch(`/myvet/app/controllers/accesoMedicController.php?action=obtenerPorId&id=${id}`);
            const res = await resp.json();
            if (res.success) {
                const c = res.data;
                console.log(c);
                $('#modalTitulo').text('Actualizar Expediente de Paciente');
                $('#modal_cliente_id').val(c.id);
                $('#modal_contacto').val(c.contacto);
                $('#modal_nombre_comercial').val(c.nombre_comercial);
                $('#modal_razon_social').val(c.razon_social);

                if (c.fecha_nacimiento) {
                    const fechaLimpiar = c.fecha_nacimiento.split(' ')[0];
                    $('#modal_fecha_nacimiento').val(fechaLimpiar);
                } else {
                    $('#modal_fecha_nacimiento').val('');
                }

                if (c.sexo) {
                    $('#modal_sexo').val(c.sexo.trim().toUpperCase());
                } else {
                    $('#modal_sexo').val('');
                }
                $('#modal_rfc').val(c.rfc);
                $('#modal_telefono').val(c.telefono);

                const direccion = c.direccion || '';
                const calle = (direccion.match(/calle\s(.*?)(?=,\scol|$)/i) || [, ''])[1];
                const colonia = (direccion.match(/col\s(.*?)(?=,\spueblo|$)/i) || [, ''])[1];
                const pueblo = (direccion.match(/pueblo\s(.*?)(?=,\sciudad|$)/i) || [, ''])[1];
                const ciudad = (direccion.match(/ciudad\s(.*)$/i) || [, ''])[1];

                $('#modal_calle').val(calle.trim());
                $('#modal_colonia').val(colonia.trim());
                $('#modal_pueblo').val(pueblo.trim());
                $('#modal_ciudad').val(ciudad.trim());
                $('#modal_correo').val(c.correo);
                $('#modal_codigo_postal').val(c.codigo_postal);
                $('#modal_almacen_id').val(c.almacen_id);

                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCliente')).show();
            }
        } catch (e) { console.error(e); }
    }

    $('#formCliente').on('submit', async function (e) {
        e.preventDefault();
        try {
            obtenerOGenerarRFC();
            const resp = await fetch('/myvet/app/controllers/clientesPacientesMedicina.php?action=guardar', {
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
        let rfc = $('#modal_rfc').val() ? $('#modal_rfc').val().trim() : '';
        let nombre = $('#modal_nombre_comercial').val() ? $('#modal_nombre_comercial').val().trim() : '';

        if (rfc !== '') {
            return rfc.toUpperCase();
        }

        let limpio = nombre
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .toUpperCase()
            .replace(/[^A-Z\s]/g, "");

        let letras = limpio.replace(/\s+/g, '');

        let rfcGenerado = '';
        if (letras.length >= 4) {
            rfcGenerado = letras.substring(0, 4) + '010101XXX';
        } else if (letras.length > 0) {
            rfcGenerado = letras.padEnd(4, 'X') + '010101XXX';
        } else {
            rfcGenerado = 'XAXX010101000';
        }
        $('#modal_rfc').val(rfcGenerado);
        return rfcGenerado;
    }
</script>
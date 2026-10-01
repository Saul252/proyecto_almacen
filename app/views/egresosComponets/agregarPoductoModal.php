<div class="modal fade" id="modalAgregarProducto" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content  shadow-lg rounded-4 overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle-fill me-2"></i> Nuevo Producto al Catálogo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- FORM -->
            <form id="formAgregarProducto" autocomplete="off">

                <div class="modal-body  p-4">

                    <!-- 🔹 BLOQUE: INFORMACIÓN GENERAL -->
                    <div class="card  shadow-sm mb-4 rounded-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3 text-dark">
                                <i class="bi bi-box-seam me-2"></i>Información del Producto
                            </h6>

                            <div class="row g-3">
                                <select name="select_almacen_id" id="select_almacen_id" class="form-select shadow-sm"
                                    required>

                                </select>

                                <input type="hidden" name="precio_adquisicion" value="0">
                                <div class="col-md-8">
                                    <label class="form-label small text-body-secondary">Nombre del Producto</label>
                                    <input type="text" name="nombre" id="nombreProducto" class="form-control shadow-sm"
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-body-secondary">SKU / Código</label>
                                    <input type="text" name="sku" class="form-control shadow-sm" required>
                                </div>



                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-label small text-body-secondary">Categoría</label>
                                        <button type="button" class="btn btn-sm btn-light border rounded-circle"
                                            onclick="abrirSubModalCategoria()" title="Agregar categoría">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>

                                    <select name="categoria_id" id="select_categoria_id" class="form-select shadow-sm"
                                        required>
                                        <option value="">Seleccionar categoría...</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- 🔹 BLOQUE: UNIDADES -->
                    <div class="card  shadow-sm mb-4 rounded-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3 text-dark">
                                <i class="bi bi-diagram-3 me-2"></i>Unidades y Conversión
                            </h6>

                            <div class="row g-3">
                                <div class="col-md-4">


                                    <label class="form-label small fw-bold text-secondary">UNIDAD BASE (VENTA)</label>
                                    <select required id="u_mayoreo" name="unidad_reporte"
                                        class="form-select  shadow-sm fw-bold">
                                        <option value="">Seleccione...</option>

                                    </select>
                                </div>

                                <div class="col-md-4">

                                    <label class="form-label small fw-bold text-secondary">UNIDAD BASE (VENTA)</label>
                                    <select required name="unidad_medida" id="u_base"
                                        class="form-select  shadow-sm fw-bold">
                                        <option value="">Seleccione...</option>

                                    </select>

                                </div>



                                <div class="col-md-4">
                                    <label class="form-label small text-body-secondary">Factor de conversión</label>
                                    <input type="number" id="f_conversion" name="factor_conversion"
                                        class="form-control shadow-sm" value="1">
                                    <small id="helper-conversion" class="text-primary"></small>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- 🔹 BLOQUE: DATOS FISCALES -->
                    <div class="card  shadow-sm mb-4 rounded-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3 text-dark">
                                <i class="bi bi-receipt me-2"></i>Datos Fiscales
                            </h6>

                            <div class="row g-3">

                                <div class="col-md-4">
                                    <label class="form-label small text-body-secondary">IVA (%)</label>
                                    <input type="number" name="impuesto_iva" class="form-control shadow-sm" value="16">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small text-body-secondary">Clave SAT</label>
                                    <input type="text" name="fiscal_clave_prod" class="form-control shadow-sm">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small text-body-secondary">Clave Unidad</label>
                                    <input type="text" name="fiscal_clave_unidad" class="form-control shadow-sm">
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- 🔹 BLOQUE: PRECIOS -->
                    <div class="card  shadow-sm rounded-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3 text-dark">
                                <i class="bi bi-cash-coin me-2"></i>Precios de Venta
                            </h6>

                            <div class="row g-3">

                                <div class="col-md-4">
                                    <label class="form-label small text-body-secondary">Minorista</label>
                                    <input type="number" step="0.01" name="precio_minorista"
                                        class="form-control shadow-sm" value="0">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small text-body-secondary">Mayorista</label>
                                    <input type="number" step="0.01" name="precio_mayorista"
                                        class="form-control shadow-sm" value="0">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small text-body-secondary">Distribuidor</label>
                                    <input type="number" step="0.01" name="precio_distribuidor"
                                        class="form-control shadow-sm" value="0">
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer   px-4 pb-4">

                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" id="btnGuardarProducto" class="btn btn-dark rounded-pill px-4">
                        <i class="bi bi-save me-2"></i> Guardar producto
                    </button>

                </div>

            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalNuevaCategoria" style="z-index: 10000;" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h6 class="modal-title">Nueva Categoría</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formRapidoCategoria">
                    <div class="mb-3">
                        <select name="almacen_id_compra" id="almacen_id_compra"
                            class="form-select  shadow-sm rounded-3 py-2" required>

                            <?php foreach ($almacenes as $a): ?>
                                <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?><?= $a['id'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label class="form-label small">Nombre de la Categoría</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ej: Herramientas"
                            required>
                    </div>
                    <button type="button" onclick="guardarCategoriaRapida()" class="btn btn-success w-100">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    // 1. Convertir inputs de texto y textareas a mayúsculas en tiempo real
    document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
        elemento.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
        });
    });

    // 2. Abrir submodal de categoría de manera segura
    function abrirSubModalCategoria() {
        const myModal = new bootstrap.Modal(document.getElementById('modalNuevaCategoria'), {
            backdrop: 'static',
            keyboard: false
        });
        myModal.show();
    }

    // 3. Generador de SKU automático
    function generarSKU(nombre) {
        if (!nombre) return '';
        let limpio = nombre
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .toUpperCase();

        const palabras = limpio.split(' ').filter(p => p.length > 0);
        let prefijo = palabras[0].substring(0, 2);

        const matchNumero = limpio.match(/\d+/);
        let numero = matchNumero ? matchNumero[0] : 1;
        let numerorandom = Math.floor(Math.random() * 10000);

        return numero ? `${prefijo}-${numero}-${numerorandom}` : prefijo;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const inputNombre = document.querySelector('input[name="nombre"]');
        const inputSKU = document.querySelector('input[name="sku"]');

        if (inputNombre && inputSKU) {
            inputNombre.addEventListener('input', function () {
                inputSKU.value = generarSKU(this.value);
            });
        }
    });

    // 4. Obtener Almacenes para el select principal


    // 5. Guardar Categoría Rápida desde el submodal
    function guardarCategoriaRapida() {
        const input = document.getElementById('nombre');
        const nombre = input.value.trim();
        const inputAlmacen = document.getElementById('almacen_id_compra');
        const almacen = inputAlmacen.value;

        if (!nombre) {
            return Swal.fire('Error', 'Escribe un nombre', 'error');
        }

        const formData = new FormData();
        formData.append('nombre', nombre);
        formData.append('almacen', almacen);

        fetch('/myvet/app/controllers/egresosController.php?action=guardarCategoria', {
            method: 'POST',
            body: formData
        })
            .then(res => res.text())
            .then(text => {
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error("Error parseando JSON:", text);
                    return Swal.fire('Error', 'Respuesta inválida del servidor', 'error');
                }

                if (data.status === 'success') {
                    const id = data.id;

                    // Actualizar todos los selects de categorías en la vista
                    document.querySelectorAll('select[name="categoria_id"]').forEach(select => {
                        const existe = Array.from(select.options).some(opt => opt.value == id);
                        if (!existe) {
                            const nuevaOpcion = new Option(data.nombre, id);
                            select.add(nuevaOpcion);
                        }
                        select.value = String(id);
                    });

                    // Cerrar modal de categoría rápida
                    const modal = bootstrap.Modal.getOrCreateInstance(
                        document.getElementById('modalNuevaCategoria')
                    );
                    modal.hide();

                    input.value = '';

                    // Restaurar clase modal-open de Bootstrap si queda el modal principal abierto
                    setTimeout(() => {
                        if (document.querySelectorAll('.modal.show').length > 0) {
                            document.body.classList.add('modal-open');
                        }
                    }, 300);

                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Categoría guardada y seleccionada.',
                        icon: 'success',
                        timer: 1200,
                        showConfirmButton: false
                    });

                } else {
                    Swal.fire('Error', data.message || 'Error desconocido', 'error');
                }
            })
            .catch(error => {
                console.error("FETCH ERROR:", error);
                Swal.fire('Error', 'No se pudo procesar la categoría', 'error');
            });
    }

    // 6. Módulo Principal de Productos
    function iniciarModuloProducto() {
        if (typeof $ === 'undefined') {
            setTimeout(iniciarModuloProducto, 100);
            return;
        }

        getAlmacenes();

        const ProdModulo = {
            urlControlador: '/myvet/app/controllers/productosController.php',

            init: function () {
                this.bindEvents();
                this.cargarCategorias();
                this.actualizarTexto();
                this.cargarUnidades();
            },

            bindEvents: function () {
                $('#u_mayoreo, #u_base, #f_conversion')
                    .off('input')
                    .on('input', () => this.actualizarTexto());

                const modalEl = document.getElementById('modalAgregarProducto');
                if (modalEl) {
                    modalEl.addEventListener('show.bs.modal', () => {
                        this.cargarCategorias();
                        this.cargarUnidades();
                    });
                }

                $('#formAgregarProducto')
                    .off('submit')
                    .on('submit', (e) => {
                        e.preventDefault();
                        this.guardar();
                    });
            },

            cargarCategorias: function () {
                const select = $('#select_categoria_id');
                const almacenId = $('#select_almacen_id').val(); // CORREGIDO: Se obtiene el .val()
                select.html('<option value="">Cargando...</option>');

                $.ajax({
                    url: `${this.urlControlador}?action=getCategoriasJSON&idAlmacen=${almacenId}`,
                    type: 'GET',
                    dataType: 'json',
                    success: (data) => {
                        select.empty().append('<option value="">Seleccionar...</option>');
                        if (Array.isArray(data)) {
                            data.forEach(cat => {
                                select.append(`<option value="${cat.id}">${cat.nombre}</option>`);
                            });
                        }
                    },
                    error: () => {
                        select.html('<option value="">Error al cargar</option>');
                    }
                });
            },

            cargarUnidades: function () {
                const select = $('#u_mayoreo');
                const select_unidad = $('#u_base');

                select.html('<option value="">Cargando...</option>');
                select_unidad.html('<option value="">Cargando...</option>');

                $.ajax({
                    url: `${this.urlControlador}?action=getUnidadesMedidaJSON`,
                    type: 'GET',
                    dataType: 'json',
                    success: (data) => {
                        select.empty().append('<option value="">Seleccionar...</option>');
                        select_unidad.empty().append('<option value="">Seleccionar...</option>');

                        if (Array.isArray(data)) {
                            data.forEach(uni => {
                                let opcionHtml = `<option value="${uni.clave}">${uni.nombre} - ${uni.clave}</option>`;
                                select.append(opcionHtml);
                                select_unidad.append(opcionHtml);
                            });
                        }
                    },
                    error: (xhr) => {
                        console.error("Error al cargar unidades:", xhr.responseText);
                        select.html('<option value="">Error al cargar</option>');
                        select_unidad.html('<option value="">Error al cargar</option>');
                    }
                });
            },

            actualizarTexto: function () {
                let m = $('#u_mayoreo').val() || 'Unidad';
                let b = $('#u_base').val() || 'PZA';
                let f = $('#f_conversion').val() || '1';

                $('#helper-conversion').text(`1 ${m} = ${f} ${b}(s)`);
            },

            guardar: function () {
                const btn = $('#btnGuardarProducto');
                btn.prop('disabled', true).html('Guardando...');

                $.ajax({
                    url: this.urlControlador + '?action=guardarProducto',
                    type: 'POST',
                    data: $('#formAgregarProducto').serialize(),
                    dataType: 'json',
                    success: (res) => {
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Producto guardado',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            if (typeof verListaMedidas === 'function') {
                                verListaMedidas(res.id, 1, $('#nombreProducto').val(), $('#u_base').val());
                            }
                            if (typeof recargarProductos === 'function') {
                                recargarProductos();
                            }

                            const modal = bootstrap.Modal.getInstance(
                                document.getElementById('modalAgregarProducto')
                            );
                            if (modal) modal.hide();

                            $('#formAgregarProducto')[0].reset();
                            this.actualizarTexto();

                            if (typeof refrescarListaProductosCompra === "function") {
                                refrescarListaProductosCompra(res.id);
                            }
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    },
                    error: () => {
                        Swal.fire('Error', 'Error de conexión con el servidor', 'error');
                    },
                    complete: () => {
                        btn.prop('disabled', false)
                            .html('<i class="bi bi-save me-2"></i> Guardar producto');
                    }
                });
            }
        };

        ProdModulo.init();

        // Funciones globales auxiliares por si se llaman desde otros scripts
        window.abrirModalProducto = function () {
            new bootstrap.Modal(document.getElementById('modalAgregarProducto')).show();
        };

        window.abrirModalCategoria = function () {
            new bootstrap.Modal(document.getElementById('modalNuevaCategoria')).show();
        };
    }

    // Iniciar todo al cargar el DOM
    document.addEventListener('DOMContentLoaded', iniciarModuloProducto);
</script>
<script>



    function abrirSubModalCategoria() {
        // Simplemente abrimos el modal de categoría sin cerrar el anterior
        const myModal = new bootstrap.Modal(document.getElementById('modalNuevaCategoria'), {
            backdrop: 'static', // Evita que se cierre el de atrás si haces clic fuera
            keyboard: false
        });
        myModal.show();
    }
    function generarSKU(nombre) {
        if (!nombre) return '';

        // limpiar acentos y pasar a mayúsculas
        let limpio = nombre
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .toUpperCase();

        // separar palabras
        const palabras = limpio.split(' ').filter(p => p.length > 0);

        // tomar primeras 2 letras de la primera palabra
        let prefijo = palabras[0].substring(0, 2);

        // buscar número en todo el texto
        const matchNumero = limpio.match(/\d+/);
        let numero = matchNumero ? matchNumero[0] : 1;
        numerorandom = Math.floor(Math.random() * 10000); // 0 - 9999

        return numero ? `${prefijo}-${numero}-${numerorandom}` : prefijo;

    }
    document.addEventListener('DOMContentLoaded', () => {
        const inputNombre = document.querySelector('input[name="nombre"]');
        const inputSKU = document.querySelector('input[name="sku"]');

        inputNombre.addEventListener('input', function () {
            inputSKU.value = generarSKU(this.value);
        });
    });
    function getAlmacenes() {
        const selectPrincipal = document.getElementById('select_almacen_id');

        if (selectPrincipal) {

            fetch('/myvet/app/controllers/accesoController.php?action=getAlmacenesJSON')
                .then(res => res.json())
                .then(categorias => {


                    categorias.forEach(cat => {
                        const option = new Option(cat.nombre, cat.id);
                        selectPrincipal.add(option);
                    });


                });
        }

    }
    function guardarCategoriaRapida() {
        const input = document.getElementById('nombre');
        const nombre = input ? input.value.trim() : '';
        const inputAlmacen = document.getElementById('almacen_id_compra');
        const almacen = inputAlmacen ? inputAlmacen.value : '';

        if (!nombre) {
            return Swal.fire('Error', 'Escribe un nombre', 'error');
        }

        const formData = new FormData();
        formData.append('nombre', nombre);
        formData.append('almacen', almacen);

        fetch('/myvet/app/controllers/egresosController.php?action=guardarCategoria', {
            method: 'POST',
            body: formData
        })
            .then(async res => {
                // Capturamos el texto crudo primero por si hay algún detalle de depuración
                const text = await res.text();
                console.group("📥 DEPURACIÓN DE RESPUESTA");
                console.log("Status HTTP:", res.status);
                console.log("Respuesta Cruda (Texto):", text);
                console.groupEnd();

                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error("Error al convertir la respuesta en JSON:", text);
                    throw new Error("El servidor devolvió un formato no válido.");
                }
            })
            .then(data => {
                console.log("✅ JSON Procesado:", data);

                // Validar si el controlador respondió con un error controlado
                if (data.status !== 'success') {
                    return Swal.fire('Atención', data.message || 'No se pudo procesar la categoría', 'warning');
                }

                // 🔥 SI EL ESTADO ES SUCCESS
                const id = data.id;

                // 1. ACTUALIZAR TODOS LOS SELECTS
                document.querySelectorAll('select[name="categoria_id"]').forEach(select => {
                    const existe = Array.from(select.options)
                        .some(opt => opt.value == id);

                    if (!existe) {
                        const nuevaOpcion = new Option(data.nombre, id);
                        select.add(nuevaOpcion);
                    }

                    select.value = String(id);
                });

                // 2. RECARGAR SELECT ESPECÍFICO (si la función existe)
                if (typeof cargarCategoriasFuncion === 'function') {
                    cargarCategoriasFuncion();
                }

                // 3. CERRAR MODAL
                const modalEl = document.getElementById('modalNuevaCategoria');
                if (modalEl) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.hide();
                }

                // 4. LIMPIAR INPUT
                if (input) {
                    input.value = '';
                }

                // 5. FIX SCROLL DE BOOTSTRAP
                setTimeout(() => {
                    if (document.querySelectorAll('.modal.show').length > 0) {
                        document.body.classList.add('modal-open');
                    }
                }, 300);

                // 6. MENSAJE DE ÉXITO
                Swal.fire({
                    title: '¡Éxito!',
                    text: 'Categoría guardada y seleccionada.',
                    icon: 'success',
                    timer: 1200,
                    showConfirmButton: false
                });

            })
            .catch(error => {
                console.error("❌ FETCH ERROR:", error);
                Swal.fire('Error', error.message || 'No se pudo conectar con el servidor', 'error');
            });
    }
</script>
<script>
    async function cargarCategoriasFuncion() {

        const select = $('#select_categoria_id');
        const almacenId = $('#select_almacen_id');
        select.html('<option value="">Cargando...</option>');

        $.ajax({
            url: `/myvet/app/controllers/productosController.php?action=getCategoriasJSON&idAlmacen=${almacenId}`,
            type: 'GET',
            dataType: 'json',

            success: (data) => {

                select.empty().append('<option value="">Seleccionar...</option>');

                if (Array.isArray(data)) {
                    data.forEach(cat => {
                        select.append(
                            `<option value="${cat.id}">${cat.nombre}</option>`);
                    });
                }
            },

            error: () => {
                select.html('<option value="">Error al cargar</option>');
            }
        });
    }
    function iniciarModuloProducto() {

        // Esperar a que jQuery esté listo (si lo usas)
        if (typeof $ === 'undefined') {
            setTimeout(iniciarModuloProducto, 100);
            return;
        }

        const ProdModulo = {

            urlControlador: '/myvet/app/controllers/productosController.php',

            init: function () {
                this.bindEvents();
                this.cargarCategorias();
                this.actualizarTexto();
                this.cargarUnidades();
            },

            bindEvents: function () {

                // 🔥 Inputs dinámicos
                $('#u_mayoreo, #u_base, #f_conversion')
                    .off('input')
                    .on('input', () => this.actualizarTexto());

                // 🔥 Cuando se abre el modal
                const modalEl = document.getElementById('modalAgregarProducto');
                modalEl.addEventListener('show.bs.modal', () => {
                    this.cargarCategorias();
                    this.cargarUnidades();
                });

                // 🔥 Submit
                $('#formAgregarProducto')
                    .off('submit')
                    .on('submit', (e) => {
                        e.preventDefault();
                        this.guardar();
                    });
            },


            // 🔥 Cargar categorías
            cargarCategorias: function () {

                const select = $('#select_categoria_id');
                const almacenId = $('#select_almacen_id');
                select.html('<option value="">Cargando...</option>');

                $.ajax({
                    url: `/myvet/app/controllers/productosController.php?action=getCategoriasJSON&idAlmacen=${almacenId}`,
                    type: 'GET',
                    dataType: 'json',

                    success: (data) => {

                        select.empty().append('<option value="">Seleccionar...</option>');

                        if (Array.isArray(data)) {
                            data.forEach(cat => {
                                select.append(
                                    `<option value="${cat.id}">${cat.nombre}</option>`);
                            });
                        }
                    },

                    error: () => {
                        select.html('<option value="">Error al cargar</option>');
                    }
                });
            },
            cargarUnidades: function () {
                const select = $('#u_mayoreo');
                const select_unidad = $('#u_base');

                // 1. Colocar ambos en estado de carga
                select.html('<option value="">Cargando...</option>');
                select_unidad.html('<option value="">Cargando...</option>');

                $.ajax({
                    url: '/myvet/app/controllers/productosController.php?action=getUnidadesMedidaJSON',
                    type: 'GET',
                    dataType: 'json',
                    success: (data) => {
                        // Imprime en consola para verificar qué estructura llega de PHP
                        console.log("Datos recibidos:", data);

                        // 2. Limpiar y colocar la opción por defecto
                        select.empty().append('<option value="">Seleccionar...</option>');
                        select_unidad.empty().append('<option value="">Seleccionar...</option>');

                        if (Array.isArray(data)) {
                            data.forEach(uni => {
                                // NOTA: Asegúrate de que 'clave' y 'nombre' existan tal cual en tu JSON
                                let opcionHtml = `<option value="${uni.clave}">${uni.nombre} - ${uni.clave}</option>`;

                                select.append(opcionHtml);
                                select_unidad.append(opcionHtml);
                            });
                        } else {
                            console.warn("Los datos recibidos no son un Array válido.");
                        }
                    },
                    error: (xhr, status, error) => {
                        // Imprime el error exacto en la consola para saber qué falló
                        console.error("Error en la petición AJAX:", error);
                        console.log("Respuesta del servidor:", xhr.responseText);

                        // 3. Actualizar ambos selects en caso de error
                        select.html('<option value="">Error al cargar</option>');
                        select_unidad.html('<option value="">Error al cargar</option>');
                    }
                }); // Quitamos la llave y coma sobrantes si vas a usarlo de forma independiente
            },
            // 🔥 Texto conversión
            actualizarTexto: function () {

                let m = $('#u_mayoreo').val() || 'Unidad';
                let b = $('#u_base').val() || 'PZA';
                let f = $('#f_conversion').val() || '1';

                $('#helper-conversion').text(`1 ${m} = ${f} ${b}(s)`);
            },

            // 🔥 Guardar producto
            guardar: function () {

                const btn = $('#btnGuardarProducto');

                btn.prop('disabled', true).html('Guardando...');

                $.ajax({
                    url: this.urlControlador + '?action=guardarProducto',
                    type: 'POST',
                    data: $('#formAgregarProducto').serialize(),
                    dataType: 'json',

                    success: (res) => {

                        if (res.status === 'success') {

                            Swal.fire({
                                icon: 'success',
                                title: 'Producto guardado',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            verListaMedidas(res.id, 1, $('#nombreProducto').val(), $('#u_base').val());
                            if (typeof recargarProductos === 'function') {

                                recargarProductos();

                                // 🔥 si es async espera


                            }

                            // 🔥 cerrar modal (Bootstrap 5)
                            const modal = bootstrap.Modal.getInstance(
                                document.getElementById('modalAgregarProducto')
                            );
                            modal.hide();

                            // limpiar form
                            $('#formAgregarProducto')[0].reset();
                            this.actualizarTexto();

                            // refrescar si existe función externa
                            if (typeof refrescarListaProductosCompra === "function") {
                                refrescarListaProductosCompra(res.id);
                            }

                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    },

                    error: () => {
                        Swal.fire('Error', 'Error de conexión con el servidor', 'error');
                    },

                    complete: () => {
                        btn.prop('disabled', false)
                            .html('<i class="bi bi-save me-2"></i> Guardar');
                    }
                });
            }
        };

        // 🔥 iniciar módulo
        ProdModulo.init();


        // ===============================
        // 🔥 FUNCIONES GLOBALES
        // ===============================

        // abrir modal producto
        window.abrirModalProducto = function () {
            new bootstrap.Modal(
                document.getElementById('modalAgregarProducto')
            ).show();
        };

        // abrir modal categoría
        window.abrirModalCategoria = function () {
            new bootstrap.Modal(
                document.getElementById('modalAgregarCategoria')
            ).show();
        };

        // guardar categoría
        window.ejecutarGuardarCategoria = function () {

            const nombre = $('#inputNombreCategoria').val().trim();

            if (!nombre) return;

            $.post('/myvet/app/controllers/productosController.php?action=guardarCategoria', {
                nombre
            }, function (res) {

                if (res.status === "success") {

                    ProdModulo.cargarCategorias();

                    setTimeout(() => {
                        $('#select_categoria_id').val(res.id);
                    }, 300);

                    const modalCat = bootstrap.Modal.getInstance(
                        document.getElementById('modalAgregarCategoria')
                    );
                    modalCat.hide();

                    $('#inputNombreCategoria').val('');
                }

            }, 'json');
        };
    }

    // 🔥 iniciar todo
    document.addEventListener('DOMContentLoaded', iniciarModuloProducto);
</script>
<!-- =========================================
MODAL LISTA DE MEDIDAS
========================================= -->
<div class="modal fade" id="modalListaMedidas" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content border-0 shadow-lg overflow-hidden">

            <!-- HEADER: Gradiente adaptativo -->
            <div class="modal-header bg-primary bg-gradient text-white border-0 p-4 position-relative">

                <div class="pe-4">
                    <h5 class="modal-title fw-bold mb-1 d-flex align-items-center gap-2">
                        <i class="bi bi-rulers fs-4"></i>
                        Medidas Disponibles
                    </h5>

                    <small id="subtituloListaMedidas" class="text-white-50 fw-medium">
                        Cargando detalles...
                    </small>
                </div>

                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-4"
                    data-bs-dismiss="modal" aria-label="Close">
                </button>

            </div>

            <!-- BODY -->
            <div class="modal-body p-0 bg-body">

                <!-- Barra superior de acciones -->
                <div class="p-3 border-bottom bg-body-tertiary d-flex justify-content-between align-items-center">
                    <span class="text-body-secondary small fw-semibold text-uppercase tracking-wide">
                        Lista de Equivalencias
                    </span>
                    <button type="button" id="agregarMedida"
                        class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                        <i class="bi bi-plus-circle-fill"></i>
                        <span>Agregar Medida</span>
                    </button>
                </div>

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="bg-body-tertiary border-bottom">

                            <tr>
                                <th class="ps-4 text-body-secondary small text-uppercase">Nombre Medida</th>
                                <th class="text-body-secondary small text-uppercase">Equivalencia</th>
                                <th class="text-end pe-4 text-body-secondary small text-uppercase">Acciones</th>
                            </tr>

                        </thead>

                        <tbody id="tablaCuerpoMedidas">

                            <!-- JS Populate -->

                        </tbody>

                    </table>

                </div>

                <!-- EMPTY STATE -->
                <div id="listaVacia" class="text-center py-5 d-none">

                    <i class="bi bi-inbox fs-1 text-body-tertiary d-block mb-2"></i>

                    <p class="text-body-secondary fw-medium mb-0">
                        No hay medidas adicionales configuradas para este producto.
                    </p>

                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer border-top bg-body-tertiary px-4 py-3">

                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold"
                    data-bs-dismiss="modal">
                    Cerrar
                </button>

            </div>

        </div>

    </div>

</div>


<!-- =========================================
MODAL EDITAR MEDIDA
========================================= -->
<div class="modal fade" id="modalEditarMedida" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header bg-dark text-white px-4 py-3 border-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-25 text-primary p-2 rounded-3 d-flex align-items-center justify-content-center"
                        style="width: 42px; height: 42px;">
                        <i class="bi bi-pencil-square fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white">Editar Medida</h5>
                        <p class="text-white-50 small mb-0">Actualiza los parámetros de conversión</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <!-- FORM -->
            <form id="formEditarMedida">
                <input type="hidden" id="edit_medida_id" name="id">
                <input type="hidden" id="edit_producto_id" name="producto_id">

                <div class="modal-body p-4 bg-light">

                    <!-- NOMBRE -->
                    <div class="mb-3">
                        <label for="edit_nombre_medida"
                            class="form-label fw-bold small text-muted text-uppercase tracking-wider">
                            Nombre de la medida
                        </label>
                        <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-white border-end-0 text-muted ps-3">
                                <i class="bi bi-tag"></i>
                            </span>
                            <input type="text" id="edit_nombre_medida"
                                class="form-control border-start-0 ps-2 fs-6 bg-white" name="nombre_edit"
                                placeholder="Ej. Caja, Gramo, Pieza" required>
                        </div>
                    </div>

                    <!-- EQUIVALENCIA (DISEÑO TARJETA MEJORADO) -->


                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-uppercase text-secondary mb-2">
                            Factor de equivalencia
                        </label>

                        <div class="border rounded-3 p-3 bg-white shadow-sm">
                            <div class="row g-2 align-items-center">

                                <!-- Valor / Unidad Adicional -->
                                <div class="col-5">
                                    <div class="input-group input-group-sm">
                                        <input type="number"
                                            class="form-control fw-bold text-primary text-center bg-light border-end-0"
                                            id="edit_equivalencia" name="equivalencia" step="0.000000001" min="0.00001"
                                            required>
                                        <span
                                            class="input-group-text bg-light text-muted fw-normal px-2 border-start-0 text-truncate"
                                            id="edit_unidad_text" style="max-width: 80px; font-size: 0.75rem;">
                                            Unidades
                                        </span>
                                    </div>
                                </div>

                                <!-- Conector Central -->
                                <div class="col-2 text-center text-muted fw-semibold" style="font-size: 0.8rem;">
                                    <span class="text-uppercase tracking-wider text-muted opacity-75"
                                        style="font-size: 0.65rem;">EQUIV. A</span>
                                </div>

                                <!-- Valor / Unidad Base -->
                                <div class="col-5">
                                    <div class="input-group input-group-sm">
                                        <input type="number"
                                            class="form-control fw-bold text-success text-center bg-light border-end-0"
                                            id="edit_base" name="base" step="0.000000001" min="0.00001" required>
                                        <span
                                            class="input-group-text bg-light text-muted fw-normal px-2 border-start-0 text-truncate"
                                            id="unidadMedidaMostrar" style="max-width: 80px; font-size: 0.75rem;">
                                            Base
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>


                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-white border-0 px-4 py-3 gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-secondary"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-semibold shadow-sm">
                        <i class="bi bi-check2 me-1"></i> Guardar Cambios
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
<!-- =========================================
ESTILOS ADICIONALES (CSS)
========================================= -->
<style>
    /* Z-Index para modales anidados */
    #modalEditarMedida {
        z-index: 1065 !important;
    }

    #modalEditarMedida+.modal-backdrop {
        z-index: 1060 !important;
    }

    .miSwalZ {
        z-index: 10000 !important;
    }

    .tracking-wide {
        letter-spacing: 0.05em;
    }
</style>


<!-- =========================================
JAVASCRIPT
========================================= -->
<script>
    const URL_MEDIDAS = '/myvet/app/controllers/productosController.php';

    let ultimaMedidaProductoId = 0;
    let ultimaMedidaAlmacenId = 0;
    let ultimaMedidaNombreProducto = '';
    let ultimaUnidadMedida = '';

    // =========================================
    // VER LISTA MEDIDAS
    // =========================================
    async function verListaMedidas(idProducto, idAlmacen, nombreProducto, unidad_medida) {
        ultimaMedidaProductoId = idProducto;
        ultimaMedidaAlmacenId = idAlmacen;
        ultimaMedidaNombreProducto = nombreProducto;
        ultimaUnidadMedida = unidad_medida;

        const tbody = document.getElementById('tablaCuerpoMedidas');
        const subtitulo = document.getElementById('subtituloListaMedidas');
        const emptyState = document.getElementById('listaVacia');

        subtitulo.innerText = `Producto: ${nombreProducto}`;
        emptyState.classList.add('d-none');

        tbody.innerHTML = `
        <tr>
            <td colspan="3" class="text-center py-4">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                <span class="ms-2 text-body-secondary fw-medium">Cargando medidas...</span>
            </td>
        </tr>
    `;

        const modalEl = document.getElementById('modalListaMedidas');
        let myModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        myModal.show();

        try {
            const resp = await fetch(`${URL_MEDIDAS}?action=obtnerMedidas&id=${idProducto}`);

            if (!resp.ok) throw new Error('Error en la red');

            const data = await resp.json();
            tbody.innerHTML = '';

            if (data.status && data.producto.medidas && data.producto.medidas.length > 0) {

                $('#agregarMedida').attr(
                    'onclick',
                    `prepararNuevaMedida(${idProducto}, ${idAlmacen}, '${nombreProducto}', '${unidad_medida}')`
                );

                // =========================================================
                // 1. BUSCAR LA MEDIDA BASE (EQUIVALENCIA = 1)
                // =========================================================
                const medidaBase = data.producto.medidas.find(m => (parseFloat(m.equivalencia) || 0) === 1);
                const idBase = medidaBase ? Number(medidaBase.id) : null;

                data.producto.medidas.forEach(m => {
                    const medidaData = encodeURIComponent(JSON.stringify(m));

                    // Convertir a número por seguridad
                    const equiv = parseFloat(m.equivalencia) || 0;
                    const idActual = Number(m.id);

                    // Cálculo de la relación inversa (1 / equivalencia)
                    const inversa = equiv > 0 ? (1 / equiv) : 0;

                    // Formateo inteligente para limpiar ceros innecesarios a la derecha
                    const equivFormateada = Number(equiv.toFixed(6));
                    const inversaFormateada = Number(inversa.toFixed(2));

                    // =========================================================
                    // 2. REGLA DE PROTECCIÓN DE ELIMINACIÓN
                    // =========================================================
                    // Se protege si:
                    // - Su equivalencia es 1
                    // - Su nombre coincide con la unidad de medida principal
                    // - Su ID es menor o igual al ID de la medida base (idActual <= idBase)
                    const esEquivBase = (equivFormateada === 1);
                    const esUnidadBase = (m.nombre === unidad_medida);
                    const esMenorOIgualQueBase = (idBase !== null && idActual <= idBase);

                    const esProtegido = esEquivBase || esUnidadBase || esMenorOIgualQueBase;

                    let boton = esProtegido
                        ? ''
                        : `<button class="btn btn-sm btn-light-hover text-danger rounded-circle border-0 p-2 d-inline-flex align-items-center justify-content-center"
                                title="Eliminar medida"
                                style="width: 34px; height: 34px;"
                                onclick="eliminarMedida(${m.id})">
                            <i class="bi bi-trash-fill fs-6"></i>
                        </button>`;

                    // Determinar la frase según el tipo de equivalencia
                    let textoRelacion = '';

                    if (equiv >= 1) {
                        textoRelacion = `
                        <span class="badge bg-body-tertiary text-body border border-translucent rounded-pill px-3 py-2 fw-normal d-inline-flex align-items-center gap-2 shadow-sm">
                            <span><strong class="text-primary">${equivFormateada}</strong> ${m.nombre}(s)= <strong>1</strong> ${unidad_medida} </span>
                        </span>
                    `;
                    } else {
                        textoRelacion = `
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-normal d-inline-flex align-items-center gap-2 shadow-sm">
                            <i class="bi bi-arrow-left-right opacity-75"></i>
                            <span> <strong>1</strong> ${m.nombre} = <strong>${inversaFormateada}</strong> ${unidad_medida}(s) </span>
                        </span>
                    `;
                    }

                    const fila = `
                    <tr class="align-middle">
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="p-2 bg-body-tertiary rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-rulers text-primary fs-6"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-body mb-0">${m.nombre}</div>
                                    <small class="text-body-secondary fs-7">
                                        Valor base: ${equivFormateada}
                                    </small>
                                </div>
                            </div>
                        </td>

                        <td>
                            ${textoRelacion}
                        </td>

                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1">
                                <button class="btn btn-sm btn-light-hover text-primary rounded-circle border-0 p-2 d-inline-flex align-items-center justify-content-center"
                                        title="Editar medida"
                                        style="width: 34px; height: 34px;"
                                        onclick="abrirEditarMedida('${medidaData}','${unidad_medida}')">
                                    <i class="bi bi-pencil-fill fs-6"></i>
                                </button>
                                ${boton}
                            </div>
                        </td>
                    </tr>
                `;

                    tbody.insertAdjacentHTML('beforeend', fila);
                });
            } else {
                emptyState.classList.remove('d-none');
                $('#agregarMedida').attr(
                    'onclick',
                    `prepararNuevaMedida(${idProducto}, ${idAlmacen}, '${nombreProducto}', '${unidad_medida}')`
                );
            }

        } catch (error) {
            console.error("Error:", error);
            tbody.innerHTML = `
            <tr>
                <td colspan="3" class="text-center py-4 text-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    No se pudo cargar la información
                </td>
            </tr>
        `;
        }
    }
    function recargarModalMedidas() {
        verListaMedidas(
            ultimaMedidaProductoId,
            ultimaMedidaAlmacenId,
            ultimaMedidaNombreProducto,
            ultimaUnidadMedida
        );
    }

    // =========================================
    // ABRIR EDITAR
    // =========================================
    function abrirEditarMedida(data, unidadM) {
        // 1. Primero parseamos los datos
        const medida = JSON.parse(decodeURIComponent(data));
        console.log(medida);

        // 2. Calculamos la equivalencia de forma segura
        let equi = 0;
        let mayor = 1;
        let menor = 1;
        if (medida.equivalencia && Number(medida.equivalencia) !== 0) {
            let calculo = 1 / parseFloat(medida.equivalencia);

            // 1. Limpiamos el ruido de coma flotante si está muy cerca de un entero (ej: 5.9999 -> 6, 21.999912 -> 22)
            const tolerancia = 0.001;
            if (Math.abs(calculo - Math.round(calculo)) < tolerancia) {
                calculo = Math.round(calculo);
            }

            if (calculo < 1) {
                equi = Math.round(1 / calculo);
                mayor = 1;
            } else {
                equi = 1;
                mayor = calculo;
            }
        }

        // 3. Obtenemos las referencias del DOM
        const inputId = document.getElementById('edit_medida_id');
        const inputProdId = document.getElementById('edit_producto_id');
        const inputNombre = document.getElementById('edit_nombre_medida');
        const inputBase = document.getElementById('edit_base');
        const inputEquiv = document.getElementById('edit_equivalencia');
        const textMOstrarMedida = document.getElementById('unidadMedidaMostrar');
        const textUnidad = document.getElementById('edit_unidad_text');

        // 4. Asignamos valores
        if (inputId) inputId.value = medida.id;
        if (inputProdId) inputProdId.value = medida.producto_id;
        if (inputEquiv) inputEquiv.value = equi;
        if (inputBase) inputBase.value = mayor;
        if (textUnidad) textUnidad.innerText = medida.nombre;
        if (textMOstrarMedida) textMOstrarMedida.innerText = unidadM;
        if (inputNombre) inputNombre.value = medida.nombre;

        // 5. Mostramos el modal de Bootstrap
        const modalEl = document.getElementById('modalEditarMedida');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    }

    // =========================================
    // GUARDAR CAMBIOS
    // =========================================
    document.getElementById('formEditarMedida').addEventListener('submit', async function (e) {
        e.preventDefault();

        try {
            Swal.fire({
                title: 'Actualizando...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
                customClass: { popup: 'miSwalZ' }
            });

            const formData = new FormData(this);

            const resp = await fetch(`${URL_MEDIDAS}?action=actualizarMedidaAdicional`, {
                method: 'POST',
                body: formData
            });

            const data = await resp.json();
            Swal.close();

            if (data.status || data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Actualizado',
                    text: 'La medida fue actualizada correctamente',
                    timer: 1500,
                    showConfirmButton: false,
                    customClass: { popup: 'miSwalZ' }
                });

                const modalEditar = bootstrap.Modal.getInstance(document.getElementById('modalEditarMedida'));
                if (modalEditar) modalEditar.hide();

                recargarModalMedidas();

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'No se pudo actualizar',
                    customClass: { popup: 'miSwalZ' }
                });
            }

        } catch (error) {
            console.error(error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Falló la comunicación con el servidor',
                customClass: { popup: 'miSwalZ' }
            });
        }
    });

    // =========================================
    // ELIMINAR MEDIDA
    // =========================================
    async function eliminarMedida(id) {
        const swalConfig = {
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: { container: 'miSwalZ' }
        };

        const confirmacion = await Swal.fire(swalConfig);

        if (confirmacion.isConfirmed) {
            try {
                Swal.fire({
                    title: 'Eliminando...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading(),
                    customClass: { container: 'miSwalZ' }
                });

                const formData = new FormData();
                formData.append('id', id);

                const resp = await fetch(`${URL_MEDIDAS}?action=eliminarMedidaAdicional`, {
                    method: 'POST',
                    body: formData
                });

                const data = await resp.json();

                if (data.status || data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: '¡Eliminado!',
                        text: 'La medida ha sido removida.',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { container: 'miSwalZ' }
                    });
                    recargarModalMedidas();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo eliminar',
                        customClass: { container: 'miSwalZ' }
                    });
                }
            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Fallo de comunicación con el servidor',
                    customClass: { container: 'miSwalZ' }
                });
            }
        }
    }
</script>
<!-- =========================================================
MODAL CREAR MEDIDA ADICIONAL
========================================================= -->

<style>
    /* =========================================
Z-INDEX & OVERLAYS
========================================= */
    #modalMedidaAdicional {
        z-index: 1065 !important;
    }

    #modalMedidaAdicional+.modal-backdrop {
        z-index: 1060 !important;
    }

    .miSwalZ,
    .swal2-container {
        z-index: 10000 !important;
    }

    /* =========================================
MODAL CONTENT & STRUCTURE
========================================= */
    #modalMedidaAdicional .modal-content {

        border-radius: 1.25rem;
        overflow: hidden;
        background-color: var(--bs-modal-bg);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
    }

    #modalMedidaAdicional .modal-header {
        background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-primary-text-emphasis, #0a58ca) 100%);

    }

    /* =========================================
INPUTS & CONTROLS
========================================= */
    #modalMedidaAdicional .form-control {
        height: 48px;
        border-radius: 0.75rem;
        background-color: var(--bs-body-bg);
        border: 1px solid var(--bs-border-color);
        color: var(--bs-body-color);
        transition: all 0.2s ease;
    }

    #modalMedidaAdicional .form-control:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.15);
    }

    /* =========================================
FORMULA BOX & EQUIVALENCIA (MODO OSCURO COMPATIBLE)
========================================= */
    #modalMedidaAdicional .formula-box {
        background-color: var(--bs-tertiary-bg);
        border: 1px dashed var(--bs-border-color-translucent);
        border-radius: 1rem;
        padding: 1.25rem;
    }

    /* Resultado calculado resaltado */
    #equivalencia {
        background-color: var(--bs-warning-bg-subtle) !important;
        color: var(--bs-warning-text-emphasis) !important;
        border: 1px solid var(--bs-warning-border-subtle) !important;
        font-size: 1.15rem;
        font-weight: 700;
        text-align: center;
    }

    /* =========================================
TIPO CARD (TARJETAS RADIO BUTTON)
========================================= */
    .tipo-card {
        border: 1px solid var(--bs-border-color);
        border-radius: 0.875rem;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        background-color: var(--bs-body-bg);
        display: block;
    }

    .tipo-card:hover {
        border-color: var(--bs-primary);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .tipo-card:has(input:checked) {
        border-color: var(--bs-primary) !important;
        background-color: var(--bs-primary-bg-subtle) !important;
    }

    .tipo-card input[type="radio"] {
        width: 1.15em;
        height: 1.15em;
        cursor: pointer;
    }

    .tracking-wide {
        letter-spacing: 0.04em;
    }
</style>

<div class="modal fade" id="modalMedidaAdicional" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header text-white p-4 position-relative">

                <div class="pe-4">
                    <h5 class="modal-title fw-bold mb-1 d-flex align-items-center gap-2">
                        <i class="bi bi-rulers fs-4"></i>
                        Nueva Medida
                    </h5>

                    <small id="infoProductoModal" class="text-white-50 fw-medium">
                        Configura equivalencia de unidades
                    </small>
                </div>

                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-4"
                    data-bs-dismiss="modal" aria-label="Close">
                </button>

            </div>

            <!-- FORM -->
            <form id="formMedidaAdicional">

                <input type="hidden" name="producto_id" id="id_producto_crear">
                <input type="hidden" name="almacen_id" id="id_almacen_crear">

                <!-- BODY -->
                <div class="modal-body p-4 bg-body">

                    <!-- NOMBRE -->
                    <div class="mb-4">
                        <label for="nombreNuevaUnidad"
                            class="form-label fw-bold small text-uppercase text-body-secondary tracking-wide">
                            Nombre de la nueva unidad
                        </label>

                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary text-body-secondary border-end-0">
                                <i class="bi bi-tag-fill"></i>
                            </span>
                            <input type="text" name="nombre" id="nombreNuevaUnidad"
                                class="form-control border-start-0 ps-0" placeholder="Ej: Caja, Gramo, Tonelada"
                                required>
                        </div>
                    </div>

                    <!-- TIPO CONVERSIÓN -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-body-secondary mb-2 tracking-wide">
                            Tipo de conversión
                        </label>

                        <div class="row g-3">

                            <!-- MÁS GRANDE -->
                            <div class="row g-3">

                                <!-- MÁS GRANDE (MAYOR) -->
                                <div class="col-md-6">
                                    <label class="tipo-card h-100 p-3 rounded-4 d-flex align-items-center">
                                        <div class="d-flex align-items-center gap-3 w-100">
                                            <input type="radio" name="tipoConversion" value="grande"
                                                class="form-check-input flex-shrink-0 mt-0" checked>

                                            <div class="lh-sm">

                                                <small class="text-body-secondary d-block fs-7">
                                                    MAS GRANDE QUE <span id="masg"
                                                        class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 ms-1">Unidad</span>
                                                </small>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <!-- MÁS PEQUEÑA (MENOR) -->
                                <div class="col-md-6">
                                    <label class="tipo-card h-100 p-3 rounded-4 d-flex align-items-center">
                                        <div class="d-flex align-items-center gap-3 w-100">
                                            <input type="radio" name="tipoConversion" value="pequena"
                                                class="form-check-input flex-shrink-0 mt-0">

                                            <div class="lh-sm">

                                                <small class="text-body-secondary d-block fs-7">
                                                    MAS PEQUEÑA QUE <span id="masp"
                                                        class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-1 ms-1">Unidad</span>
                                                </small>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- FORMULA BOX -->
                    <div class="formula-box mb-4">

                        <div class="mb-3">
                            <label for="cantidadConversion"
                                class="form-label fw-bold small text-uppercase text-body-secondary tracking-wide">
                                Conversión
                            </label>

                            <input type="number" id="cantidadConversion" class="form-control text-center fw-bold fs-4"
                                step="0.00000001" min="0" placeholder="0.00">
                        </div>

                        <!-- TEXTO FÓRMULA -->
                        <div class="alert bg-body border text-body text-center py-2 px-3 mb-3 shadow-sm rounded-3">
                            <span id="textoFormula" class="fw-medium small">
                                <i class="bi bi-calculator me-1 text-primary"></i> Fórmula de conversión
                            </span>
                        </div>

                        <!-- RESULTADO CALCULADO -->
                        <div>
                            <label for="equivalencia"
                                class="form-label fw-bold small text-uppercase text-body-secondary tracking-wide">
                                Equivalencia calculada
                            </label>

                            <input type="number" id="equivalencia" name="equivalencia" class="form-control"
                                step="0.000000001" readonly>
                        </div>

                    </div>

                    <!-- EJEMPLO EN ALERTA ADAPTATIVA -->
                    <div
                        class="alert alert-info  bg-info-subtle text-info-emphasis d-flex align-items-start gap-2 m-0 p-3 rounded-3">
                        <i class="bi bi-info-circle-fill fs-5 flex-shrink-0 mt-n1"></i>
                        <small id="ejemploConversion" class="fw-medium">
                            Esperando datos...
                        </small>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer  bg-body px-4 pb-4 pt-0 gap-2">

                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-semibold shadow-sm">
                        <i class="bi bi-check-lg me-1"></i>
                        Guardar
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<script>
    // =====================================================
    // 🔥 VARIABLES
    // =====================================================

    let unidadBaseActual = 'Unidad';

    // =====================================================
    // 🔥 ABRIR MODAL
    // =====================================================

    window.prepararNuevaMedida = function (
        idProducto,
        idAlmacen,
        nombreProducto,
        unidadBase
    ) {

        unidadBaseActual = unidadBase || 'Unidad';

        document.getElementById('id_producto_crear').value = idProducto;
        document.getElementById('id_almacen_crear').value = idAlmacen;

        // Corrección crítica: Usar innerText en lugar de .text
        document.getElementById('masg').innerText = unidadBaseActual;
        document.getElementById('masp').innerText = unidadBaseActual;

        document.getElementById('infoProductoModal').innerText = `Producto: ${nombreProducto}`;

        document.getElementById('cantidadConversion').value = '';
        document.getElementById('equivalencia').value = '';
        document.getElementById('nombreNuevaUnidad').value = '';

        actualizarFormula();

        const modal = bootstrap.Modal.getOrCreateInstance(
            document.getElementById('modalMedidaAdicional')
        );

        modal.show();
    };

    // =====================================================
    // 🔥 ACTUALIZAR FORMULA
    // =====================================================

    function actualizarFormula() {

        const tipoRadio = document.querySelector('input[name="tipoConversion"]:checked');
        const tipo = tipoRadio ? tipoRadio.value : 'grande';

        const cantidad = parseFloat(document.getElementById('cantidadConversion').value) || 0;
        const nuevaUnidad = document.getElementById('nombreNuevaUnidad').value.trim() || 'Nueva Unidad';

        const texto = document.getElementById('textoFormula');
        const equivalencia = document.getElementById('equivalencia');
        const ejemplo = document.getElementById('ejemploConversion');

        // =================================================
        // 🔥 MÁS GRANDE
        // Ej: 1000 KG caben en 1 TONELADA
        // equivalencia = 0.001
        // =================================================
        if (tipo === 'grande') {

            texto.innerHTML = `<i class="bi bi-calculator me-1 text-primary"></i> <strong>${cantidad || '?'}</strong> ${unidadBaseActual} caben en <strong>1 ${nuevaUnidad}</strong>`;

            if (cantidad > 0) {

                const equivVal = (1 / cantidad).toFixed(8);
                equivalencia.value = equivVal;

                ejemplo.innerHTML = `
                <strong>${cantidad} ${unidadBaseActual}</strong> = <strong>1 ${nuevaUnidad}</strong>
                <br>
                <span class="text-body-secondary">Entonces: 1 ${unidadBaseActual} = ${equivVal} ${nuevaUnidad}</span>
            `;
            } else {
                ejemplo.innerText = 'Ingresa una cantidad válida para calcular la equivalencia.';
            }
        }

        // =================================================
        // 🔥 MÁS PEQUEÑA
        // Ej: 1 KG contiene 1000 GRAMOS
        // equivalencia = 1000
        // =================================================
        else {

            texto.innerHTML = `<i class="bi bi-calculator me-1 text-primary"></i> <strong>1 ${unidadBaseActual}</strong> contiene <strong>${cantidad || '?'} ${nuevaUnidad}</strong>`;

            if (cantidad > 0) {

                const equivVal = cantidad.toFixed(8);
                equivalencia.value = equivVal;

                ejemplo.innerHTML = `
                <strong>1 ${unidadBaseActual}</strong> = <strong>${cantidad} ${nuevaUnidad}</strong>
            `;
            } else {
                ejemplo.innerText = 'Ingresa una cantidad válida para calcular la equivalencia.';
            }
        }
    }

    // =====================================================
    // 🔥 EVENTOS
    // =====================================================

    document.getElementById('cantidadConversion').addEventListener('input', actualizarFormula);
    document.getElementById('nombreNuevaUnidad').addEventListener('input', actualizarFormula);

    document.querySelectorAll('input[name="tipoConversion"]').forEach(radio => {
        radio.addEventListener('change', actualizarFormula);
    });

    // =====================================================
    // 🔥 GUARDAR FORMULARIO
    // =====================================================

    document.getElementById('formMedidaAdicional').addEventListener('submit', async function (e) {

        e.preventDefault();

        try {

            Swal.fire({
                title: 'Guardando...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
                customClass: {
                    container: 'miSwalZ'
                }
            });

            const formData = new FormData(this);

            const resp = await fetch(
                '/myvet/app/controllers/productosController.php?action=guardarOpcionMedida',
                {
                    method: 'POST',
                    body: formData
                }
            );

            const data = await resp.json();

            Swal.close();

            if (data.success || data.status === 'success') {

                await Swal.fire({
                    icon: 'success',
                    title: 'Guardado',
                    text: 'Medida agregada correctamente',
                    timer: 1500,
                    showConfirmButton: false,
                    customClass: {
                        container: 'miSwalZ'
                    }
                });

                const modalEl = document.getElementById('modalMedidaAdicional');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) {
                    modalInstance.hide();
                }

                document.getElementById('formMedidaAdicional').reset();

                if (typeof recargarModalMedidas === 'function') {
                    recargarModalMedidas();
                }

            } else {

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'No se pudo guardar',
                    customClass: {
                        container: 'miSwalZ'
                    }
                });
            }

        } catch (error) {

            console.error(error);

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Falló la comunicación con el servidor',
                customClass: {
                    container: 'miSwalZ'
                }
            });
        }
    });
</script>
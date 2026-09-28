 /* ================= FILTROS TIEMPO REAL ================= */
    function aplicarFiltros() {

        let texto = document.getElementById("buscador").value.toLowerCase();
        let categoria = document.getElementById("filtroCategoria").value;
        let almacen = document.getElementById("filtroAlmacen").value;

        document.querySelectorAll(".tabla-productos tbody tr").forEach(fila => {

            let coincideTexto = fila.innerText.toLowerCase().includes(texto);
            let coincideCategoria = !categoria || fila.dataset.categoria === categoria;
            let coincideAlmacen = !almacen || fila.dataset.almacen === almacen;

            fila.style.display = (coincideTexto && coincideCategoria && coincideAlmacen) ? "" : "none";

        });
    }

    document.getElementById("buscador").addEventListener("keyup", aplicarFiltros);
    document.getElementById("filtroCategoria").addEventListener("change", aplicarFiltros);
    document.getElementById("filtroAlmacen").addEventListener("change", aplicarFiltros);


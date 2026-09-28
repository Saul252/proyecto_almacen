<?php
$reglasPorAlm = [];
if (!empty($listaConversiones)) {
    foreach ($listaConversiones as $r) { $reglasPorAlm[$r['almacen']][] = $r; }
}
$isGlobal = (int)($_SESSION['almacen_id'] ?? 0) === 0;
?>

<style>
    .tm-widget { max-width: 450px; font-family: -apple-system, system-ui, sans-serif; font-size: 0.8rem; }
    .tm-search-box { background: #f2f2f7; border-radius: 10px; border: 1px solid #d1d1d6; padding: 4px 10px; display: flex; align-items: center; }
    .tm-search-box input {  background: transparent; width: 100%; font-size: 0.75rem; padding: 2px 5px; outline: none; }
    
    /* Contenedor con Scroll Interno */
    .tm-scroll-area { 
        max-height: 400px; /* Ajusta esta altura según prefieras */
        overflow-y: auto; 
        padding-right: 5px; 
        margin-top: 10px;
    }

    /* Scrollbar Estética (Chrome/Safari) */
    .tm-scroll-area::-webkit-scrollbar { width: 4px; }
    .tm-scroll-area::-webkit-scrollbar-track { background: transparent; }
    .tm-scroll-area::-webkit-scrollbar-thumb { background: #d1d1d6; border-radius: 10px; }
    .tm-scroll-area::-webkit-scrollbar-thumb:hover { background: #8e8e93; }

    .tm-item { background: #fff; border-radius: 8px; border: 1px solid #efeff4; padding: 8px; margin-bottom: 5px; display: flex; align-items: center; justify-content: space-between; transition: 0.2s; }
    .tm-item:hover { border-color: #007aff; background: #fbfbfd; }
    
    .tm-info { line-height: 1.2; flex-grow: 1; }
    .tm-origin { color: #3a3a3c; font-weight: 600; font-size: 0.75rem; }
    .tm-arrow { color: #007aff; font-size: 0.6rem; margin: 0 4px; }
    .tm-dest { color: #007aff; font-weight: 500; font-size: 0.75rem; }
    .tm-sku-mini { color: #8e8e93; font-size: 0.65rem; font-family: monospace; display: block; }
    
    .tm-factor-pill { background: #007aff; color: #fff; padding: 2px 8px; border-radius: 6px; font-weight: 700; font-size: 0.7rem; min-width: 55px; text-align: center; margin-left: 10px; }
    .tm-label-alm { font-size: 0.65rem; color: #8e8e93; font-weight: 700; text-transform: uppercase; margin: 10px 0 5px 5px; display: block; }
</style>

<div class="tm-widget">
    <div class="tm-search-box mb-2">
        <i class="bi bi-search text-body-secondary small"></i>
        <input type="text" id="tm-q" placeholder="Producto o SKU..." onkeyup="tm_f()">
        <?php if ($isGlobal): ?>
            <select id="tm-a" onchange="tm_f()" style="border:none; background:transparent; font-size:0.65rem; font-weight:bold; outline:none; cursor:pointer;">
                <option value="all">TODOS</option>
                <?php foreach (array_keys($reglasPorAlm) as $n): ?>
                    <option value="<?= strtolower(trim($n)) ?>"><?= $n ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
    </div>

    <div id="tm-list" class="tm-scroll-area">
        <?php foreach ($reglasPorAlm as $nombreAlm => $reglas): ?>
            <div class="tm-sec" data-a="<?= strtolower(trim($nombreAlm)) ?>">
                <span class="tm-label-alm"><i class="bi bi-geo-alt"></i> <?= $nombreAlm ?></span>
                <?php foreach ($reglas as $r): ?>
                    <div class="tm-item tm-row">
                        <div class="tm-info">
                            <div>
                                <span class="tm-origin"><?= $r['producto_origen'] ?></span>
                                <i class="bi bi-arrow-right tm-arrow"></i>
                                <span class="tm-dest"><?= $r['producto_destino'] ?></span>
                            </div>
                            <span class="tm-sku-mini"><?= $r['sku_origen'] ?> → <?= $r['sku_destino'] ?></span>
                        </div>
                        <div class="tm-factor-pill">
                            1:<?= number_format($r['rendimiento_teorico'], 1) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function tm_f() {
    const q = document.getElementById('tm-q').value.toLowerCase().trim();
    const a = document.getElementById('tm-a')?.value || "all";
    document.querySelectorAll('.tm-sec').forEach(s => {
        let has = 0;
        const aM = (a === "all" || s.getAttribute('data-a') === a);
        s.querySelectorAll('.tm-row').forEach(r => {
            const txt = r.innerText.toLowerCase();
            const show = aM && txt.includes(q);
            r.style.display = show ? "flex" : "none";
            if(show) has++;
        });
        s.style.display = has > 0 ? "block" : "none";
    });
}
</script>
SELECT 
    v.id AS id_venta,
    MAX(v.folio) AS folio_venta,
    MAX(c.nombre_comercial) AS cliente,
    MAX(trm.vehiculo_id) AS vehiculo_id,
    MAX(tc.viaje_folio) AS folio_viaje,
    MAX(trp.descripcion_punto) AS direccion_entrega,
    MAX(trp.estado_punto) AS estatus_parada,
    MAX(crv.id) AS id_evidencia,
    MAX(crv.estatus) AS estatus_evidencia,
    MAX(crv.comentario) AS comentario_evidencia,
    MAX(IF(crv.id IS NOT NULL AND crv.entrega_id = '899', 1, 0)) AS ya_entregado,
    
    -- Trae el último ID de movimiento de esta venta
    MAX(trp.id) AS ids_movimientos_grupo,
    
    -- Evidencias fotográficas basadas únicamente en crv.entrega_id
    MAX(IF(crv.fotografia_entrega IS NOT NULL AND crv.entrega_id = '899' AND crv.fotografia_entrega != '', CONCAT('/myvet/', crv.fotografia_entrega), NULL)) AS foto_registrada,
    MAX(IF(crv.fotografia_nota IS NOT NULL AND crv.entrega_id = '899' AND crv.fotografia_nota != '', CONCAT('/myvet/', crv.fotografia_nota), NULL)) AS nota_registrada,
    
    -- Agrupación de productos y sus detalles
    GROUP_CONCAT(p.nombre SEPARATOR ', ') AS productos,
    GROUP_CONCAT(CONCAT(m.cantidad, ' ', p.unidad_medida) SEPARATOR ', ') AS cantidades_detalladas,
    
    -- Suma de cantidades
    SUM(m.cantidad) AS total_piezas_venta
FROM transporte_consolidacion tc
INNER JOIN transporte_repartos_maestro trm ON tc.reparto_id = trm.id
INNER JOIN transporte_rutas_puntos trp ON trm.id = trp.reparto_id 
INNER JOIN movimientos m ON trm.entrega_venta_id = m.id
INNER JOIN productos p ON m.producto_id = p.id
LEFT JOIN ventas v ON m.referencia_id = v.id
LEFT JOIN clientes c ON v.id_cliente = c.id
LEFT JOIN confirmacion_reparto_viaje crv 
    ON v.id = crv.id_venta 
    AND crv.entrega_id = tc.entrega_id
WHERE tc.entrega_id = '899'
GROUP BY v.id;
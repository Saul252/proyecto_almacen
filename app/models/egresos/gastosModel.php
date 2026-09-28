<?php
/**
 * GastoModel.php
 * Modelo para la gestión de folios y registros de gastos
 */
class GastoModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    /**
     * Genera el siguiente folio para un gasto
     * Formato sugerido: GAS-0001
     */
//     public function generarSiguienteFolioGasto($almacen_id=0) {
//     // Buscamos el ID más alto de la tabla gastos
//     $sql = "SELECT (MAX(CAST(folio AS UNSIGNED)) + 1) AS ultimo_id FROM gastos where almacen_id=$almacen_id";
//     $resultado = $this->db->query($sql);
//     $fila = $resultado->fetch_assoc();
    
//     $ultimoId = $fila['ultimo_id'] ?? 0;
//     $nuevoId = $ultimoId ;

//     // Retornamos solo el número puro
//     return $nuevoId;
// }
public function generarSiguienteFolioGasto($almacen_id = 0) {
    $almacen_id = (int)$almacen_id;

    if ($almacen_id > 0) {
        // Consulta filtrada para un almacén específico
        $sql = "SELECT COALESCE(MAX(CAST(folio AS UNSIGNED)), 0) + 1 AS siguiente_folio 
                FROM gastos 
                WHERE almacen_id = ?";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return 1;

        $stmt->bind_param("i", $almacen_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        $stmt->close();
    } else {
        // Si el almacén es 0 (Admin / Global), obtiene el máximo de TODOS los registros
        $sql = "SELECT COALESCE(MAX(CAST(folio AS UNSIGNED)), 0) + 1 AS siguiente_folio 
                FROM gastos";
        
        $resultado = $this->db->query($sql);
        if (!$resultado) return 1;

        $fila = $resultado->fetch_assoc();
    }

    return (int)($fila['siguiente_folio'] ?? 1);
}
public function actualizarDocumentoGasto($id, $documento_url) {

    $sql = "UPDATE gastos 
            SET documento_url = ?
            WHERE id = ?";

    $stmt = $this->db->prepare($sql);
    if (!$stmt) return false;

    $stmt->bind_param("si", $documento_url, $id);

    return $stmt->execute();
}

    /**
     * Registra un gasto completo (Cabecera y Detalle)
     * Basado en la estructura de tus tablas 'gastos' y 'detalle_gasto'
     */
    public function registrarGastoCompleto($datosCabecera, $detalles) {
        $this->db->begin_transaction();
        try {
            // 1. Insertar Cabecera
            $sqlCabecera = "INSERT INTO gastos (folio, fecha_gasto, almacen_id, usuario_registra_id, total) 
                            VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sqlCabecera);
            $stmt->bind_param("ssiid", 
                $datosCabecera['folio'], 
                $datosCabecera['fecha'], 
                $datosCabecera['almacen_id'], 
                $datosCabecera['usuario_id'], 
                $datosCabecera['total']
            );
            $stmt->execute();
            $gastoId = $this->db->insert_id;

            // 2. Insertar Detalles
            $sqlDetalle = "INSERT INTO detalle_gasto (gasto_id, descripcion, subtotal) VALUES (?, ?, ?)";
            $stmtDet = $this->db->prepare($sqlDetalle);

            foreach ($detalles as $item) {
                $stmtDet->bind_param("isd", 
                    $gastoId, 
                    $item['descripcion'], 
                    $item['subtotal']
                );
                $stmtDet->execute();
            }

            $this->db->commit();
            return ['success' => true, 'id' => $gastoId];

        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    public function cancelarGastoConRazon($id_gasto, $id_usuario, $razon) {
    try {
        // 1. Obtener nombre del usuario y observaciones actuales
        $sqlInfo = "SELECT g.observaciones, u.nombre as nombre_usuario 
                    FROM gastos g 
                    JOIN usuarios u ON u.id = ? 
                    WHERE g.id = ?";
        $stmtInfo = $this->db->prepare($sqlInfo);
        $stmtInfo->bind_param("ii", $id_usuario, $id_gasto);
        $stmtInfo->execute();
        $info = $stmtInfo->get_result()->fetch_assoc();

        if (!$info) throw new Exception("Gasto o Usuario no encontrado.");

        $obsAnterior = $info['observaciones'] ?? "";
        $nombreUser = $info['nombre_usuario'];
        $fechaHoy = date('Y-m-d H:i');

        // 2. Construir nueva leyenda de observaciones
        $nuevaLeyenda = trim($obsAnterior) . "\n" . 
                        "*** CANCELADO por $nombreUser el $fechaHoy ***\n" . 
                        "RAZÓN: " . $razon;

        // 3. Actualizar el registro
        $sqlUpd = "UPDATE gastos SET 
                   estado = 'cancelado', 
                   observaciones = ? 
                   WHERE id = ?";
        $stmtUpd = $this->db->prepare($sqlUpd);
        $stmtUpd->bind_param("si", $nuevaLeyenda, $id_gasto);
        $stmtUpd->execute();

        return ['success' => true, 'message' => "Gasto cancelado y documentado correctamente."];

    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
}
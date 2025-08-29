<?php
class MovimientoCaja {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function obtenerTodos() {
        $stmt = $this->conn->prepare("SELECT mc.*, f.numero_factura, mp.metodo_pago, c.cliente 
                                     FROM movimientos_caja mc 
                                     JOIN facturas f ON mc.id_factura = f.id_factura 
                                     JOIN metodos_pago mp ON mc.id_metodo_pago = mp.id_metodo_pago 
                                     JOIN clientes c ON f.id_cliente = c.id_cliente 
                                     WHERE mc.deleted = 0 
                                     ORDER BY mc.fecha_pago DESC");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare("SELECT mc.*, f.numero_factura, mp.metodo_pago, c.cliente 
                                     FROM movimientos_caja mc 
                                     JOIN facturas f ON mc.id_factura = f.id_factura 
                                     JOIN metodos_pago mp ON mc.id_metodo_pago = mp.id_metodo_pago 
                                     JOIN clientes c ON f.id_cliente = c.id_cliente 
                                     WHERE mc.id_movimiento = ? AND mc.deleted = 0");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function obtenerPorFactura($id_factura) {
        $stmt = $this->conn->prepare("SELECT mc.*, mp.metodo_pago 
                                     FROM movimientos_caja mc 
                                     JOIN metodos_pago mp ON mc.id_metodo_pago = mp.id_metodo_pago 
                                     WHERE mc.id_factura = ? AND mc.deleted = 0 
                                     ORDER BY mc.fecha_pago DESC");
        $stmt->bind_param("i", $id_factura);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function crear($id_factura, $id_metodo_pago, $fecha_pago, $monto, $observaciones) {
        // Iniciar transacción
        $this->conn->begin_transaction();
        
        try {
            // 1. Insertar el movimiento de caja
            $stmt = $this->conn->prepare("INSERT INTO movimientos_caja (id_factura, id_metodo_pago, fecha_pago, monto, observaciones) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisds", $id_factura, $id_metodo_pago, $fecha_pago, $monto, $observaciones);
            $result = $stmt->execute();
            
            if (!$result) {
                throw new Exception("Error al crear el movimiento de caja");
            }
            
            // 2. Calcular el total pagado hasta ahora
            $stmt = $this->conn->prepare("
                SELECT f.total, COALESCE(SUM(mc.monto), 0) as total_pagado
                FROM facturas f
                LEFT JOIN movimientos_caja mc ON f.id_factura = mc.id_factura AND mc.deleted = 0
                WHERE f.id_factura = ?
                GROUP BY f.id_factura
            ");
            $stmt->bind_param("i", $id_factura);
            $stmt->execute();
            $factura = $stmt->get_result()->fetch_assoc();
            
            if (!$factura) {
                throw new Exception("Factura no encontrada");
            }
            
            $total = floatval($factura['total']);
            $total_pagado = floatval($factura['total_pagado']);
            
            // 3. Determinar el nuevo estado de la factura
            $nuevo_estado = 1; // Por defecto, Emitida
            
            if ($total_pagado <= 0) {
                $nuevo_estado = 1; // Emitida
            } elseif ($total_pagado > 0 && $total_pagado < $total) {
                $nuevo_estado = 2; // Parcialmente Pagada
            } elseif ($total_pagado >= $total) {
                $nuevo_estado = 3; // Pagada
            }
            
            // 4. Actualizar el estado de la factura
            $stmt = $this->conn->prepare("UPDATE facturas SET estado = ? WHERE id_factura = ?");
            $stmt->bind_param("ii", $nuevo_estado, $id_factura);
            $result = $stmt->execute();
            
            if (!$result) {
                throw new Exception("Error al actualizar el estado de la factura");
            }
            
            // Si todo salió bien, confirmar la transacción
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            // Si hay algún error, revertir la transacción
            $this->conn->rollback();
            error_log("Error en MovimientoCaja::crear: " . $e->getMessage());
            return false;
        }
    }

    public function editar($id_movimiento, $id_factura, $id_metodo_pago, $fecha_pago, $monto, $observaciones) {
        $stmt = $this->conn->prepare("UPDATE movimientos_caja SET id_factura = ?, id_metodo_pago = ?, fecha_pago = ?, monto = ?, observaciones = ? WHERE id_movimiento = ?");
        $stmt->bind_param("iisdsi", $id_factura, $id_metodo_pago, $fecha_pago, $monto, $observaciones, $id_movimiento);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $stmt = $this->conn->prepare("UPDATE movimientos_caja SET deleted = 1 WHERE id_movimiento = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function obtenerTotalPagadoPorFactura($id_factura) {
        $stmt = $this->conn->prepare("SELECT SUM(monto) as total_pagado 
                                     FROM movimientos_caja 
                                     WHERE id_factura = ? AND deleted = 0");
        $stmt->bind_param("i", $id_factura);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total_pagado'] ?? 0;
    }
}

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
        $stmt = $this->conn->prepare("INSERT INTO movimientos_caja (id_factura, id_metodo_pago, fecha_pago, monto, observaciones) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisds", $id_factura, $id_metodo_pago, $fecha_pago, $monto, $observaciones);
        return $stmt->execute();
    }

    public function editar($id_movimiento, $id_factura, $id_metodo_pago, $fecha_pago, $monto, $observaciones) {
        $stmt = $this->conn->prepare("UPDATE movimientos_caja SET id_factura = ?, id_metodo_pago = ?, fecha_pago = ?, monto = ?, observaciones = ? WHERE id_movimiento = ?");
        $stmt->bind_param("iidsdi", $id_factura, $id_metodo_pago, $fecha_pago, $monto, $observaciones, $id_movimiento);
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

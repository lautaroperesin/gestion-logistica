<?php
class Factura {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function obtenerTodos() {
        $stmt = $this->conn->prepare("SELECT f.*, e.numero_seguimiento, c.cliente 
                                     FROM facturas f 
                                     JOIN envios e ON f.id_envio = e.id_envio 
                                     JOIN clientes c ON f.id_cliente = c.id_cliente 
                                     WHERE f.deleted = 0 
                                     ORDER BY f.fecha_emision DESC");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare("SELECT f.*, e.numero_seguimiento, c.cliente 
                                     FROM facturas f 
                                     JOIN envios e ON f.id_envio = e.id_envio 
                                     JOIN clientes c ON f.id_cliente = c.id_cliente 
                                     WHERE f.id_factura = ? AND f.deleted = 0");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function crear($id_envio, $numero_factura, $fecha_emision, $fecha_vencimiento, $id_cliente, $iva, $subtotal, $total, $estado) {
        $stmt = $this->conn->prepare("INSERT INTO facturas (id_envio, numero_factura, fecha_emision, fecha_vencimiento, id_cliente, iva, subtotal, total, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssiiiii", $id_envio, $numero_factura, $fecha_emision, $fecha_vencimiento, $id_cliente, $iva, $subtotal, $total, $estado);
        return $stmt->execute();
    }

    public function editar($id_factura, $id_envio, $numero_factura, $fecha_emision, $fecha_vencimiento, $id_cliente, $iva, $subtotal, $total, $estado) {
        $stmt = $this->conn->prepare("UPDATE facturas SET id_envio = ?, numero_factura = ?, fecha_emision = ?, fecha_vencimiento = ?, id_cliente = ?, iva = ?, subtotal = ?, total = ?, estado = ? WHERE id_factura = ?");
        $stmt->bind_param("isssiiiiii", $id_envio, $numero_factura, $fecha_emision, $fecha_vencimiento, $id_cliente, $iva, $subtotal, $total, $estado, $id_factura);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $stmt = $this->conn->prepare("UPDATE facturas SET deleted = 1 WHERE id_factura = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Métodos para filtrar facturas
    public function obtenerPorEnvio($id_envio) {
        $stmt = $this->conn->prepare("SELECT * FROM facturas WHERE id_envio = ? AND deleted = 0");
        $stmt->bind_param("i", $id_envio);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerPorCliente($id_cliente) {
        $stmt = $this->conn->prepare("SELECT * FROM facturas WHERE id_cliente = ? AND deleted = 0 ORDER BY fecha_emision DESC");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerPorEstado($estado) {
        $stmt = $this->conn->prepare("SELECT * FROM facturas WHERE estado = ? AND deleted = 0 ORDER BY fecha_emision DESC");
        $stmt->bind_param("i", $estado);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerPorFecha($fecha_inicio, $fecha_fin) {
        $stmt = $this->conn->prepare("SELECT * FROM facturas WHERE fecha_emision BETWEEN ? AND ? AND deleted = 0 ORDER BY fecha_emision DESC");
        $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

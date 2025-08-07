<?php
class Envio {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function obtenerTodos() {
        $stmt = $this->conn->prepare("SELECT * FROM envios WHERE deleted = 0 ORDER BY fecha_creacion_envio DESC");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM envios WHERE id_envio = ? AND deleted = 0");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function crear($id_origen, $id_destino, $fecha_salida, $id_estado_envio, $peso_kg, $id_vehiculo, $descripcion, $costo_total, $id_conductor, $id_cliente, $id_tipo_carga, $numero_seguimiento, $volumen_m3) {
        $stmt = $this->conn->prepare("INSERT INTO envios (id_origen, id_destino, fecha_creacion_envio, fecha_salida, id_estado_envio, peso_kg, id_vehiculo, descripcion, costo_total, id_conductor, id_cliente, id_tipo_carga, numero_seguimiento, volumen_m3) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisddssdddi", $id_origen, $id_destino, $fecha_salida, $id_estado_envio, $peso_kg, $id_vehiculo, $descripcion, $costo_total, $id_conductor, $id_cliente, $id_tipo_carga, $numero_seguimiento, $volumen_m3);
        return $stmt->execute();
    }

    public function editar($id_envio, $id_origen, $id_destino, $fecha_salida, $id_estado_envio, $peso_kg, $id_vehiculo, $descripcion, $costo_total, $id_conductor, $id_cliente, $id_tipo_carga, $numero_seguimiento, $volumen_m3) {
        $stmt = $this->conn->prepare("UPDATE envios SET id_origen = ?, id_destino = ?, fecha_salida = ?, id_estado_envio = ?, peso_kg = ?, id_vehiculo = ?, descripcion = ?, costo_total = ?, id_conductor = ?, id_cliente = ?, id_tipo_carga = ?, numero_seguimiento = ?, volumen_m3 = ? WHERE id_envio = ?");
        $stmt->bind_param("iiisddssdddi", $id_origen, $id_destino, $fecha_salida, $id_estado_envio, $peso_kg, $id_vehiculo, $descripcion, $costo_total, $id_conductor, $id_cliente, $id_tipo_carga, $numero_seguimiento, $volumen_m3, $id_envio);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $stmt = $this->conn->prepare("UPDATE envios SET deleted = 1 WHERE id_envio = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function obtenerPorEstado($estado) {
        $stmt = $this->conn->prepare("SELECT * FROM envios WHERE id_estado_envio = ? AND deleted = 0 ORDER BY fecha_creacion_envio DESC");
        $stmt->bind_param("i", $estado);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerPorCliente($id_cliente) {
        $stmt = $this->conn->prepare("SELECT * FROM envios WHERE id_cliente = ? AND deleted = 0 ORDER BY fecha_creacion_envio DESC");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

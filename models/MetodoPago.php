<?php
class MetodoPago {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function obtenerTodos() {
        $stmt = $this->conn->prepare("SELECT * FROM metodos_pago");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM metodos_pago WHERE id_metodo_pago = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function crear($metodo_pago) {
        $stmt = $this->conn->prepare("INSERT INTO metodos_pago (metodo_pago) VALUES (?)");
        $stmt->bind_param("s", $metodo_pago);
        return $stmt->execute();
    }

    public function editar($id, $metodo_pago) {
        $stmt = $this->conn->prepare("UPDATE metodos_pago SET metodo_pago = ? WHERE id_metodo_pago = ?");
        $stmt->bind_param("si", $metodo_pago, $id);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $stmt = $this->conn->prepare("DELETE FROM metodos_pago WHERE id_metodo_pago = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

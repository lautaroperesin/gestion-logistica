<?php
class TipoCarga {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function obtenerTodos() {
        $stmt = $this->conn->prepare("SELECT * FROM tipos_carga ORDER BY id_tipo_carga ASC");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM tipos_carga WHERE id_tipo_carga = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function crear($carga) {
        $stmt = $this->conn->prepare("INSERT INTO tipos_carga (carga) VALUES (?)");
        $stmt->bind_param("s", $carga);
        return $stmt->execute();
    }

    public function editar($id_tipo_carga, $carga) {
        $stmt = $this->conn->prepare("UPDATE tipos_carga SET carga = ? WHERE id_tipo_carga = ?");
        $stmt->bind_param("si", $carga, $id_tipo_carga);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $stmt = $this->conn->prepare("DELETE FROM tipos_carga WHERE id_tipo_carga = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
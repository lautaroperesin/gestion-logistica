<?php
class EstadoEnvio {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function obtenerTodos() {
        $stmt = $this->conn->prepare("SELECT * FROM estados_envio WHERE deleted = 0");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function crear($nombre) {
        $sql = "INSERT INTO estados_envio (estado) VALUES (?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $nombre);
        return $stmt->execute();
    }

    public function eliminar($id) {
        // Soft delete - marcamos como eliminado en lugar de borrar físicamente
        $sql = "UPDATE estados_envio SET deleted = 1 WHERE id_estado_envio = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

<?php
class EstadoEnvio {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function obtenerTodos() {
        $stmt = $this->conn->prepare("SELECT * FROM estados_envio");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

<?php
class Cliente {
    private $conn;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }

    public function obtenerTodos() {
        $sql = "SELECT * FROM clientes WHERE deleted = 0 ORDER BY cliente ASC";
        return $this->conn->query($sql);
    }


    public function obtenerPorId($id_cliente) {
        $stmt = $this->conn->prepare("SELECT * FROM clientes WHERE id_cliente = ? AND deleted = 0");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function crear($nombre, $email, $telefono) {
        $stmt = $this->conn->prepare("INSERT INTO clientes (cliente, email, telefono) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $email, $telefono);
        return $stmt->execute();
    }

    public function editar($id_cliente, $nombre, $email, $telefono) {
        $stmt = $this->conn->prepare("UPDATE clientes SET cliente = ?, email = ?, telefono = ? WHERE id_cliente = ?");
        $stmt->bind_param("sssi", $nombre, $email, $telefono, $id_cliente);
        return $stmt->execute();
    }

    public function eliminar($id_cliente) {
        $stmt = $this->conn->prepare("UPDATE clientes SET deleted = 1 WHERE id_cliente = ?");
        $stmt->bind_param("i", $id_cliente);
        return $stmt->execute();
    }
    
    public function buscarPorNombre($termino) {
        $termino = "%$termino%";
        $stmt = $this->conn->prepare("SELECT * FROM clientes WHERE cliente LIKE ? AND deleted = 0 ORDER BY cliente ASC");
        $stmt->bind_param("s", $termino);
        $stmt->execute();
        return $stmt->get_result();
    }
}

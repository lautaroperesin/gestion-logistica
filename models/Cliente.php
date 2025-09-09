<?php
class Cliente {
    private $conn;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }

    public function contarTotal($buscar = '') {
        $sql = "SELECT COUNT(*) as total FROM clientes WHERE deleted = 0";
        
        if (!empty($buscar)) {
            $buscar = "%$buscar%";
            $sql .= " AND cliente LIKE ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $buscar);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc()['total'];
        }
        
        $result = $this->conn->query($sql);
        return $result->fetch_assoc()['total'];
    }

    public function obtenerTodos($porPagina = 10, $pagina = 1, $buscar = '') {
        $offset = ($pagina - 1) * $porPagina;
        
        if (!empty($buscar)) {
            $buscar = "%$buscar%";
            $sql = "SELECT * FROM clientes WHERE cliente LIKE ? AND deleted = 0 ORDER BY cliente ASC LIMIT ? OFFSET ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sii", $buscar, $porPagina, $offset);
        } else {
            $sql = "SELECT * FROM clientes WHERE deleted = 0 ORDER BY cliente ASC LIMIT ? OFFSET ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $porPagina, $offset);
        }
        
        $stmt->execute();
        return $stmt->get_result();
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
    
    // Este método ya no es necesario ya que la búsqueda está integrada en obtenerTodos
}

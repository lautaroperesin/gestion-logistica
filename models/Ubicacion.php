<?php
class Ubicacion {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function obtenerTodos() {
        $stmt = $this->conn->prepare("SELECT u.*, l.localidad, p.provincia, pa.pais 
                                     FROM ubicaciones u 
                                     JOIN localidades l ON u.id_localidad = l.id_localidad 
                                     JOIN provincias p ON l.id_provincia = p.id_provincia 
                                     JOIN paises pa ON p.id_pais = pa.id_pais 
                                     WHERE u.deleted = 0 ");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare("SELECT u.*, l.localidad, l.id_localidad, p.provincia, p.id_provincia, pa.pais, pa.id_pais 
                                      FROM ubicaciones u 
                                      JOIN localidades l ON u.id_localidad = l.id_localidad 
                                      JOIN provincias p ON l.id_provincia = p.id_provincia 
                                      JOIN paises pa ON p.id_pais = pa.id_pais 
                                      WHERE u.id_ubicacion = ? AND u.deleted = 0");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function crear($direccion, $id_localidad, $descripcion) {
        $stmt = $this->conn->prepare("INSERT INTO ubicaciones (direccion, id_localidad, descripcion) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $direccion, $id_localidad, $descripcion);
        return $stmt->execute();
    }

    public function editar($id_ubicacion, $direccion, $id_localidad, $descripcion) {
        $stmt = $this->conn->prepare("UPDATE ubicaciones SET direccion = ?, id_localidad = ?, descripcion = ? WHERE id_ubicacion = ?");
        $stmt->bind_param("sisi", $direccion, $id_localidad, $descripcion, $id_ubicacion);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $stmt = $this->conn->prepare("UPDATE ubicaciones SET deleted = 1 WHERE id_ubicacion = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Metodos para obtener paises, provincias y localidades para select en el formulario para agregar o editar una ubicacion.
    public function obtenerPaises() {
        $stmt = $this->conn->prepare("SELECT * FROM paises WHERE deleted = 0");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerProvinciasPorPais($id_pais) {
        $stmt = $this->conn->prepare("SELECT * FROM provincias WHERE deleted = 0 AND id_pais = ?");
        $stmt->bind_param("i", $id_pais);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerLocalidadesPorProvincia($id_provincia) {
        $stmt = $this->conn->prepare("SELECT * FROM localidades WHERE deleted = 0 AND id_provincia = ?");
        $stmt->bind_param("i", $id_provincia);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Metodo para obtener ubicaciones por localidad y provincia, puede servirme para informes.
    public function obtenerPorLocalidad($id_localidad) {
        $stmt = $this->conn->prepare("SELECT * FROM ubicaciones WHERE id_localidad = ? AND deleted = 0");
        $stmt->bind_param("i", $id_localidad);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerPorProvincia($id_provincia) {
        $stmt = $this->conn->prepare("SELECT u.* FROM ubicaciones u 
                                     JOIN localidades l ON u.id_localidad = l.id_localidad 
                                     WHERE l.id_provincia = ? AND u.deleted = 0");
        $stmt->bind_param("i", $id_provincia);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

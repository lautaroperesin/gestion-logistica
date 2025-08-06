<?php
class Conductor {
    private $conn;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }

    public function obtenerTodos() {
        $sql = "SELECT * FROM conductores WHERE deleted = 0";
        return $this->conn->query($sql);
    }


    public function obtenerPorId($id_conductor) {
        $stmt = $this->conn->prepare("SELECT * FROM conductores WHERE id_conductor = ? AND deleted = 0");
        $stmt->bind_param("i", $id_conductor);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function crear($nombre, $email, $telefono, $dni, $claseLicencia, $vencimientoLicencia) {
        $stmt = $this->conn->prepare("INSERT INTO conductores (conductor, email, telefono, dni, clase_licencia, vencimiento_licencia) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nombre, $email, $telefono, $dni, $claseLicencia, $vencimientoLicencia);
        return $stmt->execute();
    }

    public function editar($id_conductor, $nombre, $email, $telefono, $dni, $claseLicencia, $vencimientoLicencia) {
        $stmt = $this->conn->prepare("UPDATE conductores SET conductor = ?, email = ?, telefono = ?, dni = ?, clase_licencia = ?, vencimiento_liencia = ? WHERE id_conductor = ?");
        $stmt->bind_param("ssssssi", $nombre, $email, $telefono, $dni, $claseLicencia, $vencimientoLicencia, $id_conductor);
        return $stmt->execute();
    }

    public function eliminar($id_conductor) {
        $stmt = $this->conn->prepare("UPDATE conductores SET deleted = 1 WHERE id_conductor = ?");
        $stmt->bind_param("i", $id_conductor);
        return $stmt->execute();
    }
}

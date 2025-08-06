<?php

class Vehiculo {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function obtenerTodos() {
        $stmt = $this->conn->prepare("SELECT * FROM vehiculos WHERE deleted = 0 ORDER BY patente");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM vehiculos WHERE id_vehiculo = ? AND deleted = 0");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function crear($patente, $marca, $modelo, $capacidad_kg, $ultima_inspeccion, $estado_vehiculo, $rto_vencimiento) {
        $stmt = $this->conn->prepare("INSERT INTO vehiculos (patente, marca, modelo, capacidad_kg, ultima_inspeccion, estado_vehiculo, rto_vencimiento) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdiss", $patente, $marca, $modelo, $capacidad_kg, $ultima_inspeccion, $estado_vehiculo, $rto_vencimiento);
        return $stmt->execute();
    }

    public function editar($id_vehiculo, $patente, $marca, $modelo, $capacidad_kg, $ultima_inspeccion, $estado_vehiculo, $rto_vencimiento) {
        $stmt = $this->conn->prepare("UPDATE vehiculos SET patente = ?, marca = ?, modelo = ?, capacidad_kg = ?, ultima_inspeccion = ?, estado_vehiculo = ?, rto_vencimiento = ? WHERE id_vehiculo = ?");
        $stmt->bind_param("sssdissi", $patente, $marca, $modelo, $capacidad_kg, $ultima_inspeccion, $estado_vehiculo, $rto_vencimiento, $id_vehiculo);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $stmt = $this->conn->prepare("UPDATE vehiculos SET deleted = 1 WHERE id_vehiculo = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function cambiarEstado($id_vehiculo, $estado) {
        $stmt = $this->conn->prepare("UPDATE vehiculos SET estado_vehiculo = ? WHERE id_vehiculo = ?");
        $stmt->bind_param("ii", $estado, $id_vehiculo);
        return $stmt->execute();
    }

    public function obtenerVehiculosDisponibles() {
        $stmt = $this->conn->prepare("SELECT * FROM vehiculos WHERE deleted = 0 AND estado_vehiculo = 1 ORDER BY patente");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

<?php
require_once __DIR__ . '/../config/database.php';

class DashboardController {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function index() {
        // Obtener estadísticas para el dashboard
        $stats = $this->getStats();
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/dashboard/index.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    private function getStats() {
        $conn = $this->db->getConnection();
        $stats = [
            'clientes' => 0,
            'conductores' => 0,
            'vehiculos' => 0,
            'tipos_carga' => 0
        ];

        // Contar clientes activos
        $result = $conn->query("SELECT COUNT(*) as total FROM clientes WHERE deleted = 0");
        if ($result && $row = $result->fetch_assoc()) {
            $stats['clientes'] = $row['total'];
        }

        // Contar conductores activos (si existe la tabla)
        $result = $conn->query("SHOW TABLES LIKE 'conductores'");
        if ($result && $result->num_rows > 0) {
            $result = $conn->query("SELECT COUNT(*) as total FROM conductores WHERE deleted = 0");
            if ($result && $row = $result->fetch_assoc()) {
                $stats['conductores'] = $row['total'];
            }
        }

        // Contar vehículos activos (si existe la tabla)
        $result = $conn->query("SHOW TABLES LIKE 'vehiculos'");
        if ($result && $result->num_rows > 0) {
            $result = $conn->query("SELECT COUNT(*) as total FROM vehiculos WHERE deleted = 0");
            if ($result && $row = $result->fetch_assoc()) {
                $stats['vehiculos'] = $row['total'];
            }
        }

        // Contar tipos de carga (si existe la tabla)
        $result = $conn->query("SHOW TABLES LIKE 'tipos_carga'");
        if ($result && $result->num_rows > 0) {
            $result = $conn->query("SELECT COUNT(*) as total FROM tipos_carga");
            if ($result && $row = $result->fetch_assoc()) {
                $stats['tipos_carga'] = $row['total'];
            }
        }

        return $stats;
    }
}
?>

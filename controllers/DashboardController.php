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
            // Estadísticas generales
            'clientes' => 0,
            'conductores' => 0,
            'facturas_pendientes' => 0,
            'monto_pendiente' => '0.00',
            'envios_mes_actual' => 0,
            'ingresos_mes_actual' => '0.00',
            'envios_pendientes' => 0,
            'carga_vehiculos' => [],
            'top_clientes' => [],
            'envios_por_mes' => [],
            'estados_envio' => []
        ];
        
        // Para manejo de errores
        $error = null;

        // Obtener facturas pendientes de pago
        $result = $conn->query("
            SELECT 
                f.id_factura,
                f.total,
                (SELECT COALESCE(SUM(mc.monto), 0) 
                 FROM movimientos_caja mc 
                 WHERE mc.id_factura = f.id_factura) as pagado
            FROM facturas f
            WHERE (f.estado = 2 OR f.estado = 1)
            AND f.deleted = 0
            HAVING (total - pagado) > 0
        ") or die($conn->error);
        
        $total_pendiente = 0;
        $total_facturas = 0;
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $total_pendiente += ($row['total'] - $row['pagado']);
                $total_facturas++;
            }
            $stats['facturas_pendientes'] = $total_facturas;
            $stats['monto_pendiente'] = number_format($total_pendiente, 2, '.', '');
        }

        // Contar clientes activos
        $result = $conn->query("SELECT COUNT(*) as total FROM clientes WHERE deleted = 0");
        if ($result && $row = $result->fetch_assoc()) {
            $stats['clientes'] = $row['total'];
        }

        // Contar conductores activos
        $result = $conn->query("SHOW TABLES LIKE 'conductores'");
        if ($result && $result->num_rows > 0) {
            $result = $conn->query("SELECT COUNT(*) as total FROM conductores WHERE deleted = 0");
            if ($result && $row = $result->fetch_assoc()) {
                $stats['conductores'] = $row['total'];
            }
        }

        // Estadísticas de vehículos
        try {
            $result = $conn->query("SHOW TABLES LIKE 'vehiculos'");
            if ($result && $result->num_rows > 0) {
                // Total de vehículos
                $result = $conn->query("SELECT COUNT(*) as total FROM vehiculos WHERE deleted = 0") or die($conn->error);
                if ($row = $result->fetch_assoc()) {
                    $stats['vehiculos'] = (int)$row['total'];
                }
                
                // Vehículos disponibles
                $result = $conn->query("
                    SELECT COUNT(*) as total 
                    FROM vehiculos 
                    WHERE estado_vehiculo = 1 
                    AND deleted = 0
                ") or die($conn->error);
                
                if ($row = $result->fetch_assoc()) {
                    $stats['vehiculos_disponibles'] = (int)$row['total'];
                }
            }
        } catch (Exception $e) {
            $error = "Error al obtener estadísticas de vehículos: " . $e->getMessage();
            error_log($error);
        }

        // Estadísticas de envíos
        try {
            $result = $conn->query("SHOW TABLES LIKE 'envios'");
            if ($result && $result->num_rows > 0) {
                // Envíos del mes actual
                $result = $conn->query("
                    SELECT COUNT(*) as total 
                    FROM envios 
                    WHERE MONTH(fecha_salida) = MONTH(CURRENT_DATE()) 
                    AND YEAR(fecha_salida) = YEAR(CURRENT_DATE())
                    AND deleted = 0
                ") or die($conn->error);
                
                if ($row = $result->fetch_assoc()) {
                    $stats['envios_mes_actual'] = (int)$row['total'];
                }

                // Ingresos del mes actual - suma de todos los movimentos de caja del mes
                $result = $conn->query("
                    SELECT COALESCE(SUM(monto), 0) as total 
                    FROM movimientos_caja 
                    WHERE MONTH(fecha_pago) = MONTH(CURRENT_DATE()) 
                    AND YEAR(fecha_pago) = YEAR(CURRENT_DATE())
                    AND deleted = 0
                ") or die($conn->error);
                
                if ($row = $result->fetch_assoc()) {
                    $stats['ingresos_mes_actual'] = number_format($row['total'], 2, '.', '');
                }

                // Envíos pendientes (estado 1 = Pendiente)
                $result = $conn->query("
                    SELECT COUNT(*) as total 
                    FROM envios 
                    WHERE id_estado_envio = 1 
                    AND deleted = 0
                ") or die($conn->error);
                
                if ($row = $result->fetch_assoc()) {
                    $stats['envios_pendientes'] = (int)$row['total'];
                }

                // Carga de vehículos (top 5)
                $result = $conn->query("
                    SELECT 
                        v.patente, 
                        v.marca,
                        v.modelo,
                        COUNT(e.id_envio) as total_envios,
                        COALESCE(SUM(e.costo_total), 0) as ingresos_totales
                    FROM vehiculos v 
                    LEFT JOIN envios e ON v.id_vehiculo = e.id_vehiculo 
                    WHERE v.deleted = 0 
                    GROUP BY v.id_vehiculo 
                    ORDER BY total_envios DESC 
                    LIMIT 5
                ") or die($conn->error);
                
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $stats['carga_vehiculos'][] = [
                            'patente' => $row['patente'],
                            'vehiculo' => $row['marca'] . ' ' . $row['modelo'],
                            'total_envios' => (int)$row['total_envios'],
                            'ingresos' => number_format($row['ingresos_totales'], 2, '.', '')
                        ];
                    }
                }

                // Top clientes (por gasto total)
                $result = $conn->query("
                    SELECT 
                        c.cliente, 
                        COUNT(e.id_envio) as total_envios,
                        COALESCE(SUM(e.costo_total), 0) as total_gastado
                    FROM clientes c
                    LEFT JOIN envios e ON c.id_cliente = e.id_cliente
                    WHERE c.deleted = 0
                    AND e.deleted = 0
                    GROUP BY c.id_cliente
                    HAVING total_gastado > 0
                    ORDER BY total_gastado DESC
                    LIMIT 5
                ") or die($conn->error);
                
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $stats['top_clientes'][] = [
                            'cliente' => $row['cliente'],
                            'total_envios' => (int)$row['total_envios'],
                            'total_gastado' => number_format($row['total_gastado'], 2, '.', '')
                        ];
                    }
                }

                // Estadísticas de envíos por mes (últimos 6 meses)
                $result = $conn->query("
                    SELECT 
                        DATE_FORMAT(fecha_salida, '%Y-%m') as mes,
                        COUNT(*) as total_envios,
                        COALESCE(SUM(costo_total), 0) as ingresos
                    FROM envios
                    WHERE fecha_salida >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                    AND deleted = 0
                    GROUP BY mes
                    ORDER BY mes ASC
                ") or die($conn->error);
                
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $stats['envios_por_mes'][] = [
                            'mes' => $row['mes'],
                            'total_envios' => (int)$row['total_envios'],
                            'ingresos' => (float)$row['ingresos']
                        ];
                    }
                }
                
                // Obtener distribución de estados de envío
                $result = $conn->query("
                    SELECT 
                        ee.estado,
                        COUNT(e.id_envio) as cantidad
                    FROM estados_envio ee
                    LEFT JOIN envios e ON ee.id_estado_envio = e.id_estado_envio
                    WHERE e.deleted = 0 OR e.id_estado_envio IS NULL
                    GROUP BY ee.id_estado_envio
                    ORDER BY cantidad DESC
                ") or die($conn->error);
                
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $stats['estados_envio'][] = [
                            'estado' => $row['estado'],
                            'cantidad' => (int)$row['cantidad']
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            $error = "Error al obtener estadísticas de envíos: " . $e->getMessage();
            error_log($error);
        }

        return $stats;
        }
    }

?>

<?php
class Factura {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function obtenerTodos() {
        $sql = "
            SELECT 
                f.*, 
                e.numero_seguimiento, 
                c.cliente,
                COALESCE(SUM(mc.monto), 0) as total_pagado
            FROM facturas f
            JOIN envios e ON f.id_envio = e.id_envio
            JOIN clientes c ON f.id_cliente = c.id_cliente
            LEFT JOIN movimientos_caja mc ON f.id_factura = mc.id_factura
            WHERE f.deleted = 0
            GROUP BY f.id_factura
            ORDER BY f.fecha_emision DESC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare("SELECT f.*, e.numero_seguimiento, c.cliente 
                                     FROM facturas f 
                                     JOIN envios e ON f.id_envio = e.id_envio 
                                     JOIN clientes c ON f.id_cliente = c.id_cliente 
                                     WHERE f.id_factura = ? AND f.deleted = 0");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $factura = $result->fetch_assoc();
        
        if ($factura) {
            // Obtener movimientos y calcular saldo pendiente
            $movimientoCajaModel = new MovimientoCaja($this->conn);
            $movimientos = $movimientoCajaModel->obtenerPorFactura($id);
            
            // Calcular saldo pendiente
            $total_pagado = 0;
            foreach ($movimientos as $mov) {
                $total_pagado += floatval($mov['monto']);
            }
            $saldo = floatval($factura['total']) - $total_pagado;
            $factura['saldo_pendiente'] = max(0, $saldo); // Asegurar que no sea negativo
            
            // Agregar los movimientos a la factura
            $factura['movimientos'] = $movimientos;
        }
        
        return $factura;
    }

    public function crear($id_envio, $numero_factura, $fecha_emision, $fecha_vencimiento, $id_cliente, $iva, $subtotal, $total, $estado) {
        $stmt = $this->conn->prepare("INSERT INTO facturas (id_envio, numero_factura, fecha_emision, fecha_vencimiento, id_cliente, iva, subtotal, total, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssiiiii", $id_envio, $numero_factura, $fecha_emision, $fecha_vencimiento, $id_cliente, $iva, $subtotal, $total, $estado);
        return $stmt->execute();
    }

    public function editar($id_factura, $id_envio, $numero_factura, $fecha_emision, $fecha_vencimiento, $id_cliente, $iva, $subtotal, $total, $estado) {
        $stmt = $this->conn->prepare("UPDATE facturas SET id_envio = ?, numero_factura = ?, fecha_emision = ?, fecha_vencimiento = ?, id_cliente = ?, iva = ?, subtotal = ?, total = ?, estado = ? WHERE id_factura = ?");
        $stmt->bind_param("isssiiiiii", $id_envio, $numero_factura, $fecha_emision, $fecha_vencimiento, $id_cliente, $iva, $subtotal, $total, $estado, $id_factura);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $stmt = $this->conn->prepare("UPDATE facturas SET deleted = 1 WHERE id_factura = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function buscar($filtros, $porPagina = 10, $pagina = 1) {
        // Primero, obtener el conteo total de registros
        $sqlCount = "
            SELECT COUNT(DISTINCT f.id_factura) as total
            FROM facturas f
            JOIN envios e ON f.id_envio = e.id_envio
            JOIN clientes c ON f.id_cliente = c.id_cliente
            LEFT JOIN movimientos_caja mc ON f.id_factura = mc.id_factura
            WHERE f.deleted = 0
        ";
        
        // Consulta principal para obtener los datos
        $sql = "
            SELECT 
                f.*, 
                e.numero_seguimiento, 
                c.cliente,
                COALESCE(SUM(mc.monto), 0) as total_pagado
            FROM facturas f
            JOIN envios e ON f.id_envio = e.id_envio
            JOIN clientes c ON f.id_cliente = c.id_cliente
            LEFT JOIN movimientos_caja mc ON f.id_factura = mc.id_factura
            WHERE f.deleted = 0
        ";
        
        $params = [];
        $types = '';
        
        // Aplicar filtros
        $conditions = [];
        
        if (!empty($filtros['numero_factura'])) {
            $conditions[] = "f.numero_factura LIKE ?";
            $params[] = '%' . $filtros['numero_factura'] . '%';
            $types .= 's';
        }
        
        if (!empty($filtros['id_cliente'])) {
            $conditions[] = "f.id_cliente = ?";
            $params[] = $filtros['id_cliente'];
            $types .= 'i';
        }
        
        if (!empty($filtros['estado'])) {
            $conditions[] = "f.estado = ?";
            $params[] = $filtros['estado'];
            $types .= 's';
        }
        
        if (!empty($filtros['fecha_desde'])) {
            $conditions[] = "f.fecha_emision >= ?";
            $params[] = $filtros['fecha_desde'] . ' 00:00:00';
            $types .= 's';
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $conditions[] = "f.fecha_emision <= ?";
            $params[] = $filtros['fecha_hasta'] . ' 23:59:59';
            $types .= 's';
        }
        
        if (!empty($conditions)) {
            $whereClause = ' AND ' . implode(' AND ', $conditions);
            $sql .= $whereClause;
            $sqlCount .= $whereClause;
        }
        
        // Obtener el total de registros
        $stmtCount = $this->conn->prepare($sqlCount);
        if (!empty($params)) {
            $stmtCount->bind_param($types, ...$params);
        }
        $stmtCount->execute();
        $totalRegistros = $stmtCount->get_result()->fetch_assoc()['total'];
        
        // Calcular la paginación
        $totalPaginas = ceil($totalRegistros / $porPagina);
        $offset = ($pagina - 1) * $porPagina;
        
        // Agregar agrupación, ordenamiento y límites a la consulta principal
        $sql .= " GROUP BY f.id_factura ORDER BY f.fecha_emision DESC LIMIT ? OFFSET ?";
        
        // Agregar los parámetros de paginación
        $params[] = $porPagina;
        $params[] = $offset;
        $types .= 'ii';
        
        // Ejecutar la consulta principal
        $stmt = $this->conn->prepare($sql);
        
        // Vincular parámetros si hay filtros
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $resultados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Si hay un filtro de estado, lo aplicamos después de calcular el estado real
        if (!empty($filtros['estado'])) {            
            // Reindexar el array después de filtrar
            $resultados = array_values($resultados);
            
            // Recalcular el total de registros después de aplicar el filtro de estado
            $totalRegistros = count($resultados);
            $totalPaginas = ceil($totalRegistros / $porPagina);
        }
        
        return [
            'datos' => $resultados,
            'paginacion' => [
                'total_registros' => $totalRegistros,
                'por_pagina' => $porPagina,
                'pagina_actual' => $pagina,
                'total_paginas' => $totalPaginas
            ]
        ];
    }
    
    public function obtenerPorFecha($fecha_inicio, $fecha_fin) {
        $stmt = $this->conn->prepare("SELECT * FROM facturas WHERE fecha_emision BETWEEN ? AND ? AND deleted = 0 ORDER BY fecha_emision DESC");
        $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

<?php
class Envio {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function obtenerTodos($limit = null, $offset = 0, $filtros = []) {
        $sql = "
            SELECT e.*, 
                   u1.direccion as origen_direccion,
                   l1.localidad as origen_localidad,
                   p1.provincia as origen_provincia,
                   pa1.pais as origen_pais,
                   u2.direccion as destino_direccion,
                   l2.localidad as destino_localidad,
                   p2.provincia as destino_provincia,
                   pa2.pais as destino_pais,
                   ee.estado as estado
            FROM envios e
            JOIN ubicaciones u1 ON e.id_origen = u1.id_ubicacion
            JOIN localidades l1 ON u1.id_localidad = l1.id_localidad
            JOIN provincias p1 ON l1.id_provincia = p1.id_provincia
            JOIN paises pa1 ON p1.id_pais = pa1.id_pais
            JOIN ubicaciones u2 ON e.id_destino = u2.id_ubicacion
            JOIN localidades l2 ON u2.id_localidad = l2.id_localidad
            JOIN provincias p2 ON l2.id_provincia = p2.id_provincia
            JOIN paises pa2 ON p2.id_pais = pa2.id_pais
            JOIN estados_envio ee ON e.id_estado_envio = ee.id_estado_envio
            WHERE e.deleted = 0 
        ";
        
        // Aplicar filtros
        $params = [];
        $types = '';
        $whereConditions = [];
        
        // Filtro por número de seguimiento
        if (!empty($filtros['numero_seguimiento'])) {
            $whereConditions[] = "e.numero_seguimiento LIKE ?";
            $params[] = '%' . $filtros['numero_seguimiento'] . '%';
            $types .= 's';
        }
        
        // Filtro por estado
        if (!empty($filtros['estado'])) {
            $whereConditions[] = "ee.estado = ?";
            $params[] = $filtros['estado'];
            $types .= 's';
        }
        
        // Filtro por fecha desde
        if (!empty($filtros['fecha_desde'])) {
            $whereConditions[] = "e.fecha_salida >= ?";
            $params[] = $filtros['fecha_desde'];
            $types .= 's';
        }
        
        // Filtro por fecha hasta
        if (!empty($filtros['fecha_hasta'])) {
            $whereConditions[] = "e.fecha_salida <= ?";
            $params[] = $filtros['fecha_hasta'] . ' 23:59:59'; // Incluir todo el día
            $types .= 's';
        }
        
        // Agregar condiciones WHERE si existen
        if (!empty($whereConditions)) {
            $sql .= " AND " . implode(" AND ", $whereConditions);
        }
        
        // Ordenar y paginar
        $sql .= " ORDER BY e.fecha_creacion_envio DESC";
        
        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $types .= 'ii';
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->conn->prepare($sql);
        
        // Vincular parámetros si existen
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $envios = $result->fetch_all(MYSQLI_ASSOC);
        
        // Crear una cadena formateada para origen y destino
        foreach ($envios as &$envio) {
            $envio['origen'] = $envio['origen_direccion'] . ', ' . 
                             $envio['origen_localidad'] . ', ' . 
                             $envio['origen_provincia'] . ', ' . 
                             $envio['origen_pais'];
            
            $envio['destino'] = $envio['destino_direccion'] . ', ' . 
                              $envio['destino_localidad'] . ', ' . 
                              $envio['destino_provincia'] . ', ' . 
                              $envio['destino_pais'];
            
            // Eliminar las columnas individuales que ya no necesitamos
            unset($envio['origen_direccion']);
            unset($envio['origen_localidad']);
            unset($envio['origen_provincia']);
            unset($envio['origen_pais']);
            unset($envio['destino_direccion']);
            unset($envio['destino_localidad']);
            unset($envio['destino_provincia']);
            unset($envio['destino_pais']);
        }
        
        return $envios;
    }

    public function contarTotal($filtros = []) {
        $sql = "SELECT COUNT(*) as total 
                FROM envios e
                JOIN estados_envio ee ON e.id_estado_envio = ee.id_estado_envio
                WHERE e.deleted = 0";
        
        $params = [];
        $types = '';
        $whereConditions = [];
        
        // Filtro por número de seguimiento
        if (!empty($filtros['numero_seguimiento'])) {
            $whereConditions[] = "e.numero_seguimiento LIKE ?";
            $params[] = '%' . $filtros['numero_seguimiento'] . '%';
            $types .= 's';
        }
        
        // Filtro por estado
        if (!empty($filtros['estado'])) {
            $whereConditions[] = "ee.estado = ?";
            $params[] = $filtros['estado'];
            $types .= 's';
        }
        
        // Filtro por fecha desde
        if (!empty($filtros['fecha_desde'])) {
            $whereConditions[] = "e.fecha_salida >= ?";
            $params[] = $filtros['fecha_desde'];
            $types .= 's';
        }
        
        // Filtro por fecha hasta
        if (!empty($filtros['fecha_hasta'])) {
            $whereConditions[] = "e.fecha_salida <= ?";
            $params[] = $filtros['fecha_hasta'] . ' 23:59:59';
            $types .= 's';
        }
        
        // Agregar condiciones WHERE si existen
        if (!empty($whereConditions)) {
            $sql .= " AND " . implode(" AND ", $whereConditions);
        }
        
        $stmt = $this->conn->prepare($sql);
        
        // Vincular parámetros si existen
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }
    
    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare("
            SELECT e.*, 
                   u1.direccion as origen_direccion,
                   l1.localidad as origen_localidad,
                   p1.provincia as origen_provincia,
                   pa1.pais as origen_pais,
                   u2.direccion as destino_direccion,
                   l2.localidad as destino_localidad,
                   p2.provincia as destino_provincia,
                   pa2.pais as destino_pais,
                   ee.estado as estado
            FROM envios e
            JOIN ubicaciones u1 ON e.id_origen = u1.id_ubicacion
            JOIN localidades l1 ON u1.id_localidad = l1.id_localidad
            JOIN provincias p1 ON l1.id_provincia = p1.id_provincia
            JOIN paises pa1 ON p1.id_pais = pa1.id_pais
            JOIN ubicaciones u2 ON e.id_destino = u2.id_ubicacion
            JOIN localidades l2 ON u2.id_localidad = l2.id_localidad
            JOIN provincias p2 ON l2.id_provincia = p2.id_provincia
            JOIN paises pa2 ON p2.id_pais = pa2.id_pais
            JOIN estados_envio ee ON e.id_estado_envio = ee.id_estado_envio
            WHERE e.id_envio = ? AND e.deleted = 0
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $envio = $result->fetch_assoc();
        
        if ($envio) {
            $envio['origen'] = $envio['origen_direccion'] . ', ' . 
                             $envio['origen_localidad'] . ', ' . 
                             $envio['origen_provincia'] . ', ' . 
                             $envio['origen_pais'];
            
            $envio['destino'] = $envio['destino_direccion'] . ', ' . 
                              $envio['destino_localidad'] . ', ' . 
                              $envio['destino_provincia'] . ', ' . 
                              $envio['destino_pais'];
            
            unset($envio['origen_direccion']);
            unset($envio['origen_localidad']);
            unset($envio['origen_provincia']);
            unset($envio['origen_pais']);
            unset($envio['destino_direccion']);
            unset($envio['destino_localidad']);
            unset($envio['destino_provincia']);
            unset($envio['destino_pais']);
        }
        
        return $envio;
    }

    public function crear($id_origen, $id_destino, $fecha_salida, $id_estado_envio, $peso_kg, $id_vehiculo, $descripcion, $costo_total, $id_conductor, $id_cliente, $id_tipo_carga, $numero_seguimiento, $volumen_m3) {
        $stmt = $this->conn->prepare("INSERT INTO envios (id_origen, id_destino, fecha_creacion_envio, fecha_salida, id_estado_envio, peso_kg, id_vehiculo, descripcion, costo_total, id_conductor, id_cliente, id_tipo_carga, numero_seguimiento, volumen_m3) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisidisdiiisd", $id_origen, $id_destino, $fecha_salida, $id_estado_envio, $peso_kg, $id_vehiculo, $descripcion, $costo_total, $id_conductor, $id_cliente, $id_tipo_carga, $numero_seguimiento, $volumen_m3);
        return $stmt->execute();
    }

    public function editar($id_envio, $id_origen, $id_destino, $fecha_salida, $id_estado_envio, $peso_kg, $id_vehiculo, $descripcion, $costo_total, $id_conductor, $id_cliente, $id_tipo_carga, $numero_seguimiento, $volumen_m3) {
        $stmt = $this->conn->prepare("UPDATE envios SET id_origen = ?, id_destino = ?, fecha_salida = ?, id_estado_envio = ?, peso_kg = ?, id_vehiculo = ?, descripcion = ?, costo_total = ?, id_conductor = ?, id_cliente = ?, id_tipo_carga = ?, numero_seguimiento = ?, volumen_m3 = ? WHERE id_envio = ?");
        $stmt->bind_param("iisidisdiiisdi", $id_origen, $id_destino, $fecha_salida, $id_estado_envio, $peso_kg, $id_vehiculo, $descripcion, $costo_total, $id_conductor, $id_cliente, $id_tipo_carga, $numero_seguimiento, $volumen_m3, $id_envio);
        return $stmt->execute();
    }

    public function eliminar($id_envio) {
        $stmt = $this->conn->prepare("UPDATE envios SET deleted = 1 WHERE id_envio = ?");
        $stmt->bind_param("i", $id_envio);
        return $stmt->execute();
    }
}

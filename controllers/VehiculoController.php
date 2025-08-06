<?php
require_once __DIR__ . '/../models/Vehiculo.php';
require_once __DIR__ . '/../config/database.php';

class VehiculoController {
    private $vehiculo;
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->vehiculo = new Vehiculo($this->db->getConnection());
    }

    public function index() {
        $vehiculos = $this->vehiculo->obtenerTodos();
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/vehiculos/index.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function create() {
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/vehiculos/form.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $patente = $_POST['patente'] ?? '';
            $marca = $_POST['marca'] ?? '';
            $modelo = $_POST['modelo'] ?? '';
            $capacidad_kg = $_POST['capacidad_kg'] ?? '';
            $ultima_inspeccion = $_POST['ultima_inspeccion'] ?? '';
            $estado_vehiculo = $_POST['estado_vehiculo'] ?? '';
            $rto_vencimiento = $_POST['rto_vencimiento'] ?? '';

            if (!empty($patente) && !empty($marca) && !empty($modelo) && !empty($capacidad_kg) && !empty($ultima_inspeccion) && !empty($estado_vehiculo) && !empty($rto_vencimiento)) {
                if ($this->vehiculo->crear($patente, $marca, $modelo, $capacidad_kg, $ultima_inspeccion, $estado_vehiculo, $rto_vencimiento)) {
                    header('Location: ?route=vehiculos&success=Vehículo creado exitosamente');
                } else {
                    header('Location: ?route=vehiculos_create&error=Error al crear el vehículo');
                }
            } else {
                header('Location: ?route=vehiculos_create&error=Todos los campos son requeridos');
            }
        }
        exit();
    }

    public function edit() {
        $id = $_GET['id_vehiculo'] ?? null;
        if ($id) {
            $vehiculo = $this->vehiculo->obtenerPorId($id);
            if ($vehiculo) {
                include __DIR__ . '/../views/layouts/header.php';
                include __DIR__ . '/../views/vehiculos/form.php';
                include __DIR__ . '/../views/layouts/footer.php';
            } else {
                header('Location: ?route=vehiculos&error=Vehículo no encontrado');
            }
        } else {
            header('Location: ?route=vehiculos&error=ID de vehículo no válido');
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_vehiculo'] ?? null;
            $patente = $_POST['patente'] ?? '';
            $marca = $_POST['marca'] ?? '';
            $modelo = $_POST['modelo'] ?? '';
            $capacidad_kg = $_POST['capacidad_kg'] ?? '';
            $ultima_inspeccion = $_POST['ultima_inspeccion'] ?? '';
            $estado_vehiculo = $_POST['estado_vehiculo'] ?? '';
            $rto_vencimiento = $_POST['rto_vencimiento'] ?? '';

            if ($id && !empty($patente) && !empty($marca) && !empty($modelo) && !empty($capacidad_kg) && !empty($ultima_inspeccion) && !empty($estado_vehiculo) && !empty($rto_vencimiento)) {
                if ($this->vehiculo->editar($id, $patente, $marca, $modelo, $capacidad_kg, $ultima_inspeccion, $estado_vehiculo, $rto_vencimiento)) {
                    header('Location: ?route=vehiculos&success=Vehículo actualizado exitosamente');
                } else {
                    header('Location: ?route=vehiculos_edit&id_vehiculo=' . $id . '&error=Error al actualizar el vehículo');
                }
            } else {
                header('Location: ?route=vehiculos_edit&id_vehiculo=' . $id . '&error=Todos los campos son requeridos');
            }
        }
        exit();
    }

    public function delete() {
        $id = $_GET['id_vehiculo'] ?? null;
        if ($id) {
            if ($this->vehiculo->eliminar($id)) {
                header('Location: ?route=vehiculos&success=Vehículo eliminado exitosamente');
            } else {
                header('Location: ?route=vehiculos&error=Error al eliminar el vehículo');
            }
        } else {
            header('Location: ?route=vehiculos&error=ID de vehículo no válido');
        }
        exit();
    }

    public function cambiarEstado() {
        $id = $_GET['id_vehiculo'] ?? null;
        $estado = $_GET['estado'] ?? null;
        
        if ($id && $estado) {
            if ($this->vehiculo->cambiarEstado($id, $estado)) {
                header('Location: ?route=vehiculos&success=Estado del vehículo actualizado exitosamente');
            } else {
                header('Location: ?route=vehiculos&error=Error al cambiar el estado del vehículo');
            }
        } else {
            header('Location: ?route=vehiculos&error=Datos inválidos para cambiar estado');
        }
        exit();
    }
}

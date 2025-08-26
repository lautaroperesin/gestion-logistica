<?php
require_once __DIR__ . '/../models/Conductor.php';
require_once __DIR__ . '/../config/database.php';

class ConductorController {
    private $conductor;
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->conductor = new Conductor($this->db->getConnection());
    }

    public function index() {
        $busqueda = $_GET['buscar'] ?? '';
        
        if (!empty($busqueda)) {
            $conductores = $this->conductor->buscarPorNombre($busqueda);
        } else {
            $conductores = $this->conductor->obtenerTodos();
        }
        
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/conductores/index.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function create() {
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/conductores/form.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['conductor'] ?? '';
            $dni = $_POST['dni'] ?? '';
            $claseLicencia = $_POST['clase_licencia'] ?? '';
            $vencimientoLicencia = $_POST['vencimiento_licencia'] ?? '';
            $email = $_POST['email'] ?? '';
            $telefono = $_POST['telefono'] ?? '';

            if (!empty($nombre) && !empty($email)) {
                if ($this->conductor->crear($nombre, $email, $telefono, $dni, $claseLicencia, $vencimientoLicencia)) {
                    header('Location: ?route=conductores&success=Conductor creado exitosamente');
                } else {
                    header('Location: ?route=conductores_create&error=Error al crear el conductor');
                }
            } else {
                header('Location: ?route=conductores_create&error=Todos los campos son requeridos');
            }
        }
        exit();
    }

    public function edit() {
        $id = $_GET['id_conductor'] ?? null;
        if ($id) {
            $conductor = $this->conductor->obtenerPorId($id);
            if ($conductor) {
                include __DIR__ . '/../views/layouts/header.php';
                include __DIR__ . '/../views/conductores/form.php';
                include __DIR__ . '/../views/layouts/footer.php';
            } else {
                header('Location: ?route=conductores&error=Conductor no encontrado');
                exit();
            }
        } else {
            header('Location: ?route=conductores&error=ID de conductor no válido');
            exit();
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_conductor'] ?? null;
            $nombre = $_POST['conductor'] ?? '';
            $email = $_POST['email'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $dni = $_POST['dni'] ?? '';
            $claseLicencia = $_POST['clase_licencia'] ?? '';
            $vencimientoLicencia = $_POST['vencimiento_licencia'] ?? '';

            if ($id && !empty($nombre) && !empty($email)) {
                if ($this->conductor->editar($id, $nombre, $email, $telefono, $dni, $claseLicencia, $vencimientoLicencia)) {
                    header('Location: ?route=conductores&success=Conductor actualizado exitosamente');
                } else {
                    header('Location: ?route=conductores_edit&id_conductor=' . $id . '&error=Error al actualizar el conductor');
                }
            } else {
                header('Location: ?route=conductores_edit&id_conductor=' . $id . '&error=Todos los campos son requeridos');
            }
        }
        exit();
    }

    public function delete() {
        $id = $_GET['id_conductor'] ?? null;
        if ($id) {
            if ($this->conductor->eliminar($id)) {
                header('Location: ?route=conductores&success=Conductor eliminado exitosamente');
            } else {
                header('Location: ?route=conductores&error=Error al eliminar el conductor');
            }
        } else {
            header('Location: ?route=conductores&error=ID de conductor no válido');
        }
        exit();
    }
}
?>
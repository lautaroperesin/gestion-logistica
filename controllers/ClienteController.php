<?php
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../config/database.php';

class ClienteController {
    private $cliente;
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->cliente = new Cliente($this->db->getConnection());
    }

    public function index() {
        $busqueda = $_GET['buscar'] ?? '';
        
        if (!empty($busqueda)) {
            $clientes = $this->cliente->buscarPorNombre($busqueda);
        } else {
            $clientes = $this->cliente->obtenerTodos();
        }
        
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/clientes/index.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function create() {
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/clientes/form.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['cliente'] ?? '';
            $email = $_POST['email'] ?? '';
            $telefono = $_POST['telefono'] ?? '';

            if (!empty($nombre) && !empty($email)) {
                if ($this->cliente->crear($nombre, $email, $telefono)) {
                    header('Location: ?route=clientes&success=Cliente creado exitosamente');
                } else {
                    header('Location: ?route=clientes_create&error=Error al crear el cliente');
                }
            } else {
                header('Location: ?route=clientes_create&error=Todos los campos son requeridos');
            }
        }
        exit();
    }

    public function edit() {
        $id = $_GET['id_cliente'] ?? null;
        if ($id) {
            $cliente = $this->cliente->obtenerPorId($id);
            if ($cliente) {
                include __DIR__ . '/../views/layouts/header.php';
                include __DIR__ . '/../views/clientes/form.php';
                include __DIR__ . '/../views/layouts/footer.php';
            } else {
                header('Location: ?route=clientes&error=Cliente no encontrado');
                exit();
            }
        } else {
            header('Location: ?route=clientes&error=ID de cliente no válido');
            exit();
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_cliente'] ?? null;
            $nombre = $_POST['cliente'] ?? '';
            $email = $_POST['email'] ?? '';
            $telefono = $_POST['telefono'] ?? '';

            if ($id && !empty($nombre) && !empty($email)) {
                if ($this->cliente->editar($id, $nombre, $email, $telefono)) {
                    header('Location: ?route=clientes&success=Cliente actualizado exitosamente');
                } else {
                    header('Location: ?route=clientes_edit&id_cliente=' . $id . '&error=Error al actualizar el cliente');
                }
            } else {
                header('Location: ?route=clientes_edit&id_cliente=' . $id . '&error=Todos los campos son requeridos');
            }
        }
        exit();
    }

    public function delete() {
        $id = $_GET['id_cliente'] ?? null;
        if ($id) {
            if ($this->cliente->eliminar($id)) {
                header('Location: ?route=clientes&success=Cliente eliminado exitosamente');
            } else {
                header('Location: ?route=clientes&error=Error al eliminar el cliente');
            }
        } else {
            header('Location: ?route=clientes&error=ID de cliente no válido');
        }
        exit();
    }
}
?>
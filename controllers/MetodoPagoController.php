<?php
require_once __DIR__ . '/../models/MetodoPago.php';
require_once __DIR__ . '/../config/database.php';

class MetodoPagoController {
    private $metodoPagoModel;
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->metodoPagoModel = new MetodoPago($this->db->getConnection());
    }

    public function index() {
        $metodos_pago = $this->metodoPagoModel->obtenerTodos();
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/metodos_pago/index.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function create() {
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/metodos_pago/form.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $metodo_pago = $_POST['metodo_pago'] ?? '';
            if ($this->metodoPagoModel->crear($metodo_pago)) {
                header('Location: ?route=metodos_pago');
                exit;
            }
        }
    }

    public function edit() {
        $id = $_GET['id_metodo_pago'] ?? null;
        if ($id) {
            $metodo_pago = $this->metodoPagoModel->obtenerPorId($id);
            if ($metodo_pago) {
                include __DIR__ . '/../views/layouts/header.php';
                include __DIR__ . '/../views/metodos_pago/form.php';
                include __DIR__ . '/../views/layouts/footer.php';
            } else {
                header('Location: ?route=metodos_pago&error=Método de pago no encontrado');
                exit;
            }
        } else {
            header('Location: ?route=metodos_pago&error=ID no válido');
            exit;
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_metodo_pago'] ?? null;
            $metodo_pago = $_POST['metodo_pago'] ?? '';
            if ($this->metodoPagoModel->editar($id, $metodo_pago)) {
                header('Location: ?route=metodos_pago');
                exit;
            }
        }
    }

    public function delete() {
        $id = $_GET['id_metodo_pago'] ?? null;
        if ($this->metodoPagoModel->eliminar($id)) {
            header('Location: ?route=metodos_pago');
            exit;
        }
    }
}

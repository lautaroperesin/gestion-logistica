<?php
require_once __DIR__ . '/../models/Factura.php';
require_once __DIR__ . '/../config/database.php';

class FacturaController {
    private $facturaModel;
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->facturaModel = new Factura($this->db->getConnection());
    }

    public function index() {
        $facturas = $this->facturaModel->obtenerTodos();
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/facturas/index.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function create() {
        $envios = (new Envio($this->db->getConnection()))->obtenerTodos();
        $clientes = (new Cliente($this->db->getConnection()))->obtenerTodos();
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/facturas/form.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_envio = $_POST['id_envio'] ?? null;
            $numero_factura = $_POST['numero_factura'] ?? '';
            $fecha_emision = $_POST['fecha_emision'] ?? date('Y-m-d H:i:s');
            $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? null;
            $id_cliente = $_POST['id_cliente'] ?? null;
            $iva = $_POST['iva'] ?? 0;
            $subtotal = $_POST['subtotal'] ?? 0;
            $total = $_POST['total'] ?? 0;
            $estado = $_POST['estado'] ?? 1;

            if ($this->facturaModel->crear($id_envio, $numero_factura, $fecha_emision, $fecha_vencimiento, $id_cliente, $iva, $subtotal, $total, $estado)) {
                header('Location: ?route=facturas');
                exit;
            }
        }
    }

    public function edit() {
        $id = $_GET['id_factura'] ?? null;
        if ($id) {
            $factura = $this->facturaModel->obtenerPorId($id);
            if ($factura) {
                $envios = (new Envio($this->db->getConnection()))->obtenerTodos();
                $clientes = (new Cliente($this->db->getConnection()))->obtenerTodos();
                include __DIR__ . '/../views/layouts/header.php';
                include __DIR__ . '/../views/facturas/form.php';
                include __DIR__ . '/../views/layouts/footer.php';
            } else {
                header('Location: ?route=facturas&error=Factura no encontrada');
                exit;
            }
        } else {
            header('Location: ?route=facturas&error=ID de factura no válido');
            exit;
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_factura = $_POST['id_factura'] ?? null;
            $id_envio = $_POST['id_envio'] ?? null;
            $numero_factura = $_POST['numero_factura'] ?? '';
            $fecha_emision = $_POST['fecha_emision'] ?? date('Y-m-d H:i:s');
            $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? null;
            $id_cliente = $_POST['id_cliente'] ?? null;
            $iva = $_POST['iva'] ?? 0;
            $subtotal = $_POST['subtotal'] ?? 0;
            $total = $_POST['total'] ?? 0;
            $estado = $_POST['estado'] ?? 1;

            if ($this->facturaModel->editar($id_factura, $id_envio, $numero_factura, $fecha_emision, $fecha_vencimiento, $id_cliente, $iva, $subtotal, $total, $estado)) {
                header('Location: ?route=facturas');
                exit;
            }
        }
    }

    public function delete() {
        $id = $_GET['id_factura'] ?? null;
        if ($this->facturaModel->eliminar($id)) {
            header('Location: ?route=facturas');
            exit;
        }
    }

    public function getByEnvio($id_envio) {
        $facturas = $this->facturaModel->obtenerPorEnvio($id_envio);
        include __DIR__ . '/../views/facturas/index.php';
    }

    public function getByCliente($id_cliente) {
        $facturas = $this->facturaModel->obtenerPorCliente($id_cliente);
        include __DIR__ . '/../views/facturas/index.php';
    }

    public function getByEstado($estado) {
        $facturas = $this->facturaModel->obtenerPorEstado($estado);
        include __DIR__ . '/../views/facturas/index.php';
    }
}

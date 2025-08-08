<?php
require_once __DIR__ . '/../models/MovimientoCaja.php';
require_once __DIR__ . '/../config/database.php';

class MovimientoCajaController {
    private $movimientoCajaModel;
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->movimientoCajaModel = new MovimientoCaja($this->db->getConnection());
    }

    public function index() {
        $movimientos = $this->movimientoCajaModel->obtenerTodos();
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/movimientos_caja/index.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function create() {
        $facturas = (new Factura($this->db->getConnection()))->obtenerTodos();
        $metodos_pago = (new MetodoPago($this->db->getConnection()))->obtenerTodos();
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/movimientos_caja/form.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_factura = $_POST['id_factura'] ?? null;
            $id_metodo_pago = $_POST['id_metodo_pago'] ?? null;
            $fecha_pago = $_POST['fecha_pago'] ?? date('Y-m-d H:i:s');
            $monto = $_POST['monto'] ?? 0;
            $observaciones = $_POST['observaciones'] ?? '';

            if ($this->movimientoCajaModel->crear($id_factura, $id_metodo_pago, $fecha_pago, $monto, $observaciones)) {
                header('Location: ?route=facturas');
                exit;
            }
        }
    }

    public function edit() {
        $id = $_GET['id_movimiento'] ?? null;
        if ($id) {
            $movimiento = $this->movimientoCajaModel->obtenerPorId($id);
            if ($movimiento) {
                $facturas = (new Factura($this->db->getConnection()))->obtenerTodos();
                $metodos_pago = (new MetodoPago($this->db->getConnection()))->obtenerTodos();
                include __DIR__ . '/../views/layouts/header.php';
                include __DIR__ . '/../views/movimientos_caja/form.php';
                include __DIR__ . '/../views/layouts/footer.php';
            } else {
                header('Location: ?route=movimientos_caja&error=Movimiento no encontrado');
                exit;
            }
        } else {
            header('Location: ?route=movimientos_caja&error=ID no válido');
            exit;
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_movimiento = $_POST['id_movimiento'] ?? null;
            $id_factura = $_POST['id_factura'] ?? null;
            $id_metodo_pago = $_POST['id_metodo_pago'] ?? null;
            $fecha_pago = $_POST['fecha_pago'] ?? date('Y-m-d H:i:s');
            $monto = $_POST['monto'] ?? 0;
            $observaciones = $_POST['observaciones'] ?? '';

            if ($this->movimientoCajaModel->editar($id_movimiento, $id_factura, $id_metodo_pago, $fecha_pago, $monto, $observaciones)) {
                header('Location: ?route=movimientos_caja');
                exit;
            }
        }
    }

    public function delete() {
        $id = $_GET['id_movimiento'] ?? null;
        if ($this->movimientoCajaModel->eliminar($id)) {
            header('Location: ?route=movimientos_caja');
            exit;
        }
    }

    public function getByFactura($id_factura) {
        $movimientos = $this->movimientoCajaModel->obtenerPorFactura($id_factura);
        include __DIR__ . '/../views/movimientos_caja/index.php';
    }
}

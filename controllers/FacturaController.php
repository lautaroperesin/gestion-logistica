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
        // Obtener parámetros de filtrado
        $filtros = [
            'numero_factura' => $_GET['numero_factura'] ?? '',
            'id_cliente' => $_GET['id_cliente'] ?? '',
            'estado' => $_GET['estado'] ?? '',
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? ''
        ];
        
        // Configuración de paginación
        $porPagina = 10; // Fijamos 10 elementos por página
        $pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        // Obtener datos con paginación
        $resultado = $this->facturaModel->buscar($filtros, $porPagina, $pagina);
        
        // Extraer datos y paginación
        $facturas = $resultado['datos'];
        $paginacion = $resultado['paginacion'];
        
        // Obtener lista de clientes para el filtro
        $clientes = (new Cliente($this->db->getConnection()))->obtenerTodos();
        
        // Pasar datos a la vista
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
        // Obtener el ID de la factura de GET o POST
        $id = $_POST['id_factura'] ?? $_GET['id_factura'] ?? null;
        
        if ($id && $this->facturaModel->eliminar($id)) {
            // Redirigir con mensaje de éxito
            header('Location: ?route=facturas&success=Factura eliminada correctamente');
            exit;
        } else {
            // Redirigir con mensaje de error
            header('Location: ?route=facturas&error=No se pudo eliminar la factura');
            exit;
        }
    }

    public function pago() {
        $id_factura = $_GET['id_factura'] ?? null;
        if (!$id_factura) {
            header('Location: ?route=facturas');
            exit;
        }

        $factura = $this->facturaModel->obtenerPorId($id_factura);
        if (!$factura) {
            header('Location: ?route=facturas');
            exit;
        }

        require __DIR__ . '/../views/facturas/pago.php';
    }
}

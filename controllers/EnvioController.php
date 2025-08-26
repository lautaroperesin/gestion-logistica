<?php
require_once __DIR__ . '/../models/Envio.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Conductor.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/TipoCarga.php';
require_once __DIR__ . '/../models/Vehiculo.php';
require_once __DIR__ . '/../models/Ubicacion.php';
require_once __DIR__ . '/../models/EstadoEnvio.php';

class EnvioController {
    private $envioModel;
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->envioModel = new Envio($this->db->getConnection());
    }

    public function index() {
        // Configuración de paginación
        $itemsPorPagina = 10; // Número de elementos por página
        $paginaActual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $paginaActual = max(1, $paginaActual); // Asegurar que la página sea al menos 1
        
        // Obtener parámetros de búsqueda
        $filtros = [
            'numero_seguimiento' => $_GET['numero_seguimiento'] ?? '',
            'estado' => $_GET['estado'] ?? '',
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? ''
        ];
        
        // Calcular el offset
        $offset = ($paginaActual - 1) * $itemsPorPagina;
        
        // Obtener los envíos para la página actual con los filtros aplicados
        $envios = $this->envioModel->obtenerTodos($itemsPorPagina, $offset, $filtros);
        
        // Obtener el total de envíos para la paginación (con los mismos filtros)
        $totalEnvios = $this->envioModel->contarTotal($filtros);
        $totalPaginas = ceil($totalEnvios / $itemsPorPagina);
        
        // Pasar los datos a la vista
        $currentPage = $paginaActual;
        $totalPages = $totalPaginas;
        
        include __DIR__ . '/../views/envios/index.php';
    }

    public function create() {
        $conductorModel = new Conductor($this->db->getConnection());
        $clienteModel = new Cliente($this->db->getConnection());
        $tipoCargaModel = new TipoCarga($this->db->getConnection());
        $vehiculoModel = new Vehiculo($this->db->getConnection());
        $ubicacionModel = new Ubicacion($this->db->getConnection());
        $estadoEnvioModel = new EstadoEnvio($this->db->getConnection());

        $conductores = $conductorModel->obtenerTodos();
        $clientes = $clienteModel->obtenerTodos();
        $tiposCarga = $tipoCargaModel->obtenerTodos();
        $vehiculos = $vehiculoModel->obtenerTodos();
        $ubicaciones = $ubicacionModel->obtenerTodos();
        $estados = $estadoEnvioModel->obtenerTodos();

        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/envios/form.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_origen = $_POST['id_origen'] ?? null;
            $id_destino = $_POST['id_destino'] ?? null;
            $fecha_salida = $_POST['fecha_salida'] ?? null;
            $id_estado_envio = $_POST['id_estado_envio'] ?? null;
            $peso_kg = $_POST['peso_kg'] ?? null;
            $id_vehiculo = $_POST['id_vehiculo'] ?? null;
            $descripcion = $_POST['descripcion'] ?? '';
            $costo_total = $_POST['costo_total'] ?? null;
            $id_conductor = $_POST['id_conductor'] ?? null;
            $id_cliente = $_POST['id_cliente'] ?? null;
            $id_tipo_carga = $_POST['id_tipo_carga'] ?? null;
            $numero_seguimiento = $_POST['numero_seguimiento'] ?? '';
            $volumen_m3 = $_POST['volumen_m3'] ?? null;

            if ($this->envioModel->crear($id_origen, $id_destino, $fecha_salida, $id_estado_envio, $peso_kg, $id_vehiculo, $descripcion, $costo_total, $id_conductor, $id_cliente, $id_tipo_carga, $numero_seguimiento, $volumen_m3)) {
                header('Location: ?route=envios');
                exit;
            }
        }
    }

    public function edit() {
        $id = $_GET['id_envio'] ?? null;
        if ($id) {
            $envio = $this->envioModel->obtenerPorId($id);
            if ($envio) {
                $conductorModel = new Conductor($this->db->getConnection());
                $clienteModel = new Cliente($this->db->getConnection());
                $tipoCargaModel = new TipoCarga($this->db->getConnection());
                $vehiculoModel = new Vehiculo($this->db->getConnection());
                $ubicacionModel = new Ubicacion($this->db->getConnection());
                $estadoEnvioModel = new EstadoEnvio($this->db->getConnection());

                $conductores = $conductorModel->obtenerTodos();
                $clientes = $clienteModel->obtenerTodos();
                $tiposCarga = $tipoCargaModel->obtenerTodos();
                $vehiculos = $vehiculoModel->obtenerTodos();
                $ubicaciones = $ubicacionModel->obtenerTodos();
                $estados = $estadoEnvioModel->obtenerTodos();

                include __DIR__ . '/../views/layouts/header.php';
                include __DIR__ . '/../views/envios/form.php';
                include __DIR__ . '/../views/layouts/footer.php';
            } else {
                header('Location: ?route=envios&error=Envio no encontrado');
                exit;
            }
        } else {
            header('Location: ?route=envios&error=ID de envio no válido');
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_envio = $_POST['id_envio'] ?? null;
            if (!$id_envio) {
                header('Location: ?route=envios&error=ID de envío no encontrado');
                exit;
            }

            $id_origen = $_POST['id_origen'] ?? null;
            $id_destino = $_POST['id_destino'] ?? null;
            $fecha_salida = $_POST['fecha_salida'] ?? null;
            $id_estado_envio = $_POST['id_estado_envio'] ?? null;
            $peso_kg = $_POST['peso_kg'] ?? null;
            $id_vehiculo = $_POST['id_vehiculo'] ?? null;
            $descripcion = $_POST['descripcion'] ?? '';
            $costo_total = $_POST['costo_total'] ?? null;
            $id_conductor = $_POST['id_conductor'] ?? null;
            $id_cliente = $_POST['id_cliente'] ?? null;
            $id_tipo_carga = $_POST['id_tipo_carga'] ?? null;
            $numero_seguimiento = $_POST['numero_seguimiento'] ?? '';
            $volumen_m3 = $_POST['volumen_m3'] ?? null;

            if ($this->envioModel->editar($id_envio, $id_origen, $id_destino, $fecha_salida, $id_estado_envio, $peso_kg, $id_vehiculo, $descripcion, $costo_total, $id_conductor, $id_cliente, $id_tipo_carga, $numero_seguimiento, $volumen_m3)) {
                header('Location: ?route=envios');
                exit;
            } else {
                header('Location: ?route=envios&error=Error al actualizar el envío');
                exit;
            }
        } else {
            header('Location: ?route=envios');
            exit;
        }
    }

    public function delete() {
        $id_envio = $_POST['id_envio'] ?? null;
        if ($this->envioModel->eliminar($id_envio)) {
            header('Location: ?route=envios');
            exit;
        }
    }
}

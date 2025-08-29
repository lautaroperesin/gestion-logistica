<?php
require_once __DIR__ . '/../models/Ubicacion.php';
require_once __DIR__ . '/../config/database.php';

class UbicacionController {
    private $ubicacionModel;
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->ubicacionModel = new Ubicacion($this->db->getConnection());
    }

    public function index() {
        $porPagina = 10;
        $pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        // Obtener el total de ubicaciones para la paginación
        $totalUbicaciones = $this->ubicacionModel->contarTotal();
        $totalPaginas = ceil($totalUbicaciones / $porPagina);
        
        // Asegurarse de que la página actual esté dentro del rango válido
        $pagina = max(1, min($pagina, $totalPaginas));
        
        // Obtener las ubicaciones para la página actual
        $ubicaciones = $this->ubicacionModel->obtenerTodos($porPagina, $pagina);
        
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/ubicaciones/index.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function create() {
        $paises = $this->ubicacionModel->obtenerPaises();
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/ubicaciones/form.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $direccion = $_POST['direccion'] ?? '';
            $id_localidad = $_POST['id_localidad'] ?? null;
            $descripcion = $_POST['descripcion'] ?? '';

            if ($this->ubicacionModel->crear($direccion, $id_localidad, $descripcion)) {
                header('Location: ?route=ubicaciones');
                exit;
            }
        }
    }

    public function edit() {
        $id = $_GET['id_ubicacion'] ?? null;
        if ($id) {
            $ubicacion = $this->ubicacionModel->obtenerPorId($id);
            if ($ubicacion) {
                $paises = $this->ubicacionModel->obtenerPaises();
                include __DIR__ . '/../views/layouts/header.php';
                include __DIR__ . '/../views/ubicaciones/form.php';
                include __DIR__ . '/../views/layouts/footer.php';
            } else {
                header('Location: ?route=ubicaciones&error=Ubicación no encontrada');
                exit;
            }
        } else {
            header('Location: ?route=ubicaciones&error=ID de ubicación no válido');
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_ubicacion = $_POST['id_ubicacion'] ?? null;
            $direccion = $_POST['direccion'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';

            if ($this->ubicacionModel->editar($id_ubicacion, $direccion, $descripcion)) {
                header('Location: ?route=ubicaciones');
                exit;
            }
        }
    }

    public function delete() {
        $id = $_GET['id_ubicacion'] ?? null;
        if ($this->ubicacionModel->eliminar($id)) {
            header('Location: ?route=ubicaciones');
            exit;
        }
    }

    public function getByLocalidad($id_localidad) {
        $ubicaciones = $this->ubicacionModel->obtenerPorLocalidad($id_localidad);
        include __DIR__ . '/../views/ubicaciones/index.php';
    }

    public function getByProvincia($id_provincia) {
        $ubicaciones = $this->ubicacionModel->obtenerPorProvincia($id_provincia);
        include __DIR__ . '/../views/ubicaciones/index.php';
    }

    public function getByPais($id_pais) {
        $ubicaciones = $this->ubicacionModel->obtenerPorPais($id_pais);
        include __DIR__ . '/../views/ubicaciones/index.php';
    }

    public function getProvinciasByPais() {
        $id_pais = $_GET['id_pais'] ?? null;
        if ($id_pais) {
            try {
                $provincias = $this->ubicacionModel->obtenerProvinciasPorPais($id_pais);
                if ($provincias) {
                    echo json_encode($provincias);
                } else {
                    echo json_encode([]); // Retorna un array vacío si no hay provincias
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Error al obtener provincias']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID de país no proporcionado']);
        }
        exit;
    }

    public function getLocalidadesByProvincia() {
        $id_provincia = $_GET['id_provincia'] ?? null;
        if ($id_provincia) {
            try {
                $localidades = $this->ubicacionModel->obtenerLocalidadesPorProvincia($id_provincia);
                if ($localidades) {
                    echo json_encode($localidades);
                } else {
                    echo json_encode([]); // Retorna un array vacío si no hay localidades
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Error al obtener localidades']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID de provincia no proporcionado']);
        }
        exit;
    }
}

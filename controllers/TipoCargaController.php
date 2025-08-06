<?php
require_once __DIR__ . '/../models/TipoCarga.php';
require_once __DIR__ . '/../config/database.php';

class TipoCargaController {
    private $tipoCarga;
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->tipoCarga = new TipoCarga($this->db->getConnection());
    }

    public function index() {
        $tipos_carga = $this->tipoCarga->obtenerTodos();
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/tipos-carga/index.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function create() {
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/tipos-carga/form.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $carga = $_POST['carga'] ?? '';

            if (!empty($carga)) {
                if ($this->tipoCarga->crear($carga)) {
                    header('Location: ?route=tipos_carga&success=Tipo de carga creado exitosamente');
                } else {
                    header('Location: ?route=tipos_carga_create&error=Error al crear el tipo de carga');
                }
            } else {
                header('Location: ?route=tipos_carga_create&error=El nombre del tipo de carga es requerido');
            }
        }
        exit();
    }

    public function edit() {
        $id = $_GET['id_tipo_carga'] ?? null;
        if ($id) {
            $tipo_carga = $this->tipoCarga->obtenerPorId($id);
            if ($tipo_carga) {
                include __DIR__ . '/../views/layouts/header.php';
                include __DIR__ . '/../views/tipos-carga/form.php';
                include __DIR__ . '/../views/layouts/footer.php';
            } else {
                header('Location: ?route=tipos_carga&error=Tipo de carga no encontrado');
            }
        } else {
            header('Location: ?route=tipos_carga&error=ID de tipo de carga no válido');
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_tipo_carga'] ?? null;
            $carga = $_POST['carga'] ?? '';

            if ($id && !empty($carga)) {
                if ($this->tipoCarga->editar($id, $carga)) {
                    header('Location: ?route=tipos_carga&success=Tipo de carga actualizado exitosamente');
                } else {
                    header('Location: ?route=tipos_carga_edit&id_tipo_carga=' . $id . '&error=Error al actualizar el tipo de carga');
                }
            } else {
                header('Location: ?route=tipos_carga_edit&id_tipo_carga=' . $id . '&error=El nombre del tipo de carga es requerido');
            }
        }
        exit();
    }

    public function delete() {
        $id = $_GET['id_tipo_carga'] ?? null;
        if ($id) {
            if ($this->tipoCarga->eliminar($id)) {
                header('Location: ?route=tipos_carga&success=Tipo de carga eliminado exitosamente');
            } else {
                header('Location: ?route=tipos_carga&error=Error al eliminar el tipo de carga');
            }
        } else {
            header('Location: ?route=tipos_carga&error=ID de tipo de carga no válido');
        }
        exit();
    }
}
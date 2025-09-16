<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/EnvioController.php';
include __DIR__ . '/../../views/layouts/header.php';

$db = new Database();
$conn = $db->getConnection();
$envioController = new EnvioController();

// Obtener configuración actual
$config_query = "SELECT * FROM config LIMIT 1";
$config_result = $conn->query($config_query);
$config = $config_result->fetch_assoc();

// Obtener lista de estados de envío
$estados = $envioController->obtenerEstadosEnvio();

// Procesar formulario de configuración
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['guardar_configuracion'])) {
        $nombre_empresa = $_POST['nombre_empresa'] ?? '';
        $telefono_empresa = $_POST['telefono_empresa'] ?? '';
        $email_empresa = $_POST['email_empresa'] ?? '';
        $ubicacion = $_POST['ubicacion_empresa'] ?? 1;
        
        $update_sql = "UPDATE config SET 
                      nombre_empresa = ?,
                      telefono_empresa = ?,
                      email_empresa = ?,
                      ubicacion_empresa = ?
                      WHERE id = 1";
        
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssss", $nombre_empresa, $telefono_empresa, $email_empresa, $ubicacion);
        $stmt->execute();
        
        header("Location: ?route=configuracion");
        exit();
    }
    
    // Procesar nuevo estado
    if (isset($_POST['agregar_estado'])) {
        $nuevo_estado = trim($_POST['nuevo_estado'] ?? '');
        if (!empty($nuevo_estado)) {
            $resultado = $envioController->agregarEstadoEnvio($nuevo_estado);
            exit();
        }
    }
    
    // Procesar eliminación de estado
    if (isset($_POST['eliminar_estado'])) {
        $id_estado = (int)($_POST['id_estado'] ?? 0);
        if ($id_estado > 0) {
            $resultado = $envioController->eliminarEstadoEnvio($id_estado);
            exit();
        }
    }
}
?>

<div class="container mt-4">
    <h2>Configuración del Sistema</h2>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            switch($_GET['success']) {
                case 1: echo "Configuración guardada correctamente."; break;
                case 2: echo "Estado agregado correctamente."; break;
                case 3: echo "Estado eliminado correctamente."; break;
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <ul class="nav nav-tabs" id="configTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="empresa-tab" data-bs-toggle="tab" data-bs-target="#empresa" type="button" role="tab">
                Datos de la Empresa
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="estados-tab" data-bs-toggle="tab" data-bs-target="#estados" type="button" role="tab">
                Estados de Envío
            </button>
        </li>
    </ul>

    <div class="tab-content p-3 border border-top-0 rounded-bottom" id="configTabsContent">
        <!-- Pestaña de Datos de la Empresa -->
        <div class="tab-pane fade show active" id="empresa" role="tabpanel">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nombre_empresa" class="form-label">Nombre de la Empresa</label>
                    <input type="text" class="form-control" id="nombre_empresa" name="nombre_empresa" 
                           value="<?= htmlspecialchars($config['nombre_empresa'] ?? '') ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="telefono_empresa" class="form-label">Teléfono</label>
                    <input type="tel" class="form-control" id="telefono_empresa" name="telefono_empresa" 
                           value="<?= htmlspecialchars($config['telefono_empresa'] ?? '') ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="email_empresa" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email_empresa" name="email_empresa" 
                           value="<?= htmlspecialchars($config['email_empresa'] ?? '') ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="ubicacion_empresa" class="form-label">Ubicación</label>
                    <input type="text" class="form-control" id="ubicacion_empresa" name="ubicacion_empresa" 
                           value="<?= htmlspecialchars($config['ubicacion_empresa'] ?? '') ?>" required>
                </div>
                
                <button type="submit" name="guardar_configuracion" class="btn btn-primary">Guardar Cambios</button>
            </form>
        </div>

        <!-- Pestaña de Estados de Envío -->
        <div class="tab-pane fade" id="estados" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <h4>Agregar Nuevo Estado</h4>
                    <form method="POST" action="" class="mb-4">
                        <div class="input-group">
                            <input type="text" class="form-control" name="nuevo_estado" 
                                   placeholder="Nombre del nuevo estado" required>
                            <button type="submit" name="agregar_estado" class="btn btn-success">Agregar</button>
                        </div>
                    </form>

                    <h4>Lista de Estados</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($estados as $estado): ?>
                                <tr>
                                    <td><?= htmlspecialchars($estado['id_estado_envio']) ?></td>
                                    <td><?= htmlspecialchars($estado['estado']) ?></td>
                                    <td>
                                        <form method="POST" action="" style="display:inline;">
                                            <input type="hidden" name="id_estado" value="<?= $estado['id_estado_envio'] ?>">
                                            <button type="submit" name="eliminar_estado" class="btn btn-danger btn-sm" 
                                                    onclick="return confirm('¿Está seguro de eliminar este estado?')">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../views/layouts/footer.php'; ?>

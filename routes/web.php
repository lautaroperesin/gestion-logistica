<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/ClienteController.php';
require_once __DIR__ . '/../controllers/ConductorController.php';
require_once __DIR__ . '/../controllers/VehiculoController.php';
require_once __DIR__ . '/../controllers/TipoCargaController.php';
require_once __DIR__ . '/../controllers/DashboardController.php';

// Función helper para detectar la página activa
function isCurrentRoute($route) {
    $currentRoute = $_GET['route'] ?? 'home';
    return $currentRoute === $route;
}

$route = $_GET['route'] ?? 'home';

switch ($route) {
    // Rutas del Dashboard
    case 'home':
    case 'dashboard':
        $dashboardController = new DashboardController();
        $dashboardController->index();
        break;

    // Rutas de Clientes
    case 'clientes':
        $clienteController = new ClienteController();
        $clienteController->index();
        break;
    case 'clientes_create':
        $clienteController = new ClienteController();
        $clienteController->create();
        break;
    case 'clientes_store':
        $clienteController = new ClienteController();
        $clienteController->store();
        break;
    case 'clientes_edit':
        $clienteController = new ClienteController();
        $clienteController->edit();
        break;
    case 'clientes_update':
        $clienteController = new ClienteController();
        $clienteController->update();
        break;
    case 'clientes_delete':
        $clienteController = new ClienteController();
        $clienteController->delete();
        break;
    
    // Rutas de conductores
    case 'conductores':
        $conductorController = new ConductorController();
        $conductorController->index();
        break;
    case 'conductores_create':
        $conductorController = new ConductorController();
        $conductorController->create();
        break;
    case 'conductores_store':
        $conductorController = new ConductorController();
        $conductorController->store();
        break;
    case 'conductores_edit':
        $conductorController = new ConductorController();
        $conductorController->edit();
        break;
    case 'conductores_update':
        $conductorController = new ConductorController();
        $conductorController->update();
        break;
    case 'conductores_delete':
        $conductorController = new ConductorController();
        $conductorController->delete();
        break;

    // Rutas de Vehículos
    case 'vehiculos':
        $vehiculoController = new VehiculoController();
        $vehiculoController->index();
        break;
    case 'vehiculos_create':
        $vehiculoController = new VehiculoController();
        $vehiculoController->create();
        break;
    case 'vehiculos_store':
        $vehiculoController = new VehiculoController();
        $vehiculoController->store();
        break;
    case 'vehiculos_edit':
        $vehiculoController = new VehiculoController();
        $vehiculoController->edit();
        break;
    case 'vehiculos_update':
        $vehiculoController = new VehiculoController();
        $vehiculoController->update();
        break;
    case 'vehiculos_delete':
        $vehiculoController = new VehiculoController();
        $vehiculoController->delete();
        break;

    // Rutas de Tipos de Carga
    case 'tipos_carga':
        $tipoCargaController = new TipoCargaController();
        $tipoCargaController->index();
        break;
    case 'tipos_carga_create':
        $tipoCargaController = new TipoCargaController();
        $tipoCargaController->create();
        break;
    case 'tipos_carga_store':
        $tipoCargaController = new TipoCargaController();
        $tipoCargaController->store();
        break;
    case 'tipos_carga_edit':
        $tipoCargaController = new TipoCargaController();
        $tipoCargaController->edit();
        break;
    case 'tipos_carga_update':
        $tipoCargaController = new TipoCargaController();
        $tipoCargaController->update();
        break;
    case 'tipos_carga_delete':
        $tipoCargaController = new TipoCargaController();
        $tipoCargaController->delete();
        break;

    // Ruta por defecto
    default:
        $dashboardController = new DashboardController();
        $dashboardController->index();
        break;
}
?>

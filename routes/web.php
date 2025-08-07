<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/ClienteController.php';
require_once __DIR__ . '/../controllers/ConductorController.php';
require_once __DIR__ . '/../controllers/VehiculoController.php';
require_once __DIR__ . '/../controllers/TipoCargaController.php';
require_once __DIR__ . '/../controllers/DashboardController.php';
require_once __DIR__ . '/../controllers/EnvioController.php';
require_once __DIR__ . '/../controllers/UbicacionController.php';
require_once __DIR__ . '/../controllers/FacturaController.php';
require_once __DIR__ . '/../controllers/MetodoPagoController.php';
require_once __DIR__ . '/../controllers/MovimientoCajaController.php';

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

    // Rutas de Envíos
    case 'envios':
        $envioController = new EnvioController();
        $envioController->index();
        break;
    case 'envios_create':
        $envioController = new EnvioController();
        $envioController->create();
        break;
    case 'envios_store':
        $envioController = new EnvioController();
        $envioController->store();
        break;
    case 'envios_edit':
        $envioController = new EnvioController();
        $envioController->edit();
        break;
    case 'envios_update':
        $envioController = new EnvioController();
        $envioController->update();
        break;
    case 'envios_delete':
        $envioController = new EnvioController();
        $envioController->delete();
        break;

    // Rutas de Ubicaciones
    case 'ubicaciones':
        $ubicacionController = new UbicacionController();
        $ubicacionController->index();
        break;
    case 'ubicaciones_create':
        $ubicacionController = new UbicacionController();
        $ubicacionController->create();
        break;
    case 'ubicaciones_store':
        $ubicacionController = new UbicacionController();
        $ubicacionController->store();
        break;
    case 'ubicaciones_edit':
        $ubicacionController = new UbicacionController();
        $ubicacionController->edit();
        break;
    case 'ubicaciones_update':
        $ubicacionController = new UbicacionController();
        $ubicacionController->update();
        break;
    case 'ubicaciones_delete':
        $ubicacionController = new UbicacionController();
        $ubicacionController->delete();
        break;
    case 'getProvinciasByPais':
        $ubicacionController = new UbicacionController();
        $ubicacionController->getProvinciasByPais();
        break;
    case 'getLocalidadesByProvincia':
        $ubicacionController = new UbicacionController();
        $ubicacionController->getLocalidadesByProvincia();
        break;

    // Rutas de Facturas
    case 'facturas':
        $facturaController = new FacturaController();
        $facturaController->index();
        break;
    case 'facturas_create':
        $facturaController = new FacturaController();
        $facturaController->create();
        break;
    case 'facturas_store':
        $facturaController = new FacturaController();
        $facturaController->store();
        break;
    case 'facturas_edit':
        $facturaController = new FacturaController();
        $facturaController->edit();
        break;
    case 'facturas_update':
        $facturaController = new FacturaController();
        $facturaController->update();
        break;
    case 'facturas_delete':
        $facturaController = new FacturaController();
        $facturaController->delete();
        break;
    case 'facturas_pago':
        $facturaController = new FacturaController();
        $facturaController->pago();
        break;
    case 'facturas_by_envio':
        $facturaController = new FacturaController();
        $facturaController->getByEnvio();
        break;
    case 'facturas_by_cliente':
        $facturaController = new FacturaController();
        $facturaController->getByCliente();
        break;
    case 'facturas_by_estado':
        $facturaController = new FacturaController();
        $facturaController->getByEstado();
        break;
        
    // Rutas de Métodos de Pago
    case 'metodos_pago':
        $metodoPagoController = new MetodoPagoController();
        $metodoPagoController->index();
        break;
    case 'metodos_pago_create':
        $metodoPagoController = new MetodoPagoController();
        $metodoPagoController->create();
        break;
    case 'metodos_pago_store':
        $metodoPagoController = new MetodoPagoController();
        $metodoPagoController->store();
        break;
    case 'metodos_pago_edit':
        $metodoPagoController = new MetodoPagoController();
        $metodoPagoController->edit();
        break;
    case 'metodos_pago_update':
        $metodoPagoController = new MetodoPagoController();
        $metodoPagoController->update();
        break;
    case 'metodos_pago_delete':
        $metodoPagoController = new MetodoPagoController();
        $metodoPagoController->delete();
        break;

    // Rutas de Movimientos de Caja
    case 'movimientos_caja':
        $movimientoCajaController = new MovimientoCajaController();
        $movimientoCajaController->index();
        break;
    case 'movimientos_caja_create':
        $movimientoCajaController = new MovimientoCajaController();
        $movimientoCajaController->create();
        break;
    case 'movimientos_caja_store':
        $movimientoCajaController = new MovimientoCajaController();
        $movimientoCajaController->store();
        break;
    case 'movimientos_caja_edit':
        $movimientoCajaController = new MovimientoCajaController();
        $movimientoCajaController->edit();
        break;
    case 'movimientos_caja_update':
        $movimientoCajaController = new MovimientoCajaController();
        $movimientoCajaController->update();
        break;
    case 'movimientos_caja_delete':
        $movimientoCajaController = new MovimientoCajaController();
        $movimientoCajaController->delete();
        break;
    case 'movimientos_caja_by_factura':
        $movimientoCajaController = new MovimientoCajaController();
        $movimientoCajaController->getByFactura();
        break;

    // Ruta por defecto
    default:
        $dashboardController = new DashboardController();
        $dashboardController->index();
        break;
}
?>

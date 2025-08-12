<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/UsuarioController.php';
require_once __DIR__ . '/routes/web.php';

// Inicializar el controlador de usuario
$usuarioController = new UsuarioController();

// Obtener la ruta actual
$route = $_GET['route'] ?? '';

// Si no hay ruta especificada
if (empty($route)) {
    // Si hay sesión, redirigir a dashboard
    if ($usuarioController->isLogged()) {
        header('Location: ?route=dashboard');
        exit();
    }
    // Si no hay sesión, redirigir a login
    header('Location: ?route=login');
    exit();
}

// Si hay una ruta, procesarla (web.php ya se incluyó al inicio)
exit();
?>

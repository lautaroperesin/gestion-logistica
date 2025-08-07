<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Logística</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background-color: #343a40;
            min-height: 100vh;
        }
        .sidebar .nav-link {
            color: #adb5bd;
            padding: 0.75rem 1rem;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: #495057;
        }
        .sidebar .nav-link.active {
            color: #fff;
            background-color: #007bff;
        }
        .main-content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block sidebar">
                <div class="sidebar-sticky">
                    <div class="text-center py-3">
                        <h5 class="text-white">Sistema Logística</h5>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo isCurrentRoute('home') || isCurrentRoute('dashboard') ? 'active' : ''; ?>" href="?route=dashboard">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo isCurrentRoute('envios') ? 'active' : ''; ?>" href="?route=envios">
                                <i class="fas fa-boxes"></i> Envíos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo isCurrentRoute('clientes') ? 'active' : ''; ?>" href="?route=clientes">
                                <i class="fas fa-users"></i> Clientes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo isCurrentRoute('conductores') ? 'active' : ''; ?>" href="?route=conductores">
                                <i class="fas fa-user-tie"></i> Conductores
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo isCurrentRoute('vehiculos') ? 'active' : ''; ?>" href="?route=vehiculos">
                                <i class="fas fa-truck"></i> Vehículos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo isCurrentRoute('tipos_carga') ? 'active' : ''; ?>" href="?route=tipos_carga">
                                <i class="fas fa-boxes"></i> Tipos de Carga
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main role="main" class="col-md-10 ml-sm-auto main-content">


    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tachometer-alt me-2"></i>Panel de Control
            </h1>
            <div class="d-flex align-items-center">
                <span class="badge bg-primary me-2">
                    <i class="fas fa-calendar-day me-1"></i> <?= date('d/m/Y') ?>
                </span>
            </div>
        </div>

        <!-- Estadísticas Principales -->
        <div class="row mb-4">
            <!-- Ingresos del Mes -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Ingresos del Mes</div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800">
                                    $<?= number_format((float)$stats['ingresos_mes_actual'], 2, ',', '.') ?>
                                </div>
                                <div class="mt-2 text-xs text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i> <?= date('m/Y') ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-primary-100"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Envíos del Mes -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Envíos del Mes</div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800">
                                    <?= number_format($stats['envios_mes_actual'], 0, ',', '.') ?>
                                </div>
                                <div class="mt-2">
                                    <span class="badge bg-success bg-opacity-10 text-success">
                                        <i class="fas fa-truck me-1"></i> 
                                        <?= $stats['envios_pendientes'] ?? 0 ?> pendientes
                                    </span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shipping-fast fa-2x text-success-100"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flota de Vehículos -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Flota de Vehículos</div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800">
                                    <?= number_format($stats['vehiculos_disponibles'] ?? 0, 0, ',', '.') ?> / 
                                    <?= number_format($stats['vehiculos'] ?? 0, 0, ',', '.') ?>
                                    <small class="text-xs text-muted">disponibles</small>
                                </div>
                                <div class="mt-2">
                                    <?php 
                                    $porcentaje = ($stats['vehiculos'] > 0) 
                                        ? ($stats['vehiculos_disponibles'] / $stats['vehiculos']) * 100 
                                        : 0;
                                    ?>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-info" role="progressbar" 
                                             style="width: <?= $porcentaje ?>%" 
                                             aria-valuenow="<?= $stats['vehiculos_disponibles'] ?? 0 ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="<?= $stats['vehiculos'] ?? 0 ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-truck-pickup fa-2x text-info-100"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clientes Activos -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Clientes Activos</div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800">
                                    <?= number_format($stats['clientes'] ?? 0, 0, ',', '.') ?>
                                </div>
                                <div class="mt-2">
                                    <span class="badge bg-warning bg-opacity-10 text-warning">
                                        <i class="fas fa-users me-1"></i> 
                                        <?= count($stats['top_clientes'] ?? []) ?> destacados
                                    </span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-warning-100"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Accesos Rápidos -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white py-3">
                        <h6 class="m-0 fw-bold">
                            <i class="fas fa-bolt me-2"></i> Accesos Rápidos
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3">
                            <!-- Gestión de Clientes -->
                            <div class="col-xl-2 col-md-4 col-6">
                                <a href="?route=clientes_create" class="btn btn-light btn-hover-scale w-100 h-100 p-3 border-0 shadow-sm text-center d-flex flex-column align-items-center justify-content-center" 
                                   data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar nuevo cliente">
                                    <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle p-3 mb-2">
                                        <i class="fas fa-user-plus fa-2x text-primary"></i>
                                    </div>
                                    <div class="fw-bold text-dark mb-1">Nuevo Cliente</div>
                                    <small class="text-muted">Registrar cliente</small>
                                </a>
                            </div>
                            
                            <div class="col-xl-2 col-md-4 col-6">
                                <a href="?route=clientes" class="btn btn-light btn-hover-scale w-100 h-100 p-3 border-0 shadow-sm text-center d-flex flex-column align-items-center justify-content-center"
                                   data-bs-toggle="tooltip" data-bs-placement="top" title="Ver todos los clientes">
                                    <div class="icon-wrapper bg-info bg-opacity-10 rounded-circle p-3 mb-2">
                                        <i class="fas fa-users fa-2x text-info"></i>
                                    </div>
                                    <div class="fw-bold">Ver Clientes</div>
                                    <small>Gestionar clientes existentes</small>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="#" class="btn btn-outline-success quick-action-btn w-100 py-3" onclick="alert('Módulo en desarrollo')">
                                    <i class="fas fa-user-tie fa-2x mb-2 d-block"></i>
                                    <div class="fw-bold">Conductores</div>
                                    <small>Gestionar conductores</small>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="#" class="btn btn-outline-info quick-action-btn w-100 py-3" onclick="alert('Módulo en desarrollo')">
                                    <i class="fas fa-truck fa-2x mb-2 d-block"></i>
                                    <div class="fw-bold">Vehículos</div>
                                    <small>Gestionar vehículos</small>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
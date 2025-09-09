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

            <!-- Facturas Pendientes -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase">
                                        <i class="fas fa-clock me-1"></i> Facturas Por Cobrar
                                    </div>
                                    <span class="badge bg-danger bg-opacity-10 text-danger">
                                        <?= $stats['facturas_pendientes'] ?? 0 ?> pendientes
                                    </span>
                                </div>
                                <div class="h3 mb-2 font-weight-bold text-gray-800">
                                    $<?= number_format($stats['monto_pendiente'] ?? 0, 2, ',', '.') ?>
                                </div>
                                <div class="mt-3">
                                    <a href="?route=facturas" class="text-xs text-danger">
                                        <i class="fas fa-arrow-circle-right me-1"></i> Ver facturas
                                    </a>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="icon-circle bg-danger bg-opacity-10">
                                    <i class="fas fa-clock text-danger"></i>
                                </div>
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
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="m-0 fw-bold text-uppercase text-muted">
                            <i class="fas fa-bolt text-warning me-2"></i> Acciones Rápidas
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3">
                            <!-- Nuevo Envío -->
                            <div class="col-xl-2 col-md-4 col-6">
                                <a href="?route=envios_create" class="btn btn-light btn-hover-zoom w-100 h-100 p-3 border-0 shadow-sm text-center d-flex flex-column align-items-center justify-content-center" 
                                   data-bs-toggle="tooltip" data-bs-placement="top" title="Crear nuevo envío">
                                    <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle p-3 mb-2">
                                        <i class="fas fa-shipping-fast fa-2x text-success"></i>
                                    </div>
                                    <div class="fw-bold text-dark mb-1">Nuevo Envío</div>
                                    <small class="text-muted">Registrar envío</small>
                                </a>
                            </div>
                            
                            <!-- Lista de Envíos -->
                            <div class="col-xl-2 col-md-4 col-6">
                                <a href="?route=envios" class="btn btn-light btn-hover-zoom w-100 h-100 p-3 border-0 shadow-sm text-center d-flex flex-column align-items-center justify-content-center"
                                   data-bs-toggle="tooltip" data-bs-placement="top" title="Ver todos los envíos">
                                    <div class="icon-wrapper bg-info bg-opacity-10 rounded-circle p-3 mb-2">
                                        <i class="fas fa-list-ul fa-2x text-info"></i>
                                    </div>
                                    <div class="fw-bold">Ver Envíos</div>
                                    <small>Gestionar envíos</small>
                                </a>
                            </div>

                             <!-- Gestión de Clientes -->
                             <div class="col-xl-2 col-md-4 col-6">
                                <a href="?route=clientes_create" class="btn btn-light btn-hover-zoom w-100 h-100 p-3 border-0 shadow-sm text-center d-flex flex-column align-items-center justify-content-center" 
                                   data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar nuevo cliente">
                                    <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle p-3 mb-2">
                                        <i class="fas fa-user-plus fa-2x text-primary"></i>
                                    </div>
                                    <div class="fw-bold text-dark mb-1">Nuevo Cliente</div>
                                    <small class="text-muted">Registrar cliente</small>
                                </a>
                            </div>
                            
                            <!-- Gestión de Conductores -->
                            <div class="col-xl-2 col-md-4 col-6">
                                <a href="?route=conductores" class="btn btn-light btn-hover-zoom w-100 h-100 p-3 border-0 shadow-sm text-center d-flex flex-column align-items-center justify-content-center"
                                   data-bs-toggle="tooltip" data-bs-placement="top" title="Gestionar conductores">
                                    <div class="icon-wrapper bg-warning bg-opacity-10 rounded-circle p-3 mb-2">
                                        <i class="fas fa-user-tie fa-2x text-warning"></i>
                                    </div>
                                    <div class="fw-bold">Conductores</div>
                                    <small>Gestionar personal</small>
                                </a>
                            </div>
                            
                            <!-- Gestión de Vehículos -->
                            <div class="col-xl-2 col-md-4 col-6">
                                <a href="?route=vehiculos" class="btn btn-light btn-hover-zoom w-100 h-100 p-3 border-0 shadow-sm text-center d-flex flex-column align-items-center justify-content-center"
                                   data-bs-toggle="tooltip" data-bs-placement="top" title="Gestionar vehículos">
                                    <div class="icon-wrapper bg-purple bg-opacity-10 rounded-circle p-3 mb-2">
                                        <i class="fas fa-truck-pickup fa-2x text-purple"></i>
                                    </div>
                                    <div class="fw-bold">Vehículos</div>
                                    <small>Gestionar flota</small>
                                </a>
                            </div>

                            <!-- Agregar ubicacion -->
                            <div class="col-xl-2 col-md-4 col-6">
                                <a href="?route=ubicaciones_create" class="btn btn-light btn-hover-zoom w-100 h-100 p-3 border-0 shadow-sm text-center d-flex flex-column align-items-center justify-content-center"
                                   data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar nueva ubicación">
                                    <div class="icon-wrapper bg-info bg-opacity-10 rounded-circle p-3 mb-2">
                                        <i class="fas fa-map-marker-alt fa-2x text-info"></i>
                                    </div>
                                    <div class="fw-bold">Ubicaciones</div>
                                    <small>Agregar nueva ubicación</small>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
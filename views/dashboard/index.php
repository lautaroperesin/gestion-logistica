    <div class="container-fluid px-4">
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-4 text-gray-800">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </h1>
            </div>
        </div>

        <!-- Tarjetas de estadísticas -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-border-left card-border-left-primary shadow stat-card h-100">
                    <div class="card-body py-3">
                        <div class="row no-gutters align-items-center">
                            <div class="col me-2">
                                <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                    Clientes Activos
                                </div>
                                <div class="h5 mb-0 fw-bold text-gray-800">
                                    <?= $stats['clientes'] ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-border-left card-border-left-success shadow stat-card h-100">
                    <div class="card-body py-3">
                        <div class="row no-gutters align-items-center">
                            <div class="col me-2">
                                <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                    Conductores Activos
                                </div>
                                <div class="h5 mb-0 fw-bold text-gray-800">
                                    <?= $stats['conductores'] ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-border-left card-border-left-info shadow stat-card h-100">
                    <div class="card-body py-3">
                        <div class="row no-gutters align-items-center">
                            <div class="col me-2">
                                <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                    Vehículos Activos
                                </div>
                                <div class="h5 mb-0 fw-bold text-gray-800">
                                    <?= $stats['vehiculos'] ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-truck fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-border-left card-border-left-warning shadow stat-card h-100">
                    <div class="card-body py-3">
                        <div class="row no-gutters align-items-center">
                            <div class="col me-2">
                                <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                    Tipos de Carga
                                </div>
                                <div class="h5 mb-0 fw-bold text-gray-800">
                                    <?= $stats['tipos_carga'] ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-boxes fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accesos rápidos -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h6 class="m-0 fw-bold">
                            <i class="fas fa-bolt"></i> Accesos Rápidos
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="?route=clientes_create" class="btn btn-primary quick-action-btn w-100 py-3">
                                    <i class="fas fa-user-plus fa-2x mb-2 d-block"></i>
                                    <div class="fw-bold">Nuevo Cliente</div>
                                    <small>Registrar un nuevo cliente</small>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="?route=clientes" class="btn btn-outline-primary quick-action-btn w-100 py-3">
                                    <i class="fas fa-users fa-2x mb-2 d-block"></i>
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
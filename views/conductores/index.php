<div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-users"></i> Lista de Conductores</h1>
                    <a href="?route=conductores_create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Conductor
                    </a>
                </div>
                
                <!-- Barra de búsqueda -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <form action="" method="get" class="d-flex">
                            <input type="hidden" name="route" value="conductores">
                            <div class="input-group">
                                <input type="text" 
                                       name="buscar" 
                                       class="form-control" 
                                       placeholder="Buscar conductor por nombre..." 
                                       value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                                <?php if(isset($_GET['buscar']) && !empty($_GET['buscar'])): ?>
                                    <a href="?route=conductores" class="btn btn-outline-danger">
                                        <i class="fas fa-times"></i> Limpiar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_GET['success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_GET['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow">
                    <div class="card-body">
                        <?php if ($conductores && $conductores->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th><i class="fas fa-hashtag"></i> ID</th>
                                            <th><i class="fas fa-user"></i> Conductor</th>
                                            <th><i class="fas fa-id-card"></i> DNI</th>
                                            <th><i class="fas fa-id-card"></i> Clase licencia</th>
                                            <th><i class="fas fa-calendar"></i> Vencimiento licencia</th>
                                            <th><i class="fas fa-envelope"></i> Email</th>
                                            <th><i class="fas fa-phone"></i> Telefono</th>
                                            <th><i class="fas fa-cogs"></i> Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($conductor = $conductores->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($conductor['id_conductor']) ?></td>
                                                <td><?= htmlspecialchars($conductor['conductor']) ?></td>
                                                <td><?= htmlspecialchars($conductor['dni']) ?></td>
                                                <td><?= htmlspecialchars($conductor['clase_licencia']) ?></td>
                                                <td><?= !empty($conductor['vencimiento_licencia']) ? date('d/m/Y', strtotime($conductor['vencimiento_licencia'])) : '' ?></td>
                                                <td><?= htmlspecialchars($conductor['email']) ?></td>
                                                <td><?= htmlspecialchars($conductor['telefono']) ?></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="?route=conductores_edit&id_conductor=<?= $conductor['id_conductor'] ?>" 
                                                           class="btn btn-sm btn-outline-primary" 
                                                           title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="?route=conductores_delete&id_conductor=<?= $conductor['id_conductor'] ?>" 
                                                           class="btn btn-sm btn-outline-danger" 
                                                           title="Eliminar"
                                                           onclick="return confirm('¿Estás seguro de que deseas eliminar este conductor?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">No hay conductores registrados</h4>
                                <p class="text-muted">Comienza agregando tu primer conductor</p>
                                <a href="?route=conductores_create" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Crear Primer Conductor
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

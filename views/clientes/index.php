 <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-users"></i> Lista de Clientes</h1>
                    <a href="?route=clientes_create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Cliente
                    </a>
                </div>
                
                <!-- Barra de búsqueda -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <form action="" method="get" class="d-flex">
                            <input type="hidden" name="route" value="clientes">
                            <div class="input-group">
                                <input type="text" 
                                       name="buscar" 
                                       class="form-control" 
                                       placeholder="Buscar cliente por nombre..." 
                                       value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                                <?php if(isset($_GET['buscar']) && !empty($_GET['buscar'])): ?>
                                    <a href="?route=clientes" class="btn btn-outline-danger">
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
                        <?php if ($clientes && $clientes->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th><i class="fas fa-hashtag"></i> ID</th>
                                            <th><i class="fas fa-user"></i> Cliente</th>
                                            <th><i class="fas fa-envelope"></i> Email</th>
                                            <th><i class="fas fa-phone"></i> Telefono</th>
                                            <th><i class="fas fa-cogs"></i> Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($cliente = $clientes->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($cliente['id_cliente']) ?></td>
                                                <td><?= htmlspecialchars($cliente['cliente']) ?></td>
                                                <td><?= htmlspecialchars($cliente['email']) ?></td>
                                                <td><?= htmlspecialchars($cliente['telefono']) ?></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="?route=clientes_edit&id_cliente=<?= $cliente['id_cliente'] ?>" 
                                                           class="btn btn-sm btn-outline-primary" 
                                                           title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="?route=clientes_delete&id_cliente=<?= $cliente['id_cliente'] ?>" 
                                                           class="btn btn-sm btn-outline-danger" 
                                                           title="Eliminar"
                                                           onclick="return confirm('¿Estás seguro de que deseas eliminar este cliente?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                                
                                <?php if ($totalPaginas > 1): ?>
                                <nav class="mt-4">
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?route=clientes&page=<?= $pagina - 1 ?><?= !empty($buscar) ? '&buscar=' . urlencode($buscar) : '' ?>">
                                                <i class="fas fa-chevron-left"></i> Anterior
                                            </a>
                                        </li>
                                        
                                        <?php 
                                        // Mostrar máximo 5 números de página alrededor de la página actual
                                        $inicio = max(1, $pagina - 2);
                                        $fin = min($totalPaginas, $pagina + 2);
                                        
                                        // Ajustar si estamos cerca del inicio o del final
                                        if ($fin - $inicio < 4) {
                                            if ($inicio === 1) {
                                                $fin = min(5, $totalPaginas);
                                            } else {
                                                $inicio = max(1, $totalPaginas - 4);
                                            }
                                        }
                                        
                                        // Mostrar primera página si no está en el rango
                                        if ($inicio > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?route=clientes&page=1<?= !empty($buscar) ? '&buscar=' . urlencode($buscar) : '' ?>">1</a>
                                            </li>
                                            <?php if ($inicio > 2): ?>
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = $inicio; $i <= $fin; $i++): ?>
                                            <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                                <a class="page-link" href="?route=clientes&page=<?= $i ?><?= !empty($buscar) ? '&buscar=' . urlencode($buscar) : '' ?>">
                                                    <?= $i ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php // Mostrar última página si no está en el rango
                                        if ($fin < $totalPaginas): ?>
                                            <?php if ($fin < $totalPaginas - 1): ?>
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            <?php endif; ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?route=clientes&page=<?= $totalPaginas ?><?= !empty($buscar) ? '&buscar=' . urlencode($buscar) : '' ?>">
                                                    <?= $totalPaginas ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <li class="page-item <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?route=clientes&page=<?= $pagina + 1 ?><?= !empty($buscar) ? '&buscar=' . urlencode($buscar) : '' ?>">
                                                Siguiente <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                                <?php endif; ?>
                                
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">No hay clientes registrados</h4>
                                <p class="text-muted">Comienza agregando tu primer cliente</p>
                                <a href="?route=clientes_create" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Crear Primer Cliente
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

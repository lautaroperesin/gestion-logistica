<?php
require_once __DIR__ . '/../layouts/header.php';

function getStatusBadgeClass($status) {
    $classes = [
        'pendiente' => 'bg-warning',
        'en preparacion' => 'bg-info',
        'en transito' => 'bg-primary',
        'entregado' => 'bg-success',
        'demorado' => 'bg-warning',
        'cancelado' => 'bg-danger'
    ];
    
    return $classes[strtolower($status)] ?? 'bg-secondary';
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Envíos</h1>
        <a href="?route=envios_create" class="btn btn-primary">Nuevo Envío</a>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filtros de búsqueda</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <input type="hidden" name="route" value="envios">
                
                <div class="col-md-3">
                    <label for="numero_seguimiento" class="form-label">Número de Seguimiento</label>
                    <input type="text" class="form-control" id="numero_seguimiento" name="numero_seguimiento" 
                           value="<?= htmlspecialchars($_GET['numero_seguimiento'] ?? '') ?>">
                </div>
                
                <div class="col-md-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-select" id="estado" name="estado">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" <?= ($_GET['estado'] ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="en preparacion" <?= ($_GET['estado'] ?? '') === 'en preparacion' ? 'selected' : '' ?>>En Preparación</option>
                        <option value="en transito" <?= ($_GET['estado'] ?? '') === 'en transito' ? 'selected' : '' ?>>En Tránsito</option>
                        <option value="entregado" <?= ($_GET['estado'] ?? '') === 'entregado' ? 'selected' : '' ?>>Entregado</option>
                        <option value="demorado" <?= ($_GET['estado'] ?? '') === 'demorado' ? 'selected' : '' ?>>Demorado</option>
                        <option value="cancelado" <?= ($_GET['estado'] ?? '') === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="fecha_desde" class="form-label">Fecha desde</label>
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" 
                           value="<?= htmlspecialchars($_GET['fecha_desde'] ?? '') ?>">
                </div>
                
                <div class="col-md-3">
                    <label for="fecha_hasta" class="form-label">Fecha hasta</label>
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" 
                           value="<?= htmlspecialchars($_GET['fecha_hasta'] ?? '') ?>">
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    <a href="?route=envios" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Número de Seguimiento</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Fecha Salida</th>
                            <th>Estado</th>
                            <th>Peso (kg)</th>
                            <th>Volumen (m³)</th>
                            <th>Costo Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($envios as $envio): ?>
                        <tr>
                            <td><?= htmlspecialchars($envio['numero_seguimiento']) ?></td>
                            <td><?= htmlspecialchars($envio['origen']) ?></td>
                            <td><?= htmlspecialchars($envio['destino']) ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($envio['fecha_salida']))) ?></td>
                            <td><span class="badge <?= getStatusBadgeClass($envio['estado']) ?>"><?= ucfirst(str_replace('_', ' ', $envio['estado'])) ?></span></td>
                            <td><?= number_format($envio['peso_kg'], 2) ?></td>
                            <td><?= number_format($envio['volumen_m3'], 2) ?></td>
                            <td>$<?= number_format($envio['costo_total'], 2) ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="?route=envios_edit&id_envio=<?= $envio['id_envio'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                    <form action="?route=envios_delete" method="POST" class="d-inline">
                                        <input type="hidden" name="id_envio" value="<?= $envio['id_envio'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este envío?')"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <?php if ($totalPages > 1): ?>
            <?php
            // Crear un array con los parámetros actuales del GET
            $params = [];
            $ignoreParams = ['page'];
            
            // Obtener todos los parámetros de filtro del GET
            foreach ($_GET as $key => $value) {
                if (!in_array($key, $ignoreParams) && !empty($value)) {
                    $params[$key] = $value;
                }
            }
            
            // Asegurarse de que el parámetro 'route' esté presente
            $params['route'] = 'envios';
            ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <!-- Botón Anterior -->
                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        <?php if ($currentPage > 1): ?>
                            <a class="page-link" href="?<?= http_build_query(array_merge($params, ['page' => $currentPage - 1])) ?>">Anterior</a>
                        <?php else: ?>
                            <span class="page-link">Anterior</span>
                        <?php endif; ?>
                    </li>
                    
                    <!-- Números de página -->
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($params, ['page' => $i])) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <!-- Botón Siguiente -->
                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <?php if ($currentPage < $totalPages): ?>
                            <a class="page-link" href="?<?= http_build_query(array_merge($params, ['page' => $currentPage + 1])) ?>">Siguiente</a>
                        <?php else: ?>
                            <span class="page-link">Siguiente</span>
                        <?php endif; ?>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

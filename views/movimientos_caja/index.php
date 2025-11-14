<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Movimientos de Caja</h1>
        <!-- <a href="?route=movimientos_caja_create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Movimiento
        </a> -->
    </div>

    <!-- Formulario de búsqueda -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Búsqueda de Movimientos</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="mb-0">
                <input type="hidden" name="route" value="movimientos_caja">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="numero_factura" class="form-label">N° Factura</label>
                        <input type="text" class="form-control" id="numero_factura" name="numero_factura" 
                               value="<?= htmlspecialchars($_GET['numero_factura'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="cliente" class="form-label">Cliente</label>
                        <input type="text" class="form-control" id="cliente" name="cliente"
                               value="<?= htmlspecialchars($_GET['cliente'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="fecha_desde" class="form-label">Desde</label>
                        <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"
                               value="<?= htmlspecialchars($_GET['fecha_desde'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="fecha_hasta" class="form-label">Hasta</label>
                        <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"
                               value="<?= htmlspecialchars($_GET['fecha_hasta'] ?? '') ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="btn-group w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="?route=movimientos_caja" class="btn btn-secondary">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped text-end">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Número Factura</th>
                            <th>Cliente</th>
                            <th>Método de Pago</th>
                            <th>Fecha de Pago</th>
                            <th>Monto</th>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movimientos as $movimiento): ?>
                            <tr>
                                <td><?= $movimiento['id_movimiento'] ?></td>
                                <td><?= $movimiento['numero_factura'] ?></td>
                                <td><?= htmlspecialchars($movimiento['cliente']) ?></td>
                                <td><?= htmlspecialchars($movimiento['metodo_pago']) ?></td>
                                <td><?= date('d/m/Y', strtotime($movimiento['fecha_pago'])) ?></td>
                                <td>$<?= number_format($movimiento['monto'], 2) ?></td>
                                <td><?= htmlspecialchars($movimiento['observaciones']) ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="?route=movimientos_caja_recibo_pdf&id_movimiento=<?= $movimiento['id_movimiento'] ?>" class="btn btn-sm btn-success" title="Imprimir Recibo PDF" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        <a href="?route=movimientos_caja_edit&id_movimiento=<?= $movimiento['id_movimiento'] ?>" class="btn btn-sm btn-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?route=movimientos_caja_delete&id_movimiento=<?= $movimiento['id_movimiento'] ?>" class="btn btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este movimiento?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php if ($totalPaginas > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?route=movimientos_caja&page=<?= $pagina - 1 ?>">Anterior</a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                            <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                <a class="page-link" href="?route=movimientos_caja&page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>">
                            <a class="page-link" href="?route=movimientos_caja&page=<?= $pagina + 1 ?>">Siguiente</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

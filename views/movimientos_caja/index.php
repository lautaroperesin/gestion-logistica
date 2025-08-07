<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col-md-6">
            <h2>Movimientos de Caja</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="?route=movimientos_caja_create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Movimiento
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
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
                                <td><?= $movimiento['cliente'] ?></td>
                                <td><?= $movimiento['metodo_pago'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($movimiento['fecha_pago'])) ?></td>
                                <td>$<?= number_format($movimiento['monto'], 2) ?></td>
                                <td><?= $movimiento['observaciones'] ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="?route=movimientos_caja_edit&id_movimiento=<?= $movimiento['id_movimiento'] ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?route=movimientos_caja_delete&id_movimiento=<?= $movimiento['id_movimiento'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este movimiento?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

<?php
require_once __DIR__ . '/../../helpers/factura_helper.php';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col-md-6">
            <h2>Facturas</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="?route=facturas_create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Factura
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
                            <th>Envío</th>
                            <th>Cliente</th>
                            <th>Fecha Emisión</th>
                            <th>Fecha Vencimiento</th>
                            <th>Estado</th>
                            <th>Subtotal</th>
                            <th>IVA (%)</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($facturas as $factura): ?>
                            <tr>
                                <td><?= $factura['id_factura'] ?></td>
                                <td><?= $factura['numero_factura'] ?></td>
                                <td><?= $factura['numero_seguimiento'] ?></td>
                                <td><?= $factura['cliente'] ?></td>
                                <td><?= date('d/m/Y', strtotime($factura['fecha_emision'])) ?></td>
                                <td><?= $factura['fecha_vencimiento'] ? date('d/m/Y', strtotime($factura['fecha_vencimiento'])) : '-' ?></td>
                                <td>
                                    <?php
                                    // Obtener estado basado en el total_pagado pre-calculado
                                    $estado = calcularEstadoFactura(
                                        $factura['total'],
                                        $factura['total_pagado'],
                                        $factura['fecha_vencimiento']
                                    );
                                    
                                    // Mostrar badge con estado
                                    echo getBadgeEstadoFactura($estado, $factura['total'], $factura['total_pagado']);
                                    ?>
                                </td>
                                <td>$<?= number_format($factura['subtotal'], 2) ?></td>
                                <td>%<?= number_format($factura['iva'], 2) ?></td>
                                <td>$<?= number_format($factura['total'], 2) ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="?route=facturas_edit&id_factura=<?= $factura['id_factura'] ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?route=facturas_pago&id_factura=<?= $factura['id_factura'] ?>" class="btn btn-sm btn-success">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmarEliminacion('facturas', <?= $factura['id_factura'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
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

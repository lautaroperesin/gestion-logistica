<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Envíos</h1>
        <a href="?route=envios_create" class="btn btn-primary">Nuevo Envío</a>
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
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($envio['fecha_salida']))) ?></td>
                            <td><?= htmlspecialchars($envio['estado']) ?></td>
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
        </div>
    </div>
</div>

<?php
$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<div class="container mt-4">
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-car"></i> Lista de Vehículos</h2>
        <a href="?route=vehiculos_create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Vehículo
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patente</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Capacidad (kg)</th>
                            <th>Última Inspección</th>
                            <th>Estado</th>
                            <th>Venc. RTO</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehiculos as $vehiculo): ?>
                            <tr>
                                <td><?= htmlspecialchars($vehiculo['id_vehiculo']) ?></td>
                                <td><?= htmlspecialchars($vehiculo['patente']) ?></td>
                                <td><?= htmlspecialchars($vehiculo['marca']) ?></td>
                                <td><?= htmlspecialchars($vehiculo['modelo']) ?></td>
                                <td><?= htmlspecialchars($vehiculo['capacidad_kg']) ?> kg</td>
                                <td><?= date('d/m/Y', strtotime($vehiculo['ultima_inspeccion'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= $vehiculo['estado_vehiculo'] ? 'success' : 'danger' ?>">
                                        <?= $vehiculo['estado_vehiculo'] ? 'Disponible' : 'No Disponible' ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($vehiculo['rto_vencimiento'])) ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="?route=vehiculos_edit&id_vehiculo=<?= $vehiculo['id_vehiculo'] ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?route=vehiculos_delete&id_vehiculo=<?= $vehiculo['id_vehiculo'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de que desea eliminar este vehículo?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <a href="?route=vehiculos_cambiar_estado&id_vehiculo=<?= $vehiculo['id_vehiculo'] ?>&estado=<?= $vehiculo['estado_vehiculo'] ? 0 : 1 ?>" class="btn btn-<?= $vehiculo['estado_vehiculo'] ? 'danger' : 'success' ?> btn-sm" onclick="return confirm('¿Está seguro de que desea cambiar el estado del vehículo?')">
                                            <i class="fas fa-<?= $vehiculo['estado_vehiculo'] ? 'times' : 'check' ?>"></i>
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
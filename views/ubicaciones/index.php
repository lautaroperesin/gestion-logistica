<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Ubicaciones</h1>
        <a href="?route=ubicaciones_create" class="btn btn-primary">Nueva Ubicación</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Dirección</th>
                            <th>Localidad</th>
                            <th>Provincia</th>
                            <th>País</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ubicaciones as $ubicacion): ?>
                        <tr>
                            <td><?= htmlspecialchars($ubicacion['direccion']) ?></td>
                            <td><?= htmlspecialchars($ubicacion['localidad']) ?></td>
                            <td><?= htmlspecialchars($ubicacion['provincia']) ?></td>
                            <td><?= htmlspecialchars($ubicacion['pais']) ?></td>
                            <td><?= htmlspecialchars($ubicacion['descripcion']) ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="?route=ubicaciones_edit&id_ubicacion=<?= $ubicacion['id_ubicacion'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                    <a href="?route=ubicaciones_delete&id_ubicacion=<?= $ubicacion['id_ubicacion'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar esta ubicación?')"><i class="fas fa-trash"></i></a>
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

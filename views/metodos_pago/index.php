<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col-md-6">
            <h2>Métodos de Pago</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="?route=metodos_pago_create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Método de Pago
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
                            <th>Método de Pago</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($metodos_pago as $metodo): ?>
                            <tr>
                                <td><?= $metodo['id_metodo_pago'] ?></td>
                                <td><?= $metodo['metodo_pago'] ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="?route=metodos_pago_edit&id_metodo_pago=<?= $metodo['id_metodo_pago'] ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?route=metodos_pago_delete&id_metodo_pago=<?= $metodo['id_metodo_pago'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este método de pago?')">
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

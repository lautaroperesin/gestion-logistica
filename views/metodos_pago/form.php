<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0"><?php echo isset($metodo) ? 'Editar Método de Pago' : 'Nuevo Método de Pago'; ?></h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo isset($metodo) ? '?route=metodos_pago_update' : '?route=metodos_pago_store'; ?>">
                        <?php if (isset($metodo)): ?>
                            <input type="hidden" name="id_metodo_pago" value="<?php echo $metodo['id_metodo_pago']; ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="metodo_pago" class="form-label">Método de Pago</label>
                            <input type="text" class="form-control" id="metodo_pago" name="metodo_pago" 
                                   value="<?php echo isset($metodo) ? htmlspecialchars($metodo['metodo_pago']) : ''; ?>" required>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="?route=metodos_pago" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary"><?php echo isset($metodo) ? 'Actualizar' : 'Crear'; ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

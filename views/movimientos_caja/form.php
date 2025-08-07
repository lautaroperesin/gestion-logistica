<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0"><?php echo isset($movimiento) ? 'Editar Movimiento' : 'Nuevo Movimiento'; ?></h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo isset($movimiento) ? '?route=movimientos_caja_update' : '?route=movimientos_caja_store'; ?>">
                        <?php if (isset($movimiento)): ?>
                            <input type="hidden" name="id_movimiento" value="<?php echo $movimiento['id_movimiento']; ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="id_factura" class="form-label">Factura</label>
                            <select class="form-select" id="id_factura" name="id_factura" required>
                                <option value="">Seleccione una factura</option>
                                <?php foreach ($facturas as $factura): ?>
                                    <option value="<?= $factura['id_factura'] ?>"
                                            <?php echo isset($movimiento) && $movimiento['id_factura'] == $factura['id_factura'] ? 'selected' : ''; ?>>
                                        Factura #<?= $factura['numero_factura'] ?> - <?= $factura['cliente'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="id_metodo_pago" class="form-label">Método de Pago</label>
                            <select class="form-select" id="id_metodo_pago" name="id_metodo_pago" required>
                                <option value="">Seleccione un método de pago</option>
                                <?php foreach ($metodos_pago as $metodo): ?>
                                    <option value="<?= $metodo['id_metodo_pago'] ?>"
                                            <?php echo isset($movimiento) && $movimiento['id_metodo_pago'] == $metodo['id_metodo_pago'] ? 'selected' : ''; ?>>
                                        <?= $metodo['metodo_pago'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_pago" class="form-label">Fecha de Pago</label>
                            <input type="datetime-local" class="form-control" id="fecha_pago" name="fecha_pago" 
                                   value="<?php echo isset($movimiento) ? date('Y-m-d\TH:i', strtotime($movimiento['fecha_pago'])) : date('Y-m-d\TH:i'); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="monto" class="form-label">Monto</label>
                            <input type="number" step="0.01" class="form-control" id="monto" name="monto" 
                                   value="<?php echo isset($movimiento) ? $movimiento['monto'] : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"><?php echo isset($movimiento) ? htmlspecialchars($movimiento['observaciones']) : ''; ?></textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="?route=movimientos_caja" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary"><?php echo isset($movimiento) ? 'Actualizar' : 'Crear'; ?></button>
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

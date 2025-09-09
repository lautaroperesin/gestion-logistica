<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0"><?php echo (isset($fromEnvio) && $fromEnvio) ? 'Nueva Factura' : (isset($factura) ? 'Editar Factura' : 'Nueva Factura'); ?></h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo (isset($fromEnvio) && $fromEnvio) ? '?route=facturas_store' : (isset($factura) ? '?route=facturas_update' : '?route=facturas_store'); ?>">
                        <?php if (isset($factura)): ?>
                            <input type="hidden" name="id_factura" value="<?php echo $factura['id_factura']; ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="numero_factura" class="form-label">Número de Factura</label>
                            <input type="text" class="form-control" id="numero_factura" name="numero_factura" 
                                   value="<?php echo isset($factura) ? htmlspecialchars($factura['numero_factura']) : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_emision" class="form-label">Fecha de Emisión</label>
                            <input type="date" class="form-control" id="fecha_emision" name="fecha_emision" 
                                   value="<?php echo isset($factura) ? date('Y-m-d', strtotime($factura['fecha_emision'])) : date('Y-m-d'); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                            <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento"
                                   value="<?php echo isset($factura) ? date('Y-m-d', strtotime($factura['fecha_vencimiento'])) : ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label for="id_envio" class="form-label">Envío</label>
                            <?php if (isset($fromEnvio) && $fromEnvio): ?>
                                <input type="hidden" name="id_envio" value="<?= $factura['id_envio'] ?>">
                                <input type="text" class="form-control" value="<?= htmlspecialchars($envio['numero_seguimiento']) ?>" readonly>
                            <?php else: ?>
                                <select class="form-select" id="id_envio" name="id_envio" required>
                                    <option value="">Seleccione un envío</option>
                                    <?php foreach ($envios as $envio): ?>
                                        <option value="<?= $envio['numero_seguimiento'] ?>"
                                                <?php echo isset($factura) && $factura['id_envio'] == $envio['id_envio'] ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($envio['numero_seguimiento']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="id_cliente" class="form-label">Cliente</label>
                            <?php if (isset($fromEnvio) && $fromEnvio): ?>
                                <input type="hidden" name="id_cliente" value="<?= $factura['id_cliente'] ?>">
                                <input type="text" class="form-control" value="<?= htmlspecialchars($factura['cliente'] ?? '') ?>" readonly>
                            <?php else: ?>
                                <select class="form-select" id="id_cliente" name="id_cliente" required>
                                    <option value="">Seleccione un cliente</option>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <option value="<?= $cliente['id_cliente'] ?>"
                                                <?php echo isset($factura) && $factura['id_cliente'] == $cliente['id_cliente'] ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($cliente['cliente']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="1" <?php echo isset($factura) && $factura['estado'] == 1 ? 'selected' : ''; ?>>Emitida</option>
                                <option value="2" <?php echo isset($factura) && $factura['estado'] == 2 ? 'selected' : ''; ?>>Parcialmente Pagada</option>
                                <option value="3" <?php echo isset($factura) && $factura['estado'] == 3 ? 'selected' : ''; ?>>Pagada</option>
                                <option value="4" <?php echo isset($factura) && $factura['estado'] == 4 ? 'selected' : ''; ?>>Vencida</option>
                                <option value="5" <?php echo isset($factura) && $factura['estado'] == 5 ? 'selected' : ''; ?>>Anulada</option>
                            </select>
                            <div class="form-text">
                                <small>
                                    <strong>Nota:</strong> El estado se actualizará automáticamente basado en los pagos realizados.
                                </small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="subtotal" class="form-label">Subtotal</label>
                            <input type="number" step="0.01" class="form-control" id="subtotal" name="subtotal" 
                                   value="<?php echo isset($factura) ? $factura['subtotal'] : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="iva" class="form-label">IVA (%)</label>
                            <!-- detectar si es una factura generada desde un envio -->
                            <?php if (isset($fromEnvio) && $fromEnvio): ?>
                                <input type="number" step="0.01" class="form-control" id="iva" name="iva" value="21" required>
                            <?php else: ?>
                                <input type="number" step="0.01" class="form-control" id="iva" name="iva" 
                                       value="<?php echo isset($factura) ? $factura['iva'] : '21'; ?>" required>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="total" class="form-label">Total</label>
                            <input type="number" step="0.01" class="form-control" id="total" name="total" 
                                   value="<?php echo isset($factura) ? $factura['total'] : ''; ?>" required readonly>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="?route=facturas" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary"><?php echo isset($factura) ? 'Actualizar' : 'Crear'; ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const subtotalInput = document.getElementById('subtotal');
    const ivaInput = document.getElementById('iva');
    const totalInput = document.getElementById('total');

    function calcularTotal() {
        const subtotal = parseFloat(subtotalInput.value) || 0;
        const iva = parseFloat(ivaInput.value) || 0;
        const total = subtotal * (1 + iva / 100);
        totalInput.value = total.toFixed(2);
    }

    subtotalInput.addEventListener('input', calcularTotal);
    ivaInput.addEventListener('input', calcularTotal);
});
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

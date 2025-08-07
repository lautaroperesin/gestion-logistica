<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../config/database.php';

// Obtener el ID de la factura desde la URL
$id_factura = $_GET['id_factura'] ?? null;

// Si no hay ID de factura, redirigir a la lista de facturas
if (!$id_factura) {
    header('Location: ?route=facturas');
    exit;
}

// Obtener la factura
$db = new Database();
$factura = (new Factura($db->getConnection()))->obtenerPorId($id_factura);

// Si la factura no existe, redirigir
if (!$factura) {
    header('Location: ?route=facturas');
    exit;
}

// Calcular saldo pendiente
$movimientoCajaModel = new MovimientoCaja($db->getConnection());
$movimientos = $movimientoCajaModel->obtenerPorFactura($id_factura);
$total_pagado = 0;
foreach ($movimientos as $mov) {
    $total_pagado += floatval($mov['monto']);
}
$saldo_pendiente = floatval($factura['total']) - $total_pagado;
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Registrar Pago - Factura #<?= $factura['numero_factura'] ?></h5>
                        <a href="?route=facturas" class="btn btn-light">
                            <i class="fas fa-arrow-left"></i> Volver a Facturas
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="?route=movimientos_caja_store" method="POST">
                        <input type="hidden" name="id_factura" value="<?= $id_factura ?>">
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Cliente</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($factura['cliente']) ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Fecha Emisión</label>
                                    <input type="text" class="form-control" value="<?= date('d/m/Y', strtotime($factura['fecha_emision'])) ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Total Factura</label>
                                    <input type="text" class="form-control" value="$<?= number_format($factura['total'], 2) ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Saldo Pendiente</label>
                                    <input type="text" class="form-control" value="$<?= number_format($saldo_pendiente, 2) ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Monto a Pagar</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="monto" 
                                               value="<?= $saldo_pendiente ?>" 
                                               max="<?= $saldo_pendiente ?>" 
                                               required
                                               id="monto_input">
                                        <button class="btn btn-outline-success" type="button" onclick="setMonto(100)">100%</button>
                                        <button class="btn btn-outline-success" type="button" onclick="setMonto(50)">50%</button>
                                        <button class="btn btn-outline-success" type="button" onclick="setMonto(25)">25%</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Historial de Pagos -->
                        <?php if (!empty($movimientos)): ?>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Historial de Pagos</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Fecha</th>
                                                        <th>Método</th>
                                                        <th>Monto</th>
                                                        <th>Observaciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($movimientos as $mov): ?>
                                                        <tr>
                                                            <td><?= date('d/m/Y H:i', strtotime($mov['fecha_pago'])) ?></td>
                                                            <td><?= htmlspecialchars($mov['metodo_pago']) ?></td>
                                                            <td>$<?= number_format($mov['monto'], 2) ?></td>
                                                            <td><?= htmlspecialchars($mov['observaciones'] ?? '') ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Método de Pago</label>
                                    <select class="form-select" name="id_metodo_pago" required>
                                        <option value="">Seleccione un método de pago</option>
                                        <?php 
                                        $metodoPagoModel = new MetodoPago($db->getConnection());
                                        $metodos = $metodoPagoModel->obtenerTodos();
                                        foreach ($metodos as $metodo): ?>
                                            <option value="<?= $metodo['id_metodo_pago'] ?>">
                                                <?= htmlspecialchars($metodo['metodo_pago']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Observaciones</label>
                                    <textarea class="form-control" name="observaciones" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="?route=facturas" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> Registrar Pago
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setMonto(porcentaje) {
    const saldo = parseFloat(document.querySelector('.saldo-pendiente').value.replace('$', ''));
    const monto = (saldo * porcentaje) / 100;
    const input = document.getElementById('monto_input');
    input.value = monto.toFixed(2);
    input.dispatchEvent(new Event('change'));
}

// Formatear el monto al cargar la página
window.addEventListener('load', function() {
    const input = document.getElementById('monto_input');
    if (input) {
        const value = parseFloat(input.value);
        input.value = value.toFixed(2);
    }
});

// Formatear el monto al cambiar
const formatCurrency = function(e) {
    const value = parseFloat(e.target.value);
    if (!isNaN(value)) {
        e.target.value = value.toFixed(2);
    }
};

document.getElementById('monto_input').addEventListener('change', formatCurrency);
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

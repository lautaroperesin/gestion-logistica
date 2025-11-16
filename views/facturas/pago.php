<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Factura.php';
require_once __DIR__ . '/../../models/MovimientoCaja.php';
require_once __DIR__ . '/../../models/MetodoPago.php';

// Obtener el ID de la factura desde la URL
$id_factura = $_GET['id_factura'] ?? null;

// Si no hay ID de factura, redirigir a la lista de facturas
if (!$id_factura) {
    header('Location: ?route=facturas');
    exit;
}

// Obtener la factura (incluye saldo pendiente)
$db = new Database();
$factura = (new Factura($db->getConnection()))->obtenerPorId($id_factura);

// Si la factura no existe, redirigir
if (!$factura) {
    header('Location: ?route=facturas');
    exit;
}

// El saldo pendiente ya viene calculado en $factura['saldo_pendiente']
$saldo_pendiente = $factura['saldo_pendiente'];
$total_factura = $factura['total'];
$total_pagado = $total_factura - $saldo_pendiente;
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_GET['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($_GET['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
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
                    <form action="?route=movimientos_caja_store" method="POST" onsubmit="return validarFormulario()">
                        <input type="hidden" name="id_factura" value="<?= $id_factura ?>">
                        <input type="hidden" name="fecha_pago" value="<?= date('Y-m-d H:i:s') ?>">
                        <input type="hidden" id="saldo_pendiente" value="<?= (float)$saldo_pendiente ?>">
                        
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Cliente</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($factura['cliente']) ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Fecha Emisión</label>
                                    <input type="text" class="form-control" value="<?= date('d/m/Y', strtotime($factura['fecha_emision'])) ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Total Factura</label>
                                    <input type="text" class="form-control fw-bold" value="$<?= number_format($factura['total'], 2) ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Saldo Pendiente</label>
                                    <input type="text" class="form-control fw-bold text-danger" id="saldoPendienteDisplay" value="$<?= number_format($saldo_pendiente, 2) ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de Métodos de Pago -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Métodos de Pago</h5>
                                <div id="metodosPago">
                                    <!-- Primer método de pago -->
                                    <div class="metodo-pago-item mb-3 border-bottom pb-3">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Método de Pago</label>
                                                    <select class="form-select" name="id_metodo_pago[]" required onchange="actualizarTipoMetodo(this)">
                                                        <option value="">Seleccione un método de pago</option>
                                                        <?php 
                                                        $metodoPagoModel = new MetodoPago($db->getConnection());
                                                        $metodos = $metodoPagoModel->obtenerTodos();
                                                        foreach ($metodos as $metodo): ?>
                                                            <option value="<?= $metodo['id_metodo_pago'] ?>" data-tipo="<?= htmlspecialchars(strtolower($metodo['metodo_pago'])) ?>">
                                                                <?= htmlspecialchars($metodo['metodo_pago']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">Monto</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" step="0.01" min="0" class="form-control monto-pago text-end" 
                                                               name="monto_metodo[]" 
                                                               value="<?= $saldo_pendiente ?>" 
                                                               required
                                                               oninput="actualizarMontos()">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Observaciones</label>
                                                    <input type="text" class="form-control" 
                                                           name="observaciones_metodo[]">
                                                </div>
                                            </div>
                                            <div class="col-md-1 d-flex align-items-end">
                                                <button type="button" class="btn btn-outline-danger btn-sm mb-3" onclick="eliminarMetodoPago(this)" disabled>
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="agregarMetodoPago()">
                                            <i class="fas fa-plus"></i> Agregar Forma de Pago
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Historial de Pagos -->
                        <?php if (!empty($factura['movimientos'])): ?>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0">Historial de Pagos</h5>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover mb-0 text-end">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Fecha</th>
                                                            <th>Método de Pago</th>
                                                            <th class="text-end">Monto</th>
                                                            <th>Observaciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($factura['movimientos'] as $mov): ?>
                                                            <tr>
                                                                <td><?= date('d/m/Y', strtotime($mov['fecha_pago'])) ?></td>
                                                                <td><?= htmlspecialchars($mov['metodo_pago']) ?></td>
                                                                <td class="text-end">$<?= number_format($mov['monto'], 2) ?></td>
                                                                <td><?= htmlspecialchars($mov['observaciones'] ?? '') ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

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
// Función para formatear números con dos decimales
function formatNumber(num) {
    return parseFloat(num).toFixed(2);
}

// Función para obtener el saldo pendiente desde el hidden input
function getSaldoPendiente() {
    const saldoElement = document.getElementById('saldo_pendiente');
    if (!saldoElement) return 0;
    return parseFloat(saldoElement.value) || 0;
}

// Función para agregar un nuevo método de pago
function agregarMetodoPago() {
    const metodosPago = document.getElementById('metodosPago');
    
    // Verificar si ya hay un método de pago vacío
    const metodosVacios = Array.from(document.querySelectorAll('.metodo-pago-item')).some(item => {
        const select = item.querySelector('select');
        return !select.value;
    });
    
    if (metodosVacios) {
        alert('Por favor complete el método de pago actual antes de agregar uno nuevo.');
        return;
    }
    
    // Clonar el primer método de pago
    const nuevoMetodo = document.querySelector('.metodo-pago-item').cloneNode(true);
    
    // Limpiar valores
    const select = nuevoMetodo.querySelector('select');
    const inputMonto = nuevoMetodo.querySelector('input[type="number"]');
    const inputObservaciones = nuevoMetodo.querySelector('input[type="text"]');
    const btnEliminar = nuevoMetodo.querySelector('button');
    
    select.value = '';
    select.required = true;
    inputMonto.value = '0';
    inputMonto.max = '';
    inputObservaciones.value = '';
    btnEliminar.disabled = false;
    
    // Ocultar detalles adicionales
    const detalles = nuevoMetodo.querySelector('.detalles-adicionales');
    if (detalles) {
        detalles.style.display = 'none';
    }
    
    // Agregar el nuevo método
    metodosPago.appendChild(nuevoMetodo);
    
    // Desplazarse al nuevo método
    nuevoMetodo.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    
    // Actualizar máximos
    actualizarMontos();
    
    // Enfocar el select del nuevo método
    select.focus();
}

// Función para eliminar un método de pago
function eliminarMetodoPago(btn) {
    const item = btn.closest('.metodo-pago-item');
    if (document.querySelectorAll('.metodo-pago-item').length > 1) {
        item.remove();
        actualizarMontos();
    }
}

// Función para actualizar el tipo de método de pago
function actualizarTipoMetodo(select) {
    const item = select.closest('.metodo-pago-item');
    const detalles = item.querySelector('.detalles-adicionales');
    const tipo = select.options[select.selectedIndex].dataset.tipo || '';
}

// Función para validar el formulario antes de enviar
function validarFormulario() {
    const saldoPendiente = getSaldoPendiente();
    const montoInputs = document.querySelectorAll('.monto-pago');
    let totalPago = 0;
    
    // Calcular el total a pagar
    montoInputs.forEach(input => {
        totalPago += parseFloat(input.value) || 0;
    });
    
    // Validar que el total no exceda el saldo pendiente
    if (totalPago > saldoPendiente) {
        alert(`El total a pagar ($${formatNumber(totalPago)}) excede el saldo pendiente ($${formatNumber(saldoPendiente)}). Por favor, ajuste los montos.`);
        return false;
    }
    
    return true;
}

// Función para actualizar los montos y validaciones
function actualizarMontos() {
    const saldoPendiente = getSaldoPendiente();
    const montoInputs = document.querySelectorAll('.monto-pago');
    const totalPagar = document.getElementById('totalPagar');
    const saldoRestante = document.getElementById('saldoRestante');
    const saldoPendienteDisplay = document.getElementById('saldoPendienteDisplay');
    
    // Calcular el total pagado
    let totalPago = 0;
    montoInputs.forEach(input => {
        totalPago += parseFloat(input.value) || 0;
    });
    
    const saldo = Math.max(0, saldoPendiente - totalPago);
    
    // Actualizar total y saldo restante
    totalPagar.textContent = '$' + formatNumber(totalPago);
    saldoRestante.textContent = '$' + formatNumber(saldo);
    
    // Actualizar el saldo pendiente después del pago
    if (saldoPendienteDisplay) {
        saldoPendienteDisplay.value = '$' + formatNumber(saldo);
        // Cambiar color según el saldo
        if (saldo === 0) {
            saldoPendienteDisplay.classList.remove('text-danger');
            saldoPendienteDisplay.classList.add('text-success');
        } else {
            saldoPendienteDisplay.classList.remove('text-success');
            saldoPendienteDisplay.classList.add('text-danger');
        }
    }
    
    // Actualizar máximos para cada input (solo para referencia, no bloquea)
    montoInputs.forEach((input, index) => {
        let maxMonto = saldoPendiente;
        
        // Restar los otros montos al máximo
        montoInputs.forEach((otherInput, otherIndex) => {
            if (index !== otherIndex) {
                maxMonto -= parseFloat(otherInput.value) || 0;
            }
        });
        
        input.max = maxMonto > 0 ? maxMonto : 0;
    });
    
    // Advertencia visual si se excede el saldo (sin mostrar negativo)
    if (totalPago > saldoPendiente) {
        saldoRestante.classList.add('text-danger');
    } else {
        saldoRestante.classList.remove('text-danger');
    }
    
    // Actualizar botones de eliminar
    const btnsEliminar = document.querySelectorAll('.btn-outline-danger');
    btnsEliminar.forEach((btn, index) => {
        btn.disabled = (index === 0 && btnsEliminar.length === 1);
    });
}

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    actualizarMontos();
});
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

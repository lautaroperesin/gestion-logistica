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

    <!-- Filtros de búsqueda -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filtros de búsqueda</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="mb-4" id="filtro-form">
                <input type="hidden" name="route" value="facturas">
                <input type="hidden" name="page" value="1"> 
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="numero_factura" class="form-label">N° Factura</label>
                        <input type="text" class="form-control" id="numero_factura" name="numero_factura" value="<?= htmlspecialchars($_GET['numero_factura'] ?? '') ?>">
                    </div>
                
                    <div class="col-md-3">
                        <label for="id_cliente" class="form-label">Cliente</label>
                        <select class="form-select" id="id_cliente" name="id_cliente">
                            <option value="">Todos los clientes</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= $cliente['id_cliente'] ?>" <?= (isset($_GET['id_cliente']) && $_GET['id_cliente'] == $cliente['id_cliente']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cliente['cliente']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                
                    <div class="col-md-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="">Todos los estados</option>
                            <option value="1" <?= (isset($_GET['estado']) && $_GET['estado'] === '1') ? 'selected' : '' ?>>Emitida</option>
                            <option value="2" <?= (isset($_GET['estado']) && $_GET['estado'] === '2') ? 'selected' : '' ?>>Parcialmente Pagada</option>
                            <option value="3" <?= (isset($_GET['estado']) && $_GET['estado'] === '3') ? 'selected' : '' ?>>Pagada</option>
                            <option value="4" <?= (isset($_GET['estado']) && $_GET['estado'] === '4') ? 'selected' : '' ?>>Vencida</option>
                            <option value="5" <?= (isset($_GET['estado']) && $_GET['estado'] === '5') ? 'selected' : '' ?>>Anulada</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="fecha_desde" class="form-label">Fecha emisión desde</label>
                        <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" 
                               value="<?= htmlspecialchars($_GET['fecha_desde'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="fecha_hasta" class="form-label">Fecha emisión hasta</label>
                        <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" 
                               value="<?= htmlspecialchars($_GET['fecha_hasta'] ?? '') ?>">
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="?route=facturas" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
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
                                      <form action="?route=facturas_delete" method="POST" class="d-inline">
                                        <input type="hidden" name="id_factura" value="<?= $factura['id_factura'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar esta factura?')"><i class="fas fa-trash"></i></button>
                                    </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <?php if ($paginacion['total_paginas'] > 1): ?>
            <?php
            // Crear un array con los parámetros actuales del GET
            $params = [];
            $ignoreParams = ['page'];
            
            // Obtener todos los parámetros de filtro del GET
            foreach ($_GET as $key => $value) {
                if (!in_array($key, $ignoreParams) && !empty($value)) {
                    $params[$key] = $value;
                }
            }
            ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <!-- Botón Anterior -->
                    <li class="page-item <?= $paginacion['pagina_actual'] <= 1 ? 'disabled' : '' ?>">
                        <?php if ($paginacion['pagina_actual'] > 1): ?>
                            <a class="page-link" href="?<?= http_build_query(array_merge($params, ['page' => $paginacion['pagina_actual'] - 1])) ?>">Anterior</a>
                        <?php else: ?>
                            <span class="page-link">Anterior</span>
                        <?php endif; ?>
                    </li>
                    
                    <!-- Números de página -->
                    <?php for ($i = 1; $i <= $paginacion['total_paginas']; $i++): ?>
                        <li class="page-item <?= $i == $paginacion['pagina_actual'] ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($params, ['page' => $i])) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <!-- Botón Siguiente -->
                    <li class="page-item <?= $paginacion['pagina_actual'] >= $paginacion['total_paginas'] ? 'disabled' : '' ?>">
                        <?php if ($paginacion['pagina_actual'] < $paginacion['total_paginas']): ?>
                            <a class="page-link" href="?<?= http_build_query(array_merge($params, ['page' => $paginacion['pagina_actual'] + 1])) ?>">Siguiente</a>
                        <?php else: ?>
                            <span class="page-link">Siguiente</span>
                        <?php endif; ?>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
            
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

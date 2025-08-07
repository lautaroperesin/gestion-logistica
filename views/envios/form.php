<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0"><?php echo isset($envio) ? 'Editar Envío' : 'Nuevo Envío'; ?></h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo isset($envio) ? '../envios/update/' . $envio['id_envio'] : '../envios/store'; ?>">
                        <div class="mb-3">
                            <label for="numero_seguimiento" class="form-label">Número de Seguimiento</label>
                            <input type="text" class="form-control" id="numero_seguimiento" name="numero_seguimiento" 
                                   value="<?php echo isset($envio) ? htmlspecialchars($envio['numero_seguimiento']) : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="id_origen" class="form-label">Origen</label>
                            <select class="form-select" id="id_origen" name="id_origen" required>
                                <option value="">Seleccione un origen</option>
                                <!-- Aquí irían las opciones de ubicaciones cuando se implemente el módulo -->
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="id_destino" class="form-label">Destino</label>
                            <select class="form-select" id="id_destino" name="id_destino" required>
                                <option value="">Seleccione un destino</option>
                                <!-- Aquí irían las opciones de ubicaciones cuando se implemente el módulo -->
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_salida" class="form-label">Fecha de Salida</label>
                            <input type="datetime-local" class="form-control" id="fecha_salida" name="fecha_salida" 
                                   value="<?php echo isset($envio) ? date('Y-m-d\TH:i', strtotime($envio['fecha_salida'])) : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="peso_kg" class="form-label">Peso (kg)</label>
                            <input type="number" step="0.01" class="form-control" id="peso_kg" name="peso_kg" 
                                   value="<?php echo isset($envio) ? $envio['peso_kg'] : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="volumen_m3" class="form-label">Volumen (m³)</label>
                            <input type="number" step="0.01" class="form-control" id="volumen_m3" name="volumen_m3" 
                                   value="<?php echo isset($envio) ? $envio['volumen_m3'] : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="costo_total" class="form-label">Costo Total</label>
                            <input type="number" step="0.01" class="form-control" id="costo_total" name="costo_total" 
                                   value="<?php echo isset($envio) ? $envio['costo_total'] : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?php echo isset($envio) ? htmlspecialchars($envio['descripcion']) : ''; ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="id_estado_envio" class="form-label">Estado</label>
                            <select class="form-select" id="id_estado_envio" name="id_estado_envio" required>
                                <option value="">Seleccione un estado</option>
                                <!-- Aquí irían las opciones de estados cuando se implemente el módulo -->
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="id_vehiculo" class="form-label">Vehículo</label>
                            <select class="form-select" id="id_vehiculo" name="id_vehiculo" required>
                                <option value="">Seleccione un vehículo</option>
                                <?php foreach ($vehiculos as $vehiculo): ?>
                                    <option value="<?= $vehiculo['id_vehiculo'] ?>" 
                                            <?php echo isset($envio) && $envio['id_vehiculo'] == $vehiculo['id_vehiculo'] ? 'selected' : ''; ?>>
                                        <?= $vehiculo['patente'] ?> - <?= $vehiculo['marca'] ?> <?= $vehiculo['modelo'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="id_conductor" class="form-label">Conductor</label>
                            <select class="form-select" id="id_conductor" name="id_conductor" required>
                                <option value="">Seleccione un conductor</option>
                                <?php foreach ($conductores as $conductor): ?>
                                    <option value="<?= $conductor['id_conductor'] ?>" 
                                            <?php echo isset($envio) && $envio['id_conductor'] == $conductor['id_conductor'] ? 'selected' : ''; ?>>
                                        <?= $conductor['conductor'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="id_cliente" class="form-label">Cliente</label>
                            <select class="form-select" id="id_cliente" name="id_cliente" required>
                                <option value="">Seleccione un cliente</option>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?= $cliente['id_cliente'] ?>" 
                                            <?php echo isset($envio) && $envio['id_cliente'] == $cliente['id_cliente'] ? 'selected' : ''; ?>>
                                        <?= $cliente['cliente'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="id_tipo_carga" class="form-label">Tipo de Carga</label>
                            <select class="form-select" id="id_tipo_carga" name="id_tipo_carga" required>
                                <option value="">Seleccione un tipo de carga</option>
                                <?php foreach ($tiposCarga as $tipo): ?>
                                    <option value="<?= $tipo['id_tipo_carga'] ?>" 
                                            <?php echo isset($envio) && $envio['id_tipo_carga'] == $tipo['id_tipo_carga'] ? 'selected' : ''; ?>>
                                        <?= $tipo['carga'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="?route=envios" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary"><?php echo isset($envio) ? 'Actualizar' : 'Crear'; ?></button>
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

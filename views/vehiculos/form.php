<?php
// Determinar si es edición o creación
$isEdit = isset($vehiculo) && !empty($vehiculo['id_vehiculo']);
$mode = $isEdit ? 'edit' : 'create';
?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header <?= $isEdit ? 'bg-warning' : 'bg-primary' ?> text-dark">
                    <h4 class="mb-0">
                        <i class="fas <?= $isEdit ? 'fa-car-side' : 'fa-car' ?>"></i> 
                        <?= $isEdit ? 'Editar Vehículo' : 'Nuevo Vehículo' ?>
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_GET['error']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="?route=<?= $isEdit ? 'vehiculos_update' : 'vehiculos_store' ?>" method="POST" id="vehiculoForm">
                        <?php if ($isEdit): ?>
                            <input type="hidden" name="id_vehiculo" value="<?= htmlspecialchars($vehiculo['id_vehiculo']) ?>">
                            <div class="mb-3">
                                <label for="id_display" class="form-label">
                                    <i class="fas fa-hashtag"></i> ID
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="id_display" 
                                       value="<?= htmlspecialchars($vehiculo['id_vehiculo']) ?>" 
                                       disabled>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="patente" class="form-label">
                                <i class="fas fa-car"></i> Patente *
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="patente" 
                                   name="patente" 
                                   required 
                                   placeholder="Ingrese la patente"
                                   value="<?= $isEdit ? htmlspecialchars($vehiculo['patente']) : htmlspecialchars($_POST['patente'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="marca" class="form-label">
                                <i class="fas fa-car-side"></i> Marca *
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="marca" 
                                   name="marca" 
                                   required 
                                   placeholder="Ingrese la marca"
                                   value="<?= $isEdit ? htmlspecialchars($vehiculo['marca']) : htmlspecialchars($_POST['marca'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="modelo" class="form-label">
                                <i class="fas fa-car-alt"></i> Modelo *
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="modelo" 
                                   name="modelo" 
                                   required 
                                   placeholder="Ingrese el modelo"
                                   value="<?= $isEdit ? htmlspecialchars($vehiculo['modelo']) : htmlspecialchars($_POST['modelo'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="capacidad_kg" class="form-label">
                                <i class="fas fa-weight"></i> Capacidad (kg) *
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="capacidad_kg" 
                                   name="capacidad_kg" 
                                   required 
                                   step="0.1"
                                   min="0"
                                   placeholder="Ingrese la capacidad en kg"
                                   value="<?= $isEdit ? htmlspecialchars($vehiculo['capacidad_kg']) : htmlspecialchars($_POST['capacidad_kg'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="ultima_inspeccion" class="form-label">
                                <i class="fas fa-check-circle"></i> Última Inspección *
                            </label>
                            <input type="date" 
                                   class="form-control" 
                                   id="ultima_inspeccion" 
                                   name="ultima_inspeccion" 
                                   required
                                   value="<?= $isEdit ? htmlspecialchars($vehiculo['ultima_inspeccion']) : htmlspecialchars($_POST['ultima_inspeccion'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="estado_vehiculo" class="form-label">
                                <i class="fas fa-toggle-on"></i> Estado *
                            </label>
                            <select class="form-select" id="estado_vehiculo" name="estado_vehiculo" required>
                                <option value="" disabled selected>Seleccione el estado</option>
                                <option value="1" <?= ($isEdit && $vehiculo['estado_vehiculo'] == 1) || (!isset($vehiculo) && $_POST['estado_vehiculo'] == 1) ? 'selected' : '' ?>>Disponible</option>
                                <option value="0" <?= ($isEdit && $vehiculo['estado_vehiculo'] == 0) || (!isset($vehiculo) && $_POST['estado_vehiculo'] == 0) ? 'selected' : '' ?>>No Disponible</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="rto_vencimiento" class="form-label">
                                <i class="fas fa-calendar"></i> Vencimiento RTO *
                            </label>
                            <input type="date" 
                                   class="form-control" 
                                   id="rto_vencimiento" 
                                   name="rto_vencimiento" 
                                   required
                                   value="<?= $isEdit ? htmlspecialchars($vehiculo['rto_vencimiento']) : htmlspecialchars($_POST['rto_vencimiento'] ?? '') ?>">
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="?route=vehiculos" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn <?= $isEdit ? 'btn-warning' : 'btn-primary' ?>">
                                <i class="fas fa-save"></i> <?= $isEdit ? 'Actualizar Vehículo' : 'Guardar Vehículo' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Validación del formulario
    document.getElementById('vehiculoForm').addEventListener('submit', function(e) {
        const patente = document.getElementById('patente').value.trim();
        const marca = document.getElementById('marca').value.trim();
        const modelo = document.getElementById('modelo').value.trim();
        const capacidad = document.getElementById('capacidad_kg').value.trim();
        const ultimaInspeccion = document.getElementById('ultima_inspeccion').value;
        const estado = document.getElementById('estado_vehiculo').value;
        const rtoVencimiento = document.getElementById('rto_vencimiento').value;
        
        if (!patente) {
            e.preventDefault();
            alert('La patente es obligatoria');
            document.getElementById('patente').focus();
            return;
        }
        
        if (!marca) {
            e.preventDefault();
            alert('La marca es obligatoria');
            document.getElementById('marca').focus();
            return;
        }
        
        if (!modelo) {
            e.preventDefault();
            alert('El modelo es obligatorio');
            document.getElementById('modelo').focus();
            return;
        }
        
        if (!capacidad) {
            e.preventDefault();
            alert('La capacidad es obligatoria');
            document.getElementById('capacidad_kg').focus();
            return;
        }
        
        if (!ultimaInspeccion) {
            e.preventDefault();
            alert('La fecha de última inspección es obligatoria');
            document.getElementById('ultima_inspeccion').focus();
            return;
        }
        
        if (!estado) {
            e.preventDefault();
            alert('El estado es obligatorio');
            document.getElementById('estado_vehiculo').focus();
            return;
        }
        
        if (!rtoVencimiento) {
            e.preventDefault();
            alert('La fecha de vencimiento del RTO es obligatoria');
            document.getElementById('rto_vencimiento').focus();
            return;
        }
        
        <?php if ($isEdit): ?>
        // Confirmación antes de actualizar
        if (!confirm('¿Está seguro de que desea actualizar este vehículo?')) {
            e.preventDefault();
        }
        <?php endif; ?>
    });
</script>

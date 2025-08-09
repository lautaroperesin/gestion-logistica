<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-car"></i> Lista de Vehículos</h2>
        <a href="?route=vehiculos_create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Vehículo
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patente</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Capacidad (kg)</th>
                            <th>Última Inspección</th>
                            <th>Estado</th>
                            <th>Venc. RTO</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehiculos as $vehiculo): ?>
                            <tr>
                                <td><?= htmlspecialchars($vehiculo['id_vehiculo']) ?></td>
                                <td><?= htmlspecialchars($vehiculo['patente']) ?></td>
                                <td><?= htmlspecialchars($vehiculo['marca']) ?></td>
                                <td><?= htmlspecialchars($vehiculo['modelo']) ?></td>
                                <td><?= htmlspecialchars($vehiculo['capacidad_kg']) ?> kg</td>
                                <td><?= date('d/m/Y', strtotime($vehiculo['ultima_inspeccion'])) ?></td>
                                <td>
                                    <span class="badge 
                                    <?= 
                                        $vehiculo['estado_vehiculo'] == 1 ? 'bg-success' : 
                                        ($vehiculo['estado_vehiculo'] == 2 ? 'bg-warning text-dark' : 
                                        ($vehiculo['estado_vehiculo'] == 3 ? 'bg-secondary' : 'bg-dark'))
                                    ?>">
                                    <?= 
                                        $vehiculo['estado_vehiculo'] == 1 ? 'Disponible' : 
                                        ($vehiculo['estado_vehiculo'] == 2 ? 'En Viaje' : 
                                        ($vehiculo['estado_vehiculo'] == 3 ? 'En Mantenimiento' : 'Desconocido'))
                                    ?>
                                </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($vehiculo['rto_vencimiento'])) ?></td>
                                <td>
                                     <div class="btn-group">
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#cambiarEstadoModal" onclick="cargarVehiculo(<?= $vehiculo['id_vehiculo'] ?>, <?= $vehiculo['estado_vehiculo'] ?>)">
                                            <i class="fas fa-sync"></i>
                                        </button>
                                        <a href="?route=vehiculos_edit&id_vehiculo=<?= $vehiculo['id_vehiculo'] ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?route=vehiculos_delete&id_vehiculo=<?= $vehiculo['id_vehiculo'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de que desea eliminar este vehículo?')">
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

<!-- Modal para cambiar estado -->
<div class="modal fade" id="cambiarEstadoModal" tabindex="-1" aria-labelledby="cambiarEstadoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cambiarEstadoModalLabel">Cambiar Estado del Vehículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formCambiarEstado" method="POST" action="?route=vehiculos_cambiar_estado">
                    <input type="hidden" id="id_vehiculo" name="id_vehiculo" value="">
                    <div class="mb-3">
                        <label for="estado" class="form-label">Nuevo Estado</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="1">Disponible</option>
                            <option value="2">En Viaje</option>
                            <option value="3">En Mantenimiento</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formCambiarEstado" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<script>
function cargarVehiculo(id, estadoActual) {
    document.getElementById('id_vehiculo').value = id;
    document.getElementById('estado').value = estadoActual;
}

// Manejar el envío del formulario
$(document).ready(function() {
    $('#formCambiarEstado').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                // Recargar la página para ver los cambios
                window.location.href = '?route=vehiculos&success=Estado actualizado correctamente';
            },
            error: function() {
                window.location.href = '?route=vehiculos&error=Error al actualizar el estado';
            }
        });
    });
});
</script>
</div>
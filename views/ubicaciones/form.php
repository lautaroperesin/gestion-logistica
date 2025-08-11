<?php
require_once __DIR__ . '/../layouts/header.php';
?>
<script src="js/ubicaciones.js"></script>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0"><?php echo isset($ubicacion) ? 'Editar Ubicación' : 'Nueva Ubicación'; ?></h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo isset($ubicacion) ? '?route=ubicaciones_update' : '?route=ubicaciones_store'; ?>">
                        <?php if (isset($ubicacion)): ?>
                            <input type="hidden" name="id_ubicacion" value="<?php echo $ubicacion['id_ubicacion']; ?>">
                            
                            <!-- Mostrar información de ubicación en modo lectura -->
                            <div class="mb-4">
                                <h5>Ubicación</h5>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <p class="mb-1"><strong>País:</strong> <?= htmlspecialchars($ubicacion['pais'] ?? '') ?></p>
                                        <p class="mb-1"><strong>Provincia:</strong> <?= htmlspecialchars($ubicacion['provincia'] ?? '') ?></p>
                                        <p class="mb-0"><strong>Localidad:</strong> <?= htmlspecialchars($ubicacion['localidad'] ?? '') ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" 
                                       value="<?= htmlspecialchars($ubicacion['direccion'] ?? '') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= htmlspecialchars($ubicacion['descripcion'] ?? '') ?></textarea>
                            </div>
                        
                        <?php else: ?>
                            <!-- Formulario para nueva ubicación -->
                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" required>
                            </div>

                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                            </div>

                        <!-- Selects en cascada solo para nueva ubicación -->
                        <?php if (!isset($ubicacion)): ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="id_pais" class="form-label">País</label>
                                        <select class="form-select" id="id_pais" name="id_pais" required>
                                            <option value="">Seleccione un país</option>
                                            <?php foreach ($paises as $pais): ?>
                                                <option value="<?= $pais['id_pais'] ?>">
                                                    <?= htmlspecialchars($pais['pais']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="id_provincia" class="form-label">Provincia</label>
                                        <select class="form-select" id="id_provincia" name="id_provincia" required>
                                            <option value="">Seleccione una provincia</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="id_localidad" class="form-label">Localidad</label>
                                        <select class="form-select" id="id_localidad" name="id_localidad" required>
                                            <option value="">Seleccione una localidad</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php endif; ?>

                        <div class="d-flex justify-content-end">
                            <a href="?route=ubicaciones" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary"><?php echo isset($ubicacion) ? 'Actualizar' : 'Crear'; ?></button>
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

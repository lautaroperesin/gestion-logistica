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
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" 
                                   value="<?php echo isset($ubicacion) ? htmlspecialchars($ubicacion['direccion']) : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?php echo isset($ubicacion) ? htmlspecialchars($ubicacion['descripcion']) : ''; ?></textarea>
                        </div>

                        <!-- Selects en cascada -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="id_pais" class="form-label">País</label>
                                    <select class="form-select" id="id_pais" name="id_pais" required>
                                        <option value="">Seleccione un país</option>
                                        <?php foreach ($paises as $pais): ?>
                                            <option value="<?= $pais['id_pais'] ?>" 
                                                    <?php echo isset($ubicacion) && $ubicacion['pais'] == $pais['pais'] ? 'selected' : ''; ?>>
                                                <?= $pais['pais'] ?>
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
                                        <?php 
                                        if (isset($ubicacion) && isset($ubicacion['id_pais'])) {
                                            $provincias = (new Ubicacion())->obtenerProvinciasPorPais($ubicacion['id_pais']);
                                            if ($provincias) {
                                                foreach ($provincias as $provincia): ?>
                                                    <option value="<?= $provincia['id'] ?>" 
                                                            <?php echo isset($ubicacion) && isset($ubicacion['id_provincia']) && $ubicacion['id_provincia'] == $provincia['id'] ? 'selected' : ''; ?>>
                                                        <?= $provincia['provincia'] ?>
                                                    </option>
                                                <?php endforeach;
                                            }
                                        } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="id_localidad" class="form-label">Localidad</label>
                                    <select class="form-select" id="id_localidad" name="id_localidad" required>
                                        <option value="">Seleccione una localidad</option>
                                        <?php 
                                        if (isset($ubicacion) && isset($ubicacion['id_provincia'])) {
                                            $localidades = (new Ubicacion())->obtenerLocalidadesPorProvincia($ubicacion['id_provincia']);
                                            if ($localidades) {
                                                foreach ($localidades as $localidad): ?>
                                                    <option value="<?= $localidad['id_localidad'] ?>" 
                                                            <?php echo isset($ubicacion) && isset($ubicacion['id_localidad']) && $ubicacion['id_localidad'] == $localidad['id_localidad'] ? 'selected' : ''; ?>>
                                                        <?= $localidad['localidad'] ?>
                                                    </option>
                                                <?php endforeach;
                                            }
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

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

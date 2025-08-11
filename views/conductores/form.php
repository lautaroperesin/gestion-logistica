<?php
// Determinar si es edición o creación
$isEdit = isset($conductor) && !empty($conductor['id_conductor']);
$mode = $isEdit ? 'edit' : 'create';
?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header <?= $isEdit ? 'bg-warning' : 'bg-primary' ?> text-dark">
                    <h4 class="mb-0">
                        <i class="fas <?= $isEdit ? 'fa-user-edit' : 'fa-user-plus' ?>"></i> 
                        <?= $isEdit ? 'Editar Conductor' : 'Nuevo Conductor' ?>
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_GET['error']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="?route=<?= $isEdit ? 'conductores_update' : 'conductores_store' ?>" method="POST" id="conductorForm">
                        <?php if ($isEdit): ?>
                            <input type="hidden" name="id_conductor" value="<?= htmlspecialchars($conductor['id_conductor']) ?>">
                            <div class="mb-3">
                                <label for="id_display" class="form-label">
                                    <i class="fas fa-hashtag"></i> ID
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="id_display" 
                                       value="<?= htmlspecialchars($conductor['id_conductor']) ?>" 
                                       disabled>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="nombre" class="form-label">
                                <i class="fas fa-user"></i> Nombre y apellido *
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="nombre" 
                                   name="conductor" 
                                   required 
                                   placeholder="Ingrese el nombre completo del conductor"
                                   value="<?= $isEdit ? htmlspecialchars($conductor['conductor']) : htmlspecialchars($_POST['conductor'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Email *
                            </label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   required 
                                   placeholder="ejemplo@correo.com"
                                   value="<?= $isEdit ? htmlspecialchars($conductor['email']) : htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="telefono" class="form-label">
                                <i class="fas fa-phone"></i> Teléfono
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="telefono" 
                                   name="telefono" 
                                   placeholder="Ingrese el teléfono del conductor"
                                   value="<?= $isEdit ? htmlspecialchars($conductor['telefono']) : htmlspecialchars($_POST['telefono'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="dni" class="form-label">
                                <i class="fas fa-id-card"></i> DNI *
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="dni" 
                                   name="dni" 
                                   required 
                                   placeholder="Ingrese el DNI"
                                   value="<?= $isEdit ? htmlspecialchars($conductor['dni']) : htmlspecialchars($_POST['dni'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="clase_licencia" class="form-label">
                                <i class="fas fa-car"></i> Clase de Licencia *
                            </label>
                            <?php
                            $clases_licencia = ['B1', 'B2', 'C1', 'C2', 'C3', 'E1'];
                            $clase_actual = $isEdit ? $conductor['clase_licencia'] : ($_POST['clase_licencia'] ?? '');
                            ?>
                            <select class="form-select" id="clase_licencia" name="clase_licencia" required>
                                <option value="" disabled <?= empty($clase_actual) ? 'selected' : '' ?>>Seleccione una clase de licencia</option>
                                <?php foreach ($clases_licencia as $clase): ?>
                                    <option value="<?= $clase ?>" <?= $clase_actual === $clase ? 'selected' : '' ?>><?= $clase ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="vencimiento_licencia" class="form-label">
                                <i class="fas fa-calendar"></i> Vencimiento Licencia *
                            </label>
                            <?php
                            $fecha_vencimiento = '';
                            if ($isEdit && !empty($conductor['vencimiento_licencia'])) {
                                // Convertir la fecha al formato YYYY-MM-DD si no está vacía
                                $fecha = new DateTime($conductor['vencimiento_licencia']);
                                $fecha_vencimiento = $fecha->format('Y-m-d');
                            } elseif (isset($_POST['vencimiento_licencia'])) {
                                $fecha_vencimiento = htmlspecialchars($_POST['vencimiento_licencia']);
                            }
                            ?>
                            <input type="date" 
                                   class="form-control" 
                                   id="vencimiento_licencia" 
                                   name="vencimiento_licencia" 
                                   required
                                   value="<?= $fecha_vencimiento ?>">
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="?route=conductores" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn <?= $isEdit ? 'btn-warning' : 'btn-primary' ?>">
                                <i class="fas fa-save"></i> <?= $isEdit ? 'Actualizar Conductor' : 'Guardar Conductor' ?>
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
    document.getElementById('conductorForm').addEventListener('submit', function(e) {
        const nombre = document.getElementById('nombre').value.trim();
        const email = document.getElementById('email').value.trim();
        const dni = document.getElementById('dni').value.trim();
        const claseLicencia = document.getElementById('clase_licencia').value.trim();
        const vencimientoLicencia = document.getElementById('vencimiento_licencia').value;
        
        if (!nombre) {
            e.preventDefault();
            alert('El nombre es obligatorio');
            document.getElementById('nombre').focus();
            return;
        }
        
        if (!email) {
            e.preventDefault();
            alert('El email es obligatorio');
            document.getElementById('email').focus();
            return;
        }
        
        if (!dni) {
            e.preventDefault();
            alert('El DNI es obligatorio');
            document.getElementById('dni').focus();
            return;
        }
        
        if (!claseLicencia) {
            e.preventDefault();
            alert('La clase de licencia es obligatoria');
            document.getElementById('clase_licencia').focus();
            return;
        }
        
        if (!vencimientoLicencia) {
            e.preventDefault();
            alert('La fecha de vencimiento de la licencia es obligatoria');
            document.getElementById('vencimiento_licencia').focus();
            return;
        }
        
        // Validación básica de email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Ingrese un email válido');
            document.getElementById('email').focus();
            return;
        }
    });
</script>

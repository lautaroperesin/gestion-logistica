<?php
// Determinar si es edición o creación
$isEdit = isset($tipo_carga) && !empty($tipo_carga['id_tipo_carga']);
$mode = $isEdit ? 'edit' : 'create';
?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header <?= $isEdit ? 'bg-warning' : 'bg-primary' ?> text-dark">
                    <h4 class="mb-0">
                        <i class="fas <?= $isEdit ? 'fa-edit' : 'fa-plus' ?>"></i> 
                        <?= $isEdit ? 'Editar Tipo de Carga' : 'Nuevo Tipo de Carga' ?>
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_GET['error']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="?route=<?= $isEdit ? 'tipos_carga_update' : 'tipos_carga_store' ?>" method="POST" id="tipoCargaForm">
                        <?php if ($isEdit): ?>
                            <input type="hidden" name="id_tipo_carga" value="<?= htmlspecialchars($tipo_carga['id_tipo_carga']) ?>">
                            <div class="mb-3">
                                <label for="id_display" class="form-label">
                                    <i class="fas fa-hashtag"></i> ID
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="id_display" 
                                       value="<?= htmlspecialchars($tipo_carga['id_tipo_carga']) ?>" 
                                       disabled>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="carga" class="form-label">
                                <i class="fas fa-box"></i> Tipo de Carga *
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="carga" 
                                   name="carga" 
                                   required 
                                   placeholder="Ingrese el tipo de carga"
                                   value="<?= $isEdit ? htmlspecialchars($tipo_carga['carga']) : htmlspecialchars($_POST['carga'] ?? '') ?>">
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="?route=tipos_carga" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn <?= $isEdit ? 'btn-warning' : 'btn-primary' ?>">
                                <i class="fas fa-save"></i> <?= $isEdit ? 'Actualizar Tipo de Carga' : 'Guardar Tipo de Carga' ?>
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
    document.getElementById('tipoCargaForm').addEventListener('submit', function(e) {
        const carga = document.getElementById('carga').value.trim();
        
        if (!carga) {
            e.preventDefault();
            alert('El nombre del tipo de carga es requerido');
            document.getElementById('carga').focus();
            return;
        }
        
        <?php if ($isEdit): ?>
        // Confirmación antes de actualizar
        if (!confirm('¿Está seguro de que desea actualizar este tipo de carga?')) {
            e.preventDefault();
        }
        <?php endif; ?>
    });
</script>

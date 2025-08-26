<?php
// Determinar si es edición o creación
$isEdit = isset($cliente) && !empty($cliente['id_cliente']);
$mode = $isEdit ? 'edit' : 'create';
?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header <?= $isEdit ? 'bg-warning' : 'bg-primary' ?> text-dark">
                    <h4 class="mb-0">
                        <i class="fas <?= $isEdit ? 'fa-user-edit' : 'fa-user-plus' ?>"></i> 
                        <?= $isEdit ? 'Editar Cliente' : 'Nuevo Cliente' ?>
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_GET['error']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="?route=<?= $isEdit ? 'clientes_update' : 'clientes_store' ?>" method="POST" id="clienteForm">
                        <?php if ($isEdit): ?>
                            <input type="hidden" name="id_cliente" value="<?= htmlspecialchars($cliente['id_cliente']) ?>">
                            <div class="mb-3">
                                <label for="id_display" class="form-label">
                                    <i class="fas fa-hashtag"></i> ID
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="id_display" 
                                       value="<?= htmlspecialchars($cliente['id_cliente']) ?>" 
                                       disabled>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="nombre" class="form-label">
                                <i class="fas fa-user"></i> Nombre y apellido*
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="nombre" 
                                   name="cliente" 
                                   required 
                                   placeholder="Ingrese el nombre completo del cliente"
                                   value="<?= $isEdit ? htmlspecialchars($cliente['cliente']) : htmlspecialchars($_POST['cliente'] ?? '') ?>">
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
                                   value="<?= $isEdit ? htmlspecialchars($cliente['email']) : htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="telefono" class="form-label">
                                <i class="fas fa-phone"></i> Teléfono
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="telefono" 
                                   name="telefono" 
                                   placeholder="Ingrese el teléfono del cliente"
                                   value="<?= $isEdit ? htmlspecialchars($cliente['telefono']) : htmlspecialchars($_POST['telefono'] ?? '') ?>">
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="?route=clientes" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn <?= $isEdit ? 'btn-warning' : 'btn-primary' ?>">
                                <i class="fas fa-save"></i> <?= $isEdit ? 'Actualizar Cliente' : 'Guardar Cliente' ?>
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
    document.getElementById('clienteForm').addEventListener('submit', function(e) {
        const nombre = document.getElementById('nombre').value.trim();
        const email = document.getElementById('email').value.trim();
        
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

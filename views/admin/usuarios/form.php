<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-person-<?= $action === 'create' ? 'plus' : 'gear' ?>"></i> 
            <?= $action === 'create' ? 'Crear Usuario' : 'Editar Usuario' ?>
        </h1>
        <a href="<?= BASE_URL ?>/admin/usuarios" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver a Lista
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Datos del Usuario
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> <?= htmlspecialchars($success) ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>/admin/usuarios?action=<?= $action ?><?= $action === 'edit' && isset($usuario) ? '&id=' . $usuario['id'] : '' ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre *</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nombre" 
                                           name="nombre" 
                                           required 
                                           value="<?= htmlspecialchars($usuario['nombre'] ?? $_POST['nombre'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           required 
                                           value="<?= htmlspecialchars($usuario['email'] ?? $_POST['email'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        Contraseña <?= $action === 'create' ? '*' : '(dejar vacío para mantener actual)' ?>
                                    </label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password" 
                                           <?= $action === 'create' ? 'required' : '' ?>>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tipo_usuario" class="form-label">Tipo de Usuario</label>
                                    <select class="form-control" id="tipo_usuario" name="tipo_usuario" required>
                                        <option value="cliente" <?= ($usuario['tipo_usuario'] ?? $_POST['tipo_usuario'] ?? '') === 'cliente' ? 'selected' : '' ?>>
                                            Cliente
                                        </option>
                                        <option value="admin" <?= ($usuario['tipo_usuario'] ?? $_POST['tipo_usuario'] ?? '') === 'admin' ? 'selected' : '' ?>>
                                            Administrador
                                        </option>
                                        <option value="superadmin" <?= ($usuario['tipo_usuario'] ?? $_POST['tipo_usuario'] ?? '') === 'superadmin' ? 'selected' : '' ?>>
                                            Super Administrador
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" 
                                           class="form-control" 
                                           id="telefono" 
                                           name="telefono" 
                                           value="<?= htmlspecialchars($usuario['telefono'] ?? $_POST['telefono'] ?? '') ?>">
                                </div>
                            </div>
                            <?php if ($action === 'edit'): ?>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="activo" 
                                               name="activo" 
                                               <?= ($usuario['activo'] ?? 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="activo">
                                            Usuario activo
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <textarea class="form-control" 
                                      id="direccion" 
                                      name="direccion" 
                                      rows="3"><?= htmlspecialchars($usuario['direccion'] ?? $_POST['direccion'] ?? '') ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= BASE_URL ?>/admin/usuarios" class="btn btn-secondary me-md-2">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> 
                                <?= $action === 'create' ? 'Crear Usuario' : 'Actualizar Usuario' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información</h6>
                </div>
                <div class="card-body">
                    <h6><i class="bi bi-info-circle text-info"></i> Tipos de Usuario</h6>
                    <ul class="list-unstyled">
                        <li><strong>Cliente:</strong> Acceso básico al sistema</li>
                        <li><strong>Admin:</strong> Gestión de reservas y datos</li>
                        <li><strong>SuperAdmin:</strong> Acceso completo al sistema</li>
                    </ul>

                    <?php if ($action === 'edit' && isset($usuario)): ?>
                    <hr>
                    <h6><i class="bi bi-calendar text-info"></i> Información de Registro</h6>
                    <p><strong>Creado:</strong> <?= date('d/m/Y H:i', strtotime($usuario['fecha_registro'])) ?></p>
                    <p><strong>Última actualización:</strong> <?= date('d/m/Y H:i', strtotime($usuario['fecha_actualizacion'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación del formulario
    const form = document.querySelector('form');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    form.addEventListener('submit', function(e) {
        const emailInput = document.getElementById('email');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!emailRegex.test(emailInput.value)) {
            e.preventDefault();
            alert('Por favor, ingrese un email válido');
            emailInput.focus();
            return false;
        }
        
        if (password.value && password.value.length < 6) {
            e.preventDefault();
            alert('La contraseña debe tener al menos 6 caracteres');
            password.focus();
            return false;
        }
    });
});
</script>
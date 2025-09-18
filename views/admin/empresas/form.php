<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-briefcase-<?= $action === 'create' ? 'fill' : 'gear' ?>"></i> 
            <?= $action === 'create' ? 'Crear Empresa' : 'Editar Empresa' ?>
        </h1>
        <a href="<?= BASE_URL ?>/admin/empresas" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver a Lista
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Datos de la Empresa
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

                    <form method="POST">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre de la Empresa *</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nombre" 
                                           name="nombre" 
                                           required 
                                           value="<?= htmlspecialchars($empresa['nombre'] ?? $_POST['nombre'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="rfc" class="form-label">RFC</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="rfc" 
                                           name="rfc" 
                                           maxlength="20"
                                           value="<?= htmlspecialchars($empresa['rfc'] ?? $_POST['rfc'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="razon_social" class="form-label">Razón Social</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="razon_social" 
                                   name="razon_social" 
                                   value="<?= htmlspecialchars($empresa['razon_social'] ?? $_POST['razon_social'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <textarea class="form-control" 
                                      id="direccion" 
                                      name="direccion" 
                                      rows="2"><?= htmlspecialchars($empresa['direccion'] ?? $_POST['direccion'] ?? '') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" 
                                           class="form-control" 
                                           id="telefono" 
                                           name="telefono" 
                                           value="<?= htmlspecialchars($empresa['telefono'] ?? $_POST['telefono'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           value="<?= htmlspecialchars($empresa['email'] ?? $_POST['email'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <?php if ($action === 'edit'): ?>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="activo" 
                                       name="activo" 
                                       <?= ($empresa['activo'] ?? 1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="activo">
                                    Empresa activa
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= BASE_URL ?>/admin/empresas" class="btn btn-secondary me-md-2">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> 
                                <?= $action === 'create' ? 'Crear Empresa' : 'Actualizar Empresa' ?>
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
                    <h6><i class="bi bi-info-circle text-info"></i> Empresas Cliente</h6>
                    <p class="text-muted">
                        Las empresas son entidades corporativas que pueden realizar reservas 
                        y generar facturación para eventos empresariales.
                    </p>

                    <?php if ($action === 'edit' && isset($empresa)): ?>
                    <hr>
                    <h6><i class="bi bi-calendar text-info"></i> Información de Registro</h6>
                    <p><strong>Creada:</strong> <?= date('d/m/Y H:i', strtotime($empresa['fecha_registro'])) ?></p>
                    <p><strong>Última actualización:</strong> <?= date('d/m/Y H:i', strtotime($empresa['fecha_actualizacion'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const rfcInput = document.getElementById('rfc');
    
    // Formatear RFC en mayúsculas
    rfcInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    form.addEventListener('submit', function(e) {
        const nombreInput = document.getElementById('nombre');
        const emailInput = document.getElementById('email');
        
        if (nombreInput.value.trim().length < 3) {
            e.preventDefault();
            alert('El nombre de la empresa debe tener al menos 3 caracteres');
            nombreInput.focus();
            return false;
        }
        
        if (emailInput.value && !isValidEmail(emailInput.value)) {
            e.preventDefault();
            alert('Por favor, ingrese un email válido');
            emailInput.focus();
            return false;
        }
    });
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
});
</script>
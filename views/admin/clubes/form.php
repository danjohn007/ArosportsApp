<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-building-<?= $action === 'create' ? 'add' : 'gear' ?>"></i> 
            <?= $action === 'create' ? 'Crear Club' : 'Editar Club' ?>
        </h1>
        <a href="<?= BASE_URL ?>/admin/clubes" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver a Lista
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Datos del Club
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

                    <form method="POST" action="<?= BASE_URL ?>/admin/clubes?action=<?= $action ?><?= $action === 'edit' && isset($club) ? '&id=' . $club['id'] : '' ?>">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre del Club *</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nombre" 
                                           name="nombre" 
                                           required 
                                           value="<?= htmlspecialchars($club['nombre'] ?? $_POST['nombre'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" 
                                      id="descripcion" 
                                      name="descripcion" 
                                      rows="3"><?= htmlspecialchars($club['descripcion'] ?? $_POST['descripcion'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <textarea class="form-control" 
                                      id="direccion" 
                                      name="direccion" 
                                      rows="2"><?= htmlspecialchars($club['direccion'] ?? $_POST['direccion'] ?? '') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" 
                                           class="form-control" 
                                           id="telefono" 
                                           name="telefono" 
                                           value="<?= htmlspecialchars($club['telefono'] ?? $_POST['telefono'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           value="<?= htmlspecialchars($club['email'] ?? $_POST['email'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h6 class="text-primary"><i class="bi bi-person-badge"></i> Datos del Representante</h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="representante_nombre" class="form-label">Nombre del Representante</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="representante_nombre" 
                                           name="representante_nombre" 
                                           value="<?= htmlspecialchars($club['representante_nombre'] ?? $_POST['representante_nombre'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="representante_email" class="form-label">Email del Representante</label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="representante_email" 
                                           name="representante_email" 
                                           value="<?= htmlspecialchars($club['representante_email'] ?? $_POST['representante_email'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="representante_telefono" class="form-label">Teléfono del Representante</label>
                                    <input type="tel" 
                                           class="form-control" 
                                           id="representante_telefono" 
                                           name="representante_telefono" 
                                           value="<?= htmlspecialchars($club['representante_telefono'] ?? $_POST['representante_telefono'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="representante_password" class="form-label">
                                        Contraseña del Representante 
                                        <?php if ($action === 'edit'): ?>
                                        <small class="text-muted">(dejar vacío para mantener actual)</small>
                                        <?php endif; ?>
                                    </label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="representante_password" 
                                           name="representante_password">
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
                                       <?= ($club['activo'] ?? 1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="activo">
                                    Club activo
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= BASE_URL ?>/admin/clubes" class="btn btn-secondary me-md-2">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> 
                                <?= $action === 'create' ? 'Crear Club' : 'Actualizar Club' ?>
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
                    <h6><i class="bi bi-info-circle text-info"></i> Acerca de los Clubes</h6>
                    <p class="text-muted">
                        Los clubes son las instalaciones deportivas donde se realizan las reservas. 
                        Pueden tener fraccionamientos asociados.
                    </p>

                    <?php if ($action === 'edit' && isset($club)): ?>
                    <hr>
                    <h6><i class="bi bi-calendar text-info"></i> Información de Registro</h6>
                    <p><strong>Creado:</strong> <?= date('d/m/Y H:i', strtotime($club['fecha_registro'])) ?></p>
                    <p><strong>Última actualización:</strong> <?= date('d/m/Y H:i', strtotime($club['fecha_actualizacion'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function(e) {
        const nombreInput = document.getElementById('nombre');
        const emailInput = document.getElementById('email');
        
        if (nombreInput.value.trim().length < 3) {
            e.preventDefault();
            alert('El nombre del club debe tener al menos 3 caracteres');
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
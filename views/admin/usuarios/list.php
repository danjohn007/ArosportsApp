<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-people"></i> Gestión de Usuarios
        </h1>
        <a href="<?= BASE_URL ?>/admin/usuarios?action=create" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo Usuario
        </a>
    </div>

    <?php if (isset($error)): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Usuarios</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="usuariosTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Tipo</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= $usuario['id'] ?></td>
                            <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                            <td><?= htmlspecialchars($usuario['email']) ?></td>
                            <td>
                                <span class="badge bg-<?= $usuario['tipo_usuario'] === 'superadmin' ? 'danger' : ($usuario['tipo_usuario'] === 'admin' ? 'warning' : 'info') ?>">
                                    <?= ucfirst($usuario['tipo_usuario']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($usuario['telefono'] ?? 'N/A') ?></td>
                            <td>
                                <span class="badge bg-<?= $usuario['activo'] ? 'success' : 'secondary' ?>">
                                    <?= $usuario['activo'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($usuario['fecha_registro'])) ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?= BASE_URL ?>/admin/usuarios?action=edit&id=<?= $usuario['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php if ($usuario['id'] != $_SESSION['user_id']): ?>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger delete-btn" 
                                            data-id="<?= $usuario['id'] ?>"
                                            data-nombre="<?= htmlspecialchars($usuario['nombre']) ?>"
                                            title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">No hay usuarios registrados</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro de que desea eliminar al usuario <strong id="deleteUserName"></strong>?
                <br><small class="text-muted">Esta acción no se puede deshacer.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const deleteUserName = document.getElementById('deleteUserName');
    const confirmDelete = document.getElementById('confirmDelete');
    let currentDeleteId = null;
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            currentDeleteId = this.dataset.id;
            deleteUserName.textContent = this.dataset.nombre;
            deleteModal.show();
        });
    });
    
    confirmDelete.addEventListener('click', function() {
        if (currentDeleteId) {
            fetch(`<?= BASE_URL ?>/admin/usuarios?action=delete&id=${currentDeleteId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar usuario');
            });
            
            deleteModal.hide();
        }
    });
});
</script>
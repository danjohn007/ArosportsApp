<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-briefcase"></i> Gestión de Empresas
        </h1>
        <a href="<?= BASE_URL ?>/admin/empresas?action=create" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nueva Empresa
        </a>
    </div>

    <?php if (isset($error)): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Empresas</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>RFC</th>
                            <th>Razón Social</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($empresas as $empresa): ?>
                        <tr>
                            <td><?= $empresa['id'] ?></td>
                            <td><?= htmlspecialchars($empresa['nombre']) ?></td>
                            <td><?= htmlspecialchars($empresa['rfc'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars(substr($empresa['razon_social'] ?? '', 0, 30)) ?><?= strlen($empresa['razon_social'] ?? '') > 30 ? '...' : '' ?></td>
                            <td><?= htmlspecialchars($empresa['telefono'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($empresa['email'] ?? 'N/A') ?></td>
                            <td>
                                <span class="badge bg-<?= $empresa['activo'] ? 'success' : 'secondary' ?>">
                                    <?= $empresa['activo'] ? 'Activa' : 'Inactiva' ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?= BASE_URL ?>/admin/empresas?action=edit&id=<?= $empresa['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger delete-btn" 
                                            data-id="<?= $empresa['id'] ?>"
                                            data-nombre="<?= htmlspecialchars($empresa['nombre']) ?>"
                                            title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($empresas)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">No hay empresas registradas</td>
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
                ¿Está seguro de que desea eliminar la empresa <strong id="deleteName"></strong>?
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
    const deleteName = document.getElementById('deleteName');
    const confirmDelete = document.getElementById('confirmDelete');
    let currentDeleteId = null;
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            currentDeleteId = this.dataset.id;
            deleteName.textContent = this.dataset.nombre;
            deleteModal.show();
        });
    });
    
    confirmDelete.addEventListener('click', function() {
        if (currentDeleteId) {
            fetch(`<?= BASE_URL ?>/admin/empresas?action=delete&id=${currentDeleteId}`, {
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
                alert('Error al eliminar empresa');
            });
            
            deleteModal.hide();
        }
    });
});
</script>
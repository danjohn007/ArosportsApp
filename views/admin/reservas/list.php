<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-calendar-event"></i> Gestión de Reservas
        </h1>
        <a href="<?= BASE_URL ?>/admin/reservas?action=create" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nueva Reserva
        </a>
    </div>

    <?php if (isset($error)): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Reservas</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Club</th>
                            <th>Fecha</th>
                            <th>Horario</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th>Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservas as $reserva): ?>
                        <tr>
                            <td><?= $reserva['id'] ?></td>
                            <td><?= htmlspecialchars($reserva['usuario_nombre'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($reserva['club_nombre'] ?? 'Sin Club') ?></td>
                            <td><?= date('d/m/Y', strtotime($reserva['fecha_reserva'])) ?></td>
                            <td>
                                <?= date('H:i', strtotime($reserva['hora_inicio'])) ?> - 
                                <?= date('H:i', strtotime($reserva['hora_fin'])) ?>
                            </td>
                            <td class="text-end">$<?= number_format($reserva['precio'], 2) ?></td>
                            <td>
                                <span class="badge bg-<?= getStatusColor($reserva['estado']) ?>">
                                    <?= ucfirst($reserva['estado']) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($reserva['fecha_registro'])) ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?= BASE_URL ?>/admin/reservas?action=edit&id=<?= $reserva['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger delete-btn" 
                                            data-id="<?= $reserva['id'] ?>"
                                            data-info="Reserva #<?= $reserva['id'] ?> - <?= htmlspecialchars($reserva['usuario_nombre'] ?? '') ?>"
                                            title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($reservas)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">No hay reservas registradas</td>
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
                ¿Está seguro de que desea eliminar la <strong id="deleteInfo"></strong>?
                <br><small class="text-muted">Esta acción no se puede deshacer.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<?php
function getStatusColor($estado) {
    switch ($estado) {
        case 'completada': return 'success';
        case 'confirmada': return 'primary';
        case 'pendiente': return 'warning';
        case 'cancelada': return 'danger';
        default: return 'secondary';
    }
}
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const deleteInfo = document.getElementById('deleteInfo');
    const confirmDelete = document.getElementById('confirmDelete');
    let currentDeleteId = null;
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            currentDeleteId = this.dataset.id;
            deleteInfo.textContent = this.dataset.info;
            deleteModal.show();
        });
    });
    
    confirmDelete.addEventListener('click', function() {
        if (currentDeleteId) {
            fetch(`<?= BASE_URL ?>/admin/reservas?action=delete&id=${currentDeleteId}`, {
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
                alert('Error al eliminar reserva');
            });
            
            deleteModal.hide();
        }
    });
});
</script>
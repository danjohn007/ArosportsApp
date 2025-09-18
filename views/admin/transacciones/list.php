<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-receipt"></i> Gestión de Transacciones
        </h1>
        <a href="<?= BASE_URL ?>/admin/transacciones?action=create" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nueva Transacción
        </a>
    </div>

    <?php if (isset($error)): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <i class="bi bi-check-circle"></i> 
        <?php
        switch($_GET['success']) {
            case 'created': echo 'Transacción creada exitosamente'; break;
            case 'authorized': echo 'Transacción autorizada exitosamente'; break;
            case 'deleted': echo 'Transacción eliminada exitosamente'; break;
            default: echo 'Operación completada exitosamente';
        }
        ?>
    </div>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="bi bi-funnel"></i> Filtros
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row">
                <div class="col-md-2">
                    <label for="tipo" class="form-label">Tipo</label>
                    <select name="tipo" id="tipo" class="form-select">
                        <option value="">Todos</option>
                        <option value="ingreso" <?= $filtros['tipo'] === 'ingreso' ? 'selected' : '' ?>>Ingreso</option>
                        <option value="gasto" <?= $filtros['tipo'] === 'gasto' ? 'selected' : '' ?>>Gasto</option>
                        <option value="retiro" <?= $filtros['tipo'] === 'retiro' ? 'selected' : '' ?>>Retiro</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="categoria" class="form-label">Categoría</label>
                    <select name="categoria" id="categoria" class="form-select">
                        <option value="">Todas</option>
                        <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= $categoria['id'] ?>" <?= $filtros['categoria'] == $categoria['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($categoria['nombre']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="estado" class="form-label">Estado</label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendiente" <?= $filtros['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="autorizada" <?= $filtros['estado'] === 'autorizada' ? 'selected' : '' ?>>Autorizada</option>
                        <option value="cancelada" <?= $filtros['estado'] === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="fecha_desde" class="form-label">Desde</label>
                    <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" 
                           value="<?= htmlspecialchars($filtros['fecha_desde']) ?>">
                </div>
                <div class="col-md-2">
                    <label for="fecha_hasta" class="form-label">Hasta</label>
                    <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" 
                           value="<?= htmlspecialchars($filtros['fecha_hasta']) ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="<?= BASE_URL ?>/admin/transacciones" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de transacciones -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Transacciones</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Categoría</th>
                            <th>Concepto</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transacciones)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox display-4"></i>
                                <p class="mt-2">No hay transacciones registradas</p>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($transacciones as $transaccion): ?>
                        <tr>
                            <td><?= $transaccion['id'] ?></td>
                            <td><?= date('d/m/Y', strtotime($transaccion['fecha_transaccion'])) ?></td>
                            <td>
                                <?php
                                $tipoClass = [
                                    'ingreso' => 'success',
                                    'gasto' => 'danger',
                                    'retiro' => 'warning'
                                ];
                                $tipoIcon = [
                                    'ingreso' => 'bi-arrow-down-circle',
                                    'gasto' => 'bi-arrow-up-circle',
                                    'retiro' => 'bi-cash-coin'
                                ];
                                ?>
                                <span class="badge bg-<?= $tipoClass[$transaccion['tipo']] ?>">
                                    <i class="bi <?= $tipoIcon[$transaccion['tipo']] ?>"></i> 
                                    <?= ucfirst($transaccion['tipo']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge" style="background-color: <?= $transaccion['categoria_color'] ?>;">
                                    <?= htmlspecialchars($transaccion['categoria_nombre']) ?>
                                </span>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($transaccion['concepto']) ?></strong>
                                <?php if ($transaccion['descripcion']): ?>
                                <br><small class="text-muted"><?= htmlspecialchars($transaccion['descripcion']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <strong class="<?= $transaccion['tipo'] === 'ingreso' ? 'text-success' : 'text-danger' ?>">
                                    <?= $transaccion['tipo'] === 'ingreso' ? '+' : '-' ?>$<?= number_format($transaccion['monto'], 2) ?>
                                </strong>
                            </td>
                            <td>
                                <?php
                                $estadoClass = [
                                    'pendiente' => 'warning',
                                    'autorizada' => 'success',
                                    'cancelada' => 'danger'
                                ];
                                ?>
                                <span class="badge bg-<?= $estadoClass[$transaccion['estado']] ?>">
                                    <?= ucfirst($transaccion['estado']) ?>
                                </span>
                                <?php if ($transaccion['autorizada_por_nombre']): ?>
                                <br><small class="text-muted">Por: <?= htmlspecialchars($transaccion['autorizada_por_nombre']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($transaccion['usuario_nombre']) ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <?php if ($transaccion['estado'] === 'pendiente' && in_array($_SESSION['user']['tipo_usuario'], ['superadmin', 'admin'])): ?>
                                    <button type="button" class="btn btn-sm btn-success" 
                                            onclick="autorizarTransaccion(<?= $transaccion['id'] ?>, 'autorizar')"
                                            title="Autorizar">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning" 
                                            onclick="autorizarTransaccion(<?= $transaccion['id'] ?>, 'rechazar')"
                                            title="Rechazar">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                    <?php endif; ?>
                                    
                                    <?php if ($_SESSION['user']['tipo_usuario'] === 'superadmin'): ?>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="eliminarTransaccion(<?= $transaccion['id'] ?>)"
                                            title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para autorización -->
<div class="modal fade" id="authorizeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="authorizeModalTitle">Confirmar Acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="authorizeModalBody">
                ¿Está seguro de que desea realizar esta acción?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmAuthorizeBtn">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro de que desea eliminar esta transacción? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const authorizeModal = new bootstrap.Modal(document.getElementById('authorizeModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    
    let currentTransactionId = null;
    let currentAction = null;

    window.autorizarTransaccion = function(id, accion) {
        currentTransactionId = id;
        currentAction = accion;
        
        const modalTitle = document.getElementById('authorizeModalTitle');
        const modalBody = document.getElementById('authorizeModalBody');
        const confirmBtn = document.getElementById('confirmAuthorizeBtn');
        
        if (accion === 'autorizar') {
            modalTitle.textContent = 'Autorizar Transacción';
            modalBody.textContent = '¿Está seguro de que desea autorizar esta transacción?';
            confirmBtn.className = 'btn btn-success';
            confirmBtn.textContent = 'Autorizar';
        } else {
            modalTitle.textContent = 'Rechazar Transacción';
            modalBody.textContent = '¿Está seguro de que desea rechazar esta transacción?';
            confirmBtn.className = 'btn btn-warning';
            confirmBtn.textContent = 'Rechazar';
        }
        
        authorizeModal.show();
    };

    window.eliminarTransaccion = function(id) {
        currentTransactionId = id;
        deleteModal.show();
    };

    document.getElementById('confirmAuthorizeBtn').addEventListener('click', function() {
        if (currentTransactionId && currentAction) {
            fetch(`<?= BASE_URL ?>/admin/transacciones?action=authorize`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${currentTransactionId}&accion=${currentAction}`
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
                alert('Error al procesar la transacción');
            });
            
            authorizeModal.hide();
        }
    });

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (currentTransactionId) {
            fetch(`<?= BASE_URL ?>/admin/transacciones?action=delete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${currentTransactionId}`
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
                alert('Error al eliminar transacción');
            });
            
            deleteModal.hide();
        }
    });
});
</script>
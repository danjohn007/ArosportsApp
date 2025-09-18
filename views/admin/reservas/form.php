<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-calendar-<?= $action === 'create' ? 'plus' : 'gear' ?>"></i> 
            <?= $action === 'create' ? 'Crear Reserva' : 'Editar Reserva' ?>
        </h1>
        <a href="<?= BASE_URL ?>/admin/reservas" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver a Lista
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Datos de la Reserva
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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="usuario_id" class="form-label">Usuario *</label>
                                    <select class="form-control" id="usuario_id" name="usuario_id" required>
                                        <option value="">Seleccionar usuario</option>
                                        <?php foreach ($usuarios as $usuario): ?>
                                        <option value="<?= $usuario['id'] ?>" <?= ($reserva['usuario_id'] ?? $_POST['usuario_id'] ?? '') == $usuario['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($usuario['nombre']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="club_id" class="form-label">Club</label>
                                    <select class="form-control" id="club_id" name="club_id">
                                        <option value="">Sin club específico</option>
                                        <?php foreach ($clubes as $club): ?>
                                        <option value="<?= $club['id'] ?>" <?= ($reserva['club_id'] ?? $_POST['club_id'] ?? '') == $club['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($club['nombre']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fraccionamiento_id" class="form-label">Fraccionamiento</label>
                                    <select class="form-control" id="fraccionamiento_id" name="fraccionamiento_id">
                                        <option value="">Sin fraccionamiento</option>
                                        <?php foreach ($fraccionamientos as $fraccionamiento): ?>
                                        <option value="<?= $fraccionamiento['id'] ?>" <?= ($reserva['fraccionamiento_id'] ?? $_POST['fraccionamiento_id'] ?? '') == $fraccionamiento['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($fraccionamiento['nombre']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="empresa_id" class="form-label">Empresa</label>
                                    <select class="form-control" id="empresa_id" name="empresa_id">
                                        <option value="">Sin empresa</option>
                                        <?php foreach ($empresas as $empresa): ?>
                                        <option value="<?= $empresa['id'] ?>" <?= ($reserva['empresa_id'] ?? $_POST['empresa_id'] ?? '') == $empresa['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($empresa['nombre']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="fecha_reserva" class="form-label">Fecha de Reserva *</label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="fecha_reserva" 
                                           name="fecha_reserva" 
                                           required 
                                           value="<?= htmlspecialchars($reserva['fecha_reserva'] ?? $_POST['fecha_reserva'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="hora_inicio" class="form-label">Hora Inicio *</label>
                                    <input type="time" 
                                           class="form-control" 
                                           id="hora_inicio" 
                                           name="hora_inicio" 
                                           required 
                                           value="<?= htmlspecialchars($reserva['hora_inicio'] ?? $_POST['hora_inicio'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="hora_fin" class="form-label">Hora Fin *</label>
                                    <input type="time" 
                                           class="form-control" 
                                           id="hora_fin" 
                                           name="hora_fin" 
                                           required 
                                           value="<?= htmlspecialchars($reserva['hora_fin'] ?? $_POST['hora_fin'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="precio" class="form-label">Precio *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               class="form-control" 
                                               id="precio" 
                                               name="precio" 
                                               step="0.01" 
                                               min="0" 
                                               required 
                                               value="<?= htmlspecialchars($reserva['precio'] ?? $_POST['precio'] ?? '0') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select class="form-control" id="estado" name="estado" required>
                                        <option value="pendiente" <?= ($reserva['estado'] ?? $_POST['estado'] ?? 'pendiente') === 'pendiente' ? 'selected' : '' ?>>
                                            Pendiente
                                        </option>
                                        <option value="confirmada" <?= ($reserva['estado'] ?? $_POST['estado'] ?? '') === 'confirmada' ? 'selected' : '' ?>>
                                            Confirmada
                                        </option>
                                        <option value="completada" <?= ($reserva['estado'] ?? $_POST['estado'] ?? '') === 'completada' ? 'selected' : '' ?>>
                                            Completada
                                        </option>
                                        <option value="cancelada" <?= ($reserva['estado'] ?? $_POST['estado'] ?? '') === 'cancelada' ? 'selected' : '' ?>>
                                            Cancelada
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" 
                                      id="observaciones" 
                                      name="observaciones" 
                                      rows="3"><?= htmlspecialchars($reserva['observaciones'] ?? $_POST['observaciones'] ?? '') ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= BASE_URL ?>/admin/reservas" class="btn btn-secondary me-md-2">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> 
                                <?= $action === 'create' ? 'Crear Reserva' : 'Actualizar Reserva' ?>
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
                    <h6><i class="bi bi-info-circle text-info"></i> Estados de Reserva</h6>
                    <ul class="list-unstyled">
                        <li><span class="badge bg-warning">Pendiente</span> - Reserva solicitada</li>
                        <li><span class="badge bg-primary">Confirmada</span> - Reserva confirmada</li>
                        <li><span class="badge bg-success">Completada</span> - Reserva realizada</li>
                        <li><span class="badge bg-danger">Cancelada</span> - Reserva cancelada</li>
                    </ul>

                    <hr>
                    <h6><i class="bi bi-currency-dollar text-info"></i> Precio</h6>
                    <p class="text-muted">
                        El precio se utiliza para el cálculo de estadísticas financieras 
                        en el dashboard del sistema.
                    </p>

                    <?php if ($action === 'edit' && isset($reserva)): ?>
                    <hr>
                    <h6><i class="bi bi-calendar text-info"></i> Información de Registro</h6>
                    <p><strong>Creada:</strong> <?= date('d/m/Y H:i', strtotime($reserva['fecha_registro'])) ?></p>
                    <p><strong>Última actualización:</strong> <?= date('d/m/Y H:i', strtotime($reserva['fecha_actualizacion'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const horaInicio = document.getElementById('hora_inicio');
    const horaFin = document.getElementById('hora_fin');
    const fechaReserva = document.getElementById('fecha_reserva');
    
    // Validación de fechas y horas
    form.addEventListener('submit', function(e) {
        const fecha = new Date(fechaReserva.value);
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        
        if (fecha < hoy) {
            e.preventDefault();
            alert('La fecha de reserva no puede ser anterior a hoy');
            fechaReserva.focus();
            return false;
        }
        
        if (horaInicio.value >= horaFin.value) {
            e.preventDefault();
            alert('La hora de fin debe ser posterior a la hora de inicio');
            horaFin.focus();
            return false;
        }
    });

    // Configurar fecha mínima
    const today = new Date().toISOString().split('T')[0];
    fechaReserva.setAttribute('min', today);
});
</script>
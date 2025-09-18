<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-graph-up"></i> Sistema de Reportes
        </h1>
        <div class="text-muted">
            <i class="bi bi-calendar3"></i> <?= date('d/m/Y H:i') ?>
        </div>
    </div>

    <!-- Filtros de Reporte -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="bi bi-funnel"></i> Filtros de Reporte
            </h6>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= BASE_URL ?>/reportes" id="reportForm">
                <div class="row">
                    <!-- Rango de Fechas -->
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="fecha_inicio" 
                                   name="fecha_inicio"
                                   value="<?= htmlspecialchars($filtrosAplicados['fecha_inicio'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="fecha_fin" class="form-label">Fecha Fin</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="fecha_fin" 
                                   name="fecha_fin"
                                   value="<?= htmlspecialchars($filtrosAplicados['fecha_fin'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- Tipo de Reporte -->
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="tipo_reporte" class="form-label">Tipo de Reporte</label>
                            <select class="form-control" id="tipo_reporte" name="tipo_reporte">
                                <option value="resumen" <?= ($filtrosAplicados['tipo_reporte'] ?? '') === 'resumen' ? 'selected' : '' ?>>
                                    Resumen General
                                </option>
                                <option value="financiero" <?= ($filtrosAplicados['tipo_reporte'] ?? '') === 'financiero' ? 'selected' : '' ?>>
                                    Reporte Financiero
                                </option>
                                <option value="usuarios" <?= ($filtrosAplicados['tipo_reporte'] ?? '') === 'usuarios' ? 'selected' : '' ?>>
                                    Actividad de Usuarios
                                </option>
                                <option value="actividad" <?= ($filtrosAplicados['tipo_reporte'] ?? '') === 'actividad' ? 'selected' : '' ?>>
                                    Actividad Detallada
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Botón Generar -->
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-play-circle"></i> Generar Reporte
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros Adicionales -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="usuario_id" class="form-label">Usuario</label>
                            <select class="form-control" id="usuario_id" name="usuario_id">
                                <option value="">Todos los usuarios</option>
                                <?php foreach ($filtros['usuarios'] as $usuario): ?>
                                <option value="<?= $usuario['id'] ?>" <?= ($filtrosAplicados['usuario_id'] ?? '') == $usuario['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($usuario['nombre']) ?> (<?= $usuario['tipo_usuario'] ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="club_id" class="form-label">Club</label>
                            <select class="form-control" id="club_id" name="club_id">
                                <option value="">Todos los clubes</option>
                                <?php foreach ($filtros['clubes'] as $club): ?>
                                <option value="<?= $club['id'] ?>" <?= ($filtrosAplicados['club_id'] ?? '') == $club['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($club['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado de Reserva</label>
                            <select class="form-control" id="estado" name="estado">
                                <option value="">Todos los estados</option>
                                <option value="pendiente" <?= ($filtrosAplicados['estado'] ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                <option value="confirmada" <?= ($filtrosAplicados['estado'] ?? '') === 'confirmada' ? 'selected' : '' ?>>Confirmada</option>
                                <option value="completada" <?= ($filtrosAplicados['estado'] ?? '') === 'completada' ? 'selected' : '' ?>>Completada</option>
                                <option value="cancelada" <?= ($filtrosAplicados['estado'] ?? '') === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="tipo_usuario" class="form-label">Tipo de Usuario</label>
                            <select class="form-control" id="tipo_usuario" name="tipo_usuario">
                                <option value="">Todos los tipos</option>
                                <option value="cliente" <?= ($filtrosAplicados['tipo_usuario'] ?? '') === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                                <option value="admin" <?= ($filtrosAplicados['tipo_usuario'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="superadmin" <?= ($filtrosAplicados['tipo_usuario'] ?? '') === 'superadmin' ? 'selected' : '' ?>>SuperAdmin</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Resultados del Reporte -->
    <?php if (!empty($reporteData)): ?>
    
    <?php if (isset($reporteData['error'])): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($reporteData['error']) ?>
    </div>
    <?php else: ?>

    <!-- Reporte de Resumen -->
    <?php if ($reporteData['tipo'] === 'resumen'): ?>
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Reservas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($reporteData['resumen']['total_reservas']) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-check text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Ingresos Totales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">$<?= number_format($reporteData['resumen']['ingresos_totales'], 2) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Completadas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($reporteData['resumen']['reservas_completadas']) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Precio Promedio</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">$<?= number_format($reporteData['resumen']['precio_promedio'], 2) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-graph-up text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de detalles por mes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Actividad por Mes</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Mes</th>
                            <th>Total Reservas</th>
                            <th>Ingresos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reporteData['detalles_por_mes'] as $detalle): ?>
                        <tr>
                            <td><?= date('F Y', strtotime($detalle['mes'] . '-01')) ?></td>
                            <td><?= number_format($detalle['total_reservas']) ?></td>
                            <td>$<?= number_format($detalle['ingresos'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($reporteData['detalles_por_mes'])): ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted">No hay datos disponibles</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Reporte Financiero -->
    <?php if ($reporteData['tipo'] === 'financiero'): ?>
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ingresos por Club</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Club</th>
                                    <th>Reservas</th>
                                    <th>Ingresos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reporteData['ingresos_por_club'] as $club): ?>
                                <tr>
                                    <td><?= htmlspecialchars($club['club_nombre']) ?></td>
                                    <td><?= number_format($club['total_reservas']) ?></td>
                                    <td>$<?= number_format($club['ingresos_totales'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ingresos por Tipo de Usuario</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tipo Usuario</th>
                                    <th>Reservas</th>
                                    <th>Ingresos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reporteData['ingresos_por_tipo_usuario'] as $tipo): ?>
                                <tr>
                                    <td><span class="badge bg-info"><?= ucfirst($tipo['tipo_usuario']) ?></span></td>
                                    <td><?= number_format($tipo['total_reservas']) ?></td>
                                    <td>$<?= number_format($tipo['ingresos_totales'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Reporte de Usuarios -->
    <?php if ($reporteData['tipo'] === 'usuarios'): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Actividad de Usuarios</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Tipo</th>
                            <th>Total Reservas</th>
                            <th>Completadas</th>
                            <th>Total Gastado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reporteData['actividad_usuarios'] as $usuario): ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario['usuario_nombre']) ?></td>
                            <td><?= htmlspecialchars($usuario['usuario_email']) ?></td>
                            <td><span class="badge bg-info"><?= ucfirst($usuario['tipo_usuario']) ?></span></td>
                            <td><?= number_format($usuario['total_reservas']) ?></td>
                            <td><?= number_format($usuario['reservas_completadas']) ?></td>
                            <td>$<?= number_format($usuario['total_gastado'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($reporteData['actividad_usuarios'])): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No hay datos disponibles</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Reporte de Actividad Detallada -->
    <?php if ($reporteData['tipo'] === 'actividad'): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Actividad Detallada (Últimas 1000 reservas)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Club</th>
                            <th>Fecha</th>
                            <th>Horario</th>
                            <th>Precio</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reporteData['actividad_detallada'] as $actividad): ?>
                        <tr>
                            <td><?= $actividad['id'] ?></td>
                            <td><?= htmlspecialchars($actividad['usuario_nombre']) ?></td>
                            <td><?= htmlspecialchars($actividad['club_nombre']) ?></td>
                            <td><?= date('d/m/Y', strtotime($actividad['fecha_reserva'])) ?></td>
                            <td><?= date('H:i', strtotime($actividad['hora_inicio'])) ?> - <?= date('H:i', strtotime($actividad['hora_fin'])) ?></td>
                            <td>$<?= number_format($actividad['precio'], 2) ?></td>
                            <td>
                                <span class="badge bg-<?= getStatusColor($actividad['estado']) ?>">
                                    <?= ucfirst($actividad['estado']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($reporteData['actividad_detallada'])): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No hay datos disponibles</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php endif; ?>
    <?php endif; ?>
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
    // Configurar fechas por defecto (último mes)
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    
    if (!fechaInicio.value) {
        const hoy = new Date();
        const mesAtras = new Date();
        mesAtras.setMonth(mesAtras.getMonth() - 1);
        
        fechaInicio.value = mesAtras.toISOString().split('T')[0];
        fechaFin.value = hoy.toISOString().split('T')[0];
    }
});
</script>
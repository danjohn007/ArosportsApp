<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Financiero</h1>
        <div class="d-flex align-items-center">
            <div class="text-muted me-3">
                <i class="bi bi-calendar3"></i> <?= date('d/m/Y H:i') ?>
            </div>
            <?php if (in_array($_SESSION['user_role'], ['superadmin', 'admin', 'cliente'])): ?>
            <a href="<?= BASE_URL ?>/admin/transacciones?action=create" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i> Nueva Transacción
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filtros del Dashboard -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="bi bi-funnel"></i> Filtros del Dashboard
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="<?= BASE_URL ?>/dashboard" id="dashboardFilters">
                <div class="row">
                    <!-- Filtros de fecha (para todos los usuarios) -->
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="fecha_inicio" 
                                   name="fecha_inicio"
                                   value="<?= htmlspecialchars($filters['fecha_inicio'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="fecha_fin" class="form-label">Fecha Fin</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="fecha_fin" 
                                   name="fecha_fin"
                                   value="<?= htmlspecialchars($filters['fecha_fin'] ?? '') ?>">
                        </div>
                    </div>

                    <?php if (($_SESSION['user_role'] ?? '') === 'superadmin'): ?>
                    <!-- Filtros adicionales para SuperAdmin -->
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="club_id" class="form-label">Club</label>
                            <select class="form-control" id="club_id" name="club_id">
                                <option value="">Todos los Clubes</option>
                                <?php foreach (($filterOptions['clubes'] ?? []) as $club): ?>
                                <option value="<?= $club['id'] ?>" 
                                        <?= ($filters['club_id'] ?? '') == $club['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($club['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="empresa_id" class="form-label">Empresa</label>
                            <select class="form-control" id="empresa_id" name="empresa_id">
                                <option value="">Todas las Empresas</option>
                                <?php foreach (($filterOptions['empresas'] ?? []) as $empresa): ?>
                                <option value="<?= $empresa['id'] ?>" 
                                        <?= ($filters['empresa_id'] ?? '') == $empresa['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($empresa['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="fraccionamiento_id" class="form-label">Fraccionamiento</label>
                            <select class="form-control" id="fraccionamiento_id" name="fraccionamiento_id">
                                <option value="">Todos los Fraccionamientos</option>
                                <?php foreach (($filterOptions['fraccionamientos'] ?? []) as $fraccionamiento): ?>
                                <option value="<?= $fraccionamiento['id'] ?>" 
                                        <?= ($filters['fraccionamiento_id'] ?? '') == $fraccionamiento['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($fraccionamiento['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Botones de acción -->
                    <div class="col-md-<?= ($_SESSION['user_role'] ?? '') === 'superadmin' ? '12' : '6' ?>">
                        <div class="mb-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Aplicar Filtros
                                </button>
                                <a href="<?= BASE_URL ?>/dashboard" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tarjetas de estadísticas financieras -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Ingresos
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                $<?= number_format($stats['total_ingresos'], 2) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Gastos
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                $<?= number_format($stats['total_gastos'], 2) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cash-stack text-danger" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card border-left-<?= $stats['utilidad_total'] >= 0 ? 'success' : 'warning' ?> shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-<?= $stats['utilidad_total'] >= 0 ? 'success' : 'warning' ?> text-uppercase mb-1">
                                Utilidad Total
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                $<?= number_format($stats['utilidad_total'], 2) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-<?= $stats['utilidad_total'] >= 0 ? 'graph-up-arrow' : 'graph-down-arrow' ?> text-<?= $stats['utilidad_total'] >= 0 ? 'success' : 'warning' ?>" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Ingresos del Período
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                $<?= number_format($stats['ingresos_periodo'], 2) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-check text-success" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Gastos del Período
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                $<?= number_format($stats['gastos_periodo'], 2) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-x text-danger" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card border-left-<?= $stats['utilidad_periodo'] >= 0 ? 'info' : 'warning' ?> shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-<?= $stats['utilidad_periodo'] >= 0 ? 'info' : 'warning' ?> text-uppercase mb-1">
                                Utilidad del Período
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                $<?= number_format($stats['utilidad_periodo'], 2) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-<?= $stats['utilidad_periodo'] >= 0 ? 'trending-up' : 'trending-down' ?> text-<?= $stats['utilidad_periodo'] >= 0 ? 'info' : 'warning' ?>" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de estadísticas operativas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Reservas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['total_reservas']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-event text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Reservas Pendientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['reservas_pendientes']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Transacciones Pendientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['transacciones_pendientes']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-hourglass text-secondary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Acciones Rápidas
                            </div>
                            <div class="mt-2">
                                <a href="<?= BASE_URL ?>/admin/transacciones" class="btn btn-primary btn-sm me-1">
                                    <i class="bi bi-receipt"></i> Transacciones
                                </a>
                                <br>
                                <a href="<?= BASE_URL ?>/reportes" class="btn btn-info btn-sm mt-1">
                                    <i class="bi bi-file-earmark-text"></i> Reportes
                                </a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-lightning text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos principales -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-graph-up"></i> Ingresos vs Gastos por Día (Período Seleccionado)
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="ingresosVsGastosChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-pie-chart"></i> Gastos por Categoría (Período Seleccionado)
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="gastosPorCategoriaChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Segunda fila de gráficos -->
    <div class="row mb-4">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-building"></i> Ingresos por Club
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="ingresosPorClubChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <!-- Reservas recientes -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-clock-history"></i> Reservas Recientes
                    </h6>
                    <a href="<?= BASE_URL ?>/admin/reservas" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye"></i> Ver Todas
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Club</th>
                                    <th>Fecha</th>  
                                    <th>Estado</th>
                                    <th class="text-end">Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentReservations)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        No hay reservas recientes
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach (array_slice($recentReservations, 0, 5) as $reserva): ?>
                                <tr>
                                    <td class="small">
                                        <?= htmlspecialchars($reserva['usuario_nombre'] ?? 'N/A') ?>
                                    </td>
                                    <td class="small">
                                        <?= htmlspecialchars($reserva['club_nombre'] ?? 'Sin Club') ?>
                                    </td>
                                    <td class="small">
                                        <?= date('d/m/Y', strtotime($reserva['fecha_reserva'])) ?>
                                    </td>
                                    <td>
                                        <?php
                                        $estadoClass = [
                                            'pendiente' => 'warning',
                                            'confirmada' => 'info',
                                            'completada' => 'success',
                                            'cancelada' => 'danger'
                                        ];
                                        ?>
                                        <span class="badge bg-<?= $estadoClass[$reserva['estado']] ?? 'secondary' ?> small">
                                            <?= ucfirst($reserva['estado']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end small">
                                        $<?= number_format($reserva['precio'], 2) ?>
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
    </div>
</div>

<script>
// Datos para los gráficos
const ingresosPorDia = <?= json_encode($chartData['ingresos_por_dia']) ?>;
const ingresosPorClub = <?= json_encode($chartData['ingresos_por_club']) ?>;
const gastosPorCategoria = <?= json_encode($chartData['gastos_por_categoria']) ?>;

// Gráfico de Ingresos vs Gastos por día
const ctxIngresosVsGastos = document.getElementById('ingresosVsGastosChart').getContext('2d');
new Chart(ctxIngresosVsGastos, {
    type: 'line',
    data: {
        labels: ingresosPorDia.map(item => {
            const date = new Date(item.fecha);
            return date.toLocaleDateString('es-MX', { day: '2-digit', month: 'short' });
        }),
        datasets: [
            {
                label: 'Ingresos',
                data: ingresosPorDia.map(item => parseFloat(item.ingresos || 0)),
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Gastos',
                data: ingresosPorDia.map(item => parseFloat(item.gastos || 0)),
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': $' + context.parsed.y.toLocaleString();
                    }
                }
            },
            legend: {
                position: 'top',
            }
        }
    }
});

// Gráfico de gastos por categoría
if (gastosPorCategoria.length > 0) {
    const ctxGastos = document.getElementById('gastosPorCategoriaChart').getContext('2d');
    new Chart(ctxGastos, {
        type: 'doughnut',
        data: {
            labels: gastosPorCategoria.map(item => item.nombre || 'Sin Categoría'),
            datasets: [{
                data: gastosPorCategoria.map(item => parseFloat(item.total)),
                backgroundColor: gastosPorCategoria.map(item => item.color || '#6c757d'),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': $' + context.parsed.toLocaleString() + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
} else {
    // Mostrar mensaje cuando no hay datos de gastos
    document.getElementById('gastosPorCategoriaChart').style.display = 'none';
    const container = document.getElementById('gastosPorCategoriaChart').parentElement;
    container.innerHTML = '<div class="text-center text-muted py-4"><i class="bi bi-inbox display-4"></i><p class="mt-2">No hay gastos registrados este mes</p></div>';
}

// Gráfico de ingresos por club
const ctxClub = document.getElementById('ingresosPorClubChart').getContext('2d');
new Chart(ctxClub, {
    type: 'bar',
    data: {
        labels: ingresosPorClub.map(item => item.nombre || 'Sin Club'),
        datasets: [{
            label: 'Ingresos',
            data: ingresosPorClub.map(item => parseFloat(item.total)),
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(239, 68, 68, 0.8)',
                'rgba(139, 92, 246, 0.8)',
                'rgba(236, 72, 153, 0.8)'
            ],
            borderColor: [
                'rgb(59, 130, 246)',
                'rgb(16, 185, 129)',
                'rgb(245, 158, 11)',
                'rgb(239, 68, 68)',
                'rgb(139, 92, 246)',
                'rgb(236, 72, 153)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Ingresos: $' + context.parsed.y.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>

<script>
// Configurar fechas por defecto si están vacías
document.addEventListener('DOMContentLoaded', function() {
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    
    if (!fechaInicio.value) {
        const hoy = new Date();
        const hace30Dias = new Date();
        hace30Dias.setDate(hace30Dias.getDate() - 30);
        
        fechaInicio.value = hace30Dias.toISOString().split('T')[0];
    }
    
    if (!fechaFin.value) {
        const hoy = new Date();
        fechaFin.value = hoy.toISOString().split('T')[0];
    }
});
</script>
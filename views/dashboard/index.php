<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Financiero</h1>
        <div class="text-muted">
            <i class="bi bi-calendar3"></i> <?= date('d/m/Y H:i') ?>
        </div>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Ingresos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?= number_format($stats['total_ingresos'], 2) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Ingresos del Mes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?= number_format($stats['ingresos_mes'], 2) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-graph-up text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                            <i class="bi bi-calendar-check text-info" style="font-size: 2rem;"></i>
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
    </div>

    <!-- Gráficos -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Ingresos por Mes</h6>
                </div>
                <div class="card-body">
                    <canvas id="ingresosPorMesChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Ingresos por Club</h6>
                </div>
                <div class="card-body">
                    <canvas id="ingresosPorClubChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservas recientes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Reservas Recientes</h6>
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentReservations as $reserva): ?>
                        <tr>
                            <td><?= $reserva['id'] ?></td>
                            <td><?= htmlspecialchars($reserva['usuario_nombre'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($reserva['club_nombre'] ?? 'N/A') ?></td>
                            <td><?= date('d/m/Y', strtotime($reserva['fecha_reserva'])) ?></td>
                            <td><?= date('H:i', strtotime($reserva['hora_inicio'])) ?> - <?= date('H:i', strtotime($reserva['hora_fin'])) ?></td>
                            <td>$<?= number_format($reserva['precio'], 2) ?></td>
                            <td>
                                <span class="badge bg-<?= getStatusColor($reserva['estado']) ?>">
                                    <?= ucfirst($reserva['estado']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($recentReservations)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No hay reservas registradas</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
// Datos para los gráficos
const ingresosPorMes = <?= json_encode($chartData['ingresos_por_mes']) ?>;
const ingresosPorClub = <?= json_encode($chartData['ingresos_por_club']) ?>;

// Gráfico de ingresos por mes
const ctxMes = document.getElementById('ingresosPorMesChart').getContext('2d');
new Chart(ctxMes, {
    type: 'line',
    data: {
        labels: ingresosPorMes.map(item => {
            const date = new Date(item.mes + '-01');
            return date.toLocaleDateString('es-MX', { month: 'short', year: 'numeric' });
        }),
        datasets: [{
            label: 'Ingresos',
            data: ingresosPorMes.map(item => parseFloat(item.total)),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
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
        }
    }
});

// Gráfico de ingresos por club
const ctxClub = document.getElementById('ingresosPorClubChart').getContext('2d');
new Chart(ctxClub, {
    type: 'doughnut',
    data: {
        labels: ingresosPorClub.map(item => item.nombre || 'Sin Club'),
        datasets: [{
            data: ingresosPorClub.map(item => parseFloat(item.total)),
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': $' + context.parsed.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
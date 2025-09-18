<?php
/**
 * Controlador del dashboard financiero
 */
require_once 'BaseController.php';

class DashboardController extends BaseController {
    
    public function index() {
        $this->requireAuth();
        
        $data = [
            'title' => 'Dashboard Financiero - ' . APP_NAME,
            'stats' => $this->getFinancialStats(),
            'chartData' => $this->getChartData(),
            'recentReservations' => $this->getRecentReservations()
        ];
        
        $this->render('dashboard/index', $data);
    }
    
    private function getFinancialStats() {
        try {
            // Total de ingresos (reservas completadas)
            $stmt = $this->db->query("SELECT SUM(precio) as total FROM reservas WHERE estado = 'completada'");
            $totalIngresos = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Total de ingresos adicionales (transacciones de ingreso autorizadas)
            $stmt = $this->db->query("SELECT SUM(monto) as total FROM transacciones WHERE tipo = 'ingreso' AND estado = 'autorizada'");
            $ingresosAdicionales = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Total de gastos (transacciones de gasto y retiro autorizadas)
            $stmt = $this->db->query("SELECT SUM(monto) as total FROM transacciones WHERE tipo IN ('gasto', 'retiro') AND estado = 'autorizada'");
            $totalGastos = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Ingresos del mes actual
            $stmt = $this->db->query("
                SELECT 
                    (SELECT COALESCE(SUM(precio), 0) FROM reservas 
                     WHERE estado = 'completada' 
                       AND MONTH(fecha_reserva) = MONTH(CURRENT_DATE()) 
                       AND YEAR(fecha_reserva) = YEAR(CURRENT_DATE())) +
                    (SELECT COALESCE(SUM(monto), 0) FROM transacciones 
                     WHERE tipo = 'ingreso' AND estado = 'autorizada'
                       AND MONTH(fecha_transaccion) = MONTH(CURRENT_DATE()) 
                       AND YEAR(fecha_transaccion) = YEAR(CURRENT_DATE())) as total
            ");
            $ingresosMes = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Gastos del mes actual
            $stmt = $this->db->query("
                SELECT COALESCE(SUM(monto), 0) as total FROM transacciones 
                WHERE tipo IN ('gasto', 'retiro') AND estado = 'autorizada'
                  AND MONTH(fecha_transaccion) = MONTH(CURRENT_DATE()) 
                  AND YEAR(fecha_transaccion) = YEAR(CURRENT_DATE())
            ");
            $gastosMes = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Total de reservas
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM reservas");
            $totalReservas = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Reservas pendientes
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM reservas WHERE estado = 'pendiente'");
            $reservasPendientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Transacciones pendientes de autorización
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM transacciones WHERE estado = 'pendiente'");
            $transaccionesPendientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            $totalIngresosCompleto = $totalIngresos + $ingresosAdicionales;
            $utilidadMes = $ingresosMes - $gastosMes;
            $utilidadTotal = $totalIngresosCompleto - $totalGastos;
            
            return [
                'total_ingresos' => $totalIngresosCompleto,
                'total_gastos' => $totalGastos,
                'utilidad_total' => $utilidadTotal,
                'ingresos_mes' => $ingresosMes,
                'gastos_mes' => $gastosMes,
                'utilidad_mes' => $utilidadMes,
                'total_reservas' => $totalReservas,
                'reservas_pendientes' => $reservasPendientes,
                'transacciones_pendientes' => $transaccionesPendientes
            ];
        } catch (PDOException $e) {
            error_log('Error obteniendo estadísticas: ' . $e->getMessage());
            return [
                'total_ingresos' => 0,
                'total_gastos' => 0,
                'utilidad_total' => 0,
                'ingresos_mes' => 0,
                'gastos_mes' => 0,
                'utilidad_mes' => 0,
                'total_reservas' => 0,
                'reservas_pendientes' => 0,
                'transacciones_pendientes' => 0
            ];
        }
    }
    
    private function getChartData() {
        try {
            // Ingresos vs Gastos por mes (últimos 6 meses)
            $stmt = $this->db->query("
                SELECT 
                    DATE_FORMAT(fecha, '%Y-%m') as mes,
                    SUM(CASE WHEN tipo IN ('ingreso', 'reserva') THEN monto ELSE 0 END) as ingresos,
                    SUM(CASE WHEN tipo IN ('gasto', 'retiro') THEN monto ELSE 0 END) as gastos
                FROM (
                    SELECT fecha_reserva as fecha, precio as monto, 'reserva' as tipo
                    FROM reservas 
                    WHERE estado = 'completada' 
                      AND fecha_reserva >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
                    
                    UNION ALL
                    
                    SELECT fecha_transaccion as fecha, monto, tipo
                    FROM transacciones 
                    WHERE estado = 'autorizada'
                      AND fecha_transaccion >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
                ) AS movimientos
                GROUP BY DATE_FORMAT(fecha, '%Y-%m')
                ORDER BY mes
            ");
            $ingresosPorMes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Ingresos por club (incluyendo transacciones)
            $stmt = $this->db->query("
                SELECT 
                    COALESCE(club_nombre, 'Sin Club') as nombre,
                    SUM(monto) as total
                FROM (
                    SELECT c.nombre as club_nombre, r.precio as monto
                    FROM reservas r
                    LEFT JOIN clubes c ON r.club_id = c.id
                    WHERE r.estado = 'completada'
                    
                    UNION ALL
                    
                    SELECT c.nombre as club_nombre, t.monto
                    FROM transacciones t
                    LEFT JOIN clubes c ON t.club_id = c.id
                    WHERE t.estado = 'autorizada' AND t.tipo = 'ingreso'
                ) AS ingresos
                GROUP BY club_nombre
                ORDER BY total DESC
                LIMIT 10
            ");
            $ingresosPorClub = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Gastos por categoría (último mes)
            $stmt = $this->db->query("
                SELECT 
                    c.nombre,
                    c.color,
                    SUM(t.monto) as total
                FROM transacciones t
                LEFT JOIN categorias c ON t.categoria_id = c.id
                WHERE t.estado = 'autorizada' 
                  AND t.tipo IN ('gasto', 'retiro')
                  AND t.fecha_transaccion >= DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)
                GROUP BY c.id, c.nombre, c.color
                ORDER BY total DESC
                LIMIT 8
            ");
            $gastosPorCategoria = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'ingresos_por_mes' => $ingresosPorMes,
                'ingresos_por_club' => $ingresosPorClub,
                'gastos_por_categoria' => $gastosPorCategoria
            ];
        } catch (PDOException $e) {
            error_log('Error obteniendo datos de gráficos: ' . $e->getMessage());
            return [
                'ingresos_por_mes' => [],
                'ingresos_por_club' => [],
                'gastos_por_categoria' => []
            ];
        }
    }
    
    private function getRecentReservations() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    r.*,
                    u.nombre as usuario_nombre,
                    c.nombre as club_nombre
                FROM reservas r
                LEFT JOIN usuarios u ON r.usuario_id = u.id
                LEFT JOIN clubes c ON r.club_id = c.id
                ORDER BY r.fecha_registro DESC
                LIMIT 10
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error obteniendo reservas recientes: ' . $e->getMessage());
            return [];
        }
    }
}
?>
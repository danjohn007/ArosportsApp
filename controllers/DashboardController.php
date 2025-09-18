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
            // Total de ingresos
            $stmt = $this->db->query("SELECT SUM(precio) as total FROM reservas WHERE estado = 'completada'");
            $totalIngresos = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Ingresos del mes actual
            $stmt = $this->db->query("SELECT SUM(precio) as total FROM reservas WHERE estado = 'completada' AND MONTH(fecha_reserva) = MONTH(CURRENT_DATE()) AND YEAR(fecha_reserva) = YEAR(CURRENT_DATE())");
            $ingresosMes = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Total de reservas
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM reservas");
            $totalReservas = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Reservas pendientes
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM reservas WHERE estado = 'pendiente'");
            $reservasPendientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            return [
                'total_ingresos' => $totalIngresos,
                'ingresos_mes' => $ingresosMes,
                'total_reservas' => $totalReservas,
                'reservas_pendientes' => $reservasPendientes
            ];
        } catch (PDOException $e) {
            error_log('Error obteniendo estadísticas: ' . $e->getMessage());
            return [
                'total_ingresos' => 0,
                'ingresos_mes' => 0,
                'total_reservas' => 0,
                'reservas_pendientes' => 0
            ];
        }
    }
    
    private function getChartData() {
        try {
            // Ingresos por mes (últimos 6 meses)
            $stmt = $this->db->query("
                SELECT 
                    DATE_FORMAT(fecha_reserva, '%Y-%m') as mes,
                    SUM(precio) as total
                FROM reservas 
                WHERE estado = 'completada' 
                    AND fecha_reserva >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(fecha_reserva, '%Y-%m')
                ORDER BY mes
            ");
            $ingresosPorMes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Ingresos por club
            $stmt = $this->db->query("
                SELECT 
                    c.nombre,
                    SUM(r.precio) as total
                FROM reservas r
                LEFT JOIN clubes c ON r.club_id = c.id
                WHERE r.estado = 'completada'
                GROUP BY c.id, c.nombre
                ORDER BY total DESC
            ");
            $ingresosPorClub = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'ingresos_por_mes' => $ingresosPorMes,
                'ingresos_por_club' => $ingresosPorClub
            ];
        } catch (PDOException $e) {
            error_log('Error obteniendo datos de gráficos: ' . $e->getMessage());
            return [
                'ingresos_por_mes' => [],
                'ingresos_por_club' => []
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
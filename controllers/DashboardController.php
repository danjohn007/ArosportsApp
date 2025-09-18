<?php
/**
 * Controlador del dashboard financiero
 */
require_once 'BaseController.php';

class DashboardController extends BaseController {
    
    public function index() {
        $this->requireAuth();
        
        // Procesar filtros
        $filters = $this->processFilters();
        
        $data = [
            'title' => 'Dashboard Financiero - ' . APP_NAME,
            'stats' => $this->getFinancialStats($filters),
            'chartData' => $this->getChartData($filters),
            'recentReservations' => $this->getRecentReservations($filters),
            'filters' => $filters,
            'filterOptions' => $this->getFilterOptions()
        ];
        
        $this->render('dashboard/index', $data);
    }
    
    private function processFilters() {
        // Filtros de fecha (para todos los usuarios)
        $fechaInicio = $_GET['fecha_inicio'] ?? $_POST['fecha_inicio'] ?? '';
        $fechaFin = $_GET['fecha_fin'] ?? $_POST['fecha_fin'] ?? '';
        
        // Si no hay fechas, usar últimos 30 días por defecto
        if (empty($fechaInicio)) {
            $fechaInicio = date('Y-m-d', strtotime('-30 days'));
        }
        if (empty($fechaFin)) {
            $fechaFin = date('Y-m-d');
        }
        
        $filters = [
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin
        ];
        
        // Filtros adicionales para SuperAdmin
        if (($_SESSION['user_role'] ?? '') === 'superadmin') {
            $filters['club_id'] = $_GET['club_id'] ?? $_POST['club_id'] ?? '';
            $filters['empresa_id'] = $_GET['empresa_id'] ?? $_POST['empresa_id'] ?? '';
            $filters['fraccionamiento_id'] = $_GET['fraccionamiento_id'] ?? $_POST['fraccionamiento_id'] ?? '';
        }
        
        return $filters;
    }
    
    private function getFilterOptions() {
        $options = [];
        
        // Solo para SuperAdmin
        if (($_SESSION['user_role'] ?? '') === 'superadmin') {
            try {
                // Obtener clubes
                $stmt = $this->db->query("SELECT id, nombre FROM clubes WHERE activo = 1 ORDER BY nombre");
                $options['clubes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Obtener empresas
                $stmt = $this->db->query("SELECT id, nombre FROM empresas WHERE activo = 1 ORDER BY nombre");
                $options['empresas'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Obtener fraccionamientos
                $stmt = $this->db->query("SELECT id, nombre FROM fraccionamientos WHERE activo = 1 ORDER BY nombre");
                $options['fraccionamientos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log('Error obteniendo opciones de filtro: ' . $e->getMessage());
                $options = ['clubes' => [], 'empresas' => [], 'fraccionamientos' => []];
            }
        }
        
        return $options;
    }
    
    private function getFinancialStats($filters = []) {
        try {
            // Construir condiciones WHERE para filtros
            $whereConditions = $this->buildWhereConditions($filters);
            $params = $this->buildQueryParams($filters);
            
            // Total de ingresos (reservas completadas) con filtros
            $sql = "SELECT SUM(precio) as total FROM reservas WHERE estado = 'completada'";
            if (!empty($whereConditions['reservas'])) {
                $sql .= " AND " . $whereConditions['reservas'];
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params['reservas']);
            $totalIngresos = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Total de ingresos adicionales (transacciones de ingreso autorizadas) con filtros
            $sql = "SELECT SUM(monto) as total FROM transacciones WHERE tipo = 'ingreso' AND estado = 'autorizada'";
            if (!empty($whereConditions['transacciones'])) {
                $sql .= " AND " . $whereConditions['transacciones'];
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params['transacciones']);
            $ingresosAdicionales = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Total de gastos (transacciones de gasto y retiro autorizadas) con filtros  
            $sql = "SELECT SUM(monto) as total FROM transacciones WHERE tipo IN ('gasto', 'retiro') AND estado = 'autorizada'";
            if (!empty($whereConditions['transacciones'])) {
                $sql .= " AND " . $whereConditions['transacciones'];
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params['transacciones']);
            $totalGastos = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Ingresos del periodo actual
            $sql = "
                SELECT 
                    (SELECT COALESCE(SUM(precio), 0) FROM reservas 
                     WHERE estado = 'completada' 
                       AND fecha_reserva >= ? AND fecha_reserva <= ?
                       " . (!empty($whereConditions['reservas_simple']) ? " AND " . $whereConditions['reservas_simple'] : "") . ") +
                    (SELECT COALESCE(SUM(monto), 0) FROM transacciones 
                     WHERE tipo = 'ingreso' AND estado = 'autorizada'
                       AND fecha_transaccion >= ? AND fecha_transaccion <= ?
                       " . (!empty($whereConditions['transacciones_simple']) ? " AND " . $whereConditions['transacciones_simple'] : "") . ") as total
            ";
            $paramsIngresos = array_merge(
                [$filters['fecha_inicio'], $filters['fecha_fin']], 
                $params['reservas_simple'] ?? [],
                [$filters['fecha_inicio'], $filters['fecha_fin']], 
                $params['transacciones_simple'] ?? []
            );
            $stmt = $this->db->prepare($sql);
            $stmt->execute($paramsIngresos);
            $ingresosPeriodo = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Gastos del periodo actual
            $sql = "
                SELECT COALESCE(SUM(monto), 0) as total FROM transacciones 
                WHERE tipo IN ('gasto', 'retiro') AND estado = 'autorizada'
                  AND fecha_transaccion >= ? AND fecha_transaccion <= ?
                  " . (!empty($whereConditions['transacciones_simple']) ? " AND " . $whereConditions['transacciones_simple'] : "");
            $paramsGastos = array_merge([$filters['fecha_inicio'], $filters['fecha_fin']], $params['transacciones_simple'] ?? []);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($paramsGastos);
            $gastosPeriodo = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Total de reservas con filtros
            $sql = "SELECT COUNT(*) as total FROM reservas WHERE 1=1";
            if (!empty($whereConditions['reservas'])) {
                $sql .= " AND " . $whereConditions['reservas'];
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params['reservas']);
            $totalReservas = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Reservas pendientes con filtros
            $sql = "SELECT COUNT(*) as total FROM reservas WHERE estado = 'pendiente'";
            if (!empty($whereConditions['reservas'])) {
                $sql .= " AND " . $whereConditions['reservas'];
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params['reservas']);
            $reservasPendientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Transacciones pendientes con filtros
            $sql = "SELECT COUNT(*) as total FROM transacciones WHERE estado = 'pendiente'";
            if (!empty($whereConditions['transacciones'])) {
                $sql .= " AND " . $whereConditions['transacciones'];
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params['transacciones']);
            $transaccionesPendientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            $totalIngresosCompleto = $totalIngresos + $ingresosAdicionales;
            $utilidadPeriodo = $ingresosPeriodo - $gastosPeriodo;
            $utilidadTotal = $totalIngresosCompleto - $totalGastos;
            
            return [
                'total_ingresos' => $totalIngresosCompleto,
                'total_gastos' => $totalGastos,
                'utilidad_total' => $utilidadTotal,
                'ingresos_periodo' => $ingresosPeriodo,
                'gastos_periodo' => $gastosPeriodo,
                'utilidad_periodo' => $utilidadPeriodo,
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
                'ingresos_periodo' => 0,
                'gastos_periodo' => 0,
                'utilidad_periodo' => 0,
                'total_reservas' => 0,
                'reservas_pendientes' => 0,
                'transacciones_pendientes' => 0
            ];
        }
    }
    
    private function buildWhereConditions($filters) {
        $conditions = [
            'reservas' => [],
            'transacciones' => [],
            'reservas_simple' => [],
            'transacciones_simple' => []
        ];
        
        // Filtros de fecha
        if (!empty($filters['fecha_inicio']) && !empty($filters['fecha_fin'])) {
            $conditions['reservas'][] = 'fecha_reserva >= ? AND fecha_reserva <= ?';
            $conditions['transacciones'][] = 'fecha_transaccion >= ? AND fecha_transaccion <= ?';
        }
        
        // Filtros de entidad para SuperAdmin
        if (($_SESSION['user_role'] ?? '') === 'superadmin') {
            if (!empty($filters['club_id'])) {
                $conditions['reservas'][] = 'club_id = ?';
                $conditions['transacciones'][] = 'club_id = ?';
                $conditions['reservas_simple'][] = 'club_id = ?';
                $conditions['transacciones_simple'][] = 'club_id = ?';
            }
            
            if (!empty($filters['empresa_id'])) {
                $conditions['reservas'][] = 'empresa_id = ?';
                $conditions['transacciones'][] = 'empresa_id = ?';
                $conditions['reservas_simple'][] = 'empresa_id = ?';
                $conditions['transacciones_simple'][] = 'empresa_id = ?';
            }
            
            if (!empty($filters['fraccionamiento_id'])) {
                $conditions['reservas'][] = 'fraccionamiento_id = ?';
                $conditions['transacciones'][] = 'fraccionamiento_id = ?';
                $conditions['reservas_simple'][] = 'fraccionamiento_id = ?';
                $conditions['transacciones_simple'][] = 'fraccionamiento_id = ?';
            }
        }
        
        return [
            'reservas' => implode(' AND ', $conditions['reservas']),
            'transacciones' => implode(' AND ', $conditions['transacciones']),
            'reservas_simple' => implode(' AND ', $conditions['reservas_simple']),
            'transacciones_simple' => implode(' AND ', $conditions['transacciones_simple'])
        ];
    }
    
    private function buildQueryParams($filters) {
        $params = [
            'reservas' => [],
            'transacciones' => [],
            'reservas_simple' => [],
            'transacciones_simple' => []
        ];
        
        // Parámetros para fechas
        if (!empty($filters['fecha_inicio']) && !empty($filters['fecha_fin'])) {
            $params['reservas'] = array_merge($params['reservas'], [$filters['fecha_inicio'], $filters['fecha_fin']]);
            $params['transacciones'] = array_merge($params['transacciones'], [$filters['fecha_inicio'], $filters['fecha_fin']]);
        }
        
        // Parámetros para entidades (SuperAdmin)
        if (($_SESSION['user_role'] ?? '') === 'superadmin') {
            if (!empty($filters['club_id'])) {
                $params['reservas'][] = $filters['club_id'];
                $params['transacciones'][] = $filters['club_id'];
                $params['reservas_simple'][] = $filters['club_id'];
                $params['transacciones_simple'][] = $filters['club_id'];
            }
            
            if (!empty($filters['empresa_id'])) {
                $params['reservas'][] = $filters['empresa_id'];
                $params['transacciones'][] = $filters['empresa_id'];
                $params['reservas_simple'][] = $filters['empresa_id'];
                $params['transacciones_simple'][] = $filters['empresa_id'];
            }
            
            if (!empty($filters['fraccionamiento_id'])) {
                $params['reservas'][] = $filters['fraccionamiento_id'];
                $params['transacciones'][] = $filters['fraccionamiento_id'];
                $params['reservas_simple'][] = $filters['fraccionamiento_id'];
                $params['transacciones_simple'][] = $filters['fraccionamiento_id'];
            }
        }
        
        return $params;
    }
    
    private function getChartData($filters = []) {
        try {
            $whereConditions = $this->buildWhereConditions($filters);
            $params = $this->buildQueryParams($filters);
            
            // Ingresos vs Gastos por día (en lugar de por mes)
            $sql = "
                SELECT 
                    DATE_FORMAT(fecha, '%Y-%m-%d') as fecha,
                    SUM(CASE WHEN tipo IN ('ingreso', 'reserva') THEN monto ELSE 0 END) as ingresos,
                    SUM(CASE WHEN tipo IN ('gasto', 'retiro') THEN monto ELSE 0 END) as gastos
                FROM (
                    SELECT fecha_reserva as fecha, precio as monto, 'reserva' as tipo
                    FROM reservas 
                    WHERE estado = 'completada' 
                      AND fecha_reserva >= ? AND fecha_reserva <= ?
                      " . (!empty($whereConditions['reservas_simple']) ? " AND " . $whereConditions['reservas_simple'] : "") . "
                    
                    UNION ALL
                    
                    SELECT fecha_transaccion as fecha, monto, tipo
                    FROM transacciones 
                    WHERE estado = 'autorizada'
                      AND fecha_transaccion >= ? AND fecha_transaccion <= ?
                      " . (!empty($whereConditions['transacciones_simple']) ? " AND " . $whereConditions['transacciones_simple'] : "") . "
                ) AS movimientos
                GROUP BY DATE_FORMAT(fecha, '%Y-%m-%d')
                ORDER BY fecha
            ";
            $chartParams = array_merge(
                [$filters['fecha_inicio'], $filters['fecha_fin']], 
                $params['reservas_simple'] ?? [],
                [$filters['fecha_inicio'], $filters['fecha_fin']], 
                $params['transacciones_simple'] ?? []
            );
            $stmt = $this->db->prepare($sql);
            $stmt->execute($chartParams);
            $ingresosPorDia = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Ingresos por club (incluyendo transacciones) con filtros
            $sql = "
                SELECT 
                    COALESCE(club_nombre, 'Sin Club') as nombre,
                    SUM(monto) as total
                FROM (
                    SELECT c.nombre as club_nombre, r.precio as monto
                    FROM reservas r
                    LEFT JOIN clubes c ON r.club_id = c.id
                    WHERE r.estado = 'completada'
                      AND r.fecha_reserva >= ? AND r.fecha_reserva <= ?
                      " . (!empty($whereConditions['reservas_simple']) ? " AND " . $whereConditions['reservas_simple'] : "") . "
                    
                    UNION ALL
                    
                    SELECT c.nombre as club_nombre, t.monto
                    FROM transacciones t
                    LEFT JOIN clubes c ON t.club_id = c.id
                    WHERE t.estado = 'autorizada' AND t.tipo = 'ingreso'
                      AND t.fecha_transaccion >= ? AND t.fecha_transaccion <= ?
                      " . (!empty($whereConditions['transacciones_simple']) ? " AND " . $whereConditions['transacciones_simple'] : "") . "
                ) AS ingresos
                GROUP BY club_nombre
                ORDER BY total DESC
                LIMIT 10
            ";
            $clubParams = array_merge(
                [$filters['fecha_inicio'], $filters['fecha_fin']], 
                $params['reservas_simple'] ?? [],
                [$filters['fecha_inicio'], $filters['fecha_fin']], 
                $params['transacciones_simple'] ?? []
            );
            $stmt = $this->db->prepare($sql);
            $stmt->execute($clubParams);
            $ingresosPorClub = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Gastos por categoría (período seleccionado) con filtros
            $sql = "
                SELECT 
                    c.nombre,
                    c.color,
                    SUM(t.monto) as total
                FROM transacciones t
                LEFT JOIN categorias c ON t.categoria_id = c.id
                WHERE t.estado = 'autorizada' 
                  AND t.tipo IN ('gasto', 'retiro')
                  AND t.fecha_transaccion >= ? AND t.fecha_transaccion <= ?
                  " . (!empty($whereConditions['transacciones_simple']) ? " AND " . $whereConditions['transacciones_simple'] : "") . "
                GROUP BY c.id, c.nombre, c.color
                ORDER BY total DESC
                LIMIT 8
            ";
            $gastosParams = array_merge([$filters['fecha_inicio'], $filters['fecha_fin']], $params['transacciones_simple'] ?? []);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($gastosParams);
            $gastosPorCategoria = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'ingresos_por_dia' => $ingresosPorDia,
                'ingresos_por_club' => $ingresosPorClub,
                'gastos_por_categoria' => $gastosPorCategoria
            ];
        } catch (PDOException $e) {
            error_log('Error obteniendo datos de gráficos: ' . $e->getMessage());
            return [
                'ingresos_por_dia' => [],
                'ingresos_por_club' => [],
                'gastos_por_categoria' => []
            ];
        }
    }
    
    private function getRecentReservations($filters = []) {
        try {
            $whereConditions = $this->buildWhereConditions($filters);
            $params = $this->buildQueryParams($filters);
            
            $sql = "
                SELECT 
                    r.*,
                    u.nombre as usuario_nombre,
                    c.nombre as club_nombre
                FROM reservas r
                LEFT JOIN usuarios u ON r.usuario_id = u.id
                LEFT JOIN clubes c ON r.club_id = c.id
                WHERE 1=1
            ";
            
            if (!empty($whereConditions['reservas'])) {
                $sql .= " AND " . $whereConditions['reservas'];
            }
            
            $sql .= " ORDER BY r.fecha_registro DESC LIMIT 10";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params['reservas']);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error obteniendo reservas recientes: ' . $e->getMessage());
            return [];
        }
    }
}
?>
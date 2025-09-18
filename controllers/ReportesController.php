<?php
/**
 * Controlador de reportes con filtros avanzados
 */
require_once 'BaseController.php';

class ReportesController extends BaseController {
    
    public function index() {
        $this->requireAuth('superadmin');
        
        $data = [
            'title' => 'Reportes del Sistema - ' . APP_NAME,
            'filtros' => $this->getFiltrosData(),
            'reporteData' => []
        ];
        
        // Si hay filtros aplicados, generar el reporte
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || !empty($_GET['generate'])) {
            $filtros = $this->procesarFiltros();
            $data['reporteData'] = $this->generarReporte($filtros);
            $data['filtrosAplicados'] = $filtros;
        }
        
        $this->render('reportes/index', $data);
    }
    
    private function getFiltrosData() {
        try {
            // Obtener usuarios para filtro
            $stmt = $this->db->query("SELECT id, nombre, tipo_usuario FROM usuarios WHERE activo = 1 ORDER BY nombre");
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener clubes para filtro
            $stmt = $this->db->query("SELECT id, nombre FROM clubes WHERE activo = 1 ORDER BY nombre");
            $clubes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener fraccionamientos para filtro
            $stmt = $this->db->query("SELECT id, nombre FROM fraccionamientos WHERE activo = 1 ORDER BY nombre");
            $fraccionamientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener empresas para filtro
            $stmt = $this->db->query("SELECT id, nombre FROM empresas WHERE activo = 1 ORDER BY nombre");
            $empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'usuarios' => $usuarios,
                'clubes' => $clubes,
                'fraccionamientos' => $fraccionamientos,
                'empresas' => $empresas
            ];
        } catch (PDOException $e) {
            error_log('Error obteniendo datos de filtros: ' . $e->getMessage());
            return [
                'usuarios' => [],
                'clubes' => [],
                'fraccionamientos' => [],
                'empresas' => []
            ];
        }
    }
    
    private function procesarFiltros() {
        return [
            'fecha_inicio' => $_POST['fecha_inicio'] ?? $_GET['fecha_inicio'] ?? '',
            'fecha_fin' => $_POST['fecha_fin'] ?? $_GET['fecha_fin'] ?? '',
            'usuario_id' => $_POST['usuario_id'] ?? $_GET['usuario_id'] ?? '',
            'club_id' => $_POST['club_id'] ?? $_GET['club_id'] ?? '',
            'fraccionamiento_id' => $_POST['fraccionamiento_id'] ?? $_GET['fraccionamiento_id'] ?? '',
            'empresa_id' => $_POST['empresa_id'] ?? $_GET['empresa_id'] ?? '',
            'estado' => $_POST['estado'] ?? $_GET['estado'] ?? '',
            'tipo_usuario' => $_POST['tipo_usuario'] ?? $_GET['tipo_usuario'] ?? '',
            'tipo_reporte' => $_POST['tipo_reporte'] ?? $_GET['tipo_reporte'] ?? 'resumen'
        ];
    }
    
    private function generarReporte($filtros) {
        try {
            $whereConditions = ['1=1'];
            $params = [];
            
            // Aplicar filtros de fecha
            if (!empty($filtros['fecha_inicio'])) {
                $whereConditions[] = 'r.fecha_reserva >= ?';
                $params[] = $filtros['fecha_inicio'];
            }
            
            if (!empty($filtros['fecha_fin'])) {
                $whereConditions[] = 'r.fecha_reserva <= ?';
                $params[] = $filtros['fecha_fin'];
            }
            
            // Aplicar filtros de entidades
            if (!empty($filtros['usuario_id'])) {
                $whereConditions[] = 'r.usuario_id = ?';
                $params[] = $filtros['usuario_id'];
            }
            
            if (!empty($filtros['club_id'])) {
                $whereConditions[] = 'r.club_id = ?';
                $params[] = $filtros['club_id'];
            }
            
            if (!empty($filtros['fraccionamiento_id'])) {
                $whereConditions[] = 'r.fraccionamiento_id = ?';
                $params[] = $filtros['fraccionamiento_id'];
            }
            
            if (!empty($filtros['empresa_id'])) {
                $whereConditions[] = 'r.empresa_id = ?';
                $params[] = $filtros['empresa_id'];
            }
            
            if (!empty($filtros['estado'])) {
                $whereConditions[] = 'r.estado = ?';
                $params[] = $filtros['estado'];
            }
            
            if (!empty($filtros['tipo_usuario'])) {
                $whereConditions[] = 'u.tipo_usuario = ?';
                $params[] = $filtros['tipo_usuario'];
            }
            
            $whereClause = implode(' AND ', $whereConditions);
            
            // Generar diferentes tipos de reporte
            switch ($filtros['tipo_reporte']) {
                case 'financiero':
                    return $this->generarReporteFinanciero($whereClause, $params);
                case 'usuarios':
                    return $this->generarReporteUsuarios($whereClause, $params);
                case 'actividad':
                    return $this->generarReporteActividad($whereClause, $params);
                default:
                    return $this->generarReporteResumen($whereClause, $params);
            }
            
        } catch (PDOException $e) {
            error_log('Error generando reporte: ' . $e->getMessage());
            return [
                'error' => 'Error al generar el reporte: ' . $e->getMessage(),
                'resumen' => [],
                'detalles' => []
            ];
        }
    }
    
    private function generarReporteResumen($whereClause, $params) {
        // Resumen general
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_reservas,
                COUNT(CASE WHEN r.estado = 'completada' THEN 1 END) as reservas_completadas,
                COUNT(CASE WHEN r.estado = 'pendiente' THEN 1 END) as reservas_pendientes,
                COUNT(CASE WHEN r.estado = 'cancelada' THEN 1 END) as reservas_canceladas,
                COALESCE(SUM(CASE WHEN r.estado = 'completada' THEN r.precio ELSE 0 END), 0) as ingresos_totales,
                COALESCE(AVG(CASE WHEN r.estado = 'completada' THEN r.precio ELSE NULL END), 0) as precio_promedio
            FROM reservas r
            LEFT JOIN usuarios u ON r.usuario_id = u.id
            LEFT JOIN clubes c ON r.club_id = c.id
            LEFT JOIN fraccionamientos f ON r.fraccionamiento_id = f.id
            LEFT JOIN empresas e ON r.empresa_id = e.id
            WHERE $whereClause
        ");
        $stmt->execute($params);
        $resumen = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Detalles por mes
        $stmt = $this->db->prepare("
            SELECT 
                DATE_FORMAT(r.fecha_reserva, '%Y-%m') as mes,
                COUNT(*) as total_reservas,
                COALESCE(SUM(CASE WHEN r.estado = 'completada' THEN r.precio ELSE 0 END), 0) as ingresos
            FROM reservas r
            LEFT JOIN usuarios u ON r.usuario_id = u.id
            LEFT JOIN clubes c ON r.club_id = c.id
            LEFT JOIN fraccionamientos f ON r.fraccionamiento_id = f.id
            LEFT JOIN empresas e ON r.empresa_id = e.id
            WHERE $whereClause
            GROUP BY DATE_FORMAT(r.fecha_reserva, '%Y-%m')
            ORDER BY mes DESC
            LIMIT 12
        ");
        $stmt->execute($params);
        $detallesPorMes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'tipo' => 'resumen',
            'resumen' => $resumen,
            'detalles_por_mes' => $detallesPorMes
        ];
    }
    
    private function generarReporteFinanciero($whereClause, $params) {
        // Ingresos por club
        $stmt = $this->db->prepare("
            SELECT 
                COALESCE(c.nombre, 'Sin Club') as club_nombre,
                COUNT(*) as total_reservas,
                COALESCE(SUM(CASE WHEN r.estado = 'completada' THEN r.precio ELSE 0 END), 0) as ingresos_totales
            FROM reservas r
            LEFT JOIN usuarios u ON r.usuario_id = u.id
            LEFT JOIN clubes c ON r.club_id = c.id
            LEFT JOIN fraccionamientos f ON r.fraccionamiento_id = f.id
            LEFT JOIN empresas e ON r.empresa_id = e.id
            WHERE $whereClause AND r.estado = 'completada'
            GROUP BY c.id, c.nombre
            ORDER BY ingresos_totales DESC
        ");
        $stmt->execute($params);
        $ingresosPorClub = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Ingresos por tipo de usuario
        $stmt = $this->db->prepare("
            SELECT 
                u.tipo_usuario,
                COUNT(*) as total_reservas,
                COALESCE(SUM(r.precio), 0) as ingresos_totales
            FROM reservas r
            LEFT JOIN usuarios u ON r.usuario_id = u.id
            LEFT JOIN clubes c ON r.club_id = c.id
            LEFT JOIN fraccionamientos f ON r.fraccionamiento_id = f.id
            LEFT JOIN empresas e ON r.empresa_id = e.id
            WHERE $whereClause AND r.estado = 'completada'
            GROUP BY u.tipo_usuario
            ORDER BY ingresos_totales DESC
        ");
        $stmt->execute($params);
        $ingresosPorTipoUsuario = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'tipo' => 'financiero',
            'ingresos_por_club' => $ingresosPorClub,
            'ingresos_por_tipo_usuario' => $ingresosPorTipoUsuario
        ];
    }
    
    private function generarReporteUsuarios($whereClause, $params) {
        // Actividad por usuario
        $stmt = $this->db->prepare("
            SELECT 
                u.nombre as usuario_nombre,
                u.email as usuario_email,
                u.tipo_usuario,
                COUNT(*) as total_reservas,
                COUNT(CASE WHEN r.estado = 'completada' THEN 1 END) as reservas_completadas,
                COALESCE(SUM(CASE WHEN r.estado = 'completada' THEN r.precio ELSE 0 END), 0) as total_gastado
            FROM reservas r
            LEFT JOIN usuarios u ON r.usuario_id = u.id
            LEFT JOIN clubes c ON r.club_id = c.id
            LEFT JOIN fraccionamientos f ON r.fraccionamiento_id = f.id
            LEFT JOIN empresas e ON r.empresa_id = e.id
            WHERE $whereClause
            GROUP BY u.id, u.nombre, u.email, u.tipo_usuario
            ORDER BY total_reservas DESC
            LIMIT 50
        ");
        $stmt->execute($params);
        $actividadUsuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'tipo' => 'usuarios',
            'actividad_usuarios' => $actividadUsuarios
        ];
    }
    
    private function generarReporteActividad($whereClause, $params) {
        // Actividad detallada
        $stmt = $this->db->prepare("
            SELECT 
                r.*,
                u.nombre as usuario_nombre,
                u.email as usuario_email,
                u.tipo_usuario,
                COALESCE(c.nombre, 'Sin Club') as club_nombre,
                COALESCE(f.nombre, 'Sin Fraccionamiento') as fraccionamiento_nombre,
                COALESCE(e.nombre, 'Sin Empresa') as empresa_nombre
            FROM reservas r
            LEFT JOIN usuarios u ON r.usuario_id = u.id
            LEFT JOIN clubes c ON r.club_id = c.id
            LEFT JOIN fraccionamientos f ON r.fraccionamiento_id = f.id
            LEFT JOIN empresas e ON r.empresa_id = e.id
            WHERE $whereClause
            ORDER BY r.fecha_reserva DESC, r.hora_inicio DESC
            LIMIT 1000
        ");
        $stmt->execute($params);
        $actividadDetallada = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'tipo' => 'actividad',
            'actividad_detallada' => $actividadDetallada
        ];
    }
}
?>
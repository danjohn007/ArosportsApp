<?php
/**
 * Controlador para gestión de transacciones (gastos y retiros)
 */
require_once 'BaseController.php';

class TransaccionesController extends BaseController {
    
    public function index() {
        $this->requireAuth();
        
        $action = $_GET['action'] ?? 'list';
        
        switch ($action) {
            case 'create':
                $this->create();
                break;
            case 'edit':
                $this->edit();
                break;
            case 'delete':
                $this->delete();
                break;
            case 'authorize':
                $this->authorize();
                break;
            default:
                $this->list();
                break;
        }
    }
    
    private function list() {
        try {
            // Filtros
            $tipo = $_GET['tipo'] ?? '';
            $categoria = $_GET['categoria'] ?? '';
            $estado = $_GET['estado'] ?? '';
            $fecha_desde = $_GET['fecha_desde'] ?? '';
            $fecha_hasta = $_GET['fecha_hasta'] ?? '';
            
            // Construir query
            $whereConditions = ["1=1"];
            $params = [];
            
            if ($tipo) {
                $whereConditions[] = "t.tipo = ?";
                $params[] = $tipo;
            }
            
            if ($categoria) {
                $whereConditions[] = "t.categoria_id = ?";
                $params[] = $categoria;
            }
            
            if ($estado) {
                $whereConditions[] = "t.estado = ?";
                $params[] = $estado;
            }
            
            if ($fecha_desde) {
                $whereConditions[] = "t.fecha_transaccion >= ?";
                $params[] = $fecha_desde;
            }
            
            if ($fecha_hasta) {
                $whereConditions[] = "t.fecha_transaccion <= ?";
                $params[] = $fecha_hasta;
            }
            
            $whereClause = implode(" AND ", $whereConditions);
            
            $stmt = $this->db->prepare("
                SELECT 
                    t.*,
                    c.nombre as categoria_nombre,
                    c.color as categoria_color,
                    u.nombre as usuario_nombre,
                    cl.nombre as club_nombre,
                    au.nombre as autorizada_por_nombre
                FROM transacciones t
                LEFT JOIN categorias c ON t.categoria_id = c.id
                LEFT JOIN usuarios u ON t.usuario_id = u.id
                LEFT JOIN clubes cl ON t.club_id = cl.id
                LEFT JOIN usuarios au ON t.autorizada_por = au.id
                WHERE $whereClause
                ORDER BY t.fecha_transaccion DESC, t.fecha_registro DESC
                LIMIT 100
            ");
            
            $stmt->execute($params);
            $transacciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener categorías para filtros
            $stmt = $this->db->query("SELECT * FROM categorias WHERE activo = 1 ORDER BY tipo, nombre");
            $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener clubes para filtros
            $stmt = $this->db->query("SELECT * FROM clubes WHERE activo = 1 ORDER BY nombre");
            $clubes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $data = [
                'title' => 'Gestión de Transacciones - ' . APP_NAME,
                'transacciones' => $transacciones,
                'categorias' => $categorias,
                'clubes' => $clubes,
                'filtros' => [
                    'tipo' => $tipo,
                    'categoria' => $categoria,
                    'estado' => $estado,
                    'fecha_desde' => $fecha_desde,
                    'fecha_hasta' => $fecha_hasta
                ]
            ];
            
            $this->render('admin/transacciones/list', $data);
            
        } catch (PDOException $e) {
            error_log('Error obteniendo transacciones: ' . $e->getMessage());
            $this->render('admin/transacciones/list', [
                'title' => 'Gestión de Transacciones - ' . APP_NAME,
                'error' => 'Error al obtener las transacciones',
                'transacciones' => [],
                'categorias' => [],
                'clubes' => []
            ]);
        }
    }
    
    private function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $categoria_id = $_POST['categoria_id'] ?? '';
                $tipo = $_POST['tipo'] ?? 'gasto';
                $concepto = $_POST['concepto'] ?? '';
                $descripcion = $_POST['descripcion'] ?? '';
                $monto = $_POST['monto'] ?? 0;
                $fecha_transaccion = $_POST['fecha_transaccion'] ?? date('Y-m-d');
                $club_id = $_POST['club_id'] ?? null;
                $metodo_pago = $_POST['metodo_pago'] ?? 'efectivo';
                $referencia = $_POST['referencia'] ?? '';
                $observaciones = $_POST['observaciones'] ?? '';
                
                // Validaciones
                if (empty($categoria_id) || empty($concepto) || $monto <= 0) {
                    throw new Exception('Todos los campos obligatorios deben estar completos');
                }
                
                // Determinar estado inicial según tipo de usuario
                $estado = 'pendiente';
                if ($_SESSION['user']['tipo_usuario'] === 'superadmin') {
                    $estado = 'autorizada';
                    $autorizada_por = $_SESSION['user']['id'];
                    $fecha_autorizacion = date('Y-m-d H:i:s');
                } else {
                    $autorizada_por = null;
                    $fecha_autorizacion = null;
                }
                
                $stmt = $this->db->prepare("
                    INSERT INTO transacciones 
                    (categoria_id, usuario_id, club_id, tipo, concepto, descripcion, monto, 
                     fecha_transaccion, metodo_pago, referencia, estado, autorizada_por, 
                     fecha_autorizacion, observaciones)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $categoria_id,
                    $_SESSION['user']['id'],
                    $club_id ?: null,
                    $tipo,
                    $concepto,
                    $descripcion,
                    $monto,
                    $fecha_transaccion,
                    $metodo_pago,
                    $referencia ?: null,
                    $estado,
                    $autorizada_por,
                    $fecha_autorizacion,
                    $observaciones ?: null
                ]);
                
                // Respuesta JSON para AJAX
                if ($_SERVER['HTTP_ACCEPT'] && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Transacción creada exitosamente']);
                    return;
                }
                
                header('Location: ' . BASE_URL . '/admin/transacciones?success=created');
                exit;
                
            } catch (Exception $e) {
                error_log('Error creando transacción: ' . $e->getMessage());
                $error = $e->getMessage();
            }
        }
        
        // Obtener datos para el formulario
        try {
            $stmt = $this->db->query("SELECT * FROM categorias WHERE activo = 1 ORDER BY tipo, nombre");
            $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stmt = $this->db->query("SELECT * FROM clubes WHERE activo = 1 ORDER BY nombre");
            $clubes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $data = [
                'title' => 'Nueva Transacción - ' . APP_NAME,
                'categorias' => $categorias,
                'clubes' => $clubes,
                'error' => $error ?? null,
                'old' => $_POST ?? []
            ];
            
            $this->render('admin/transacciones/form', $data);
            
        } catch (PDOException $e) {
            error_log('Error obteniendo datos para formulario: ' . $e->getMessage());
            $this->render('admin/transacciones/form', [
                'title' => 'Nueva Transacción - ' . APP_NAME,
                'error' => 'Error al cargar el formulario',
                'categorias' => [],
                'clubes' => []
            ]);
        }
    }
    
    private function authorize() {
        // Solo superadmin y admin pueden autorizar
        if (!in_array($_SESSION['user']['tipo_usuario'], ['superadmin', 'admin'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'No tiene permisos para autorizar transacciones']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'] ?? $_GET['id'] ?? '';
                $accion = $_POST['accion'] ?? 'autorizar'; // autorizar o rechazar
                
                if (empty($id)) {
                    throw new Exception('ID de transacción requerido');
                }
                
                if ($accion === 'autorizar') {
                    $stmt = $this->db->prepare("
                        UPDATE transacciones 
                        SET estado = 'autorizada', 
                            autorizada_por = ?, 
                            fecha_autorizacion = NOW() 
                        WHERE id = ? AND estado = 'pendiente'
                    ");
                    $stmt->execute([$_SESSION['user']['id'], $id]);
                    $message = 'Transacción autorizada exitosamente';
                } else {
                    $stmt = $this->db->prepare("
                        UPDATE transacciones 
                        SET estado = 'cancelada', 
                            autorizada_por = ?, 
                            fecha_autorizacion = NOW() 
                        WHERE id = ? AND estado = 'pendiente'
                    ");
                    $stmt->execute([$_SESSION['user']['id'], $id]);
                    $message = 'Transacción cancelada exitosamente';
                }
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => $message]);
                
            } catch (Exception $e) {
                error_log('Error autorizando transacción: ' . $e->getMessage());
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
    }
    
    private function delete() {
        // Solo superadmin puede eliminar
        if ($_SESSION['user']['tipo_usuario'] !== 'superadmin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'No tiene permisos para eliminar transacciones']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'] ?? $_GET['id'] ?? '';
                
                if (empty($id)) {
                    throw new Exception('ID de transacción requerido');
                }
                
                $stmt = $this->db->prepare("DELETE FROM transacciones WHERE id = ?");
                $stmt->execute([$id]);
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Transacción eliminada exitosamente']);
                
            } catch (Exception $e) {
                error_log('Error eliminando transacción: ' . $e->getMessage());
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
    }
}
?>
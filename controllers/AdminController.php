<?php
/**
 * Controlador de administración para SuperAdmin
 * Maneja CRUD de todas las entidades del sistema
 */
require_once 'BaseController.php';

class AdminController extends BaseController {
    
    public function usuarios() {
        $this->requireAuth('superadmin');
        
        $action = $_GET['action'] ?? 'list';
        
        switch ($action) {
            case 'create':
                $this->createUsuario();
                break;
            case 'edit':
                $this->editUsuario();
                break;
            case 'delete':
                $this->deleteUsuario();
                break;
            default:
                $this->listUsuarios();
                break;
        }
    }
    
    public function clubes() {
        $this->requireAuth('superadmin');
        
        $action = $_GET['action'] ?? 'list';
        
        switch ($action) {
            case 'create':
                $this->createClub();
                break;
            case 'edit':
                $this->editClub();
                break;
            case 'delete':
                $this->deleteClub();
                break;
            default:
                $this->listClubes();
                break;
        }
    }
    
    public function fraccionamientos() {
        $this->requireAuth('superadmin');
        
        $action = $_GET['action'] ?? 'list';
        
        switch ($action) {
            case 'create':
                $this->createFraccionamiento();
                break;
            case 'edit':
                $this->editFraccionamiento();
                break;
            case 'delete':
                $this->deleteFraccionamiento();
                break;
            default:
                $this->listFraccionamientos();
                break;
        }
    }
    
    public function empresas() {
        $this->requireAuth('superadmin');
        
        $action = $_GET['action'] ?? 'list';
        
        switch ($action) {
            case 'create':
                $this->createEmpresa();
                break;
            case 'edit':
                $this->editEmpresa();
                break;
            case 'delete':
                $this->deleteEmpresa();
                break;
            default:
                $this->listEmpresas();
                break;
        }
    }
    
    public function reservas() {
        $this->requireAuth('admin');
        
        $action = $_GET['action'] ?? 'list';
        
        switch ($action) {
            case 'create':
                $this->createReserva();
                break;
            case 'edit':
                $this->editReserva();
                break;
            case 'delete':
                $this->deleteReserva();
                break;
            default:
                $this->listReservas();
                break;
        }
    }
    
    // === USUARIOS ===
    private function listUsuarios() {
        try {
            $stmt = $this->db->query("SELECT * FROM usuarios ORDER BY fecha_registro DESC");
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->render('admin/usuarios/list', [
                'title' => 'Gestión de Usuarios - ' . APP_NAME,
                'usuarios' => $usuarios
            ]);
        } catch (PDOException $e) {
            error_log('Error listando usuarios: ' . $e->getMessage());
            $this->render('admin/usuarios/list', [
                'title' => 'Gestión de Usuarios - ' . APP_NAME,
                'usuarios' => [],
                'error' => 'Error al cargar usuarios'
            ]);
        }
    }
    
    private function createUsuario() {
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $tipo_usuario = $_POST['tipo_usuario'] ?? 'cliente';
            $telefono = trim($_POST['telefono'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            
            if (empty($nombre) || empty($email) || empty($password)) {
                $error = 'Los campos nombre, email y contraseña son obligatorios.';
            } else {
                try {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    
                    $stmt = $this->db->prepare("INSERT INTO usuarios (nombre, email, password, tipo_usuario, telefono, direccion) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$nombre, $email, $hashedPassword, $tipo_usuario, $telefono, $direccion]);
                    
                    $success = 'Usuario creado exitosamente.';
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        $error = 'El email ya está registrado.';
                    } else {
                        $error = 'Error al crear usuario: ' . $e->getMessage();
                    }
                }
            }
        }
        
        $this->render('admin/usuarios/form', [
            'title' => 'Crear Usuario - ' . APP_NAME,
            'action' => 'create',
            'error' => $error,
            'success' => $success
        ]);
    }
    
    private function editUsuario() {
        $id = $_GET['id'] ?? 0;
        $error = '';
        $success = '';
        $usuario = null;
        
        if ($id) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = ?");
                $stmt->execute([$id]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$usuario) {
                    $this->redirect('admin/usuarios');
                    return;
                }
            } catch (PDOException $e) {
                $error = 'Error al cargar usuario.';
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $usuario) {
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $tipo_usuario = $_POST['tipo_usuario'] ?? 'cliente';
            $telefono = trim($_POST['telefono'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $activo = isset($_POST['activo']) ? 1 : 0;
            
            if (empty($nombre) || empty($email)) {
                $error = 'Los campos nombre y email son obligatorios.';
            } else {
                try {
                    if (!empty($password)) {
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $this->db->prepare("UPDATE usuarios SET nombre = ?, email = ?, password = ?, tipo_usuario = ?, telefono = ?, direccion = ?, activo = ? WHERE id = ?");
                        $stmt->execute([$nombre, $email, $hashedPassword, $tipo_usuario, $telefono, $direccion, $activo, $id]);
                    } else {
                        $stmt = $this->db->prepare("UPDATE usuarios SET nombre = ?, email = ?, tipo_usuario = ?, telefono = ?, direccion = ?, activo = ? WHERE id = ?");
                        $stmt->execute([$nombre, $email, $tipo_usuario, $telefono, $direccion, $activo, $id]);
                    }
                    
                    $success = 'Usuario actualizado exitosamente.';
                    
                    // Recargar datos del usuario
                    $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = ?");
                    $stmt->execute([$id]);
                    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        $error = 'El email ya está registrado por otro usuario.';
                    } else {
                        $error = 'Error al actualizar usuario: ' . $e->getMessage();
                    }
                }
            }
        }
        
        $this->render('admin/usuarios/form', [
            'title' => 'Editar Usuario - ' . APP_NAME,
            'action' => 'edit',
            'usuario' => $usuario,
            'error' => $error,
            'success' => $success
        ]);
    }
    
    private function deleteUsuario() {
        $id = $_GET['id'] ?? 0;
        
        if ($id && $_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // No permitir eliminar el usuario actual
                if ($id == $_SESSION['user_id']) {
                    $this->jsonResponse(['success' => false, 'message' => 'No puede eliminar su propio usuario']);
                    return;
                }
                
                $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = ?");
                $stmt->execute([$id]);
                
                $this->jsonResponse(['success' => true, 'message' => 'Usuario eliminado exitosamente']);
            } catch (PDOException $e) {
                $this->jsonResponse(['success' => false, 'message' => 'Error al eliminar usuario: ' . $e->getMessage()]);
            }
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'ID inválido']);
        }
    }
    
    // === CLUBES ===
    private function listClubes() {
        try {
            $stmt = $this->db->query("SELECT * FROM clubes ORDER BY fecha_registro DESC");
            $clubes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->render('admin/clubes/list', [
                'title' => 'Gestión de Clubes - ' . APP_NAME,
                'clubes' => $clubes
            ]);
        } catch (PDOException $e) {
            error_log('Error listando clubes: ' . $e->getMessage());
            $this->render('admin/clubes/list', [
                'title' => 'Gestión de Clubes - ' . APP_NAME,
                'clubes' => [],
                'error' => 'Error al cargar clubes'
            ]);
        }
    }
    
    private function createClub() {
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $email = trim($_POST['email'] ?? '');
            
            // Datos del representante
            $representante_nombre = trim($_POST['representante_nombre'] ?? '');
            $representante_email = trim($_POST['representante_email'] ?? '');
            $representante_telefono = trim($_POST['representante_telefono'] ?? '');
            $representante_password = $_POST['representante_password'] ?? '';
            
            if (empty($nombre)) {
                $error = 'El nombre del club es obligatorio.';
            } else {
                try {
                    // Hash password if provided
                    $hashedPassword = null;
                    if (!empty($representante_password)) {
                        $hashedPassword = password_hash($representante_password, PASSWORD_DEFAULT);
                    }
                    
                    $stmt = $this->db->prepare("INSERT INTO clubes (nombre, descripcion, direccion, telefono, email, representante_nombre, representante_email, representante_telefono, representante_password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$nombre, $descripcion, $direccion, $telefono, $email, $representante_nombre, $representante_email, $representante_telefono, $hashedPassword]);
                    
                    $success = 'Club creado exitosamente.';
                } catch (PDOException $e) {
                    $error = 'Error al crear club: ' . $e->getMessage();
                }
            }
        }
        
        $this->render('admin/clubes/form', [
            'title' => 'Crear Club - ' . APP_NAME,
            'action' => 'create',
            'error' => $error,
            'success' => $success
        ]);
    }
    
    private function editClub() {
        $id = $_GET['id'] ?? 0;
        $error = '';
        $success = '';
        $club = null;
        
        if ($id) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM clubes WHERE id = ?");
                $stmt->execute([$id]);
                $club = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$club) {
                    $this->redirect('admin/clubes');
                    return;
                }
            } catch (PDOException $e) {
                $error = 'Error al cargar club.';
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $club) {
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $activo = isset($_POST['activo']) ? 1 : 0;
            
            // Datos del representante
            $representante_nombre = trim($_POST['representante_nombre'] ?? '');
            $representante_email = trim($_POST['representante_email'] ?? '');
            $representante_telefono = trim($_POST['representante_telefono'] ?? '');
            $representante_password = $_POST['representante_password'] ?? '';
            
            if (empty($nombre)) {
                $error = 'El nombre del club es obligatorio.';
            } else {
                try {
                    // Only update password if a new one is provided
                    if (!empty($representante_password)) {
                        $hashedPassword = password_hash($representante_password, PASSWORD_DEFAULT);
                        $stmt = $this->db->prepare("UPDATE clubes SET nombre = ?, descripcion = ?, direccion = ?, telefono = ?, email = ?, activo = ?, representante_nombre = ?, representante_email = ?, representante_telefono = ?, representante_password = ? WHERE id = ?");
                        $stmt->execute([$nombre, $descripcion, $direccion, $telefono, $email, $activo, $representante_nombre, $representante_email, $representante_telefono, $hashedPassword, $id]);
                    } else {
                        $stmt = $this->db->prepare("UPDATE clubes SET nombre = ?, descripcion = ?, direccion = ?, telefono = ?, email = ?, activo = ?, representante_nombre = ?, representante_email = ?, representante_telefono = ? WHERE id = ?");
                        $stmt->execute([$nombre, $descripcion, $direccion, $telefono, $email, $activo, $representante_nombre, $representante_email, $representante_telefono, $id]);
                    }
                    
                    $success = 'Club actualizado exitosamente.';
                    
                    // Recargar datos del club
                    $stmt = $this->db->prepare("SELECT * FROM clubes WHERE id = ?");
                    $stmt->execute([$id]);
                    $club = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                } catch (PDOException $e) {
                    $error = 'Error al actualizar club: ' . $e->getMessage();
                }
            }
        }
        
        $this->render('admin/clubes/form', [
            'title' => 'Editar Club - ' . APP_NAME,
            'action' => 'edit',
            'club' => $club,
            'error' => $error,
            'success' => $success
        ]);
    }
    
    private function deleteClub() {
        $id = $_GET['id'] ?? 0;
        
        if ($id && $_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $stmt = $this->db->prepare("DELETE FROM clubes WHERE id = ?");
                $stmt->execute([$id]);
                
                $this->jsonResponse(['success' => true, 'message' => 'Club eliminado exitosamente']);
            } catch (PDOException $e) {
                $this->jsonResponse(['success' => false, 'message' => 'Error al eliminar club: ' . $e->getMessage()]);
            }
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'ID inválido']);
        }
    }
    
    // === FRACCIONAMIENTOS ===
    private function listFraccionamientos() {
        try {
            $stmt = $this->db->query("
                SELECT f.*, c.nombre as club_nombre 
                FROM fraccionamientos f
                LEFT JOIN clubes c ON f.club_id = c.id
                ORDER BY f.fecha_registro DESC
            ");
            $fraccionamientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->render('admin/fraccionamientos/list', [
                'title' => 'Gestión de Fraccionamientos - ' . APP_NAME,
                'fraccionamientos' => $fraccionamientos
            ]);
        } catch (PDOException $e) {
            error_log('Error listando fraccionamientos: ' . $e->getMessage());
            $this->render('admin/fraccionamientos/list', [
                'title' => 'Gestión de Fraccionamientos - ' . APP_NAME,
                'fraccionamientos' => [],
                'error' => 'Error al cargar fraccionamientos'
            ]);
        }
    }
    
    private function createFraccionamiento() {
        $error = '';
        $success = '';
        $clubes = [];
        
        // Obtener lista de clubes para el select
        try {
            $stmt = $this->db->query("SELECT id, nombre FROM clubes WHERE activo = 1 ORDER BY nombre");
            $clubes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error obteniendo clubes: ' . $e->getMessage());
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $club_id = $_POST['club_id'] ?? null;
            
            // Datos del representante
            $representante_nombre = trim($_POST['representante_nombre'] ?? '');
            $representante_email = trim($_POST['representante_email'] ?? '');
            $representante_telefono = trim($_POST['representante_telefono'] ?? '');
            $representante_password = $_POST['representante_password'] ?? '';
            
            if (empty($nombre)) {
                $error = 'El nombre del fraccionamiento es obligatorio.';
            } else {
                try {
                    // Hash password if provided
                    $hashedPassword = null;
                    if (!empty($representante_password)) {
                        $hashedPassword = password_hash($representante_password, PASSWORD_DEFAULT);
                    }
                    
                    $stmt = $this->db->prepare("INSERT INTO fraccionamientos (nombre, descripcion, direccion, club_id, representante_nombre, representante_email, representante_telefono, representante_password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$nombre, $descripcion, $direccion, $club_id ?: null, $representante_nombre, $representante_email, $representante_telefono, $hashedPassword]);
                    
                    $success = 'Fraccionamiento creado exitosamente.';
                } catch (PDOException $e) {
                    $error = 'Error al crear fraccionamiento: ' . $e->getMessage();
                }
            }
        }
        
        $this->render('admin/fraccionamientos/form', [
            'title' => 'Crear Fraccionamiento - ' . APP_NAME,
            'action' => 'create',
            'clubes' => $clubes,
            'error' => $error,
            'success' => $success
        ]);
    }
    
    private function editFraccionamiento() {
        $id = $_GET['id'] ?? 0;
        $error = '';
        $success = '';
        $fraccionamiento = null;
        $clubes = [];
        
        // Obtener lista de clubes para el select
        try {
            $stmt = $this->db->query("SELECT id, nombre FROM clubes WHERE activo = 1 ORDER BY nombre");
            $clubes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error obteniendo clubes: ' . $e->getMessage());
        }
        
        if ($id) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM fraccionamientos WHERE id = ?");
                $stmt->execute([$id]);
                $fraccionamiento = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$fraccionamiento) {
                    $this->redirect('admin/fraccionamientos');
                    return;
                }
            } catch (PDOException $e) {
                $error = 'Error al cargar fraccionamiento.';
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $fraccionamiento) {
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $club_id = $_POST['club_id'] ?? null;
            $activo = isset($_POST['activo']) ? 1 : 0;
            
            // Datos del representante
            $representante_nombre = trim($_POST['representante_nombre'] ?? '');
            $representante_email = trim($_POST['representante_email'] ?? '');
            $representante_telefono = trim($_POST['representante_telefono'] ?? '');
            $representante_password = $_POST['representante_password'] ?? '';
            
            if (empty($nombre)) {
                $error = 'El nombre del fraccionamiento es obligatorio.';
            } else {
                try {
                    // Only update password if a new one is provided
                    if (!empty($representante_password)) {
                        $hashedPassword = password_hash($representante_password, PASSWORD_DEFAULT);
                        $stmt = $this->db->prepare("UPDATE fraccionamientos SET nombre = ?, descripcion = ?, direccion = ?, club_id = ?, activo = ?, representante_nombre = ?, representante_email = ?, representante_telefono = ?, representante_password = ? WHERE id = ?");
                        $stmt->execute([$nombre, $descripcion, $direccion, $club_id ?: null, $activo, $representante_nombre, $representante_email, $representante_telefono, $hashedPassword, $id]);
                    } else {
                        $stmt = $this->db->prepare("UPDATE fraccionamientos SET nombre = ?, descripcion = ?, direccion = ?, club_id = ?, activo = ?, representante_nombre = ?, representante_email = ?, representante_telefono = ? WHERE id = ?");
                        $stmt->execute([$nombre, $descripcion, $direccion, $club_id ?: null, $activo, $representante_nombre, $representante_email, $representante_telefono, $id]);
                    }
                    
                    $success = 'Fraccionamiento actualizado exitosamente.';
                    
                    // Recargar datos del fraccionamiento
                    $stmt = $this->db->prepare("SELECT * FROM fraccionamientos WHERE id = ?");
                    $stmt->execute([$id]);
                    $fraccionamiento = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                } catch (PDOException $e) {
                    $error = 'Error al actualizar fraccionamiento: ' . $e->getMessage();
                }
            }
        }
        
        $this->render('admin/fraccionamientos/form', [
            'title' => 'Editar Fraccionamiento - ' . APP_NAME,
            'action' => 'edit',
            'fraccionamiento' => $fraccionamiento,
            'clubes' => $clubes,
            'error' => $error,
            'success' => $success
        ]);
    }
    
    private function deleteFraccionamiento() {
        $id = $_GET['id'] ?? 0;
        
        if ($id && $_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $stmt = $this->db->prepare("DELETE FROM fraccionamientos WHERE id = ?");
                $stmt->execute([$id]);
                
                $this->jsonResponse(['success' => true, 'message' => 'Fraccionamiento eliminado exitosamente']);
            } catch (PDOException $e) {
                $this->jsonResponse(['success' => false, 'message' => 'Error al eliminar fraccionamiento: ' . $e->getMessage()]);
            }
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'ID inválido']);
        }
    }
    
    // === EMPRESAS ===
    private function listEmpresas() {
        try {
            $stmt = $this->db->query("SELECT * FROM empresas ORDER BY fecha_registro DESC");
            $empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->render('admin/empresas/list', [
                'title' => 'Gestión de Empresas - ' . APP_NAME,
                'empresas' => $empresas
            ]);
        } catch (PDOException $e) {
            error_log('Error listando empresas: ' . $e->getMessage());
            $this->render('admin/empresas/list', [
                'title' => 'Gestión de Empresas - ' . APP_NAME,
                'empresas' => [],
                'error' => 'Error al cargar empresas'
            ]);
        }
    }
    
    private function createEmpresa() {
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $rfc = trim($_POST['rfc'] ?? '');
            $razon_social = trim($_POST['razon_social'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $email = trim($_POST['email'] ?? '');
            
            // Datos del representante
            $representante_nombre = trim($_POST['representante_nombre'] ?? '');
            $representante_email = trim($_POST['representante_email'] ?? '');
            $representante_telefono = trim($_POST['representante_telefono'] ?? '');
            $representante_password = $_POST['representante_password'] ?? '';
            
            if (empty($nombre)) {
                $error = 'El nombre de la empresa es obligatorio.';
            } else {
                try {
                    // Hash password if provided
                    $hashedPassword = null;
                    if (!empty($representante_password)) {
                        $hashedPassword = password_hash($representante_password, PASSWORD_DEFAULT);
                    }
                    
                    $stmt = $this->db->prepare("INSERT INTO empresas (nombre, rfc, razon_social, direccion, telefono, email, representante_nombre, representante_email, representante_telefono, representante_password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$nombre, $rfc, $razon_social, $direccion, $telefono, $email, $representante_nombre, $representante_email, $representante_telefono, $hashedPassword]);
                    
                    $success = 'Empresa creada exitosamente.';
                } catch (PDOException $e) {
                    $error = 'Error al crear empresa: ' . $e->getMessage();
                }
            }
        }
        
        $this->render('admin/empresas/form', [
            'title' => 'Crear Empresa - ' . APP_NAME,
            'action' => 'create',
            'error' => $error,
            'success' => $success
        ]);
    }
    
    private function editEmpresa() {
        $id = $_GET['id'] ?? 0;
        $error = '';
        $success = '';
        $empresa = null;
        
        if ($id) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM empresas WHERE id = ?");
                $stmt->execute([$id]);
                $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$empresa) {
                    $this->redirect('admin/empresas');
                    return;
                }
            } catch (PDOException $e) {
                $error = 'Error al cargar empresa.';
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $empresa) {
            $nombre = trim($_POST['nombre'] ?? '');
            $rfc = trim($_POST['rfc'] ?? '');
            $razon_social = trim($_POST['razon_social'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $activo = isset($_POST['activo']) ? 1 : 0;
            
            // Datos del representante
            $representante_nombre = trim($_POST['representante_nombre'] ?? '');
            $representante_email = trim($_POST['representante_email'] ?? '');
            $representante_telefono = trim($_POST['representante_telefono'] ?? '');
            $representante_password = $_POST['representante_password'] ?? '';
            
            if (empty($nombre)) {
                $error = 'El nombre de la empresa es obligatorio.';
            } else {
                try {
                    // Only update password if a new one is provided
                    if (!empty($representante_password)) {
                        $hashedPassword = password_hash($representante_password, PASSWORD_DEFAULT);
                        $stmt = $this->db->prepare("UPDATE empresas SET nombre = ?, rfc = ?, razon_social = ?, direccion = ?, telefono = ?, email = ?, activo = ?, representante_nombre = ?, representante_email = ?, representante_telefono = ?, representante_password = ? WHERE id = ?");
                        $stmt->execute([$nombre, $rfc, $razon_social, $direccion, $telefono, $email, $activo, $representante_nombre, $representante_email, $representante_telefono, $hashedPassword, $id]);
                    } else {
                        $stmt = $this->db->prepare("UPDATE empresas SET nombre = ?, rfc = ?, razon_social = ?, direccion = ?, telefono = ?, email = ?, activo = ?, representante_nombre = ?, representante_email = ?, representante_telefono = ? WHERE id = ?");
                        $stmt->execute([$nombre, $rfc, $razon_social, $direccion, $telefono, $email, $activo, $representante_nombre, $representante_email, $representante_telefono, $id]);
                    }
                    
                    $success = 'Empresa actualizada exitosamente.';
                    
                    // Recargar datos de la empresa
                    $stmt = $this->db->prepare("SELECT * FROM empresas WHERE id = ?");
                    $stmt->execute([$id]);
                    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                } catch (PDOException $e) {
                    $error = 'Error al actualizar empresa: ' . $e->getMessage();
                }
            }
        }
        
        $this->render('admin/empresas/form', [
            'title' => 'Editar Empresa - ' . APP_NAME,
            'action' => 'edit',
            'empresa' => $empresa,
            'error' => $error,
            'success' => $success
        ]);
    }
    
    private function deleteEmpresa() {
        $id = $_GET['id'] ?? 0;
        
        if ($id && $_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $stmt = $this->db->prepare("DELETE FROM empresas WHERE id = ?");
                $stmt->execute([$id]);
                
                $this->jsonResponse(['success' => true, 'message' => 'Empresa eliminada exitosamente']);
            } catch (PDOException $e) {
                $this->jsonResponse(['success' => false, 'message' => 'Error al eliminar empresa: ' . $e->getMessage()]);
            }
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'ID inválido']);
        }
    }
    
    // === RESERVAS ===
    private function listReservas() {
        try {
            $stmt = $this->db->query("
                SELECT r.*, 
                       u.nombre as usuario_nombre,
                       c.nombre as club_nombre,
                       f.nombre as fraccionamiento_nombre,
                       e.nombre as empresa_nombre
                FROM reservas r
                LEFT JOIN usuarios u ON r.usuario_id = u.id
                LEFT JOIN clubes c ON r.club_id = c.id
                LEFT JOIN fraccionamientos f ON r.fraccionamiento_id = f.id
                LEFT JOIN empresas e ON r.empresa_id = e.id
                ORDER BY r.fecha_reserva DESC, r.hora_inicio DESC
            ");
            $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->render('admin/reservas/list', [
                'title' => 'Gestión de Reservas - ' . APP_NAME,
                'reservas' => $reservas
            ]);
        } catch (PDOException $e) {
            error_log('Error listando reservas: ' . $e->getMessage());
            $this->render('admin/reservas/list', [
                'title' => 'Gestión de Reservas - ' . APP_NAME,
                'reservas' => [],
                'error' => 'Error al cargar reservas'
            ]);
        }
    }
    
    private function createReserva() {
        $error = '';
        $success = '';
        $data = [];
        
        // Obtener datos para los selects
        try {
            $stmt = $this->db->query("SELECT id, nombre FROM usuarios WHERE activo = 1 ORDER BY nombre");
            $data['usuarios'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stmt = $this->db->query("SELECT id, nombre FROM clubes WHERE activo = 1 ORDER BY nombre");
            $data['clubes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stmt = $this->db->query("SELECT id, nombre FROM fraccionamientos WHERE activo = 1 ORDER BY nombre");
            $data['fraccionamientos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stmt = $this->db->query("SELECT id, nombre FROM empresas WHERE activo = 1 ORDER BY nombre");
            $data['empresas'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log('Error obteniendo datos para reserva: ' . $e->getMessage());
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario_id = $_POST['usuario_id'] ?? '';
            $club_id = $_POST['club_id'] ?? null;
            $fraccionamiento_id = $_POST['fraccionamiento_id'] ?? null;
            $empresa_id = $_POST['empresa_id'] ?? null;
            $fecha_reserva = $_POST['fecha_reserva'] ?? '';
            $hora_inicio = $_POST['hora_inicio'] ?? '';
            $hora_fin = $_POST['hora_fin'] ?? '';
            $precio = $_POST['precio'] ?? 0;
            $estado = $_POST['estado'] ?? 'pendiente';
            $observaciones = trim($_POST['observaciones'] ?? '');
            
            if (empty($usuario_id) || empty($fecha_reserva) || empty($hora_inicio) || empty($hora_fin)) {
                $error = 'Los campos usuario, fecha, hora inicio y hora fin son obligatorios.';
            } else {
                try {
                    $stmt = $this->db->prepare("INSERT INTO reservas (usuario_id, club_id, fraccionamiento_id, empresa_id, fecha_reserva, hora_inicio, hora_fin, precio, estado, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $usuario_id, 
                        $club_id ?: null, 
                        $fraccionamiento_id ?: null, 
                        $empresa_id ?: null, 
                        $fecha_reserva, 
                        $hora_inicio, 
                        $hora_fin, 
                        $precio, 
                        $estado, 
                        $observaciones
                    ]);
                    
                    $success = 'Reserva creada exitosamente.';
                } catch (PDOException $e) {
                    $error = 'Error al crear reserva: ' . $e->getMessage();
                }
            }
        }
        
        $this->render('admin/reservas/form', array_merge($data, [
            'title' => 'Crear Reserva - ' . APP_NAME,
            'action' => 'create',
            'error' => $error,
            'success' => $success
        ]));
    }
    
    private function editReserva() {
        $id = $_GET['id'] ?? 0;
        $error = '';
        $success = '';
        $reserva = null;
        $data = [];
        
        // Obtener datos para los selects
        try {
            $stmt = $this->db->query("SELECT id, nombre FROM usuarios WHERE activo = 1 ORDER BY nombre");
            $data['usuarios'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stmt = $this->db->query("SELECT id, nombre FROM clubes WHERE activo = 1 ORDER BY nombre");
            $data['clubes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stmt = $this->db->query("SELECT id, nombre FROM fraccionamientos WHERE activo = 1 ORDER BY nombre");
            $data['fraccionamientos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stmt = $this->db->query("SELECT id, nombre FROM empresas WHERE activo = 1 ORDER BY nombre");
            $data['empresas'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log('Error obteniendo datos para reserva: ' . $e->getMessage());
        }
        
        if ($id) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM reservas WHERE id = ?");
                $stmt->execute([$id]);
                $reserva = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$reserva) {
                    $this->redirect('admin/reservas');
                    return;
                }
            } catch (PDOException $e) {
                $error = 'Error al cargar reserva.';
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $reserva) {
            $usuario_id = $_POST['usuario_id'] ?? '';
            $club_id = $_POST['club_id'] ?? null;
            $fraccionamiento_id = $_POST['fraccionamiento_id'] ?? null;
            $empresa_id = $_POST['empresa_id'] ?? null;
            $fecha_reserva = $_POST['fecha_reserva'] ?? '';
            $hora_inicio = $_POST['hora_inicio'] ?? '';
            $hora_fin = $_POST['hora_fin'] ?? '';
            $precio = $_POST['precio'] ?? 0;
            $estado = $_POST['estado'] ?? 'pendiente';
            $observaciones = trim($_POST['observaciones'] ?? '');
            
            if (empty($usuario_id) || empty($fecha_reserva) || empty($hora_inicio) || empty($hora_fin)) {
                $error = 'Los campos usuario, fecha, hora inicio y hora fin son obligatorios.';
            } else {
                try {
                    $stmt = $this->db->prepare("UPDATE reservas SET usuario_id = ?, club_id = ?, fraccionamiento_id = ?, empresa_id = ?, fecha_reserva = ?, hora_inicio = ?, hora_fin = ?, precio = ?, estado = ?, observaciones = ? WHERE id = ?");
                    $stmt->execute([
                        $usuario_id, 
                        $club_id ?: null, 
                        $fraccionamiento_id ?: null, 
                        $empresa_id ?: null, 
                        $fecha_reserva, 
                        $hora_inicio, 
                        $hora_fin, 
                        $precio, 
                        $estado, 
                        $observaciones,
                        $id
                    ]);
                    
                    $success = 'Reserva actualizada exitosamente.';
                    
                    // Recargar datos de la reserva
                    $stmt = $this->db->prepare("SELECT * FROM reservas WHERE id = ?");
                    $stmt->execute([$id]);
                    $reserva = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                } catch (PDOException $e) {
                    $error = 'Error al actualizar reserva: ' . $e->getMessage();
                }
            }
        }
        
        $this->render('admin/reservas/form', array_merge($data, [
            'title' => 'Editar Reserva - ' . APP_NAME,
            'action' => 'edit',
            'reserva' => $reserva,
            'error' => $error,
            'success' => $success
        ]));
    }
    
    private function deleteReserva() {
        $id = $_GET['id'] ?? 0;
        
        if ($id && $_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $stmt = $this->db->prepare("DELETE FROM reservas WHERE id = ?");
                $stmt->execute([$id]);
                
                $this->jsonResponse(['success' => true, 'message' => 'Reserva eliminada exitosamente']);
            } catch (PDOException $e) {
                $this->jsonResponse(['success' => false, 'message' => 'Error al eliminar reserva: ' . $e->getMessage()]);
            }
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'ID inválido']);
        }
    }
}
?>
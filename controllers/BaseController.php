<?php
/**
 * Controlador base con funcionalidades comunes
 */
class BaseController {
    protected $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    protected function requireAuth($minRole = 'cliente') {
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
            exit;
        }
        
        $roles = ['cliente' => 1, 'admin' => 2, 'superadmin' => 3];
        $userRole = $_SESSION['user_role'] ?? 'cliente';
        
        if ($roles[$userRole] < $roles[$minRole]) {
            $this->show403();
            exit;
        }
    }
    
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    protected function redirect($url) {
        $fullUrl = BASE_URL . '/' . ltrim($url, '/');
        header('Location: ' . $fullUrl);
        exit;
    }
    
    protected function render($view, $data = []) {
        // Extraer variables para la vista
        extract($data);
        
        // Iniciar buffer de salida
        ob_start();
        
        // Incluir la vista
        include "views/{$view}.php";
        
        // Obtener contenido del buffer
        $content = ob_get_clean();
        
        // Incluir layout principal
        include 'views/layouts/main.php';
    }
    
    protected function renderPartial($view, $data = []) {
        extract($data);
        include "views/{$view}.php";
    }
    
    protected function show403() {
        http_response_code(403);
        $this->render('403');
    }
    
    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>
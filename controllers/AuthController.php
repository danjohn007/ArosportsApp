<?php
/**
 * Controlador de autenticación
 */
require_once 'BaseController.php';

class AuthController extends BaseController {
    
    public function login() {
        // Si ya está logueado, redirigir al dashboard
        if ($this->isLoggedIn()) {
            $this->redirect('dashboard');
            return;
        }
        
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $error = 'Por favor, complete todos los campos.';
            } else {
                $user = $this->authenticateUser($email, $password);
                
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['nombre'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['tipo_usuario'];
                    
                    $this->redirect('dashboard');
                    return;
                } else {
                    $error = 'Email o contraseña incorrectos.';
                }
            }
        }
        
        $this->render('auth/login', ['error' => $error]);
    }
    
    public function logout() {
        session_destroy();
        $this->redirect('login');
    }
    
    private function authenticateUser($email, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ? AND activo = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                return $user;
            }
        } catch (PDOException $e) {
            error_log('Error en autenticación: ' . $e->getMessage());
        }
        
        return false;
    }
}
?>
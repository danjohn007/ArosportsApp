<?php
/**
 * Controlador principal del home
 */
require_once 'BaseController.php';

class HomeController extends BaseController {
    
    public function index() {
        // Si no está logueado, mostrar login
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
            return;
        }
        
        // Si está logueado, redirigir al dashboard
        $this->redirect('dashboard');
    }
}
?>
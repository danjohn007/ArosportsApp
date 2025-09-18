<?php
/**
 * Punto de entrada principal del sistema Arosports
 */

// Cargar configuración
require_once 'config/config.php';
require_once 'config/database.php';

// Router simple para URLs amigables
class Router {
    private $routes = [];
    
    public function __construct() {
        // Definir rutas principales
        $this->routes = [
            '' => ['controller' => 'HomeController', 'action' => 'index'],
            'home' => ['controller' => 'HomeController', 'action' => 'index'],
            'login' => ['controller' => 'AuthController', 'action' => 'login'],
            'logout' => ['controller' => 'AuthController', 'action' => 'logout'],
            'dashboard' => ['controller' => 'DashboardController', 'action' => 'index'],
            'admin/usuarios' => ['controller' => 'AdminController', 'action' => 'usuarios'],
            'admin/clubes' => ['controller' => 'AdminController', 'action' => 'clubes'],
            'admin/fraccionamientos' => ['controller' => 'AdminController', 'action' => 'fraccionamientos'],
            'admin/empresas' => ['controller' => 'AdminController', 'action' => 'empresas'],
            'admin/reservas' => ['controller' => 'AdminController', 'action' => 'reservas'],
            'admin/transacciones' => ['controller' => 'TransaccionesController', 'action' => 'index'],
            'reportes' => ['controller' => 'ReportesController', 'action' => 'index'],
            'test-db' => ['controller' => 'TestController', 'action' => 'database']
        ];
    }
    
    public function dispatch() {
        $url = $this->getCurrentUrl();
        
        if (isset($this->routes[$url])) {
            $route = $this->routes[$url];
            $controllerName = $route['controller'];
            $actionName = $route['action'];
            
            $controllerFile = 'controllers/' . $controllerName . '.php';
            
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                
                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    
                    if (method_exists($controller, $actionName)) {
                        $controller->$actionName();
                        return;
                    }
                }
            }
        }
        
        // Página no encontrada
        $this->show404();
    }
    
    private function getCurrentUrl() {
        $url = $_SERVER['REQUEST_URI'];
        $url = parse_url($url, PHP_URL_PATH);
        $url = trim($url, '/');
        
        // Remover la parte base si existe
        $basePath = parse_url(BASE_URL, PHP_URL_PATH);
        if ($basePath && strpos($url, trim($basePath, '/')) === 0) {
            $url = substr($url, strlen(trim($basePath, '/')));
            $url = trim($url, '/');
        }
        
        return $url;
    }
    
    private function show404() {
        http_response_code(404);
        include 'views/404.php';
    }
}

// Inicializar router
$router = new Router();
$router->dispatch();
?>
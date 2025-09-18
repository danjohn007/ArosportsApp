<?php
/**
 * Controlador para pruebas del sistema
 */
require_once 'BaseController.php';

class TestController extends BaseController {
    
    public function database() {
        $results = [];
        
        // Probar conexión a la base de datos
        $database = new Database();
        
        $results['connection'] = $database->testConnection();
        $results['base_url'] = BASE_URL;
        $results['db_config'] = [
            'host' => DB_HOST,
            'database' => DB_NAME,
            'user' => DB_USER
        ];
        
        // Probar si existen las tablas
        if ($results['connection']) {
            try {
                $conn = $database->getConnection();
                
                $tables = ['usuarios', 'clubes', 'fraccionamientos', 'empresas', 'reservas'];
                $results['tables'] = [];
                
                foreach ($tables as $table) {
                    $stmt = $conn->query("SHOW TABLES LIKE '$table'");
                    $exists = $stmt->rowCount() > 0;
                    $results['tables'][$table] = $exists;
                    
                    if ($exists) {
                        $stmt = $conn->query("SELECT COUNT(*) as count FROM $table");
                        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                        $results['tables'][$table . '_count'] = $count;
                    }
                }
            } catch (PDOException $e) {
                $results['tables_error'] = $e->getMessage();
            }
        }
        
        $this->render('test/database', [
            'title' => 'Test de Conexión - ' . APP_NAME,
            'results' => $results
        ]);
    }
}
?>
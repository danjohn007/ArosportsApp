<?php
/**
 * Configuración principal del sistema Arosports
 */

// Detectar URL base automáticamente
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $path = dirname($scriptName);
    
    // Limpiar la ruta
    if ($path === '/' || $path === '\\') {
        $path = '';
    }
    
    return $protocol . '://' . $host . $path;
}

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'fix360_arosports');
define('DB_USER', 'fix360_arosports');
define('DB_PASS', 'Danjohn007!');
define('DB_CHARSET', 'utf8');

// Configuración del sistema
define('BASE_URL', getBaseUrl());
define('APP_NAME', 'Arosports Admin');
define('APP_VERSION', '1.0.0');

// Configuración de sesiones
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

// Zona horaria
date_default_timezone_set('America/Mexico_City');

// Configuración de errores (desarrollo)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

# Sistema Arosports - Dashboard Financiero

Sistema completo de administración deportiva con dashboard financiero desarrollado en PHP puro, MySQL y Bootstrap 5.

## 🚀 Características Principales

- **Dashboard Financiero Avanzado**: Visualización completa de ingresos vs gastos con gráficos interactivos
- **Sistema de Transacciones**: Gestión completa de gastos y retiros por categorías
- **Gestión de Entidades**: Administración completa de Clubes, Fraccionamientos, Empresas y Usuarios
- **Sistema de Reservas**: Control completo del sistema de reservas con seguimiento financiero
- **Reportes Avanzados**: Generación de reportes por rangos de fechas y filtros
- **Control de Autorización**: Sistema de autorización de transacciones por niveles de usuario
- **Categorización Financiera**: Sistema de categorías predefinidas para ingresos y gastos
- **Autenticación Segura**: Sistema de login con hash de contraseñas y control de sesiones
- **URLs Amigables**: Sistema de routing con URLs limpias y semánticas
- **Responsive Design**: Interfaz adaptable con Bootstrap 5

## 🛠️ Tecnologías Utilizadas

- **Backend**: PHP 7+ (puro, sin framework)
- **Base de Datos**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Gráficos**: Chart.js para visualización de datos
- **Icons**: Bootstrap Icons
- **Autenticación**: Sessions PHP con password_hash()

## 📋 Requisitos del Sistema

- PHP 7.0 o superior
- MySQL 5.7 o superior
- Servidor Apache con mod_rewrite habilitado
- Extensiones PHP: PDO, PDO_MySQL

## 🔧 Instalación

### 1. Descargar el código
```bash
git clone https://github.com/danjohn007/ArosportsApp.git
cd ArosportsApp
```

### 2. Configurar el servidor web
Copie todos los archivos al directorio de su servidor Apache (htdocs, www, etc.)

### 3. Configurar la base de datos

#### Opción A: Configuración automática
1. Cree una base de datos MySQL llamada `arosports`
2. Importe el archivo de estructura básica
```bash
mysql -u root -p arosports < sql/arosports_structure.sql
```
3. **IMPORTANTE**: Ejecute el script de actualización para soporte de gastos y categorías
```bash
mysql -u root -p arosports < sql/arosports_update_v1.1.sql
```

#### Opción B: Configuración manual
```sql
CREATE DATABASE arosports CHARACTER SET utf8 COLLATE utf8_general_ci;
-- Ejecute el contenido de sql/arosports_structure.sql
-- Ejecute el contenido de sql/arosports_update_v1.1.sql
```

### 4. Configurar credenciales
Edite el archivo `config/config.php` con sus credenciales de base de datos:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'arosports');  
define('DB_USER', 'su_usuario');
define('DB_PASS', 'su_contraseña');
```

### 5. Verificar instalación
Visite: `http://su-dominio/ruta-instalacion/test-db`

Este utilitario le mostrará:
- Estado de la conexión a la base de datos
- Verificación de tablas y datos
- URL base detectada automáticamente
- Instrucciones de configuración

## 🎯 Acceso al Sistema

### Credenciales por defecto:
- **SuperAdmin**: `admin@arosports.com` / `password`
- **Admin Club**: `admin.club@arosports.com` / `password`  
- **Cliente Demo**: `cliente@demo.com` / `password`

### URLs principales:
- **Login**: `/login`
- **Dashboard**: `/dashboard`
- **Test DB**: `/test-db`
- **Admin Usuarios**: `/admin/usuarios`
- **Admin Clubes**: `/admin/clubes`
- **Admin Fraccionamientos**: `/admin/fraccionamientos`
- **Admin Empresas**: `/admin/empresas`
- **Reportes**: `/reportes`

## 🏗️ Estructura del Proyecto

```
ArosportsApp/
├── config/              # Configuración del sistema
│   ├── config.php       # Configuración principal
│   └── database.php     # Clase de conexión DB
├── controllers/         # Controladores MVC
│   ├── BaseController.php
│   ├── AuthController.php
│   ├── DashboardController.php
│   ├── HomeController.php
│   └── TestController.php
├── models/              # Modelos de datos (futuras mejoras)
├── views/               # Vistas del sistema
│   ├── layouts/         # Layouts principales
│   ├── auth/            # Vistas de autenticación
│   ├── dashboard/       # Dashboard financiero
│   ├── admin/           # Paneles de administración
│   └── test/            # Utilidades de testing
├── public/              # Archivos públicos
│   ├── css/             # Estilos personalizados
│   ├── js/              # JavaScript personalizado
│   └── images/          # Imágenes del sistema
├── sql/                 # Scripts de base de datos
│   ├── arosports_structure.sql      # Estructura básica
│   └── arosports_update_v1.1.sql    # Actualización para gastos y categorías
├── utils/               # Utilidades del sistema
├── .htaccess           # Configuración Apache
├── index.php           # Punto de entrada y router
└── README.md           # Este archivo
```

## 💾 Estructura de Base de Datos

El sistema maneja las siguientes entidades principales:

### Usuarios (`usuarios`)
- Gestión de usuarios con roles: superadmin, admin, cliente
- Autenticación segura con password_hash()

### Clubes (`clubes`)  
- Información de clubes deportivos
- Datos de contacto y ubicación

### Fraccionamientos (`fraccionamientos`)
- Áreas residenciales asociadas a clubes
- Relación con clubes via foreign key

### Empresas (`empresas`)
- Gestión de empresas clientes
- Información fiscal y de contacto

### Reservas (`reservas`)
- **Núcleo del sistema de ingresos**
- Columna `precio` para tracking de ingresos
- Estados: pendiente, confirmada, cancelada, completada
- Relaciones con usuarios, clubes, fraccionamientos y empresas

### Categorías (`categorias`) - **NUEVO v1.1**
- Categorías predefinidas para ingresos y gastos
- Colores personalizables para visualización en gráficos
- Tipos: ingreso, gasto
- Incluye categorías como: Mantenimiento, Servicios Públicos, Personal, etc.

### Transacciones (`transacciones`) - **NUEVO v1.1**
- **Sistema completo de gastos y retiros**
- Soporte para múltiples tipos: ingreso, gasto, retiro
- Estados de autorización: pendiente, autorizada, cancelada
- Métodos de pago: efectivo, transferencia, cheque, tarjeta
- Sistema de autorización por niveles de usuario
- Campos para referencia, comprobantes y observaciones

## 💰 Sistema de Gestión Financiera

### Funcionalidades Principales
- **Dashboard Expandido**: Métricas de ingresos, gastos y utilidades
- **Gestión de Transacciones**: CRUD completo para gastos y retiros
- **Sistema de Categorías**: Organización por tipos de gastos e ingresos
- **Control de Autorización**: Flujo de aprobación para transacciones
- **Reportes Avanzados**: Análisis financiero completo

### Niveles de Usuario y Permisos
- **Cliente**: Puede crear transacciones (quedan pendientes)
- **Admin**: Puede autorizar/rechazar transacciones de otros usuarios
- **Superadmin**: Autorización automática, puede eliminar transacciones

## 📊 Dashboard Financiero Avanzado

El dashboard proporciona una vista completa del estado financiero:

### Métricas Principales
- **Total de Ingresos**: Suma de reservas completadas + ingresos adicionales
- **Total de Gastos**: Suma de todas las transacciones de gasto/retiro autorizadas
- **Utilidad Total**: Diferencia entre ingresos totales y gastos totales
- **Ingresos del Mes**: Ingresos del mes actual (reservas + transacciones)
- **Gastos del Mes**: Gastos del mes actual
- **Utilidad del Mes**: Ganancia/pérdida del mes actual
- **Transacciones Pendientes**: Transacciones esperando autorización

### Visualizaciones Avanzadas
- **Gráfico Ingresos vs Gastos**: Comparativa mensual de los últimos 6 meses
- **Gráfico de Gastos por Categoría**: Distribución de gastos del último mes
- **Gráfico de Ingresos por Club**: Análisis de rendimiento por club
- **Tabla de Reservas Recientes**: Actividad reciente del sistema

## 🔒 Seguridad

- Autenticación basada en sesiones PHP
- Passwords hasheados con `password_hash()`  
- Validación de permisos por rol de usuario
- Protección contra SQL injection con PDO prepared statements
- Configuración de seguridad en .htaccess

## 🛣️ Sistema de Routing

El sistema incluye un router personalizado que maneja:
- URLs amigables sin extensiones .php
- Detección automática de URL base
- Manejo de errores 404 y 403
- Redirecciones automáticas según autenticación

## 🔧 Configuración Avanzada

### URL Base Automática
El sistema detecta automáticamente la URL base, permitiendo instalación en cualquier directorio:
```php
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['SCRIPT_NAME']);
    return $protocol . '://' . $host . $path;
}
```

### Apache mod_rewrite
El archivo `.htaccess` incluye:
- Redirección de todas las peticiones al index.php
- Configuración de cache para archivos estáticos
- Protección de archivos sensibles

## 🚀 Siguientes Pasos de Desarrollo

Para extender el sistema, considere implementar:

1. **Módulo de Administración Completo**
   - CRUD completo para todas las entidades
   - Gestión avanzada de usuarios y permisos

2. **Sistema de Reportes Avanzado**
   - Filtros por fechas, usuarios, clubes
   - Exportación a PDF/Excel
   - Reportes personalizables

3. **Calendario de Actividades**
   - Integración con FullCalendar.js
   - Vista de reservas en calendario
   - Gestión de disponibilidad

4. **API REST**
   - Endpoints para integración con apps móviles
   - Documentación con Swagger

## 🐛 Solución de Problemas

### Error de conexión a base de datos
1. Verifique que MySQL esté ejecutándose
2. Confirme credenciales en `config/config.php`
3. Asegúrese de que la base de datos `arosports` existe
4. Visite `/test-db` para diagnóstico completo

### URLs no funcionan (404 en páginas internas)  
1. Verifique que mod_rewrite esté habilitado en Apache
2. Confirme que el archivo `.htaccess` existe y es legible
3. Revise los permisos del directorio

### Problemas de sesión
1. Verifique permisos de escritura en el directorio de sesiones PHP
2. Confirme que las cookies estén habilitadas en el navegador

## 📞 Soporte

Para soporte técnico o consultas:
- Revise la documentación en `/test-db`
- Verifique los logs de error de Apache/PHP
- Consulte los comentarios en el código fuente

## 📄 Licencia

Este proyecto está desarrollado como sistema propietario para Arosports.

---
**Desarrollado con ❤️ para Arosports** - Dashboard Financiero v1.0.0

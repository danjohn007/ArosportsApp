# Sistema Arosports - Dashboard Financiero

Sistema completo de administración deportiva con dashboard financiero desarrollado en PHP puro, MySQL y Bootstrap 5.

## 🚀 Características Principales

- **Dashboard Financiero**: Visualización de ingresos, estadísticas y gráficos interactivos
- **Gestión de Entidades**: Administración completa de Clubes, Fraccionamientos, Empresas y Usuarios
- **Sistema de Reservas**: Control completo del sistema de reservas con seguimiento financiero
- **Reportes Avanzados**: Generación de reportes por rangos de fechas y filtros
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
2. Importe el archivo `sql/arosports_structure.sql`
```bash
mysql -u root -p arosports < sql/arosports_structure.sql
```

#### Opción B: Configuración manual
```sql
CREATE DATABASE arosports CHARACTER SET utf8 COLLATE utf8_general_ci;
-- Luego ejecute el contenido de sql/arosports_structure.sql
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
│   └── arosports_structure.sql
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
- **Núcleo del sistema financiero**
- Columna `precio` para tracking de ingresos
- Estados: pendiente, confirmada, cancelada, completada
- Relaciones con usuarios, clubes, fraccionamientos y empresas

## 📊 Dashboard Financiero

El dashboard proporciona:

### Métricas Principales
- **Total de Ingresos**: Suma de todas las reservas completadas
- **Ingresos del Mes**: Ingresos del mes actual
- **Total de Reservas**: Contador total de reservas
- **Reservas Pendientes**: Reservas por confirmar

### Visualizaciones
- **Gráfico de Línea**: Tendencia de ingresos por mes (últimos 6 meses)
- **Gráfico de Dona**: Distribución de ingresos por club
- **Tabla de Reservas**: Listado de reservas recientes con estados

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

# gymIntranet - Sistema de Gestión para Gimnasios

Sistema web integral para la gestión de gimnasios y centros deportivos. Permite a los administradores gestionar usuarios, clases y pistas, mientras que los usuarios pueden reservar clases, pistas deportivas y hacer seguimiento de su actividad física.

## Características

- Sistema de autenticación con roles (administrador, staff, usuario)
- Gestión de usuarios y miembros
- Reserva de clases deportivas
- Reserva de pistas deportivas
- Seguimiento de actividad física
- Dashboard personalizado por tipo de usuario
- Sistema de notificaciones

## Tecnologías utilizadas

- PHP 7.4+
- MySQL
- HTML5, CSS3, JavaScript
- Bootstrap 5
- FontAwesome
- PHPMailer

## Instalación

1. Clonar el repositorio
2. Configurar el archivo `app/config/env.php` con los datos de conexión
3. Importar la estructura de la base de datos desde los archivos SQL
4. Ejecutar `composer install` para instalar dependencias

## Configuración

Para configurar la aplicación, debes modificar el archivo `app/config/env.php` con los siguientes datos:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
define('DB_NAME', 'nombre_bd');

define('URLROOT', 'http://localhost/gymIntranet');
define('SITENAME', 'Gym Intranet');
```

## Créditos

Desarrollado por Estudiant
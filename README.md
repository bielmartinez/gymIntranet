# GymIntranet

## Descripción
GymIntranet es una aplicación web completa para la gestión interna de gimnasios. Diseñada para facilitar la comunicación y la organización entre el personal del gimnasio, los entrenadores y los usuarios. La plataforma permite administrar clases, rutinas de entrenamiento, reservas, notificaciones y seguimiento físico de los usuarios.

## Características principales

### Para administradores
- **Gestión de usuarios**: Registro, visualización y edición de usuarios con diferentes roles (administrador, staff, usuario).
- **Administración de clases**: Creación, modificación y eliminación de clases grupales con horarios y aforo.
- **Panel de control**: Vista general de estadísticas del gimnasio, incluyendo asistencias, clases más populares y usuarios activos.
- **Sistema de notificaciones**: Envío de comunicaciones importantes a usuarios o miembros del staff.

### Para personal del gimnasio (staff)
- **Creación de rutinas**: Diseño de programas de entrenamiento personalizados para los usuarios.
- **Búsqueda de ejercicios**: Acceso a una amplia base de datos de ejercicios para incluir en las rutinas.
- **Seguimiento de usuarios**: Monitorización del progreso y asistencia de los clientes.
- **Comunicación directa**: Envío de notificaciones personalizadas a usuarios específicos.

### Para usuarios
- **Reserva de clases**: Visualización del calendario de clases y sistema de reservas.
- **Acceso a rutinas**: Consulta de rutinas asignadas por el personal del gimnasio.
- **Seguimiento físico**: Registro y visualización del progreso en medidas corporales y rendimiento.
- **Centro de notificaciones**: Recepción de avisos importantes del gimnasio y comunicaciones de los entrenadores.

## Tecnologías utilizadas
- PHP (Patrón MVC)
- MySQL
- JavaScript/jQuery
- HTML5/CSS3
- Bootstrap
- PHPMailer (sistema de correos)
- TCPDF (generación de documentos PDF)

## Requisitos del sistema
- Servidor web con PHP 7.4 o superior
- MySQL 5.7 o superior
- Extensiones PHP: PDO, GD, mbstring, json

## Instalación
1. Clone el repositorio en su servidor web local o remoto
2. Importe el archivo `gymintranet.sql` en su base de datos MySQL
3. Configure los parámetros de conexión a la base de datos en `app/config/config.php`
4. Asegúrese de que el servidor web tenga permisos de escritura en la carpeta `public/uploads`

## Acceso al sistema
- **URL**: http://[localhost]/gymIntranet
- **Administrador predeterminado**:
  - Usuario: admin@admin.com
  - Contraseña: admin12345


<?php
/**
 * Controlador para la gestión de funciones del personal (staff)
 */
class StaffController {
    private $userModel;
    private $classModel;
    private $reservationModel;
    
    public function __construct() {
        // Verificar que el usuario sea staff o admin
        if(!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'staff' && $_SESSION['user_role'] !== 'admin')) {
            header('Location: ' . URLROOT);
            exit;
        }
        
        $this->userModel = new User();
        
        // Cargar modelo de clases
        require_once APPROOT . '/models/Class.php';
        $this->classModel = new Class_();
        
        // Cargar modelo de reservas
        require_once APPROOT . '/models/Reservation.php';
        $this->reservationModel = new Reservation();
    }
    
    /**
     * Método helper para cargar vistas con datos
     * @param string $view Nombre de la vista a cargar
     * @param array $data Datos a pasar a la vista
     */
    private function loadView($view, $data = []) {
        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar la vista solicitada
        if (file_exists(APPROOT . '/views/' . $view . '.php')) {
            include_once APPROOT . '/views/' . $view . '.php';
        } else {
            // Mostrar un error si la vista no existe
            include_once APPROOT . '/views/shared/error/404.php';
        }
        
        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
      /**
     * Página de inicio para el personal
     */
    public function index() {
        // Obtener el ID del usuario (monitor)
        $monitorId = $_SESSION['user_id'] ?? 0;
        
        // Cargar el modelo de clases para obtener las próximas clases del monitor
        require_once APPROOT . '/models/Class.php';
        $classModel = new Class_();
        $staffClasses = $classModel->getUpcomingClassesByMonitor($monitorId, 3); // Próximos 3 días
        
        // Obtener estadísticas para el dashboard
        $data = [
            'title' => 'Dashboard del Personal',
            'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Staff',
            'staff_classes' => $staffClasses
        ];
        
        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar vista de dashboard para staff
        include_once APPROOT . '/views/staff/dashboard.php';
        
        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
    
    /**
     * Alias para index() - Dashboard para staff
     * Agregado para manejar redirecciones desde login
     */
    public function dashboard() {
        // Simplemente redirigir a index()
        $this->index();
    }
    
    /**
     * Vista de gestión de clases
     * Ahora redirige a la gestión de clases de Admin
     */
    public function classManagement() {
        // Redirigir a la vista de administración de clases
        header('Location: ' . URLROOT . '/admin/classes');
        exit;
    }
    
    /**
     * Añadir una nueva clase - Redirige a Admin
     */
    public function addClass() {
        // Redirigir a Admin
        header('Location: ' . URLROOT . '/admin/addClass');
        exit;
    }
    
    /**
     * Obtener detalles de una clase - Redirige a Admin
     */
    public function getClassDetails($classId = null) {
        // Redirigir a Admin
        header('Location: ' . URLROOT . '/admin/getClassDetails/' . $classId);
        exit;
    }
    
    /**
     * Actualizar una clase existente - Redirige a Admin
     */
    public function updateClass() {
        // Redirigir a Admin
        header('Location: ' . URLROOT . '/admin/updateClass');
        exit;
    }
    
    /**
     * Eliminar una clase - Redirige a Admin
     */
    public function deleteClass() {
        // Redirigir a Admin
        header('Location: ' . URLROOT . '/admin/deleteClass');
        exit;
    }
    
    /**
     * Actualizar el registro de asistencia a clases (a través de AJAX)
     */
    public function updateAttendance() {
        // Verificar que sea una petición AJAX POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        // Obtener datos del cuerpo de la petición
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        
        if (empty($data->attendance)) {
            echo json_encode(['success' => false, 'message' => 'No se proporcionaron datos de asistencia']);
            return;
        }
        
        // Actualizar cada registro de asistencia
        $updatedCount = 0;
        foreach ($data->attendance as $attendance) {
            if ($this->reservationModel->updateAttendance($attendance->reservationId, $attendance->attended)) {
                $updatedCount++;
            }
        }
        
        // Devolver resultado
        if ($updatedCount > 0) {
            echo json_encode(['success' => true, 'message' => 'Asistencia actualizada correctamente', 'updatedCount' => $updatedCount]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo actualizar la asistencia']);
        }
    }
    
    /**
     * Cancelar una reserva (a través de AJAX)
     */
    public function cancelReservation() {
        // Verificar que sea una petición AJAX POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        // Obtener datos del cuerpo de la petición
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        
        if (!isset($data->reservationId) || !isset($data->classId)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }
        
        // Cancelar la reserva
        if ($this->reservationModel->cancelReservation($data->reservationId)) {
            // Actualizar la capacidad actual de la clase
            $this->classModel->updateCapacity($data->classId);
            
            echo json_encode(['success' => true, 'message' => 'Reserva cancelada correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo cancelar la reserva']);
        }
    }
    
    /**
     * Vista para enviar notificaciones
     */
    public function sendNotification() {
        $data = [
            'title' => 'Enviar Notificación'
        ];
        
        // Cargar header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar vista
        include_once APPROOT . '/views/staff/send_notification.php';
        
        // Cargar footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
    
    /**
     * Vista para seguimiento de usuarios
     */
    public function userTracking() {
        $data = [
            'title' => 'Seguimiento de Usuarios'
        ];
        
        // Cargar header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar vista
        include_once APPROOT . '/views/staff/user_tracking.php';
        
        // Cargar footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
    
    /**
     * Muestra la página de gestión de clases del staff
     * Ahora redirige a la gestión de clases de Admin
     */
    public function classes() {
        // Redirigir a la vista de administración de clases
        header('Location: ' . URLROOT . '/admin/classes');
        exit;
    }
    
    /**
     * Obtener los detalles de los alumnos de una clase
     * @param int $classId ID de la clase
     */
    public function getClassStudents($classId) {
        // Verificar que la petición sea AJAX
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            header('HTTP/1.1 403 Forbidden');
            echo 'Esta ruta solo acepta peticiones AJAX';
            return;
        }
        
        // Cargar modelos necesarios
        require_once APPROOT . '/models/Class.php';
        require_once APPROOT . '/models/Reservation.php';
        
        $classModel = new Class_();
        $reservationModel = new Reservation();
        
        // Obtener la clase
        $class = $classModel->getClassById($classId);
        
        if (!$class) {
            echo json_encode(['success' => false, 'message' => 'Clase no encontrada']);
            return;
        }
        
        // Verificar que el staff actual sea el instructor asignado
        if ($_SESSION['user_role'] !== 'admin' && $class->monitor_id != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'No tienes permiso para ver esta clase']);
            return;
        }
        
        // Obtener reservas/alumnos para esta clase
        $students = $reservationModel->getStudentsByClassId($classId);
        
        // Devolver como JSON
        echo json_encode([
            'success' => true,
            'class' => $class,
            'students' => $students
        ]);
    }
    
    /**
     * Actualiza el registro de asistencia de los alumnos
     */
    public function updateStudentAttendance() {
        // Verificar que la petición sea AJAX y POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || 
            !isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        // Cargar modelo de reservas
        require_once APPROOT . '/models/Reservation.php';
        $reservationModel = new Reservation();
        
        // Obtener datos del cuerpo de la petición
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        
        if (empty($data->attendance)) {
            echo json_encode(['success' => false, 'message' => 'No se proporcionaron datos de asistencia']);
            return;
        }
        
        // Actualizar cada registro de asistencia
        $updatedCount = 0;
        foreach ($data->attendance as $attendance) {
            if ($reservationModel->updateAttendance($attendance->reservationId, $attendance->attended)) {
                $updatedCount++;
            }
        }
        
        // Devolver resultado
        if ($updatedCount > 0) {
            echo json_encode([
                'success' => true, 
                'message' => 'Asistencia actualizada correctamente', 
                'updatedCount' => $updatedCount
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'No se pudo actualizar la asistencia'
            ]);
        }
    }
    
    /**
     * Cancelar una reserva de un estudiante específica
     */
    public function cancelStudentReservation() {
        // Verificar que la petición sea AJAX y POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || 
            !isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        // Cargar modelos necesarios
        require_once APPROOT . '/models/Reservation.php';
        require_once APPROOT . '/models/Class.php';
        
        $reservationModel = new Reservation();
        $classModel = new Class_();
        
        // Obtener datos del cuerpo de la petición
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        
        if (empty($data->reservationId) || empty($data->classId)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }
        
        // Verificar permisos (solo el monitor asignado o un admin pueden cancelar)
        $class = $classModel->getClassById($data->classId);
        
        if (!$class) {
            echo json_encode(['success' => false, 'message' => 'Clase no encontrada']);
            return;
        }
        
        if ($_SESSION['user_role'] !== 'admin' && $class->monitor_id != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'No tienes permiso para cancelar esta reserva']);
            return;
        }
        
        // Cancelar la reserva usando el método correcto: cancelReservation
        if ($reservationModel->cancelReservation($data->reservationId)) {
            // Actualizar capacidad actual de la clase
            if ($classModel->updateCapacity($data->classId, -1)) {
                echo json_encode(['success' => true, 'message' => 'Reserva cancelada correctamente']);
            } else {
                echo json_encode(['success' => true, 'message' => 'Reserva cancelada, pero hubo un problema al actualizar la capacidad']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo cancelar la reserva']);
        }
    }
    
    /**
     * Filtrar clases - Redirige a Admin
     */
    public function filterClasses() {
        // Redirigir a Admin
        header('Location: ' . URLROOT . '/admin/filterClasses');
        exit;
    }
}
?>
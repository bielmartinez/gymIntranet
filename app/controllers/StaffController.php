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
     * Página de inicio para el personal
     */
    public function index() {
        // Obtener estadísticas para el dashboard
        $data = [
            'title' => 'Dashboard del Personal',
            'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Staff'
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
     */
    public function classManagement() {
        $data = [
            'title' => 'Gestión de Clases'
        ];
        
        // Cargar header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar vista
        include_once APPROOT . '/views/staff/class_management.php';
        
        // Cargar footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
    
    /**
     * Añadir una nueva clase
     */
    public function addClass() {
        // Verificar si se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: ' . URLROOT . '/staff/classManagement');
            exit;
        }
        
        // Recoger datos del formulario
        $data = [
            'tipus_classe_id' => trim($_POST['tipus_classe_id']),
            'monitor_id' => trim($_POST['monitor_id']),
            'data' => trim($_POST['data']),
            'hora' => trim($_POST['hora']),
            'duracio' => trim($_POST['duracio']),
            'capacitat_maxima' => trim($_POST['capacitat_maxima']),
            'sala' => trim($_POST['sala'])
        ];
        
        // Verificar si hay conflictos de horario para el monitor
        if ($this->classModel->hasScheduleConflict($data)) {
            $_SESSION['staff_message'] = 'El monitor ya tiene una clase programada en ese horario';
            $_SESSION['staff_message_type'] = 'danger';
            header('Location: ' . URLROOT . '/staff/classManagement');
            exit;
        }
        
        // Añadir la clase
        if ($this->classModel->addClass($data)) {
            $_SESSION['staff_message'] = 'Clase añadida correctamente';
            $_SESSION['staff_message_type'] = 'success';
        } else {
            $_SESSION['staff_message'] = 'Error al añadir la clase';
            $_SESSION['staff_message_type'] = 'danger';
        }
        
        header('Location: ' . URLROOT . '/staff/classManagement');
        exit;
    }
    
    /**
     * Obtener detalles de una clase (para AJAX)
     * @param int $classId ID de la clase
     */
    public function getClassDetails($classId = null) {
        // Verificar que se proporcione un ID
        if (!$classId) {
            echo json_encode(['success' => false, 'error' => 'ID de clase no válido']);
            return;
        }
        
        // Obtener la clase
        $class = $this->classModel->getClassById($classId);
        
        if (!$class) {
            echo json_encode(['success' => false, 'error' => 'Clase no encontrada']);
            return;
        }
        
        // Obtener información adicional
        require_once APPROOT . '/models/TypeClass.php';
        $typeClassModel = new TypeClass();
        $typeClass = $typeClassModel->getById($class->tipus_classe_id);
        $typeClassName = $typeClass ? $typeClass->nom : 'N/A';
        
        // Formatear la fecha y hora para el formulario
        $class->data = date('Y-m-d', strtotime($class->data));
        $class->hora = date('H:i', strtotime($class->hora));
        
        // Obtener alumnos inscritos en la clase
        $students = $this->reservationModel->getClassReservations($classId);
        
        // Devolver los datos como JSON
        echo json_encode([
            'success' => true, 
            'class' => $class,
            'typeClassName' => $typeClassName,
            'students' => $students
        ]);
    }
    
    /**
     * Actualizar una clase existente
     */
    public function updateClass() {
        // Verificar si se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: ' . URLROOT . '/staff/classManagement');
            exit;
        }
        
        // Recoger datos del formulario
        $data = [
            'classe_id' => trim($_POST['classe_id']),
            'tipus_classe_id' => trim($_POST['tipus_classe_id']),
            'monitor_id' => trim($_POST['monitor_id']),
            'data' => trim($_POST['data']),
            'hora' => trim($_POST['hora']),
            'duracio' => trim($_POST['duracio']),
            'capacitat_maxima' => trim($_POST['capacitat_maxima']),
            'sala' => trim($_POST['sala'])
        ];
        
        // Verificar que la capacidad máxima no sea menor que la actual
        $currentClass = $this->classModel->getClassById($data['classe_id']);
        if ($currentClass && $data['capacitat_maxima'] < $currentClass->capacitat_actual) {
            $_SESSION['staff_message'] = 'La capacidad máxima no puede ser menor que el número actual de reservas (' . $currentClass->capacitat_actual . ')';
            $_SESSION['staff_message_type'] = 'danger';
            header('Location: ' . URLROOT . '/staff/classManagement');
            exit;
        }
        
        // Verificar si hay conflictos de horario para el monitor
        if ($this->classModel->hasScheduleConflict($data, $data['classe_id'])) {
            $_SESSION['staff_message'] = 'El monitor ya tiene una clase programada en ese horario';
            $_SESSION['staff_message_type'] = 'danger';
            header('Location: ' . URLROOT . '/staff/classManagement');
            exit;
        }
        
        // Actualizar la clase
        if ($this->classModel->updateClass($data)) {
            $_SESSION['staff_message'] = 'Clase actualizada correctamente';
            $_SESSION['staff_message_type'] = 'success';
        } else {
            $_SESSION['staff_message'] = 'Error al actualizar la clase';
            $_SESSION['staff_message_type'] = 'danger';
        }
        
        header('Location: ' . URLROOT . '/staff/classManagement');
        exit;
    }
    
    /**
     * Eliminar una clase
     */
    public function deleteClass() {
        // Verificar si se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: ' . URLROOT . '/staff/classManagement');
            exit;
        }
        
        // Verificar que se haya proporcionado un ID
        if (!isset($_POST['classe_id']) || empty($_POST['classe_id'])) {
            $_SESSION['staff_message'] = 'ID de clase no válido';
            $_SESSION['staff_message_type'] = 'danger';
            header('Location: ' . URLROOT . '/staff/classManagement');
            exit;
        }
        
        // Eliminar la clase
        if ($this->classModel->deleteClass($_POST['classe_id'])) {
            $_SESSION['staff_message'] = 'Clase eliminada correctamente';
            $_SESSION['staff_message_type'] = 'success';
        } else {
            $_SESSION['staff_message'] = 'Error al eliminar la clase';
            $_SESSION['staff_message_type'] = 'danger';
        }
        
        header('Location: ' . URLROOT . '/staff/classManagement');
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
}
?>
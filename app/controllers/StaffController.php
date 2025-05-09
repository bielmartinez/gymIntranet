<?php
/**
 * Controlador para la gestión de funciones del personal (staff)
 */
class StaffController extends BaseController {
    protected $userModel;
    protected $classModel;
    protected $reservationModel;
      public function __construct() {
        parent::__construct();
        
        // Verificar que el usuario sea staff o admin
        $this->requireRole(['staff', 'admin']);
          // Cargar explícitamente el modelo Class ya que es una palabra reservada
        require_once dirname(__DIR__) . '/models/Class.php';
        
        // Inicializar modelos
        $this->userModel = new User();
        $this->classModel = new Class_();
        $this->reservationModel = new Reservation();
    }    /**
     * Página de inicio para el personal
     */    
    public function index() {
        // Obtener el ID del usuario (monitor)
        $monitorId = $_SESSION['user_id'] ?? 0;
        
        // Obtener las próximas clases del monitor
        $staffClasses = $this->classModel->getUpcomingClassesByMonitor($monitorId, 3); // Próximos 3 días
          // Obtener información del usuario para mostrar su nombre
        $userData = $this->userModel->getUserById($monitorId);

        // Preparar datos para la vista
        $data = [
            'title' => 'Dashboard del Personal',
            'staff_classes' => $staffClasses,
            'user_name' => $userData ? $userData->nombre : 'Usuario'
        ];
        
        // Cargar la vista usando el método del BaseController
        $this->loadView('staff/dashboard', $data);
    }
    
    /**
     * Alias para index() - Dashboard para staff
     * Agregado para manejar redirecciones desde login
     */    public function dashboard() {
        // Simplemente redirigir a index()
        $this->index();
    }
    
    /**
     * Manejo de acceso al antiguo método userTracking (redirigir a users)
     * Para mantener compatibilidad con enlaces existentes
     */
    public function userTracking() {
        // Redirigir a la nueva funcionalidad de usuarios
        $this->redirect('staff/users');
    }
      
    /**
     * Vista de gestión de clases
     * Ahora redirige a la gestión de clases de Admin
     */
    public function classManagement() {
        // Redirigir a la vista de administración de clases usando el método del BaseController
        $this->redirect('admin/classes');
    }
    
    /**
     * Añadir una nueva clase - Redirige a Admin
     */
    public function addClass() {
        // Redirigir a Admin usando el método del BaseController
        $this->redirect('admin/addClass');
    }
    
    /**
     * Obtener detalles de una clase - Redirige a Admin
     */
    public function getClassDetails($classId = null) {
        // Redirigir a Admin usando el método del BaseController
        $this->redirect('admin/getClassDetails/' . ($classId ?? ''));
    }
    
    /**
     * Actualizar una clase existente - Redirige a Admin
     */
    public function updateClass() {
        // Redirigir a Admin usando el método del BaseController
        $this->redirect('admin/updateClass');
    }
    
    /**
     * Eliminar una clase - Redirige a Admin
     */    
    public function deleteClass() {
        // Redirigir a Admin usando el método del BaseController
        $this->redirect('admin/deleteClass');
    }
      /**
     * Actualizar el registro de asistencia a clases (a través de AJAX)
     */
    public function updateAttendance() {
        // Verificar que sea una petición POST usando el método del BaseController
        if (!$this->isPost()) {
            $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
            return;
        }
        
        // Obtener datos del cuerpo de la petición
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        
        if (empty($data->attendance)) {
            $this->jsonResponse(['success' => false, 'message' => 'No se proporcionaron datos de asistencia'], 400);
            return;
        }
        
        // Actualizar cada registro de asistencia
        $updatedCount = 0;
        foreach ($data->attendance as $attendance) {
            if ($this->reservationModel->updateAttendance($attendance->reservationId, $attendance->attended)) {
                $updatedCount++;
            }
        }
        
        // Devolver resultado usando el método del BaseController
        if ($updatedCount > 0) {
            $this->jsonResponse([
                'success' => true, 
                'message' => 'Asistencia actualizada correctamente', 
                'updatedCount' => $updatedCount
            ]);
        } else {
            $this->jsonResponse([
                'success' => false, 
                'message' => 'No se pudo actualizar la asistencia'
            ], 500);
        }
    }
      /**
     * Cancelar una reserva (a través de AJAX)
     */
    public function cancelReservation() {
        // Verificar que sea una petición POST usando el método del BaseController
        if (!$this->isPost()) {
            $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
            return;
        }
        
        // Obtener datos del cuerpo de la petición
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        
        if (!isset($data->reservationId) || !isset($data->classId)) {
            $this->jsonResponse(['success' => false, 'message' => 'Datos incompletos'], 400);
            return;
        }
        
        // Cancelar la reserva
        if ($this->reservationModel->cancelReservation($data->reservationId)) {
            // Actualizar la capacidad actual de la clase
            $this->classModel->updateCapacity($data->classId);
            
            $this->jsonResponse(['success' => true, 'message' => 'Reserva cancelada correctamente']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'No se pudo cancelar la reserva'], 500);
        }
    }
      /**
     * Lista todos los usuarios del sistema para consulta por parte del staff
     * Similar a la vista de admin pero sin opciones de activar/desactivar
     */    
    public function users() {
        // Obtener todos los usuarios
        $users = $this->userModel->getAllUsers();
        
        $data = [
            'title' => 'Consulta de Usuarios',
            'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Staff',
            'users' => $users
        ];
        
        // Cargar la vista usando el método del BaseController
        $this->loadView('staff/users', $data);
    }
    
    /**
     * Muestra la página de gestión de clases del staff
     * Ahora redirige a la gestión de clases de Admin
     */
    public function classes() {
        // Redirigir a la vista de administración de clases usando el método del BaseController
        $this->redirect('admin/classes');
    }
      /**
     * Obtener los detalles de los alumnos de una clase
     * @param int $classId ID de la clase
     */
    public function getClassStudents($classId) {
        // Verificar que el ID sea válido
        if (!$classId) {
            $this->jsonResponse(['success' => false, 'message' => 'ID de clase no proporcionado'], 400);
            return;
        }
        
        // Obtener la clase
        $class = $this->classModel->getClassById($classId);
        
        if (!$class) {
            $this->jsonResponse(['success' => false, 'message' => 'Clase no encontrada'], 404);
            return;
        }
        
        // Verificar que el staff actual sea el instructor asignado o un admin
        if ($_SESSION['user_role'] !== 'admin' && $class->monitor_id != $_SESSION['user_id']) {
            $this->jsonResponse(['success' => false, 'message' => 'No tienes permiso para ver esta clase'], 403);
            return;
        }
        
        // Obtener reservas/alumnos para esta clase
        $students = $this->reservationModel->getStudentsByClassId($classId);
        
        // Devolver como JSON usando el método del BaseController
        $this->jsonResponse([
            'success' => true,
            'class' => $class,
            'students' => $students
        ]);
    }
      /**
     * Actualiza el registro de asistencia de los alumnos
     */
    public function updateStudentAttendance() {
        // Verificar que sea una petición POST usando el método del BaseController
        if (!$this->isPost()) {
            $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
            return;
        }
        
        // Obtener datos del cuerpo de la petición
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        
        if (empty($data->attendance)) {
            $this->jsonResponse(['success' => false, 'message' => 'No se proporcionaron datos de asistencia'], 400);
            return;
        }
        
        // Actualizar cada registro de asistencia
        $updatedCount = 0;
        foreach ($data->attendance as $attendance) {
            if ($this->reservationModel->updateAttendance($attendance->reservationId, $attendance->attended)) {
                $updatedCount++;
            }
        }
        
        // Devolver resultado usando el método del BaseController
        if ($updatedCount > 0) {
            $this->jsonResponse([
                'success' => true, 
                'message' => 'Asistencia actualizada correctamente', 
                'updatedCount' => $updatedCount
            ]);
        } else {
            $this->jsonResponse([
                'success' => false, 
                'message' => 'No se pudo actualizar la asistencia'
            ], 500);
        }
    }
      /**
     * Cancelar una reserva de un estudiante específica
     */
    public function cancelStudentReservation() {
        // Verificar que sea una petición POST usando el método del BaseController
        if (!$this->isPost()) {
            $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
            return;
        }
        
        // Obtener datos del cuerpo de la petición
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        
        if (empty($data->reservationId) || empty($data->classId)) {
            $this->jsonResponse(['success' => false, 'message' => 'Datos incompletos'], 400);
            return;
        }
        
        // Verificar permisos (solo el monitor asignado o un admin pueden cancelar)
        $class = $this->classModel->getClassById($data->classId);
        
        if (!$class) {
            $this->jsonResponse(['success' => false, 'message' => 'Clase no encontrada'], 404);
            return;
        }
        
        if ($_SESSION['user_role'] !== 'admin' && $class->monitor_id != $_SESSION['user_id']) {
            $this->jsonResponse(['success' => false, 'message' => 'No tienes permiso para cancelar esta reserva'], 403);
            return;
        }
        
        // Cancelar la reserva
        if ($this->reservationModel->cancelReservation($data->reservationId)) {
            // Actualizar capacidad actual de la clase
            if ($this->classModel->updateCapacity($data->classId, -1)) {
                $this->jsonResponse(['success' => true, 'message' => 'Reserva cancelada correctamente']);
            } else {
                $this->jsonResponse([
                    'success' => true, 
                    'message' => 'Reserva cancelada, pero hubo un problema al actualizar la capacidad'
                ]);
            }
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'No se pudo cancelar la reserva'], 500);
        }
    }
      /**
     * Filtrar clases - Redirige a Admin
     */
    public function filterClasses() {
        // Redirigir a Admin usando el método del BaseController
        $this->redirect('admin/filterClasses');
    }
}
?>
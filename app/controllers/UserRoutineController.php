<?php
/**
 * Controlador para la gestión de rutinas por parte de los usuarios
 */
class UserRoutineController {
    private $routineModel;
    private $userModel;
    
    public function __construct() {
        // Verificar que el usuario esté logueado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        $this->routineModel = new Routine();
        $this->userModel = new User();
    }

    // Vista principal de rutinas del usuario
    public function index() {
        // Obtener el ID del usuario logueado
        $userId = $_SESSION['user_id'];
        
        // Obtener todas las rutinas del usuario
        $routines = $this->routineModel->getRoutinesByUser($userId);

        $data = [
            'title' => 'Mis Rutinas',
            'routines' => $routines,
            'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Usuario'
        ];

        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar la vista
        include_once APPROOT . '/views/users/routines.php';
        
        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }

    // Ver detalle de una rutina específica
    public function view($id) {
        // Obtener el ID del usuario logueado
        $userId = $_SESSION['user_id'];
        
        // Obtener la rutina
        $routine = $this->routineModel->getRoutineById($id);
        
        // Verificar que la rutina exista
        if (!$routine) {
            flash('routine_message', 'La rutina no existe', 'alert alert-danger');
            $_SESSION['toast_message'] = 'La rutina no existe';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/userRoutine');
            exit;
        }
        
        // Verificar que la rutina pertenezca al usuario o que sea administrador
        if ($routine->usuari_id != $userId && $_SESSION['user_role'] !== 'admin') {
            flash('routine_message', 'No tienes permiso para ver esta rutina', 'alert alert-danger');
            $_SESSION['toast_message'] = 'No tienes permiso para ver esta rutina';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/userRoutine');
            exit;
        }
        
        // Obtener los ejercicios de la rutina
        $exercises = $this->routineModel->getExercisesByRoutine($id);
        
        $data = [
            'title' => 'Rutina: ' . $routine->nom,
            'routine' => $routine,
            'exercises' => $exercises,
            'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Usuario'
        ];

        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar la vista
        include_once APPROOT . '/views/users/view_routine.php';
        
        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
    
    // Descargar PDF de una rutina
    public function downloadPDF($id) {
        // Obtener el ID del usuario logueado
        $userId = $_SESSION['user_id'];
        
        // Obtener la rutina
        $routine = $this->routineModel->getRoutineById($id);
        
        // Verificar que la rutina exista y tenga un PDF
        if (!$routine || empty($routine->ruta_pdf)) {
            flash('routine_message', 'El PDF no está disponible para descargar', 'alert alert-danger');
            $_SESSION['toast_message'] = 'El PDF no está disponible para descargar';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/userRoutine');
            exit;
        }
        
        // Verificar que la rutina pertenezca al usuario o que sea administrador
        if ($routine->usuari_id != $userId && $_SESSION['user_role'] !== 'admin') {
            flash('routine_message', 'No tienes permiso para descargar esta rutina', 'alert alert-danger');
            $_SESSION['toast_message'] = 'No tienes permiso para descargar esta rutina';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/userRoutine');
            exit;
        }
        
        // Construir la ruta completa al archivo PDF
        $filePath = dirname(dirname(__DIR__)) . '/public/' . $routine->ruta_pdf;
        
        if (file_exists($filePath)) {
            // Enviar el archivo al navegador
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
        } else {
            flash('routine_message', 'El archivo PDF no se encuentra en el servidor', 'alert alert-danger');
            $_SESSION['toast_message'] = 'El archivo PDF no se encuentra en el servidor';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/userRoutine');
            exit;
        }
    }
}
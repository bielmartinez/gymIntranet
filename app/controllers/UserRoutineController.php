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

    /**
     * Muestra los detalles de una rutina específica para el usuario
     * @param int $id ID de la rutina a ver
     */
    public function viewRoutine($id = null) {
        // Verificar si el usuario está logueado
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['toast_message'] = 'Debes iniciar sesión para ver las rutinas';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
        
        if (!$id) {
            $_SESSION['toast_message'] = 'La rutina no existe';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/user/routines');
            exit;
        }
        
        // Obtener la rutina
        require_once APPROOT . '/models/Routine.php';
        $routineModel = new Routine();
        $routine = $routineModel->getRoutineById($id);
        
        // Verificar si la rutina existe y pertenece al usuario
        if (!$routine) {
            $_SESSION['toast_message'] = 'La rutina no existe';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/user/routines');
            exit;
        }
        
        // Verificar que el usuario tenga permiso para ver esta rutina
        if ($routine->usuari_id != $_SESSION['user_id'] && $_SESSION['user_role'] !== 'admin') {
            $_SESSION['toast_message'] = 'No tienes permiso para ver esta rutina';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/user/routines');
            exit;
        }
        
        // Obtener los ejercicios de la rutina
        $exercises = $routineModel->getExercisesByRoutine($id);
        
        $data = [
            'title' => 'Ver Rutina',
            'routine' => $routine,
            'exercises' => $exercises
        ];
        
        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar la vista
        include_once APPROOT . '/views/users/view_routine.php';
        
        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }

    /**
     * Método de compatibilidad para redirigir a viewRoutine
     * @param int $id ID de la rutina a ver
     */
    public function view($id = null) {
        // Simplemente redirigir a viewRoutine
        $this->viewRoutine($id);
    }
    
    /**
     * Genera y descarga un PDF con la rutina del usuario
     * @param int $id ID de la rutina a descargar
     */
    public function downloadRoutine($id = null) {
        // Verificar si el usuario está logueado
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['toast_message'] = 'Debes iniciar sesión para descargar rutinas';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
        
        if (!$id) {
            $_SESSION['toast_message'] = 'La rutina no existe';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/user/routines');
            exit;
        }
        
        // Obtener la rutina
        require_once APPROOT . '/models/Routine.php';
        $routineModel = new Routine();
        $routine = $routineModel->getRoutineById($id);
        
        // Verificar si la rutina existe
        if (!$routine) {
            $_SESSION['toast_message'] = 'La rutina no existe';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/user/routines');
            exit;
        }
        
        // Verificar que el usuario tenga permiso para descargar esta rutina
        if ($routine->usuari_id != $_SESSION['user_id'] && $_SESSION['user_role'] !== 'admin') {
            $_SESSION['toast_message'] = 'No tienes permiso para descargar esta rutina';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/user/routines');
            exit;
        }
        
        // Obtener ejercicios de la rutina
        $exercises = $routineModel->getExercisesByRoutine($id);
        
        // Generar el PDF
        require_once APPROOT . '/utils/PDFGenerator.php';
        $pdfGenerator = new PDFGenerator();
        $pdfPath = $pdfGenerator->generateRoutinePDF($routine, $exercises);
        
        if ($pdfPath) {
            // Descargar el PDF
            if (file_exists(APPROOT . '/../public/' . $pdfPath)) {
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="rutina_' . $id . '.pdf"');
                header('Cache-Control: max-age=0');
                readfile(APPROOT . '/../public/' . $pdfPath);
                exit;
            }
        }
        
        // Si llegamos aquí, hubo un error
        $_SESSION['toast_message'] = 'Error al generar el PDF';
        $_SESSION['toast_type'] = 'error';
        header('Location: ' . URLROOT . '/user/routines');
        exit;
    }

    /**
     * Método de compatibilidad para redirigir a downloadRoutine
     * @param int $id ID de la rutina a descargar en PDF
     */
    public function downloadPDF($id = null) {
        // Simplemente redirigir a downloadRoutine
        $this->downloadRoutine($id);
    }
}
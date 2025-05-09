<?php
/**
 * Controlador para la gestión de rutinas por parte de los usuarios
 */
require_once dirname(__DIR__) . '/utils/PDFGenerator.php';

class UserRoutineController extends BaseController {
    protected $routineModel;
    protected $userModel;
    protected $pdfGenerator;
    
    public function __construct() {
        parent::__construct();
        
        // Verificar que el usuario esté logueado
        $this->requireAuth();

        $this->routineModel = new Routine();
        $this->userModel = new User();
        $this->pdfGenerator = new PDFGenerator();
    }
    
    /**
     * Vista principal de rutinas del usuario
     */    public function index() {
        // Obtener el ID del usuario logueado
        $userId = $_SESSION['user_id'];
        
        // Obtener todas las rutinas del usuario
        $routines = $this->routineModel->getRoutinesByUser($userId);

        $data = [
            'title' => 'Mis Rutinas',
            'routines' => $routines
        ];

        // Cargar la vista usando el método del BaseController
        $this->loadView('users/routines', $data);
    }
    
    /**
     * Muestra los detalles de una rutina específica para el usuario
     * @param int $id ID de la rutina a ver
     */
    public function viewRoutine($id = null) {
        // La autenticación ya se verifica en el constructor
        
        if (!$id) {
            $this->handleError('La rutina no existe', 'userRoutine');
            return;
        }
        
        // Obtener la rutina
        $routine = $this->routineModel->getRoutineById($id);
        
        // Verificar si la rutina existe
        if (!$routine) {
            $this->handleError('La rutina no existe', 'userRoutine');
            return;
        }
        
        // Verificar que el usuario tenga permiso para ver esta rutina
        if ($routine->usuari_id != $_SESSION['user_id'] && $_SESSION['user_role'] !== 'admin') {
            $this->handleError('No tienes permiso para ver esta rutina', 'userRoutine');
            return;
        }
        
        // Obtener los ejercicios de la rutina
        $exercises = $this->routineModel->getExercisesByRoutine($id);
        
        $data = [
            'title' => 'Ver Rutina',
            'routine' => $routine,
            'exercises' => $exercises
        ];
        
        // Cargar la vista usando el método del BaseController
        $this->loadView('users/view_routine', $data);
    }    /**
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
        // La autenticación ya se verifica en el constructor
        
        if (!$id) {
            $this->handleError('La rutina no existe', 'userRoutine');
            return;
        }
        
        // Obtener la rutina
        $routine = $this->routineModel->getRoutineById($id);
        
        // Verificar si la rutina existe
        if (!$routine) {
            $this->handleError('La rutina no existe', 'userRoutine');
            return;
        }
        
        // Verificar que el usuario tenga permiso para descargar esta rutina
        if ($routine->usuari_id != $_SESSION['user_id'] && $_SESSION['user_role'] !== 'admin') {
            $this->handleError('No tienes permiso para descargar esta rutina', 'userRoutine');
            return;
        }
        
        // Obtener ejercicios de la rutina
        $exercises = $this->routineModel->getExercisesByRoutine($id);
        
        // Generar el PDF
        $pdfPath = $this->pdfGenerator->generateRoutinePDF($routine, $exercises);
        
        if ($pdfPath) {
            // Descargar el PDF
            if (file_exists(APPROOT . '/../public/' . $pdfPath)) {
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="rutina_' . $id . '.pdf"');
                header('Cache-Control: max-age=0');
                readfile(APPROOT . '/../public/' . $pdfPath);
                exit;
            } else {
                $this->handleError('El archivo PDF no existe', 'userRoutine');
                return;
            }
        } else {
            $this->handleError('Error al generar el PDF', 'userRoutine');
            return;
        }
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
<?php
/**
 * Controlador para la gestión de rutinas por parte del staff (monitores)
 */
require_once dirname(__DIR__) . '/utils/ExerciseApiService.php';
require_once dirname(__DIR__) . '/utils/PDFGenerator.php';
require_once dirname(__DIR__) . '/utils/FlashMessages.php';
require_once dirname(__DIR__) . '/utils/AuthUtils.php';

class StaffRoutineController
{
    private $routineModel;
    private $userModel;
    private $exerciseApiService;
    private $pdfGenerator;

    public function __construct()
    {
        // Verificar que el usuario esté logueado y sea staff
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        } elseif ($_SESSION['user_role'] !== 'staff' && $_SESSION['user_role'] !== 'admin') {
            // Solo el personal y administradores pueden acceder
            header('Location: ' . URLROOT . '/users/dashboard');
            exit;
        }

        $this->routineModel = new Routine();
        $this->userModel = new User();
        $this->exerciseApiService = new ExerciseApiService();
        $this->pdfGenerator = new PDFGenerator();
    }

    // Listar todas las rutinas
    public function index()
    {
        $routines = $this->routineModel->getAllRoutines();
        $users = $this->userModel->getAllUsers();

        $data = [
            'title' => 'Rutinas',
            'routines' => $routines,
            'users' => $users
        ];

        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';

        // Cargar la vista
        include_once APPROOT . '/views/staff/routines.php';

        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }

    // Crear una nueva rutina
    public function create()
    {
        // Verificar permisos
        if (!isStaff() && !isAdmin()) {
            header('Location: ' . URLROOT);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitizar input
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            // Datos para modelo (corregidos para usar los nombres de campos del formulario)
            $data = [
                'name' => trim($_POST['nombre'] ?? ''),
                'description' => trim($_POST['descripcion'] ?? ''),
                'usuari_id' => isset($_POST['usuario_id']) ? $_POST['usuario_id'] : null,
                'exercises' => isset($_POST['exercises']) ? $_POST['exercises'] : []
            ];

            // Registrar datos para depuración
            if (class_exists('Logger')) {
                Logger::log('DEBUG', 'Intentando crear rutina: ' . json_encode($data));
            }

            // Validar entrada
            if (empty($data['name'])) {
                flash('routine_message', 'Por favor, introduce un nombre para la rutina', 'alert alert-danger');
                $_SESSION['toast_message'] = 'Por favor, introduce un nombre para la rutina';
                $_SESSION['toast_type'] = 'error';
                header('Location: ' . URLROOT . '/staffRoutine/create');
                exit;
            }

            // Crear rutina
            $routineData = [
                'nom' => $data['name'],
                'descripcio' => $data['description'],
                'usuari_id' => $data['usuari_id']
            ];

            $routineId = $this->routineModel->addRoutine($routineData);

            if ($routineId) {
                // Generar PDF si se solicita
                if (isset($_POST['generate_pdf']) && $_POST['generate_pdf'] == 'yes') {
                    $this->generatePDF($routineId);
                }

                flash('routine_message', 'Rutina creada con éxito', 'alert alert-success');
                $_SESSION['toast_message'] = 'Rutina creada con éxito';
                $_SESSION['toast_type'] = 'success';
                header('Location: ' . URLROOT . '/staffRoutine');
            } else {
                flash('routine_message', 'Error al crear la rutina', 'alert alert-danger');
                $_SESSION['toast_message'] = 'Error al crear la rutina';
                $_SESSION['toast_type'] = 'error';
                header('Location: ' . URLROOT . '/staffRoutine/create');
            }
            exit;
        }

        // Obtener usuarios para asignar rutina
        $users = $this->userModel->getAllUsers();

        $data = [
            'users' => $users
        ];

        $this->view('staff/create_routine', $data);
    }

    // Editar una rutina
    public function edit($id)
    {
        // Verificar permisos
        if (!isStaff() && !isAdmin()) {
            header('Location: ' . URLROOT);
            exit;
        }

        // Obtener datos de la rutina
        $routine = $this->routineModel->getRoutineById($id);
        
        if (!$routine) {
            flash('routine_message', 'Rutina no encontrada', 'alert alert-danger');
            $_SESSION['toast_message'] = 'Rutina no encontrada';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitizar input
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Datos para modelo (compatible con nombres en inglés y español)
            $data = [
                'id' => $id,
                'name' => trim($_POST['name'] ?? $_POST['nombre'] ?? ''),
                'description' => trim($_POST['description'] ?? $_POST['descripcion'] ?? ''),
                'usuari_id' => isset($_POST['user_id']) ? $_POST['user_id'] : (isset($_POST['usuario_id']) ? $_POST['usuario_id'] : $routine->usuari_id)
            ];
            
            // Registrar datos para depuración
            if (class_exists('Logger')) {
                Logger::log('DEBUG', 'Intentando editar rutina: ' . json_encode($data));
            }

            // Validar entrada
            if (empty($data['name'])) {
                flash('routine_message', 'Por favor, introduce un nombre para la rutina', 'alert alert-danger');
                $_SESSION['toast_message'] = 'Por favor, introduce un nombre para la rutina';
                $_SESSION['toast_type'] = 'error';
                header('Location: ' . URLROOT . '/staffRoutine/edit/' . $id);
                exit;
            }

            // Actualizar rutina
            if ($this->routineModel->updateRoutine($data)) {
                // Generar PDF si se solicita
                if (isset($_POST['generate_pdf']) && $_POST['generate_pdf'] == 'yes') {
                    $this->generatePDF($id);
                }

                flash('routine_message', 'Rutina actualizada con éxito', 'alert alert-success');
                $_SESSION['toast_message'] = 'Rutina actualizada con éxito';
                $_SESSION['toast_type'] = 'success';
                header('Location: ' . URLROOT . '/staffRoutine');
            } else {
                flash('routine_message', 'Error al actualizar la rutina', 'alert alert-danger');
                $_SESSION['toast_message'] = 'Error al actualizar la rutina';
                $_SESSION['toast_type'] = 'error';
                header('Location: ' . URLROOT . '/staffRoutine/edit/' . $id);
            }
            exit;
        }

        // Obtener ejercicios de la rutina
        $exercises = $this->routineModel->getExercisesByRoutineId($id);
        
        // Obtener usuarios para asignar rutina
        $users = $this->userModel->getAllUsers();

        $data = [
            'routine' => $routine,
            'exercises' => $exercises,
            'users' => $users
        ];

        $this->view('staff/edit_routine', $data);
    }

    // Eliminar una rutina
    public function delete($id)
    {
        // Verificar permisos
        if (!isStaff() && !isAdmin()) {
            header('Location: ' . URLROOT);
            exit;
        }

        // Verificar si existe la rutina
        $routine = $this->routineModel->getRoutineById($id);
        
        if (!$routine) {
            flash('routine_message', 'Rutina no encontrada', 'alert alert-danger');
            $_SESSION['toast_message'] = 'Rutina no encontrada';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->routineModel->deleteRoutine($id)) {
                flash('routine_message', 'Rutina eliminada con éxito', 'alert alert-success');
                $_SESSION['toast_message'] = 'Rutina eliminada con éxito';
                $_SESSION['toast_type'] = 'success';
            } else {
                flash('routine_message', 'Error al eliminar la rutina', 'alert alert-danger');
                $_SESSION['toast_message'] = 'Error al eliminar la rutina';
                $_SESSION['toast_type'] = 'error';
            }
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }

        // Mostrar confirmación de eliminación
        $data = [
            'routine' => $routine
        ];

        $this->view('staff/delete_routine', $data);
    }

    // Añadir un ejercicio a una rutina
    public function addExercise($routineId = null)
    {
        // Verificar permisos
        if (!isStaff() && !isAdmin()) {
            header('Location: ' . URLROOT);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitizar input
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Extraemos el ID de rutina (con varias fuentes posibles)
            $routineId = $_POST['routine_id'] ?? $routineId ?? null;

            // Verificar que tenemos un ID válido
            if (!$routineId) {
                flash('exercise_message', 'ID de rutina no proporcionado', 'alert alert-danger');
                $_SESSION['toast_message'] = 'ID de rutina no proporcionado';
                $_SESSION['toast_type'] = 'error';
                header('Location: ' . URLROOT . '/staffRoutine');
                exit;
            }

            // Verificar si existe la rutina
            $routine = $this->routineModel->getRoutineById($routineId);
            if (!$routine) {
                flash('exercise_message', 'Rutina no encontrada', 'alert alert-danger');
                $_SESSION['toast_message'] = 'Rutina no encontrada';
                $_SESSION['toast_type'] = 'error';
                header('Location: ' . URLROOT . '/staffRoutine');
                exit;
            }

            // Datos para modelo - Unificamos nombres de campos para mayor compatibilidad
            $data = [
                'routine_id' => $routineId,
                'name' => trim($_POST['exercise_name'] ?? $_POST['name'] ?? ''),
                'description' => trim($_POST['exercise_description'] ?? $_POST['description'] ?? ''),
                'sets' => isset($_POST['exercise_sets']) ? (int)$_POST['exercise_sets'] : (isset($_POST['sets']) ? (int)$_POST['sets'] : 3),
                'reps' => isset($_POST['exercise_reps']) ? (int)$_POST['exercise_reps'] : (isset($_POST['reps']) ? (int)$_POST['reps'] : 12),
                'rest' => isset($_POST['exercise_rest']) ? (int)$_POST['exercise_rest'] : (isset($_POST['rest']) ? (int)$_POST['rest'] : 60),
                'order' => isset($_POST['exercise_order']) ? (int)$_POST['exercise_order'] : (isset($_POST['order']) ? (int)$_POST['order'] : 0),
                'additional_info' => isset($_POST['additional_info']) ? $_POST['additional_info'] : null
            ];

            // Registrar datos para depuración
            if (class_exists('Logger')) {
                Logger::log('DEBUG', 'Intentando añadir ejercicio: ' . json_encode($data));
            }

            // Validar entrada
            if (empty($data['name'])) {
                flash('exercise_message', 'Por favor, introduce un nombre para el ejercicio', 'alert alert-danger');
                $_SESSION['toast_message'] = 'Por favor, introduce un nombre para el ejercicio';
                $_SESSION['toast_type'] = 'error';
                header('Location: ' . URLROOT . '/staffRoutine/edit/' . $routineId);
                exit;
            }
            
            // Añadir ejercicio
            $result = $this->routineModel->addExercise($data);
            if ($result) {
                flash('exercise_message', 'Ejercicio añadido con éxito', 'alert alert-success');
                $_SESSION['toast_message'] = 'Ejercicio añadido con éxito';
                $_SESSION['toast_type'] = 'success';
                
                // Si se ha marcado "añadir otro", redirigir a la búsqueda de ejercicios
                if (isset($_POST['add_more']) && $_POST['add_more'] == 1) {
                    header('Location: ' . URLROOT . '/staffRoutine/searchExercises/' . $routineId);
                    exit;
                }
            } else {
                $errorMsg = 'Error al añadir el ejercicio';
                
                // Intentar obtener información adicional del error
                $dbError = $this->routineModel->getLastError();
                if ($dbError) {
                    $errorMsg .= ' - ' . json_encode($dbError);
                }
                
                flash('exercise_message', $errorMsg, 'alert alert-danger');
                $_SESSION['toast_message'] = $errorMsg;
                $_SESSION['toast_type'] = 'error';
                
                // Registrar error
                if (class_exists('Logger')) {
                    Logger::log('ERROR', 'Error al añadir ejercicio. Error DB: ' . json_encode($dbError));
                }
            }
            
            // Redireccionar de vuelta a la página de edición de la rutina
            header('Location: ' . URLROOT . '/staffRoutine/edit/' . $routineId);
            exit;
        } else if ($routineId) {
            // Si hay ID de rutina en la URL pero no es POST, mostrar formulario
            header('Location: ' . URLROOT . '/staffRoutine/edit/' . $routineId);
            exit;
        } else {
            // Si alguien intenta acceder directamente sin POST ni ID, redireccionar al índice
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }
    }

    // Editar un ejercicio existente
    public function editExercise($id)
    {
        // Verificar permisos
        if (!isStaff() && !isAdmin()) {
            header('Location: ' . URLROOT);
            exit;
        }

        // Obtener datos del ejercicio
        $exercise = $this->routineModel->getExerciseById($id);
        
        if (!$exercise) {
            flash('exercise_message', 'Ejercicio no encontrado', 'alert alert-danger');
            $_SESSION['toast_message'] = 'Ejercicio no encontrado';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitizar input
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Datos para modelo (compatible con nombres en inglés y español)
            $data = [
                'id' => $id,
                'name' => trim($_POST['name'] ?? $_POST['exercise_name'] ?? $_POST['nombre'] ?? $_POST['nombre_ejercicio'] ?? ''),
                'description' => trim($_POST['description'] ?? $_POST['exercise_description'] ?? $_POST['descripcion'] ?? $_POST['descripcion_ejercicio'] ?? ''),
                'sets' => isset($_POST['sets']) ? (int)$_POST['sets'] : 
                         (isset($_POST['exercise_sets']) ? (int)$_POST['exercise_sets'] : 
                         (isset($_POST['series']) ? (int)$_POST['series'] : $exercise->series)),
                'reps' => isset($_POST['reps']) ? (int)$_POST['reps'] : 
                         (isset($_POST['exercise_reps']) ? (int)$_POST['exercise_reps'] : 
                         (isset($_POST['repeticiones']) ? (int)$_POST['repeticiones'] : $exercise->repeticions)),
                'rest' => isset($_POST['rest']) ? (int)$_POST['rest'] : 
                         (isset($_POST['exercise_rest']) ? (int)$_POST['exercise_rest'] : 
                         (isset($_POST['descanso']) ? (int)$_POST['descanso'] : $exercise->descans)),
                'order' => isset($_POST['order']) ? (int)$_POST['order'] : 
                          (isset($_POST['exercise_order']) ? (int)$_POST['exercise_order'] : 
                          (isset($_POST['orden']) ? (int)$_POST['orden'] : $exercise->ordre)),
                'additional_info' => isset($_POST['additional_info']) ? $_POST['additional_info'] : 
                                    (isset($_POST['info_adicional']) ? $_POST['info_adicional'] : $exercise->info_adicional)
            ];

            // Registrar datos para depuración
            if (class_exists('Logger')) {
                Logger::log('DEBUG', 'Intentando actualizar ejercicio: ' . json_encode($data));
            }

            // Validar entrada
            if (empty($data['name'])) {
                flash('exercise_message', 'Por favor, introduce un nombre para el ejercicio', 'alert alert-danger');
                $_SESSION['toast_message'] = 'Por favor, introduce un nombre para el ejercicio';
                $_SESSION['toast_type'] = 'error';
                header('Location: ' . URLROOT . '/staffRoutine/editExercise/' . $id);
                exit;
            }

            // Actualizar ejercicio
            if ($this->routineModel->updateExercise($data)) {
                flash('exercise_message', 'Ejercicio actualizado con éxito', 'alert alert-success');
                $_SESSION['toast_message'] = 'Ejercicio actualizado con éxito';
                $_SESSION['toast_type'] = 'success';
                header('Location: ' . URLROOT . '/staffRoutine/edit/' . $exercise->rutina_id);
            } else {
                flash('exercise_message', 'Error al actualizar el ejercicio', 'alert alert-danger');
                $_SESSION['toast_message'] = 'Error al actualizar el ejercicio';
                $_SESSION['toast_type'] = 'error';
                
                // Registrar error
                if (class_exists('Logger')) {
                    Logger::log('ERROR', 'Error al actualizar ejercicio. Error DB: ' . json_encode($this->routineModel->getLastError()));
                }
                
                header('Location: ' . URLROOT . '/staffRoutine/editExercise/' . $id);
            }
            exit;
        }

        $data = [
            'exercise' => $exercise
        ];

        $this->view('staff/edit_exercise', $data);
    }

    // Eliminar un ejercicio
    public function deleteExercise($id)
    {
        // Verificar permisos
        if (!isStaff() && !isAdmin()) {
            header('Location: ' . URLROOT);
            exit;
        }

        // Verificar si existe el ejercicio
        $exercise = $this->routineModel->getExerciseById($id);
        
        if (!$exercise) {
            flash('exercise_message', 'Ejercicio no encontrado', 'alert alert-danger');
            $_SESSION['toast_message'] = 'Ejercicio no encontrado';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }

        $routineId = $exercise->rutina_id;

        if ($this->routineModel->deleteExercise($id)) {
            flash('exercise_message', 'Ejercicio eliminado con éxito', 'alert alert-success');
            $_SESSION['toast_message'] = 'Ejercicio eliminado con éxito';
            $_SESSION['toast_type'] = 'success';
        } else {
            flash('exercise_message', 'Error al eliminar el ejercicio', 'alert alert-danger');
            $_SESSION['toast_message'] = 'Error al eliminar el ejercicio';
            $_SESSION['toast_type'] = 'error';
        }

        header('Location: ' . URLROOT . '/staffRoutine/edit/' . $routineId);
        exit;
    }

    // Generar PDF de rutina
    public function generatePDF($id)
    {
        // Verificar permisos
        if (!isStaff() && !isAdmin()) {
            $_SESSION['toast_message'] = 'No tienes permisos para realizar esta acción';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT);
            exit;
        }

        // Obtener datos de la rutina
        $routine = $this->routineModel->getRoutineById($id);
        
        if (!$routine) {
            $_SESSION['toast_message'] = 'Rutina no encontrada';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }

        // Obtener ejercicios de la rutina
        $exercises = $this->routineModel->getExercisesByRoutineId($id);

        // Generar PDF con la clase PDFGenerator
        $pdfGenerator = new PDFGenerator();
        $fileName = 'rutina_' . $id . '_' . time() . '.pdf';
        $filePath = APPROOT . '/../public/uploads/routines/' . $fileName;
        
        $result = $pdfGenerator->generateRoutinePDF($routine, $exercises, $filePath);
        
        if ($result) {
            // Actualizar ruta del PDF en la base de datos
            $pdfPath = 'uploads/routines/' . $fileName;
            
            if ($this->routineModel->updateRoutinePdf($id, $pdfPath)) {
                $_SESSION['toast_message'] = 'PDF generado y guardado correctamente';
                $_SESSION['toast_type'] = 'success';
            } else {
                $_SESSION['toast_message'] = 'PDF generado pero hubo un error al guardar la ruta';
                $_SESSION['toast_type'] = 'warning';
            }
        } else {
            $_SESSION['toast_message'] = 'Error al generar el PDF';
            $_SESSION['toast_type'] = 'error';
        }
    }

    // Método para descargar PDF de rutina
    public function downloadPDF($id) 
    {
        // Verificar permisos
        if (!isStaff() && !isAdmin()) {
            $_SESSION['toast_message'] = 'No tienes permisos para realizar esta acción';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT);
            exit;
        }

        // Obtener datos de la rutina
        $routine = $this->routineModel->getRoutineById($id);
        
        if (!$routine || empty($routine->ruta_pdf)) {
            $_SESSION['toast_message'] = 'PDF no encontrado';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }

        $filePath = APPROOT . '/../public/' . $routine->ruta_pdf;
        
        if (file_exists($filePath)) {
            // Configurar cabeceras para descarga
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
        } else {
            $_SESSION['toast_message'] = 'El archivo PDF no existe en el servidor';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/staffRoutine/edit/' . $id);
            exit;
        }
    }

    // Buscar ejercicios en API externa
    public function searchExercises($routineId = null) 
    {
        // Verificar permisos
        if (!isStaff() && !isAdmin()) {
            header('Location: ' . URLROOT);
            exit;
        }
        
        // Verificar que tenemos un ID de rutina
        if (!$routineId) {
            flash('routine_message', 'ID de rutina no proporcionado', 'alert alert-danger');
            $_SESSION['toast_message'] = 'ID de rutina no proporcionado';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }
        
        // Verificar si existe la rutina
        $routine = $this->routineModel->getRoutineById($routineId);
        
        if (!$routine) {
            flash('routine_message', 'Rutina no encontrada', 'alert alert-danger');
            $_SESSION['toast_message'] = 'Rutina no encontrada';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }
        
        $exercises = [];
        $searchTerm = '';
        $muscle = '';
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Procesar búsqueda
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $searchTerm = trim($_POST['search_term'] ?? '');
            
            // Procesar el grupo muscular seleccionado (puede venir como array o como string)
            if (isset($_POST['muscles'])) {
                if (is_array($_POST['muscles']) && !empty($_POST['muscles'][0])) {
                    $muscle = $_POST['muscles'][0]; // Tomar solo el primer valor si es array
                } else if (!is_array($_POST['muscles']) && !empty($_POST['muscles'])) {
                    // Si viene como string
                    $muscle = $_POST['muscles'];
                }
            }
            
            // Si tenemos algún criterio de búsqueda, llamar a la API
            if (!empty($searchTerm) || !empty($muscle)) {
                $exercises = $this->exerciseApiService->searchExercises($searchTerm, $muscle);
            }
        }
        
        $data = [
            'title' => 'Buscar Ejercicios',
            'routine' => $routine,
            'exercises' => $exercises,
            'searchTerm' => $searchTerm,
            'muscles' => !empty($muscle) ? [$muscle] : []
        ];
        
        $this->view('staff/search_exercises', $data);
    }
    
    // Método para cargar una vista
    protected function view($view, $data = [])
    {
        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';

        // Cargar la vista
        include_once APPROOT . '/views/' . $view . '.php';

        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
}
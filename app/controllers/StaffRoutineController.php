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
            $_SESSION['toast_message'] = 'No tienes permiso para acceder a esta función';
            $_SESSION['toast_type'] = 'error';
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

    /**
     * Crea una nueva rutina
     */
    public function createRoutine() {
        // Verificar si el usuario está autorizado
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['staff', 'admin'])) {
            $_SESSION['toast_message'] = 'No tienes permiso para acceder a esta función';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Procesar el formulario
            $postData = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            // Validar los datos
            if (empty($postData['nombre'])) {
                $_SESSION['toast_message'] = 'Por favor, introduce un nombre para la rutina';
                $_SESSION['toast_type'] = 'error';
                $this->loadView('staff/create_routine', ['formData' => $postData]);
                return;
            }
            
            // Preparar los datos de la rutina con los nombres de campos correctos para el modelo
            $routineData = [
                'nom' => $postData['nombre'],
                'descripcio' => $postData['descripcion'] ?? '',
                'usuari_id' => $postData['usuari_id'] ?? null,
                'creat_el' => date('Y-m-d H:i:s')
            ];
            
            // Si no se especificó un usuario, se considera una rutina "plantilla" o para el propio staff
            if (empty($routineData['usuari_id'])) {
                $routineData['usuari_id'] = null;
            }
            
            // Registrar los datos para depuración
            if (class_exists('Logger')) {
                Logger::log('DEBUG', 'Datos enviados a addRoutine: ' . json_encode($routineData));
            }
            
            // Crear la rutina
            $newRoutineId = $this->routineModel->addRoutine($routineData);
            if ($newRoutineId) {
                $_SESSION['toast_message'] = 'Rutina creada con éxito';
                $_SESSION['toast_type'] = 'success';
                header('Location: ' . URLROOT . '/staffRoutine');
                exit;
            } else {
                $_SESSION['toast_message'] = 'Error al crear la rutina';
                $_SESSION['toast_type'] = 'error';
                // Registrar el error
                if (class_exists('Logger')) {
                    Logger::log('ERROR', 'Error al crear rutina: ' . json_encode($this->routineModel->getLastError()));
                }
                $this->loadView('staff/create_routine', ['formData' => $postData]);
                return;
            }
        } else {
            // Cargar formulario vacío
            // Obtener la lista de usuarios para el selector
            $users = $this->userModel->getAllUsers();
            
            $this->loadView('staff/create_routine', [
                'users' => $users
            ]);
        }
    }
    
    /**
     * Método alias para la creación de rutinas
     * Redirecciona al método createRoutine()
     */
    public function create() {
        // Simplemente redirigir al método createRoutine
        $this->createRoutine();
    }
    
    /**
     * Editar una rutina existente
     * @param int $id ID de la rutina a editar
     */
    public function editRoutine($id = null) {
        // Verificar si el usuario está autorizado
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['staff', 'admin'])) {
            $_SESSION['toast_message'] = 'No tienes permiso para acceder a esta función';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT);
            exit;
        }
        
        // Verificar que se proporcionó un ID
        if (!$id) {
            $_SESSION['toast_message'] = 'Rutina no encontrada';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }
        
        // Obtener la rutina
        $routine = $this->routineModel->getRoutineById($id);
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Procesar el formulario
            $postData = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            // Validar los datos
            if (empty($postData['nombre'])) {
                $_SESSION['toast_message'] = 'Por favor, introduce un nombre para la rutina';
                $_SESSION['toast_type'] = 'error';
                $this->loadView('staff/edit_routine', [
                    'routine' => $routine,
                    'formData' => $postData
                ]);
                return;
            }
            
            // Preparar los datos de la rutina
            $routineData = [
                'id' => $id,
                'nombre' => $postData['nombre'],
                'descripcion' => $postData['descripcion'] ?? '',
                'nivel' => $postData['nivel'] ?? 'Principiante',
                'objectiu' => $postData['objectiu'] ?? '',
                'usuari_id' => $postData['usuari_id'] ?? null
            ];
            
            // Actualizar la rutina
            if ($this->routineModel->updateRoutine($routineData)) {
                $_SESSION['toast_message'] = 'Rutina actualizada con éxito';
                $_SESSION['toast_type'] = 'success';
                header('Location: ' . URLROOT . '/staffRoutine');
                exit;
            } else {
                $_SESSION['toast_message'] = 'Error al actualizar la rutina';
                $_SESSION['toast_type'] = 'error';
                $this->loadView('staff/edit_routine', [
                    'routine' => $routine,
                    'formData' => $postData
                ]);
                return;
            }
        } else {
            // Verificar que la rutina exista
            if (!$routine) {
                $_SESSION['toast_message'] = 'Rutina no encontrada';
                $_SESSION['toast_type'] = 'error';
                header('Location: ' . URLROOT . '/staffRoutine');
                exit;
            }
            
            // Cargar formulario con datos de la rutina
            // Obtener los ejercicios de esta rutina
            $exercises = $this->routineModel->getExercisesByRoutineId($id);
            
            // Obtener la lista de usuarios para el selector
            $users = $this->userModel->getAllUsers();
            
            $this->loadView('staff/edit_routine', [
                'routine' => $routine,
                'exercises' => $exercises,
                'users' => $users
            ]);
        }
    }

    /**
     * Método alias para editar una rutina
     * Redirecciona al método editRoutine()
     * @param int $id ID de la rutina a editar
     */
    public function edit($id = null) {
        // Simplemente redirigir al método editRoutine
        $this->editRoutine($id);
    }
    
    /**
     * Eliminar una rutina
     * @param int $id ID de la rutina a eliminar
     */
    public function deleteRoutine($id = null) {
        // Verificar si el usuario está autorizado
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['staff', 'admin'])) {
            $_SESSION['toast_message'] = 'No tienes permiso para acceder a esta función';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT);
            exit;
        }
        
        // Verificar que se proporcionó un ID
        if (!$id) {
            $_SESSION['toast_message'] = 'Rutina no encontrada';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }
        
        // Obtener la rutina
        $routine = $this->routineModel->getRoutineById($id);
        
        // Verificar que la rutina exista y pertenezca al staff o sea un admin
        if (!$routine || ($routine->staff_id != $_SESSION['user_id'] && $_SESSION['user_role'] !== 'admin')) {
            $_SESSION['toast_message'] = 'No tienes permiso para eliminar esta rutina';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }
        
        // Eliminar la rutina
        if ($this->routineModel->deleteRoutine($id)) {
            $_SESSION['toast_message'] = 'Rutina eliminada con éxito';
            $_SESSION['toast_type'] = 'success';
        } else {
            $_SESSION['toast_message'] = 'Error al eliminar la rutina';
            $_SESSION['toast_type'] = 'error';
        }
        
        header('Location: ' . URLROOT . '/staffRoutine');
        exit;
    }
    
    /**
     * Añadir un ejercicio a una rutina
     */
    public function addExercise() {
        // Verificar si es una solicitud POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitizar los datos de entrada
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            // Obtener los datos del formulario
            $routineId = $_POST['routine_id'] ?? null;
            $name = $_POST['name'] ?? null;
            $description = $_POST['description'] ?? '';
            $sets = $_POST['sets'] ?? 3;
            $reps = $_POST['reps'] ?? 10;
            $rest = $_POST['rest'] ?? 60;
            $order = $_POST['order'] ?? 1;
            $additionalInfo = $_POST['additional_info'] ?? '';
            $addMore = isset($_POST['add_more']) ? (bool)$_POST['add_more'] : false;
            
            // Datos adicionales de la API si están disponibles
            $apiDetails = [];
            
            // Verificar si es un ejercicio de la API y guardar todos los detalles
            if (isset($_POST['api_data']) && !empty($_POST['api_data'])) {
                $apiDetails = json_decode($_POST['api_data'], true);
            }
            
            // Preparar los datos para el modelo
            $data = [
                'routine_id' => $routineId,
                'name' => $name,
                'description' => $description,
                'sets' => $sets,
                'reps' => $reps,
                'rest' => $rest,
                'order' => $order,
                'additional_info' => $additionalInfo,
                'api_details' => $apiDetails
            ];
              // Registro para debugging
            if (class_exists('Logger')) {
                Logger::log('DEBUG', 'Intentando añadir ejercicio con datos: ' . json_encode($data, JSON_PRETTY_PRINT));
            }
            
            // Añadir el ejercicio a la rutina
            $result = $this->routineModel->addExercise($data);
            
            if ($result) {
                $_SESSION['toast_message'] = 'Ejercicio añadido correctamente a la rutina';
                $_SESSION['toast_type'] = 'success';
                
                // Si se seleccionó "Continuar añadiendo ejercicios", redirigir de vuelta a la página de búsqueda
                if ($addMore) {
                    header('Location: ' . URLROOT . '/staffRoutine/searchExercises/' . $routineId);
                    exit;
                }
            } else {
                // Mejorar mensaje de error con más detalles
                $errorMsg = 'Error al añadir el ejercicio a la rutina';
                if (method_exists($this->routineModel, 'getLastError')) {
                    $lastError = $this->routineModel->getLastError();
                    if ($lastError) {
                        $errorMsg .= ': ' . $lastError;
                    }
                }
                $_SESSION['toast_message'] = $errorMsg;
                $_SESSION['toast_type'] = 'error';
                
                // Registrar el error detallado
                if (class_exists('Logger')) {
                    Logger::log('ERROR', 'Error al añadir ejercicio: ' . ($this->routineModel->getLastError() ?? 'Desconocido'));
                }
            }
            
            // Redirigir a la página de edición de la rutina si no se seleccionó "Continuar añadiendo ejercicios"
            if (!$addMore) {
                header('Location: ' . URLROOT . '/staffRoutine/edit/' . $routineId);
                exit;
            }
        } else {
            // Redirigir si no es una solicitud POST
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }
    }

    /**
     * Actualiza un ejercicio existente
     * @param int $exerciseId ID del ejercicio a actualizar
     * @param int $routineId ID de la rutina a la que pertenece el ejercicio
     */
    public function updateExercise($exerciseId = null, $routineId = null) {
        // Verificar si el usuario está autorizado
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['staff', 'admin'])) {
            $_SESSION['toast_message'] = 'No tienes permiso para acceder a esta función';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT);
            exit;
        }
        
        // Verificar que se proporcionaron los IDs necesarios
        if (!$exerciseId || !$routineId) {
            $_SESSION['toast_message'] = 'Falta información necesaria para actualizar el ejercicio';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }
        
        // Obtener la rutina
        $routine = $this->routineModel->getRoutineById($routineId);
        
        // Verificar que la rutina exista y pertenezca al staff o sea un admin
        if (!$routine || ($routine->staff_id != $_SESSION['user_id'] && $_SESSION['user_role'] !== 'admin')) {
            $_SESSION['toast_message'] = 'No tienes permiso para editar esta rutina';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Procesar el formulario
            $postData = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            // Validar los datos
            if (empty($postData['nombre'])) {
                $_SESSION['toast_message'] = 'Por favor, introduce un nombre para el ejercicio';
                $_SESSION['toast_type'] = 'error';
                header('Location: ' . URLROOT . '/staffRoutine/edit/' . $routineId);
                exit;
            }
            
            // Preparar los datos del ejercicio
            $exerciseData = [
                'id' => $exerciseId,
                'name' => $postData['nombre'],
                'description' => $postData['descripcion'] ?? '',
                'sets' => $postData['series'] ?? 0,
                'reps' => $postData['repeticiones'] ?? 0,
                'rest' => $postData['tiempo_descanso'] ?? 0,
                'order' => $postData['orden'] ?? 0,
                'additional_info' => $postData['info_adicional'] ?? ''
            ];
            
            // Actualizar el ejercicio
            if ($this->routineModel->updateExercise($exerciseData)) {
                $_SESSION['toast_message'] = 'Ejercicio actualizado con éxito';
                $_SESSION['toast_type'] = 'success';
            } else {
                $_SESSION['toast_message'] = 'Error al actualizar el ejercicio';
                $_SESSION['toast_type'] = 'error';
            }
            
            // Redireccionar de vuelta a la página de edición de rutina
            header('Location: ' . URLROOT . '/staffRoutine/edit/' . $routineId);
            exit;
        } else {
            // Si se accede directamente sin POST, redirigir a la página de edición
            header('Location: ' . URLROOT . '/staffRoutine/edit/' . $routineId);
            exit;
        }
    }

    /**
     * Buscar ejercicios para añadir a una rutina
     * @param int $routineId ID de la rutina a la que añadir ejercicios
     */
    public function searchExercises($routineId = null) {
        // Verificar si el usuario está autorizado
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['staff', 'admin'])) {
            $_SESSION['toast_message'] = 'No tienes permiso para acceder a esta función';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT);
            exit;
        }
        
        // Verificar que se proporcionó un ID de rutina
        if (!$routineId) {
            $_SESSION['toast_message'] = 'ID de rutina no proporcionado';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }
        
        // Obtener la rutina
        $routine = $this->routineModel->getRoutineById($routineId);
        
        // Verificar que la rutina exista
        if (!$routine) {
            $_SESSION['toast_message'] = 'Rutina no encontrada';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }
        
        // Variables para los resultados de búsqueda
        $exercises = [];
        $searchPerformed = false;
        $searchTerm = '';
        $muscles = [];
        $filters = [];
        
        // Obtener lista de grupos musculares disponibles para el dropdown
        $availableMuscles = $this->exerciseApiService->getMuscleGroups();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Procesar el formulario de búsqueda
            $postData = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            // Extraer términos de búsqueda y filtros
            $searchTerm = $postData['search_term'] ?? '';
            
            // Procesar el grupo muscular
            if (isset($postData['muscles']) && !empty($postData['muscles'])) {
                // Si es array, tomamos solo el primer valor (para compatibilidad)
                if (is_array($postData['muscles'])) {
                    $muscles = $postData['muscles'];
                    $muscleFilter = !empty($muscles[0]) ? $muscles[0] : '';
                } else {
                    // Si es un string, lo usamos directamente
                    $muscleFilter = $postData['muscles'];
                    $muscles = [$muscleFilter];
                }
                
                // Si hay un grupo muscular seleccionado, lo agregamos a los filtros
                if (!empty($muscleFilter)) {
                    $filters['muscle'] = $muscleFilter;
                }
            }
            
            // Registrar información detallada para depuración
            if (class_exists('Logger')) {
                Logger::log('INFO', 'Realizando búsqueda de ejercicios: Término="' . $searchTerm . '", Filtros=' . json_encode($filters));
                Logger::log('DEBUG', 'Músculos elegidos: ' . json_encode($muscles));
                Logger::log('DEBUG', 'Filtros finales: ' . json_encode($filters));
            }
            
            // Realizar la búsqueda
            $exercises = $this->exerciseApiService->searchExercises($searchTerm, $filters);
            
            // Registrar la respuesta completa para diagnóstico
            if (class_exists('Logger') && !empty($exercises)) {
                Logger::log('DEBUG', 'Respuesta de la API (primeros 2 ejercicios): ' . 
                    json_encode(array_slice($exercises, 0, 2), JSON_PRETTY_PRINT));
            }
            
            // Convertir arrays a objetos para que la vista funcione correctamente
            $exercisesObjects = [];
            foreach ($exercises as $exercise) {
                // Convertir el array asociativo a un objeto estándar
                $exerciseObj = new stdClass();
                
                // Asegurarnos de que todas las propiedades sean strings para evitar warnings con htmlspecialchars()
                $exerciseObj->name = isset($exercise['name']) ? (string)$exercise['name'] : '';
                $exerciseObj->description = isset($exercise['instructions']) ? (string)$exercise['instructions'] : '';
                $exerciseObj->difficulty = isset($exercise['difficulty']) ? (string)$exercise['difficulty'] : '';
                $exerciseObj->muscles = isset($exercise['muscle']) ? (string)$exercise['muscle'] : '';
                $exerciseObj->type = isset($exercise['type']) ? (string)$exercise['type'] : '';
                $exerciseObj->equipment = isset($exercise['equipment']) ? (string)$exercise['equipment'] : '';
                
                // Registrar cada objeto creado para depuración si está vacío
                if (empty($exerciseObj->name) && class_exists('Logger')) {
                    Logger::log('WARNING', 'Ejercicio con nombre vacío: ' . json_encode($exercise));
                }
                
                $exercisesObjects[] = $exerciseObj;
            }
            $exercises = $exercisesObjects;
            
            $searchPerformed = true;
            
            // Registrar para depuración
            if (class_exists('Logger')) {
                Logger::log('DEBUG', 'Resultados procesados: ' . count($exercises) . ' ejercicios encontrados');
                if (empty($exercises)) {
                    if ($this->exerciseApiService->getLastError()) {
                        Logger::log('ERROR', 'Error en la API: ' . $this->exerciseApiService->getLastError());
                    } else {
                        Logger::log('INFO', 'No se encontraron ejercicios con los criterios especificados');
                    }
                }
            }
        }
        
        // Preparar datos para la vista
        $data = [
            'title' => 'Buscar Ejercicios',
            'routine' => $routine,
            'exercises' => $exercises,
            'searchPerformed' => $searchPerformed,
            'searchTerm' => $searchTerm,
            'muscles' => $muscles,
            'filters' => $filters,
            'availableMuscles' => $availableMuscles
        ];
        
        // Cargar la vista
        $this->loadView('staff/search_exercises', $data);
    }
    
    /**
     * Método para buscar ejercicios en la API externa desde JavaScript
     * Responde con JSON para peticiones AJAX
     */
    public function apiSearchExercises() {
        // Verificar si el usuario está autorizado
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['staff', 'admin'])) {
            echo json_encode(['error' => 'No tienes permiso para acceder a esta función']);
            exit;
        }
        
        // Verificar que sea una petición AJAX
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            echo json_encode(['error' => 'Solo se permiten peticiones AJAX']);
            exit;
        }
        
        // Obtener los parámetros de búsqueda
        $searchTerm = $_GET['term'] ?? '';
        
        // Filtros
        $filters = [];
        if (isset($_GET['muscle']) && !empty($_GET['muscle'])) $filters['muscle'] = $_GET['muscle'];
        if (isset($_GET['equipment']) && !empty($_GET['equipment'])) $filters['equipment'] = $_GET['equipment'];
        if (isset($_GET['difficulty']) && !empty($_GET['difficulty'])) $filters['difficulty'] = $_GET['difficulty'];
        if (isset($_GET['type']) && !empty($_GET['type'])) $filters['type'] = $_GET['type'];
        
        // Realizar la búsqueda
        $exercises = $this->exerciseApiService->searchExercises($searchTerm, $filters);
        
        // Devolver los resultados en formato JSON
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'results' => $exercises,
            'count' => count($exercises)
        ]);
        exit;
    }

    /**
     * Descarga una rutina en formato PDF directamente al navegador del cliente
     * @param int $id ID de la rutina a descargar
     */
    public function downloadPDF($id = null) {
        // Verificar que el usuario esté logueado y tenga los permisos adecuados
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['toast_message'] = 'Debes iniciar sesión para descargar rutinas';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        } elseif (!in_array($_SESSION['user_role'], ['staff', 'admin'])) {
            $_SESSION['toast_message'] = 'No tienes permisos para acceder a esta función';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/users/dashboard');
            exit;
        }

        if (!$id) {
            $_SESSION['toast_message'] = 'La rutina no existe';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }

        // Obtener la rutina
        $routine = $this->routineModel->getRoutineById($id);

        // Verificar que la rutina exista
        if (!$routine) {
            $_SESSION['toast_message'] = 'La rutina no existe';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }

        // Obtener los ejercicios de la rutina
        $exercises = $this->routineModel->getExercisesByRoutine($id);
        
        // Registrar la acción en el log
        if (class_exists('Logger')) {
            Logger::log('INFO', "Usuario {$_SESSION['user_id']} ({$_SESSION['user_role']}) descargando PDF de rutina {$id}");
        }

        // Generar y descargar el PDF directamente al cliente
        $filename = 'Rutina_' . $routine->nom . '_' . date('Y-m-d') . '.pdf';
        if ($this->pdfGenerator->downloadRoutinePDF($routine, $exercises, $filename)) {
            // El PDF fue enviado al cliente, no necesitamos hacer nada más
            exit;
        } else {
            // Si hay un error, informar al usuario
            $_SESSION['toast_message'] = 'Error al generar el PDF';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }
    }

    // Método para cargar una vista
    protected function loadView($view, $data = [])
    {
        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';

        // Cargar la vista
        include_once APPROOT . '/views/' . $view . '.php';

        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
}
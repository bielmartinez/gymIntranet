<?php
/**
 * Controlador para la gestión de rutinas por parte del staff (monitores)
 */
require_once dirname(__DIR__) . '/utils/ExerciseApiService.php';
require_once dirname(__DIR__) . '/utils/PDFGenerator.php';
require_once dirname(__DIR__) . '/utils/FlashMessages.php';

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
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitizar los datos del formulario
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Procesar datos del formulario
            $data = [
                'usuari_id' => trim($_POST['usuario_id']),
                'nom' => trim($_POST['nombre']),
                'descripcio' => trim($_POST['descripcion']),
                'error' => ''
            ];

            // Validar campos obligatorios
            if (empty($data['usuari_id']) || empty($data['nom'])) {
                $data['error'] = 'Por favor, rellene todos los campos obligatorios';
                flash('routine_message', $data['error'], 'alert alert-danger');
                header('Location: ' . URLROOT . '/staffRoutine/create');
                exit;
            }

            // Guardar rutina en la base de datos
            $routineId = $this->routineModel->addRoutine($data);

            if ($routineId) {
                flash('routine_message', 'Rutina creada con éxito', 'alert alert-success');
                header('Location: ' . URLROOT . '/staffRoutine/edit/' . $routineId);
                exit;
            } else {
                flash('routine_message', 'Ha ocurrido un error al crear la rutina', 'alert alert-danger');
                header('Location: ' . URLROOT . '/staffRoutine/create');
                exit;
            }
        } else {
            // Obtener todos los usuarios para asignar la rutina
            $users = $this->userModel->getUsersByRole('user');

            $data = [
                'title' => 'Crear Rutina',
                'usuarios' => $users
            ];

            // Cargar el header
            include_once APPROOT . '/views/shared/header/main.php';

            // Cargar la vista
            include_once APPROOT . '/views/staff/create_routine.php';

            // Cargar el footer
            include_once APPROOT . '/views/shared/footer/main.php';
        }
    }

    // Editar una rutina
    public function edit($id)
    {
        $routine = $this->routineModel->getRoutineById($id);

        if (!$routine) {
            flash('routine_message', 'La rutina no existe', 'alert alert-danger');
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitizar los datos del formulario
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Procesar datos del formulario
            $data = [
                'rutina_id' => $id,
                'usuari_id' => trim($_POST['usuario_id']),
                'nom' => trim($_POST['nombre']),
                'descripcio' => trim($_POST['descripcion']),
                'error' => ''
            ];

            // Validar campos obligatorios
            if (empty($data['usuari_id']) || empty($data['nom'])) {
                $data['error'] = 'Por favor, rellene todos los campos obligatorios';
                flash('routine_message', $data['error'], 'alert alert-danger');
                header('Location: ' . URLROOT . '/staffRoutine/edit/' . $id);
                exit;
            }

            // Actualizar rutina en la base de datos
            if ($this->routineModel->updateRoutine($data)) {
                flash('routine_message', 'Rutina actualizada con éxito', 'alert alert-success');
            } else {
                flash('routine_message', 'Ha ocurrido un error al actualizar la rutina', 'alert alert-danger');
            }
            header('Location: ' . URLROOT . '/staffRoutine/edit/' . $id);
            exit;
        } else {
            // Obtener todos los usuarios para asignar la rutina
            $users = $this->userModel->getUsersByRole('user');
            $exercises = $this->routineModel->getExercisesByRoutine($id);

            $data = [
                'title' => 'Editar Rutina',
                'routine' => $routine,
                'usuarios' => $users,
                'exercises' => $exercises
            ];

            // Cargar el header
            include_once APPROOT . '/views/shared/header/main.php';

            // Cargar la vista
            include_once APPROOT . '/views/staff/edit_routine.php';

            // Cargar el footer
            include_once APPROOT . '/views/shared/footer/main.php';
        }
    }

    // Eliminar una rutina
    public function delete($id)
    {
        $routine = $this->routineModel->getRoutineById($id);

        if (!$routine) {
            flash('routine_message', 'La rutina no existe', 'alert alert-danger');
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }

        if ($this->routineModel->deleteRoutine($id)) {
            flash('routine_message', 'Rutina eliminada con éxito', 'alert alert-success');
        } else {
            flash('routine_message', 'Ha ocurrido un error al eliminar la rutina', 'alert alert-danger');
        }
        header('Location: ' . URLROOT . '/staffRoutine');
        exit;
    }

    // Añadir un ejercicio a una rutina
    public function addExercise($routineId)
    {
        $routine = $this->routineModel->getRoutineById($routineId);

        if (!$routine) {
            flash('routine_message', 'La rutina no existe', 'alert alert-danger');
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitizar los datos del formulario
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Procesar datos del formulario
            $data = [
                'rutina_id' => $routineId,
                'nom' => trim($_POST['exercise_name']),
                'descripcio' => trim($_POST['exercise_description']),
                'series' => (int) $_POST['exercise_sets'],
                'repeticions' => (int) $_POST['exercise_reps'],
                'descans' => (int) $_POST['exercise_rest'],
                'imatge_url' => trim($_POST['exercise_image']),
                'ordre' => (int) $_POST['exercise_order']
            ];

            // Validar campos obligatorios
            if (empty($data['nom'])) {
                flash('routine_message', 'El nombre del ejercicio es obligatorio', 'alert alert-danger');
                header('Location: ' . URLROOT . '/staffRoutine/edit/' . $routineId);
                exit;
            }

            // Guardar ejercicio en la base de datos
            if ($this->routineModel->addExercise($data)) {
                flash('routine_message', 'Ejercicio añadido con éxito', 'alert alert-success');
            } else {
                flash('routine_message', 'Ha ocurrido un error al añadir el ejercicio', 'alert alert-danger');
            }
        }

        header('Location: ' . URLROOT . '/staffRoutine/edit/' . $routineId);
        exit;
    }

    // Eliminar un ejercicio
    public function deleteExercise($id)
    {
        $exercise = $this->routineModel->getExerciseById($id);

        if (!$exercise) {
            flash('routine_message', 'El ejercicio no existe', 'alert alert-danger');
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }

        $routineId = $exercise->rutina_id;

        if ($this->routineModel->deleteExercise($id)) {
            flash('routine_message', 'Ejercicio eliminado con éxito', 'alert alert-success');
        } else {
            flash('routine_message', 'Ha ocurrido un error al eliminar el ejercicio', 'alert alert-danger');
        }

        header('Location: ' . URLROOT . '/staffRoutine/edit/' . $routineId);
        exit;
    }

    // Buscar ejercicios en la API externa
    public function searchExercises($routineId)
    {
        $routine = $this->routineModel->getRoutineById($routineId);

        if (!$routine) {
            flash('routine_message', 'La rutina no existe', 'alert alert-danger');
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }

        $exercises = $this->routineModel->getExercisesByRoutine($routineId);
        $apiExercises = [];
        $searchQuery = '';
        $searchType = 'muscle';  // Por defecto buscamos por grupo muscular

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitizar los datos del formulario
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $searchQuery = trim($_POST['search_query']);
            $searchType = trim($_POST['search_type']);

            if (!empty($searchQuery)) {
                // Llamada a nuestra clase de servicio de API
                switch ($searchType) {
                    case 'muscle':
                        $apiExercises = $this->exerciseApiService->searchByMuscle($searchQuery);
                        break;
                    case 'type':
                        $apiExercises = $this->exerciseApiService->searchByType($searchQuery);
                        break;
                    case 'name':
                        $apiExercises = $this->exerciseApiService->searchByName($searchQuery);
                        break;
                    case 'advanced':
                        $params = [
                            'muscle' => isset($_POST['muscle']) ? trim($_POST['muscle']) : '',
                            'type' => isset($_POST['type']) ? trim($_POST['type']) : '',
                            'name' => isset($_POST['name']) ? trim($_POST['name']) : '',
                            'difficulty' => isset($_POST['difficulty']) ? trim($_POST['difficulty']) : ''
                        ];
                        $apiExercises = $this->exerciseApiService->advancedSearch($params);
                        break;
                }

                if ($apiExercises === false) {
                    flash('routine_message', 'Error al conectar con la API de ejercicios', 'alert alert-danger');
                }
            }
        }

        $data = [
            'title' => 'Buscar Ejercicios',
            'routine' => $routine,
            'exercises' => $exercises,
            'apiExercises' => $apiExercises,
            'searchQuery' => $searchQuery,
            'searchType' => $searchType,
            'muscleGroups' => $this->exerciseApiService->getAvailableMuscleGroups(),
            'exerciseTypes' => $this->exerciseApiService->getAvailableTypes(),
            'difficulties' => $this->exerciseApiService->getAvailableDifficulties()
        ];

        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';

        // Cargar la vista
        include_once APPROOT . '/views/staff/search_exercises.php';

        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }

    // Añadir ejercicio desde la API con personalización
    public function addExerciseFromApi($routineId = null)
    {
        // If no routineId parameter is provided, check if it's in the URL or POST data
        if ($routineId === null) {
            // Check if routineId is in the URL parts (after /addExerciseFromApi/)
            $url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';
            $urlParts = explode('/', $url);
            if (isset($urlParts[2]) && is_numeric($urlParts[2])) {
                $routineId = $urlParts[2];
            }
            // If still null, check POST data
            elseif (isset($_POST['routineId'])) {
                $routineId = $_POST['routineId'];
            } else {
                // No routineId found, redirect to routines list
                flash('routine_message', 'ID de rutina no especificado', 'alert alert-danger');
                header('Location: ' . URLROOT . '/staffRoutine');
                exit;
            }
        }

        // Verificar la rutina
        $routine = $this->routineModel->getRoutineById($routineId);

        if (!$routine) {
            flash('routine_message', 'La rutina no existe', 'alert alert-danger');
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitizar datos
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Preparar datos del ejercicio
            $data = [
                'rutina_id' => $routineId,
                'nom' => trim($_POST['name']),
                'descripcio' => trim($_POST['description']),
                'series' => isset($_POST['series']) ? (int) $_POST['series'] : 3,
                'repeticions' => isset($_POST['repetitions']) ? (int) $_POST['repetitions'] : 10,
                'descans' => isset($_POST['rest']) ? (int) $_POST['rest'] : 60,
                'imatge_url' => isset($_POST['image_url']) ? trim($_POST['image_url']) : '',
                'ordre' => isset($_POST['order']) ? (int) $_POST['order'] : 1
            ];

            // Información adicional si existe
            if (isset($_POST['muscle']) || isset($_POST['equipment']) || isset($_POST['difficulty'])) {
                $additional_info = [
                    'muscle' => isset($_POST['muscle']) ? trim($_POST['muscle']) : '',
                    'equipment' => isset($_POST['equipment']) ? trim($_POST['equipment']) : '',
                    'difficulty' => isset($_POST['difficulty']) ? trim($_POST['difficulty']) : ''
                ];
                $data['info_adicional'] = json_encode($additional_info);
            }

            // Validar campos obligatorios
            if (empty($data['nom']) || empty($data['descripcio'])) {
                flash('routine_message', 'El nombre y la descripción son obligatorios', 'alert alert-danger');
                header('Location: ' . URLROOT . '/staffRoutine/searchExercises/' . $routineId);
                exit;
            }

            // Guardar ejercicio en la base de datos
            if ($this->routineModel->addExercise($data)) {
                flash('routine_message', 'Ejercicio añadido con éxito', 'alert alert-success');
                header('Location: ' . URLROOT . '/staffRoutine/edit/' . $routineId);
                exit;
            } else {
                flash('routine_message', 'Ha ocurrido un error al añadir el ejercicio', 'alert alert-danger');
                header('Location: ' . URLROOT . '/staffRoutine/searchExercises/' . $routineId);
                exit;
            }
        } else {
            // Redirect always to the searchExercises page since this method is meant to handle POST requests
            header('Location: ' . URLROOT . '/staffRoutine/searchExercises/' . $routineId);
            exit;
        }
    }

    // Generar PDF de una rutina
    public function generatePDF($id)
    {
        $routine = $this->routineModel->getRoutineById($id);

        if (!$routine) {
            flash('routine_message', 'La rutina no existe', 'alert alert-danger');
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }

        $exercises = $this->routineModel->getExercisesByRoutine($id);

        // Generar el PDF usando nuestro servicio
        $pdfPath = $this->pdfGenerator->generateRoutinePDF($routine, $exercises);

        if ($pdfPath) {
            // Actualizar la ruta del PDF en la base de datos
            if ($this->routineModel->updateRoutinePdf($id, $pdfPath)) {
                flash('routine_message', 'PDF generado con éxito', 'alert alert-success');
            } else {
                flash('routine_message', 'PDF generado pero no se pudo actualizar la ruta en la base de datos', 'alert alert-warning');
            }

            // Redirigir a la página de edición de la rutina
            header('Location: ' . URLROOT . '/staffRoutine/edit/' . $id);
            exit;
        } else {
            flash('routine_message', 'Error al generar el PDF', 'alert alert-danger');
            header('Location: ' . URLROOT . '/staffRoutine/edit/' . $id);
            exit;
        }
    }

    // Descargar PDF de una rutina
    public function downloadPDF($id)
    {
        $routine = $this->routineModel->getRoutineById($id);

        if (!$routine || empty($routine->ruta_pdf)) {
            flash('routine_message', 'El PDF no está disponible para descargar', 'alert alert-danger');
            header('Location: ' . URLROOT . '/staffRoutine/edit/' . $id);
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
            header('Location: ' . URLROOT . '/staffRoutine/edit/' . $id);
            exit;
        }
    }

    // Endpoint AJAX para buscar ejercicios en la API
    public function apiSearch()
    {
        // Verificar que es una solicitud AJAX
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            $responseData = ['error' => 'Acceso no permitido'];
            header('Content-Type: application/json');
            echo json_encode($responseData);
            exit;
        }

        // Obtener parámetros
        $type = isset($_GET['type']) ? $_GET['type'] : 'name';
        $query = isset($_GET['query']) ? $_GET['query'] : '';

        if (empty($query)) {
            $responseData = ['error' => 'Consulta vacía'];
            header('Content-Type: application/json');
            echo json_encode($responseData);
            exit;
        }

        // Realizar búsqueda según el tipo
        $results = [];
        switch ($type) {
            case 'muscle':
                $results = $this->exerciseApiService->searchByMuscle($query);
                break;
            case 'type':
                $results = $this->exerciseApiService->searchByType($query);
                break;
            case 'equipment':
                // Para equipment, usamos el campo "name" ya que la API no tiene un filtro específico para equipo
                // Podemos filtrar los resultados después
                $results = $this->exerciseApiService->searchByName($query);
                break;
            case 'name':
            default:
                $results = $this->exerciseApiService->searchByName($query);
                break;
        }

        // Si hubo un error, verificar y registrar
        if (empty($results)) {
            $error = $this->exerciseApiService->getLastError();
            error_log("API Error: " . ($error ? $error : "No specific error message"));
            // Devolvemos un error genérico
            header('Content-Type: application/json');
            echo json_encode(['error' => 'No se pudieron obtener resultados. Intente de nuevo más tarde.']);
            exit;
        }

        // Transformar los resultados añadiendo URLs de imágenes de ejemplo si no existen
        $formattedResults = [];
        foreach ($results as $exercise) {
            // Si es una búsqueda por equipo, podemos filtrar los resultados
            if ($type === 'equipment' && !empty($exercise['equipment'])) {
                if (stripos($exercise['equipment'], $query) === false) {
                    continue; // Omitir si el equipo no coincide con la búsqueda
                }
            }

            // Generar una URL de imagen de ejemplo si no hay gifUrl
            $gifUrl = $exercise['gifUrl'] ?? '';
            if (empty($gifUrl)) {
                // Asignar una imagen predeterminada basada en el músculo o tipo
                $muscle = strtolower($exercise['target'] ?? $exercise['bodyPart'] ?? 'general');
                $gifUrl = $this->getDefaultExerciseImage($muscle);
            }

            // Formatear los resultados para el frontend
            $formattedResults[] = [
                'id' => isset($exercise['id']) ? $exercise['id'] : uniqid(), // Generar un ID único si no existe
                'name' => $exercise['name'] ?? '',
                'target' => $exercise['target'] ?? '',
                'bodyPart' => $exercise['bodyPart'] ?? '',
                'equipment' => $exercise['equipment'] ?? '',
                'gifUrl' => $gifUrl,
                'instructions' => isset($exercise['instructions']) ? explode('. ', $exercise['instructions']) : []
            ];
        }

        // Devolver los resultados como JSON
        header('Content-Type: application/json');
        echo json_encode($formattedResults);
        exit;
    }

    /**
     * Obtiene una imagen predeterminada para un ejercicio basado en el grupo muscular
     * @param string $muscle Nombre del grupo muscular
     * @return string URL de la imagen predeterminada
     */
    private function getDefaultExerciseImage($muscle) {
        // Mapeo de músculos a imágenes predeterminadas
        $defaultImages = [
            'chest' => 'https://api.exercisedb.io/image/wkQvyEq1gjCBh5',
            'back' => 'https://api.exercisedb.io/image/wHo9o1RFkJV5Tl',
            'biceps' => 'https://api.exercisedb.io/image/xQXt6ViMpRpSP4',
            'triceps' => 'https://api.exercisedb.io/image/216QiVfdCzG8LT',
            'shoulders' => 'https://api.exercisedb.io/image/Db9Wo2fLaTqgVk',
            'legs' => 'https://api.exercisedb.io/image/Pvk-fQeMSQabgT',
            'abs' => 'https://api.exercisedb.io/image/68AgWiRf5m6Vt6',
            'calves' => 'https://api.exercisedb.io/image/h69fbKzE-iI0sF'
        ];
        
        // Intentar encontrar una imagen que coincida con el músculo
        foreach ($defaultImages as $key => $url) {
            if (stripos($muscle, $key) !== false) {
                return $url;
            }
        }
        
        // Si no hay coincidencia, devolver una imagen genérica
        return 'https://api.exercisedb.io/image/Db9Wo2fLaTqgVk';
    }

    // Método para añadir un ejercicio desde la API directamente
    public function addApiExercise()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }

        // Sanitizar datos del formulario
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Validar y procesar datos
        $routineId = $_POST['routine_id'] ?? '';

        if (empty($routineId)) {
            flash('routine_message', 'Debe especificar una rutina para añadir el ejercicio', 'alert alert-danger');
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }

        // Preparar los datos del ejercicio
        $data = [
            'rutina_id' => $routineId,
            'nom' => trim($_POST['exercise_name'] ?? ''),
            'descripcio' => trim($_POST['exercise_description'] ?? ''),
            'series' => (int) ($_POST['exercise_sets'] ?? 3),
            'repeticions' => (int) ($_POST['exercise_reps'] ?? 10),
            'descans' => (int) ($_POST['exercise_rest'] ?? 60),
            'imatge_url' => trim($_POST['exercise_image'] ?? ''),
            'ordre' => (int) ($_POST['exercise_order'] ?? 1)
        ];

        // Validar campo obligatorio
        if (empty($data['nom'])) {
            flash('routine_message', 'El nombre del ejercicio es obligatorio', 'alert alert-danger');
            header('Location: ' . URLROOT . '/staffRoutine/edit/' . $routineId);
            exit;
        }

        // Guardar ejercicio en la base de datos
        if ($this->routineModel->addExercise($data)) {
            flash('routine_message', 'Ejercicio añadido con éxito a la rutina', 'alert alert-success');
        } else {
            flash('routine_message', 'Ha ocurrido un error al añadir el ejercicio', 'alert alert-danger');
        }

        // Redireccionar a la página de edición de rutina
        header('Location: ' . URLROOT . '/staffRoutine/edit/' . $routineId);
        exit;
    }

    // Añadir ejercicio directamente desde la API (sin personalización)
    public function addDirectFromApi($routineId)
    {
        // Verificar la rutina
        $routine = $this->routineModel->getRoutineById($routineId);

        if (!$routine) {
            flash('routine_message', 'La rutina no existe', 'alert alert-danger');
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitizar datos
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Preparar datos del ejercicio
            $data = [
                'rutina_id' => $routineId,
                'nom' => trim($_POST['name']),
                'descripcio' => trim($_POST['description']),
                'series' => 3, // Valor por defecto
                'repeticions' => 10, // Valor por defecto
                'descans' => 60, // Valor por defecto en segundos
                'imatge_url' => isset($_POST['image_url']) ? trim($_POST['image_url']) : '',
                'ordre' => $this->routineModel->getNextExerciseOrder($routineId)
            ];

            // Información adicional si existe
            if (isset($_POST['muscle']) || isset($_POST['equipment']) || isset($_POST['difficulty'])) {
                $additional_info = [
                    'muscle' => isset($_POST['muscle']) ? trim($_POST['muscle']) : '',
                    'equipment' => isset($_POST['equipment']) ? trim($_POST['equipment']) : '',
                    'difficulty' => isset($_POST['difficulty']) ? trim($_POST['difficulty']) : ''
                ];
                $data['info_adicional'] = json_encode($additional_info);
            }

            // Guardar ejercicio en la base de datos
            if ($this->routineModel->addExercise($data)) {
                flash('routine_message', 'Ejercicio añadido con éxito', 'alert alert-success');
                header('Location: ' . URLROOT . '/staffRoutine/searchExercises/' . $routineId);
                exit;
            } else {
                flash('routine_message', 'Ha ocurrido un error al añadir el ejercicio', 'alert alert-danger');
                header('Location: ' . URLROOT . '/staffRoutine/searchExercises/' . $routineId);
                exit;
            }
        } else {
            header('Location: ' . URLROOT . '/staffRoutine/searchExercises/' . $routineId);
            exit;
        }
    }

    // Editar un ejercicio existente
    public function editExercise($exerciseId = null)
    {
        if ($exerciseId === null) {
            flash('routine_message', 'ID de ejercicio no especificado', 'alert alert-danger');
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }

        // Obtener el ejercicio
        $exercise = $this->routineModel->getExerciseById($exerciseId);

        if (!$exercise) {
            flash('routine_message', 'El ejercicio no existe', 'alert alert-danger');
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }

        // Obtener la rutina a la que pertenece el ejercicio
        $routineId = $exercise->rutina_id;
        $routine = $this->routineModel->getRoutineById($routineId);

        if (!$routine) {
            flash('routine_message', 'La rutina no existe', 'alert alert-danger');
            header('Location: ' . URLROOT . '/staffRoutine');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitizar los datos del formulario
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Procesar datos del formulario
            $data = [
                'exercici_id' => $exerciseId,
                'nom' => trim($_POST['exercise_name']),
                'descripcio' => trim($_POST['exercise_description']),
                'series' => (int) $_POST['exercise_sets'],
                'repeticions' => (int) $_POST['exercise_reps'],
                'descans' => (int) $_POST['exercise_rest']
            ];

            // Campos opcionales
            if (isset($_POST['exercise_image']) && !empty($_POST['exercise_image'])) {
                $data['imatge_url'] = trim($_POST['exercise_image']);
            }

            if (isset($_POST['exercise_order']) && !empty($_POST['exercise_order'])) {
                $data['ordre'] = (int) $_POST['exercise_order'];
            }

            // Información adicional si existe
            if (
                (isset($_POST['muscle']) && !empty($_POST['muscle'])) ||
                (isset($_POST['equipment']) && !empty($_POST['equipment'])) ||
                (isset($_POST['difficulty']) && !empty($_POST['difficulty']))
            ) {
                $additional_info = [
                    'muscle' => isset($_POST['muscle']) ? trim($_POST['muscle']) : '',
                    'equipment' => isset($_POST['equipment']) ? trim($_POST['equipment']) : '',
                    'difficulty' => isset($_POST['difficulty']) ? trim($_POST['difficulty']) : ''
                ];
                $data['info_adicional'] = json_encode($additional_info);
            }

            // Validar campos obligatorios
            if (empty($data['nom'])) {
                flash('routine_message', 'El nombre del ejercicio es obligatorio', 'alert alert-danger');
                header('Location: ' . URLROOT . '/staffRoutine/editExercise/' . $exerciseId);
                exit;
            }

            // Actualizar ejercicio en la base de datos
            if ($this->routineModel->updateExercise($data)) {
                flash('routine_message', 'Ejercicio actualizado con éxito', 'alert alert-success');
                header('Location: ' . URLROOT . '/staffRoutine/edit/' . $routineId);
                exit;
            } else {
                flash('routine_message', 'Ha ocurrido un error al actualizar el ejercicio', 'alert alert-danger');
                header('Location: ' . URLROOT . '/staffRoutine/editExercise/' . $exerciseId);
                exit;
            }
        } else {
            // Preparar información adicional si existe
            $additionalInfo = null;
            if (!empty($exercise->info_adicional)) {
                $additionalInfo = json_decode($exercise->info_adicional, true);
            }

            $data = [
                'title' => 'Editar Ejercicio',
                'routine' => $routine,
                'exercise' => $exercise,
                'additionalInfo' => $additionalInfo
            ];

            // Cargar el header
            include_once APPROOT . '/views/shared/header/main.php';

            // Cargar la vista
            include_once APPROOT . '/views/staff/edit_exercise.php';

            // Cargar el footer
            include_once APPROOT . '/views/shared/footer/main.php';
        }
    }
}
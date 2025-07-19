<?php
/**
 * Encuesta Controller
 * Handles surveys and responses
 */

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/Encuesta.php';
require_once APP_PATH . '/models/Egresado.php';

class EncuestaController extends Controller {
    private $encuestaModel;
    private $egresadoModel;
    
    public function __construct() {
        parent::__construct();
        $this->encuestaModel = new Encuesta();
        $this->egresadoModel = new Egresado();
    }
    
    /**
     * List available surveys
     */
    public function index() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $egresado = $this->egresadoModel->findByUserId($user['id']);
        
        if (!$egresado) {
            $this->redirect('/dashboard');
        }
        
        $encuestas = $this->encuestaModel->getResponsesByGraduate($egresado['dni']);
        
        $this->view('encuesta/index', [
            'encuestas' => $encuestas,
            'egresado' => $egresado
        ]);
    }
    
    /**
     * Show survey form
     */
    public function show($id) {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $egresado = $this->egresadoModel->findByUserId($user['id']);
        
        if (!$egresado) {
            $this->redirect('/dashboard');
        }
        
        $encuesta = $this->encuestaModel->find($id);
        
        if (!$encuesta || $encuesta['estado'] !== 'activa') {
            $this->redirect('/encuestas');
        }
        
        // Check if already responded
        $hasResponded = $this->encuestaModel->hasResponded($egresado['dni'], $id);
        
        $this->view('encuesta/show', [
            'encuesta' => $encuesta,
            'egresado' => $egresado,
            'has_responded' => $hasResponded,
            'csrf_token' => $this->generateCsrf()
        ]);
    }
    
    /**
     * Submit survey response
     */
    public function respond() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/encuestas');
        }
        
        $user = $this->getCurrentUser();
        $egresado = $this->egresadoModel->findByUserId($user['id']);
        
        if (!$egresado) {
            $this->redirect('/dashboard');
        }
        
        $csrf_token = $_POST['csrf_token'] ?? '';
        $encuesta_id = $_POST['encuesta_id'] ?? '';
        $respuesta = $_POST['respuesta'] ?? '';
        
        // Validate CSRF token
        if (!$this->validateCsrf($csrf_token)) {
            $this->redirect('/encuestas');
        }
        
        if (empty($encuesta_id) || empty($respuesta)) {
            $this->redirect('/encuestas');
        }
        
        $encuesta = $this->encuestaModel->find($encuesta_id);
        
        if (!$encuesta || $encuesta['estado'] !== 'activa') {
            $this->redirect('/encuestas');
        }
        
        try {
            $this->encuestaModel->saveResponse($egresado['dni'], $encuesta_id, $respuesta);
            $this->redirect('/encuestas');
            
        } catch (Exception $e) {
            $this->redirect('/encuestas/' . $encuesta_id);
        }
    }
    
    /**
     * List all surveys (admin only)
     */
    public function list() {
        $this->requireRole('admin');
        
        $encuestas = $this->encuestaModel->findAll();
        
        $this->view('admin/encuestas/list', [
            'encuestas' => $encuestas
        ]);
    }
    
    /**
     * Show survey statistics (admin only)
     */
    public function statistics($id) {
        $this->requireRole('admin');
        
        $encuesta = $this->encuestaModel->find($id);
        
        if (!$encuesta) {
            $this->redirect('/admin/encuestas');
        }
        
        $statistics = $this->encuestaModel->getStatistics($id);
        $responses = $this->encuestaModel->getAllResponses($id);
        
        $this->view('admin/encuestas/statistics', [
            'encuesta' => $encuesta,
            'statistics' => $statistics,
            'responses' => $responses
        ]);
    }
    
    /**
     * Create new survey (admin only)
     */
    public function create() {
        $this->requireRole('admin');
        
        $this->view('admin/encuestas/create', [
            'csrf_token' => $this->generateCsrf()
        ]);
    }
    
    /**
     * Store new survey (admin only)
     */
    public function store() {
        $this->requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/encuestas/create');
        }
        
        $csrf_token = $_POST['csrf_token'] ?? '';
        
        // Validate CSRF token
        if (!$this->validateCsrf($csrf_token)) {
            $this->redirect('/admin/encuestas/create');
        }
        
        $pregunta = $_POST['pregunta'] ?? '';
        $tipo_respuesta = $_POST['tipo_respuesta'] ?? '';
        $opciones = $_POST['opciones'] ?? '';
        $orden_pregunta = $_POST['orden_pregunta'] ?? 0;
        
        // Validate input
        $errors = [];
        
        if (empty($pregunta)) {
            $errors[] = 'Pregunta es requerida';
        }
        
        if (!in_array($tipo_respuesta, ['texto', 'opcion_multiple', 'escala', 'si_no'])) {
            $errors[] = 'Tipo de respuesta inválido';
        }
        
        if ($tipo_respuesta === 'opcion_multiple' && empty($opciones)) {
            $errors[] = 'Opciones son requeridas para respuesta múltiple';
        }
        
        if (!empty($errors)) {
            $this->view('admin/encuestas/create', [
                'errors' => $errors,
                'csrf_token' => $this->generateCsrf(),
                'formData' => $_POST
            ]);
            return;
        }
        
        try {
            $opcionesJson = null;
            if ($tipo_respuesta === 'opcion_multiple') {
                $opcionesArray = array_filter(array_map('trim', explode("\n", $opciones)));
                $opcionesJson = json_encode($opcionesArray);
            } elseif ($tipo_respuesta === 'escala') {
                $opcionesJson = json_encode([
                    'min' => 1,
                    'max' => 5,
                    'labels' => ['Muy malo', 'Malo', 'Regular', 'Bueno', 'Muy bueno']
                ]);
            }
            
            $encuestaData = [
                'pregunta' => $pregunta,
                'tipo_respuesta' => $tipo_respuesta,
                'opciones' => $opcionesJson,
                'orden_pregunta' => $orden_pregunta
            ];
            
            $this->encuestaModel->create($encuestaData);
            
            $this->redirect('/admin/encuestas');
            
        } catch (Exception $e) {
            $this->view('admin/encuestas/create', [
                'error' => 'Error al crear la encuesta',
                'csrf_token' => $this->generateCsrf(),
                'formData' => $_POST
            ]);
        }
    }
    
    /**
     * Toggle survey status (admin only)
     */
    public function toggleStatus() {
        $this->requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
            return;
        }
        
        $encuestaId = $_POST['encuesta_id'] ?? '';
        
        if (empty($encuestaId)) {
            $this->json(['error' => 'ID de encuesta requerido'], 400);
            return;
        }
        
        try {
            $this->encuestaModel->toggleStatus($encuestaId);
            $this->json(['success' => true]);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al actualizar'], 500);
        }
    }
}
?>
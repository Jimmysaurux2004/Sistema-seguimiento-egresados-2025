<?php
/**
 * Tutoria Controller
 * Handles tutoring session management
 */

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/Tutoria.php';
require_once APP_PATH . '/models/Egresado.php';
require_once APP_PATH . '/models/Notificacion.php';

class TutoriaController extends Controller {
    private $tutoriaModel;
    private $egresadoModel;
    private $notificacionModel;
    
    public function __construct() {
        parent::__construct();
        $this->tutoriaModel = new Tutoria();
        $this->egresadoModel = new Egresado();
        $this->notificacionModel = new Notificacion();
    }
    
    /**
     * List tutoring sessions
     */
    public function index() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        
        if ($user['role'] === 'admin') {
            $tutorias = $this->tutoriaModel->findAll();
        } else {
            $egresado = $this->egresadoModel->findByUserId($user['id']);
            if (!$egresado) {
                $this->redirect('/dashboard');
            }
            $tutorias = $this->tutoriaModel->getByGraduate($egresado['dni']);
        }
        
        $this->view('tutoria/index', [
            'tutorias' => $tutorias,
            'user_role' => $user['role']
        ]);
    }
    
    /**
     * Show create form
     */
    public function create() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        if ($user['role'] === 'admin') {
            $this->redirect('/admin/tutorias');
        }
        
        $egresado = $this->egresadoModel->findByUserId($user['id']);
        if (!$egresado) {
            $this->redirect('/dashboard');
        }
        
        // Get available teachers (this would normally come from a teachers table)
        $teachers = [
            'Dr. Juan Pérez',
            'Dra. María García',
            'Mg. Carlos López',
            'Ing. Ana Rodríguez',
            'Dr. Luis Martínez'
        ];
        
        $this->view('tutoria/create', [
            'egresado' => $egresado,
            'teachers' => $teachers,
            'csrf_token' => $this->generateCsrf()
        ]);
    }
    
    /**
     * Store new tutoring session
     */
    public function store() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/tutorias/create');
        }
        
        $user = $this->getCurrentUser();
        $egresado = $this->egresadoModel->findByUserId($user['id']);
        
        if (!$egresado) {
            $this->redirect('/dashboard');
        }
        
        $csrf_token = $_POST['csrf_token'] ?? '';
        
        // Validate CSRF token
        if (!$this->validateCsrf($csrf_token)) {
            $this->redirect('/tutorias/create');
        }
        
        $docente = $_POST['docente'] ?? '';
        $fecha = $_POST['fecha'] ?? '';
        $hora = $_POST['hora'] ?? '';
        $motivo = $_POST['motivo'] ?? '';
        
        // Validate input
        $errors = [];
        
        if (empty($docente)) {
            $errors[] = 'Docente es requerido';
        }
        
        if (empty($fecha)) {
            $errors[] = 'Fecha es requerida';
        } elseif (strtotime($fecha) < strtotime(date('Y-m-d'))) {
            $errors[] = 'La fecha no puede ser anterior a hoy';
        }
        
        if (empty($hora)) {
            $errors[] = 'Hora es requerida';
        }
        
        if (empty($motivo)) {
            $errors[] = 'Motivo es requerido';
        }
        
        // Check if time slot is available
        if (!empty($docente) && !empty($fecha) && !empty($hora)) {
            if (!$this->tutoriaModel->isTimeSlotAvailable($docente, $fecha, $hora)) {
                $errors[] = 'El horario seleccionado no está disponible';
            }
        }
        
        if (!empty($errors)) {
            $teachers = [
                'Dr. Juan Pérez',
                'Dra. María García',
                'Mg. Carlos López',
                'Ing. Ana Rodríguez',
                'Dr. Luis Martínez'
            ];
            
            $this->view('tutoria/create', [
                'egresado' => $egresado,
                'teachers' => $teachers,
                'errors' => $errors,
                'csrf_token' => $this->generateCsrf(),
                'formData' => $_POST
            ]);
            return;
        }
        
        try {
            $tutoriaData = [
                'egresado_dni' => $egresado['dni'],
                'docente' => $docente,
                'fecha' => $fecha,
                'hora' => $hora,
                'motivo' => $motivo
            ];
            
            $tutoriaId = $this->tutoriaModel->create($tutoriaData);
            
            // Create notification for admins
            $this->notificacionModel->createForAllUsers(
                'Nueva solicitud de tutoría',
                "Solicitud de tutoría de {$egresado['nombres']} {$egresado['apellidos']} para el $fecha a las $hora",
                'tutoria'
            );
            
            $this->redirect('/tutorias');
            
        } catch (Exception $e) {
            $this->redirect('/tutorias/create');
        }
    }
    
    /**
     * Update tutoring session status (admin only)
     */
    public function updateStatus() {
        $this->requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
            return;
        }
        
        $tutoriaId = $_POST['tutoria_id'] ?? '';
        $status = $_POST['status'] ?? '';
        $notas = $_POST['notas'] ?? '';
        
        if (empty($tutoriaId) || empty($status)) {
            $this->json(['error' => 'Datos requeridos'], 400);
            return;
        }
        
        if (!in_array($status, ['pendiente', 'confirmada', 'completada', 'cancelada'])) {
            $this->json(['error' => 'Estado inválido'], 400);
            return;
        }
        
        try {
            $this->tutoriaModel->updateStatus($tutoriaId, $status, $notas);
            
            // Get tutoring session details for notification
            $tutoria = $this->tutoriaModel->find($tutoriaId);
            if ($tutoria) {
                $egresado = $this->egresadoModel->findByDni($tutoria['egresado_dni']);
                if ($egresado && $egresado['usuario_id']) {
                    $this->notificacionModel->createNotification(
                        $egresado['usuario_id'],
                        'Estado de tutoría actualizado',
                        "Su tutoría del {$tutoria['fecha']} ha sido {$status}",
                        'tutoria'
                    );
                }
            }
            
            $this->json(['success' => true]);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al actualizar'], 500);
        }
    }
    
    /**
     * Calendar view
     */
    public function calendar() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $tutorias = [];
        
        if ($user['role'] === 'admin') {
            $tutorias = $this->tutoriaModel->getByDateRange(
                date('Y-m-01'),
                date('Y-m-t')
            );
        } else {
            $egresado = $this->egresadoModel->findByUserId($user['id']);
            if ($egresado) {
                $tutorias = $this->tutoriaModel->getByGraduate($egresado['dni']);
            }
        }
        
        $this->view('tutoria/calendar', [
            'tutorias' => $tutorias,
            'user_role' => $user['role']
        ]);
    }
}
?>
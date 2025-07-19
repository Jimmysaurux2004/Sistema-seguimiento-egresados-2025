<?php
/**
 * Egresado Controller
 * Handles graduate profile management
 */

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/Egresado.php';
require_once APP_PATH . '/models/Usuario.php';

class EgresadoController extends Controller {
    private $egresadoModel;
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->egresadoModel = new Egresado();
        $this->userModel = new Usuario();
    }
    
    /**
     * Show profile page
     */
    public function profile() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $egresado = $this->egresadoModel->findByUserId($user['id']);
        
        if (!$egresado) {
            $this->redirect('/dashboard');
        }
        
        $this->view('egresado/profile', [
            'egresado' => $egresado,
            'csrf_token' => $this->generateCsrf()
        ]);
    }
    
    /**
     * Update profile
     */
    public function updateProfile() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
        }
        
        $user = $this->getCurrentUser();
        $egresado = $this->egresadoModel->findByUserId($user['id']);
        
        if (!$egresado) {
            $this->redirect('/dashboard');
        }
        
        $csrf_token = $_POST['csrf_token'] ?? '';
        
        // Validate CSRF token
        if (!$this->validateCsrf($csrf_token)) {
            $this->view('egresado/profile', [
                'egresado' => $egresado,
                'error' => 'Token de seguridad inválido',
                'csrf_token' => $this->generateCsrf()
            ]);
            return;
        }
        
        $telefono = $_POST['telefono'] ?? '';
        $situacion_laboral = $_POST['situacion_laboral'] ?? '';
        $empresa_actual = $_POST['empresa_actual'] ?? '';
        $cargo_actual = $_POST['cargo_actual'] ?? '';
        
        // Validate input
        $errors = [];
        
        if (!empty($telefono) && !preg_match('/^[0-9+\-\s()]+$/', $telefono)) {
            $errors[] = 'Formato de teléfono inválido';
        }
        
        if (!in_array($situacion_laboral, ['empleado', 'desempleado', 'estudiando', 'emprendedor'])) {
            $errors[] = 'Situación laboral inválida';
        }
        
        if (!empty($errors)) {
            $this->view('egresado/profile', [
                'egresado' => $egresado,
                'errors' => $errors,
                'csrf_token' => $this->generateCsrf()
            ]);
            return;
        }
        
        try {
            // Update employment status
            $this->egresadoModel->updateEmploymentStatus(
                $egresado['dni'],
                $situacion_laboral,
                $empresa_actual,
                $cargo_actual
            );
            
            // Update phone if provided
            if (!empty($telefono)) {
                $this->egresadoModel->update($egresado['dni'], ['telefono' => $telefono]);
            }
            
            $this->view('egresado/profile', [
                'egresado' => $this->egresadoModel->findByDni($egresado['dni']),
                'success' => 'Perfil actualizado correctamente',
                'csrf_token' => $this->generateCsrf()
            ]);
            
        } catch (Exception $e) {
            $this->view('egresado/profile', [
                'egresado' => $egresado,
                'error' => 'Error al actualizar el perfil',
                'csrf_token' => $this->generateCsrf()
            ]);
        }
    }
    
    /**
     * List all graduates (admin only)
     */
    public function list() {
        $this->requireRole('admin');
        
        $search = $_GET['search'] ?? '';
        $egresados = [];
        
        if (!empty($search)) {
            $egresados = $this->egresadoModel->search($search);
        } else {
            $egresados = $this->egresadoModel->getAllWithUser();
        }
        
        $this->view('admin/egresados/list', [
            'egresados' => $egresados,
            'search' => $search
        ]);
    }
    
    /**
     * Show graduate details (admin only)
     */
    public function show($dni) {
        $this->requireRole('admin');
        
        $egresado = $this->egresadoModel->findByDni($dni);
        
        if (!$egresado) {
            $this->redirect('/admin/egresados');
        }
        
        $this->view('admin/egresados/show', [
            'egresado' => $egresado
        ]);
    }
    
    /**
     * Show create form (admin only)
     */
    public function create() {
        $this->requireRole('admin');
        
        $this->view('admin/egresados/create', [
            'csrf_token' => $this->generateCsrf()
        ]);
    }
    
    /**
     * Store new graduate (admin only)
     */
    public function store() {
        $this->requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/egresados/create');
        }
        
        $csrf_token = $_POST['csrf_token'] ?? '';
        
        // Validate CSRF token
        if (!$this->validateCsrf($csrf_token)) {
            $this->view('admin/egresados/create', [
                'error' => 'Token de seguridad inválido',
                'csrf_token' => $this->generateCsrf()
            ]);
            return;
        }
        
        $dni = $_POST['dni'] ?? '';
        $nombres = $_POST['nombres'] ?? '';
        $apellidos = $_POST['apellidos'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $carrera = $_POST['carrera'] ?? '';
        $anio_egreso = $_POST['anio_egreso'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validate input
        $errors = [];
        
        if (empty($dni)) {
            $errors[] = 'DNI es requerido';
        }
        
        if (empty($nombres) || empty($apellidos)) {
            $errors[] = 'Nombres y apellidos son requeridos';
        }
        
        if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email válido es requerido';
        }
        
        if (empty($carrera)) {
            $errors[] = 'Carrera es requerida';
        }
        
        if (empty($anio_egreso) || $anio_egreso < 1990 || $anio_egreso > date('Y')) {
            $errors[] = 'Año de egreso válido es requerido';
        }
        
        if (empty($password) || strlen($password) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        // Check if DNI or email already exists
        if ($this->egresadoModel->findByDni($dni)) {
            $errors[] = 'El DNI ya está registrado';
        }
        
        if ($this->userModel->findByEmail($correo)) {
            $errors[] = 'El email ya está registrado';
        }
        
        if (!empty($errors)) {
            $this->view('admin/egresados/create', [
                'errors' => $errors,
                'csrf_token' => $this->generateCsrf(),
                'formData' => $_POST
            ]);
            return;
        }
        
        try {
            // Create user account
            $userId = $this->userModel->createUser($correo, $password, 'egresado');
            
            // Create graduate profile
            $egresadoData = [
                'dni' => $dni,
                'nombres' => $nombres,
                'apellidos' => $apellidos,
                'correo' => $correo,
                'telefono' => $telefono,
                'carrera' => $carrera,
                'anio_egreso' => $anio_egreso,
                'usuario_id' => $userId
            ];
            
            $this->egresadoModel->create($egresadoData);
            
            $this->redirect('/admin/egresados');
            
        } catch (Exception $e) {
            $this->view('admin/egresados/create', [
                'error' => 'Error al crear el egresado',
                'csrf_token' => $this->generateCsrf(),
                'formData' => $_POST
            ]);
        }
    }
}
?>
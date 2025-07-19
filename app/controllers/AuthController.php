<?php
/**
 * Authentication Controller
 * Handles user login, logout, and registration
 */

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/Usuario.php';
require_once APP_PATH . '/models/Egresado.php';

class AuthController extends Controller {
    private $userModel;
    private $egresadoModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new Usuario();
        $this->egresadoModel = new Egresado();
    }
    
    /**
     * Show login page
     */
    public function showLogin() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }
        
        $this->view('auth/login', [
            'csrf_token' => $this->generateCsrf()
        ]);
    }
    
    /**
     * Process login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $csrf_token = $_POST['csrf_token'] ?? '';
        
        // Validate CSRF token
        if (!$this->validateCsrf($csrf_token)) {
            $this->view('auth/login', [
                'error' => 'Token de seguridad inválido',
                'csrf_token' => $this->generateCsrf()
            ]);
            return;
        }
        
        // Validate input
        if (empty($email) || empty($password)) {
            $this->view('auth/login', [
                'error' => 'Email y contraseña son requeridos',
                'csrf_token' => $this->generateCsrf()
            ]);
            return;
        }
        
        // Find user by email
        $user = $this->userModel->findByEmail($email);
        
        if (!$user || !$this->userModel->verifyPassword($password, $user['password_hash'])) {
            $this->view('auth/login', [
                'error' => 'Credenciales inválidas',
                'csrf_token' => $this->generateCsrf()
            ]);
            return;
        }
        
        // Set session data
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['rol'];
        
        // Update last access
        $this->userModel->updateLastAccess($user['id']);
        
        // Redirect based on role
        if ($user['rol'] === 'admin') {
            $this->redirect('/admin/dashboard');
        } else {
            $this->redirect('/dashboard');
        }
    }
    
    /**
     * Show registration page
     */
    public function showRegister() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }
        
        $this->view('auth/register', [
            'csrf_token' => $this->generateCsrf()
        ]);
    }
    
    /**
     * Process registration
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/register');
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $dni = $_POST['dni'] ?? '';
        $nombres = $_POST['nombres'] ?? '';
        $apellidos = $_POST['apellidos'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $carrera = $_POST['carrera'] ?? '';
        $anio_egreso = $_POST['anio_egreso'] ?? '';
        $csrf_token = $_POST['csrf_token'] ?? '';
        
        // Validate CSRF token
        if (!$this->validateCsrf($csrf_token)) {
            $this->view('auth/register', [
                'error' => 'Token de seguridad inválido',
                'csrf_token' => $this->generateCsrf()
            ]);
            return;
        }
        
        // Validate input
        $errors = [];
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email válido es requerido';
        }
        
        if (empty($password) || strlen($password) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Las contraseñas no coinciden';
        }
        
        if (empty($dni)) {
            $errors[] = 'DNI es requerido';
        }
        
        if (empty($nombres) || empty($apellidos)) {
            $errors[] = 'Nombres y apellidos son requeridos';
        }
        
        if (empty($carrera)) {
            $errors[] = 'Carrera es requerida';
        }
        
        if (empty($anio_egreso) || $anio_egreso < 1990 || $anio_egreso > date('Y')) {
            $errors[] = 'Año de egreso válido es requerido';
        }
        
        // Check if email already exists
        if ($this->userModel->findByEmail($email)) {
            $errors[] = 'El email ya está registrado';
        }
        
        // Check if DNI already exists
        if ($this->egresadoModel->findByDni($dni)) {
            $errors[] = 'El DNI ya está registrado';
        }
        
        if (!empty($errors)) {
            $this->view('auth/register', [
                'errors' => $errors,
                'csrf_token' => $this->generateCsrf(),
                'formData' => $_POST
            ]);
            return;
        }
        
        try {
            // Create user account
            $userId = $this->userModel->createUser($email, $password, 'egresado');
            
            // Create graduate profile
            $egresadoData = [
                'dni' => $dni,
                'nombres' => $nombres,
                'apellidos' => $apellidos,
                'correo' => $email,
                'telefono' => $telefono,
                'carrera' => $carrera,
                'anio_egreso' => $anio_egreso,
                'usuario_id' => $userId
            ];
            
            $this->egresadoModel->create($egresadoData);
            
            // Auto-login user
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 'egresado';
            
            $this->redirect('/dashboard');
            
        } catch (Exception $e) {
            $this->view('auth/register', [
                'error' => 'Error al crear la cuenta. Inténtelo nuevamente.',
                'csrf_token' => $this->generateCsrf(),
                'formData' => $_POST
            ]);
        }
    }
    
    /**
     * Logout user
     */
    public function logout() {
        session_destroy();
        $this->redirect('/login');
    }
}
?>
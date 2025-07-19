<?php

/**
 * Egresado Controller
 * Handles graduate profile management
 */

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/Egresado.php';
require_once APP_PATH . '/models/Usuario.php';

class EgresadoController extends Controller
{
    private $egresadoModel;
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->egresadoModel = new Egresado();
        $this->userModel = new Usuario();
    }

    /**
     * Show profile page
     */
    public function profile()
    {
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
    public function updateProfile()
    {
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
    public function list()
    {
        $this->requireRole('admin');

        $search = $_GET['search'] ?? '';
        $carrera = $_GET['carrera'] ?? '';
        $situacion_laboral = $_GET['situacion_laboral'] ?? '';
        $egresados = [];

        // Get filtered results
        $egresados = $this->egresadoModel->getFilteredEgresados($search, $carrera, $situacion_laboral);

        // Get statistics
        $stats = $this->egresadoModel->getStats();

        $this->view('admin/egresados/list', [
            'egresados' => $egresados,
            'search' => $search,
            'carrera' => $carrera,
            'situacion_laboral' => $situacion_laboral,
            'stats' => $stats
        ]);
    }

    /**
     * Show graduate details (admin only)
     */
    public function show($dni)
    {
        $this->requireRole('admin');

        $egresado = $this->egresadoModel->findByDni($dni);

        if (!$egresado) {
            $this->redirect('/admin/egresados');
        }

        $this->view('admin/egresados/show', [
            'egresado' => $egresado
        ]);
    }

    // Métodos de creación de egresados removidos
}

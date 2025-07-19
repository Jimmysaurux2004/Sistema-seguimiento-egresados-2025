<?php
/**
 * Base controller class
 * Provides common functionality for all controllers
 */

class Controller {
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Render view with data
     */
    protected function view($viewName, $data = []) {
        extract($data);
        $viewFile = VIEW_PATH . '/' . $viewName . '.php';
        
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            throw new Exception("View not found: " . $viewName);
        }
    }
    
    /**
     * Return JSON response
     */
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Redirect to URL
     */
    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Check if user is authenticated
     */
    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
    }
    
    /**
     * Check if user has required role
     */
    protected function requireRole($role) {
        $this->requireAuth();
        if ($_SESSION['user_role'] !== $role) {
            http_response_code(403);
            $this->view('errors/403');
            exit;
        }
    }
    
    /**
     * Get current user
     */
    protected function getCurrentUser() {
        if (isset($_SESSION['user_id'])) {
            return [
                'id' => $_SESSION['user_id'],
                'email' => $_SESSION['user_email'],
                'role' => $_SESSION['user_role']
            ];
        }
        return null;
    }
    
    /**
     * Validate CSRF token
     */
    protected function validateCsrf($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Generate CSRF token
     */
    protected function generateCsrf() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}
?>
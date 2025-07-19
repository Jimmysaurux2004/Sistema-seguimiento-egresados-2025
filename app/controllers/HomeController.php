<?php
/**
 * Home Controller
 * Handles main application pages and dashboard
 */

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/Evento.php';
require_once APP_PATH . '/models/Egresado.php';
require_once APP_PATH . '/models/Tutoria.php';
require_once APP_PATH . '/models/Notificacion.php';

class HomeController extends Controller {
    private $eventoModel;
    private $egresadoModel;
    private $tutoriaModel;
    private $notificacionModel;
    
    public function __construct() {
        parent::__construct();
        $this->eventoModel = new Evento();
        $this->egresadoModel = new Egresado();
        $this->tutoriaModel = new Tutoria();
        $this->notificacionModel = new Notificacion();
    }
    
    /**
     * Home page
     */
    public function index() {
        // Always redirect to login if not authenticated
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        
        // If authenticated, redirect to dashboard
        $this->redirect('/dashboard');
    }
    
    /**
     * Dashboard
     */
    public function dashboard() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $upcomingEvents = $this->eventoModel->getUpcoming(5);
        $notifications = $this->notificacionModel->getByUser($user['id'], 5);
        
        if ($user['role'] === 'admin') {
            $this->adminDashboard();
            return;
        }
        
        // Get graduate specific data
        $egresado = $this->egresadoModel->findByUserId($user['id']);
        $myTutorias = [];
        
        if ($egresado) {
            $myTutorias = $this->tutoriaModel->getByGraduate($egresado['dni']);
        }
        
        $this->view('home/dashboard', [
            'user' => $user,
            'egresado' => $egresado,
            'events' => $upcomingEvents,
            'tutorias' => $myTutorias,
            'notifications' => $notifications
        ]);
    }
    
    /**
     * Admin dashboard
     */
    private function adminDashboard() {
        $stats = [
            'total_egresados' => $this->egresadoModel->count(),
            'tutorias_pendientes' => $this->tutoriaModel->count(['estado' => 'pendiente']),
            'eventos_proximos' => $this->eventoModel->count(['activo' => 1])
        ];
        
        $recentTutorias = $this->tutoriaModel->getPending();
        $upcomingEvents = $this->eventoModel->getUpcoming(5);
        $employmentStats = $this->egresadoModel->getEmploymentStats();
        
        $this->view('admin/dashboard', [
            'stats' => $stats,
            'recent_tutorias' => $recentTutorias,
            'events' => $upcomingEvents,
            'employment_stats' => $employmentStats
        ]);
    }
    
    /**
     * Get notifications (AJAX)
     */
    public function getNotifications() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $notifications = $this->notificacionModel->getByUser($user['id'], 10);
        $unreadCount = $this->notificacionModel->getUnreadCount($user['id']);
        
        $this->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }
    
    /**
     * Mark notification as read
     */
    public function markNotificationRead() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
            return;
        }
        
        $notificationId = $_POST['notification_id'] ?? '';
        $user = $this->getCurrentUser();
        
        if (empty($notificationId)) {
            $this->json(['error' => 'ID de notificación requerido'], 400);
            return;
        }
        
        $success = $this->notificacionModel->markAsRead($notificationId, $user['id']);
        
        $this->json(['success' => $success]);
    }
}
?>
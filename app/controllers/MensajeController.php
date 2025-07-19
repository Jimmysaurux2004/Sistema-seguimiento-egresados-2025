<?php
/**
 * Mensaje Controller
 * Handles internal messaging system
 */

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/Mensaje.php';
require_once APP_PATH . '/models/Usuario.php';
require_once APP_PATH . '/models/Egresado.php';

class MensajeController extends Controller {
    private $mensajeModel;
    private $userModel;
    private $egresadoModel;
    
    public function __construct() {
        parent::__construct();
        $this->mensajeModel = new Mensaje();
        $this->userModel = new Usuario();
        $this->egresadoModel = new Egresado();
    }
    
    /**
     * Show inbox
     */
    public function inbox() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $messages = $this->mensajeModel->getInbox($user['id']);
        $conversations = $this->mensajeModel->getRecentConversations($user['id']);
        
        $this->view('mensaje/inbox', [
            'messages' => $messages,
            'conversations' => $conversations
        ]);
    }
    
    /**
     * Show sent messages
     */
    public function sent() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $messages = $this->mensajeModel->getSent($user['id']);
        
        $this->view('mensaje/sent', [
            'messages' => $messages
        ]);
    }
    
    /**
     * Show compose form
     */
    public function compose() {
        $this->requireAuth();
        
        $users = $this->userModel->getAllWithRole();
        
        $this->view('mensaje/compose', [
            'users' => $users,
            'csrf_token' => $this->generateCsrf()
        ]);
    }
    
    /**
     * Send message
     */
    public function send() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mensajes/compose');
        }
        
        $user = $this->getCurrentUser();
        $csrf_token = $_POST['csrf_token'] ?? '';
        
        // Validate CSRF token
        if (!$this->validateCsrf($csrf_token)) {
            $this->redirect('/mensajes/compose');
        }
        
        $receptor_id = $_POST['receptor_id'] ?? '';
        $asunto = $_POST['asunto'] ?? '';
        $mensaje = $_POST['mensaje'] ?? '';
        
        // Validate input
        $errors = [];
        
        if (empty($receptor_id)) {
            $errors[] = 'Destinatario es requerido';
        }
        
        if (empty($asunto)) {
            $errors[] = 'Asunto es requerido';
        }
        
        if (empty($mensaje)) {
            $errors[] = 'Mensaje es requerido';
        }
        
        // Check if recipient exists
        $receptor = $this->userModel->find($receptor_id);
        if (!$receptor) {
            $errors[] = 'Destinatario no válido';
        }
        
        if (!empty($errors)) {
            $users = $this->userModel->getAllWithRole();
            
            $this->view('mensaje/compose', [
                'users' => $users,
                'errors' => $errors,
                'csrf_token' => $this->generateCsrf(),
                'formData' => $_POST
            ]);
            return;
        }
        
        try {
            $this->mensajeModel->send($user['id'], $receptor_id, $asunto, $mensaje);
            $this->redirect('/mensajes/sent');
            
        } catch (Exception $e) {
            $users = $this->userModel->getAllWithRole();
            
            $this->view('mensaje/compose', [
                'users' => $users,
                'error' => 'Error al enviar el mensaje',
                'csrf_token' => $this->generateCsrf(),
                'formData' => $_POST
            ]);
        }
    }
    
    /**
     * Show conversation
     */
    public function conversation($userId) {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $otherUser = $this->userModel->find($userId);
        
        if (!$otherUser) {
            $this->redirect('/mensajes');
        }
        
        $messages = $this->mensajeModel->getConversation($user['id'], $userId);
        
        // Mark messages as read
        foreach ($messages as $message) {
            if ($message['receptor_id'] == $user['id'] && !$message['leido']) {
                $this->mensajeModel->markAsRead($message['id'], $user['id']);
            }
        }
        
        $this->view('mensaje/conversation', [
            'messages' => $messages,
            'other_user' => $otherUser,
            'csrf_token' => $this->generateCsrf()
        ]);
    }
    
    /**
     * Reply to message
     */
    public function reply() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
            return;
        }
        
        $user = $this->getCurrentUser();
        $receptor_id = $_POST['receptor_id'] ?? '';
        $mensaje = $_POST['mensaje'] ?? '';
        $csrf_token = $_POST['csrf_token'] ?? '';
        
        // Validate CSRF token
        if (!$this->validateCsrf($csrf_token)) {
            $this->json(['error' => 'Token de seguridad inválido'], 400);
            return;
        }
        
        if (empty($receptor_id) || empty($mensaje)) {
            $this->json(['error' => 'Datos requeridos'], 400);
            return;
        }
        
        try {
            $messageId = $this->mensajeModel->send($user['id'], $receptor_id, 'Re: Conversación', $mensaje);
            $this->json(['success' => true, 'message_id' => $messageId]);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al enviar mensaje'], 500);
        }
    }
    
    /**
     * Get unread message count (AJAX)
     */
    public function getUnreadCount() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $count = $this->mensajeModel->getUnreadCount($user['id']);
        
        $this->json(['unread_count' => $count]);
    }
    
    /**
     * Mark message as read (AJAX)
     */
    public function markAsRead() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
            return;
        }
        
        $user = $this->getCurrentUser();
        $messageId = $_POST['message_id'] ?? '';
        
        if (empty($messageId)) {
            $this->json(['error' => 'ID de mensaje requerido'], 400);
            return;
        }
        
        try {
            $this->mensajeModel->markAsRead($messageId, $user['id']);
            $this->json(['success' => true]);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al marcar como leído'], 500);
        }
    }
}
?>
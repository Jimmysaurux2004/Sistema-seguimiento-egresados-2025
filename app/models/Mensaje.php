<?php
/**
 * Mensaje model
 * Handles internal messaging system
 */

require_once APP_PATH . '/core/Model.php';

class Mensaje extends Model {
    protected $table = 'mensajes';
    
    /**
     * Get messages for a user (inbox)
     */
    public function getInbox($userId) {
        $sql = "SELECT m.*, u.email as emisor_email 
                FROM {$this->table} m 
                JOIN usuarios u ON m.emisor_id = u.id 
                WHERE m.receptor_id = ? 
                ORDER BY m.fecha_envio DESC";
        return $this->db->fetchAll($sql, [$userId]);
    }
    
    /**
     * Get sent messages for a user
     */
    public function getSent($userId) {
        $sql = "SELECT m.*, u.email as receptor_email 
                FROM {$this->table} m 
                JOIN usuarios u ON m.receptor_id = u.id 
                WHERE m.emisor_id = ? 
                ORDER BY m.fecha_envio DESC";
        return $this->db->fetchAll($sql, [$userId]);
    }
    
    /**
     * Get unread message count
     */
    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE receptor_id = ? AND leido = 0";
        $result = $this->db->fetch($sql, [$userId]);
        return $result['count'];
    }
    
    /**
     * Mark message as read
     */
    public function markAsRead($messageId, $userId) {
        $sql = "UPDATE {$this->table} SET leido = 1 
                WHERE id = ? AND receptor_id = ?";
        return $this->db->execute($sql, [$messageId, $userId]);
    }
    
    /**
     * Send message
     */
    public function send($emisorId, $receptorId, $asunto, $mensaje) {
        $data = [
            'emisor_id' => $emisorId,
            'receptor_id' => $receptorId,
            'asunto' => $asunto,
            'mensaje' => $mensaje
        ];
        return $this->create($data);
    }
    
    /**
     * Get conversation between two users
     */
    public function getConversation($user1, $user2) {
        $sql = "SELECT m.*, u1.email as emisor_email, u2.email as receptor_email 
                FROM {$this->table} m 
                JOIN usuarios u1 ON m.emisor_id = u1.id 
                JOIN usuarios u2 ON m.receptor_id = u2.id 
                WHERE (m.emisor_id = ? AND m.receptor_id = ?) 
                   OR (m.emisor_id = ? AND m.receptor_id = ?) 
                ORDER BY m.fecha_envio ASC";
        return $this->db->fetchAll($sql, [$user1, $user2, $user2, $user1]);
    }
    
    /**
     * Get recent conversations for a user
     */
    public function getRecentConversations($userId) {
        $sql = "SELECT DISTINCT 
                    CASE WHEN m.emisor_id = ? THEN m.receptor_id ELSE m.emisor_id END as other_user_id,
                    u.email as other_user_email,
                    MAX(m.fecha_envio) as ultima_comunicacion,
                    (SELECT COUNT(*) FROM {$this->table} WHERE receptor_id = ? AND emisor_id = other_user_id AND leido = 0) as mensajes_no_leidos
                FROM {$this->table} m 
                JOIN usuarios u ON (CASE WHEN m.emisor_id = ? THEN m.receptor_id ELSE m.emisor_id END) = u.id 
                WHERE m.emisor_id = ? OR m.receptor_id = ? 
                GROUP BY other_user_id, other_user_email 
                ORDER BY ultima_comunicacion DESC";
        return $this->db->fetchAll($sql, [$userId, $userId, $userId, $userId, $userId]);
    }
}
?>
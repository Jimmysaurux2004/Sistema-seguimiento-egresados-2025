<?php
/**
 * Notificacion model
 * Handles system notifications
 */

require_once APP_PATH . '/core/Model.php';

class Notificacion extends Model {
    protected $table = 'notificaciones';
    
    /**
     * Get notifications for a user
     */
    public function getByUser($userId, $limit = 10) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE usuario_id = ? 
                ORDER BY fecha_creacion DESC 
                LIMIT ?";
        return $this->db->fetchAll($sql, [$userId, $limit]);
    }
    
    /**
     * Get unread notifications count
     */
    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE usuario_id = ? AND leida = 0";
        $result = $this->db->fetch($sql, [$userId]);
        return $result['count'];
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($id, $userId) {
        $sql = "UPDATE {$this->table} SET leida = 1 
                WHERE id = ? AND usuario_id = ?";
        return $this->db->execute($sql, [$id, $userId]);
    }
    
    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($userId) {
        $sql = "UPDATE {$this->table} SET leida = 1 WHERE usuario_id = ?";
        return $this->db->execute($sql, [$userId]);
    }
    
    /**
     * Create notification
     */
    public function createNotification($userId, $titulo, $mensaje, $tipo = 'sistema') {
        $data = [
            'usuario_id' => $userId,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'tipo' => $tipo
        ];
        return $this->create($data);
    }
    
    /**
     * Create notification for all users
     */
    public function createForAllUsers($titulo, $mensaje, $tipo = 'sistema') {
        $users = $this->db->fetchAll("SELECT id FROM usuarios WHERE activo = 1");
        foreach ($users as $user) {
            $this->createNotification($user['id'], $titulo, $mensaje, $tipo);
        }
    }
    
    /**
     * Delete old notifications
     */
    public function deleteOld($days = 30) {
        $sql = "DELETE FROM {$this->table} 
                WHERE fecha_creacion < DATE_SUB(NOW(), INTERVAL ? DAY)";
        return $this->db->execute($sql, [$days]);
    }
}
?>
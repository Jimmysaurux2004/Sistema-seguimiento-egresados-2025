<?php
/**
 * Usuario model
 * Handles user authentication and management
 */

require_once APP_PATH . '/core/Model.php';

class Usuario extends Model {
    protected $table = 'usuarios';
    
    /**
     * Find user by email
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? AND activo = 1";
        return $this->db->fetch($sql, [$email]);
    }
    
    /**
     * Verify user password
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Hash password
     */
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Create new user
     */
    public function createUser($email, $password, $role = 'egresado') {
        $data = [
            'email' => $email,
            'password_hash' => $this->hashPassword($password),
            'rol' => $role
        ];
        
        return $this->create($data);
    }
    
    /**
     * Update last access time
     */
    public function updateLastAccess($userId) {
        $sql = "UPDATE {$this->table} SET ultimo_acceso = NOW() WHERE id = ?";
        return $this->db->execute($sql, [$userId]);
    }
    
    /**
     * Get all users with role information
     */
    public function getAllWithRole() {
        $sql = "SELECT u.*, e.nombres, e.apellidos 
                FROM {$this->table} u 
                LEFT JOIN egresados e ON u.id = e.usuario_id 
                WHERE u.activo = 1 
                ORDER BY u.fecha_registro DESC";
        return $this->db->fetchAll($sql);
    }
}
?>
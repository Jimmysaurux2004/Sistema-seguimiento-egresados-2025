<?php
/**
 * Evento model
 * Handles events and institutional activities
 */

require_once APP_PATH . '/core/Model.php';

class Evento extends Model {
    protected $table = 'eventos';
    
    /**
     * Get active events
     */
    public function getActive() {
        $sql = "SELECT * FROM {$this->table} WHERE activo = 1 ORDER BY fecha ASC";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get upcoming events
     */
    public function getUpcoming($limit = 5) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE activo = 1 AND fecha >= CURDATE() 
                ORDER BY fecha ASC LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }
    
    /**
     * Get events by type
     */
    public function getByType($tipo) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE tipo = ? AND activo = 1 
                ORDER BY fecha ASC";
        return $this->db->fetchAll($sql, [$tipo]);
    }
    
    /**
     * Get events by date range
     */
    public function getByDateRange($startDate, $endDate) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE fecha BETWEEN ? AND ? AND activo = 1 
                ORDER BY fecha ASC";
        return $this->db->fetchAll($sql, [$startDate, $endDate]);
    }
    
    /**
     * Search events
     */
    public function search($query) {
        $searchTerm = "%$query%";
        $sql = "SELECT * FROM {$this->table} 
                WHERE (nombre LIKE ? OR descripcion LIKE ?) AND activo = 1 
                ORDER BY fecha ASC";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm]);
    }
    
    /**
     * Toggle event status
     */
    public function toggleStatus($id) {
        $sql = "UPDATE {$this->table} SET activo = NOT activo WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
}
?>
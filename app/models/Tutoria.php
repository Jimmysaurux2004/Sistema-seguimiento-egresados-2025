<?php
/**
 * Tutoria model
 * Handles tutoring session management
 */

require_once APP_PATH . '/core/Model.php';

class Tutoria extends Model {
    protected $table = 'tutorias';
    
    /**
     * Get tutoring sessions for a graduate
     */
    public function getByGraduate($dni) {
        $sql = "SELECT * FROM {$this->table} WHERE egresado_dni = ? ORDER BY fecha DESC, hora DESC";
        return $this->db->fetchAll($sql, [$dni]);
    }
    
    /**
     * Get tutoring sessions by teacher
     */
    public function getByTeacher($docente) {
        $sql = "SELECT t.*, e.nombres, e.apellidos, e.correo 
                FROM {$this->table} t 
                JOIN egresados e ON t.egresado_dni = e.dni 
                WHERE t.docente = ? 
                ORDER BY t.fecha DESC, t.hora DESC";
        return $this->db->fetchAll($sql, [$docente]);
    }
    
    /**
     * Get pending tutoring sessions
     */
    public function getPending() {
        $sql = "SELECT t.*, e.nombres, e.apellidos, e.correo 
                FROM {$this->table} t 
                JOIN egresados e ON t.egresado_dni = e.dni 
                WHERE t.estado = 'pendiente' 
                ORDER BY t.fecha_solicitud DESC";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get upcoming sessions
     */
    public function getUpcoming($limit = 10) {
        $sql = "SELECT t.*, e.nombres, e.apellidos 
                FROM {$this->table} t 
                JOIN egresados e ON t.egresado_dni = e.dni 
                WHERE t.fecha >= CURDATE() AND t.estado IN ('pendiente', 'confirmada') 
                ORDER BY t.fecha ASC, t.hora ASC 
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }
    
    /**
     * Check if time slot is available
     */
    public function isTimeSlotAvailable($docente, $fecha, $hora) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE docente = ? AND fecha = ? AND hora = ? 
                AND estado IN ('pendiente', 'confirmada')";
        $result = $this->db->fetch($sql, [$docente, $fecha, $hora]);
        return $result['count'] == 0;
    }
    
    /**
     * Update session status
     */
    public function updateStatus($id, $status, $notas = null) {
        $sql = "UPDATE {$this->table} 
                SET estado = ?, notas = ?, fecha_actualizacion = NOW() 
                WHERE id = ?";
        return $this->db->execute($sql, [$status, $notas, $id]);
    }
    
    /**
     * Get sessions by date range
     */
    public function getByDateRange($startDate, $endDate) {
        $sql = "SELECT t.*, e.nombres, e.apellidos 
                FROM {$this->table} t 
                JOIN egresados e ON t.egresado_dni = e.dni 
                WHERE t.fecha BETWEEN ? AND ? 
                ORDER BY t.fecha ASC, t.hora ASC";
        return $this->db->fetchAll($sql, [$startDate, $endDate]);
    }
}
?>
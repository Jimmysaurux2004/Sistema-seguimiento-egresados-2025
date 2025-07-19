<?php
/**
 * Encuesta model
 * Handles surveys and responses
 */

require_once APP_PATH . '/core/Model.php';

class Encuesta extends Model {
    protected $table = 'encuestas';
    
    /**
     * Get active surveys
     */
    public function getActive() {
        $sql = "SELECT * FROM {$this->table} WHERE estado = 'activa' ORDER BY orden_pregunta ASC";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get survey responses for a graduate
     */
    public function getResponsesByGraduate($dni) {
        $sql = "SELECT e.*, r.respuesta, r.fecha_respuesta 
                FROM {$this->table} e 
                LEFT JOIN respuestas_encuesta r ON e.id = r.encuesta_id AND r.egresado_dni = ? 
                WHERE e.estado = 'activa' 
                ORDER BY e.orden_pregunta ASC";
        return $this->db->fetchAll($sql, [$dni]);
    }
    
    /**
     * Save survey response
     */
    public function saveResponse($dni, $encuestaId, $respuesta) {
        $sql = "INSERT INTO respuestas_encuesta (egresado_dni, encuesta_id, respuesta) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE respuesta = VALUES(respuesta), fecha_respuesta = NOW()";
        return $this->db->execute($sql, [$dni, $encuestaId, $respuesta]);
    }
    
    /**
     * Get survey statistics
     */
    public function getStatistics($encuestaId) {
        $sql = "SELECT 
                    respuesta,
                    COUNT(*) as cantidad,
                    ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM respuestas_encuesta WHERE encuesta_id = ?)), 2) as porcentaje
                FROM respuestas_encuesta 
                WHERE encuesta_id = ? 
                GROUP BY respuesta 
                ORDER BY cantidad DESC";
        return $this->db->fetchAll($sql, [$encuestaId, $encuestaId]);
    }
    
    /**
     * Get all responses for a survey
     */
    public function getAllResponses($encuestaId) {
        $sql = "SELECT r.*, e.nombres, e.apellidos 
                FROM respuestas_encuesta r 
                JOIN egresados e ON r.egresado_dni = e.dni 
                WHERE r.encuesta_id = ? 
                ORDER BY r.fecha_respuesta DESC";
        return $this->db->fetchAll($sql, [$encuestaId]);
    }
    
    /**
     * Check if graduate has responded
     */
    public function hasResponded($dni, $encuestaId) {
        $sql = "SELECT COUNT(*) as count FROM respuestas_encuesta 
                WHERE egresado_dni = ? AND encuesta_id = ?";
        $result = $this->db->fetch($sql, [$dni, $encuestaId]);
        return $result['count'] > 0;
    }
    
    /**
     * Toggle survey status
     */
    public function toggleStatus($id) {
        $sql = "UPDATE {$this->table} 
                SET estado = CASE WHEN estado = 'activa' THEN 'inactiva' ELSE 'activa' END 
                WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
}
?>
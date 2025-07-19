<?php

/**
 * Egresado model
 * Handles graduate information and operations
 */

require_once APP_PATH . '/core/Model.php';

class Egresado extends Model
{
    protected $table = 'egresados';

    /**
     * Find graduate by DNI
     */
    public function findByDni($dni)
    {
        $sql = "SELECT * FROM {$this->table} WHERE dni = ?";
        return $this->db->fetch($sql, [$dni]);
    }

    /**
     * Find graduate by email
     */
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE correo = ?";
        return $this->db->fetch($sql, [$email]);
    }

    /**
     * Find graduate by user ID
     */
    public function findByUserId($userId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE usuario_id = ?";
        return $this->db->fetch($sql, [$userId]);
    }

    /**
     * Get graduates with user information
     */
    public function getAllWithUser()
    {
        $sql = "SELECT e.*, u.email, u.activo, u.ultimo_acceso 
                FROM {$this->table} e 
                LEFT JOIN usuarios u ON e.usuario_id = u.id 
                ORDER BY e.fecha_registro DESC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get employment statistics
     */
    public function getEmploymentStats()
    {
        $sql = "SELECT 
                    situacion_laboral_actual,
                    COUNT(*) as cantidad,
                    ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM {$this->table})), 2) as porcentaje
                FROM {$this->table} 
                GROUP BY situacion_laboral_actual";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get graduates by career
     */
    public function getByCareer($carrera)
    {
        $sql = "SELECT * FROM {$this->table} WHERE carrera = ? ORDER BY anio_egreso DESC";
        return $this->db->fetchAll($sql, [$carrera]);
    }

    /**
     * Get graduates by graduation year
     */
    public function getByYear($anio)
    {
        $sql = "SELECT * FROM {$this->table} WHERE anio_egreso = ? ORDER BY apellidos, nombres";
        return $this->db->fetchAll($sql, [$anio]);
    }

    /**
     * Update employment status
     */
    public function updateEmploymentStatus($dni, $status, $empresa = null, $cargo = null)
    {
        $sql = "UPDATE {$this->table} 
                SET situacion_laboral_actual = ?, empresa_actual = ?, cargo_actual = ?, 
                    fecha_actualizacion = NOW() 
                WHERE dni = ?";
        return $this->db->execute($sql, [$status, $empresa, $cargo, $dni]);
    }

    /**
     * Search graduates
     */
    public function search($query)
    {
        $searchTerm = "%$query%";
        $sql = "SELECT * FROM {$this->table} 
                WHERE nombres LIKE ? OR apellidos LIKE ? OR correo LIKE ? OR carrera LIKE ?
                ORDER BY apellidos, nombres";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }

    /**
     * Get filtered graduates with user information
     */
    public function getFilteredEgresados($search = '', $carrera = '', $situacion_laboral = '')
    {
        $conditions = [];
        $params = [];

        if (!empty($search)) {
            $searchTerm = "%$search%";
            $conditions[] = "(e.nombres LIKE ? OR e.apellidos LIKE ? OR e.correo LIKE ? OR e.dni LIKE ?)";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }

        if (!empty($carrera)) {
            $conditions[] = "e.carrera = ?";
            $params[] = $carrera;
        }

        if (!empty($situacion_laboral)) {
            $conditions[] = "e.situacion_laboral_actual = ?";
            $params[] = $situacion_laboral;
        }

        $whereClause = '';
        if (!empty($conditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $conditions);
        }

        $sql = "SELECT e.*, u.email, u.activo, u.ultimo_acceso 
                FROM {$this->table} e 
                LEFT JOIN usuarios u ON e.usuario_id = u.id 
                {$whereClause}
                ORDER BY e.fecha_registro DESC";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get statistics for admin dashboard
     */
    public function getStats()
    {
        $stats = [];

        // Total egresados
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->fetch($sql);
        $stats['total_egresados'] = $result['total'];

        // Empleados
        $sql = "SELECT COUNT(*) as empleados FROM {$this->table} WHERE situacion_laboral_actual = 'empleado'";
        $result = $this->db->fetch($sql);
        $stats['empleados'] = $result['empleados'];

        // Desempleados
        $sql = "SELECT COUNT(*) as desempleados FROM {$this->table} WHERE situacion_laboral_actual = 'desempleado'";
        $result = $this->db->fetch($sql);
        $stats['desempleados'] = $result['desempleados'];

        // Activos este mes
        $sql = "SELECT COUNT(*) as activos FROM {$this->table} WHERE MONTH(fecha_actualizacion) = MONTH(NOW()) AND YEAR(fecha_actualizacion) = YEAR(NOW())";
        $result = $this->db->fetch($sql);
        $stats['activos_este_mes'] = $result['activos'];

        return $stats;
    }
}

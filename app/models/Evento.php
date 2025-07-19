<?php

/**
 * Evento model
 * Handles events and institutional activities
 */

require_once APP_PATH . '/core/Model.php';

class Evento extends Model
{
    protected $table = 'eventos';

    /**
     * Get active events
     */
    public function getActive()
    {
        $sql = "SELECT * FROM {$this->table} WHERE activo = 1 ORDER BY fecha ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get upcoming events
     */
    public function getUpcoming($limit = 5)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE activo = 1 AND fecha >= CURDATE() 
                ORDER BY fecha ASC LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Get events by type
     */
    public function getByType($tipo)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE tipo = ? AND activo = 1 
                ORDER BY fecha ASC";
        return $this->db->fetchAll($sql, [$tipo]);
    }

    /**
     * Get events by date range
     */
    public function getByDateRange($startDate, $endDate)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE fecha BETWEEN ? AND ? AND activo = 1 
                ORDER BY fecha ASC";
        return $this->db->fetchAll($sql, [$startDate, $endDate]);
    }

    /**
     * Search events
     */
    public function search($query)
    {
        $searchTerm = "%$query%";
        $sql = "SELECT * FROM {$this->table} 
                WHERE (nombre LIKE ? OR descripcion LIKE ?) AND activo = 1 
                ORDER BY fecha ASC";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm]);
    }

    /**
     * Toggle event status
     */
    public function toggleStatus($id)
    {
        $sql = "UPDATE {$this->table} SET activo = NOT activo WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }

    /**
     * Get event with registration count
     */
    public function getWithRegistrationCount($id)
    {
        // For now, return event without registration count
        // This will work until the registros_eventos table is created
        $sql = "SELECT e.*, 0 as inscritos 
                FROM {$this->table} e 
                WHERE e.id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    /**
     * Get all events with registration count for admin
     */
    public function getAllWithRegistrationCount()
    {
        // For now, return events without registration count
        // This will work until the registros_eventos table is created
        $sql = "SELECT e.*, 0 as inscritos 
                FROM {$this->table} e 
                ORDER BY e.fecha DESC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Delete event and its registrations
     */
    public function deleteEvent($id)
    {
        // For now, just delete the event
        // When registros_eventos table is created, this will handle registrations too
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }

    /**
     * Get filtered events with pagination
     */
    public function getFilteredEvents($filters = [])
    {
        $search = $filters['search'] ?? '';
        $tipo = $filters['tipo'] ?? '';
        $estado = $filters['estado'] ?? '';
        $fecha_desde = $filters['fecha_desde'] ?? '';
        $fecha_hasta = $filters['fecha_hasta'] ?? '';
        $page = $filters['page'] ?? 1;
        $per_page = $filters['per_page'] ?? 10;

        $offset = ($page - 1) * $per_page;

        $where_conditions = [];
        $params = [];

        // Search filter
        if (!empty($search)) {
            $where_conditions[] = "(nombre LIKE ? OR descripcion LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        // Type filter
        if (!empty($tipo)) {
            $where_conditions[] = "tipo = ?";
            $params[] = $tipo;
        }

        // Status filter
        if (!empty($estado)) {
            if ($estado === 'activo') {
                $where_conditions[] = "activo = 1 AND fecha >= CURDATE()";
            } elseif ($estado === 'finalizado') {
                $where_conditions[] = "fecha < CURDATE()";
            } elseif ($estado === 'cancelado') {
                $where_conditions[] = "activo = 0";
            }
        }

        // Date range filters
        if (!empty($fecha_desde)) {
            $where_conditions[] = "fecha >= ?";
            $params[] = $fecha_desde;
        }

        if (!empty($fecha_hasta)) {
            $where_conditions[] = "fecha <= ?";
            $params[] = $fecha_hasta;
        }

        $where_clause = '';
        if (!empty($where_conditions)) {
            $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        }

        $sql = "SELECT e.*, 0 as inscritos 
                FROM {$this->table} e 
                $where_clause 
                ORDER BY e.fecha DESC 
                LIMIT ? OFFSET ?";

        $params[] = $per_page;
        $params[] = $offset;

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get count of filtered events
     */
    public function getFilteredCount($filters = [])
    {
        $search = $filters['search'] ?? '';
        $tipo = $filters['tipo'] ?? '';
        $estado = $filters['estado'] ?? '';
        $fecha_desde = $filters['fecha_desde'] ?? '';
        $fecha_hasta = $filters['fecha_hasta'] ?? '';

        $where_conditions = [];
        $params = [];

        // Search filter
        if (!empty($search)) {
            $where_conditions[] = "(nombre LIKE ? OR descripcion LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        // Type filter
        if (!empty($tipo)) {
            $where_conditions[] = "tipo = ?";
            $params[] = $tipo;
        }

        // Status filter
        if (!empty($estado)) {
            if ($estado === 'activo') {
                $where_conditions[] = "activo = 1 AND fecha >= CURDATE()";
            } elseif ($estado === 'finalizado') {
                $where_conditions[] = "fecha < CURDATE()";
            } elseif ($estado === 'cancelado') {
                $where_conditions[] = "activo = 0";
            }
        }

        // Date range filters
        if (!empty($fecha_desde)) {
            $where_conditions[] = "fecha >= ?";
            $params[] = $fecha_desde;
        }

        if (!empty($fecha_hasta)) {
            $where_conditions[] = "fecha <= ?";
            $params[] = $fecha_hasta;
        }

        $where_clause = '';
        if (!empty($where_conditions)) {
            $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        }

        $sql = "SELECT COUNT(*) as total FROM {$this->table} $where_clause";
        $result = $this->db->fetch($sql, $params);

        return $result['total'];
    }

    /**
     * Get event statistics for admin dashboard
     */
    public function getStats()
    {
        $stats = [];

        // Total events
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->fetch($sql);
        $stats['total_eventos'] = $result['total'];

        // Active events
        $sql = "SELECT COUNT(*) as activos FROM {$this->table} WHERE activo = 1";
        $result = $this->db->fetch($sql);
        $stats['eventos_activos'] = $result['activos'];

        // Upcoming events
        $sql = "SELECT COUNT(*) as proximos FROM {$this->table} WHERE fecha >= CURDATE() AND activo = 1";
        $result = $this->db->fetch($sql);
        $stats['eventos_proximos'] = $result['proximos'];

        // Events this week
        $sql = "SELECT COUNT(*) as esta_semana FROM {$this->table} 
                WHERE fecha BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND activo = 1";
        $result = $this->db->fetch($sql);
        $stats['eventos_esta_semana'] = $result['esta_semana'];

        // Training events
        $sql = "SELECT COUNT(*) as capacitaciones FROM {$this->table} WHERE tipo = 'capacitacion' AND activo = 1";
        $result = $this->db->fetch($sql);
        $stats['capacitaciones'] = $result['capacitaciones'];

        // Total registrations (will be 0 until registros_eventos table is created)
        $stats['total_inscripciones'] = 0;

        return $stats;
    }
}

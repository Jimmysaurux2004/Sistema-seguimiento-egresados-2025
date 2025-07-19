<?php
/**
 * Base model class
 * Provides common database operations for all models
 */

class Model {
    protected $db;
    protected $table;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Find record by ID
     */
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    /**
     * Get all records
     */
    public function findAll($conditions = [], $orderBy = 'id DESC', $limit = null) {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $column => $value) {
                $where[] = "$column = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Create new record
     */
    public function create($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $this->db->execute($sql, $data);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Update record
     */
    public function update($id, $data) {
        $sets = [];
        foreach (array_keys($data) as $column) {
            $sets[] = "$column = :$column";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE id = :id";
        $data['id'] = $id;
        
        return $this->db->execute($sql, $data);
    }
    
    /**
     * Delete record
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    /**
     * Count records
     */
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $column => $value) {
                $where[] = "$column = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['total'];
    }
}
?>
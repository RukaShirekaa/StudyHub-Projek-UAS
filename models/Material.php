<?php

class Material {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function getAllByCourse($courseId, $userId = null) {
        $query = "SELECT m.*, u.name as uploader_name FROM materials m JOIN users u ON m.uploaded_by = u.id WHERE m.course_id = ?";
        $params = [$courseId];
        
        if ($userId) {
            $query .= " AND m.uploaded_by = ?";
            $params[] = $userId;
        }
        
        $query .= " ORDER BY m.created_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getAll($userId = null) {
        $query = "SELECT m.*, u.name as uploader_name, c.name as course_name FROM materials m JOIN users u ON m.uploaded_by = u.id JOIN courses c ON m.course_id = c.id";
        $params = [];
        
        if ($userId) {
            $query .= " WHERE m.uploaded_by = ?";
            $params[] = $userId;
        }
        
        $query .= " ORDER BY m.created_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById($id, $userId = null) {
        $query = "SELECT * FROM materials WHERE id = ?";
        $params = [$id];
        
        if ($userId) {
            $query .= " AND uploaded_by = ?";
            $params[] = $userId;
        }
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO materials (course_id, title, description, file_path, uploaded_by) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$data['course_id'], $data['title'], $data['description'], $data['file_path'], $data['uploaded_by']]);
    }

    public function delete($id, $userId = null) {
        $query = "DELETE FROM materials WHERE id = ?";
        $params = [$id];
        
        if ($userId) {
            $query .= " AND uploaded_by = ?";
            $params[] = $userId;
        }
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute($params);
    }
}

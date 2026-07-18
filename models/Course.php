<?php

class Course {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function getAll($search = null, $userId = null, $sort = 'terbaru') {
        $query = "SELECT * FROM courses";
        $params = [];
        $conditions = [];
        
        if ($userId) {
            $conditions[] = "user_id = ?";
            $params[] = $userId;
        }

        if ($search) {
            $conditions[] = "(name LIKE ? OR description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        if ($sort === 'az') {
            $query .= " ORDER BY name ASC";
        } else {
            $query .= " ORDER BY created_at DESC";
        }
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById($id, $userId = null) {
        $query = "SELECT * FROM courses WHERE id = ?";
        $params = [$id];
        
        if ($userId) {
            $query .= " AND user_id = ?";
            $params[] = $userId;
        }
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO courses (code, name, description, user_id) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$data['code'], $data['name'], $data['description'], $data['user_id']]);
    }

    public function delete($id, $userId = null) {
        $query = "DELETE FROM courses WHERE id = ?";
        $params = [$id];
        
        if ($userId) {
            $query .= " AND user_id = ?";
            $params[] = $userId;
        }
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute($params);
    }
}

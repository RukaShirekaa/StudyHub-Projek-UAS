<?php

class Note {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function getAllByUser($userId, $search = null) {
        $query = "SELECT n.*, c.name as course_name FROM notes n JOIN courses c ON n.course_id = c.id WHERE n.user_id = ?";
        $params = [$userId];
        
        if ($search) {
            $query .= " AND (n.title LIKE ? OR n.content LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $query .= " ORDER BY n.updated_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById($id, $userId) {
        $stmt = $this->pdo->prepare("SELECT n.*, c.name as course_name FROM notes n JOIN courses c ON n.course_id = c.id WHERE n.id = ? AND n.user_id = ?");
        $stmt->execute([$id, $userId]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO notes (user_id, course_id, title, content) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['user_id'], $data['course_id'], $data['title'], $data['content']]);
        return $this->pdo->lastInsertId();
    }

    public function update($id, $userId, $data) {
        $stmt = $this->pdo->prepare("UPDATE notes SET title = ?, content = ? WHERE id = ? AND user_id = ?");
        return $stmt->execute([$data['title'], $data['content'], $id, $userId]);
    }

    public function delete($id, $userId) {
        $stmt = $this->pdo->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }
}

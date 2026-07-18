<?php

class Forum {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function getAll($tab = 'semua', $userId = null, $search = null) {
        $query = "SELECT f.*, u.name as author_name, c.name as course_name, 
            (SELECT COUNT(*) FROM forum_replies WHERE forum_id = f.id) as reply_count 
            FROM forums f 
            JOIN users u ON f.user_id = u.id 
            JOIN courses c ON f.course_id = c.id";
            
        $params = [];
        $conditions = [];
        
        if ($tab === 'diikuti' && $userId) {
            $conditions[] = "(f.user_id = ? OR ? IN (SELECT user_id FROM forum_replies WHERE forum_id = f.id))";
            $params[] = $userId;
            $params[] = $userId;
        } elseif ($tab === 'belum_terjawab') {
            $conditions[] = "((SELECT COUNT(*) FROM forum_replies WHERE forum_id = f.id) = 0)";
        }
        
        if ($search) {
            $conditions[] = "(f.title LIKE ? OR f.content LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $query .= " ORDER BY f.created_at DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT f.*, u.name as author_name, c.name as course_name 
            FROM forums f JOIN users u ON f.user_id = u.id JOIN courses c ON f.course_id = c.id 
            WHERE f.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO forums (course_id, user_id, title, content) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['course_id'], $data['user_id'], $data['title'], $data['content']]);
        return $this->pdo->lastInsertId();
    }

    public function getReplies($forumId) {
        $stmt = $this->pdo->prepare("SELECT r.*, u.name as author_name FROM forum_replies r JOIN users u ON r.user_id = u.id WHERE r.forum_id = ? ORDER BY r.created_at ASC");
        $stmt->execute([$forumId]);
        return $stmt->fetchAll();
    }

    public function addReply($data) {
        $stmt = $this->pdo->prepare("INSERT INTO forum_replies (forum_id, user_id, content) VALUES (?, ?, ?)");
        return $stmt->execute([$data['forum_id'], $data['user_id'], $data['content']]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM forums WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

<?php

class User {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Update daily login streak.
     * - Same calendar day as last_login: no change (already counted).
     * - Exactly the day after last_login: streak + 1.
     * - Gap of 2+ days, or first ever login: reset to 1.
     */
    public function updateStreak($id) {
        $stmt = $this->pdo->prepare("SELECT last_login FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $last = $stmt->fetchColumn();

        $today = date('Y-m-d');

        if ($last) {
            $lastDate = date('Y-m-d', strtotime($last));
            if ($lastDate === $today) {
                return; // already logged in today, keep streak as is
            }
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            if ($lastDate === $yesterday) {
                $stmt = $this->pdo->prepare("UPDATE users SET streak_count = streak_count + 1, last_login = NOW() WHERE id = ?");
                $stmt->execute([$id]);
                return;
            }
        }

        // First login ever, or streak broken
        $stmt = $this->pdo->prepare("UPDATE users SET streak_count = 1, last_login = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function updateProfile($id, $data) {
        // Check if `bio` column exists to avoid SQL errors on DBs that haven't run migration
        try {
            $colStmt = $this->pdo->query("SHOW COLUMNS FROM users LIKE 'bio'");
            $hasBio = (bool) $colStmt->fetch();
        } catch (Exception $e) {
            $hasBio = false;
        }

        // Normalize semester: empty string -> NULL, numeric -> int
        $semesterParam = null;
        if (isset($data['semester']) && $data['semester'] !== '') {
            if (is_numeric($data['semester'])) {
                $semesterParam = (int)$data['semester'];
            } else {
                // non-numeric values treat as NULL to avoid SQL errors
                $semesterParam = null;
            }
        }

        if ($hasBio) {
            $stmt = $this->pdo->prepare("UPDATE users SET name = ?, prodi = ?, semester = ?, bio = ? WHERE id = ?");
            return $stmt->execute([$data['name'], $data['prodi'], $semesterParam, $data['bio'] ?? null, $id]);
        } else {
            $stmt = $this->pdo->prepare("UPDATE users SET name = ?, prodi = ?, semester = ? WHERE id = ?");
            return $stmt->execute([$data['name'], $data['prodi'], $semesterParam, $id]);
        }
    }

    public function updatePhoto($id, $photoPath) {
        $stmt = $this->pdo->prepare("UPDATE users SET photo = ? WHERE id = ?");
        return $stmt->execute([$photoPath, $id]);
    }

    public function getQuizHistory($id) {
        $stmt = $this->pdo->prepare("SELECT r.*, q.total_questions, q.difficulty, m.title as material_title, c.name as course_name 
            FROM quiz_results r 
            JOIN quizzes q ON r.quiz_id = q.id 
            JOIN materials m ON q.material_id = m.id 
            JOIN courses c ON m.course_id = c.id 
            WHERE r.user_id = ? 
            ORDER BY r.created_at DESC");
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    public function getStudyStats($id) {
        // Total materi dipelajari (total notes created + materials downloaded... for now let's just do notes created and quizzes taken)
        // Since we don't have download tracking yet, we will mock it based on material_downloads or quizzes.
        
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM notes WHERE user_id = ?");
        $stmt->execute([$id]);
        $totalNotes = $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM quiz_results WHERE user_id = ?");
        $stmt->execute([$id]);
        $totalQuizzes = $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT AVG(score) FROM quiz_results WHERE user_id = ?");
        $stmt->execute([$id]);
        $avgScore = round($stmt->fetchColumn() ?: 0, 1);

        $stmt = $this->pdo->prepare("SELECT streak_count FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $streak = $stmt->fetchColumn() ?: 0;

        return [
            'total_notes' => $totalNotes,
            'total_quizzes' => $totalQuizzes,
            'avg_score' => $avgScore,
            'streak' => $streak
        ];
    }
}

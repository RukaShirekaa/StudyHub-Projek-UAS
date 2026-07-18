<?php

class DashboardController {
    public function index() {
        $pdo = Database::getInstance();
        $userId = $_SESSION['user_id'];
        
        // Fetch user data for session if not set properly
        if (!isset($_SESSION['user_name'])) {
            $stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            if ($user) {
                $_SESSION['user_name'] = $user['name'];
            }
        }

        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../models/Material.php';
        require_once __DIR__ . '/../models/Course.php';
        require_once __DIR__ . '/../models/Forum.php';
        
        $userModel = new User();
        $materialModel = new Material();
        $courseModel = new Course();
        $forumModel = new Forum();

        $userStats = $userModel->getStudyStats($userId);
        
        $allMaterials = $materialModel->getAll($userId);
        $allCourses = $courseModel->getAll(null, $userId);
        $allTopics = $forumModel->getAll();

        $stats = [
            'total_materi' => count($allMaterials), 
            'total_catatan' => $userStats['total_notes'],
            'total_diskusi' => count($allTopics),
            'total_quiz' => $userStats['total_quizzes'],
            'streak' => $userStats['streak']
        ];

        // Get limited recent items
        $recentMaterials = array_slice($allMaterials, 0, 5);
        $recentTopics = array_slice($allTopics, 0, 5);
        $recentQuizzes = $userModel->getQuizHistory($userId);

        $title = 'Dashboard Utama';
        $active = 'dashboard';
        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}

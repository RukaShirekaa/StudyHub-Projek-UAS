<?php

class AdminController {
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "/login");
            exit;
        }
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: " . BASE_URL . "/dashboard");
            exit;
        }
    }

    public function index() {
        $pdo = Database::getInstance();
        
        $stats = [
            'total_users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'total_courses' => $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn(),
            'total_materials' => $pdo->query("SELECT COUNT(*) FROM materials")->fetchColumn(),
            'total_forums' => $pdo->query("SELECT COUNT(*) FROM forums")->fetchColumn(),
        ];

        // Recent users
        $recentUsers = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();
        
        require_once __DIR__ . '/../views/admin/index.php';
    }

    // --- USERS MANAGEMENT ---
    public function users() {
        $pdo = Database::getInstance();
        $users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
        require_once __DIR__ . '/../views/admin/users.php';
    }

    public function updateUserRole() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $role = $_POST['role'] ?? 'student';
            
            // Prevent changing own role
            if ($id == $_SESSION['user_id']) {
                $url = BASE_URL . "/admin/users";
                echo "<script>alert('Anda tidak bisa mengubah role Anda sendiri.'); window.location.href='$url';</script>";
                return;
            }

            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->execute([$role, $id]);
            
            header("Location: " . BASE_URL . "/admin/users");
            exit;
        }
    }

    public function deleteUser() {
        $id = $_GET['id'] ?? 0;
        if ($id && $id != $_SESSION['user_id']) {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
        }
        header("Location: " . BASE_URL . "/admin/users");
        exit;
    }

    // --- COURSES MANAGEMENT ---
    public function courses() {
        $pdo = Database::getInstance();
        // Load all courses with their owner
        $courses = $pdo->query("SELECT c.*, u.name as owner_name, (SELECT COUNT(*) FROM materials WHERE course_id = c.id) as material_count FROM courses c LEFT JOIN users u ON c.user_id = u.id ORDER BY c.name ASC")->fetchAll();
        
        // Load all users to populate the owner dropdown
        $users = $pdo->query("SELECT id, name, email FROM users ORDER BY name ASC")->fetchAll();
        require_once __DIR__ . '/../views/admin/courses.php';
    }

    public function addCourse() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = $_POST['code'] ?? '';
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $user_id = $_POST['user_id'] ?? $_SESSION['user_id']; // Fallback to current admin if not selected

            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("INSERT INTO courses (code, name, description, user_id) VALUES (?, ?, ?, ?)");
            try {
                $stmt->execute([$code, $name, $description, $user_id]);
            } catch (Exception $e) {
                // Ignore duplicate error for now
            }
            header("Location: " . BASE_URL . "/admin/courses");
            exit;
        }
    }

    public function editCourse() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $code = $_POST['code'] ?? '';
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $user_id = $_POST['user_id'] ?? $_SESSION['user_id'];

            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("UPDATE courses SET code = ?, name = ?, description = ?, user_id = ? WHERE id = ?");
            $stmt->execute([$code, $name, $description, $user_id, $id]);
            
            header("Location: " . BASE_URL . "/admin/courses");
            exit;
        }
    }

    public function deleteCourse() {
        $id = $_GET['id'] ?? 0;
        if ($id) {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
            $stmt->execute([$id]);
        }
        header("Location: " . BASE_URL . "/admin/courses");
        exit;
    }

    // --- MATERIALS MANAGEMENT ---
    public function materials() {
        $pdo = Database::getInstance();
        $query = "
            SELECT m.*, c.name as course_name, u.name as uploader_name 
            FROM materials m
            JOIN courses c ON m.course_id = c.id
            JOIN users u ON m.uploaded_by = u.id
            ORDER BY m.created_at DESC
        ";
        $materials = $pdo->query($query)->fetchAll();
        $users = $pdo->query("SELECT id, name, email FROM users ORDER BY name ASC")->fetchAll();
        require_once __DIR__ . '/../views/admin/materials.php';
    }

    public function deleteMaterial() {
        $id = $_GET['id'] ?? 0;
        if ($id) {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("SELECT file_path FROM materials WHERE id = ?");
            $stmt->execute([$id]);
            $material = $stmt->fetch();
            if ($material) {
                $filePath = __DIR__ . '/../' . ltrim($material['file_path'], '/');
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
                $delStmt = $pdo->prepare("DELETE FROM materials WHERE id = ?");
                $delStmt->execute([$id]);
            }
        }
        header("Location: " . BASE_URL . "/admin/materials");
        exit;
    }

    // --- ANNOUNCEMENTS MANAGEMENT ---
    public function announcements() {
        $pdo = Database::getInstance();
        $query = "
            SELECT message, MIN(created_at) as created_at 
            FROM notifications 
            WHERE type = 'announcement' 
            GROUP BY message 
            ORDER BY created_at DESC
        ";
        $announcements = $pdo->query($query)->fetchAll();
        require_once __DIR__ . '/../views/admin/announcements.php';
    }

    public function sendAnnouncement() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $message = $_POST['message'] ?? '';
            $target = $_POST['target'] ?? 'all'; // 'all' or 'students'

            if (!empty($message)) {
                $pdo = Database::getInstance();
                
                if ($target === 'all') {
                    $users = $pdo->query("SELECT id FROM users")->fetchAll(PDO::FETCH_COLUMN);
                } else {
                    $users = $pdo->query("SELECT id FROM users WHERE role = 'student'")->fetchAll(PDO::FETCH_COLUMN);
                }

                if (!empty($users)) {
                    $values = [];
                    $placeholders = [];
                    foreach ($users as $userId) {
                        $values[] = $userId;
                        $values[] = 'announcement';
                        $values[] = $message;
                        $placeholders[] = '(?, ?, ?)';
                    }
                    
                    $sql = "INSERT INTO notifications (user_id, type, message) VALUES " . implode(', ', $placeholders);
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($values);
                }
            }
            header("Location: " . BASE_URL . "/admin/announcements");
            exit;
        }
    }

    public function deleteAnnouncement() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $message = $_POST['message'] ?? '';
            if ($message !== '') {
                $pdo = Database::getInstance();
                $stmt = $pdo->prepare("DELETE FROM notifications WHERE type = 'announcement' AND message = ?");
                $stmt->execute([$message]);
            }
        }
        header("Location: " . BASE_URL . "/admin/announcements");
        exit;
    }
}

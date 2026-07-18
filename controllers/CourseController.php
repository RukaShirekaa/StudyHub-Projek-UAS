<?php
require_once __DIR__ . '/../models/Course.php';

class CourseController {
    public function add() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = $_POST['code'] ?? '';
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            $courseModel = new Course();
            $courseModel->create([
                'code' => $code,
                'name' => $name,
                'description' => $description,
                'user_id' => $_SESSION['user_id']
            ]);

            header('Location: ' . BASE_URL . '/materials');
            exit;
        }
    }

    public function delete() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $id = $_GET['id'] ?? 0;
        if ($id) {
            require_once __DIR__ . '/../models/Material.php';
            $materialModel = new Material();
            // Pass user_id to ensure we only get materials for courses we own
            $materials = $materialModel->getAllByCourse($id, $_SESSION['user_id']);
            
            // Delete physical files
            foreach ($materials as $m) {
                $filePath = __DIR__ . '/../assets/uploads/' . $m['file_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Delete course (will cascade delete materials in DB)
            $courseModel = new Course();
            // Pass user_id to ensure we only delete if we own it
            $courseModel->delete($id, $_SESSION['user_id']);
        }

        header('Location: ' . BASE_URL . '/materials');
        exit;
    }
}

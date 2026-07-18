<?php
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Material.php';

class MaterialController {
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        
        $courseModel = new Course();
        $searchQuery = $_GET['search'] ?? null;
        $sortQuery = $_GET['sort'] ?? 'terbaru';
        $courses = $courseModel->getAll($searchQuery, $_SESSION['user_id'], $sortQuery);
        
        $title = 'Materi Kuliah';
        $active = 'materials';
        require_once __DIR__ . '/../views/materials/index.php';
    }

    public function show($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $courseModel = new Course();
        $materialModel = new Material();
        
        $course = $courseModel->getById($id, $_SESSION['user_id']);
        if (!$course) {
            header('Location: ' . BASE_URL . '/materials');
            exit;
        }
        $materials = $materialModel->getAllByCourse($id, $_SESSION['user_id']);
        
        $title = 'Materi: ' . $course['name'];
        $active = 'materials';
        require_once __DIR__ . '/../views/materials/show.php';
    }

    public function upload() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $course_id = $_POST['course_id'];
            $title = $_POST['title'];
            $description = $_POST['description'];
            $uploaded_by = $_SESSION['user_id'];
            
            if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../assets/uploads/';
                $fileName = time() . '_' . basename($_FILES['file']['name']);
                $uploadFile = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
                    $materialModel = new Material();
                    $materialModel->create([
                        'course_id' => $course_id,
                        'title' => $title,
                        'description' => $description,
                        'file_path' => $fileName,
                        'uploaded_by' => $uploaded_by
                    ]);
                    header('Location: ' . BASE_URL . '/materials/course?id=' . $course_id);
                    exit;
                }
            }
        }
    }

    public function delete() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $id = $_GET['id'] ?? 0;
        if ($id) {
            $materialModel = new Material();
            $material = $materialModel->getById($id, $_SESSION['user_id']);
            if ($material) {
                // Delete physical file
                $filePath = __DIR__ . '/../assets/uploads/' . $material['file_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                
                $materialModel->delete($id, $_SESSION['user_id']);
                header('Location: ' . BASE_URL . '/materials/course?id=' . $material['course_id']);
                exit;
            }
        }
        
        header('Location: ' . BASE_URL . '/materials');
        exit;
    }
}

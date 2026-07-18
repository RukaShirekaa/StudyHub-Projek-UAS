<?php
require_once __DIR__ . '/../models/User.php';

class ProfileController {
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $userModel = new User();
        $user = $userModel->getById($_SESSION['user_id']);
        $stats = $userModel->getStudyStats($_SESSION['user_id']);
        $quizHistory = $userModel->getQuizHistory($_SESSION['user_id']);

        $title = 'Profil Pengguna';
        $active = 'profile';
        require_once __DIR__ . '/../views/profile/index.php';
    }

    public function view() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '/profile');
            exit;
        }

        if ($id == $_SESSION['user_id']) {
            header('Location: ' . BASE_URL . '/profile');
            exit;
        }

        $userModel = new User();
        $user = $userModel->getById($id);
        if (!$user) {
            die("Pengguna tidak ditemukan.");
        }
        
        $stats = $userModel->getStudyStats($id);
        $quizHistory = $userModel->getQuizHistory($id);

        $title = 'Profil Pengguna - ' . htmlspecialchars($user['name']);
        $active = 'profile';
        require_once __DIR__ . '/../views/profile/view.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $prodi = $_POST['prodi'];
            $semester = $_POST['semester'];
            $bio = $_POST['bio'] ?? null;

            $userModel = new User();
            $userModel->updateProfile($_SESSION['user_id'], [
                'name' => $name,
                'prodi' => $prodi,
                'semester' => $semester,
                'bio' => $bio
            ]);

            $_SESSION['user_name'] = $name; // Update session name

            // Handle photo upload
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../assets/uploads/';
                $fileName = time() . '_' . basename($_FILES['photo']['name']);
                $uploadFile = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
                    $userModel->updatePhoto($_SESSION['user_id'], $fileName);
                }
            }

            header('Location: ' . BASE_URL . '/profile');
            exit;
        }
    }

    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user_id'])) {
                header('Location: ' . BASE_URL . '/login');
                exit;
            }

            $old_password = $_POST['old_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            $profileUrl = BASE_URL . '/profile';

            if (strlen($new_password) < 6) {
                echo "<script>alert('Password baru minimal 6 karakter.'); window.location.href='$profileUrl';</script>";
                return;
            }

            if ($new_password !== $confirm_password) {
                echo "<script>alert('Konfirmasi password tidak cocok.'); window.location.href='$profileUrl';</script>";
                return;
            }

            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();

            if ($user && password_verify($old_password, $user['password'])) {
                $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
                $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $updateStmt->execute([$hashedPassword, $_SESSION['user_id']]);
                
                echo "<script>alert('Password berhasil diubah!'); window.location.href='$profileUrl';</script>";
            } else {
                echo "<script>alert('Password lama salah.'); window.location.href='$profileUrl';</script>";
            }
        }
    }
}

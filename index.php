<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE remember_token = ?");
    $stmt->execute([$_COOKIE['remember_token']]);
    $user = $stmt->fetch();
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];

        // Update daily login streak on auto-login via remember-me
        require_once __DIR__ . '/models/User.php';
        (new User())->updateStreak($user['id']);
    }
}

require_once __DIR__ . '/core/Router.php';

$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
if ($basePath === '/') $basePath = '';
define('BASE_URL', $basePath);

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/MaterialController.php';
require_once __DIR__ . '/controllers/CourseController.php';
require_once __DIR__ . '/controllers/QuizController.php';
require_once __DIR__ . '/controllers/AiAssistantController.php';
require_once __DIR__ . '/controllers/SearchController.php';
require_once __DIR__ . '/controllers/ForumController.php';
require_once __DIR__ . '/controllers/NoteController.php';
require_once __DIR__ . '/controllers/ProfileController.php';
require_once __DIR__ . '/controllers/ChatController.php';
require_once __DIR__ . '/controllers/AmbaAiController.php';
require_once __DIR__ . '/controllers/AdminController.php';

$router = new Router();

$router->get('/', function() {
    require_once __DIR__ . '/views/landing.php';
});

// Auth Routes
$router->get('/login', ['AuthController', 'showLogin']);
$router->post('/login', ['AuthController', 'login']);
$router->get('/register', ['AuthController', 'showRegister']);
$router->post('/register', ['AuthController', 'register']);
$router->get('/verify', ['AuthController', 'verifyEmail']);
$router->get('/logout', ['AuthController', 'logout']);
$router->get('/forgot-password', ['AuthController', 'showForgotPassword']);
$router->post('/forgot-password', ['AuthController', 'forgotPassword']);
$router->get('/reset-password', ['AuthController', 'showResetPassword']);
$router->post('/reset-password', ['AuthController', 'resetPassword']);

// Dashboard Route
$router->get('/dashboard', ['DashboardController', 'index']);

// Search Route
$router->get('/search', ['SearchController', 'index']);

// Material & Course Routes
$router->get('/materials', ['MaterialController', 'index']);
$router->get('/materials/course', function() {
    $controller = new MaterialController();
    $controller->show($_GET['id'] ?? 0);
});
$router->post('/course/add', ['CourseController', 'add']);
$router->get('/course/delete', ['CourseController', 'delete']);
$router->post('/materials/upload', ['MaterialController', 'upload']);
$router->get('/materials/delete', ['MaterialController', 'delete']);
// Quiz Routes
$router->get('/quiz', ['QuizController', 'index']);
$router->get('/quiz/generate', ['QuizController', 'generate']);
$router->post('/quiz/generate', ['QuizController', 'generate']);
$router->get('/quiz/take', ['QuizController', 'take']);
$router->post('/quiz/submit', ['QuizController', 'submit']);
$router->get('/quiz/result', ['QuizController', 'result']);

// AI Assistant Routes
$router->get('/assistant', ['AiAssistantController', 'index']);
$router->post('/assistant/chat', ['AiAssistantController', 'chat']);
$router->get('/assistant/clear', ['AiAssistantController', 'clear']);

// Forum Routes
$router->get('/forum', ['ForumController', 'index']);
$router->post('/forum/create', ['ForumController', 'create']);
$router->get('/forum/show', ['ForumController', 'show']);
$router->post('/forum/reply', ['ForumController', 'reply']);
$router->get('/forum/delete', ['ForumController', 'delete']);
// Notes Routes
$router->get('/notes', ['NoteController', 'index']);
$router->get('/notes/create', ['NoteController', 'create']);
$router->post('/notes/create', ['NoteController', 'create']);
$router->get('/notes/edit', ['NoteController', 'edit']);
$router->post('/notes/update', ['NoteController', 'update']);
$router->get('/notes/delete', ['NoteController', 'delete']);
$router->post('/notes/generate_ai', ['NoteController', 'generateAi']);
$router->get('/notes/export_pdf', ['NoteController', 'exportPdf']);

// Profile Routes
$router->get('/profile', ['ProfileController', 'index']);
$router->get('/profile/view', ['ProfileController', 'view']);
$router->post('/profile/update', ['ProfileController', 'update']);
$router->post('/profile/change-password', ['ProfileController', 'changePassword']);

// Global Chat Routes
$router->get('/chat/messages', ['ChatController', 'getMessages']);
$router->post('/chat/send', ['ChatController', 'sendMessage']);

// Notification Routes
$router->post('/notifications/read', function() {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        return;
    }
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = TRUE WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    echo json_encode(['status' => 'success']);
});

// Amba AI Chat Routes
$router->get('/amba/messages', ['AmbaAiController', 'messages']);
$router->post('/amba/send', ['AmbaAiController', 'send']);
$router->get('/amba/clear', ['AmbaAiController', 'clear']);

// Admin Routes
$router->get('/admin', ['AdminController', 'index']);
$router->get('/admin/users', ['AdminController', 'users']);
$router->post('/admin/users/role', ['AdminController', 'updateUserRole']);
$router->get('/admin/users/delete', ['AdminController', 'deleteUser']);
$router->get('/admin/courses', ['AdminController', 'courses']);
$router->post('/admin/courses/add', ['AdminController', 'addCourse']);
$router->post('/admin/courses/edit', ['AdminController', 'editCourse']);
$router->get('/admin/courses/delete', ['AdminController', 'deleteCourse']);
$router->get('/admin/materials', ['AdminController', 'materials']);
$router->get('/admin/materials/delete', ['AdminController', 'deleteMaterial']);
$router->get('/admin/announcements', ['AdminController', 'announcements']);
$router->post('/admin/announcements/send', ['AdminController', 'sendAnnouncement']);

$router->resolve();

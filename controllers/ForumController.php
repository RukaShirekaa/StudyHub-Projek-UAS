<?php
require_once __DIR__ . '/../models/Forum.php';
require_once __DIR__ . '/../models/Course.php';

class ForumController {
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $forumModel = new Forum();
        $currentTab = $_GET['tab'] ?? 'semua';
        $searchQuery = $_GET['search'] ?? null;
        $forums = $forumModel->getAll($currentTab, $_SESSION['user_id'], $searchQuery);

        $courseModel = new Course();
        $courses = $courseModel->getAll(null, $_SESSION['user_id']);

        $title = 'Forum Diskusi';
        $active = 'forum';
        require_once __DIR__ . '/../views/forum/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $course_id = $_POST['course_id'];
            $title = $_POST['title'];
            $content = $_POST['content'];
            
            $forumModel = new Forum();
            $forumId = $forumModel->create([
                'course_id' => $course_id,
                'user_id' => $_SESSION['user_id'],
                'title' => $title,
                'content' => $content
            ]);
            
            header('Location: ' . BASE_URL . '/forum/show?id=' . $forumId);
            exit;
        }
    }

    public function show() {
        if (!isset($_GET['id'])) {
            header('Location: ' . BASE_URL . '/forum');
            exit;
        }
        
        $forumId = $_GET['id'];
        $forumModel = new Forum();
        $forum = $forumModel->getById($forumId);
        if(!$forum) die("Diskusi tidak ditemukan.");
        
        $replies = $forumModel->getReplies($forumId);

        $title = htmlspecialchars($forum['title']);
        $active = 'forum';
        require_once __DIR__ . '/../views/forum/show.php';
    }

    public function reply() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $forum_id = $_POST['forum_id'];
            $content = $_POST['content'];
            
            $forumModel = new Forum();
            $forumModel->addReply([
                'forum_id' => $forum_id,
                'user_id' => $_SESSION['user_id'],
                'content' => $content
            ]);
            
            header('Location: ' . BASE_URL . '/forum/show?id=' . $forum_id);
            exit;
        }
    }

    public function delete() {
        if (!isset($_GET['id'])) {
            header('Location: ' . BASE_URL . '/forum');
            exit;
        }
        $forumId = $_GET['id'];
        $forumModel = new Forum();
        $forum = $forumModel->getById($forumId);
        
        // Verify if forum exists and user is the author
        if ($forum && $forum['user_id'] == $_SESSION['user_id']) {
            $forumModel->delete($forumId);
        }
        header('Location: ' . BASE_URL . '/forum');
        exit;
    }
}

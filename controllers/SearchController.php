<?php

require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Forum.php';
require_once __DIR__ . '/../models/Note.php';

class SearchController {
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $query = $_GET['q'] ?? '';
        
        $courses = [];
        $forums = [];
        $notes = [];

        if (trim($query) !== '') {
            $courseModel = new Course();
            $courses = $courseModel->getAll($query, $_SESSION['user_id']);
            
            $forumModel = new Forum();
            $forums = $forumModel->getAll('semua', null, $query);
            
            $noteModel = new Note();
            $notes = $noteModel->getAllByUser($_SESSION['user_id'], $query);
        }

        $title = 'Hasil Pencarian';
        $active = ''; // no specific sidebar menu active

        require_once __DIR__ . '/../views/search/index.php';
    }
}

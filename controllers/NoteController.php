<?php
require_once __DIR__ . '/../models/Note.php';
require_once __DIR__ . '/../models/Course.php';

class NoteController {
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $noteModel = new Note();
        
        $search = $_GET['search'] ?? '';
        $notes = $noteModel->getAllByUser($_SESSION['user_id'], $search);
        
        // SQL filter takes care of search
        // Group by course
        $groupedNotes = [];
        foreach ($notes as $n) {
            $groupedNotes[$n['course_name']][] = $n;
        }

        $courseModel = new Course();
        $courses = $courseModel->getAll(null, $_SESSION['user_id']);

        $title = 'Catatan Saya';
        $active = 'notes';
        require_once __DIR__ . '/../views/notes/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $course_id = $_POST['course_id'];
            $title = $_POST['title'];
            $content = $_POST['content'];
            
            $noteModel = new Note();
            $noteId = $noteModel->create([
                'user_id' => $_SESSION['user_id'],
                'course_id' => $course_id,
                'title' => $title,
                'content' => $content
            ]);
            
            header('Location: ' . BASE_URL . '/notes/edit?id=' . $noteId);
            exit;
        }

        $noteModel = new Note();
        $notes = $noteModel->getAllByUser($_SESSION['user_id']);
        
        $groupedNotes = [];
        foreach ($notes as $n) {
            $groupedNotes[$n['course_name']][] = $n;
        }

        $courseModel = new Course();
        $courses = $courseModel->getAll(null, $_SESSION['user_id']);

        require_once __DIR__ . '/../models/Material.php';
        $materialModel = new Material();
        // Since we want all materials for this user, and getAll needs search/user_id/sort, we'll just query it directly
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT m.id, m.title, c.name as course_name FROM materials m JOIN courses c ON m.course_id = c.id WHERE m.uploaded_by = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $materials = $stmt->fetchAll();

        $title = 'Buat Catatan';
        $active = 'notes';
        require_once __DIR__ . '/../views/notes/create.php';
    }

    public function edit() {
        if (!isset($_GET['id'])) {
            header('Location: ' . BASE_URL . '/notes');
            exit;
        }
        
        $noteId = $_GET['id'];
        $noteModel = new Note();
        $note = $noteModel->getById($noteId, $_SESSION['user_id']);
        if(!$note) die("Catatan tidak ditemukan.");
        
        // Fetch all notes for the sidebar (split screen layout)
        $notes = $noteModel->getAllByUser($_SESSION['user_id']);
        $groupedNotes = [];
        foreach ($notes as $n) {
            $groupedNotes[$n['course_name']][] = $n;
        }

        $courseModel = new Course();
        $courses = $courseModel->getAll(null, $_SESSION['user_id']);
        
        require_once __DIR__ . '/../models/Material.php';
        $materialModel = new Material();
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT m.id, m.title, c.name as course_name FROM materials m JOIN courses c ON m.course_id = c.id WHERE m.uploaded_by = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $materials = $stmt->fetchAll();

        $title = 'Edit Catatan';
        $active = 'notes';
        require_once __DIR__ . '/../views/notes/edit.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $title = $_POST['title'];
            $content = $_POST['content'];
            
            $noteModel = new Note();
            $noteModel->update($id, $_SESSION['user_id'], [
                'title' => $title,
                'content' => $content
            ]);
            
            header('Location: ' . BASE_URL . '/notes');
            exit;
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $noteModel = new Note();
            $noteModel->delete($_GET['id'], $_SESSION['user_id']);
        }
        header('Location: ' . BASE_URL . '/notes');
        exit;
    }

    public function generateAi() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
            return;
        }

        $material_id = $_POST['material_id'] ?? null;
        if (!$material_id) {
            http_response_code(400);
            echo json_encode(['error' => 'Material ID is required']);
            return;
        }

        require_once __DIR__ . '/../models/Material.php';
        $materialModel = new Material();
        $material = $materialModel->getById($material_id, $_SESSION['user_id']);
        
        if (!$material) {
            http_response_code(404);
            echo json_encode(['error' => 'Material not found']);
            return;
        }

        $filePath = __DIR__ . '/../assets/uploads/' . $material['file_path'];
        if (!file_exists($filePath)) {
            http_response_code(404);
            echo json_encode(['error' => 'File physical not found']);
            return;
        }

        try {
            require_once __DIR__ . '/../vendor/autoload.php';
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();
            $text = substr($text, 0, 15000); // Limit text to prevent payload too large

            $apiKey = $_SERVER['GROQ_API_KEY'] ?? getenv('GROQ_API_KEY');
            if (!$apiKey) {
                throw new Exception('API Key Groq tidak ditemukan dalam konfigurasi.');
            }
            
            $prompt = "Buatkan catatan belajar yang terstruktur, rapi, dan komprehensif berdasarkan materi berikut. Fokus pada poin-poin penting, definisi, dan konsep utama. Jangan menambahkan teks pengantar seperti 'Berikut adalah catatannya'. FORMAT WAJIB: Gunakan HTML murni (seperti <h3>, <p>, <strong>, <ul>, <li>). Jangan gunakan markdown (** atau #). Jangan gunakan tag <html>, <head>, atau <body>, cukup elemen isinya saja. Materi: \n\n" . $text;

            $data = [
                "model" => "llama-3.3-70b-versatile",
                "messages" => [
                    [
                        "role" => "system",
                        "content" => "Anda adalah asisten pembuat catatan pintar."
                    ],
                    [
                        "role" => "user",
                        "content" => $prompt
                    ]
                ],
                "temperature" => 0.5
            ];

            $ch = curl_init("https://api.groq.com/openai/v1/chat/completions");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $apiKey",
                "Content-Type: application/json"
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);
            
            if (isset($result['choices'][0]['message']['content'])) {
                $generatedText = $result['choices'][0]['message']['content'];
                echo json_encode(['success' => true, 'text' => trim($generatedText)]);
            } else {
                echo json_encode(['error' => 'Gagal menghasilkan catatan dari AI.', 'details' => $result]);
            }
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function exportPdf() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $id = $_GET['id'] ?? 0;
        if (!$id) {
            header('Location: ' . BASE_URL . '/notes');
            exit;
        }

        $noteModel = new Note();
        $note = $noteModel->getById($id, $_SESSION['user_id']);
        if (!$note) {
            header('Location: ' . BASE_URL . '/notes');
            exit;
        }
        
        require_once __DIR__ . '/../models/Course.php';
        $courseModel = new Course();
        $course = $courseModel->getById($note['course_id'], $_SESSION['user_id']);
        $courseName = $course ? $course['name'] : 'Mata Kuliah Tidak Diketahui';

        // Include dompdf
        require_once __DIR__ . '/../vendor/autoload.php';
        $dompdf = new \Dompdf\Dompdf();
        
        // Simple HTML structure
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: sans-serif; line-height: 1.6; color: #333; }
                h1 { color: #4338ca; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; margin-bottom: 5px; }
                .meta { color: #6b7280; font-size: 0.9em; margin-bottom: 30px; }
                .content { font-size: 11pt; }
                .content img { max-width: 100%; height: auto; }
            </style>
        </head>
        <body>
            <h1>' . htmlspecialchars($note['title']) . '</h1>
            <div class="meta">
                Mata Kuliah: ' . htmlspecialchars($courseName) . '<br>
                Terakhir diperbarui: ' . date('d M Y, H:i', strtotime($note['updated_at'])) . '
            </div>
            <div class="content">' . $note['content'] . '</div>
        </body>
        </html>';

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Catatan_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $note['title']) . '.pdf';
        $dompdf->stream($filename, ["Attachment" => true]);
    }
}

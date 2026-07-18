<?php
require_once __DIR__ . '/../models/Material.php';
use Smalot\PdfParser\Parser;

class AiAssistantController {
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT m.id, m.title, c.name as course_name FROM materials m JOIN courses c ON m.course_id = c.id WHERE m.uploaded_by = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $materials = $stmt->fetchAll();

        // Check if a material is selected to show chat
        $material_id = $_GET['material_id'] ?? null;
        $chatHistory = [];
        
        if ($material_id) {
            $stmt = $pdo->prepare("SELECT * FROM ai_chat_history WHERE user_id = ? AND material_id = ? ORDER BY created_at ASC");
            $stmt->execute([$_SESSION['user_id'], $material_id]);
            $chatHistory = $stmt->fetchAll();
        }

        // Fetch recent materials the user has chatted about
        $stmt = $pdo->prepare("
            SELECT m.id, m.title, c.name as course_name, 
                   MAX(ach.created_at) as last_chat
            FROM materials m
            JOIN courses c ON m.course_id = c.id
            JOIN ai_chat_history ach ON ach.material_id = m.id
            WHERE ach.user_id = ?
            GROUP BY m.id, m.title, c.name
            ORDER BY last_chat DESC
            LIMIT 5
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $recentChats = $stmt->fetchAll();

        $title = 'AI Study Assistant';
        $active = 'assistant';
        require_once __DIR__ . '/../views/assistant/index.php';
    }

    public function chat() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $material_id = $_POST['material_id'];
            $user_message = $_POST['message'];
            
            if (!$material_id || trim($user_message) === '') {
                header('Location: ' . BASE_URL . '/assistant');
                exit;
            }

            $materialModel = new Material();
            $material = $materialModel->getById($material_id, $_SESSION['user_id']);
            if(!$material) die("Materi tidak valid.");

            // Get PDF Context
            $pdfPath = __DIR__ . '/../assets/uploads/' . $material['file_path'];
            $textContext = "";
            if (file_exists($pdfPath)) {
                $parser = new Parser();
                $pdf = $parser->parseFile($pdfPath);
                $textContext = substr($pdf->getText(), 0, 10000); // Send first 10,000 chars as context
            }

            // Save user message to DB
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("INSERT INTO ai_chat_history (user_id, material_id, role, message) VALUES (?, ?, 'user', ?)");
            $stmt->execute([$_SESSION['user_id'], $material_id, $user_message]);

            // Call Groq API
            $aiResponse = $this->callGroqApi($textContext, $user_message);

            // Save AI response to DB
            $stmt = $pdo->prepare("INSERT INTO ai_chat_history (user_id, material_id, role, message) VALUES (?, ?, 'ai', ?)");
            $stmt->execute([$_SESSION['user_id'], $material_id, $aiResponse]);
            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                echo json_encode(['success' => true, 'ai_message' => $aiResponse]);
                exit;
            }

            header('Location: ' . BASE_URL . '/assistant?material_id=' . $material_id);
            exit;
        }
    }

    private function callGroqApi($context, $message) {
        $apiKey = $_SERVER['GROQ_API_KEY'] ?? getenv('GROQ_API_KEY');
        $url = "https://api.groq.com/openai/v1/chat/completions";

        $data = [
            "model" => "llama-3.3-70b-versatile",
            "messages" => [
                [
                    "role" => "system",
                    "content" => "Anda adalah Asisten Belajar AI. Jawab pertanyaan mahasiswa HANYA berdasarkan materi PDF berikut. Jika pertanyaannya di luar materi, beritahu bahwa Anda tidak dapat menjawabnya.\n\nATURAN EKSPOR DOKUMEN SANGAT KETAT:\n1. JANGAN PERNAH menambahkan kode ekspor apa pun jika pengguna hanya berterima kasih, bertanya biasa, atau mengobrol santai.\n2. HANYA JIKA pengguna SECARA EKSPLISIT meminta rangkuman/jawaban untuk dijadikan file PDF atau ingin di-download, tambahkan string `[DOWNLOAD:PDF]` di akhir jawaban Anda.\n3. HANYA JIKA pengguna meminta file Word/Document, tambahkan `[DOWNLOAD:DOC]` di akhir jawaban Anda.\n\nMateri PDF:\n$context"
                ],
                [
                    "role" => "user",
                    "content" => $message
                ]
            ],
            "temperature" => 0.5
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $apiKey",
            "Content-Type: application/json"
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $responseData = json_decode($response, true);
            if (isset($responseData['choices'][0]['message']['content'])) {
                return $responseData['choices'][0]['message']['content'];
            }
        }
        return "Maaf, terjadi kesalahan saat menghubungi AI.";
    }

    public function clear() {
        if (isset($_GET['material_id']) && isset($_SESSION['user_id'])) {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("DELETE FROM ai_chat_history WHERE user_id = ? AND material_id = ?");
            $stmt->execute([$_SESSION['user_id'], $_GET['material_id']]);
        }
        header('Location: ' . BASE_URL . '/assistant');
        exit;
    }
}

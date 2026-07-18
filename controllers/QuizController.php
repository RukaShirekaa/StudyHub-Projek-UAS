<?php
require_once __DIR__ . '/../models/Material.php';
use Smalot\PdfParser\Parser;

class QuizController {
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT m.id, m.title, c.name as course_name FROM materials m JOIN courses c ON m.course_id = c.id WHERE m.uploaded_by = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $materials = $stmt->fetchAll();

        require_once __DIR__ . '/../models/User.php';
        $userModel = new User();
        $recentQuizzes = $userModel->getQuizHistory($_SESSION['user_id']);

        $title = 'Latihan & Quiz';
        $active = 'quiz';
        require_once __DIR__ . '/../views/quiz/index.php';
    }

    public function generate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $material_id = $_POST['material_id'];
            $total_questions = (int) $_POST['total_questions'];
            $difficulty = $_POST['difficulty'];
            
            $materialModel = new Material();
            $material = $materialModel->getById($material_id, $_SESSION['user_id']);
            
            if (!$material) {
                die("Materi tidak ditemukan.");
            }

            $pdfPath = __DIR__ . '/../assets/uploads/' . $material['file_path'];
            if (!file_exists($pdfPath)) {
                die("File PDF tidak ditemukan.");
            }

            // Extract text from PDF
            $parser = new Parser();
            $pdf = $parser->parseFile($pdfPath);
            $text = $pdf->getText();
            
            // Truncate text to avoid token limits (e.g. first 5000 chars)
            $text = substr($text, 0, 15000);

            // Call Groq API (with cache + fallback)
            $cacheKey = md5($text . '|' . $total_questions . '|' . $difficulty);
            $quizJson = $this->cacheGet($cacheKey);
            if (!$quizJson) {
                $quizJson = $this->callGroqApiForQuiz($text, $total_questions, $difficulty);
                if ($quizJson) {
                    $this->cacheSet($cacheKey, $quizJson);
                    $this->logGroq("SUCCESS: cached result for key $cacheKey");
                } else {
                    $this->logGroq("API_FAIL: falling back to heuristic generator for key $cacheKey");
                    // fallback to heuristic generator to avoid blocking users when API limit reached
                    $quizJson = $this->generateHeuristicQuiz($text, $total_questions, $difficulty);
                }
            } else {
                $this->logGroq("CACHE_HIT: key $cacheKey");
            }

            $quizData = json_decode($quizJson, true);
            if (!$quizData || !isset($quizData['questions'])) {
                // simple fallback logic to retry or show error
                die("Format response AI tidak valid. Raw: " . htmlspecialchars($quizJson));
            }

            // Force slice to exact requested amount if AI generated more
            if (count($quizData['questions']) > $total_questions) {
                $quizData['questions'] = array_slice($quizData['questions'], 0, $total_questions);
            }

            // Save Quiz to DB
            $pdo = Database::getInstance();
            $pdo->beginTransaction();
            try {
                $actual_total = count($quizData['questions']);
                $stmt = $pdo->prepare("INSERT INTO quizzes (material_id, user_id, total_questions, difficulty) VALUES (?, ?, ?, ?)");
                $stmt->execute([$material_id, $_SESSION['user_id'], $actual_total, $difficulty]);
                $quiz_id = $pdo->lastInsertId();

                $stmtQ = $pdo->prepare("INSERT INTO quiz_questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, explanation) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                
                foreach ($quizData['questions'] as $q) {
                    $stmtQ->execute([
                        $quiz_id, 
                        $q['question'], 
                        $q['options']['A'], 
                        $q['options']['B'], 
                        $q['options']['C'], 
                        $q['options']['D'], 
                        $q['correct_answer'], 
                        $q['explanation']
                    ]);
                }
                $pdo->commit();
                
                header('Location: ' . BASE_URL . '/quiz/take?id=' . $quiz_id);
                exit;
            } catch (Exception $e) {
                $pdo->rollBack();
                die("Database Error: " . $e->getMessage());
            }
        }
    }

    private function callGroqApiForQuiz($text, $total, $difficulty) {
        $apiKey = $_SERVER['GROQ_API_KEY'] ?? getenv('GROQ_API_KEY');
        if (empty($apiKey)) {
            $this->logGroq('NO_API_KEY');
            return null;
        }
        $url = "https://api.groq.com/openai/v1/chat/completions";

        $difficultyInstructions = "";
        if ($difficulty === 'easy') {
            $difficultyInstructions = "- Fokus pada pertanyaan yang menguji ingatan dasar, definisi konsep, dan fakta langsung dari materi.\n- Gunakan bahasa yang sederhana dengan pilihan jawaban salah (distractor) yang sangat jelas perbedaannya.";
        } elseif ($difficulty === 'medium') {
            $difficultyInstructions = "- Fokus pada pemahaman konsep yang lebih dalam dan penerapan informasi pada situasi atau contoh sederhana.\n- Pilihan jawaban salah (distractor) harus sedikit mengecoh untuk menguji pemahaman logis peserta, bukan sekadar hafalan.";
        } elseif ($difficulty === 'hard') {
            $difficultyInstructions = "- Fokus pada soal bergaya penalaran kritis (HOTS - Higher Order Thinking Skills), analisis mendalam, evaluasi, atau pemecahan masalah (studi kasus).\n- Pilihan jawaban salah (distractor) harus sangat logis dan mirip dengan jawaban benar untuk menguji ketelitian dan analisis tingkat tinggi peserta.";
        }

        $prompt = "Buatkan TEPAT $total soal pilihan ganda (A, B, C, D) berdasarkan materi berikut:\n\n$text\n\nInstruksi Khusus Tingkat Kesulitan ('$difficulty'):\n$difficultyInstructions\n\nPENTING: Kamu WAJIB menghasilkan jumlah soal persis sebanyak $total, tidak boleh kurang.\n\nKeluarkan hasil HANYA dalam format JSON dengan struktur persis seperti ini, tanpa ada teks sebelum atau sesudahnya:\n{\n  \"questions\": [\n    {\n      \"question\": \"Pertanyaan...\",\n      \"options\": {\n        \"A\": \"Pilihan A\",\n        \"B\": \"Pilihan B\",\n        \"C\": \"Pilihan C\",\n        \"D\": \"Pilihan D\"\n      },\n      \"correct_answer\": \"A\",\n      \"explanation\": \"Penjelasan mengapa A benar...\"\n    }\n  ]\n}";

        $data = [
            "model" => "llama-3.3-70b-versatile", // Use a powerful 70B Groq model
            "messages" => [
                [
                    "role" => "system",
                    "content" => "Anda adalah AI pembuat kuis pendidikan yang selalu merespons HANYA dengan JSON valid tanpa markdown formatting (tanpa ```json)."
                ],
                [
                    "role" => "user",
                    "content" => $prompt
                ]
            ],
            "temperature" => 0.5,
            "response_format" => [ "type" => "json_object" ]
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
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            die("cURL Error: " . htmlspecialchars($err));
        }

        if ($response) {
            $responseData = json_decode($response, true);
            if (isset($responseData['choices'][0]['message']['content'])) {
                $content = $responseData['choices'][0]['message']['content'];

                // If content is already an array/object, try to return JSON string
                if (is_array($content) || is_object($content)) {
                    $jsonString = json_encode($content);
                    if ($jsonString) return $jsonString;
                }

                // If content is a string, attempt to decode. If invalid, try to extract JSON substring.
                if (is_string($content)) {
                    $trimmed = trim($content);
                    $decoded = json_decode($trimmed, true);
                    if ($decoded !== null) {
                        return $trimmed;
                    }

                    // Attempt to find first '{' and last '}' and decode substring
                    $first = strpos($trimmed, '{');
                    $last = strrpos($trimmed, '}');
                    if ($first !== false && $last !== false && $last > $first) {
                        $maybe = substr($trimmed, $first, $last - $first + 1);
                        $decoded2 = json_decode($maybe, true);
                        if ($decoded2 !== null) {
                            return $maybe;
                        }
                    }
                }

                // If we reach here, log invalid JSON content and return null for fallback
                $this->logGroq('INVALID_JSON_CONTENT: ' . (is_scalar($content) ? $content : json_encode($content)));
                return null;
            } else {
                $this->logGroq('API_RESPONSE_INVALID: ' . $response);
                return null;
            }
        }
        $this->logGroq('EMPTY_RESPONSE');
        return null;
    }

    // Simple file-based cache helpers
    private function cacheGet($key) {
        $dir = __DIR__ . '/../storage';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $cacheFile = $dir . '/groq_cache.json';
        if (!file_exists($cacheFile)) return null;
        $all = json_decode(file_get_contents($cacheFile), true) ?? [];
        return $all[$key] ?? null;
    }

    private function cacheSet($key, $value) {
        $dir = __DIR__ . '/../storage';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $cacheFile = $dir . '/groq_cache.json';
        $all = file_exists($cacheFile) ? (json_decode(file_get_contents($cacheFile), true) ?? []) : [];
        $all[$key] = $value;
        file_put_contents($cacheFile, json_encode($all, JSON_PRETTY_PRINT));
    }

    private function logGroq($msg) {
        $dir = __DIR__ . '/../storage';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $file = $dir . '/groq.log';
        $line = date('Y-m-d H:i:s') . " - " . $msg . "\n";
        file_put_contents($file, $line, FILE_APPEND);
    }

    // Very simple heuristic quiz generator when API is unavailable
    private function generateHeuristicQuiz($text, $total, $difficulty) {
        // Ensure we have a UTF-8 string
        if (!is_string($text)) $text = (string)$text;
        // Try to normalize encoding to UTF-8 to avoid preg_split failures on binary content
        $text = @mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        $clean = strip_tags($text);

        // Split into sentences; if preg_split fails, fallback to newline or period splitting
        $sentences = @preg_split('/(?<=[.?!])\s+/u', $clean);
        if ($sentences === false) {
            $sentences = @preg_split('/[\r\n]+/', $clean);
        }
        if ($sentences === false || empty($sentences)) {
            // final fallback: treat entire text as single sentence if non-empty
            $sentences = $clean !== '' ? [$clean] : [];
        }

        $candidates = array_values(array_filter(array_map('trim', $sentences)));
        if (empty($candidates)) {
            // fallback minimal dummy
            $questions = [];
            for ($i = 0; $i < $total; $i++) {
                $questions[] = [
                    'question' => 'Soal sementara: tidak ada teks materi.',
                    'options' => ['A' => 'Ops1', 'B' => 'Ops2', 'C' => 'Ops3', 'D' => 'Ops4'],
                    'correct_answer' => 'A',
                    'explanation' => 'Ini adalah soal sementara karena materi tidak dapat diproses.'
                ];
            }
            return json_encode(['questions' => $questions]);
        }

        // Build pool of words for distractors
        $wordPool = [];
        foreach ($candidates as $s) {
            foreach (preg_split('/[^\p{L}]+/u', $s) as $w) {
                $w = trim($w);
                if (mb_strlen($w) >= 4) $wordPool[] = $w;
            }
        }
        $wordPool = array_values(array_unique($wordPool));

        $questions = [];
        $cid = 0;
        while (count($questions) < $total && $cid < count($candidates)) {
            $s = $candidates[$cid++];
            // choose a word to blank
            $words = preg_split('/[^\p{L}]+/u', $s);
            $words = array_values(array_filter(array_map('trim', $words)));
            if (empty($words)) continue;
            // pick a candidate answer word preferably long
            $ans = null;
            foreach ($words as $w) {
                if (mb_strlen($w) >= 5) { $ans = $w; break; }
            }
            if (!$ans) $ans = $words[array_rand($words)];

            $questionText = str_replace($ans, '_____', $s);
            if ($questionText === $s) $questionText = $s; // if replacement failed, keep original

            // build options
            $opts = [];
            $opts['A'] = $ans;
            // add 3 distractors
            $distractors = [];
            $pool = $wordPool ?: [$ans . 'a', $ans . 'b', $ans . 'c'];
            while (count($distractors) < 3) {
                $cand = $pool[array_rand($pool)];
                if ($cand === $ans) continue;
                if (in_array($cand, $distractors)) continue;
                $distractors[] = $cand;
            }
            $labels = ['B','C','D'];
            foreach ($labels as $i => $lab) {
                $opts[$lab] = $distractors[$i];
            }

            $questions[] = [
                'question' => mb_substr(trim($questionText), 0, 500),
                'options' => $opts,
                'correct_answer' => 'A',
                'explanation' => 'Jawaban diambil langsung dari materi.'
            ];
        }

        // if not enough generated, pad with simple placeholders
        while (count($questions) < $total) {
            $questions[] = [
                'question' => 'Soal tambahan sementara.',
                'options' => ['A' => 'Ops1', 'B' => 'Ops2', 'C' => 'Ops3', 'D' => 'Ops4'],
                'correct_answer' => 'A',
                'explanation' => 'Soal tambahan karena kuota soal belum terpenuhi.'
            ];
        }

        return json_encode(['questions' => $questions]);
    }

    public function take() {
        if (!isset($_GET['id'])) {
            header('Location: ' . BASE_URL . '/quiz');
            exit;
        }
        $quiz_id = $_GET['id'];
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND user_id = ?");
        $stmt->execute([$quiz_id, $_SESSION['user_id']]);
        $quiz = $stmt->fetch();
        if(!$quiz) die("Quiz tidak ditemukan.");

        $stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ?");
        $stmt->execute([$quiz_id]);
        $questions = $stmt->fetchAll();

        $title = 'Kerjakan Quiz';
        $active = 'quiz';
        require_once __DIR__ . '/../views/quiz/take.php';
    }

    public function submit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $quiz_id = $_POST['quiz_id'];
            $answers = $_POST['answers'] ?? [];
            
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ?");
            $stmt->execute([$quiz_id]);
            $questions = $stmt->fetchAll();
            
            $correct = 0;
            foreach ($questions as $q) {
                if (isset($answers[$q['id']]) && $answers[$q['id']] === $q['correct_answer']) {
                    $correct++;
                }
            }
            
            $score = ($correct / count($questions)) * 100;
            $answersJson = json_encode($answers);
            
            $stmt = $pdo->prepare("INSERT INTO quiz_results (quiz_id, user_id, score, user_answers) VALUES (?, ?, ?, ?)");
            $stmt->execute([$quiz_id, $_SESSION['user_id'], $score, $answersJson]);
            $result_id = $pdo->lastInsertId();
            
            // Store answers in session as fallback, though no longer strictly needed
            $_SESSION['quiz_answers'][$result_id] = $answers;
            
            header('Location: ' . BASE_URL . '/quiz/result?id=' . $result_id);
            exit;
        }
    }

    public function result() {
        if (!isset($_GET['id'])) {
            header('Location: ' . BASE_URL . '/quiz');
            exit;
        }
        $result_id = $_GET['id'];
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT r.*, q.total_questions, m.title FROM quiz_results r JOIN quizzes q ON r.quiz_id = q.id JOIN materials m ON q.material_id = m.id WHERE r.id = ?");
        $stmt->execute([$result_id]);
        $result = $stmt->fetch();
        
        $stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ?");
        $stmt->execute([$result['quiz_id']]);
        $questions = $stmt->fetchAll();

        // Load user answers from database if available, fallback to session
        $user_answers = !empty($result['user_answers']) ? json_decode($result['user_answers'], true) : ($_SESSION['quiz_answers'][$result_id] ?? []);

        $title = 'Hasil Quiz';
        $active = 'quiz';
        require_once __DIR__ . '/../views/quiz/result.php';
    }
}

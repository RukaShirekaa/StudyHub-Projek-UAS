<?php
class AmbaAiController {
    public function messages() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Not authenticated']);
            exit;
        }

        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM amba_chat_history WHERE user_id = ? ORDER BY created_at ASC");
        $stmt->execute([$_SESSION['user_id']]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($messages);
        exit;
    }

    public function send() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Not authenticated']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_message = $_POST['message'] ?? '';
            
            if (trim($user_message) === '') {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Empty message']);
                exit;
            }

            $pdo = Database::getInstance();
            
            // Save user message to DB
            $stmt = $pdo->prepare("INSERT INTO amba_chat_history (user_id, role, message) VALUES (?, 'user', ?)");
            $stmt->execute([$_SESSION['user_id'], $user_message]);
            
            // Call Groq API for Amba AI
            $aiResponse = $this->callAmbaAiApi($user_message, $_SESSION['user_id']);

            // Parse for redirect command
            $redirectUrl = null;
            if (preg_match('/\[REDIRECT:(.+?)\]/', $aiResponse, $matches)) {
                $redirectUrl = trim($matches[1]);
                // Clean the message to remove the redirect token
                $aiResponse = trim(preg_replace('/\[REDIRECT:.+?\]/', '', $aiResponse));
                if (empty($aiResponse)) {
                    $aiResponse = "Tentu! Saya akan langsung mengarahkan Anda ke sana.";
                }
            }

            // Parse for play YouTube command
            $playYt = null;
            if (preg_match('/\[PLAY_YT:(.+?)\]/', $aiResponse, $matches)) {
                $query = trim($matches[1]);
                $aiResponse = trim(preg_replace('/\[PLAY_YT:.+?\]/', '', $aiResponse));
                if (empty($aiResponse)) {
                    $aiResponse = "Tentu! Saya akan memutarkan lagu tersebut untuk Anda di YouTube.";
                }
                
                // Fetch YouTube search results and get the first video ID
                $ytUrl = "https://www.youtube.com/results?search_query=" . urlencode($query);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $ytUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
                $html = curl_exec($ch);
                curl_close($ch);
                
                if ($html && preg_match('/"videoId":"([a-zA-Z0-9_-]{11})"/', $html, $vidMatches)) {
                    $playYt = "https://www.youtube.com/watch?v=" . $vidMatches[1];
                } else {
                    $playYt = $ytUrl; // Fallback to search page
                }
            }

            // Save AI response to DB
            $stmt = $pdo->prepare("INSERT INTO amba_chat_history (user_id, role, message) VALUES (?, 'ai', ?)");
            $stmt->execute([$_SESSION['user_id'], $aiResponse]);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'redirect' => $redirectUrl, 'play_yt' => $playYt]);
            exit;
        }
    }

    private function callAmbaAiApi($message, $userId) {
        $apiKey = $_SERVER['GROQ_API_KEY'] ?? getenv('GROQ_API_KEY');
        $url = "https://api.groq.com/openai/v1/chat/completions";

        // Get past context (last 5 messages)
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT role, message FROM amba_chat_history WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
        $stmt->execute([$userId]);
        $history = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));

        // Get user's courses for context
        $stmt = $pdo->prepare("SELECT id, name FROM courses WHERE user_id = ?");
        $stmt->execute([$userId]);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $courseContext = "";
        if (!empty($courses)) {
            $courseContext = "Daftar Mata Kuliah (beserta ID) milik pengguna:\n";
            foreach ($courses as $c) {
                $courseContext .= "- " . $c['name'] . " (ID: " . $c['id'] . ")\n";
            }
        } else {
            $courseContext = "Pengguna belum menambahkan mata kuliah apa pun.\n";
        }

        // Get user's materials for context
        $stmt = $pdo->prepare("SELECT m.id, m.title, m.file_path, c.name as course_name FROM materials m JOIN courses c ON m.course_id = c.id WHERE m.uploaded_by = ?");
        $stmt->execute([$userId]);
        $materials = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $materialContext = "";
        if (!empty($materials)) {
            $materialContext = "Daftar PDF Materi milik pengguna:\n";
            foreach ($materials as $m) {
                $materialContext .= "- Judul: " . $m['title'] . " (MK: " . $m['course_name'] . ") | Path: /assets/uploads/" . $m['file_path'] . "\n";
            }
        } else {
            $materialContext = "Pengguna belum mengunggah materi PDF apa pun.\n";
        }

        $systemPrompt = "Anda adalah 'Amba AI', asisten cerdas untuk web aplikasi 'StudyHub'. StudyHub adalah web aplikasi Personal Study Management di mana pengguna dapat mengelola catatan (notes), materi kuliah (PDF), kuis AI (latihan soal otomatis dari PDF), chat assistant khusus materi, dan forum diskusi. Tugas Anda adalah membantu pengguna menjawab pertanyaan umum, memberikan tips belajar, atau menjelaskan fitur-fitur di website ini. Jawablah dengan ramah, suportif, dan ringkas dalam bahasa Indonesia.\n\n";
        $systemPrompt .= "PENTING - FITUR NAVIGASI: Jika pengguna SECARA EKSPLISIT MEMERINTAHKAN ATAU MEMINTA Anda untuk membuka halaman tertentu, berpindah menu, melihat mata kuliah tertentu, ATAU membuka/melihat isi PDF materi, Anda DAPAT mengarahkan mereka secara otomatis dengan menyisipkan perintah navigasi.\n";
        $systemPrompt .= "Daftar Menu Utama:\n- Beranda (Dashboard): /dashboard\n- Materi Kuliah: /materials\n- AI Quiz: /quiz\n- AI Assistant (Upload Materi): /assistant\n- Forum Diskusi: /forum\n- Catatan: /notes\n\n";
        $systemPrompt .= $courseContext . "\n";
        $systemPrompt .= $materialContext . "\n";
        $systemPrompt .= "ATURAN NAVIGASI SANGAT KETAT:\n";
        $systemPrompt .= "1. BEDA PERINTAH vs PERTANYAAN: Lakukan navigasi/redirect HANYA jika pengguna menyuruh/meminta (contoh: 'buka quiz', 'arahkan ke materi', 'tolong bukain PBO'). JANGAN redirect jika pengguna HANYA BERTANYA cara menggunakan fitur (contoh: 'bagaimana cara bikin quiz?', 'dimana letak catatan?').\n";
        $systemPrompt .= "2. Jika syarat navigasi terpenuhi, Anda WAJIB menyertakan string `[REDIRECT:url_tujuan]` di akhir pesan Anda. \n";
        $systemPrompt .= "3. FITUR TERSEMBUNYI (EASTER EGG): Jika pengguna menyuruh memutar lagu atau bilang 'setel lagu [nama lagu] di yt', 'puterin lagu', dll. Anda WAJIB membalas dengan santai dan menyisipkan `[PLAY_YT:nama_lagu]` di akhir pesan. Contoh: 'Siap laksanakan! Memutar Surat Cinta Untuk Starla untukmu...' [PLAY_YT:Surat Cinta Untuk Starla]\n";
        $systemPrompt .= "Contoh Benar (Perintah):\n- User: 'buka materi kuliah'\n  AI: 'Baik, saya akan membuka menu Materi Kuliah.' [REDIRECT:/materials]\n- User: 'tolong bukain matakuliah PBO'\n  AI: 'Tentu, saya arahkan Anda ke mata kuliah PBO.' [REDIRECT:/materials/course?id=ID_MATA_KULIAH]\n- User: 'setel lagu bernadya di yt dong'\n  AI: 'Siap! Langsung saya putarkan lagu Bernadya di YouTube.' [PLAY_YT:Bernadya]\n";
        $systemPrompt .= "Contoh Benar (Pertanyaan - TANPA REDIRECT):\n- User: 'gimana cara buat quiz?'\n  AI: 'Untuk membuat quiz, Anda bisa mengunjungi menu AI Quiz yang ada di sidebar.'\n";
        $systemPrompt .= "Gunakan ID mata kuliah atau Path File PDF yang sesuai dari daftar di atas. Pastikan format url persis seperti contoh.";

        $messages = [
            [
                "role" => "system",
                "content" => $systemPrompt
            ]
        ];

        // Ensure we don't duplicate the latest message if it's already in history
        $hasLastUserMessage = false;
        foreach ($history as $h) {
            $role = $h['role'] === 'ai' ? 'assistant' : 'user';
            $messages[] = [
                "role" => $role,
                "content" => $h['message']
            ];
            if ($h['message'] === $message) {
                $hasLastUserMessage = true;
            }
        }

        // Add the current message if it's not the last one in history (it should be since we just inserted it, but we already added history, so we don't need to append again if it's fetched from DB. Wait, the history query fetches the message we JUST inserted! So it's already included. We just pass $messages).
        
        $data = [
            "model" => "llama-3.3-70b-versatile",
            "messages" => $messages,
            "temperature" => 0.7
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
        return "Maaf, Amba AI sedang mengalami gangguan koneksi. Coba lagi nanti.";
    }

    public function clear() {
        if (isset($_SESSION['user_id'])) {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("DELETE FROM amba_chat_history WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
        }
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }
}

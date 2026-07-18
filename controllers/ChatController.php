<?php

class ChatController {
    public function getMessages() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            SELECT g.id, g.message, g.created_at, u.name as user_name, g.user_id 
            FROM global_chats g
            JOIN users u ON g.user_id = u.id
            ORDER BY g.created_at DESC
            LIMIT 50
        ");
        $stmt->execute();
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Reverse so chronological order
        $messages = array_reverse($messages);

        header('Content-Type: application/json');
        echo json_encode($messages);
    }

    public function sendMessage() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $message = $_POST['message'] ?? '';
        if (trim($message) === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Message cannot be empty']);
            return;
        }

        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("INSERT INTO global_chats (user_id, message) VALUES (?, ?)");
        if ($stmt->execute([$_SESSION['user_id'], $message])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save message']);
        }
    }
}

<?php
// Simple migration runner for local development ONLY.
// Usage: http://localhost/StudyHub/migrations/run.php  
// Optional: add ?seed=1 to populate existing users' bio with a default message.

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = Database::getInstance();

    // Add bio column if not exists
    $col = $pdo->query("SHOW COLUMNS FROM users LIKE 'bio'")->fetch();
    if (!$col) {
        $pdo->exec("ALTER TABLE users ADD COLUMN bio TEXT NULL AFTER semester");
        echo "Column 'bio' added to users table.<br>";
    } else {
        echo "Column 'bio' already exists.<br>";
    }

    if (isset($_GET['seed']) && $_GET['seed'] == '1') {
        $stmt = $pdo->prepare("UPDATE users SET bio = ? WHERE bio IS NULL");
        $stmt->execute(["Halo! Saya menggunakan StudyHub."]);
        echo "Default bio populated for existing users.<br>";
    }

    echo "Migration completed successfully.";
} catch (Exception $e) {
    echo "Migration failed: " . htmlspecialchars($e->getMessage());
}

?>
<?php
// Basic contact capture using SQLite
// Stores submissions from the contact form

$dbPath = __DIR__ . DIRECTORY_SEPARATOR . 'contact_messages.db';

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec('CREATE TABLE IF NOT EXISTS messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        email TEXT,
        subject TEXT,
        message TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )');

    // Fetch POST data safely
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Basic validation
    if ($name === '' || $email === '' || $message === '') {
        throw new Exception('Please provide name, email, and message.');
    }

    $stmt = $pdo->prepare('INSERT INTO messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)');
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':subject' => $subject,
        ':message' => $message
    ]);

    $response = [
        'status' => 'success',
        'message' => 'Thanks for reaching out. We will contact you soon.'
    ];
} catch (Exception $e) {
    http_response_code(400);
    $response = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
}

// Return JSON for AJAX; fallback simple text if not
header('Content-Type: application/json');
echo json_encode($response);
?>

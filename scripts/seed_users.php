<?php
require_once __DIR__ . '/../config/db.php';

try {
    $pdo = getPDO();

    // Ensure users table exists (safe if table already created)
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('admin','teacher','student') DEFAULT 'student',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $username = 'admin';
    $password = 'admin123';

    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username LIMIT 1');
    $stmt->execute(['username' => $username]);

    if ($stmt->fetch()) {
        echo 'User "' . htmlspecialchars($username, ENT_QUOTES) . '" already exists.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ins = $pdo->prepare('INSERT INTO users (username, password_hash, role) VALUES (:username, :hash, :role)');
        $ins->execute(['username' => $username, 'hash' => $hash, 'role' => 'admin']);
        echo 'Inserted user "' . htmlspecialchars($username, ENT_QUOTES) . '" with password: ' . htmlspecialchars($password, ENT_QUOTES) . '. Please change it after first login.';
    }
} catch (Exception $e) {
    echo 'Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES);
}

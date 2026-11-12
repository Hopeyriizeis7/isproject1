<?php
// Sample settings. On the server: cp config.sample.php config.php, then put your real password in config.php.
$dbHost = 'localhost';
$dbName = 'CAT2';
$dbUser = 'cat2user';
$dbPass = 'ChangeThisPassword123!';

try {
    $pdo = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed.');
}

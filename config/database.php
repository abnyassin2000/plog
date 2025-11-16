<?php
// Database connection settings
$dsn = "mysql:host=localhost;dbname=phpblog;charset=utf8"; // DSN = Data Source Name
$username = "root"; // Database username
$password = ""; // Database password (usually empty on localhost)

try {
    $pdo = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
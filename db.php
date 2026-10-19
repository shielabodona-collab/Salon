
<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "shalon");

if (!$conn) {
    die("Database connection failed. Check db.php settings.");
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

function requireAdmin() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header("Location: login.php");
        exit;
    }
}
?>

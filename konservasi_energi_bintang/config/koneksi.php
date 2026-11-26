<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';$dbname = 'konservasi_energi_bintang';
$username = 'root';$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username,$password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

define('BASE_URL', '/konservasi_energi_bintang');

function wajib_login() {
    if (!isset($_SESSION['user'])) {
        header("Location: " . BASE_URL . "/login.php");
        exit;
    }
}

function wajib_admin() {
    wajib_login();
    if ($_SESSION['user']['role'] !== 'admin') {
        header("Location: " . BASE_URL . "/index.php");
        exit;
    }
}
?>
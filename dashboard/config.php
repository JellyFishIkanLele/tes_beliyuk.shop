<?php
// ====================================================
// CONFIG.PHP - UNTUK BELIYUK.SHOP CPANEL
// ====================================================

// DATABASE CONFIGURATION (CPANEL)
$host = 'localhost';
$dbname = 'bikslrey_beliiyuk';  // Format cPanel: username_dbname
$username = 'bikslrey_beliyukuser';       // Format cPanel: username_username
$password = '9eg]BUwSM28V';    // Password database cPanel Anda

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    // Jangan tampilkan error detail di production
    die("❌ Database connection error. Please contact administrator.");
}

// SET TIMEZONE
date_default_timezone_set('Asia/Jakarta');

// CHECK IF FUNCTION ALREADY EXISTS TO PREVENT DUPLICATION
if (!function_exists('getDefaultImageUrl')) {
    function getDefaultImageUrl() {
        return 'https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp';
    }
}

if (!function_exists('createSafeFolderName')) {
    function createSafeFolderName($productName) {
        // Clean the product name for folder
        $folderName = preg_replace('/[^\p{L}\p{N}\s]/u', '', $productName);
        $folderName = str_replace(' ', '-', $folderName);
        $folderName = strtolower($folderName);
        $folderName = preg_replace('/-+/', '-', $folderName);
        $folderName = trim($folderName, '-');
        $folderName = $folderName . '-' . time();
        
        return $folderName;
    }
}

// SESSION START IF NOT STARTED
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
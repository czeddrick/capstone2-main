<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/capstone2-main/');
}

if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/');
}

// Include database connection (only if it exists)
$connectFile = BASE_PATH . '../db/connect.php';
if (file_exists($connectFile)) {
    include $connectFile;
} else {
    die("Error: Database connection file not found.");
}
?>

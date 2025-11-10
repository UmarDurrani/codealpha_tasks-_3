<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'project_management');
define('DB_USER', 'root');
define('DB_PASS', '');

// Create database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Get current user ID
function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}

// Check if user has access to project
function has_project_access($project_id) {
    global $pdo;
    $user_id = get_user_id();
    
    if (!$user_id) return false;
    
    $stmt = $pdo->prepare("
        SELECT pm.id FROM project_members pm 
        WHERE pm.project_id = ? AND pm.user_id = ?
        UNION
        SELECT p.id FROM projects p 
        WHERE p.id = ? AND p.created_by = ?
    ");
    $stmt->execute([$project_id, $user_id, $project_id, $user_id]);
    return $stmt->fetch() !== false;
}
?>
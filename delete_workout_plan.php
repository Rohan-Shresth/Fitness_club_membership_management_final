<?php
session_start();
require 'db.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: workout_plans.php");
    exit;
}

// Validate CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid request. CSRF token mismatch.");
}

// Check authorization
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] === 'customer') {
    header("Location: workout_plans.php");
    exit;
}

if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = (int)$_POST['id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM workout_plans WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        header("Location: workout_plans.php");
        exit;
    } catch (PDOException $e) {
        die("Error deleting workout plan: " . $e->getMessage());
    }
}

header("Location: workout_plans.php");
exit;
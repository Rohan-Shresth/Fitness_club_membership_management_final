<?php
session_start();
require "db.php";

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check authorization
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] === 'customer') {
    header("Location: workout_plans.php");
    exit;
}

$error = "";
$success = "";

// Fetch members and trainers for dropdowns
$members = $pdo->query("SELECT id, full_name FROM customers ORDER BY full_name")->fetchAll();
$trainers = $pdo->query("SELECT id, full_name FROM trainers ORDER BY full_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid request.";
    } else {
        $member_id = $_POST['member_id'];
        $trainer_id = $_POST['trainer_id'] ?: null;
        $plan_name = trim($_POST['plan_name']);
        $description = trim($_POST['description']);
        $exercises = trim($_POST['exercises']);
        $duration_weeks = (int)$_POST['duration_weeks'];
        $start_date = $_POST['start_date'];
        
        // Calculate end date
        $end_date = date('Y-m-d', strtotime($start_date . " + $duration_weeks weeks"));
        
        if (empty($member_id) || empty($plan_name) || empty($start_date)) {
            $error = "Please fill in all required fields.";
        } else {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO workout_plans 
                    (member_id, trainer_id, plan_name, description, exercises, duration_weeks, start_date, end_date)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $member_id, $trainer_id, $plan_name, $description, 
                    $exercises, $duration_weeks, $start_date, $end_date
                ]);
                
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                header("Location: workout_plans.php");
                exit;
            } catch (PDOException $e) {
                $error = "Error creating workout plan: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Add Workout Plan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar">
    <div class="navdiv">
        <div class="logo"><a href="index.php">Gym</a></div>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="workout_plans.php">Workout Plans</a></li>
        </ul>
    </div>
</nav>

<div class="main-content">
    <h1 class="page-title">Create Workout Plan</h1>
    
    <section class="page-section">
        <?php if ($error): ?>
            <p style="color:red; background: rgba(255,0,0,0.2); padding: 12px; border-radius: 10px; max-width: 600px; margin: 0 auto 20px;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>
        
        <form class="form-box" method="post" style="max-width: 600px;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            
            <select name="member_id" required>
                <option value="">Select Member *</option>
                <?php foreach ($members as $member): ?>
                    <option value="<?= $member['id'] ?>"><?= htmlspecialchars($member['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
            
            <select name="trainer_id">
                <option value="">Select Trainer (Optional)</option>
                <?php foreach ($trainers as $trainer): ?>
                    <option value="<?= $trainer['id'] ?>"><?= htmlspecialchars($trainer['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
            
            <input type="text" name="plan_name" placeholder="Plan Name *" required>
            
            <textarea name="description" placeholder="Description" rows="3" 
                      style="background: #222; color: white; padding: 14px 16px; border: none; border-radius: 10px; width: 100%; resize: vertical;"></textarea>
            
            <textarea name="exercises" placeholder="Exercises (e.g., Push-ups x20, Squats x30, Running 30min)" 
                      rows="5" style="background: #222; color: white; padding: 14px 16px; border: none; border-radius: 10px; width: 100%; resize: vertical;"></textarea>
            
            <input type="number" name="duration_weeks" placeholder="Duration (weeks)" value="4" min="1" max="52" required>
            
            <input type="date" name="start_date" required value="<?= date('Y-m-d') ?>">
            
            <button type="submit">Create Workout Plan</button>
            <a href="workout_plans.php" style="color: orange; text-decoration: none; display: block; text-align: center;">Cancel</a>
        </form>
    </section>
</div>

</body>
</html>
<?php
session_start();
require "../config/db.php";

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_role = $_SESSION['user_role'] ?? '';
$user_id = $_SESSION['user_id'];

// Handle check-in/check-out
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid request.";
    } else {
        if ($_POST['action'] === 'checkin' && $user_role === 'customer') {
            // Check if already checked in
            $stmt = $pdo->prepare("SELECT id FROM attendance WHERE member_id = ? AND check_out_time IS NULL");
            $stmt->execute([$user_id]);
            
            if (!$stmt->fetch()) {
                $stmt = $pdo->prepare("INSERT INTO attendance (member_id, check_in_time) VALUES (?, NOW())");
                $stmt->execute([$user_id]);
                $success = "Checked in successfully!";
            } else {
                $error = "You are already checked in!";
            }
        } elseif ($_POST['action'] === 'checkout' && $user_role === 'customer') {
            $stmt = $pdo->prepare("
                SELECT id, check_in_time FROM attendance 
                WHERE member_id = ? AND check_out_time IS NULL 
                ORDER BY check_in_time DESC LIMIT 1
            ");
            $stmt->execute([$user_id]);
            $active = $stmt->fetch();
            
            if ($active) {
                $check_in = new DateTime($active['check_in_time']);
                $check_out = new DateTime();
                $duration = $check_out->diff($check_in)->i + ($check_out->diff($check_in)->h * 60);
                
                $stmt = $pdo->prepare("
                    UPDATE attendance 
                    SET check_out_time = NOW(), duration_minutes = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$duration, $active['id']]);
                $success = "Checked out successfully! Duration: $duration minutes";
            } else {
                $error = "No active check-in found!";
            }
        }
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

// Fetch attendance records
if ($user_role === 'customer') {
    $stmt = $pdo->prepare("
        SELECT * FROM attendance 
        WHERE member_id = ? 
        ORDER BY check_in_time DESC 
        LIMIT 50
    ");
    $stmt->execute([$user_id]);
} else {
    $stmt = $pdo->query("
        SELECT a.*, c.full_name as member_name 
        FROM attendance a
        JOIN customers c ON a.member_id = c.id
        ORDER BY a.check_in_time DESC
        LIMIT 100
    ");
}
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if user is currently checked in
$checked_in = false;
if ($user_role === 'customer') {
    $stmt = $pdo->prepare("SELECT id FROM attendance WHERE member_id = ? AND check_out_time IS NULL");
    $stmt->execute([$user_id]);
    $checked_in = $stmt->fetch() ? true : false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Attendance - Gym Management</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .table-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            overflow-x: auto;
        }

        .check-in-box {
            max-width: 500px;
            margin: 30px auto;
            padding: 30px;
            background: #111;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0,0,0,0.7);
        }

        .check-in-box h2 {
            color: white;
            margin-bottom: 20px;
        }

        .check-in-box button {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-checkin {
            background: linear-gradient(to right, #2e7d32, #388e3c);
            color: white;
        }

        .btn-checkout {
            background: linear-gradient(to right, #d32f2f, #c62828);
            color: white;
        }

        .btn-checkin:hover, .btn-checkout:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(255,165,0,0.4);
        }

        .status-active {
            color: #4caf50;
            font-weight: bold;
        }

        table {
            width: 100%;
            background: #111;
            border-collapse: collapse;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.7);
        }

        table th {
            background: linear-gradient(to right, orange, darkorange);
            color: white;
            padding: 18px;
            text-align: left;
            font-size: 16px;
            font-weight: 600;
        }

        table td {
            padding: 15px 18px;
            border-bottom: 1px solid #222;
            color: #ccc;
        }

        table tr:hover {
            background: #1a1a1a;
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin: 20px auto;
            max-width: 600px;
            text-align: center;
        }

        .alert-success {
            background: rgba(46, 125, 50, 0.3);
            color: #4caf50;
            border: 1px solid #4caf50;
        }

        .alert-error {
            background: rgba(211, 47, 47, 0.3);
            color: #f44336;
            border: 1px solid #f44336;
        }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="navdiv">
        <div class="logo"><a href="index.php">Gym</a></div>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="workout_plans.php">Workout Plans</a></li>
            <li><a href="attendance.php">Attendance</a></li>
        </ul>
    </div>
</nav>

<div class="main-content">
    <h1 class="page-title">Attendance Tracking</h1>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if ($user_role === 'customer'): ?>
    <div class="check-in-box">
        <h2><?= $checked_in ? 'You are checked in!' : 'Ready to workout?' ?></h2>
        <p style="color: #ccc; margin-bottom: 20px;">
            <?= $checked_in ? 'Don\'t forget to check out when you leave!' : 'Check in to start your session' ?>
        </p>
        
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <?php if (!$checked_in): ?>
                <input type="hidden" name="action" value="checkin">
                <button type="submit" class="btn-checkin">Check In</button>
            <?php else: ?>
                <input type="hidden" name="action" value="checkout">
                <button type="submit" class="btn-checkout">Check Out</button>
            <?php endif; ?>
        </form>
    </div>
    <?php endif; ?>
    
    <div class="table-container">
        <h2 style="color: white; margin-bottom: 20px;">
            <?= $user_role === 'customer' ? 'My Attendance History' : 'All Attendance Records' ?>
        </h2>
        
        <?php if (count($records) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <?php if ($user_role !== 'customer'): ?>
                        <th>Member</th>
                    <?php endif; ?>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Duration</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $record): ?>
                <tr>
                    <td><?= $record['id'] ?></td>
                    <?php if ($user_role !== 'customer'): ?>
                        <td><?= htmlspecialchars($record['member_name']) ?></td>
                    <?php endif; ?>
                    <td><?= date('d M Y, h:i A', strtotime($record['check_in_time'])) ?></td>
                    <td>
                        <?= $record['check_out_time'] 
                            ? date('d M Y, h:i A', strtotime($record['check_out_time'])) 
                            : '<span class="status-active">Active</span>' 
                        ?>
                    </td>
                    <td><?= $record['duration_minutes'] ? $record['duration_minutes'] . ' min' : '-' ?></td>
                    <td><?= $record['check_out_time'] ? 'Completed' : '<span class="status-active">In Progress</span>' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p style="color: #888; text-align: center; padding: 40px;">
            No attendance records found.
        </p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
<?php
session_start();
require "db.php";

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

// Fetch workout plans based on user role
if ($user_role === 'customer') {
    // Customers see only their plans
    $stmt = $pdo->prepare("
        SELECT wp.*, c.full_name as member_name, t.full_name as trainer_name
        FROM workout_plans wp
        LEFT JOIN customers c ON wp.member_id = c.id
        LEFT JOIN trainers t ON wp.trainer_id = t.id
        WHERE wp.member_id = ?
        ORDER BY wp.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
} else {
    // Trainers and owners see all plans
    $stmt = $pdo->query("
        SELECT wp.*, c.full_name as member_name, t.full_name as trainer_name
        FROM workout_plans wp
        LEFT JOIN customers c ON wp.member_id = c.id
        LEFT JOIN trainers t ON wp.trainer_id = t.id
        ORDER BY wp.created_at DESC
    ");
}

$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Workout Plans - Gym Management</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .table-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            overflow-x: auto;
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

        table tr:last-child td {
            border-bottom: none;
        }

        table tr:hover {
            background: #1a1a1a;
        }

        table td a {
            color: orange;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
        }

        table td a:hover {
            color: darkorange;
            text-decoration: underline;
        }

        table td button {
            background: #c62828;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
        }

        table td button:hover {
            background: #b71c1c;
            transform: translateY(-2px);
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
        }

        .status-active {
            background: #2e7d32;
            color: white;
        }

        .status-completed {
            background: #1565c0;
            color: white;
        }

        .status-cancelled {
            background: #c62828;
            color: white;
        }

        .add-btn {
            display: inline-block;
            background: linear-gradient(to right, orange, darkorange);
            color: white;
            padding: 12px 25px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 20px;
            transition: 0.3s;
        }

        .add-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(255,165,0,0.4);
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #888;
            font-size: 18px;
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
    <h1 class="page-title">Workout Plans</h1>
    
    <div class="table-container">
        <?php if ($user_role !== 'customer'): ?>
            <a href="add_workout_plan.php" class="add-btn">+ Create New Plan</a>
        <?php endif; ?>
        
        <?php if (count($plans) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Member</th>
                    <th>Trainer</th>
                    <th>Plan Name</th>
                    <th>Duration</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($plans as $plan): ?>
                <tr>
                    <td><?= $plan['id'] ?></td>
                    <td><?= htmlspecialchars($plan['member_name']) ?></td>
                    <td><?= htmlspecialchars($plan['trainer_name'] ?? 'Not assigned') ?></td>
                    <td><?= htmlspecialchars($plan['plan_name']) ?></td>
                    <td><?= $plan['duration_weeks'] ?> weeks</td>
                    <td><?= $plan['start_date'] ? date('d M Y', strtotime($plan['start_date'])) : '-' ?></td>
                    <td><?= $plan['end_date'] ? date('d M Y', strtotime($plan['end_date'])) : '-' ?></td>
                    <td>
                        <span class="status-badge status-<?= $plan['status'] ?>">
                            <?= ucfirst($plan['status']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="view_workout_plan.php?id=<?= $plan['id'] ?>">View</a>
                        <?php if ($user_role !== 'customer'): ?>
                        | <a href="edit_workout_plan.php?id=<?= $plan['id'] ?>">Edit</a>
                        | <form action="delete_workout_plan.php" method="post" style="display:inline;"
                              onsubmit="return confirm('Delete this workout plan?');">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                            <input type="hidden" name="id" value="<?= $plan['id'] ?>">
                            <button type="submit">Delete</button>
                          </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="no-data">
            No workout plans found. 
            <?php if ($user_role !== 'customer'): ?>
                Click "Create New Plan" to get started.
            <?php else: ?>
                Contact your trainer to create a workout plan for you.
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
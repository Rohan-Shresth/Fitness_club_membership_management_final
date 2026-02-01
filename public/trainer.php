<?php
require "../config/db.php";
$stmt = $pdo->query("SELECT * FROM trainers");
$trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Trainers - Gym Management</title>
    <link rel="stylesheet" href="../assets/style.css">
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

        .no-data {
            text-align: center;
            padding: 40px;
            color: #888;
            font-size: 18px;
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

        @media (max-width: 768px) {
            table {
                font-size: 14px;
            }
            
            table th,
            table td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="navdiv">
        <div class="logo"><a href="index.php">Gym</a></div>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="registration.php">User Registration</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="pricing.php">Pricing</a></li>
            <li><a href="courses.php">Courses</a></li>
        </ul>
    </div>
</nav>

<div class="main-content">
    <h1 class="page-title">Trainers</h1>
    
    <div class="table-container">
        <a href="registration.php" class="add-btn">+ Add New Trainer</a>
        
        <?php if (count($trainers) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Specialty</th>
                    <th>Joined at</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trainers as $t): ?>
                <tr>
                    <td><?= $t['id'] ?></td>
                    <td><?= htmlspecialchars($t['full_name']) ?></td>
                    <td><?= htmlspecialchars($t['email']) ?></td>
                    <td><?= htmlspecialchars($t['specialty']) ?></td>
                    <td>
                        <?= $t['created_at']
                            ? date("d M Y, h:i A", strtotime($t['created_at']))
                            : "—"
                        ?>
                    </td>
                    <td>
                        <a href="edit_trainer.php?id=<?= $t['id'] ?>">Edit</a> |
                        <form action="delete_trainer.php" method="post" style="display:inline;"
                            onsubmit="return confirm('Delete this member?');">
                            <input type="hidden" name="id" value="<?= $t['id'] ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="no-data">No trainers found. Click "Add New Trainer" to get started.</div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
<?php
require "../config/db.php";
$role = $_GET['role'] ?? '';
if ($role !== 'customer') {
    header("Location: dashboard.php");
    exit;
}
$stmt = $pdo->query("
    SELECT customers.*, user_plans.plan_name, user_plans.price
    FROM customers
    LEFT JOIN user_plans ON customers.id = user_plans.user_id
");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Members - Gym Management</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .table-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            overflow-x: auto;
        }

        .search-box {
            max-width: 500px;
            margin: 0 auto 30px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 15px 20px;
            border-radius: 10px;
            border: none;
            background: #222;
            color: white;
            font-size: 16px;
        }

        .search-box input:focus {
            outline: none;
            box-shadow: 0 0 0 2px orange;
        }

        .search-box input::placeholder {
            color: #888;
        }

        #results {
            background: #111;
            border-radius: 10px;
            margin-top: 10px;
            max-height: 300px;
            overflow-y: auto;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        }

        #results:empty {
            display: none;
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

        table td form {
            display: inline;
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
    <h1 class="page-title">Members</h1>
    
    <div class="table-container">
        <a href="registration.php" class="add-btn">+ Add New Member</a>
        
        <div class="search-box">
            <input type="text" id="search" placeholder="Search members..." autocomplete="off">
            <div id="results"></div>
        </div>
        
        <?php if (count($customers) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Joined At</th>
                    <th>Plan</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $c): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><?= htmlspecialchars($c['full_name']) ?></td>
                    <td><?= htmlspecialchars($c['email']) ?></td>
                    <td><?= date("d M Y, h:i A", strtotime($c['created_at'])) ?></td>
                    <td><?= $c['plan_name'] ?? 'Not Selected' ?></td>
                    <td><?= $c['price'] ?? '-' ?></td>
                    <td>
                        <a href="edit_customer.php?id=<?= $c['id'] ?>">Edit</a> |
                        <form action="delete_customer.php" method="post" style="display:inline;"
                            onsubmit="return confirm('Delete this member?');">
                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="no-data">No members found. Click "Add New Member" to get started.</div>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById("search").addEventListener("keyup", function () {
    let query = this.value;
    if (query.length === 0) {
        document.getElementById("results").innerHTML = "";
        return;
    }
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "search_customer.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function () {
        if (this.status === 200) {
            document.getElementById("results").innerHTML = this.responseText;
        }
    };
    xhr.send("query=" + encodeURIComponent(query));
});
</script>
</body>
</html>
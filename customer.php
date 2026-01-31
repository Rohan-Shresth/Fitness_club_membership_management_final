<?php
require "db.php";

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
    <title>Members</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <div class="navdiv">
        <div class="logo"><a href="index.php">Gym</a></div>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
        </ul>
    </div>
</nav>

<div class="page-section">
    <h1>Members</h1>

    <input type="text" id="search" placeholder="Search customers..." autocomplete="off">
    <div id="results"></div>

    <table border="1" cellpadding="10" cellspacing="0" style="margin:auto; background:#111; color:white;">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Joined At</th>
            <th>Plan</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>

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
    </table>

</div>

<script>
document.getElementById("search").addEventListener("keyup", function () {
    let query = this.value;

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

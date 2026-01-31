<?php
session_start();
require "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION["user_id"];
$message = "";

if (isset($_POST["select_plan"])) {
    $plan = $_POST["plan"];
    $price = $_POST["price"];

    $stmt = $pdo->prepare(
        "INSERT INTO user_plans (user_id, plan_name, price)
         VALUES (?, ?, ?)"
    );
    $stmt->execute([$userId, $plan, $price]);

    $message = "Plan selected successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<nav class="navbar">
    <div class="navdiv">
        <div class="logo"><a href="index.php">Gym</a></div>
        <ul>
            <li><a href="user_dashboard.php">Dashboard</a></li>
            <li><a href="pricing.php">Pricing</a></li>
            <li><a href="courses.php">Courses</a></li>
            <li><a href="login.php">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="main-content">
    <h1 class="page-title">Welcome, <?= htmlspecialchars($_SESSION["user_name"]) ?></h1>

    <?php if ($message): ?>
        <p style="color: green;"><?= $message ?></p>
    <?php endif; ?>

    <h2>Select a Membership Plan</h2>

    <section class="pricing">

        <!-- BASIC -->
        <div class="price-card">
            <h3>Basic</h3>
            <h2>₹999</h2>
            <p>Gym Access</p>
            <form method="post">
                <input type="hidden" name="plan" value="Basic">
                <input type="hidden" name="price" value="999">
                <button type="submit" name="select_plan">Select Plan</button>
            </form>
        </div>

        <!-- STANDARD -->
        <div class="price-card">
            <h3>Standard</h3>
            <h2>₹1999</h2>
            <p>Gym + Classes</p>
            <form method="post">
                <input type="hidden" name="plan" value="Standard">
                <input type="hidden" name="price" value="1999">
                <button type="submit" name="select_plan">Select Plan</button>
            </form>
        </div>

        <!-- PREMIUM -->
        <div class="price-card">
            <h3>Premium</h3>
            <h2>₹2999</h2>
            <p>All Access</p>
            <form method="post">
                <input type="hidden" name="plan" value="Premium">
                <input type="hidden" name="price" value="2999">
                <button type="submit" name="select_plan">Select Plan</button>
            </form>
        </div>

    </section>
</div>

</body>
</html>

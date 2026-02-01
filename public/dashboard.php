<?php
require "../config/db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">
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
    <h1 class="page-title">Dashboard</h1>

    <div class="dashboard-cards">
        <a href="customer.php?role=customer" class="card-link">
            <div class="dashboard-card">
                <h3>Members</h3>
            </div>
        </a>

        <a href="pricing.php" class="card-link">
            <div class="dashboard-card">
                <h3>Active Plans</h3>
            </div>
        </a>

        <a href="trainer.php?role=trainer" class="card-link">
            <div class="dashboard-card">
                <h3>Trainers</h3>
            </div>
        </a>
    </div>
</div>


</body>
</html>

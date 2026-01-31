<?php
require "db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pricing</title>
    <link rel="stylesheet" href="style.css">
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
    <h1 class="page-title">Membership Plans</h1>

    <section class="pricing">
        <div class="price-card">
            <h3>Basic</h3>
            <h2>₹999</h2>
            <p>Gym Access</p>
        </div>

        <div class="price-card">
            <h3>Standard</h3>
            <h2>₹1999</h2>
            <p>Gym + Classes</p>
        </div>

        <div class="price-card">
            <h3>Premium</h3>
            <h2>₹2999</h2>
            <p>All Access</p>
        </div>
    </section>
</div>

</body>
</html>

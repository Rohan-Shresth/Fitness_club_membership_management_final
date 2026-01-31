<?php
require "db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gym | Home</title>
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

<!-- HERO -->
<section class="hero">
    <h1>Welcome to Gym Fitness Club</h1>
    <p>Your transformation starts here</p>
</section>

<!-- FEATURES -->
<section class="features">

    <div class="feature-box">
        <img src="imgs/ROI_In_Gym_Equipment_1200_628-1024x536.jpg" alt="Modern Equipment">
        <h3>Modern Equipment</h3>
        <p>Train with the latest machines.</p>
    </div>

    <div class="feature-box">
        <img src="imgs/images.jpg" alt="Expert Trainers">
        <h3>Expert Trainers</h3>
        <p>Certified and experienced coaches.</p>
    </div>

    <div class="feature-box">
        <img src="imgs/Group-Fitness-Classes-at-the-best-gyms-in-Padonia-Lutherville-Timonium-and-Baltimore-MD-1.jpg" alt="Group Classes">
        <h3>Group Classes</h3>
        <p>Yoga, HIIT, Cardio & more.</p>
    </div>

</section>


<footer class="footer">
    <p>© 2026 Gym Fitness Club</p>
</footer>

</body>
</html>

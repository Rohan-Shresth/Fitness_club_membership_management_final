<?php
session_start();          
require_once "db.php";

if (isset($_POST['register'])) {

    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $specialty = $_POST['specialty'] ?? null;

    try {
        if ($role === "customer") {
            $stmt = $pdo->prepare(
                "INSERT INTO customers (full_name, email, password)
                 VALUES (?, ?, ?)"
            );
            $stmt->execute([$name, $email, $password]);

        } elseif ($role === "trainer") {
            $stmt = $pdo->prepare(
                "INSERT INTO trainers (full_name, email, password, specialty)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$name, $email, $password, $specialty]);

        } elseif ($role === "owner") {
            $stmt = $pdo->prepare(
                "INSERT INTO owners (full_name, email, password)
                 VALUES (?, ?, ?)"
            );
            $stmt->execute([$name, $email, $password]);
        }

        $_SESSION["user_name"] = $name;
        $_SESSION["user_email"] = $email;
        $_SESSION["user_role"] = $role;

        echo "<script>
                alert('Registration successful');
                window.location.href = 'login.php';
              </script>";

    } catch (PDOException $e) {
        echo "<script>alert('Error: Email already exists');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>User Registration</title>
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
<section class="page-section">
    <h1>User Registration</h1>

    <form class="form-box" method="POST">
        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>

        <select name="role" required>
            <option value="">Register As</option>
            <option value="customer">Customer</option>
            <option value="trainer">Trainer</option>
        </select>

        <input type="text" name="specialty" placeholder="Trainer Specialty (optional)">

        <button type="submit" name="register">Register</button>
        <a href="login.php">Already have an account</a>
    </form>
</section>


</body>
</html>

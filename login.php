<?php
session_start();
require "db.php";
// Generate CSRF token if it doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate CSRF token first
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid request. Please try again.";
    } else {
        $email = trim($_POST["email"]);
        $password = $_POST["password"];
        
        if (empty($email) || empty($password)) {
            $error = "Please fill in all fields.";
        } else {
            $user = null;
            $role = null;
            $tables = [
                "customers" => "customer",
                "trainers"  => "trainer",
                "owners"    => "owner"
            ];
            
            foreach ($tables as $table => $userRole) {
                $stmt = $pdo->prepare("SELECT * FROM $table WHERE email = ?");
                $stmt->execute([$email]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result && password_verify($password, $result["password"])) {
                    $user = $result;
                    $role = $userRole;
                    break;
                }
            }
            
            if ($user) {
                $_SESSION["user_id"]   = $user["id"];
                $_SESSION["user_name"] = $user["full_name"];
                $_SESSION["user_role"] = $role;
                
                // Regenerate session ID after successful login (security best practice)
                session_regenerate_id(true);
                
                // Generate new CSRF token after login
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                
                header("Location: user_dashboard.php");
                exit;
            } else {
                $error = "Invalid email or password.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login</title>
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
    <h1 class="page-title">Login</h1>
    
    <section class="page-section">
        <?php if ($error): ?>
            <p style="color:red; background: rgba(255,0,0,0.2); padding: 12px; border-radius: 10px; max-width: 420px; margin: 0 auto 20px;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        
        <form class="form-box" method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            
            <button type="submit">Login</button>
            <a href="registration.php" style="color: orange; text-decoration: none;">I don't have an account</a>
        </form>
    </section>
</div>

</body>
</html>
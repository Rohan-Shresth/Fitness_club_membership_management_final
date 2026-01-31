<?php
session_start();
require "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {

        $user = null;
        $role = null;

        // Tables to check
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

            header("Location: user_dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Login</h2>

<?php if ($error): ?>
    <p style="color:red;"><?= $error ?></p>
<?php endif; ?>

<form method="post">
    <label>Email</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button><br><br>
    <a href="registration.php">I don't have an account</a>
</form>

</body>
</html>

<?php
require "db.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid ID");
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM trainers WHERE id = ?");
$stmt->execute([$id]);
$trainer = $stmt->fetch();

if (!$trainer) {
    die("Trainer not found");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name      = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $specialty = trim($_POST['specialty'] ?? '');

    if ($name && $email) {
        $update = $pdo->prepare(
            "UPDATE trainers 
             SET full_name = ?, email = ?, specialty = ? 
             WHERE id = ?"
        );
        $update->execute([$name, $email, $specialty, $id]);

        header("Location: trainer.php");
        exit;
    }
}
?>

<form method="post">
    <input type="text" name="full_name"
        value="<?= htmlspecialchars($trainer['full_name']) ?>" required><br>

    <input type="email" name="email"
        value="<?= htmlspecialchars($trainer['email']) ?>" required><br>

    <input type="text" name="specialty"
        value="<?= htmlspecialchars($trainer['specialty']) ?>"><br>

    <button type="submit">Update</button>
</form>

<?php
require "../config/db.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid ID");
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$id]);
$customer = $stmt->fetch();

if (!$customer) {
    die("Member not found");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name && $email) {
        $update = $pdo->prepare(
            "UPDATE customers SET full_name = ?, email = ? WHERE id = ?"
        );
        $update->execute([$name, $email, $id]);

        header("Location: customer.php?role=customer");
        exit;
    }
}
?>

<form method="post">
    <h2>Edit Member</h2>

    <input type="text" name="full_name"
        value="<?= htmlspecialchars($customer['full_name']) ?>" required>

    <br><br>

    <input type="email" name="email"
        value="<?= htmlspecialchars($customer['email']) ?>" required>

    <br><br>

    <button type="submit">Update</button>
</form>

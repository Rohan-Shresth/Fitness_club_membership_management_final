<?php
require "db.php";

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    die("Invalid ID");
}

$id = (int) $_POST['id'];

$stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
$stmt->execute([$id]);

header("Location: customer.php?role=customer");
exit;

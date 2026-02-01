<?php
require "../config/db.php";

$query = $_POST['query'] ?? '';

$stmt = $pdo->prepare(
    "SELECT * FROM customers 
     WHERE full_name LIKE ? 
     ORDER BY full_name"
);
$stmt->execute(["%$query%"]);

$customers = $stmt->fetchAll();

if (!$customers) {
    echo "<p>No results found</p>";
    exit;
}

echo "<table border='1' width='100%'>";
echo "<tr><th>Name</th><th>Email</th></tr>";

foreach ($customers as $c) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($c['full_name']) . "</td>";
    echo "<td>" . htmlspecialchars($c['email']) . "</td>";
    echo "</tr>";
}

echo "</table>";

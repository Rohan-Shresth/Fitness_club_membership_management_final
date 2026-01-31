<?php
require "db.php";

$stmt = $pdo->query("SELECT * FROM trainers");
$trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Trainers</h1>

<table border="1" cellpadding="10">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Specialty</th>
    <th>Joined at</th>
    <th>Actions</th>
</tr>

<?php foreach ($trainers as $t): ?>
<tr>
    <td><?= $t['id'] ?></td>
    <td><?= htmlspecialchars($t['full_name']) ?></td>
    <td><?= htmlspecialchars($t['email']) ?></td>
    <td><?= htmlspecialchars($t['specialty']) ?></td>
    <td>
        <?= $t['created_at']
            ? date("d M Y, h:i A", strtotime($t['created_at']))
            : "—"
        ?>
    </td>
    <td>
        <a href="edit_trainer.php?id=<?= $t['id'] ?>">Edit</a> |
        <a href="delete_trainer.php?id=<?= $t['id'] ?>"
           onclick="return confirm('Delete trainer?')">Delete</a>
    </td>
</tr>
<?php endforeach; ?>
</table>

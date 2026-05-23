<?php require_once '../../config/database.php';
require_once '../../includes/functions.php';
requireLogin();
if (isset($_GET['delete'])) {
    $pdo->prepare('DELETE FROM customers WHERE id=?')->execute([$_GET['delete']]);
    redirect('index.php');
}
$rows = $pdo->query('SELECT * FROM customers ORDER BY id DESC')->fetchAll();
include '../../includes/header.php'; ?>
<h1>Customers <a class="btn" href="form.php">Add Customer</a></h1>
<table>
    <tr>
        <th>Name</th>
        <th>Contact</th>
        <th>Email</th>
        <th>Address</th>
        <th>Actions</th>
    </tr><?php foreach ($rows as $r): ?>
        <tr>
            <td><?= e($r['name']) ?></td>
            <td><?= e($r['contact_number']) ?></td>
            <td><?= e($r['email']) ?></td>
            <td><?= e($r['address']) ?></td>
            <td><a class="btn gray" href="form.php?id=<?= $r['id'] ?>">Edit</a> <a class="btn"
                    href="history.php?id=<?= $r['id'] ?>">History</a> <a class="btn red" onclick="return confirm('Delete?')"
                    href="?delete=<?= $r['id'] ?>">Delete</a></td>
        </tr><?php endforeach; ?>
</table><?php include '../../includes/footer.php'; ?>
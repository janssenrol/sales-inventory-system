<?php require_once '../../config/database.php';
require_once '../../includes/functions.php';
requireLogin();
if (isset($_GET['delete'])) {
    $pdo->prepare('DELETE FROM categories WHERE id=?')->execute([$_GET['delete']]);
    redirect('index.php');
}
$rows = $pdo->query('SELECT * FROM categories ORDER BY id DESC')->fetchAll();
include '../../includes/header.php'; ?>
<h1>Categories <a class="btn" href="form.php">Add Category</a></h1>
<table>
    <tr>
        <th>Name</th>
        <th>Status</th>
        <th>Actions</th>
    </tr><?php foreach ($rows as $r): ?>
        <tr>
            <td><?= e($r['category_name']) ?></td>
            <td><?= e($r['status']) ?></td>
            <td><a class="btn gray" href="form.php?id=<?= $r['id'] ?>">Edit</a> <a class="btn red"
                    onclick="return confirm('Delete?')" href="?delete=<?= $r['id'] ?>">Delete</a></td>
        </tr><?php endforeach; ?>
</table><?php include '../../includes/footer.php'; ?>
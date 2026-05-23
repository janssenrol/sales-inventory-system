<?php require_once '../../config/database.php';
require_once '../../includes/functions.php';
requireLogin();
$id = $_GET['id'] ?? null;
$row = ['category_name' => '', 'status' => 'Active'];
if ($id) {
    $s = $pdo->prepare('SELECT * FROM categories WHERE id=?');
    $s->execute([$id]);
    $row = $s->fetch();
}
if ($_POST) {
    $data = [$_POST['category_name'], $_POST['status']];
    if ($id) {
        $pdo->prepare('UPDATE categories SET category_name=?, status=? WHERE id=?')->execute([...$data, $id]);
    } else {
        $pdo->prepare('INSERT INTO categories(category_name,status) VALUES(?,?)')->execute($data);
    }
    redirect('index.php');
}
include '../../includes/header.php'; ?>
<h1><?= $id ? 'Edit' : 'Add' ?> Category</h1>
<form class="card" method="post"><label>Category Name</label><input name="category_name" required
        value="<?= e($row['category_name']) ?>"><label>Status</label><select name="status">
        <option <?= $row['status'] == 'Active' ? 'selected' : '' ?>>Active</option>
        <option <?= $row['status'] == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
    </select><button>Save</button></form><?php include '../../includes/footer.php'; ?>
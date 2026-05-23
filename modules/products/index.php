<?php require_once '../../config/database.php';
require_once '../../includes/functions.php';
requireLogin();
if (isset($_GET['delete'])) {
    $pdo->prepare('DELETE FROM products WHERE id=?')->execute([$_GET['delete']]);
    redirect('index.php');
}
$q = $_GET['q'] ?? '';
$stmt = $pdo->prepare("SELECT p.*, c.category_name FROM products p JOIN categories c ON c.id=p.category_id WHERE p.product_name LIKE ? OR p.sku LIKE ? ORDER BY p.id DESC");
$stmt->execute(["%$q%", "%$q%"]);
$rows = $stmt->fetchAll();
include '../../includes/header.php'; ?>
<h1>Products <a class="btn" href="form.php">Add Product</a></h1>
<form class="noprint"><input name="q" placeholder="Search SKU or product" value="<?= e($q) ?>"></form>
<table>
    <tr>
        <th>Photo</th>
        <th>SKU</th>
        <th>Name</th>
        <th>Category</th>
        <th>Stock</th>
        <th>Price</th>
        <th>Cost</th>
        <th>Status</th>
        <th>Actions</th>
    </tr><?php foreach ($rows as $r): ?>
        <tr>
            <td><?php if ($r['product_photo']): ?><img src="../../uploads/<?= e($r['product_photo']) ?>"
                        width="45"><?php endif; ?></td>
            <td><?= e($r['sku']) ?></td>
            <td><?= e($r['product_name']) ?></td>
            <td><?= e($r['category_name']) ?></td>
            <td class="<?= $r['stock_on_hand'] <= $r['low_stock_alert'] ? 'low' : '' ?>"><?= e($r['stock_on_hand']) ?></td>
            <td><?= money($r['price']) ?></td>
            <td><?= money($r['cost_per_item']) ?></td>
            <td><?= e($r['status']) ?></td>
            <td><a class="btn gray" href="form.php?id=<?= $r['id'] ?>">Edit</a> <a class="btn red"
                    onclick="return confirm('Delete?')" href="?delete=<?= $r['id'] ?>">Delete</a></td>
        </tr><?php endforeach; ?>
</table><?php include '../../includes/footer.php'; ?>
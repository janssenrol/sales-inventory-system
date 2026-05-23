<?php require_once '../../config/database.php';
require_once '../../includes/functions.php';
requireLogin();
$id = $_GET['id'] ?? null;
$row = ['sku' => '', 'product_name' => '', 'category_id' => '', 'stock_on_hand' => 0, 'low_stock_alert' => 5, 'price' => 0, 'cost_per_item' => 0, 'product_photo' => '', 'status' => 'Active'];
if ($id) {
    $s = $pdo->prepare('SELECT * FROM products WHERE id=?');
    $s->execute([$id]);
    $row = $s->fetch();
}
$cats = $pdo->query("SELECT * FROM categories WHERE status='Active' ORDER BY category_name")->fetchAll();
if ($_POST) {
    $photo = $row['product_photo'];
    if (!empty($_FILES['product_photo']['name'])) {
        $photo = time() . '_' . basename($_FILES['product_photo']['name']);
        move_uploaded_file($_FILES['product_photo']['tmp_name'], '../../uploads/' . $photo);
    }
    $data = [$_POST['sku'], $_POST['product_name'], $_POST['category_id'], $_POST['stock_on_hand'], $_POST['low_stock_alert'], $_POST['price'], $_POST['cost_per_item'], $photo, $_POST['status']];
    if ($id) {
        $pdo->prepare('UPDATE products SET sku=?,product_name=?,category_id=?,stock_on_hand=?,low_stock_alert=?,price=?,cost_per_item=?,product_photo=?,status=? WHERE id=?')->execute([...$data, $id]);
    } else {
        $pdo->prepare('INSERT INTO products(sku,product_name,category_id,stock_on_hand,low_stock_alert,price,cost_per_item,product_photo,status) VALUES(?,?,?,?,?,?,?,?,?)')->execute($data);
    }
    redirect('index.php');
}
include '../../includes/header.php'; ?>
<h1><?= $id ? 'Edit' : 'Add' ?> Product</h1>
<form class="card" method="post" enctype="multipart/form-data">
    <div class="row">
        <div><label>SKU</label><input name="sku" required value="<?= e($row['sku']) ?>"></div>
        <div><label>Product Name</label><input name="product_name" required value="<?= e($row['product_name']) ?>"></div>
        <div><label>Category</label><select name="category_id"><?php foreach ($cats as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $row['category_id'] == $c['id'] ? 'selected' : '' ?>>
                        <?= e($c['category_name']) ?></option><?php endforeach; ?>
            </select></div>
        <div><label>Stock</label><input type="number" name="stock_on_hand" value="<?= e($row['stock_on_hand']) ?>"></div>
        <div><label>Low Stock Alert</label><input type="number" name="low_stock_alert"
                value="<?= e($row['low_stock_alert']) ?>"></div>
        <div><label>Price</label><input type="number" step="0.01" name="price" value="<?= e($row['price']) ?>"></div>
        <div><label>Cost Per Item</label><input type="number" step="0.01" name="cost_per_item"
                value="<?= e($row['cost_per_item']) ?>"></div>
        <div><label>Status</label><select name="status">
                <option <?= $row['status'] == 'Active' ? 'selected' : '' ?>>Active</option>
                <option <?= $row['status'] == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
            </select></div>
        <div><label>Photo</label><input type="file" name="product_photo"></div>
    </div><button>Save</button>
</form><?php include '../../includes/footer.php'; ?>
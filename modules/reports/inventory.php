<?php require_once '../../config/database.php';
require_once '../../includes/functions.php';
requireLogin();
if (isset($_GET['export'])) {
    header('Content-Type:text/csv');
    header('Content-Disposition: attachment; filename=inventory_report.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Product', 'Category', 'Stock', 'Inventory Cost', 'Forecasted Sales', 'Status']);
}
$where = 'WHERE 1=1';
$params = [];
if (!empty($_GET['category'])) {
    $where .= ' AND c.category_name LIKE ?';
    $params[] = '%' . $_GET['category'] . '%';
}
if (!empty($_GET['status'])) {
    $where .= ' AND p.status=?';
    $params[] = $_GET['status'];
}
$stmt = $pdo->prepare("SELECT p.*, c.category_name, (p.stock_on_hand*p.cost_per_item) inventory_cost, (p.stock_on_hand*p.price) forecasted_sales FROM products p JOIN categories c ON c.id=p.category_id $where ORDER BY p.product_name");
$stmt->execute($params);
$rows = $stmt->fetchAll();
if (isset($_GET['export'])) {
    foreach ($rows as $r) {
        fputcsv($out, [$r['product_name'], $r['category_name'], $r['stock_on_hand'], $r['inventory_cost'], $r['forecasted_sales'], $r['status']]);
    }
    exit;
}
$active = 0;
$out = 0;
$low = 0;
$cost = 0;
$forecast = 0;
foreach ($rows as $r) {
    if ($r['status'] == 'Active')
        $active++;
    if ($r['stock_on_hand'] <= 0)
        $out++;
    if ($r['stock_on_hand'] <= $r['low_stock_alert'])
        $low++;
    $cost += $r['inventory_cost'];
    $forecast += $r['forecasted_sales'];
}
include '../../includes/header.php'; ?>
<h1>Inventory Report</h1>
<form class="card noprint">
    <div class="row"><input name="category" placeholder="Category" value="<?= e($_GET['category'] ?? '') ?>"><select
            name="status">
            <option value="">All Status</option>
            <option <?= ($_GET['status'] ?? '') == 'Active' ? 'selected' : '' ?>>Active</option>
            <option <?= ($_GET['status'] ?? '') == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>
        <div><button>Filter</button> <a class="btn gray" onclick="print()">Print</a> <a class="btn"
                href="?<?= http_build_query(array_merge($_GET, ['export' => 1])) ?>">Download CSV File</a></div>
    </div>
</form>
<div class="grid">
    <div class="card">Active Products<div class="metric"><?= $active ?></div>
    </div>
    <div class="card">Out of Stock<div class="metric"><?= $out ?></div>
    </div>
    <div class="card">Low Stocks<div class="metric"><?= $low ?></div>
    </div>
    <div class="card">Total Inventory Cost<div class="metric"><?= money($cost) ?></div>
    </div>
    <div class="card">Forecasted Sales<div class="metric"><?= money($forecast) ?></div>
    </div>
</div>
<table>
    <tr>
        <th>Product Name</th>
        <th>Category</th>
        <th>Stock on Hand</th>
        <th>Inventory Cost</th>
        <th>Forecasted Sales</th>
        <th>Status</th>
    </tr><?php foreach ($rows as $r): ?>
        <tr>
            <td><?= e($r['product_name']) ?></td>
            <td><?= e($r['category_name']) ?></td>
            <td><?= e($r['stock_on_hand']) ?></td>
            <td><?= money($r['inventory_cost']) ?></td>
            <td><?= money($r['forecasted_sales']) ?></td>
            <td><?= e($r['status']) ?></td>
        </tr><?php endforeach; ?>
</table>
<p><a href="sales.php">Go to Sales Report</a></p><?php include '../../includes/footer.php'; ?>
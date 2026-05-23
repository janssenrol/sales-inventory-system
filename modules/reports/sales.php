<?php require_once '../../config/database.php';
require_once '../../includes/functions.php';
requireLogin();
if (isset($_GET['export'])) {
    header('Content-Type:text/csv');
    header('Content-Disposition: attachment; filename=sales_report.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Invoice', 'Order Date', 'Payment Type', 'Sales Amount', 'Cost', 'Net Sales', 'Status']);
}
$where = 'WHERE 1=1';
$params = [];
if (!empty($_GET['date'])) {
    $where .= ' AND o.order_date=?';
    $params[] = $_GET['date'];
}
if (!empty($_GET['customer'])) {
    $where .= ' AND c.name LIKE ?';
    $params[] = '%' . $_GET['customer'] . '%';
}
if (!empty($_GET['status'])) {
    $where .= ' AND o.status=?';
    $params[] = $_GET['status'];
}
$stmt = $pdo->prepare("SELECT o.*, c.name customer_name FROM orders o JOIN customers c ON c.id=o.customer_id $where ORDER BY o.order_date DESC");
$stmt->execute($params);
$rows = $stmt->fetchAll();
if (isset($_GET['export'])) {
    foreach ($rows as $r) {
        fputcsv($out, [$r['order_no'], $r['order_date'], $r['payment_type'], $r['total_amount'], $r['total_cost'], $r['total_amount'] - $r['total_cost'], $r['status']]);
    }
    exit;
}
include '../../includes/header.php'; ?>
<h1>Sales Report</h1>
<form class="card noprint">
    <div class="row">
        <div><label>Date</label><input type="date" name="date" value="<?= e($_GET['date'] ?? '') ?>"></div>
        <div><label>Customer</label><input name="customer" value="<?= e($_GET['customer'] ?? '') ?>"></div>
        <div><label>Status</label><select name="status">
                <option value="">All</option>
                <?php foreach (['Unpaid', 'Partial', 'Paid', 'Overdue', 'Draft', 'Cancelled'] as $s): ?>
                    <option <?= ($_GET['status'] ?? '') == $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?>
            </select></div>
        <div><label>&nbsp;</label><button>Filter</button> <a class="btn gray" onclick="print()">Print</a> <a class="btn"
                href="?<?= http_build_query(array_merge($_GET, ['export' => 1])) ?>">Export CSV</a></div>
    </div>
</form>
<table>
    <tr>
        <th>Sales Invoice Number</th>
        <th>Order Date</th>
        <th>Payment Type</th>
        <th>Sales Amount</th>
        <th>Cost</th>
        <th>Net Sales</th>
        <th>Status</th>
    </tr><?php foreach ($rows as $r): ?>
        <tr>
            <td><?= e($r['order_no']) ?></td>
            <td><?= e($r['order_date']) ?></td>
            <td><?= e($r['payment_type']) ?></td>
            <td><?= money($r['total_amount']) ?></td>
            <td><?= money($r['total_cost']) ?></td>
            <td><?= money($r['total_amount'] - $r['total_cost']) ?></td>
            <td><?= e($r['status']) ?></td>
        </tr><?php endforeach; ?>
</table>
<p><a href="inventory.php">Go to Inventory Report</a></p><?php include '../../includes/footer.php'; ?>
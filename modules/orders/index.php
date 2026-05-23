<?php require_once '../../config/database.php';
require_once '../../includes/functions.php';
requireLogin();
$tab = $_GET['tab'] ?? 'All';
$q = $_GET['q'] ?? '';
$where = "WHERE (o.order_no LIKE ? OR c.name LIKE ?)";
$params = ["%$q%", "%$q%"];
if ($tab !== 'All') {
    $where .= ' AND o.status=?';
    $params[] = $tab;
}
$sql = "SELECT o.*, c.name customer_name, COALESCE((SELECT SUM(amount) FROM payments p WHERE p.order_id=o.id),0) paid FROM orders o JOIN customers c ON c.id=o.customer_id $where ORDER BY o.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
include '../../includes/header.php'; ?>
<h1>Orders <a class="btn" href="create.php">Add Order</a></h1>
<div class="tabs noprint"><?php foreach (['All', 'Unpaid', 'Partial', 'Paid', 'Overdue', 'Draft', 'Cancelled'] as $t): ?><a
            class="btn <?= $tab == $t ? '' : 'gray' ?>" href="?tab=<?= $t ?>"><?= $t ?></a><?php endforeach; ?></div>
<form class="noprint"><input type="hidden" name="tab" value="<?= e($tab) ?>"><input name="q"
        placeholder="Search Order No. / Customer Name" value="<?= e($q) ?>"></form>
<table>
    <tr>
        <th>Order No.</th>
        <th>Order Date</th>
        <th>Customer</th>
        <th>Payment Type</th>
        <th>Total</th>
        <th>Paid</th>
        <th>Status</th>
        <th>Actions</th>
    </tr><?php foreach ($rows as $r): ?>
        <tr>
            <td><?= e($r['order_no']) ?></td>
            <td><?= e($r['order_date']) ?></td>
            <td><?= e($r['customer_name']) ?></td>
            <td><?= e($r['payment_type']) ?></td>
            <td><?= money($r['total_amount']) ?></td>
            <td><?= money($r['paid']) ?></td>
            <td><span class="badge"><?= e($r['status']) ?></span></td>
            <td><a class="btn" href="view.php?id=<?= $r['id'] ?>">View</a></td>
        </tr><?php endforeach; ?>
</table><?php include '../../includes/footer.php'; ?>
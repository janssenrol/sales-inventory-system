<?php require_once '../../config/database.php';
require_once '../../includes/functions.php';
requireLogin();
$id = $_GET['id'];
$s = $pdo->prepare('SELECT * FROM customers WHERE id=?');
$s->execute([$id]);
$c = $s->fetch();
$s = $pdo->prepare('SELECT * FROM orders WHERE customer_id=? ORDER BY order_date DESC');
$s->execute([$id]);
$orders = $s->fetchAll();
include '../../includes/header.php'; ?>
<h1>Order History: <?= e($c['name']) ?></h1>
<table>
    <tr>
        <th>Order No.</th>
        <th>Date</th>
        <th>Total</th>
        <th>Status</th>
    </tr><?php foreach ($orders as $o): ?>
        <tr>
            <td><?= e($o['order_no']) ?></td>
            <td><?= e($o['order_date']) ?></td>
            <td><?= money($o['total_amount']) ?></td>
            <td><?= e($o['status']) ?></td>
        </tr><?php endforeach; ?>
</table><?php include '../../includes/footer.php'; ?>
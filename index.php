<?php require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
requireLogin();
include __DIR__ . '/includes/header.php';
$stats = $pdo->query("SELECT COALESCE(SUM(total_amount),0) sales, COALESCE(SUM(total_cost),0) cost, COUNT(*) orders FROM orders WHERE status <> 'Cancelled'")->fetch();
$payments = $pdo->query("SELECT COALESCE(SUM(amount),0) total FROM payments")->fetchColumn();
$net = $stats['sales'] - $stats['cost'];
$daily = $pdo->query("SELECT order_date, SUM(total_amount) total FROM orders WHERE status <> 'Cancelled' GROUP BY order_date ORDER BY order_date DESC LIMIT 7")->fetchAll();
$paydist = $pdo->query("SELECT payment_type, COUNT(*) cnt FROM orders GROUP BY payment_type")->fetchAll();
$topProducts = $pdo->query("SELECT p.product_name, SUM(oi.quantity) qty FROM order_items oi JOIN products p ON p.id=oi.product_id JOIN orders o ON o.id=oi.order_id WHERE o.status <> 'Cancelled' GROUP BY p.id ORDER BY qty DESC LIMIT 5")->fetchAll();
$topCategories = $pdo->query("SELECT c.category_name, SUM(oi.quantity) qty FROM order_items oi JOIN products p ON p.id=oi.product_id JOIN categories c ON c.id=p.category_id JOIN orders o ON o.id=oi.order_id WHERE o.status <> 'Cancelled' GROUP BY c.id ORDER BY qty DESC LIMIT 5")->fetchAll();
$low = $pdo->query("SELECT product_name, stock_on_hand, low_stock_alert FROM products WHERE stock_on_hand <= low_stock_alert ORDER BY stock_on_hand")->fetchAll();
?>
<h1>Dashboard</h1>
<div class="grid">
    <div class="card">
        <p>Total Sales</p>
        <div class="metric"><?= money($stats['sales']) ?></div>
    </div>
    <div class="card">
        <p>Total Cost</p>
        <div class="metric"><?= money($stats['cost']) ?></div>
    </div>
    <div class="card">
        <p>Total Net Sales</p>
        <div class="metric"><?= money($net) ?></div>
    </div>
    <div class="card">
        <p>Number of Orders</p>
        <div class="metric"><?= e($stats['orders']) ?></div>
    </div>
    <div class="card">
        <p>Payments Collected</p>
        <div class="metric"><?= money($payments) ?></div>
    </div>
</div>
<div class="grid">
    <div class="card">
        <h3>Payment Type Distribution</h3>
        <table><?php foreach ($paydist as $r): ?>
                <tr>
                    <td><?= e($r['payment_type']) ?></td>
                    <td><?= e($r['cnt']) ?></td>
                </tr><?php endforeach; ?>
        </table>
    </div>
    <div class="card">
        <h3>Daily Sales</h3>
        <table><?php foreach ($daily as $r): ?>
                <tr>
                    <td><?= e($r['order_date']) ?></td>
                    <td><?= money($r['total']) ?></td>
                </tr><?php endforeach; ?>
        </table>
    </div>
    <div class="card">
        <h3>Top Selling Products</h3>
        <table><?php foreach ($topProducts as $r): ?>
                <tr>
                    <td><?= e($r['product_name']) ?></td>
                    <td><?= e($r['qty']) ?></td>
                </tr><?php endforeach; ?>
        </table>
    </div>
    <div class="card">
        <h3>Top Categories</h3>
        <table><?php foreach ($topCategories as $r): ?>
                <tr>
                    <td><?= e($r['category_name']) ?></td>
                    <td><?= e($r['qty']) ?></td>
                </tr><?php endforeach; ?>
        </table>
    </div>
    <div class="card">
        <h3>Low Stock Products</h3>
        <table><?php foreach ($low as $r): ?>
                <tr>
                    <td><?= e($r['product_name']) ?></td>
                    <td class="low"><?= e($r['stock_on_hand']) ?> / <?= e($r['low_stock_alert']) ?></td>
                </tr><?php endforeach; ?>
        </table>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
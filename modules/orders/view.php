<?php require_once '../../config/database.php';
require_once '../../includes/functions.php';
requireLogin();
$id = $_GET['id'];
if ($_POST && isset($_POST['add_payment'])) {
    $pdo->prepare('INSERT INTO payments(order_id,payment_date,amount,payment_method) VALUES(?,?,?,?)')->execute([$id, $_POST['payment_date'], $_POST['amount'], $_POST['payment_method']]);
    addLog($pdo, $id, 'Payment added: ' . money($_POST['amount']));
    recomputeOrderStatus($pdo, $id);
    redirect('view.php?id=' . $id);
}
if (isset($_GET['cancel'])) {
    $pdo->beginTransaction();
    $items = $pdo->prepare('SELECT * FROM order_items WHERE order_id=?');
    $items->execute([$id]);
    foreach ($items as $it) {
        $pdo->prepare('UPDATE products SET stock_on_hand=stock_on_hand+? WHERE id=?')->execute([$it['quantity'], $it['product_id']]);
    }
    $pdo->prepare("UPDATE orders SET status='Cancelled' WHERE id=?")->execute([$id]);
    addLog($pdo, $id, 'Order cancelled and inventory returned.');
    $pdo->commit();
    redirect('view.php?id=' . $id);
}
$s = $pdo->prepare('SELECT o.*, c.name customer_name FROM orders o JOIN customers c ON c.id=o.customer_id WHERE o.id=?');
$s->execute([$id]);
$o = $s->fetch();
$items = $pdo->prepare('SELECT oi.*, p.product_name FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE order_id=?');
$items->execute([$id]);
$pays = $pdo->prepare('SELECT * FROM payments WHERE order_id=? ORDER BY payment_date');
$pays->execute([$id]);
$logs = $pdo->prepare('SELECT * FROM order_logs WHERE order_id=? ORDER BY id DESC');
$logs->execute([$id]);
include '../../includes/header.php'; ?>
<h1>Order <?= e($o['order_no']) ?> <a class="btn gray" href="index.php">Back</a> <?php if ($o['status'] != 'Cancelled'): ?><a
            class="btn red" onclick="return confirm('Cancel this order and return stock?')"
            href="?id=<?= $id ?>&cancel=1">Cancel</a><?php endif; ?></h1>
<div class="grid">
    <div class="card"><b>Customer</b>
        <p><?= e($o['customer_name']) ?></p>
    </div>
    <div class="card"><b>Status</b>
        <p><?= e($o['status']) ?></p>
    </div>
    <div class="card"><b>Total</b>
        <p><?= money($o['total_amount']) ?></p>
    </div>
    <div class="card"><b>Balance</b>
        <p><?= money($o['total_amount'] - paidAmount($pdo, $id)) ?></p>
    </div>
</div>
<div class="card">
    <h3>Items</h3>
    <table>
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
        </tr><?php foreach ($items as $it): ?>
            <tr>
                <td><?= e($it['product_name']) ?></td>
                <td><?= e($it['quantity']) ?></td>
                <td><?= money($it['unit_price']) ?></td>
                <td><?= money($it['line_total']) ?></td>
            </tr><?php endforeach; ?>
    </table>
    <p>Subtotal: <?= money($o['subtotal']) ?> | Discount: <?= money($o['discount']) ?> | Shipping:
        <?= money($o['shipping_fee']) ?></p>
</div>
<div class="card">
    <h3>Add Payment</h3>
    <form method="post" class="row"><input type="hidden" name="add_payment" value="1">
        <div><label>Date</label><input type="date" name="payment_date" value="<?= date('Y-m-d') ?>"></div>
        <div><label>Amount</label><input type="number" step="0.01" name="amount" required></div>
        <div><label>Method</label><select name="payment_method">
                <option>Cash</option>
                <option>GCash</option>
                <option>Bank Transfer</option>
                <option>Credit Card</option>
            </select></div>
        <div><label>&nbsp;</label><button>Add Payment</button></div>
    </form>
    <table>
        <tr>
            <th>Date</th>
            <th>Amount</th>
            <th>Method</th>
        </tr><?php foreach ($pays as $p): ?>
            <tr>
                <td><?= e($p['payment_date']) ?></td>
                <td><?= money($p['amount']) ?></td>
                <td><?= e($p['payment_method']) ?></td>
            </tr><?php endforeach; ?>
    </table>
</div>
<div class="card">
    <h3>Logs</h3><?php foreach ($logs as $l): ?>
        <p><?= e($l['created_at']) ?> - <?= e($l['activity']) ?></p><?php endforeach; ?>
</div><?php include '../../includes/footer.php'; ?>
<?php require_once '../../config/database.php';
require_once '../../includes/functions.php';
requireLogin();
$customers = $pdo->query('SELECT * FROM customers ORDER BY name')->fetchAll();
$products = $pdo->query("SELECT * FROM products WHERE status='Active' ORDER BY product_name")->fetchAll();
if ($_POST) {
  $pdo->beginTransaction();
  try {
    $customer_id = $_POST['customer_id'];
    if ($customer_id === 'new') {
      $pdo->prepare('INSERT INTO customers(name,contact_number,email,address) VALUES(?,?,?,?)')->execute([$_POST['new_name'], $_POST['new_contact'], $_POST['new_email'], $_POST['new_address']]);
      $customer_id = $pdo->lastInsertId();
    }
    $orderNo = 'SO-' . date('Ymd-His');
    $subtotal = 0;
    $cost = 0;
    foreach ($_POST['items'] as $productId => $qty) {
      if ((int) $qty > 0) {
        $p = array_values(array_filter($products, fn($x) => $x['id'] == $productId))[0];
        $subtotal += $p['price'] * $qty;
        $cost += $p['cost_per_item'] * $qty;
      }
    }
    $discount = (float) $_POST['discount'];
    $shipping = (float) $_POST['shipping_fee'];
    $total = max(0, $subtotal - $discount + $shipping);
    $pdo->prepare('INSERT INTO orders(order_no,order_date,customer_id,payment_type,discount,shipping_fee,subtotal,total_amount,total_cost,status,due_date,is_confirmed) VALUES(?,?,?,?,?,?,?,?,?,?,?,1)')->execute([$orderNo, $_POST['order_date'], $customer_id, $_POST['payment_type'], $discount, $shipping, $subtotal, $total, $cost, 'Unpaid', $_POST['due_date'] ?: null]);
    $orderId = $pdo->lastInsertId();
    foreach ($_POST['items'] as $productId => $qty) {
      if ((int) $qty > 0) {
        $s = $pdo->prepare('SELECT * FROM products WHERE id=? FOR UPDATE');
        $s->execute([$productId]);
        $p = $s->fetch();
        if ($p['stock_on_hand'] < $qty)
          throw new Exception($p['product_name'] . ' has insufficient stock.');
        $pdo->prepare('INSERT INTO order_items(order_id,product_id,quantity,unit_price,unit_cost,line_total) VALUES(?,?,?,?,?,?)')->execute([$orderId, $productId, $qty, $p['price'], $p['cost_per_item'], $p['price'] * $qty]);
        $pdo->prepare('UPDATE products SET stock_on_hand=stock_on_hand-? WHERE id=?')->execute([$qty, $productId]);
      }
    }
    addLog($pdo, $orderId, 'Order confirmed and inventory deducted.');
    if ((float) $_POST['payment_amount'] > 0) {
      $pdo->prepare('INSERT INTO payments(order_id,payment_date,amount,payment_method) VALUES(?,?,?,?)')->execute([$orderId, $_POST['order_date'], $_POST['payment_amount'], $_POST['payment_method']]);
      addLog($pdo, $orderId, 'Payment added: ' . money($_POST['payment_amount']));
    }
    recomputeOrderStatus($pdo, $orderId);
    $pdo->commit();
    redirect('view.php?id=' . $orderId);
  } catch (Exception $e) {
    $pdo->rollBack();
    $error = $e->getMessage();
  }
}
include '../../includes/header.php'; ?>
<h1>Create Order</h1><?php if (!empty($error)): ?>
  <div class="card low"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="card">
  <div class="row">
    <div><label>Order Date</label><input type="date" name="order_date" required value="<?= date('Y-m-d') ?>"></div>
    <div><label>Due Date</label><input type="date" name="due_date"></div>
    <div><label>Payment Type</label><select name="payment_type">
        <option>Cash</option>
        <option>GCash</option>
        <option>Bank Transfer</option>
        <option>Credit Card</option>
      </select></div>
    <div><label>Customer</label><select name="customer_id" id="customer">
        <option value="new">Add New Customer</option><?php foreach ($customers as $c): ?>
          <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?>
      </select></div>
  </div>
  <div class="row"><input name="new_name" placeholder="New customer name"><input name="new_contact"
      placeholder="Contact"><input name="new_email" placeholder="Email"><input name="new_address" placeholder="Address">
  </div>
  <h3>Browse Products</h3>
  <table>
    <tr>
      <th>Product</th>
      <th>Stock</th>
      <th>Price</th>
      <th>Qty</th>
    </tr><?php foreach ($products as $p): ?>
      <tr>
        <td><?= e($p['product_name']) ?></td>
        <td><?= e($p['stock_on_hand']) ?></td>
        <td><?= money($p['price']) ?></td>
        <td><input type="number" min="0" max="<?= e($p['stock_on_hand']) ?>" name="items[<?= $p['id'] ?>]" value="0"></td>
      </tr><?php endforeach; ?>
  </table>
  <div class="row">
    <div><label>Discount</label><input type="number" step="0.01" name="discount" value="0"></div>
    <div><label>Shipping Fee</label><input type="number" step="0.01" name="shipping_fee" value="0"></div>
    <div><label>Initial Payment</label><input type="number" step="0.01" name="payment_amount" value="0"></div>
    <div><label>Payment Method</label><select name="payment_method">
        <option>Cash</option>
        <option>GCash</option>
        <option>Bank Transfer</option>
        <option>Credit Card</option>
      </select></div>
  </div><button>Create Order</button>
</form><?php include '../../includes/footer.php'; ?>
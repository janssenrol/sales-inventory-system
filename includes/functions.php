<?php
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
function redirect($url)
{
    header("Location: $url");
    exit;
}
function money($n)
{
    return 'PHP ' . number_format((float) $n, 2);
}
function paidAmount(PDO $pdo, $orderId)
{
    $stmt = $pdo->prepare('SELECT COALESCE(SUM(amount),0) total FROM payments WHERE order_id=?');
    $stmt->execute([$orderId]);
    return (float) $stmt->fetchColumn();
}
function recomputeOrderStatus(PDO $pdo, $orderId)
{
    $stmt = $pdo->prepare('SELECT total_amount, due_date, status FROM orders WHERE id=?');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order || $order['status'] === 'Cancelled')
        return;
    $paid = paidAmount($pdo, $orderId);
    if ($paid <= 0)
        $status = 'Unpaid';
    elseif ($paid < (float) $order['total_amount'])
        $status = 'Partial';
    else
        $status = 'Paid';
    if ($status !== 'Paid' && !empty($order['due_date']) && $order['due_date'] < date('Y-m-d'))
        $status = 'Overdue';
    $pdo->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$status, $orderId]);
}
function addLog(PDO $pdo, $orderId, $activity)
{
    $pdo->prepare('INSERT INTO order_logs(order_id, activity) VALUES(?,?)')->execute([$orderId, $activity]);
}
function requireLogin()
{
    if (session_status() === PHP_SESSION_NONE)
        session_start();
    if (empty($_SESSION['user_id']))
        redirect('/sales_inventory_php/login.php');
}
?>
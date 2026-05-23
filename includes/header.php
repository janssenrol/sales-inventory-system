<?php if (session_status() === PHP_SESSION_NONE)
    session_start();
require_once __DIR__ . '/functions.php'; ?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sales & Inventory</title>
    <link rel="stylesheet" href="/sales_inventory_php/assets/css/style.css">
</head>

<body>
    <nav class="top"><b>Sales & Inventory</b><span></span><a href="/sales_inventory_php/index.php">Dashboard</a><a
            href="/sales_inventory_php/modules/orders/index.php">Orders</a><a
            href="/sales_inventory_php/modules/customers/index.php">Customers</a><a
            href="/sales_inventory_php/modules/categories/index.php">Categories</a><a
            href="/sales_inventory_php/modules/products/index.php">Products</a><a
            href="/sales_inventory_php/modules/reports/sales.php">Reports</a><a
            href="/sales_inventory_php/logout.php">Logout</a></nav>
    <main class="container">
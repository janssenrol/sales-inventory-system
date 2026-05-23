<?php require_once '../../config/database.php';
require_once '../../includes/functions.php';
requireLogin();
$id = $_GET['id'] ?? null;
$row = ['name' => '', 'contact_number' => '', 'email' => '', 'address' => ''];
if ($id) {
    $s = $pdo->prepare('SELECT * FROM customers WHERE id=?');
    $s->execute([$id]);
    $row = $s->fetch();
}
if ($_POST) {
    $data = [$_POST['name'], $_POST['contact_number'], $_POST['email'], $_POST['address']];
    if ($id) {
        $pdo->prepare('UPDATE customers SET name=?,contact_number=?,email=?,address=? WHERE id=?')->execute([...$data, $id]);
    } else {
        $pdo->prepare('INSERT INTO customers(name,contact_number,email,address) VALUES(?,?,?,?)')->execute($data);
    }
    redirect('index.php');
}
include '../../includes/header.php'; ?>
<h1><?= $id ? 'Edit' : 'Add' ?> Customer</h1>
<form method="post" class="card"><label>Name</label><input name="name" required
        value="<?= e($row['name']) ?>"><label>Contact Number</label><input name="contact_number"
        value="<?= e($row['contact_number']) ?>"><label>Email</label><input name="email"
        value="<?= e($row['email']) ?>"><label>Address</label><textarea
        name="address"><?= e($row['address']) ?></textarea><button>Save</button></form>
<?php include '../../includes/footer.php'; ?>
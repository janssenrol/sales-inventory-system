<?php session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $login = $_POST['login'] ?? '';
  $pass = $_POST['password'] ?? '';
  $stmt = $pdo->prepare('SELECT * FROM users WHERE username=? OR email=?');
  $stmt->execute([$login, $login]);
  $user = $stmt->fetch();
  if ($user && password_verify($pass, $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    redirect('index.php');
  }
  $error = 'Invalid login.';
}
?><!doctype html>
<html>

<head>
  <title>Login</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
  <main class="container" style="max-width:420px">
    <div class="card">
      <h2>Login</h2><?php if ($error): ?>
        <p class="low"><?= e($error) ?></p><?php endif; ?>
      <form method="post"><label>Email/Username</label><input name="login" required><label>Password</label><input
          type="password" name="password" required><button>Login</button>
        <p>Demo: admin / password</p>
      </form>
    </div>
  </main>
</body>

</html>
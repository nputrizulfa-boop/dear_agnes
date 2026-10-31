<?php
session_start();
include "../koneksi.php";

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, username, password_hash FROM admin WHERE username = ?");
    $stmt->bind_param("s", $u);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if ($row && password_verify($p, $row['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin']      = true;
        $_SESSION['admin_id']   = $row['id'];
        $_SESSION['admin_user'] = $row['username'];
        header("Location: dashboard.php");
        exit;
    }
    $error = "Username atau password salah.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Admin</title>
  <?php include "../tema.php"; ?>
</head>
<body class="bg-slate-50 min-h-screen flex items-start justify-center p-4">
  <div class="w-full max-w-sm bg-white rounded-3xl shadow-lg p-8 mt-16">
    <h1 class="text-xl font-bold text-center text-gray-800">Login Mrs. Agnes</h1>
    <?php if ($error): ?>
      <p class="mt-4 text-sm text-red-600 bg-red-50 rounded-xl p-3 text-center"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post" class="mt-5 space-y-4">
      <input type="text" name="username" placeholder="Username" required
        class="w-full px-4 py-3 rounded-xl bg-slate-100 text-sm outline-none focus:ring-2 focus:ring-sage-400">
      <input type="password" name="password" placeholder="Password" required
        class="w-full px-4 py-3 rounded-xl bg-slate-100 text-sm outline-none focus:ring-2 focus:ring-sage-400">
      <button type="submit" class="w-full bg-sage-600 hover:bg-sage-700 text-white font-semibold py-3 rounded-xl">Masuk</button>
    </form>
  </div>
</body>
</html>
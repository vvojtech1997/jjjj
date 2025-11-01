<?php
require_once 'includes/db.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $mysqli->real_escape_string(trim($_POST['email']));
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];
    $name = $mysqli->real_escape_string(trim($_POST['name'] ?? ''));
    if ($password !== $confirm) $msg = 'Heslá sa nezhodujú.';
    else {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $mysqli->prepare("INSERT INTO users (email,password,name,created_at) VALUES (?,?,?,NOW())");
      $stmt->bind_param('sss', $email, $hash, $name);
      if ($stmt->execute()){
        header('Location: /login.php'); exit;
      } else $msg = 'Registrácia zlyhala (email už môže byť použitý).';
    }
}
include 'includes/header.php';
?>
<div class="auth-container container">
  <h2>Vytvorte si účet</h2>
  <?php if($msg) echo '<p class="error">'.htmlspecialchars($msg).'</p>'; ?>
  <form method="post">
    <input type="text" name="name" placeholder="Meno (voliteľné)">
    <input type="email" name="email" placeholder="E-mail" required>
    <input type="password" name="password" placeholder="Heslo" required>
    <input type="password" name="confirm" placeholder="Potvrďte heslo" required>
    <button class="btn-primary" type="submit">Vytvoriť účet</button>
  </form>
  <p>Už máte účet? <a href="/login.php">Prihlásiť sa</a></p>
</div>
<?php include 'includes/footer.php'; ?>
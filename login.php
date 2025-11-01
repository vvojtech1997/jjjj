<?php
session_start();
require_once 'includes/db.php';
$error = '';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = $mysqli->real_escape_string(trim($_POST['email']));
    $password = $_POST['password'];
    $res = $mysqli->query("SELECT id,password,is_admin FROM users WHERE email='$email' LIMIT 1");
    if($res && $row = $res->fetch_assoc()){
        if(password_verify($password, $row['password'])){
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['is_admin'] = $row['is_admin'];
            header('Location: /dashboard.php'); exit;
        } else $error = 'Nesprávne heslo.';
    } else $error = 'Účet neexistuje.';
}
include 'includes/header.php';
?>
<div class="auth-container container">
  <h2>Prihlásenie</h2>
  <?php if($error) echo '<p class="error">'.htmlspecialchars($error).'</p>'; ?>
  <form method="post">
    <input type="email" name="email" placeholder="E-mail" required>
    <input type="password" name="password" placeholder="Heslo" required>
    <button class="btn-primary" type="submit">Prihlásiť sa</button>
  </form>
  <p>Nemáte účet? <a href="/register.php">Registrovať</a></p>
</div>
<?php include 'includes/footer.php'; ?>
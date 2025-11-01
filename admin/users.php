<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
if(empty($_SESSION['is_admin'])){ header('Location:/login.php'); exit; }
$res = $mysqli->query("SELECT id,email,name,is_admin,created_at FROM users ORDER BY id DESC LIMIT 200");
include __DIR__ . '/../includes/header.php';
?>
<div class="container">
  <h1>Užívatelia</h1>
  <?php while($r = $res->fetch_assoc()){ echo '<div>'.htmlspecialchars($r['email']).' - '.htmlspecialchars($r['name']).'</div>'; } ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
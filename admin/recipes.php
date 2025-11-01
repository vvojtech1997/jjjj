<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
if(empty($_SESSION['is_admin'])){ header('Location:/login.php'); exit; }
$res = $mysqli->query("SELECT id,name,mealType,estimatedCost FROM recipes ORDER BY id DESC LIMIT 200");
include __DIR__ . '/../includes/header.php';
?>
<div class="container">
  <h1>Recepty</h1>
  <?php while($r = $res->fetch_assoc()){ echo '<div class="recept">'.htmlspecialchars($r['name']).' - '.htmlspecialchars($r['mealType']).' ('.htmlspecialchars($r['estimatedCost']).'€)</div>'; } ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
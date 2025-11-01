<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
if(empty($_SESSION['is_admin'])){ header('Location:/login.php'); exit; }
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST' && !empty($_POST['name']) && !empty($_POST['rss'])){
  $name = $mysqli->real_escape_string($_POST['name']);
  $rss = $mysqli->real_escape_string($_POST['rss']);
  $mysqli->query("INSERT INTO shops (name,rss_url,active,created_at) VALUES ('$name','$rss',1,NOW())");
  $msg='Pridané.';
}
if(isset($_GET['run_import'])){
  require_once __DIR__ . '/../includes/parser_varecha.php';
  $count = import_varecha($mysqli, 50);
  $msg = 'Import completed: ' . intval($count);
}
include __DIR__ . '/../includes/header.php';
?>
<div class="container">
  <h1>Nastavenia / Shops</h1>
  <?php if($msg) echo '<p class="info">'.htmlspecialchars($msg).'</p>'; ?>
  <form method="post">
    <label>Názov obchodu</label><input name="name" required>
    <label>RSS URL</label><input name="rss" required>
    <button class="btn-primary" type="submit">Pridať</button>
  </form>
  <p><a class="btn" href="?run_import=1">Spusti import (varecha)</a></p>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
if(empty($_SESSION['is_admin'])){ header('Location:/login.php'); exit; }
include __DIR__ . '/../includes/header.php';
?>
<div class="container">
  <h1>Admin - Dashboard</h1>
  <ul>
    <li><a href="/admin/recipes.php">Recepty</a></li>
    <li><a href="/admin/users.php">Užívatelia</a></li>
    <li><a href="/admin/settings.php">Nastavenia (RSS/shops)</a></li>
  </ul>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
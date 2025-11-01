<?php
session_start();
require_once 'includes/db.php';
if(!isset($_SESSION['user_id'])) { header('Location:/login.php'); exit; }
include 'includes/header.php';
?>
<div class="container card">
  <h2>Generovať týždenný plán</h2>
  <form method="post" action="/api/api_generate_plan.php">
    <label>Počet osôb</label>
    <input type="number" name="people" value="2" min="1">
    <label>Rozpočet (€ / týždeň) - prázdne = bez limitu</label>
    <input type="number" step="0.01" name="budget">
    <label>Typ stravy</label>
    <select name="goal">
      <option value="classic">Klasická</option>
      <option value="healthy">Zdravá</option>
      <option value="vegetarian">Vegetariánska</option>
    </select>
    <label>Alergény (čiarkou)</label>
    <input type="text" name="allergies" placeholder="orechy, mlieko">
    <button class="btn-primary" type="submit">Generovať plán</button>
  </form>
</div>
<?php include 'includes/footer.php'; ?>
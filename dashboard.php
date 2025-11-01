<?php
session_start();
require_once 'includes/db.php';
if(!isset($_SESSION['user_id'])) { header('Location: /login.php'); exit; }
$user_id = intval($_SESSION['user_id']);
$stmt = $mysqli->prepare("SELECT id, plan_data, created_at FROM meal_plans WHERE user_id=? ORDER BY id DESC LIMIT 1");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();
$planData = null;
if($row = $res->fetch_assoc()) $planData = json_decode($row['plan_data'], true);
include 'includes/header.php';
?>
<div class="container">
  <h1>Môj jedálniček</h1>
  <a class="btn-primary" href="/generate_plan.php">Generovať týždenný plán</a>
  <section class="card" style="margin-top:20px">
    <h2>Posledný plán</h2>
    <?php if($planData): ?>
      <?php foreach($planData as $dayIndex => $meals): ?>
        <div class="plan-day">
          <h4>Deň <?php echo $dayIndex + 1; ?></h4>
          <ul>
            <?php foreach($meals as $m): ?>
              <li><?php echo htmlspecialchars(ucfirst($m['slot']) . ' — ' . $m['name']); ?> (<?php echo $m['perMealCost'];?> €)</li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Zatiaľ nemáte žiadny uložený plán.</p>
    <?php endif; ?>
  </section>
</div>
<?php include 'includes/footer.php'; ?>
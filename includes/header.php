<?php if(session_status() === PHP_SESSION_NONE) session_start(); ?>
<!doctype html><html lang="sk"><head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>MealMind</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="navbar">
  <div class="container header-inner">
    <a class="logo" href="/index.php">MealMind</a>
    <nav class="nav">
      <a href="/index.php">Domov</a>
      <?php if(!empty($_SESSION['user_id'])): ?>
        <a href="/dashboard.php">Môj plán</a>
        <a href="/user_profile.php">Profil</a>
        <?php if(!empty($_SESSION['is_admin'])): ?><a href="/admin/index.php">Admin</a><?php endif; ?>
        <a href="/logout.php">Odhlásiť sa</a>
      <?php else: ?>
        <a href="/login.php">Prihlásiť sa</a>
        <a href="/register.php" class="btn">Registrácia</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main class="site-main">
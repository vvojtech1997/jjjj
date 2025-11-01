<?php
$logfile = __DIR__ . '/bot/cron_prices_log.txt';
file_put_contents($logfile, "[".date('Y-m-d H:i:s')."] cron prices start\n", FILE_APPEND);
require_once __DIR__ . '/bot/price_scraper.php';
file_put_contents($logfile, "[".date('Y-m-d H:i:s')."] cron prices done\n", FILE_APPEND);
?>
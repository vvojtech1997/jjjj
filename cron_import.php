<?php
$logfile = __DIR__ . '/bot/cron_import_log.txt';
file_put_contents($logfile, "[".date('Y-m-d H:i:s')."] cron import start\n", FILE_APPEND);
require_once __DIR__ . '/bot/rss_import.php';
file_put_contents($logfile, "[".date('Y-m-d H:i:s')."] cron import done\n", FILE_APPEND);
?>
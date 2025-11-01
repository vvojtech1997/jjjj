<?php
function safe_post($k){ return isset($_POST[$k]) ? trim($_POST[$k]) : null; }
function json_or_empty($s){ $d = json_decode($s,true); return $d ?: []; }
?>
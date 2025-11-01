<?php
require_once __DIR__ . '/../includes/db.php';
$shops = [];
$res = $mysqli->query("SELECT * FROM shops WHERE active=1");
if($res && $res->num_rows>0){
  while($r = $res->fetch_assoc()) $shops[] = $r;
} else {
  $shops = [
    ['name'=>'Tesco','rss_url'=>'https://www.tesco.sk/groceries/sk-SK/All-Products.atom'],
  ];
}
foreach($shops as $shop){
  $rss = $shop['rss_url'] ?? '';
  if(!$rss) continue;
  $raw = @file_get_contents($rss);
  if(!$raw) continue;
  $xml = @simplexml_load_string($raw, 'SimpleXMLElement', LIBXML_NOCDATA);
  if(!$xml) continue;
  foreach($xml->channel->item as $item){
    $title = trim((string)$item->title);
    $link = trim((string)$item->link);
    $desc = trim((string)$item->description);
    $price = null; $unit = 'kg'; $unit_amount = 1.0;
    if(isset($item->price)) $price = floatval($item->price);
    if(!$price){
      if(preg_match('/([\d\.,]+)\s*€/', $desc, $m)) $price = floatval(str_replace(',','.',$m[1]));
      elseif(preg_match('/Cena[:\s]*([\d\.,]+)\s*/u', $desc, $m)) $price = floatval(str_replace(',','.',$m[1]));
    }
    if(preg_match('/([\d\.,]+)\s*(kg|g|l|ml|ks|piece|ks)/i', $desc, $m)){
      $num = floatval(str_replace(',','.',$m[1])); $u = strtolower($m[2]);
      if($u === 'kg'){ $unit='kg'; $unit_amount=$num; }
      elseif($u === 'g'){ $unit='g'; $unit_amount=$num; }
      elseif($u === 'l'){ $unit='l'; $unit_amount=$num; }
      else { $unit = $u; $unit_amount = $num; }
    }
    if($price !== null){
      $stmt = $mysqli->prepare("INSERT INTO prices (shop,item_name,price,unit,unit_amount,source_link,created_at) VALUES (?,?,?,?,?,?,NOW())");
      $shopName = $shop['name'] ?? 'shop';
      $stmt->bind_param('ssdsss', $shopName, $title, $price, $unit, $unit_amount, $link);
      $stmt->execute();
    }
  }
}
?>
<?php
require_once __DIR__ . '/../includes/db.php';
$rss_list = [
  'https://varecha.pravda.sk/rss/',
  'https://www.recepty.sk/rss'
];
foreach($rss_list as $rss_url){
    $raw = @file_get_contents($rss_url);
    if(!$raw) continue;
    $xml = @simplexml_load_string($raw, 'SimpleXMLElement', LIBXML_NOCDATA);
    if(!$xml) continue;
    $count = 0;
    foreach($xml->channel->item as $item){
        if($count++ > 300) break;
        $title = $mysqli->real_escape_string(trim((string)$item->title));
        $link = $mysqli->real_escape_string(trim((string)$item->link));
        $desc  = $mysqli->real_escape_string(trim((string)$item->description));
        $res = $mysqli->query("SELECT id FROM recipes WHERE source_link = '$link' LIMIT 1");
        if($res && $res->num_rows > 0) continue;
        $text = strip_tags((string)$item->description);
        $lines = preg_split('/\r\n|\r|\n/', $text);
        $ingredients = [];
        foreach($lines as $ln){
            $ln = trim($ln);
            if($ln === '') continue;
            if(preg_match('/\d+(\.|,)?\d*\s*(g|kg|ml|l|ks|pieces)?/i',$ln)){
                if(preg_match('/([\d,\.]+)\s*(g|kg|ml|l|ks)?\s*(.*)/i',$ln,$m)){
                    $qty = floatval(str_replace(',','.',$m[1]));
                    $unit = strtolower($m[2] ?? '');
                    $name = trim($m[3] ?? $ln);
                    $qty_g = null;
                    if($unit === 'kg') $qty_g = $qty*1000;
                    elseif($unit === 'g') $qty_g = $qty;
                    $ingredients[] = ['name'=>$name, 'qty_g'=>$qty_g];
                } else {
                    $ingredients[] = ['name'=>$ln];
                }
            }
        }
        $ing_json = $mysqli->real_escape_string(json_encode($ingredients, JSON_UNESCAPED_UNICODE));
        $time = 15; $est = 2.50; $serv = 2; $mealType = 'lunch'; $img = null;
        $source = parse_url($link, PHP_URL_HOST) ?: 'rss';
        $mysqli->query("INSERT INTO recipes (name,description,time_min,servings,mealType,ingredients,estimatedCost,image,source,source_link,created_at) VALUES ('$title','$desc',$time,$serv,'$mealType','$ing_json',$est,'$img','$source','$link',NOW())");
    }
}
?>
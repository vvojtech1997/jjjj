<?php
// parser_varecha.php - imports recipes from Varecha RSS
function import_varecha($mysqli, $limit = 100){
    $rss = 'https://varecha.pravda.sk/rss/';
    $raw = @file_get_contents($rss);
    if(!$raw) return 0;
    $xml = @simplexml_load_string($raw,'SimpleXMLElement',LIBXML_NOCDATA);
    if(!$xml) return 0;
    $count = 0;
    foreach($xml->channel->item as $item){
        if($count >= $limit) break;
        $title = $mysqli->real_escape_string((string)$item->title);
        $link = $mysqli->real_escape_string((string)$item->link);
        $desc  = $mysqli->real_escape_string((string)$item->description);
        $res = $mysqli->query("SELECT id FROM recipes WHERE source_link='$link' LIMIT 1");
        if($res && $res->num_rows>0) continue;
        // naive ingredient extraction
        $text = strip_tags((string)$item->description);
        $lines = preg_split('/\r\n|\r|\n/', $text);
        $ings = [];
        foreach($lines as $ln){
            $ln = trim($ln);
            if($ln== '') continue;
            if(preg_match('/\d+(\.|,)?\d*\s*(g|kg|ml|l|ks|pieces)?/i',$ln)){
                if(preg_match('/([\d,\.]+)\s*(g|kg|ml|l|ks)?\s*(.*)/i',$ln,$m)){
                    $qty = floatval(str_replace(',','.',$m[1]));
                    $unit = strtolower($m[2] ?? '');
                    $name = trim($m[3] ?? $ln);
                    $qty_g = null;
                    if($unit === 'kg') $qty_g = $qty*1000;
                    elseif($unit === 'g') $qty_g = $qty;
                    $ings[] = ['name'=>$name,'qty_g'=>$qty_g];
                } else {
                    $ings[] = ['name'=>$ln];
                }
            }
        }
        $ings_json = $mysqli->real_escape_string(json_encode($ings, JSON_UNESCAPED_UNICODE));
        $time = 20; $serv = 2; $mealType = 'lunch'; $est = 2.50; $img = '';
        $source = parse_url($link, PHP_URL_HOST) ?: 'varecha';
        $mysqli->query("INSERT INTO recipes (name,description,time_min,servings,mealType,ingredients,estimatedCost,image,source,source_link,created_at) VALUES ('$title','$desc',$time,$serv,'$mealType','$ings_json',$est,'$img','$source','$link',NOW())");
        $count++;
    }
    return $count;
}
?>
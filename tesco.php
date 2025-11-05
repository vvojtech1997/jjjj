<?php
require_once __DIR__ . '/db.php';

function searchTescoPrice($query) {
    $query = urlencode($query);
    $url = "https://www.tesco.com/groceries/en-GB/search?query={$query}";

    $html = @file_get_contents($url);
    if (!$html) return 0;

    // Nájde prvú cenu v £ alebo €
    if (preg_match('/€\s*([\d\.,]+)/', $html, $m)) {
        return floatval(str_replace(',', '.', $m[1]));
    }
    if (preg_match('/£\s*([\d\.,]+)/', $html, $m)) {
        return floatval(str_replace(',', '.', $m[1])) * 1.15; // prepočet na €
    }
    return 0;
}

echo "🧠 Aktualizujem ceny podľa Tesco...\n";

$db = getDB();
$stmt = $db->query("SELECT id, ingredients FROM recipes WHERE (price IS NULL OR price = 0)");
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($recipes as $r) {
    $ingredients = explode(',', $r['ingredients']);
    $total = 0;
    $count = 0;

    foreach ($ingredients as $ing) {
        $ing = trim($ing);
        if (strlen($ing) < 3) continue;
        $price = searchTescoPrice($ing);
        if ($price > 0) {
            $total += $price;
            $count++;
        }
        sleep(1); // pauza, aby Tesco neblokovalo
    }

    $avg = ($count > 0) ? ($total / $count) : 0;
    $db->prepare("UPDATE recipes SET price = ? WHERE id = ?")
       ->execute([$avg, $r['id']]);

    echo "💰 Recipe #{$r['id']} updated with price €{$avg}\n";
}

echo "✅ Hotovo.\n";
?>

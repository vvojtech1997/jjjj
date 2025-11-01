<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/parser_tesco.php';
session_start();
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['user_id'])) { echo json_encode(['error'=>'not_logged']); exit; }
$people = max(1, intval($_POST['people'] ?? 2));
$budget = isset($_POST['budget']) && $_POST['budget'] !== '' ? floatval($_POST['budget']) : null;
$allergies = array_filter(array_map('trim', explode(',', strtolower($_POST['allergies'] ?? ''))));
$slots = ['breakfast','lunch','dinner']; $days = 7;
$res = $mysqli->query("SELECT * FROM recipes");
$recipes = [];
while($r = $res->fetch_assoc()){ $r['ingredients'] = json_decode($r['ingredients'], true) ?: []; $recipes[] = $r; }
$filtered = array_filter($recipes, function($r) use ($allergies){
    $txt = strtolower($r['name'] . ' ' . implode(' ', array_column($r['ingredients'],'name')));
    foreach($allergies as $a) if($a && strpos($txt,$a) !== false) return false;
    return true;
});
usort($filtered, function($a,$b){ return ($a['estimatedCost'] ?? 0) <=> ($b['estimatedCost'] ?? 0); });
$plan = []; $usedIds = []; $totalCost = 0.0;
foreach(range(1,$days) as $d){
    $dayMeals = [];
    foreach($slots as $slot){
        $pick = null;
        foreach($filtered as $r){ if(in_array($r['id'],$usedIds)) continue; if($r['mealType']===$slot || $r['mealType']==='lunch'){ $pick = $r; break; } }
        if(!$pick){ foreach($filtered as $r){ if(!in_array($r['id'],$usedIds)){ $pick=$r; break; } } }
        if($pick){
            $baseServings = max(1,intval($pick['servings']??2));
            $perMealCost = round(($pick['estimatedCost'] ?? 0) * $people / $baseServings, 2);
            $usedIds[] = $pick['id'];
            $scaledIngredients = [];
            foreach($pick['ingredients'] as $ing){
                $name = $ing['name'] ?? '';
                $qty_g = isset($ing['qty_g']) ? floatval($ing['qty_g']) : null;
                if($qty_g !== null){
                    $scaledQty = round($qty_g * $people / $baseServings, 1);
                    $scaledIngredients[] = ['name'=>$name,'quantity_g'=>$scaledQty];
                } else $scaledIngredients[] = ['name'=>$name,'quantity'=>($ing['quantity'] ?? '')];
            }
            $dayMeals[] = ['slot'=>$slot,'id'=>$pick['id'],'name'=>$pick['name'],'perMealCost'=>$perMealCost,'ingredients'=>$scaledIngredients];
            $totalCost += $perMealCost;
        } else {
            $dayMeals[] = ['slot'=>$slot,'id'=>null,'name'=>'(Žiadna)','perMealCost'=>0,'ingredients'=>[]];
        }
    }
    $plan[] = $dayMeals;
}
$user_id = intval($_SESSION['user_id']);
$stmt = $mysqli->prepare("INSERT INTO meal_plans (user_id,plan_data,total_cost,created_at) VALUES (?,?,?,NOW())");
$json = json_encode($plan, JSON_UNESCAPED_UNICODE);
$stmt->bind_param('isd',$user_id,$json,$totalCost);
$stmt->execute();
echo json_encode(['success'=>true,'total_cost'=>$totalCost,'plan'=>$plan], JSON_UNESCAPED_UNICODE);
?>
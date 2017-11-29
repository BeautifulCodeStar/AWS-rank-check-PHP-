<?php
require_once('../controller/Product.php');
require_once('../database/DataBase.php');

$db = new DataBase();
$product = new Product();

// Product schedule
$rankSchedule = "SELECT * FROM phrases";
$asinAndKeywords = $db->result($rankSchedule);

if ($asinAndKeywords && count($asinAndKeywords) > 0) {
    foreach($asinAndKeywords as $item) {
        $product->getProduct($item['asin']);
    }
}

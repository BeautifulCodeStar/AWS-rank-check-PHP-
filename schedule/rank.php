<?php
require_once('../controller/Product.php');
require_once('../database/DataBase.php');

$db = new DataBase();
$product = new Product();

// Rank schedule
$rankSchedule = "SELECT * FROM phrases";
$asinAndKeywords = $db->result($rankSchedule);

if ($asinAndKeywords && count($asinAndKeywords) > 0) {
    foreach($asinAndKeywords as $item) {
        	if (strpos($item['phrases'], ',')) {
    		$phrases = preg_split('/,/', $item['phrases']);
	        foreach($phrases as $phrase) {
                $product->rankSearch($phrase, $item['asin']);
            }
    	} else {
    		$product->rankSearch($item['phrases'], $item['asin']);
    	}
    }
}

<?php

require_once '../controller/Product.php';
require_once '../controller/SearchQuery.php';

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: http://localhost:4200");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

$product = new Product();
$searchQuery = new SearchQuery();

// Get all of the products saved in the database(products)
if (isset($_GET['getproducts'])) { 
	return $product->fetchProducts();
}

// Search products with phrase or asin which are users entered and save product
if (isset($_GET['product'])) { 
    return $product->getProduct($_GET['product']);
}

// Update the rank with the asin and phrases
if (isset($_GET['schedule']) && $_GET['schedule'] == 'rank') {
	$phrases = $_GET['phrases'];
	$asin = $_GET['asin'];
	echo $product->rankCheck($phrases, $asin);
}

// Search Action
if (isset($_GET['phrases']) && $_GET['phrases'] == "keywords") {
	echo $searchQuery->index();
}

// Phrases Operations
if (isset($_GET['type'])) {
	// add Asin and Phrases
	if ($_GET['type'] == 'add') {
		$req['asin'] = $_GET['asin'];
		$req['phrases'] = $_GET['phrases'];
		echo $searchQuery->addAsinAndPhrases($req);	
	}
	// edit asin and phrases
	if ($_GET['type'] == 'edit') {
		$req['id'] = $_GET['id'];
		$req['asin'] = $_GET['asin'];
		$req['phrases'] = $_GET['phrases'];
		echo $searchQuery->updateAsinAndPhrases($req);	
	}
	// delete asin and phrases
	if ($_GET['type']=='delete' && isset($_GET['asin'])) {
		echo $searchQuery->deleteAsinAndPhrases($_GET['asin']);	
	}
}



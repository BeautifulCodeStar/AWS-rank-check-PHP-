<?php
require_once "./product.php";

$product = new Product();
if (isset($_GET['product'])) {
    return $product->getProductTitles($_GET['product']);
}
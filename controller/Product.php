<?php

ini_set('max_execution_time', 0);

require_once '../vendor/autoload.php';
require_once '../database/DataBase.php';

use Keepa\API\Request;
use Keepa\API\ResponseStatus;
use Keepa\helper\CSVType;
use Keepa\helper\CSVTypeWrapper;
use Keepa\helper\KeepaTime;
use Keepa\helper\ProductAnalyzer;
use Keepa\helper\ProductType;
use Keepa\KeepaAPI;
use Keepa\objects\AmazonLocale;

class Product {

	private $api;

	public $db;

	public function __construct()
	{
		$this->api = new KeepaAPI("");
		$this->db = new DataBase();
	}
	
	// Get the product information with the asin
	public function getProduct($asin)
	{
		try {
      $params = ['rating' => true];
			$r = Request::getProductRequest(AmazonLocale::US, 0, "2015-12-31", "2018-01-01", 0, true, [$asin], $params);
			$r->path = 'product';
			$response = $this->api->sendRequestWithRetry($r);
				switch ($response->status) {
					case ResponseStatus::OK:
						$rank = null;
						foreach ($response->products as $product){
							if ($product->productType == ProductType::STANDARD || $product->productType == ProductType::DOWNLOADABLE) {
								$currentAmazonPrice = ProductAnalyzer::getLast($product->csv[CSVType::AMAZON], CSVTypeWrapper::getCSVTypeFromIndex(CSVType::AMAZON));

								$weightedMean90days = ProductAnalyzer::calcWeightedMean($product->csv[CSVType::AMAZON], KeepaTime::nowMinutes(),90, CSVTypeWrapper::getCSVTypeFromIndex(CSVType::AMAZON));
							}
						}
						break;
					default:
						break;
				}
      if ($response->products && count($response->products) > 0) {
        $products = $this->getBestSellers($response->products);
        return $this->upsertProduct($products);    
      }
		} catch(\Exception $e) {
			$this->errorHandler($e);
		}
	}

	// Get the bestSellersList
	public function getBestSellers($products)
	{
		try {
			foreach ($products as $product) {
				for ($i =0 ; $i < count($product->categories); $i++) {
					$categoryIds = $product->categories;
					$r = Request::getBestSellerRequest(AmazonLocale::US, $categoryIds[$i]);
					$response = $this->api->sendRequestWithRetry($r);
					switch ($response->status) {
						case ResponseStatus::OK:
							if ($response->bestSellersList) {
								$products[0]->bestSellersList = $response->bestSellersList;
							}
							break;
						default:
							break;
					}
				}
			}
			return $products;
		} catch(\Exception $e) {
			$this->errorHandler($e);
		}
	}

	// Save product information
	public function upsertProduct($products)
	{
		try {
			if ($products && count($products) > 0) {
				foreach ($products as $product) {
					$ifExist = "SELECT * FROM products WHERE asin = '$product->asin'";
					$exist = $this->db->result($ifExist);
					$price = $product->stats->current[1]/100;
          $rating = end($product->csv[CSVType::RATING]) / 10;
          $stock = ($product->stats->outOfStockPercentageInInterval != '-1')? 'In Stock' : 'Out of Stock';
          $bsr = end($product->csv[CSVType::SALES]);
          $date = date('m/d/Y');
					if (count($exist) == 0) { // Insert
						$query = "INSERT INTO products (asin, title,  images_csv, brand, price, rating, bsr, stock, date) VALUES(";
						$query .= "'$product->asin','$product->title','$product->imagesCSV','$product->brand', '$price','$rating', '$bsr', '$stock', '$date')";
						$add = $this->db->execute($query);
					} else { // Update
            $bsr = ($exist[0]['bsr'] && $exist[0]['bsr'] !== '')? $exist[0]['bsr'].',' . $bsr : $bsr;
            $price = ($exist[0]['price'] && $exist[0]['price'] !== '')? $exist[0]['price'].',' . $price : $price;
            $date = ($exist[0]['date'] && $exist[0]['date'] !== '')? $exist[0]['date'] .','. $date : $date;
						$query = "UPDATE products SET asin='$product->asin',title='$product->title'";
						$query .= ",images_csv='$product->imagesCSV',brand='$product->brand', price='$price',rating='$rating', bsr='$bsr', stock='$stock', date='$date'";
						$query .= " WHERE asin='$product->asin'";
						$update = $this->db->execute($query);
					}
				}
			}
			return true;
		} catch(\Exception $e) {
			$this->errorHandler($e);
		}
	}

  // Loop over getting the rank of the product
	public function rankCheck($keywords, $asin)
	{
		if (strpos($keywords, ',')) {
			$phrases = preg_split('/,/', $keywords);
			foreach($phrases as $phrase) {
				$result = $this->rankSearch($phrase, $asin);
			}   
		} else {
			$result = $this->rankSearch($keywords, $asin);
		}
		return $result;
	}

	// Search the rank of the product
	public function rankSearch($phrase, $asin)
	{
		try {
			$r= Request::getProductSearchRequest(AmazonLocale::US, $phrase, null);
			$response = $this->api->sendRequestWithRetry($r);
			$rank = $this->getProductRank($response->products, $asin);
			return $this->updateRank($rank, $asin, $phrase);    
		} catch(\Exception $e) {
			$this->errorHandler($e);
		}
	}

	// Get the rank of the product
	public function getProductRank($products, $asin)
	{
		foreach ($products as $key => $product) {
			if ($product->asin == $asin) {
				$rank = $key + 1;
				return $rank;
			}
		}
		return false;
	}

	// Upsert the rank of the product
	public function updateRank($rank, $asin, $phrase)
	{
		if ($rank && $asin && $phrase) {
      $phrase = ltrim($phrase);
			$exist = "SELECT rank FROM ranks WHERE asin='$asin' AND phrase='". $phrase . "'";
			$existRank = $this->db->result($exist);
			if ($existRank && count($existRank)>0) {
				if ($existRank[0]['rank'] && $existRank[0]['rank'] !== '') {
					$rank = $existRank[0]['rank']. ',' . $rank;
				}
				$query ="UPDATE ranks SET rank='$rank' WHERE asin='$asin' AND phrase='$phrase'";    
			} else {
				$query = "INSERT INTO ranks (asin, phrase, rank) VALUES ('$asin','$phrase','$rank')";
			}
			return $this->db->execute($query);
		} else {
			return false;
		}
	}

	// Get the all of the product
	public function fetchProducts()
	{
		try {
			$sql = "SELECT asin, title, images_csv, price, bsr, rating, brand, stock, date FROM products";
			$products = $this->db->result($sql);
			if ($products && count($products)>0) {
				$count = null;
				foreach ($products as $key => $product) {
					$query = "SELECT rank, phrase FROM ranks WHERE asin='". $product['asin'] ."'";
					$result = $this->db->result($query);
					$count = $key;
					if ($result && count($result)>0) {
						$ranking = array();
						$rankSum = 0; $rankStr = '';
						foreach ($result as $key => $rank) {
							$ranks = $rank['rank']; $rankStr .= ($key==0)? $ranks : ','.$ranks;
							$ranking[$key] = ['rank' => $ranks, 'keyword' => $rank['phrase']];
							if (strpos($ranks, ',')) {
								$rankArr = preg_split('/,/', $ranks);
								foreach ($rankArr as $value) {
									$rankSum += (int)$value;
								}
							} else {
								$rankSum += (int)$ranks;
							}
						}
						$rankArr = preg_split('/,/', $rankStr);
						if (isset($rankSum)) {
							$products[$count]['rankingAvg'] = round($rankSum/count($rankArr));
							$products[$count]['topRanking'] = min($rankArr);
						}
            $products[$count]['rank'] = $rankStr;
            $products[$count]['keywordRank'] = $ranking;
						$products[$count]['numberOfKeywords'] = count($ranking);
					}
           $query = "SELECT phrases FROM phrases WHERE asin='". $product['asin'] ."'";
            $totalNumberOfKeywords = $this->db->result($query);
            $products[$count]['totalNumberOfKeywords'] = count(preg_split('/,/', $totalNumberOfKeywords[0]['phrases']));
				}
				echo json_encode($products);
			} else {
				throw new Exception('No data');
			}
		} catch(\Exception $e) {
			$this->errorHandler($e);
		}
	}

  // Get the array of the keywords
	public function extractKeywords($titleArr, $titleStr)
	{
		$resArr = array();
		if ($titleStr) {
			foreach($titleArr as $key => $title) {
				$splitTitle = preg_split('/\s+/', $title, -1, PREG_SPLIT_NO_EMPTY);
				foreach($splitTitle as $subTitle) {
					$subTitle = str_replace(array(',', '.', '/', '(', ')', '[', ']', ':', "'"),  '', $subTitle);
					preg_match_all('/' . $subTitle .'/i', $titleStr, $matchArray, PREG_PATTERN_ORDER);
					error_reporting(E_ERROR | E_PARSE);
					$resArr[$subTitle] = count($matchArray[0]);
				}
			}
			return $resArr;
		}
	}

	public function errorHandler($e) {
		if (gettype($e) == 'string') {
			echo json_encode(['error' => $e]);
		} else {
			echo json_encode(['error' => $e->getMessage()]);    
		}
	}
}
   

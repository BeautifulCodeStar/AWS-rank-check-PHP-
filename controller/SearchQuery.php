<?php
require_once '../database/DataBase.php';
require_once '../controller/Product.php';

class SearchQuery {
    
    private $db;
    private $product;

    public function __construct()
    {
        $this->db  = new DataBase();
        $this->product = new Product();
    }

    public function index()
    {
        try {
            $query = "SELECT * FROM phrases";
            $asinArr = $this->db->result($query);
            return json_encode($asinArr);
        } catch (\Exception $e) {
            return $this->errorHandler($e);
        }
    }

    public function addAsinAndPhrases($req)
    {
        try {
            $query = "INSERT INTO phrases (asin, phrases) VALUES ('" . $req['asin'] . "','" .$req['phrases'] . "')";
            $add = $this->db->execute($query);
            if ($add) {
                return $this->product->getProduct($req['asin']);
            } else {
                throw new Exception("Failed to add asin and phrase");
            }
        } catch(\Exception $e) {
            return $this->errorHandler($e);
        }
    }


    public function updateAsinAndPhrases($req)
    {
        try {
            $query = "UPDATE phrases SET asin = '" . $req['asin'] . "',phrases='" . $req['phrases'] ."' WHERE id = '".$req['id']. "'" ;
            $update = $this->db->execute($query);
            if ($update) {
                return $this->product->getProduct($req['asin']);
            } else {
                throw new Exception("Failed to update asin and phrases");
            }
        } catch(\Exception $e) {
            return $this->errorHandler($e);
        }
    }

    public function deleteAsinAndPhrases($asin)
    {
        try {
            if ($asin) {
                $delInPhrases = "DELETE FROM phrases WHERE asin = '" . $asin . "'";
                $delInProducts = "DELETE FROM products WHERE asin = '" . $asin . "'";
                $delInRanks = "DELETE FROM ranks WHERE asin = '" . $asin . "'";
                $tables = ['phrases', 'products', 'ranks'];
                foreach ($tables as $table) {
                    $query = "DELETE FROM $table WHERE asin = '$asin'";
                    $this->db->execute($query);    
                }
            }
            return "Successfully deleted the data";
        } catch(\Exception $e) {
            return $this->errorHandler($e);
        }
    }

    public function errorHandler($e)
    {
        return "Error::" . $e->getMessage();
    }
}
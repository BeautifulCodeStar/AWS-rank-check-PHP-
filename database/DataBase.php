<?php
    define("DB_HOST", "localhost");
    define("DB_USER", "root");
    define("DB_PASS", "");
    define("DB_NAME", "aws");

class DataBase {

    private $_db;

    public function __construct()
    {
        $this->_db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$this->_db) {
            die('Could not connect: ' . mysql_error());
        }
        mysqli_set_charset($this->_db, "utf8");
    }

    public function get_fields($table_name) {
        $result = $this -> result("SHOW COLUMNS FROM" . $table_name);
        $fieldnames = array();
        for ($i = 0; $i < count($result); $i++) :
            $fieldnames[] = $result[$i]["Fields"];
        endfor;
        return $fieldnames;
    }

    public function execute($sql)
    {
        if ($sql == "") :
            return false;
        else :
            return $this->_db->query($sql);
        endif;
    }

    public function single($sql)
    {
        $b = $this -> result($sql);
        return count($b) > 0 ? $b[0] : array();
    }

    public function result($sql)
    {
        $result = $this->_db->query($sql);
        if ($result === false) {
            echo 'Could not run query: ' . $this->_db->error;
            return false;
        }
        $b = array();
        while($row = $result->fetch_array(MYSQLI_ASSOC)) :
            $b[] = $row;
        endwhile;
        return $b;      
    }

    public function __destruct()
    {
        mysqli_close($this->_db);
    }
}
<?php
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "database";
    private $charset = "utf8mb4";
    private $chartset_name = "utf8mb4_unicode_ci";
    private $conn;

    public function __construct() {
        try { 
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->database;charset=$this->charset", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names $this->charset");
            $this->conn->exec("set collation_connection = $this->chartset_name");
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("SET sql_mode = ''");
           
          
            // echo "Connection successful";

           
        } catch(PDOException $e) {
           // echo "Connection failed: " . $e->getMessage();
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>
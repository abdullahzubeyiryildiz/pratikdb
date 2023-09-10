<?php  
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "";
    private $charset = "utf8mb4";
    private $chartset_name = "utf8mb4_unicode_ci";
    private $conn;

    public function __construct($dbType = "mysql") {
        try { 
            if ($dbType == "mysql") { 
                $dsn = "mysql:host=$this->host;dbname=$this->database;charset=$this->charset";
            } else if ($dbType == "postgresql") { 
                $dsn = "pgsql:host=$this->host;dbname=$this->database";
            } 
    
            $options = array(
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '$this->charset' COLLATE '$this->chartset_name'",
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            );
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            echo "Connection successful";
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>
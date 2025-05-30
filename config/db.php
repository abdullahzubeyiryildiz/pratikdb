<?php  
class Database {
    private $host = "localhost";
    private $port = "3306";  
    private $username = "root";
    private $password = "";
    private $database = "";
    private $charset = "utf8mb4";
    private $collation = "utf8mb4_unicode_ci";
    private $conn;

    public function __construct($dbType = "mysql") {
        try {
            if ($dbType == "mysql") { 
                $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->database};charset={$this->charset}";
                $options = [
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '{$this->charset}' COLLATE '{$this->collation}'",
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ];
            } else if ($dbType == "postgresql") {
                $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->database}";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ];
            } else {
                throw new Exception("Unsupported database type: $dbType");
            }

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            echo "Connection successful";
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        } catch(Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>

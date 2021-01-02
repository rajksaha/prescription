<?php
class Database{
  
    // specify your own database credentials
    private $host = "bottom-up-dev.c8lq1wttwtce.ap-southeast-1.rds.amazonaws.com:3306";
    private $db_name = "doctor_feed";
    private $username = "admin";
    private $password = "5tgbvfr4";
    public $conn;
  
    // get the database connection
    public function getConnection(){
  
        $this->conn = null;
  
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
  
        return $this->conn;
    }
}
?>
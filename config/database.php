<?php
class Database{
  
    // specify your own database credentials
    public $conn;
  
    // get the database connection
    public function getConnection(){
  
    	$host = "bottom-up-dev.c8lq1wttwtce.ap-southeast-1.rds.amazonaws.com:3306";
    	$db_name = "doctor_feed";
    	$username = "admin";
    	$password = "5tgbvfr4";
    	
        $this->conn = null;
  
        try{
        $this->conn = mysqli_connect($host,$username,$password,$db_name);
	    if (mysqli_connect_errno()){
			 echo "Failed to connect to MySQL: " . mysqli_connect_error();
			 die();
		 }
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
  
        return $this->conn;
    }
}
?>
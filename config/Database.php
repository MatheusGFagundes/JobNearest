<?php
//namespace JobNearest\config;
class Database{
 
    // specify your own database credentials
    private $host = "localhost";
    private $db_name = "simulatorjob";
    private $username = "user";
    private $password = "1234";
    public $conn;
 
    // get the database connection
    public function getConnection(){
 
        $this->conn = null;
 
        try{
            $this->conn =  new mysqli($this->host, $this->username, $this->password, $this->db_name);
            if ( $this->conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            } 
        }catch(PDOException $exception){
           // echo "Connection error: " . $exception->getMessage();
        }
 
        return $this->conn;
    }

}
?>
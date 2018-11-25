<?php
namespace objects;

class Simulator
{
 
    // database connection and table name
    public $conn;
    public $table_name = "tb_simulator";
 
    // object properties
    public $id;
    public $residents_count = 0;
    public $jobs_count = 0;
    public $distance_mean = 0;
    public $time_routes_mean = 0;

    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }


    // read products
    function readLast()
    {
 
    // select all query
        $query = "SELECT
                id
            FROM
                " . $this->table_name . " 
            ORDER BY
                id DESC Limit 1";
 
    // prepare query statement
        $stmt = $this->conn->query($query);;
 
    // execute query
         
    // obtem linha recurerada

        return $stmt->fetch_assoc()['id'];
    }
// create product
    function create()
    {
 
    // query to insert record
        $query = "INSERT INTO `tb_simulator`(`residents_count`, `jobs_count`, `distance_mean`, `time_routes_mean`) VALUES (?,?,?,?)";
 
    // prepare query
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("dddd", $this->residents_count, $this->jobs_count, $this->distance_mean, $this->time_routes_mean);
 
 
    // execute query
        if ($stmt->execute()) {
            return $this->readLast();
        }

        return false;

    }
    function getSimulator()
    {

        $results = [];
        $query = "SELECT rj.score, rj.distance, rj.route_time, j.id as j_id ,j.lat as j_lat, j.long as j_long, r.id as r_id, r.lat as r_lat, r.long as r_long  FROM `tb_residents_has_tb_jobs` rj  INNER JOIN tb_residents r on `tb_residents_id` = r.id
       INNER JOIN tb_jobs j on `tb_jobs_id` = j.id where `simulator_id`  =" . $this->id;
        $stmt = $this->conn->query($query);
        if ($stmt->num_rows > 0) {
            while ($row = $stmt->fetch_assoc()) {
                array_push($results, $row);
            }


        } else {
            //echo "0 results";
        }

        $final["statistcs"] = $this->getStatistcs();
        $final["itens"] = $results;
        return $final;

    }
    function getAllSimulatorId()
    {

        $results = [];
        $query = "SELECT id from tb_simulator order by id desc";
        $stmt = $this->conn->query($query);
        if ($stmt->num_rows > 0) {

            while ($row = $stmt->fetch_assoc()) {
                array_push($results, $row);
            }


        } else {
           // echo "0 results";
        }

        return $results;

    }
 
    function getStatistcs()
    {
        $query = "SELECT AVG(`score`) as mean_score ,AVG(`distance`) as mean_distance, AVG(`route_time`) as mean_duration, STD(`score`) as std_score, STD(`distance`) as std_distance, STD(`route_time`) as  std_duration  FROM `tb_residents_has_tb_jobs` group BY `simulator_id` HAVING `simulator_id`  =" . $this->id;;

        $stmt = $this->conn->query($query);

        return $stmt->fetch_assoc();

    }

}
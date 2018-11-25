<?php
namespace objects;

class Job
{
 
    // database connection and table name
    public $conn;
    public $table_name = "tb_jobs";
 
    // object properties
    public $id;
    public $tb_simulator_id;
    public $lat;
    public $long;
    public $education;
    public $salary_range;
 
    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    function readOne()
    {
        if ($this->id != null) {
        
            // select all query
            $query = "SELECT * FROM " . $this->table_name . " where `id` = " . $this->id;
                    
            // prepare query statement
            $stmt = $this->conn->query($query);;

            if ($stmt->num_rows > 0) {
                $row = $stmt->fetch_assoc();
                $this->lat = $row["lat"];
                $this->long = $row['long'];
            }
        }
    }
    // read products
    function read($simulator)
    {
 
    // select all query
        $query = "SELECT
                c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.created
            FROM
                " . $this->table_name . " p
                LEFT JOIN
                    categories c
                        ON p.category_id = c.id
            ORDER BY
                p.created DESC";
        $stmt = $this->conn->query($query);;
 
    // execute query
         
    // obtem linha recurerada

        return $stmt->fetch_assoc()['id'];
    // prepare query statement
        $stmt = $this->conn->prepare($query);
 
    // execute query
        $stmt->execute();

        return $stmt;
    }
// create product
    function create($inserts)
    {
 
    // query to insert record
        $query = "INSERT INTO " . $this->table_name . " (`tb_simulator_id`, `lat`, `long`, `education`, `salary_range`) VALUES " . $inserts;;
    // prepare querys
        $stmt = $this->conn->prepare($query);
 
    // execute query
        if ($stmt->execute()) {
            return true;
        }

        return "false";

    }
// update the product
    function update()
    {
 
    // update query
        $query = "UPDATE
                " . $this->table_name . "
            SET
                name = :name,
                price = :price,
                description = :description,
                category_id = :category_id
            WHERE
                id = :id";
 
    // prepare query statement
        $stmt = $this->conn->prepare($query);
 
    // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->id = htmlspecialchars(strip_tags($this->id));
 
    // bind new values
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':id', $this->id);
 
    // execute the query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }


// used for paging products

}
<?php

class Connectiontodatabase
{


    private $server = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "design";
    public $mysqli;
    public function __construct()
    {
        $this->mysqli = new mysqli($this->server, $this->username, $this->password, $this->database);
    }
}

        $connect = new Connectiontodatabase();
        $conn = $connect->mysqli;

       
?>




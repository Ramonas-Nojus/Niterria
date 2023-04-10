<?php 

class Db {
    private $host = "localhost";
    private $user = "root";
    private $password = "";
    private $dbName = "blog";

    public function connection(){
        $dsn = "mysql:host=".$this->host.";dbname=".$this->dbName;
        $pdo = new PDO($dsn, $this->user, $this->password);
        return $pdo;
    }
}
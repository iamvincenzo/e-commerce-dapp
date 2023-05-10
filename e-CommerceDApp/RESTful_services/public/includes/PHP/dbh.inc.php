<?php

    $host = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbName = "bookstorelorvincfralmadapp";

    $conn = mysqli_connect($host, $dbUsername, $dbPassword, $dbName) or die ("Impossibile connettersi al server: " . mysqli_connect_error());
    
    // class db {
        
    //     public function connect() {
    //         $host = "127.0.0.1";
    //         $user="root";
    //         $pass="";
    //         $dbname="bookstorelorvincfralmadapp";

           
    //         $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    //         $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //         $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    //         return $pdo;
    //     }
    // }

    // class db {

    //     // Properties
    //     private $dbhost = '127.0.0.1';
    //     private $dbuser = 'root';
    //     private $dbpass = '';
    //     private $dbname = 'bookstorelorvincfralmadapp';

    //     // Connect
    //     public function connect() {

    //         $mysql_connect_str = "mysql:host=$this->dbhost;dbname=$this->dbname";
            
    //         $dbConnection = new PDO($mysql_connect_str, $this->dbuser, $this->dbpass);
            
    //         $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
    //         return $dbConnection;
    //     }
    // }
<?php

namespace App\Config;
require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv;
use PDO;
use PDOException;

class Database {


    private $ServerName;
    private $UserName;
    private $PassWord;
    private $DbName;
    private static $conn;
    private static $instance = null;

    public function __construct() {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
        
        $this->ServerName = $_ENV['DB_HOST'];
        $this->UserName = $_ENV['DB_USER'];
        $this->PassWord = $_ENV['DB_PASS'];
        $this->DbName = $_ENV['DB_NAME'];


            try {

                self::$conn = new PDO("mysql:host=" . $this->ServerName . ";dbname=" . $this->DbName,$this->UserName, $this->PassWord);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                if(self::$conn){
                    // echo "hell ya";
                }
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }

        return self::$conn;
    }

    public static function getInstanse(){

        if(self::$instance == null){
            self::$instance = new self();
        }
        return self::$instance; 
    }

    public static function getConnection() {
        return self::$conn;
    }

}

// $new = new Database();
// var_dump($new);
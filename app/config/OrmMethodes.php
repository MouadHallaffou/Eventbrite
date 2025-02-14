<?php

namespace App\config;
require_once __DIR__ . '/../../vendor/autoload.php';

use App\config\Database;
use PDO;

class OrmMethodes{

// ---------------------- getData -----------------

public static function getData($table,$condetion = ''){

    $conn = Database::getInstanse()->getConnection();

    $query = "SELECT * FROM $table $condetion";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll();
    return $users;
   }

// ---------------------- countItems -----------------

   public static function countItems($table){

       $conn = Database::getInstanse()->getConnection();

       $query = "SELECT COUNT(*) FROM $table";
       $stmt = $conn->prepare($query);
       $stmt->execute();
       $users = $stmt->fetch();
       return $users[0];
   }

// ---------------------- Add -----------------

   public static function Add($table,$colm,$val){

       $conn = Database::getInstanse()->getConnection();
        
       $query = "INSERT INTO $table ($colm) VALUES (:value)";
       $stmt = $conn->prepare($query);
       $stmt->bindParam(':value',$val);
       return $stmt->execute();
   }

// ---------------------- DeleteItem -----------------

   public static function DeleteItem($table,$id){

       $conn = Database::getInstanse()->getConnection();

       $query = "DELETE FROM $table where id = :id";
       $stmt = $conn->prepare($query);
       $stmt->bindParam(':id',$id);

      return $stmt->execute();

   }

   // ---------------------- findById -----------------

   public static function findById($table,$id){

       $conn = Database::getInstanse()->getConnection();

       $query = "SELECT * FROM $table WHERE id = :id";
       $stmt=$conn->prepare($query);
       $stmt->bindParam(':id', $id, PDO::PARAM_INT);
       $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
       return $res;
   }

   // ---------------------- update -----------------

//    public static function update($table,$columns,$id){
       
//        $conn = Database::getInstanse()->getConnection();

//        $query="UPDATE $table SET $columns where id=:id";
//        $stmt=$conn->prepare($query);
//        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
//        return $stmt->execute();

//    }

   // |---------------------- AddUser -----------------|

   public static function AddUser($table, $columns, $values)
   {
     $conn = Database::getInstanse()->getConnection();
   
     $columnsArray = explode(',', $columns);
     $placeholders = implode(', ', array_fill(0, count($columnsArray), '?'));

     $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
     $stmt = $conn->prepare($query);

     return $stmt->execute($values);
   }

   // |---------------------- findByEmail -----------------|

   public static function findByEmail($email){

     $conn = Database::getInstanse()->getConnection();

     $query = "SELECT * FROM users WHERE email = :email";
     $stmt=$conn->prepare($query);
     $stmt->bindParam(':email', $email, PDO::PARAM_STR);
     $stmt->execute();
     return $stmt->fetch(PDO::FETCH_ASSOC);
   }

       
   public static function Update($table, $values, $whereClause, $whereParams) {
    $conn = Database::getInstanse()->getConnection();

    $setClause = implode('=?, ', array_keys($values)) . '=?';
    $query = "UPDATE $table SET $setClause WHERE $whereClause";

    $stmt = $conn->prepare($query);
    return $stmt->execute(array_merge(array_values($values), $whereParams));
}


   
     





}
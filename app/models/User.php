<?php

namespace App\Models;

use App\config\Database;
use PDO;


class User{

protected $username;
protected $email;
protected $password;
protected $bio;
protected $profile_picture;
protected $role;


// public function __construct($username,$email,$password,$bio,$profile_picture,$role){

// $this->username = $username;
// $this->email = $email;
// $this->password = $password;
// $this->bio = $bio;
// $this->profile_picture = $profile_picture;
// $this->role = $role;

// }

public function setUsername($username){

    $this->username = $username;
}
public function setEmail($email){

    $this->email = $email;
}
public function setPassword($password){

    $this->password = $password;
}
public function setBio($bio){

    $this->bio = $bio;
}
public function setRole($role){

    $this->role = $role;
}

public function getUsername(){

    return $this->username;
}
public function getEmail(){

    return $this->email;
}
public function getbio(){

    return $this->bio;
}
public function getPassword(){

    return $this->password;
}
public function getRole(){

    return $this->role;
}

// |---------------------- AddUser -----------------|

public static function AddUser($columns, $values)
{
  $conn = Database::getInstanse()->getConnection();

  $table = 'users';

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


}
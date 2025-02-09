<?php

namespace App\Models;

use App\config\Database;
use PDO;


abstract class User{

protected $username;
protected $email;
protected $password;
protected $avatar;
protected $gender;
protected $role;


// public function __construct($username,$email,$password,$bio,$profile_picture,$role Role){

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

public static function AddUser($columns, $values ,$roleId)
{
  $conn = Database::getInstanse()->getConnection();

  $table = 'users';

  $columnsArray = explode(',', $columns);
  $placeholders = implode(', ', array_fill(0, count($columnsArray), '?'));

  $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
  $stmt = $conn->prepare($query);

  $result =  $stmt->execute($values);

  if($result){
    $lastid = $conn->lastInsertId();

    $role = self::AddRoleUser($lastid,$roleId);
    return true;

  }

}
public static function AddRoleUser($lastid, $roleId){
    $conn = Database::getInstanse()->getConnection();
    try {
            if (!empty($roleId)) {
                $sql = "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$lastid , $roleId]);
            } else {
                echo "Error: Tag not found - $roleId";
            }
    } catch (\PDOException $e) {
        echo $e->getMessage();
    }
}
// |---------------------- findByEmail -----------------|

public static function findByEmail($email){

  $conn = Database::getInstanse()->getConnection();

  $query ="SELECT u.user_id AS userId, u.username AS userName, u.avatar AS avatar, u.email AS userEmail , u.password , r.role_id AS roleId ,r.name_role AS UserRole
      FROM users u INNER JOIN user_roles ur ON ur.user_id = u.user_id INNER JOIN roles r on r.role_id = ur.role_id  WHERE email = :email";
  $stmt=$conn->prepare($query);
  $stmt->bindParam(':email', $email, PDO::PARAM_STR);
  $stmt->execute();
  return $stmt->fetch(PDO::FETCH_ASSOC);
}


}

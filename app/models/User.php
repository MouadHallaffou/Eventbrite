<?php

namespace App\Models;

use App\config\Database;
use App\config\OrmMethodes;

use PDO;


abstract class User{

protected $username;
protected $email;
protected $password;
protected $avatar;
protected $gender;
protected $status;
protected $table = 'users';


public function setUsername($username){

    $this->username = $username;
}
public function setEmail($email){

    $this->email = $email;
}
public function setPassword($password){

    $this->password = $password;
}
public function setGender($gender){

    $this->gender = $gender;
}
public function setStatus($status){

    $this->status = $status;
}
public function getUsername(){

    return $this->username;
}
public function getEmail(){

    return $this->email;
}
public function getGender(){

    return $this->gender;
}
public function getPassword(){

    return $this->password;
}
public function getAvatar(){

    return $this->avatar;
}
public function getStatus(){

    return $this->status;
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

  $query ="SELECT u.user_id AS userId, u.username AS userName, u.avatar AS avatar, u.email AS userEmail , u.password ,u.status, r.role_id AS roleId ,r.name_role AS UserRole
      FROM users u INNER JOIN user_roles ur ON ur.user_id = u.user_id INNER JOIN roles r on r.role_id = ur.role_id  WHERE email = :email";
  $stmt=$conn->prepare($query);
  $stmt->bindParam(':email', $email, PDO::PARAM_STR);
  $stmt->execute();
  return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function getUsersData(){

    $conn = Database::getInstanse()->getConnection();

    $query = "SELECT u.*,r.role_id,r.name_role as role FROM users u JOIN user_roles ur ON u.user_id = ur.user_id JOIN
     roles r ON  r.role_id = ur.role_id WHERE r.name_role = 'Organizer' ";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll();
    return $users;
}

public function deleteUsers($id) {
    $conn = Database::getInstanse()->getConnection();
    try {
        // Delete the user from the database
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $result = $stmt->execute();

        if ($result) {
            return true; // Deletion successful
        } else {
            error_log('Failed to delete user with ID: ' . $id);
            return false; // Deletion failed
        }
    } catch (\PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        return false; // Deletion failed due to an exception
    }
}

public function updateUserStatus($userId, $status) {
    $conn = Database::getInstanse()->getConnection();
    try {
        $stmt = $conn->prepare("UPDATE users SET status = :status WHERE user_id = :userId");
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $result = $stmt->execute();

        if ($result) {
            return true; // Update successful
        } else {
            error_log('Failed to update user status with ID: ' . $userId);
            return false; // Update failed
        }
    } catch (\PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        return false; // Update failed due to an exception
    }
}

public function updateEventStatus($eventId, $status) {
    $conn = Database::getInstanse()->getConnection();
    try {
        $stmt = $conn->prepare("UPDATE events SET status = :status WHERE event_id = :eventId");
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        return $stmt->execute(); // Returns true on success, false on failure
    } catch (\PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        return false;
    }
}

public function countUsers(){

   $result =  OrmMethodes::countItems($this->table);
   return $result;

}

}

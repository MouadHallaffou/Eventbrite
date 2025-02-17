<?php

namespace App\Models;

use App\config\Database;
use App\config\OrmMethodes;

use PDO;


class Role{

protected $id;
protected $name;
protected $table = 'roles';


public function setId($id){

    $this->id = $id;
}

public function setName($name){

    $this->name = $name;
}

public function getId(){

    return $this->Id;
}
public function getName(){

    return $this->name;
}

public function AddRole($roleName) {
    $conn = Database::getInstanse()->getConnection();
    try {
        $query = "INSERT INTO $this->table (name_role) VALUES (:name_role)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':name_role', $roleName, \PDO::PARAM_STR);
        return $stmt->execute();
    } catch (\PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        return false;
    }
}

public function removeRole($roleId) {
    $conn = Database::getInstanse()->getConnection();
    try {
        $query = "DELETE FROM $this->table WHERE role_id = :role_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':role_id', $roleId, \PDO::PARAM_INT);
        return $stmt->execute();
    } catch (\PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        return false;
    }
}

public function getRolesData(){

    $conn = Database::getInstanse()->getConnection();

    $query = "SELECT * FROM  roles";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll();
    return $users;
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


public function updateRole($roleId, $roleName) {
    $conn = Database::getInstanse()->getConnection();
    try {
        error_log('Attempting to update role: ' . $roleId . ', ' . $roleName );

            $query = "UPDATE $this->table SET name_role = :name_role WHERE role_id = :role_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':name_role', $roleName, \PDO::PARAM_STR);
            $stmt->bindParam(':role_id', $roleId, \PDO::PARAM_INT);

        $result = $stmt->execute();
        if ($result) {
            error_log('role updated successfully in database'); 
            return true;
        } else {
            error_log('Failed to update role in database');
            return false;
        }
    } catch (\PDOException $e) {
        error_log('Database error: ' . $e->getMessage()); 
        return false;
    }
}



public function findRoleById($id) {
    $findRoleById = OrmMethodes::findByRoleId($this->table, $id);
    return $findRoleById;
}

}

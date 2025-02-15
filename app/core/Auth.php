<?php
namespace App\core;

use App\models\User;
use App\config\OrmMethodes;
use App\core\Session;

use PDOException;

class Auth extends User {

    

    public function loginUser($email, $password)
    {
        Session::checkSession();
        try {            
            $row = User::findByEmail($email);

            if ($row) {

                $_SESSION["role"] = $row['roleId'];
                $_SESSION["userId"] = $row['userId'];
                $_SESSION["username"] = $row['userName'];
                $_SESSION["email"] = $row['userEmail'];


                if (password_verify($password, $row['password'])) {
                    if ($_SESSION["role"] == 2) {
                        header("Location: /addEvent");
                        exit();
                    } else if ($_SESSION["role"] == 1) {
                        header("Location: /dashboard");
                        exit();
                    }
                    else if ($_SESSION["role"] == 3) {
                        header("Location: /home");
                        exit();
                    }
                } else {
                    $_SESSION["error"] = "Incorrect password";
                    header("Location: /login");
                    exit();
                }
            } else {
                $_SESSION["error"] = "Email does not exist";
                header("Location: /login");
                exit();
            }
        } catch (\PDOException $e) {
            $_SESSION["error"] = "Something went wrong: " . $e->getMessage();
            header("Location: login");
            exit();
        }
    }


    public function registerUser($userName, $email, $password, $gender ,$roleId )
     {
     $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

     $columns = "username, email, password, gender";
     $values = [$userName, $email, $hashedPassword, $gender];

     $result= User::AddUser($columns, $values , $roleId);
     if($result){
        // return $result;
        header("Location: /login");
     }else {
        $_SESSION["error"] = "Email does not exist";
        exit();
    }
     
   }

   public function logout(){
        return session::checkSession();
        header('Location: /login');
        exit;
   }


}
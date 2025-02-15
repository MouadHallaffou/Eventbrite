<?php

namespace App\core;

use App\models\User;
use App\config\OrmMethodes;
use App\core\Session;


use PDOException;

class Auth extends User
{




    public function loginUser($email, $password)
    {
        Session::checkSession();

        try {
            // $userModel = new User();
            $row = $this->findByEmail($email);
            
            if ($row && password_verify($password, $row['password'])) {
                $_SESSION["role"] = $row['roleId'] ?? null;
                $_SESSION["userId"] = $row['userId'] ?? null;
                $_SESSION["username"] = $row['userName'] ?? null;
                $_SESSION["email"] = $row['userEmail'] ?? null;
                
                switch ($_SESSION["role"]) {
                    case 1:
                        header("Location: /dashboard");
                        break;
                        case 2:
                            header("Location: /addEvent");
                            break;
                        case 3:
                            header("Location: /profile");
                        break;
                    default:
                        $_SESSION["error"] = "Invalid role!";
                        header("Location: /login");
                }
                exit();
            } else {
                $_SESSION["error"] = "Invalid email or password.";
                header("Location: /login");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION["error"] = "Something went wrong: " . $e->getMessage();
            header("Location: /login");
            exit();
        }
    }



    public function registerUser($userName, $email, $password, $gender, $roleId)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $columns = "username, email, password, gender";
        $values = [$userName, $email, $hashedPassword, $gender];

        $result = User::AddUser($columns, $values, $roleId);

        if ($result) {
            header("Location: /login");
            exit();
        } else {
            $_SESSION["error"] = "Registration failed!";
            header("Location: /register");
            exit();
        }
    }
}

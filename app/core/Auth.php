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
            $row = User::findByEmail($email);

            if ($row) {
                if (!password_verify($password, $row['password'])) {
                    $_SESSION["error"] = "Incorrect password";
                    header("Location: /login");
                    exit();
                }

                $_SESSION["role"] = $row['roleId'] ?? null;
                $_SESSION["userId"] = $row['user_id'] ?? null;
                $_SESSION["username"] = $row['userName'] ?? null;
                $_SESSION["email"] = $row['userEmail'] ?? null;

                if ($_SESSION["userId"] === null) {
                    $_SESSION["error"] = "Error: User ID is missing.";
                    header("Location: /login");
                    exit();
                }

                // Redirect based on role
                if ($_SESSION["role"] == 2) {
                    header("Location: /home");
                } else if ($_SESSION["role"] == 1) {
                    header("Location: /dashboard");
                } else {
                    header("Location: /home");
                }
                exit();
            } else {
                $_SESSION["error"] = "Email does not exist";
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
            // return $result;
            header("Location: /login");
        } else {
            $_SESSION["error"] = "Email does not exist";
            exit();
        }
    }
}

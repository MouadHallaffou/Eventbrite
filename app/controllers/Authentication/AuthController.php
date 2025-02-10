<?php

namespace App\controllers\Authentication;

use App\core\view;
use App\core\Auth;

class AuthController extends Auth {


    public function loginView() {
        View::render('Authentication/login.twig');
    }
    public function registerView() {
        View::render('Authentication/register.twig');
    }

    public function register(){

        if (isset($_POST['register']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $userName = $_POST['username'];
            $gender = $_POST['gender'];
            $password = $_POST['password'];
            $avatar = $_POST['avatar'];
            $role = $_POST['roleId'];
            $result = $this->registerUser($userName, $email, $password, $avatar ,$gender ,$role);
            return $result;
            if ($result) {
                View::render('Authentication/login.twig');
            } else {
            echo "Error registering user.";
            }
        }
    }

    public function login(){

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["login"])) {

            $email = $_POST['email'];
            $password = $_POST['password'];
            $result = $this->loginUser($email, $password);
            if(!$result){
                echo "the user not found";
            }
        }

    }
}
<?php

namespace App\controllers\Authentication;

use App\core\view;
use App\core\Auth;



class AuthController extends Auth {


    public function loginView() {
        View::render('Auth/Login.twig');
    }
    public function registerView() {
        View::render('Auth/register.twig');
    }

    public function register(){

        if (isset($_POST['register']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $userName = $_POST['username'];
            $bio = $_POST['bio'];
            $password = $_POST['password'];
            // $role = $_POST['role'];
            
            $result = $this->registerUser($userName, $email, $password, $bio);
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
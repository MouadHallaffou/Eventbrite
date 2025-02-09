<?php
namespace App\core;

use App\models\User;
use App\config\OrmMethodes;



class Auth extends User {

    // public function loginUse($email, $password){

    //     $row = User::findByEmail($email);
    //     if($row){
    
    //         $_SESSION["role"] = $row['role'];
    //         $_SESSION["id"] = $row['id'];
    //         $_SESSION["username"] = $row['username'];
    //         $_SESSION["email"] = $row['email'];
            
    //         if(password_verify($password,$row['password_hash'])){
    
    //                 if($_SESSION['role'] == 'admin'){
                        // View::render('back/home.twig');
    //                     header("Location: /dashbaord");


    //                     exit;
    //                 }
    //                 elseif($_SESSION['role'] == 'user'){
    //                     header("Location: /");
    //                     // View::render('front/home.twig');
    //                     exit;        
    //                 }
    //                 elseif($_SESSION['role'] == 'author'){
    //                     header("Location: ../../views/front");
    //                     exit;        
    //                 }else{
    //                     header("Location: signUp.php");
    //                     exit;
    //                 }
    //         }else{
    //             die("Incorrect password.");
    //         }
    //     }else{
    //         die("Incorrect email or password.");
    //     }
    // }

    public function loginUser($email, $password)
    {
        try {
            // $email = $User->getEmail();
            
            $row = User::findByEmail($email);

            if ($row) {
                $_SESSION["role"] = $row['roleId'];
                $_SESSION["userId"] = $row['userId'];
                $_SESSION["username"] = $row['userName'];
                $_SESSION["email"] = $row['userEmail'];
                $_SESSION["avatar"] = $row['avatar'];

                if (password_verify($password, $row['password'])) {
                    if ($_SESSION["role"] == 2) {
                        // header("Location: /");
                        View::render('front/home.twig');
                        exit();
                    } else if ($_SESSION["role"] == 1) {
                        // header("Location: dashboard");
                        View::render('front/home.twig');
                        exit();
                    }
                    else if ($_SESSION["role"] == 3) {
                        // header("Location: /home.twig");
                        View::render('front/home.twig');
                        exit();
                    }
                } else {
                    $_SESSION["error"] = "Incorrect password";
                    header("Location: login");
                    exit();
                }
            } else {
                $_SESSION["error"] = "Email does not exist";
                header("Location: login");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION["error"] = "Something went wrong: " . $e->getMessage();
            header("Location: login");
            exit();
        }
    }





    public function registerUser($userName, $email, $password, $avatar, $gender ,$roleId )
     {
     $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

     $columns = "username, email, password, avatar, gender";
     $values = [$userName, $email, $hashedPassword, $avatar, $gender];

     $result= User::AddUser($columns, $values , $roleId);
     return $result; 
     
   }



}
<?php
namespace App\core;

use App\models\User;
use App\config\OrmMethodes;



class Auth extends User {

    public function loginUser($email, $password){

        $row = User::findByEmail($email);
        if($row){
    
            $_SESSION["role"] = $row['role'];
            $_SESSION["id"] = $row['id'];
            $_SESSION["username"] = $row['username'];
            $_SESSION["email"] = $row['email'];
            
            if(password_verify($password,$row['password_hash'])){
    
                    if($_SESSION['role'] == 'admin'){
                        // View::render('back/home.twig');
                        header("Location: /dashbaord");


                        exit;
                    }
                    elseif($_SESSION['role'] == 'user'){
                        header("Location: /");
                        // View::render('front/home.twig');
                        exit;        
                    }
                    elseif($_SESSION['role'] == 'author'){
                        header("Location: ../../views/front");
                        exit;        
                    }else{
                        header("Location: signUp.php");
                        exit;
                    }
            }else{
                die("Incorrect password.");
            }
        }else{
            die("Incorrect email or password.");
        }
    }

    public function registerUser($userName, $email, $password, $bio )
     {
     $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

     $columns = "username, email, password_hash, bio";
     $values = [$userName, $email, $hashedPassword, $bio];

     $result= User::AddUser($columns, $values);
     return $result; 
     
   }



}
<?php

namespace App\controllers\frontOffice;
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\core\view;
use App\core\Session;

class HomeController{


   public function index()
   {
      
       View::render('front/home.twig');
   }


     public function findevents(){
       Session::checkSession();
       $role = $_SESSION["UserRole"] ;
       $username = $_SESSION['username'] ;

      //  Debug statements
       error_log("Role: $role");
       error_log("Username: $username");

       $data = [
           'user' => [
               'role' => $role,
               'username' => $username,
           ],
       ];
        View::render('front/FindEvents.twig',$data);
     }


     public function searchEvents(){
        View::render('front/FindEvents.twig',);
     }

}







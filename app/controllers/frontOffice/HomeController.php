<?php

namespace App\controllers\frontOffice;
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\core\view;

class HomeController{
    public function index(){
        View::render('front/home.twig');
     }


     public function findevents(){
        View::render('front/FindEvents.twig');
     }


     public function searchEvents(){
        View::render('front/FindEvents.twig',);
     }

}







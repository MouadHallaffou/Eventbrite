<?php

namespace App\controllers\frontOffice;
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\core\view;

class ContactController{
    public function index(){
        View::render('front/pages/Contact.twig');

     }

    public function helpcenter(){
        View::render('front/pages/help.twig');
     }


     

}







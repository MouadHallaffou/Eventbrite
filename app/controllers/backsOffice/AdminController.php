<?php
namespace App\controllers\backsOffice;
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\core\view;


class AdminController{


    public function index(){
        View::render('back/Admin/dashboard.twig');



    }
}
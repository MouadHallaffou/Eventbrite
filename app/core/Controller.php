<?php

namespace App\core;
require_once __DIR__ . '/../../vendor/autoload.php';

class Controller {
    public static function render($view,$data=[]){
        extract($data);
        include __DIR__ ."/../views/back/$view";
    }
}                         




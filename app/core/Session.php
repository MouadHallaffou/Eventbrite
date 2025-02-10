<?php

namespace App\core;

class Session{


    public static function checkSession(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function destroy() {
        session_unset();  
        session_destroy(); 
    }

}
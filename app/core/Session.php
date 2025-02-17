<?php

namespace App\core;

class Session{


    public static function checkSession(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function destroy() {
        $_SESSION = []; 
        session_unset();  
        session_destroy(); 
    }

}
<?php

namespace App\core;

class Security{



    public static function sanitizeInput($input) {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    public static function generateCsrfToken() {
        return bin2hex(random_bytes(32));
    }

}
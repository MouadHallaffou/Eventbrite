<?php

namespace App\core;

class Validator {
    public static function validateSignup($data) {
        $errors = [];

        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            $errors[] = "Tous les champs sont obligatoires.";
        }

        if (strlen($data['name']) < 3) {
            $errors[] = "Le nom doit contenir au moins 3 caractères.";
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse e-mail n'est pas valide.";
        }

        if (strlen($data['password']) < 6) {
            $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
        }

        return $errors;
    }

    public static function validateLogin($data) {
        $errors = [];

        if (empty($data['email']) || empty($data['password'])) {
            $errors[] = "Email et mot de passe sont obligatoires.";
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse e-mail n'est pas valide.";
        }

        return $errors;
    }
}
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

    public static function validateEvent($data) {
        $errors = [];
    
        if (empty($data['title'])) {
            $errors['title'] = "Le titre est obligatoire.";
        } elseif (strlen($data['title']) < 5) {
            $errors['title'] = "Le titre doit contenir au moins 5 caractères.";
        }
    
        if (empty($data['description'])) {
            $errors['description'] = "La description est obligatoire.";
        } elseif (strlen($data['description']) < 10) {
            $errors['description'] = "La description doit contenir au moins 10 caractères.";
        }
    
        if (empty($data['eventMode'])) {
            $errors['eventMode'] = "Le mode de l'événement est obligatoire.";
        } elseif (!in_array($data['eventMode'], ['presentiel', 'enligne'])) {
            $errors['eventMode'] = "Le mode de l'événement doit être 'présentiel' ou 'en ligne'.";
        }
    
        if ($data['eventMode'] === 'presentiel' && empty($data['adresse'])) {
            $errors['adresse'] = "L'adresse est obligatoire pour un événement en présentiel.";
        }
    
        if ($data['eventMode'] === 'enligne' && empty($data['lienEvent'])) {
            $errors['lienEvent'] = "Le lien de l'événement est obligatoire pour un événement en ligne.";
        }
    
        if (empty($data['capacite'])) {
            $errors['capacite'] = "La capacité est obligatoire.";
        } elseif (!is_numeric($data['capacite']) || $data['capacite'] < 1) {
            $errors['capacite'] = "La capacité doit être un nombre supérieur à 0.";
        }
    
        if (empty($data['startEventAt'])) {
            $errors['startEventAt'] = "La date de début est obligatoire.";
        }
        if (empty($data['endEventAt'])) {
            $errors['endEventAt'] = "La date de fin est obligatoire.";
        }
        if (!empty($data['startEventAt']) && !empty($data['endEventAt'])) {
            $startDate = new \DateTime($data['startEventAt']);
            $endDate = new \DateTime($data['endEventAt']);
            if ($startDate > $endDate) {
                $errors['endEventAt'] = "La date de début doit être antérieure à la date de fin.";
            }
        }
    
        if ($data['isPaid'] === 'payant' && (empty($data['price']) || !is_numeric($data['price']) || $data['price'] < 0)) {
            $errors['price'] = "Le prix doit être un nombre positif pour un événement payant.";
        }
    
        if (empty($data['category_id'])) {
            $errors['category_id'] = "La catégorie est obligatoire.";
        }
    
        return $errors;
    }
    
}
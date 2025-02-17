<?php

use App\Models\User;
use App\core\Session;


class UserController {
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $gender = $_POST['gender'];
            $avatar = $_FILES['avatar']['name'];
            $avatarTemp = $_FILES['avatar']['tmp_name'];

            $uploadDir = 'path/to/uploads/';
            $avatarPath = $uploadDir . basename($avatar);

            if (move_uploaded_file($avatarTemp, $avatarPath)) {
                $result = User::updateUser($_SESSION['user_id'], $username, $email, $gender, $avatarPath);

                if ($result) {
                    header('Location: /profile?success=1');
                    exit;
                } else {
                    $error = "Failed to update profile.";
                }
            } else {
                $error = "Failed to upload avatar.";
            }
        }

        $user = User::findById($_SESSION['user_id']);

        require 'views/update.twig';
    }
}

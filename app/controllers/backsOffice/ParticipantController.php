<?php
namespace app\controllers;

use app\core\Controller;
use app\models\User;

class ParticipantController extends Controller {
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $avatar = $_POST['avatar'] ?? null;
            $gender = $_POST['gender'] ?? null;
    
            if (empty($username) || empty($email) || empty($password)) {
                echo json_encode(['success' => false, 'error' => 'All fields are required']);
                return;
            }
    
            $success = User::addParticipant($username, $email, $password, $avatar, $gender);
    
            if ($success) {
                echo json_encode(['success' => true, 'redirect_url' => '/participants']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to create participant']);
            }
        } else {
            $this->render('participant_create');
        }
    }
}
?>

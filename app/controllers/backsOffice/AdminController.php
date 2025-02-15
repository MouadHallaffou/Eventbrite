<?php
namespace App\controllers\backsOffice;
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\core\view;
use App\models\User;



class AdminController extends User{


    public function index(){
        $users = $this->getUsersData();
        View::render('back/Admin/dashboard.twig',['users' => $users]);
    }

    public function deleteUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
    
            $id = isset($data['userId']) ? $data['userId'] : null;
    
            if ($id) {
                $result = $this->deleteUsers($id); // Make sure deleteUsers() is implemented in the User model
    
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'User deleted successfully'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to delete user'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid user ID'
                ]);
            }
    
            exit;
        }
    }
    

    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
    
            // Log the received data for debugging
            error_log('Received data: ' . print_r($data, true));
    
            $userId = isset($data['userId']) ? $data['userId'] : null;
            $status = isset($data['status']) ? $data['status'] : null;
    
            if ($userId && $status) {
                $result = $this->updateUserStatus($userId, $status);
    
                if ($result) {
                    error_log('User status updated successfully: ' . $userId);
                    echo json_encode([
                        'success' => true,
                        'message' => 'User status updated successfully'
                    ]);
                } else {
                    error_log('Failed to update user status: ' . $userId);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to update user status'
                    ]);
                }
            } else {
                error_log('Invalid user ID or status received');
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid user ID or status'
                ]);
            }
    
            exit;
        }
    }


}
<?php
namespace App\controllers\backsOffice;
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\core\view;
use App\models\User;
use App\models\Event;
use App\config\Database;

use PDO;

class AdminController extends User{

    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstanse()->getConnection();
    }


    public function index(){
        $eventModel = new Event($this->pdo);
        $events = $eventModel->fetchAll();
        $categories = $eventModel->fetchCategories();
        $users = $this->getUsersData();
        
        View::render('back/Admin/dashboard.twig', [
            'events' => $events,
            'categories' => $categories,
            'users' => $users,
        ]);
    }


    // public function deleteUser() {
    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         $input = file_get_contents('php://input');
    //         $data = json_decode($input, true);
    
    //         $id = isset($data['eventId']) ? $data['userId'] : null;
    
    //         if ($id) {
    //             $result = $this->deleteUsers($id); // Make sure deleteUsers() is implemented in the User model
    
    //             if ($result) {
    //                 echo json_encode([
    //                     'success' => true,
    //                     'message' => 'User deleted successfully'
    //                 ]);
    //             } else {
    //                 echo json_encode([
    //                     'success' => false,
    //                     'message' => 'Failed to delete user'
    //                 ]);
    //             }
    //         } else {
    //             echo json_encode([
    //                 'success' => false,
    //                 'message' => 'Invalid user ID'
    //             ]);
    //         }
    
    //         exit;
    //     }
    // }
    

    // public function updateStatus() {
    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         $input = file_get_contents('php://input');
    //         $data = json_decode($input, true);
    
    //         // Log the received data for debugging
    //         error_log('Received data: ' . print_r($data, true));
    
    //         $userId = isset($data['userId']) ? $data['userId'] : null;
    //         $status = isset($data['status']) ? $data['status'] : null;
    
    //         if ($userId && $status) {
    //             $result = $this->updateUserStatus($userId, $status);
    
    //             if ($result) {
    //                 error_log('User status updated successfully: ' . $userId);
    //                 echo json_encode([
    //                     'success' => true,
    //                     'message' => 'User status updated successfully'
    //                 ]);
    //             } else {
    //                 error_log('Failed to update user status: ' . $userId);
    //                 echo json_encode([
    //                     'success' => false,
    //                     'message' => 'Failed to update user status'
    //                 ]);
    //             }
    //         } else {
    //             error_log('Invalid user ID or status received');
    //             echo json_encode([
    //                 'success' => false,
    //                 'message' => 'Invalid user ID or status'
    //             ]);
    //         }
    
    //         exit;
    //     }
    // }

    public function updateEventsStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Decode JSON input
            $input = json_decode(file_get_contents('php://input'), true);
    
            // Validate input
            if (!isset($input['eventId']) || !isset($input['status'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid input data'
                ]);
                exit;
            }
    
            $eventId = (int)$input['eventId'];
            $status = trim($input['status']);
    
            // Update event status
            $result = $this->updateEventStatus($eventId, $status);
    
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Event status updated successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to update event status'
                ]);
            }
            exit;
        }
    }

    public function showStatusEvents(){
     
        $eventModel = new Event($this->pdo);
        $events = $eventModel->fetchAll();
        $categories = $eventModel->fetchCategories();
        $tags = $eventModel->fetchTags();

        View::render('back/Admin/Events.twig', [
            'events' => $events,
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }


}
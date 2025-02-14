<?php
namespace App\controllers\backsOffice;
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\core\view;
use App\models\Role;



class RoleController extends Role{


    public function index(){
        $getRolesData = $this->getRolesData();
        View::render('back/Admin/role.twig',['roles' => $getRolesData]);
    }


        public function addRoles() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $input = file_get_contents('php://input');
                $data = json_decode($input, true);
    
                $roleName = $data['name_role'] ?? null;
    
                if (!$roleName) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Role name is required']);
                    exit;
                }
    
                $result = $this->addRole($roleName);
    
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Role added successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to add role']);
                }
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            }
            exit;
        }
    
        public function deleteRole() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $input = file_get_contents('php://input');
                $data = json_decode($input, true);
    
                $roleId = $data['role_id'] ?? null;
                // var_dump($roleId);
    
                if (!$roleId) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Role ID is required']);
                    exit;
                }
    
                $result = $this->removeRole($roleId);
    
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Role deleted successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to delete role']);
                }
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            }
            exit;
        }

    // public function updateStatus() {
    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         $input = file_get_contents('php://input');
    //         $data = json_decode($input, true);
    
    //         // Log the received data for debugging
    //         error_log('Received data: ' . print_r($data, true));
    
    //         $CategoryId = isset($data['CategoryId']) ? $data['CategoryId'] : null;
    //         $status = isset($data['status']) ? $data['status'] : null;
    
    //         if ($CategoryId && $status) {
    //             $result = $this->updateCategoryStatus($CategoryId, $status);
    
    //             if ($result) {
    //                 error_log('Category status updated successfully: ' . $CategoryId);
    //                 echo json_encode([
    //                     'success' => true,
    //                     'message' => 'Category status updated successfully'
    //                 ]);
    //             } else {
    //                 error_log('Failed to update Category status: ' . $CategoryId);
    //                 echo json_encode([
    //                     'success' => false,
    //                     'message' => 'Failed to update Category status'
    //                 ]);
    //             }
    //         } else {
    //             error_log('Invalid Category ID or status received');
    //             echo json_encode([
    //                 'success' => false,
    //                 'message' => 'Invalid Category ID or status'
    //             ]);
    //         }
    
    //         exit;
    //     }
    // }


}
<?php
namespace App\controllers\backsOffice;
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\core\view;
use App\core\Session;
use App\models\Role;

class RoleController extends Role{

    public function index(){
        Session::checkSession();

        if (isset($_SESSION["UserRole"]) && $_SESSION["UserRole"] == 'Admin') {

         $getRolesData = $this->getRolesData();
         View::render('back/Admin/role.twig',['roles' => $getRolesData]);
        }else{
            header("Location: /404");

        }
    }


        public function addRoles() {

            Session::checkSession();

            if (isset($_SESSION["UserRole"]) && $_SESSION["UserRole"] == 'Admin') {
    
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
            }else{
                header("Location: /404");

            }
        }
    
        public function deleteRole() {
            Session::checkSession();

            if (isset($_SESSION["UserRole"]) && $_SESSION["UserRole"] == 'Admin') {
    
             if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $input = file_get_contents('php://input');
                $data = json_decode($input, true);
    
                $roleId = $data['role_id'] ?? null;
    
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
            }else{
                header("Location: /404");

            } 
        }

        public function updateRoles() {
         Session::checkSession();

         if (isset($_SESSION["UserRole"]) && $_SESSION["UserRole"] == 'Admin') {


            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                
                $roleId = $_POST['role_id'] ?? null;
                $roleName = trim($_POST['name_role'] ?? '');
    
                if (!$roleId || !$roleName) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Missing role ID or name']);
                    exit;
                }
                $result = $this->updateRole($roleId, $roleName);
                
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'role updated successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to update role']);
                }
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            }
            exit;
         }else{
            header("Location: /404");

         }

        }
    
        public function editRole($id) {
         Session::checkSession();

         if (isset($_SESSION["UserRole"]) && $_SESSION["UserRole"] == 'Admin') {

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $role = $this->findRoleById($id);
                View::render('back/Admin/updateRole.twig', ['role' => $role]);
            }
         }else{
            header("Location: /404");

            }
        }


}
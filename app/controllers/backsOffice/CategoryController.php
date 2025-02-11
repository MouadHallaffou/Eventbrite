<?php
namespace App\controllers\backsOffice;
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\core\view;
use App\models\Category;



class CategoryController extends Category{


    public function index(){
        $Categorys = $this->showCategory();
        View::render('back/Admin/Categories.twig',['categories' => $Categorys]);
    }

    public function deleteCategories() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
    
            $id = isset($data['categoryId']) ? $data['categoryId'] : null;
            var_dump($id);
    
            if ($id) {
                $result = $this->deleteCategory($id);
    
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Category deleted successfully'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to delete Category'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid Category ID'
                ]);
            }
    
            exit;
        }
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
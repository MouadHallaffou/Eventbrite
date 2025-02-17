<?php
namespace App\controllers\backsOffice;
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\core\view;
use App\models\Category;

class CategoryController extends Category {

    public function index() {
        $Categorys = $this->showCategory();
        View::render('back/Admin/categories.twig', ['categories' => $Categorys]);
    }

    public function deleteCategories() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            $id = isset($data['categoryId']) ? $data['categoryId'] : null;
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

    public function addCategories() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['category_name']) || !isset($_FILES['category_img'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing category name or image']);
                exit;
            }
            $categoryName = trim($_POST['category_name']);
            $categoryImg = $_FILES['category_img'];

            // Validate file
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($categoryImg['type'], $allowedTypes)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid image format']);
                exit;
            }

            // Handle file upload
            $uploadDir = __DIR__ . '/../../../public/assets/images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); // Ensure the directory exists
            }

            $uniqueFileName = time() . '_' . basename($categoryImg['name']); // Unique file name
            $uploadFile = $uploadDir . $uniqueFileName;

            if (move_uploaded_file($categoryImg['tmp_name'], $uploadFile)) {
                // Save file name (not full path) in the database
                $result = $this->addCategory($categoryName, $uniqueFileName);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Category added successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to add category']);
                }
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
        exit;
    }

    public function updateCategories() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log('Received POST request for update'); // Log the request
            error_log('POST data: ' . print_r($_POST, true)); // Log POST data
            error_log('FILES data: ' . print_r($_FILES, true)); // Log FILES data

            $categoryId = $_POST['category_id'] ?? null;
            $categoryName = trim($_POST['category_name'] ?? '');
            $categoryImg = $_FILES['category_img'] ?? null;

            if (!$categoryId || !$categoryName) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing category ID or name']);
                exit;
            }

            // Handle file upload if a new image is provided
            $newImageName = null;
            if ($categoryImg && $categoryImg['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../../public/assets/images/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true); // Ensure the directory exists
                }

                $newImageName = time() . '_' . basename($categoryImg['name']);
                $uploadFile = $uploadDir . $newImageName;

                if (!move_uploaded_file($categoryImg['tmp_name'], $uploadFile)) {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
                    exit;
                }
            }

            // Update the category in the database
            $result = $this->updateCategory($categoryId, $categoryName, $newImageName);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Category updated successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to update category']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
        exit;
    }

    public function editCategories($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // $id = $_GET['id'];
            $category = $this->findCategoryById($id);
            View::render('back/Admin/updateCategory.twig', ['category' => $category]);
        }
    }
}

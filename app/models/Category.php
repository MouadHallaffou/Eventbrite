<?php

namespace App\models;

use App\config\OrmMethodes;
use App\config\Database;



class Category{


    private $table = "categories";
    private $CategoryName;


    public function showCategory(){

        $category = OrmMethodes::getData($this->table);
        return $category;
    }


    public function addCategory($category_name, $category_img) {
        $conn = Database::getInstanse()->getConnection();
        try {
            error_log('Attempting to add category: ' . $category_name . ', ' . $category_img); // Log attempt
            $query = "INSERT INTO $this->table (name, img) VALUES (:category_name, :category_img)";
            $stmt = $conn->prepare($query); // Fix typo here
            $stmt->bindParam(':category_name', $category_name, \PDO::PARAM_STR);
            $stmt->bindParam(':category_img', $category_img, \PDO::PARAM_STR);
            $result = $stmt->execute();
            if ($result) {
                error_log('Category added successfully to database'); // Log success
                return true;
            } else {
                error_log('Failed to add category to database'); // Log failure
                return false;
            }
        } catch (\PDOException $e) {
            error_log('Database error: ' . $e->getMessage()); // Log database error
            return false;
        }
    }
 
    
    public function updateCategory($categoryId, $categoryName, $categoryImg = null) {
        $conn = Database::getInstanse()->getConnection();
        try {
            error_log('Attempting to update category: ' . $categoryId . ', ' . $categoryName . ', ' . $categoryImg); // Log attempt
    
            if ($categoryImg) {
                // Update both name and image
                $query = "UPDATE $this->table SET name = :category_name, img = :category_img WHERE category_id = :category_id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':category_name', $categoryName, \PDO::PARAM_STR);
                $stmt->bindParam(':category_img', $categoryImg, \PDO::PARAM_STR);
                $stmt->bindParam(':category_id', $categoryId, \PDO::PARAM_INT);
            } else {
                // Update only the name
                $query = "UPDATE $this->table SET name = :category_name WHERE category_id = :category_id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':category_name', $categoryName, \PDO::PARAM_STR);
                $stmt->bindParam(':category_id', $categoryId, \PDO::PARAM_INT);
            }
    
            $result = $stmt->execute();
            if ($result) {
                error_log('Category updated successfully in database'); // Log success
                return true;
            } else {
                error_log('Failed to update category in database'); // Log failure
                return false;
            }
        } catch (\PDOException $e) {
            error_log('Database error: ' . $e->getMessage()); // Log database error
            return false;
        }
    }

    public function deleteCategory($id){

            $conn = Database::getInstanse()->getConnection();
     
            $query = "DELETE FROM categories where category_id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id',$id);
     
           return $stmt->execute();
     
    } 

    public function findCategoryById($id){

        $findCategoryById = OrmMethodes::findById($this->table,$id);
        return $findCategoryById;
    }

    public function countCategory(){

        $countcategory=OrmMethodes::countItems($this->table);
        return $countcategory;

    }





}
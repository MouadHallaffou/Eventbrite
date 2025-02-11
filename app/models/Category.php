<?php

namespace App\models;

use App\config\OrmMethodes;


class Category{


    private $table = "categories";
    private $column = "categorie_name";
    private $CategoryName;


    public function showCategory(){

        $category = OrmMethodes::getData($this->table);
        return $category;
    }

    public function addCategory($values){

        $category=OrmMethodes::Add($this->table,$this->column,$values);
        if($category){
            header("refresh:0");
        }    
    }

    public function deleteCategory($id){

        $deleteCategory = OrmMethodes::DeleteItem($this->table,$id);
        return $deleteCategory;
        // if($deleteCategory){
        //     header("refresh:0");
        // }
    } 

    public function findCategoryById($id){

        $findCategoryById = OrmMethodes::findById($this->table,$id);
        return $findCategoryById;
    }

    public function updateCategory($name,$id){

        $column = "categorie_name = '$name'";

        $updateCategory = OrmMethodes::update($this->table,$column,$id);
        return $updateCategory;

    }

    public function countCategory(){

        $countcategory=OrmMethodes::countItems($this->table);
        return $countcategory;

    }





}
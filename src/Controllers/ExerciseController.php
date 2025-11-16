<?php


namespace Controllers;

require_once __DIR__ . '/../Models/Exercise.php';

use Models\Exercise;

class ExerciseController
{
    private $model;
    
    public function __construct($db)
    {
        $this->model = new Exercise($db);
    }
    
    public function createExercise($name, $description, $category)
    {
        return $this->model->create($name, $description, $category);
    }
    
    public function getExercise($id)
    {
        return $this->model->read($id);
    }
    
    public function getExercises()
    {
        return $this->model->readAll();
    }
    
    /*public function updateExercise($id, $name, $email, $password)
    {
        return $this->model->update($id, $name, $email, $password);
    }*/
    
    public function deleteExercise($id)
    {
        return $this->model->delete($id);
    }
}
<?php


namespace Controllers;

require_once __DIR__ . '/../Models/Exercise.php';

use Models\Exercise;

class ExerciseController
{
    public function createExercise($name, $description, $category)
    {
        $exercise = new Exercise();
        return $exercise->create($name, $description, $category);
    }
    
    public function getExercise($id)
    {
        $exercise = new Exercise();
        return $exercise->read($id);
    }
    
    public function getExercises()
    {
        $exercise = new Exercise();
        return $exercise->readAll();
    }
    
    /*public function updateExercise($id, $name, $email, $password)
    {
        $exercise = new Exercise();
        return $exercise->update($id, $name, $email, $password);
    }*/
    
    public function deleteExercise($id)
    {
        $exercise = new Exercise();
        return $exercise->delete($id);
    }
}
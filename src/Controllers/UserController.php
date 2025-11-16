<?php


namespace Controllers;

require_once __DIR__ . '/../Models/User.php';

use Models\User;

class UserController
{
    private $model;

    public function __construct($conn)
    {
        $this->model = new User($conn);
    }
    
    public function createUser($name, $email, $password)
    {
        return $this->model->create($name, $email, $password);
    }
    
    public function getUser($id)
    {
        return $this->model->read($id);
    }
    
    public function getUsers()
    {
        return $this->model->readAll();
    }
    
    public function updateUser($id, $name, $email, $password)
    {
        return $this->model->update($id, $name, $email, $password);
    }
    
    public function deleteUser($id)
    {
        return $this->model->delete($id);
    }
}
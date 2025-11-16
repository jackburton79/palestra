<?php


namespace Controllers;

require_once __DIR__ . '/../Models/User.php';

use Models\User;

class UserController
{
    public function createUser($name, $email, $password)
    {
        $user = new User();
        return $user->create($name, $email, $password);
    }
    
    public function getUser($id)
    {
        $user = new User();
        return $user->read($id);
    }

    public function getUsers()
    {
        $user = new User();
        return $user->readAll();
    }
    
    public function updateUser($id, $name, $email, $password)
    {
        $user = new User();
        return $user->update($id, $name, $email, $password);
    }
    
    public function deleteUser($id)
    {
        $user = new User();
        return $user->delete($id);
    }
}
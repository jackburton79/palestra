<?php


namespace Controllers;

require_once __DIR__ . '/../Models/User.php';

use Slim\Http\ServerRequest as Request;
use Slim\Http\Response as Response;

use Models\User;

class UserController
{
    private $model;

    public function __construct($conn)
    {
        $this->model = new User($conn);
    }
    
    public function createUser(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $this->model->create($data['username'], $data['email'], $data['password']);
        return $response->withStatus(201)->write('User created');
    }
    
    public function getUser(Request $request, Response $response, $args)
    {
        $user = $this->model->read($args['id']);
        return $response->withJson($user);
    }
    
    public function getUsers(Request $request, Response $response)
    {
        $users = $this->model->readAll();
        return $response->withJson($users);
    }
    
    public function updateUser(Request $request, Response $response, $args)
    {
        //return $this->model->update($id, $name, $email, $password);
    }
    
    public function deleteUser(Request $request, Response $response, $args)
    {
        $this->model->delete($args['id']);
        return $response->write('User deleted');
    }
}
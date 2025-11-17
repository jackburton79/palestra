<?php


namespace Controllers;

require_once __DIR__ . '/../Models/Exercise.php';

use Slim\Http\ServerRequest as Request;
use Slim\Http\Response as Response;

use Models\Exercise;

class ExerciseController
{
    private $model;
    
    public function __construct($db)
    {
        $this->model = new Exercise($db);
    }
    
    public function createExercise(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $this->model->create($data['name'], $data['description'], $data['category']);
        return $response->withStatus(201)->write('Exercise created');
    }
    
    public function getExercise(Request $request, Response $response, $args)
    {
        $exercise = $this->model->read($args['id']);
        return $response->withJson($exercise);
    }
    
    public function getExercises(Request $request, Response $response)
    {
        $exercises = $this->model->readAll();
        return $response->withJson($exercises);
    }
    
    public function updateExercise(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $this->model->update($args['id'], $data['name'], $data['category'], $data['description']);
        return $response->write('Exercise updated');
    }
    
    public function deleteExercise(Request $request, Response $response, $args)
    {
        $this->model->delete($args['id']);
        return $response->write('Exercise deleted');
    }
}

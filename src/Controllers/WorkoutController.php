<?php


namespace Controllers;

require_once __DIR__ . '/../Models/Workout.php';

use Slim\Http\ServerRequest as Request;
use Slim\Http\Response as Response;

use Models\Workout;

class WorkoutController
{
    private $model;

    public function __construct($conn)
    {
        $this->model = new Workout($conn);
    }

    public function createWorkout(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $this->model->create($data['user_id'], $data['date'], $data['notes']);
        return $response->withStatus(201)->write('Workout created');
    }

    public function getWorkout(Request $request, Response $response, $args)
    {
        $workout = $this->model->read($args['id']);
        return $response->withJson($workout);
    }

    public function getWorkouts(Request $request, Response $response)
    {
        $workouts = $this->model->readAll();
        return $response->withJson($workouts);
    }

    public function updateWorkout(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $this->model->update($args['id'], $data['user_id'], $data['date'], $data['notes']);
        return $response->write('Workout updated');
    }

    public function deleteWorkout(Request $request, Response $response, $args)
    {
        $this->model->delete($args['id']);
        return $response->write('Workout deleted');
    }
}

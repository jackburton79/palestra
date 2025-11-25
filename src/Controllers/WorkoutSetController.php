<?php


namespace Controllers;

require_once __DIR__ . '/../Models/WorkoutSet.php';

use Slim\Http\ServerRequest as Request;
use Slim\Http\Response as Response;

use Models\WorkoutSet;

class WorkoutSetController
{
    private $model;

    public function __construct($conn)
    {
        $this->model = new WorkoutSet($conn);
    }

    public function createWorkoutSet(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $this->model->create($data['workout_id'], $data['exercise_id'], $data['set_number'],
            $data['weight'], $data['repetitions'], $data['recovery_time']);
        return $response->withStatus(201)->write('WorkoutSet created');
    }

    public function getWorkoutSet(Request $request, Response $response, $args)
    {
        $workoutset = $this->model->read($args['id']);
        return $response->withJson($workoutset);
    }

    public function getWorkoutSets(Request $request, Response $response)
    {
        $workoutsets = $this->model->readAll();
        return $response->withJson($workoutsets);
    }

    public function updateWorkoutSet(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $this->model->update($args['id'], data['workout_id'], $data['exercise_id'], $data['set_number'],
            $data['weight'], $data['repetitions'], $data['recovery_time']);
        return $response->write('WorkoutSet updated');
    }

    public function deleteWorkoutSet(Request $request, Response $response, $args)
    {
        $this->model->delete($args['id']);
        return $response->write('WorkoutSet deleted');
    }
}

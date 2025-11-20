<?php
// Simple REST API for Gym Tracker

require_once __DIR__ . '/../Controllers/ExerciseController.php';
require_once __DIR__ . '/../Controllers/UserController.php';
require_once __DIR__ . '/../Controllers/WorkoutController.php';
require_once __DIR__ . '/../Controllers/WorkoutSetController.php';

use Slim\Factory\AppFactory;
use Controllers\ExerciseController;
use Controllers\UserController;
use Controllers\WorkoutController;
use Controllers\WorkoutSetController;

require __DIR__ . '/vendor/autoload.php';


// Set container to create App with on AppFactory
$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);

$configs = include(__DIR__ . '/../Config/config.php');
$db =  new PDO("mysql:host=$configs->dbHost;dbname=$configs->dbName",
        $configs->dbUser, $configs->dbPass);

$userController = new UserController($db);
$exerciseController = new ExerciseController($db);
$workoutController = new WorkoutController($db);
$workoutSetController = new WorkoutSetController($db);
// Routes
$app->get('/users', [$userController, 'getUsers']);
$app->get('/user/{id}', [$userController, 'getUser']);
$app->post('/user', [$userController, 'createUser']);
$app->put('/user/{id}', [$userController, 'updateUser']);
$app->delete('/user/{id}', [$userController, 'deleteUser']);

$app->get('/exercises', [$exerciseController, 'getExercises']);
$app->get('/exercise/{id}', [$exerciseController, 'getExercise']);
$app->post('/exercise', [$exerciseController, 'createExercise']);
$app->put('/exercise/{id}', [$exerciseController, 'updateExercise']);
$app->delete('/exercise/{id}', [$exerciseController, 'deleteExercise']);

$app->get('/workouts', [$workoutController, 'getWorkouts']);
$app->get('/workout/{id}', [$workoutController, 'getWorkout']);
$app->post('/workout', [$workoutController, 'createWorkout']);
$app->put('/workout/{id}', [$workoutController, 'updateWorkout']);
$app->delete('/workout/{id}', [$workoutController, 'deleteWorkout']);

$app->get('/workoutsets', [$workoutSetController, 'getWorkoutSets']);
$app->get('/workoutset/{id}', [$workoutSetController, 'getWorkoutSet']);
$app->post('/workoutset', [$workoutSetController, 'createWorkoutSet']);
$app->put('/workoutset/{id}', [$workoutSetController, 'updateWorkoutSet']);
$app->delete('/workoutset/{id}', [$workoutSetController, 'deleteWorkoutSet']);

$app->run();

?>

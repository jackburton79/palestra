<?php
// Simple REST API for Gym Tracker

require_once __DIR__ . '/../Controllers/ExerciseController.php';
require_once __DIR__ . '/../Controllers/UserController.php';
require_once __DIR__ . '/../Controllers/WorkoutController.php';

use Slim\Factory\AppFactory;
use Controllers\ExerciseController;
use Controllers\UserController;
use Controllers\WorkoutController;

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
$app->run();

/*
//header('Content-Type: application/json');

require_once __DIR__ . '/Controllers/ExerciseController.php';
require_once __DIR__ . '/Controllers/UserController.php';

use Config\Database;

// Helpers
function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function get_input() {
    // Try JSON input first
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    if (is_array($data)) {
        return $data;
    }
    // Fallback to form data
    return $_POST;
}

$database = new Database();
$conn = $database->connect();

// ROUTING
$method = $_SERVER['REQUEST_METHOD'];
$path = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));
    case 'workouts':
    {
        if ($method === 'POST') {
            $data = get_input();
            $stmt = $pdo->prepare("INSERT INTO workouts (user_id, date, notes) VALUES (:user_id, :date, :notes)");
            $stmt->bindParam(":user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(":date", $data['date'], PDO::PARAM_STR);
            $stmt->bindParam(":notes", $data['notes'], PDO::PARAM_STR);
            $stmt->execute();
            respond(['id' => $pdo->lastInsertId()], 201);
        } else if ($method === 'GET') {
            if (isset($path[1])) {
                $stmt = $pdo->prepare("SELECT * FROM workouts WHERE id = :id");
                $stmt->execute([':id' => $path[1]]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                respond($result ?: ['error' => 'Workout not found'], $result ? 200 : 404);
            } else {
                $stmt = $pdo->query("SELECT * FROM workouts");
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                respond($result);
            }
        } else if (($method === 'PUT' || $method === 'PATCH') && isset($path[1])) {
            // Update workout
            $data = get_input();
            $fields = [];
            $params = [':id' => $path[1]];
            if (isset($data['user_id'])) {
                $fields[] = "user_id = :user_id";
                $params[':user_id'] = $data['user_id'];
            }
            if (isset($data['date'])) {
                $fields[] = "date = :date";
                $params[':date'] = $data['date'];
            }
            if (isset($data['notes'])) {
                $fields[] = "notes = :notes";
                $params[':notes'] = $data['notes'];
            }
            if ($fields) {
                $stmt = $pdo->prepare("UPDATE workouts SET ".implode(', ', $fields)." WHERE id = :id");
                $stmt->execute($params);
                if ($stmt->execute($params))
                    respond(['success' => true]);
                else
                     respond(['error' => 'Invalid user'], 400);
            } else {
                respond(['error' => 'No fields to update'], 400);
            }
        } else if ($method === 'DELETE' && isset($path[1])) {
            // Delete workout
            $stmt = $pdo->prepare("DELETE FROM workouts WHERE id = :id");
            $stmt->execute([':id' => $path[1]]);
            respond(['success' => true]);
        }
        break;
    }
    case 'workout_sets':
    {
        if ($method === 'POST') {
            $data = get_input();
            $stmt = $pdo->prepare("INSERT INTO workout_sets (workout_id, exercise_id, set_number, weight, repetitions) VALUES (:workout_id, :exercise_id, :set_number, :weight, :repetitions)");
            $stmt->bindParam(':workout_id', $data['workout_id'], PDO::PARAM_INT);
            $stmt->bindParam(':exercise_id', $data['exercise_id'], PDO::PARAM_INT);
            $stmt->bindParam(':set_number', $data['set_number'], PDO::PARAM_INT);
            $stmt->bindParam(':weight', $data['weight'], PDO::PARAM_INT);
            $stmt->bindParam(':repetitions', $data['repetitions'], PDO::PARAM_INT);
            $stmt->execute();
            respond(['id' => $pdo->lastInsertId()], 201);
        } else if ($method === 'GET') {
            if (isset($path[1])) {
                $stmt = $pdo->prepare("SELECT * FROM workout_sets WHERE workout_id = :workout_id");
                $stmt->execute([':workout_id' => $path[1]]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                respond($result);
            } else {
                $stmt = $pdo->query("SELECT * FROM workout_sets");
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                respond($result);
            }
        } else if (($method === 'PUT' || $method === 'PATCH') && isset($path[1])) {
            // Update set by id
            $data = get_input();
            $fields = [];
            $params = [':id' => $path[1]];
            if (isset($data['workout_id'])) {
                $fields[] = "workout_id = :workout_id";
                $params[':workout_id'] = $data['workout_id'];
            }
            if (isset($data['exercise_id'])) {
                $fields[] = "exercise_id = :exercise_id";
                $params[':exercise_id'] = $data['exercise_id'];
            }
            if (isset($data['set_number'])) {
                $fields[] = "set_number = :set_number";
                $params[':set_number'] = $data['set_number'];
            }
            if (isset($data['weight'])) {
                $fields[] = "weight = :weight";
                $params[':weight'] = $data['weight'];
            }
            if (isset($data['repetitions'])) {
                $fields[] = "repetitions = :repetitions";
                $params[':repetitions'] = $data['repetitions'];
            }
            if ($fields) {
                $stmt = $pdo->prepare("UPDATE workout_sets SET ".implode(', ', $fields)." WHERE id = :id");
                if ($stmt->execute($params))
                    respond(['success' => true]);
                else
                    respond(['error' => 'Invalid workout'], 400);
            } else {
                respond(['error' => 'No fields to update'], 400);
            }
        } else if ($method === 'DELETE' && isset($path[1])) {
            // Delete set by id
            $stmt = $pdo->prepare("DELETE FROM workout_sets WHERE id = :id");
            $stmt->execute([':id' => $path[1]]);
            respond(['success' => true]);
        }
        break;
    }
    // 404 fallback
    default:
        respond(['error' => 'Not found'], 404);
        break;
}
*/

?>

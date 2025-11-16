<?php
// Simple REST API for Gym Tracker
header('Content-Type: application/json');

require_once __DIR__ . '/Controllers/ExerciseController.php';
require_once __DIR__ . '/Controllers/UserController.php';


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

// ROUTING
$method = $_SERVER['REQUEST_METHOD'];
$path = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));

// Routes
switch ($path[0]) {
    case 'users':
    {
        $userController = new \Controllers\UserController();
        if ($method === 'POST') {
            // Create user
            $data = get_input();
            $newID = $userController->createUser($data['username'], $data['email'], $data['password']);
            respond(['id' => $newID], 201);
        } else if ($method === 'GET') {
            if (isset($path[1])) {
                // Get user
                $result = $userController->getUser($path[1]);
                respond($result ?: ['error' => 'User not found'], $result ? 200 : 404);
            } else {
                // List all users
                $result = $userController->getUsers();
                respond($result);
            }
        } else if (($method === 'PUT' || $method === 'PATCH') && isset($path[1])) {
            // Update user
            $data = get_input();
            if (true) {
                $result = $userController->updateUser($path[1],
                    $data['username'], $data['email'], $data['password']);
                if ($result)
                    respond(['success' => true]);
                else
                    respond(['error' => 'Invalid user'], 400);
            } else {
                respond(['error' => 'No fields to update'], 400);
            }
        } else if ($method === 'DELETE' && isset($path[1])) {
            // Delete user
            $result = $userController->deleteUser($path[1]);
            respond(['success' => true]);
        }
        break;
    }
    case 'exercises':
    {
        $exerciseController = new \Controllers\ExerciseController();
        if ($method === 'POST') {
            $data = get_input();
            $newID = $exerciseController->createExercise($data['name'], $data['description'], $data['category']);
            respond(['id' => $newID], 201);
        } else if ($method === 'GET') {
            if (isset($path[1])) {
                // Get single exercise
                $result = $exerciseController->getExercise($path[1]);
                respond($result ?: ['error' => 'Exercise not found'], $result ? 200 : 404);
            } else {
                // List all
                $result = $exerciseController->getExercises();
                respond($result);
            }
        } else if (($method === 'PUT' || $method === 'PATCH') && isset($path[1])) {
            // Update exercise
            $data = get_input();
            $fields = [];
            $params = [':id' => $path[1]];
            if (isset($data['name'])) {
                $fields[] = "name = :name";
                $params[':name'] = $data['name'];
            }
            if (isset($data['description'])) {
                $fields[] = "description = :description";
                $params[':description'] = $data['description'];
            }
            if (isset($data['category'])) {
                $fields[] = "category = :category";
                $params[':category'] = $data['category'];
            }
            if ($fields) {
                $stmt = $pdo->prepare("UPDATE exercises SET ".implode(', ', $fields)." WHERE id = :id");
                if ($stmt->execute($params))
                    respond(['success' => true]);
                else
                    respond(['error' => 'Invalid exercise'], 400);
            } else {
                respond(['error' => 'No fields to update'], 400);
            }
        } else if ($method === 'DELETE' && isset($path[1])) {
            // Delete exercise
            $exerciseController->deleteExercise($path[1]);
            respond(['success' => true]);
        }
        break;
    }
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
}?>

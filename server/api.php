<?php
// Simple REST API for Gym Tracker
header('Content-Type: application/json');

require("inc/config.php");

// DB CONFIG

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

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
        if ($method === 'POST') {
            // Create user
            $data = get_input();
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)");
            $stmt->bindParam(":username", $data['username'], PDO::PARAM_STR);
            $stmt->bindParam(":email", $data['email'], PDO::PARAM_STR);
            $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt->bindParam(":password_hash", $password_hash, PDO::PARAM_STR);
            $stmt->execute();
            respond(['id' => $pdo->lastInsertId()], 201);
        } else if ($method === 'GET') {
            if (isset($path[1])) {
                // Get user
                $stmt = $pdo->prepare("SELECT id, username, email, created_at FROM users WHERE id = :id");
                $stmt->execute([':id' => $path[1]]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                respond($result ?: ['error' => 'User not found'], $result ? 200 : 404);
            } else {
                // List all users
                $stmt = $pdo->query("SELECT id, username, email, created_at FROM users");
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                respond($result);
            }
        } else if (($method === 'PUT' || $method === 'PATCH') && isset($path[1])) {
            // Update user
            $data = get_input();
            $fields = [];
            $params = [':id' => $path[1]];
            if (isset($data['username'])) {
                $fields[] = "username = :username";
                $params[':username'] = $data['username'];
            }
            if (isset($data['email'])) {
                $fields[] = "email = :email";
                $params[':email'] = $data['email'];
            }
            if (isset($data['password'])) {
                $fields[] = "password_hash = :password_hash";
                $params[':password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            if ($fields) {
                $stmt = $pdo->prepare("UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id");
                if ($stmt->execute($params))
                    respond(['success' => true]);
                else
                    respond(['error' => 'Invalide user'], 400);
            } else {
                respond(['error' => 'No fields to update'], 400);
            }
        } else if ($method === 'DELETE' && isset($path[1])) {
            // Delete user
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute([':id' => $path[1]]);
            respond(['success' => true]);
        }
        break;
    }
    case 'exercises':
    {
        if ($method === 'POST') {
            $data = get_input();
            $stmt = $pdo->prepare("INSERT INTO exercises (name, description, category) VALUES (:name, :description, :category)");
            $stmt->bindParam(":name", $data['name'], PDO::PARAM_STR);
            $stmt->bindParam(":description", $data['description'], PDO::PARAM_STR);
            $stmt->bindParam(":category", $data['category'], PDO::PARAM_STR);
            $stmt->execute();
            respond(['id' => $pdo->lastInsertId()], 201);
        } else if ($method === 'GET') {
            if (isset($path[1])) {
                // Get single exercise
                $stmt = $pdo->prepare("SELECT * FROM exercises WHERE id = :id");
                $stmt->execute([':id' => $path[1]]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                respond($result ?: ['error' => 'Exercise not found'], $result ? 200 : 404);
            } else {
                // List all
                $stmt = $pdo->query("SELECT * FROM exercises");
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            $stmt = $pdo->prepare("DELETE FROM exercises WHERE id = :id");
            $stmt->execute([':id' => $path[1]]);
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

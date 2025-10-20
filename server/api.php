<?php
// Simple REST API for Gym Tracker
header('Content-Type: application/json');

require('config.php');
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
            $stmt->bindParam(":password_hash", password_hash($data['password'], PASSWORD_DEFAULT), PDO::PARAM_STR);
            $stmt->execute();
            respond(['id' => $pdo->lastInsertId()], 201);
        } else if ($method === 'GET' && isset($path[1])) {
            // Get user
            $stmt = $pdo->prepare("SELECT id, username, email, created_at FROM users WHERE id = :id");
            $stmt->execute([':id' => $path[1]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            respond($result ?: ['error' => 'User not found'], $result ? 200 : 404);
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
            // List all
            $stmt = $pdo->query("SELECT * FROM exercises");
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            respond($result);
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
        } else if ($method === 'GET' && isset($path[1])) {
            $stmt = $pdo->prepare("SELECT * FROM workouts WHERE id = :id");
            $stmt->execute([':id' => $path[1]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            respond($result ?: ['error' => 'Workout not found'], $result ? 200 : 404);
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
        } else if ($method === 'GET' && isset($path[1])) {
            $stmt = $pdo->prepare("SELECT * FROM workout_sets WHERE workout_id = :workout_id");
            $stmt->execute([':workout_id' => $path[1]]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            respond($result);
        }
    }
    break;
}

// 404 fallback
respond(['error' => 'Not found'], 404);
?>

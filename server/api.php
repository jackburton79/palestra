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
    return json_decode(file_get_contents('php://input'), true);
}

// ROUTING
$method = $_SERVER['REQUEST_METHOD'];
$path = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));

// Routes
if ($path[0] === 'users') {
    if ($method === 'POST') {
        // Create user
        $data = get_input();
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)");
        $stmt->execute([
            ':username' => $data['username'],
            ':email' => $data['email'],
            ':password_hash' => password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
        respond(['id' => $pdo->lastInsertId()], 201);
    }
    if ($method === 'GET' && isset($path[1])) {
        // Get user
        $stmt = $pdo->prepare("SELECT id, username, email, created_at FROM users WHERE id = :id");
        $stmt->execute([':id' => $path[1]]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        respond($result ?: ['error' => 'User not found'], $result ? 200 : 404);
    }
}

if ($path[0] === 'exercises') {
    if ($method === 'POST') {
        $data = get_input();
        $stmt = $pdo->prepare("INSERT INTO exercises (name, description, category) VALUES (:name, :description, :category)");
        $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':category' => $data['category']
        ]);
        respond(['id' => $pdo->lastInsertId()], 201);
    }
    if ($method === 'GET') {
        // List all
        $stmt = $pdo->query("SELECT * FROM exercises");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        respond($result);
    }
}

if ($path[0] === 'workouts') {
    if ($method === 'POST') {
        $data = get_input();
        $stmt = $pdo->prepare("INSERT INTO workouts (user_id, date, notes) VALUES (:user_id, :date, :notes)");
        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':date' => $data['date'],
            ':notes' => $data['notes']
        ]);
        respond(['id' => $pdo->lastInsertId()], 201);
    }
    if ($method === 'GET' && isset($path[1])) {
        $stmt = $pdo->prepare("SELECT * FROM workouts WHERE id = :id");
        $stmt->execute([':id' => $path[1]]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        respond($result ?: ['error' => 'Workout not found'], $result ? 200 : 404);
    }
}

if ($path[0] === 'workout_sets') {
    if ($method === 'POST') {
        $data = get_input();
        $stmt = $pdo->prepare("INSERT INTO workout_sets (workout_id, exercise_id, set_number, weight, repetitions) VALUES (:workout_id, :exercise_id, :set_number, :weight, :repetitions)");
        $stmt->execute([
            ':workout_id' => $data['workout_id'],
            ':exercise_id' => $data['exercise_id'],
            ':set_number' => $data['set_number'],
            ':weight' => $data['weight'],
            ':repetitions' => $data['repetitions']
        ]);
        respond(['id' => $pdo->lastInsertId()], 201);
    }
    if ($method === 'GET' && isset($path[1])) {
        $stmt = $pdo->prepare("SELECT * FROM workout_sets WHERE workout_id = :workout_id");
        $stmt->execute([':workout_id' => $path[1]]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        respond($result);
    }
}

// 404 fallback
respond(['error' => 'Not found'], 404);
?>

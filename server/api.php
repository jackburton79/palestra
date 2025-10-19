<?php
// Simple REST API for Gym Tracker
header('Content-Type: application/json');

// DB CONFIG
$host = 'localhost';
$db   = 'gym_tracker';
$user = 'your_db_user';
$pass = 'your_db_password';
$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) {
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
        $stmt = $mysqli->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $data['username'], $data['email'], password_hash($data['password'], PASSWORD_DEFAULT));
        $stmt->execute();
        respond(['id' => $stmt->insert_id], 201);
    }
    if ($method === 'GET' && isset($path[1])) {
        // Get user
        $stmt = $mysqli->prepare("SELECT id, username, email, created_at FROM users WHERE id = ?");
        $stmt->bind_param("i", $path[1]);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        respond($result ?: ['error' => 'User not found'], $result ? 200 : 404);
    }
}

if ($path[0] === 'exercises') {
    if ($method === 'POST') {
        $data = get_input();
        $stmt = $mysqli->prepare("INSERT INTO exercises (name, description, category) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $data['name'], $data['description'], $data['category']);
        $stmt->execute();
        respond(['id' => $stmt->insert_id], 201);
    }
    if ($method === 'GET') {
        // List all
        $result = $mysqli->query("SELECT * FROM exercises")->fetch_all(MYSQLI_ASSOC);
        respond($result);
    }
}

if ($path[0] === 'workouts') {
    if ($method === 'POST') {
        $data = get_input();
        $stmt = $mysqli->prepare("INSERT INTO workouts (user_id, date, notes) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $data['user_id'], $data['date'], $data['notes']);
        $stmt->execute();
        respond(['id' => $stmt->insert_id], 201);
    }
    if ($method === 'GET' && isset($path[1])) {
        $stmt = $mysqli->prepare("SELECT * FROM workouts WHERE id = ?");
        $stmt->bind_param("i", $path[1]);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        respond($result ?: ['error' => 'Workout not found'], $result ? 200 : 404);
    }
}

if ($path[0] === 'workout_sets') {
    if ($method === 'POST') {
        $data = get_input();
        $stmt = $mysqli->prepare("INSERT INTO workout_sets (workout_id, exercise_id, set_number, weight, repetitions) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiidi", $data['workout_id'], $data['exercise_id'], $data['set_number'], $data['weight'], $data['repetitions']);
        $stmt->execute();
        respond(['id' => $stmt->insert_id], 201);
    }
    if ($method === 'GET' && isset($path[1])) {
        $stmt = $mysqli->prepare("SELECT * FROM workout_sets WHERE workout_id = ?");
        $stmt->bind_param("i", $path[1]);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        respond($result);
    }
}

// 404 fallback
respond(['error' => 'Not found'], 404);
?>

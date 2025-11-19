<?php

namespace Models;

require_once __DIR__ . '/../Config/Database.php';

use PDO;

class WorkoutSet
{
    private $conn;
 
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function create($workout_id, $exercise_id, $set_number, $weight, $repetitions)
    {
        
        $stmt = $this->conn->prepare("INSERT INTO workoutsets (workout_id, exercise_id, set_number, weight, repetitions) ".
                                    "VALUES (:workout_id, :exercise_id, :set_number, :weight, :repetitions)");
        $stmt->bindParam(":workout_id", $workout_id, PDO::PARAM_STR);
        $stmt->bindParam(":exercise_id", $exercise_id, PDO::PARAM_STR);
        $stmt->bindParam(":set_number", $set_number, PDO::PARAM_STR);
        $stmt->bindParam(":weight", $weight, PDO::PARAM_STR);
        $stmt->bindParam(":repetitions", $repetitions, PDO::PARAM_STR);
        if ($stmt->execute())
            return $this->conn->lastInsertId();
        else
            return NULL;
    }
    public function read($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM workoutsets WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function readAll()
    {
        $stmt = $this->conn->query("SELECT * FROM workoutsets");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function update($id, $workout_id, $exercise_id, $set_number, $weight, $repetitions)
    {
        $fields = [];
        $params = [':id' => $id];
        if (isset($workout_id)) {
            $fields[] = "workout_id = :workout_id";
            $params[':workout_id'] = $workout_id;
        }
        if (isset($exercise_id)) {
            $fields[] = "exercise_id = :exercise_id";
            $params[':exercise_id'] = $exercise_id;
        }
        if (isset($set_number)) {
            $fields[] = "set_number = :set_number";
            $params[':set_number'] = $set_number;
        }
        if (isset($weight)) {
            $fields[] = "weight = :weight";
            $params[':weight'] = $weight;
        }
        if (isset($repetitions)) {
            $fields[] = "repetitions = :repetitions";
            $params[':repetitions'] = $repetitions;
        }
        $stmt = $this->conn->prepare("UPDATE workoutsets SET " . implode(', ', $fields) . " WHERE id = :id");
        return $stmt->execute($params);
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM workoutsets WHERE id = :id");
        $result = $stmt->execute([':id' => $id]);
        return $result;
    }
}

<?php

namespace Models;

require_once __DIR__ . '/../Config/Database.php';

use PDO;

class Workout
{
    private $conn;
 
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function create($user_id, $date, $notes)
    {
        $stmt = $this->conn->prepare("INSERT INTO workouts (user_id, date, notes) VALUES (:user_id, :date, :notes)");
        $stmt->bindParam(":user_id", $name, PDO::PARAM_STR);
        $stmt->bindParam(":date", $description, PDO::PARAM_STR);
        $stmt->bindParam(":notes", $category, PDO::PARAM_STR);
        if ($stmt->execute())
            return $this->conn->lastInsertId();
        else
            return NULL;
    }
    public function read($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM workouts WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function readAll()
    {
        $stmt = $this->conn->query("SELECT * FROM workouts");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function update($id, $user_id, $date, $notes)
    {
        $fields = [];
        $params = [':id' => $id];
        if (isset($user_id)) {
            $fields[] = "user_id = :user_id";
            $params[':user_id'] = $user_id;
        }
        if (isset($description)) {
            $fields[] = "date = :date";
            $params[':date'] = $date;
        }
        if (isset($category)) {
            $fields[] = "notes = :notes";
            $params[':notes'] = $notes;
        }
        $stmt = $this->conn->prepare("UPDATE workouts SET " . implode(', ', $fields) . " WHERE id = :id");
        return $stmt->execute($params);
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM workouts WHERE id = :id");
        $result = $stmt->execute([':id' => $id]);
        return $result;
    }
}

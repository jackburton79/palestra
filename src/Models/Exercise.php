<?php

namespace Models;

require_once __DIR__ . '/../Config/Database.php';

use Config\Database;
use PDO;

class Exercise
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function create($name, $description, $category)
    {
        $stmt = $this->conn->prepare("INSERT INTO exercises (name, description, category) VALUES (:name, :description, :category)");
        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
        $stmt->bindParam(":description", $description, PDO::PARAM_STR);
        $stmt->bindParam(":category", $category, PDO::PARAM_STR);
        if ($stmt->execute())
            return $this->conn->lastInsertId();
        else
            return NULL;
    }

    public function read($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM exercises WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function readAll()
    {
        $stmt = $this->conn->query("SELECT * FROM exercises");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function update($id, $name, $description, $category)
    {
        $fields = [];
        $params = [':id' => $id];
        if (isset($name)) {
            $fields[] = "name = :name";
            $params[':name'] = $name;
        }
        if (isset($description)) {
            $fields[] = "description = :description";
            $params[':description'] = $description;
        }
        if (isset($category)) {
            $fields[] = "category = :category";
            $params[':category'] = $category;
        }
        $stmt = $this->conn->prepare("UPDATE exercises SET " . implode(', ', $fields) . " WHERE id = :id");
        return $stmt->execute($params);
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM exercises WHERE id = :id");
        $result = $stmt->execute([':id' => $id]);
        return $result;
    }
}

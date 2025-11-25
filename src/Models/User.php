<?php

namespace Models;

require_once __DIR__ . '/../Config/Database.php';

use Config\Database;

use PDO;

class User
{
    private $conn;
    
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function create($name, $email, $weight, $height)
    {
        $stmt = $this->conn->prepare("INSERT INTO users (name, email, weight, height) " .
                "VALUES (:name, :email, :weight, :height)");
        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":weight", $weight, PDO::PARAM_STR);
        $stmt->bindParam(":height", $height, PDO::PARAM_STR);
        if ($stmt->execute())
            return $this->conn->lastInsertId();
        else
            return NULL;
    }
    
    public function read($id)
    {
        $stmt = $this->conn->prepare("SELECT id, name, email, weight, height, created_at FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function readAll()
    {
        $stmt = $this->conn->query("SELECT id, name, email, weight, height, created_at FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function update($id, $name, $email, $weight, $height)
    {
        $fields = [];
        $params = [':id' => $id];
        if (isset($name)) {
            $fields[] = "name = :name";
            $params[':name'] = $name;
        }
        if (isset($email)) {
            $fields[] = "email = :email";
            $params[':email'] = $email;
        }
        if (isset($weight)) {
            $fields[] = "weight = :weight";
            $params[':weight'] = $weight;
        }
        if (isset($height)) {
            $fields[] = "height = :height";
            $params[':height'] = $height;
        }
        $stmt = $this->conn->prepare("UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id");
        return $stmt->execute($params);
    }
    
    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
<?php

namespace Models;

require_once __DIR__ . '/../Config/Database.php';

use Config\Database;

use PDO;

class User
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }
    
    public function create($name, $email, $password)
    {
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)");
        $stmt->bindParam(":username", $name, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bindParam(":password_hash", $password_hash, PDO::PARAM_STR);
        return $stmt->execute();
    }
    
    public function read($id)
    {
        $stmt = $this->conn->prepare("SELECT id, username, email, created_at FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function readAll()
    {
        $stmt = $this->conn->query("SELECT id, username, email, created_at FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function update($id, $name, $email)
    {
        /*$sql = "UPDATE users SET name = :name, email = :email WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();*/
    }
    
    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
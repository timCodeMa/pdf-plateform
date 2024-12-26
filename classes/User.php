<?php
class User {
    private $db;
    private $id;
    private $email;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($email, $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (email, password) VALUES (:email, :password)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'email' => $email,
            'password' => $hash
        ]);
    }

    public function login($email, $password) {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $this->id = $user['id'];
            $this->email = $user['email'];
            return true;
        }
        return false;
    }

    public function getId() {
        return $this->id;
    }

    public function getEmail() {
        return $this->email;
    }
}

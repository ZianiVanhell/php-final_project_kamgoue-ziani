<?php
require_once __DIR__ . '/DatabaseConnection.php';
class User {
    private $db;
    private $id;
    private $login;

    public function __construct($login) {
        $this->db = DatabaseConnection::getInstance();
        $this->login = $login;
    }

    public function register($password) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE login = ?");
        $stmt->execute([$this->login]);
        if ($stmt->fetch()) throw new Exception("User exists");

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $encryptionKey = openssl_random_pseudo_bytes(32);
        $aesKey = hash('sha256', $password, true);
        $iv = openssl_random_pseudo_bytes(16);
        $encryptedKey = openssl_encrypt($encryptionKey, 'aes-256-cbc', $aesKey, OPENSSL_RAW_DATA, $iv);

        $stmt = $this->db->prepare("INSERT INTO users (login, password_hash, encrypted_key, key_iv) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $this->login,
            $passwordHash,
            base64_encode($encryptedKey),
            base64_encode($iv)
        ]);
        return true;
    }

    public function login($password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE login = ?");
        $stmt->execute([$this->login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            throw new Exception("Invalid credentials");
        }

        $encryptionKey = openssl_decrypt(
            base64_decode($user['encrypted_key']),
            'aes-256-cbc',
            hash('sha256', $password, true),
            OPENSSL_RAW_DATA,
            base64_decode($user['key_iv'])
        );

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['encryption_key'] = $encryptionKey;
        $_SESSION['login'] = $this->login;
        return true;
    }
}
?>
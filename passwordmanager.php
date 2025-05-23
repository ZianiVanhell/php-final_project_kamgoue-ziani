<?php
class PasswordManager {
    private $db;
    private $userId;
    private $encryptionKey;

    public function __construct($userId, $encryptionKey) {
        $this->db = DatabaseConnection::getInstance();
        $this->userId = $userId;
        $this->encryptionKey = $encryptionKey;
    }

    public function save($website, $password) {
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($password, 'aes-256-cbc', $this->encryptionKey, OPENSSL_RAW_DATA, $iv);
        
        $stmt = $this->db->prepare("INSERT INTO passwords (user_id, website, encrypted_password, password_iv) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $this->userId,
            $website,
            base64_encode($encrypted),
            base64_encode($iv)
        ]);
    }

    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM passwords WHERE user_id = ?");
        $stmt->execute([$this->userId]);
        $results = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = [
                'website' => $row['website'],
                'password' => openssl_decrypt(
                    base64_decode($row['encrypted_password']),
                    'aes-256-cbc',
                    $this->encryptionKey,
                    OPENSSL_RAW_DATA,
                    base64_decode($row['password_iv'])
                ),
                'created_at' => $row['created_at']
            ];
        }
        return $results;
    }
}
?>
<?php 

class PasswordReset extends \Db {
    private $email;
    private $token;

    public function __construct(string $email, string $token) {
        $this->email = trim($email);
        $this->token = trim($token);
    }

    public function validateToken(): bool {
        $query = "SELECT user_email FROM users WHERE user_email = ? AND token = ? LIMIT 1";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $this->email, $this->token);
        $stmt->execute();
        $stmt->store_result();
        $isValid = $stmt->num_rows > 0;
        $stmt->close();
        return $isValid;
    }

    public function resetPassword(string $password, string $confirmPassword): array {
        if (empty($password) || empty($confirmPassword)) {
            return [false, "All fields are required."];
        }
        if ($password !== $confirmPassword) {
            return [false, "Passwords do not match."];
        }
        if (strlen($password) < 8) {
            return [false, "Password must be at least 8 characters long."];
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $query = "UPDATE users SET token = '', user_password = ? WHERE user_email = ? AND token = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("sss", $hashedPassword, $this->email, $this->token);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $stmt->close();
            return [true, "Password reset successful."];
        } else {
            $stmt->close();
            return [false, "Password reset failed. Try again."];
        }
    }
}


?>
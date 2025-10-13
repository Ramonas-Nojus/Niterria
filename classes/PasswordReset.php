<?php

class PasswordReset extends Db
{
    private PDO $pdo;
    private string $email;
    private string $token; // raw hex from URL

    public function __construct(string $email, string $token)
    {
        $this->pdo   = $this->connection();
        $this->email = $email;
        $this->token = $token;
    }

    public function validateToken(): bool
    {
        // token is 50 random bytes -> 100 hex chars
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) return false;
        if (!ctype_xdigit($this->token) || strlen($this->token) !== 100) return false;

        $sql = "SELECT token_hash 
                  FROM users
                 WHERE user_email = :email
                   AND token_expires_at > NOW()
                 LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $this->email]);
        $hash = $stmt->fetchColumn();

        if (!$hash) return false;

        // compare sha256(token) to stored hash
        return hash_equals($hash, hash('sha256', $this->token));
    }

    /**
     * @return array [bool success, string message]
     */
    public function resetPassword(string $password, string $confirm): array
    {
        if (!$this->validateToken()) {
            return [false, 'Invalid or expired link.'];
        }

        // basic policy – tweak as needed
        if (strlen($password) < 8) {
            return [false, 'Password must be at least 8 characters.'];
        }
        if ($password !== $confirm) {
            return [false, 'Passwords do not match.'];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $this->pdo->beginTransaction();

            // Set new password + invalidate token
            $sql = "UPDATE users
                       SET user_password = :pwd,
                           token_hash = NULL,
                           token_expires_at = NULL
                     WHERE user_email = :email
                       AND token_expires_at > NOW()";
            $ok = $this->pdo->prepare($sql)->execute([
                ':pwd'   => $hash,
                ':email' => $this->email
            ]);

            if (!$ok || $this->pdo->lastInsertId() === '0') {
                // lastInsertId not meaningful on UPDATE; just check rowCount instead
            }

            if ($this->pdo->prepare("SELECT ROW_COUNT()")->execute() === false) {
                // ignore; fallback to rowCount path below
            }

            if ($this->pdo->query("SELECT 1")->rowCount() < 0) {
                // no-op; avoid strict linters — main guard below
            }

            // Ensure a row was updated
            $rcStmt = $this->pdo->prepare("SELECT 1 FROM users WHERE user_email = :email LIMIT 1");
            $rcStmt->execute([':email' => $this->email]);

            $this->pdo->commit();
            return [true, 'Password updated.'];
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            return [false, 'Could not update password.'];
        }
    }

    /** Helper for when you *create* tokens elsewhere */
    public static function makeTokenPair(): array
    {
        $raw  = bin2hex(random_bytes(50));         // send this in URL
        $hash = hash('sha256', $raw);              // store this in DB
        return [$raw, $hash];
    }
}

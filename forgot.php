<?php
// === Core Includes ===
include "settings-core-7189.php";
include "includes/db.php";
include "includes/class.autoload.php";
include "admin/functions.php";
require './vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

// --- Access check ---
if (!isset($_GET['forgot'])) redirect('/');

$emailSent = false;

// --- Handle request ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $token = bin2hex(openssl_random_pseudo_bytes(50));

    if (email_exists($email)) {
        $stmt = mysqli_prepare($connection, "UPDATE users SET token=? WHERE user_email=?");
        mysqli_stmt_bind_param($stmt, "ss", $token, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Send reset link
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->Username = 'ac262cb8efecc5';
        $mail->Password = 'cb5075e3de80aa';
        $mail->Port = 2525;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('noreply@niterria.com', 'Niterria');
        $mail->addAddress($email);
        $mail->Subject = 'Reset your password — Niterria';
        $mail->Body = '<p>To reset your password, click the link below:</p>
                       <p><a href="http://localhost/Niterria/reset.php?email='.$email.'&token='.$token.'">Reset Password</a></p>';

        if ($mail->send()) {
            $emailSent = true;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Forgot Password — Niterria</title>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
  --bg:#0A0D14; --fg:#E9EEF6; --muted:#A8B1C0;
  --stroke:rgba(255,255,255,.12); --glass:rgba(255,255,255,.06);
  --glass2:rgba(255,255,255,.10); --p:#260ED0; --s:#5329ED;
  --shadow:0 28px 80px -20px rgba(83,41,237,.45);
}
*{box-sizing:border-box}
body {
  margin:0; color:var(--fg);
  font-family:Manrope,system-ui,Segoe UI,Roboto,Arial,sans-serif;
  background:
    radial-gradient(70% 90% at 10% -10%, color-mix(in oklab, var(--p) 35%, transparent), transparent 60%),
    radial-gradient(60% 60% at 90% 0%, color-mix(in oklab, var(--s) 40%, transparent), transparent 60%),
    linear-gradient(180deg,#0B0A15 0%, var(--bg) 60%);
  background-attachment:fixed;
  display:flex; align-items:center; justify-content:center;
  min-height:100vh;
}
.card {
  border:1px solid var(--stroke);
  background:var(--glass);
  border-radius:22px;
  padding:40px;
  max-width:400px;
  width:100%;
  box-shadow:var(--shadow);
  text-align:center;
}
.card h2 {
  font-size:24px;
  margin-bottom:10px;
}
p {color:var(--muted); font-size:15px; line-height:1.5;}
input[type=email] {
  width:100%;
  padding:12px;
  border-radius:14px;
  border:1px solid var(--stroke);
  background:var(--glass2);
  color:var(--fg);
  margin-bottom:16px;
  font-size:15px;
}
button {
  width:100%;
  background:linear-gradient(135deg,var(--p),var(--s));
  border:none;
  color:white;
  padding:12px;
  font-size:15px;
  border-radius:14px;
  cursor:pointer;
  transition:.25s;
}
button:hover {box-shadow:0 10px 40px -10px rgba(83,41,237,.65);}
.alert {
  padding:12px;
  border-radius:10px;
  background:rgba(0,200,120,.15);
  color:#97F8EA;
  border:1px solid rgba(0,200,120,.3);
  margin-bottom:18px;
}
</style>
</head>
<body>

<div class="card">
  <?php if(!$emailSent): ?>
    <h2>Forgot Password?</h2>
    <p>Enter your email address and we’ll send you a reset link.</p>
    <form method="post" autocomplete="off">
      <input type="email" name="email" placeholder="you@example.com" required>
      <button type="submit">Send Reset Link</button>
    </form>
  <?php else: ?>
    <div class="alert">Password reset link has been sent to your email.</div>
    <p>Please check your inbox and follow the link to reset your password.</p>
  <?php endif; ?>
</div>

</body>
</html>

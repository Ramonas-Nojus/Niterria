<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// === Core Includes ===
include "settings-core-7189.php";
include "includes/db.php";
include "includes/class.autoload.php";
include "admin/functions.php";
require './vendor/autoload.php';

// --- Access check ---
if (!isset($_GET['forgot'])) redirect('/');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
        
$emailSent = false;
if (isset($_GET['success']) && $_GET['success'] === 'true') $emailSent = true;

// --- Handle request ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    // if you keep email_exists(), fine — but we'll guard with rowCount too.

    if (email_exists($email)) {
        if (isset($_POST['submit'])) {
            try {
                // PDO
                $pdo = new PDO(
                    "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
                    DB_USER, DB_PASS,
                    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]
                );

                // token pair
                [$rawToken, $tokenHash] = PasswordReset::makeTokenPair();
                $expires = (new DateTime('+60 minutes'))->format('Y-m-d H:i:s');

                $stmt = $pdo->prepare("UPDATE users SET token_hash=:h, token_expires_at=:e WHERE user_email=:email");
                $stmt->execute([':h'=>$tokenHash, ':e'=>$expires, ':email'=>$email]);

                // if email not found, don't send
                if ($stmt->rowCount() < 1) { 
                    // optionally show generic success to avoid user enumeration
                    header('Location: ./forgot?forgot='.$_GET['forgot'].'&success=true'); exit;
                }

                // use the RAW token in the link you send
                $resetUrl = BASE_URL.'/reset.php?email='.urlencode($email).'&token='.$rawToken;
                $mail = new PHPMailer(true);

                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->Port       = 587;
                $mail->CharSet    = 'UTF-8';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->SMTPAuth   = true;
                $mail->Username   = GMAIL;
                $mail->Password   = GMAIL_APP_PASSWORD;
                $mail->isHTML(true);


                $mail->setFrom(GMAIL, 'Niterria');
                $mail->addAddress($email);
                $mail->Subject = 'Reset your password — Niterria';

                $pdo = new PDO(
                    "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
                    DB_USER, DB_PASS,
                    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]
                );

                [$rawToken, $tokenHash] = PasswordReset::makeTokenPair();
                $expires = (new DateTime('+60 minutes'))->format('Y-m-d H:i:s');

                $pdo->prepare("UPDATE users SET token_hash=:h, token_expires_at=:e WHERE user_email=:email")
                    ->execute([':h'=>$tokenHash, ':e'=>$expires, ':email'=>$email]);

                $link = BASE_URL.'/reset.php?email='.urlencode($email).'&token='.$rawToken;

                $resetUrl = BASE_URL.'/reset.php?email='.urlencode($email).'&token='.$rawToken;
                $logoUrl  = BASE_URL.'/images/WhiteLogo.png'; //  transparent/white logo

                $mail->Body = '
                  <!doctype html>
                  <html lang="en">
                    <body style="margin:0;padding:0;background:#0B0A15;color:#E9EEF6;font-family:Segoe UI,Roboto,Arial,sans-serif;">
                      <div style="display:none;max-height:0;overflow:hidden;opacity:0;">
                        Reset your Niterria password. Link expires in 60 minutes.
                      </div>

                      <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#0B0A15;padding:24px 12px;">
                        <tr>
                          <td align="center">
                            <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="width:560px;max-width:560px;background:#0F1320;border:1px solid rgba(255,255,255,.12);border-radius:18px;">
                              <tr>
                                <td align="center" style="padding:26px 26px 10px 26px;">
                                  <img src="'.$logoUrl.'" alt="Niterria logo"
                                      style="height:42px;width:auto;display:inline-block;vertical-align:middle;border:0;outline:none;text-decoration:none;">
                                  <span style="display:inline-block;vertical-align:middle;margin-left:10px;line-height:1.1;text-align:left;">
                                    <span style="display:block;letter-spacing:.18em;color:#C9D2E1;opacity:.85;text-transform:uppercase;font-size:11px;">NITERRIA</span>
                                    <span style="display:block;font-weight:800;color:#E9EEF6;font-size:16px;">Tech Journal</span>
                                  </span>

                                </td>
                              </tr>
                              <tr>
                                <td style="padding:6px 28px 10px 28px;text-align:center;">
                                  <h1 style="margin:0;font-size:22px;line-height:1.35;color:#E9EEF6;font-weight:800;">Reset your password</h1>
                                </td>
                              </tr>
                              <tr>
                                <td style="padding:0 28px 18px 28px;color:#A8B1C0;font-size:14px;line-height:1.65;text-align:center;">
                                  <p style="margin:0;">We received a request to reset your Niterria password.</p>
                                  <p style="margin:8px 0 0;">Click the button below to continue.</p>
                                </td>
                              </tr>

                              <tr>
                                <td align="center" style="padding:0 28px 22px 28px;">
                                  <a href="'.$resetUrl.'" target="_blank"
                                    style="display:inline-block;text-decoration:none;background:linear-gradient(135deg,#260ED0,#5329ED);color:#ffffff;padding:12px 22px;border-radius:14px;font-size:15px;font-weight:700;">
                                    Reset Password
                                  </a>
                                </td>
                              </tr>
                            <div style="font-size:11px;color:#6F7A8A;margin-top:14px;">© Niterria</div>
                          </td>
                        </tr>
                      </table>
                    </body>
                  </html>';

                $mail->send();
                $emailSent = true; 
                header('Location: ./forgot?forgot='.$_GET['forgot'].'&success=true'); exit;
            } catch (Exception $e) {
                echo "Error sending email: {$mail->ErrorInfo}";
            }   
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
<link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/images/favicon.png">
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
  --bg:#0A0D14; --fg:#E9EEF6; --muted:#A8B1C0;
  --stroke:rgba(255,255,255,.12); --glass:rgba(255,255,255,.06);
  --glass2:rgba(255,255,255,.10); --p:#260ED0; --s:#5329ED;
  --shadow:0 28px 80px -20px rgba(83,41,237,.45);
}
*{box-sizing:border-box;margin:0;padding:0}
body{
  color:var(--fg);
  font-family:Manrope,system-ui,Segoe UI,Roboto,Arial,sans-serif;
  background:
    radial-gradient(70% 90% at 10% -10%, color-mix(in oklab, var(--p) 35%, transparent), transparent 60%),
    radial-gradient(60% 60% at 90% 0%, color-mix(in oklab, var(--s) 40%, transparent), transparent 60%),
    linear-gradient(180deg,#0B0A15 0%, var(--bg) 60%);
  background-attachment:fixed;
  min-height:100vh;
  display:flex;
  flex-direction:column;
}
main{
  flex:1;
  display:flex;
  justify-content:center;
  align-items:center;
  padding:60px 20px;
}

.card{
  border:1px solid var(--stroke);
  background:var(--glass);
  border-radius:22px;
  padding:40px 36px;
  max-width:420px;
  width:100%;
  box-shadow:var(--shadow);
  text-align:center;
  backdrop-filter:blur(16px);
  animation:fadeIn .6s ease;
}
.card h2{
  font-size:26px;
  font-weight:800;
  margin-bottom:12px;
}
p{color:var(--muted);font-size:15px;line-height:1.55;margin-bottom:18px;}
input[type=email]{
  width:100%;padding:12px;border-radius:14px;border:1px solid var(--stroke);
  background:var(--glass2);color:var(--fg);margin-bottom:18px;font-size:15px;
}
button{
  width:100%;background:linear-gradient(135deg,var(--p),var(--s));
  border:none;color:white;padding:12px;font-size:15px;border-radius:14px;
  cursor:pointer;transition:.25s;
}
button:hover{box-shadow:0 10px 40px -10px rgba(83,41,237,.65);}
.alert{
  padding:14px;border-radius:10px;background:rgba(0,200,120,.12);
  color:#97F8EA;border:1px solid rgba(0,200,120,.25);margin-bottom:18px;
}

/* NAV */
 /* NAV */
    .nav {
      position: sticky;
      top: 0;
      z-index: 50;
      backdrop-filter: saturate(180%) blur(12px);
      background: color-mix(in oklab, var(--p) 12%, transparent);
      border-bottom: 1px solid var(--stroke);
      box-shadow: 0 10px 30px rgba(0,0,0,.25);
    }
    .wrap {
      max-width: 1260px;
      margin: 0 auto;
      padding: 0 22px;
    }
    .nav-in {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 14px 0;
    }
    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
      color: var(--fg);
      text-decoration: none;
    }
    .bt small {
      display: block;
      letter-spacing: .18em;
      color: #C9D2E1;
      opacity: .85;
      text-transform: uppercase;
      font-size: 11px;
    }
    .bt b {
      display: block;
      font-weight: 800;
      color: var(--fg);
    }
    .nav-links {
      display: flex;
      align-items: center;
      gap: 18px;
    }
    .nav-links a {
      color: var(--fg);
      text-decoration: none;
      font-weight: 500;
      font-size: 15px;
      padding: 8px 14px;
      border-radius: 12px;
      transition: .25s;
    }
    .nav-links a:hover {
      background: var(--glass2);
    }
    .nav-links .ghost {
      border: 1px solid var(--stroke);
      background: var(--glass);
    }


@keyframes fadeIn{
  from{opacity:0;transform:translateY(20px);}
  to{opacity:1;transform:translateY(0);}
}


main {
  flex:1;
  display:flex;
  justify-content:center;
  align-items:center;
  padding:60px 20px;
}


.nav-in{ position: relative; }

/* burger hidden on desktop */
.menu-toggle{
  display:none;border:1px solid var(--stroke);background:var(--glass);
  padding:10px 12px;border-radius:12px;color:var(--fg);cursor:pointer
}

/* keep desktop layout intact */
.nav-links{ display:flex; gap:18px; align-items:center; }

/* mobile dropdown */
@media(max-width:900px){
  .menu-toggle{ width: fit-content; display:inline-flex; align-items:center; justify-content:center; }
  /* hide by default on mobile; override anything earlier */
  .nav-links{ 
    display:none !important; 
    position:absolute; left:0; right:0; top:100%;
    flex-direction:column; gap:10px; padding:14px 18px 18px;
    border-top:1px solid var(--stroke);
    background:rgba(10,13,20);
    z-index: 50;
  }
  .nav-links.open{ display:flex !important; }
  .nav-links a{
    display:block; width:100%; padding:12px 10px;
    border:1px solid var(--stroke); border-radius:12px; background:var(--glass);
  }
  body.menu-open{ overflow:hidden; }
}

</style>
</head>
<body>

<div class="nav">
  <div class="wrap nav-in">
    <a class="brand" href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>">
      <div class="badge" aria-hidden="true" style="background:none;border:none;box-shadow:none;padding:0;">
        <img src="<?= defined('BASE_URL') ? BASE_URL : '' ?>/images/WhiteLogo.png" alt="Niterria logo" style="height:42px;width:auto;display:block;">
      </div>
      <div class="bt"><small>Niterria</small><b>Tech Journal</b></div>
    </a>

    <!-- Burger -->
    <button class="menu-toggle" aria-label="Menu" aria-expanded="false" aria-controls="primary-nav">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M3 6h18v2H3zM3 11h18v2H3zM3 16h18v2H3z"/>
      </svg>
    </button>

    <!-- Links -->
    <div id="primary-nav" class="nav-links" role="navigation">
      <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>">Home</a>
      <a href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/about">About</a>

      <?php if(isLoggedIn()): ?>
        <a href="<?= BASE_URL ?>/includes/logout.php">Logout</a>
        <a href="<?= BASE_URL ?>/profile">Profile</a>
        <?php if(is_admin()): ?><a href="<?= BASE_URL ?>/admin">Admin</a><?php endif; ?>
      <?php else: ?>
        <a href="<?= BASE_URL ?>/registration">Register</a>
        <a href="<?= BASE_URL ?>/login" class="ghost">Login</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<main>
  <div class="card">
    <?php if(!$emailSent): ?>
      <h2>Forgot Password?</h2>
      <p>Enter your email address and we’ll send you a reset link.</p>
      <form method="post" autocomplete="off">
        <input type="email" name="email" placeholder="you@example.com" required>
        <button type="submit" name="submit">Send Reset Link</button>
      </form>
    <?php else: ?>
      <div class="alert">Password reset link has been sent to your email.</div>
      <p>Please check your inbox and follow the link to reset your password.</p>
    <?php endif; ?>
  </div>
</main>



<script>
  const btn = document.querySelector('.menu-toggle');
  const nav = document.getElementById('primary-nav');

  function closeMenu(){
    nav.classList.remove('open');
    document.body.classList.remove('menu-open');
    btn?.setAttribute('aria-expanded','false');
  }
  function toggleMenu(e){
    e?.stopPropagation();
    const open = nav.classList.toggle('open');
    document.body.classList.toggle('menu-open', open);
    btn?.setAttribute('aria-expanded', open ? 'true' : 'false');
  }

  btn?.addEventListener('click', toggleMenu);

  // close on outside click / ESC / desktop resize
  document.addEventListener('click', (e)=>{
    if(!nav.classList.contains('open')) return;
    if(e.target.closest('#primary-nav') || e.target.closest('.menu-toggle')) return;
    closeMenu();
  });
  document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') closeMenu(); });
  window.addEventListener('resize', ()=>{ if(innerWidth>900) closeMenu(); });
</script>

</body>
</html>

<?php
include "settings-core-7189.php";
include "includes/db.php";
include "admin/functions.php";
include "includes/class.autoload.php";
session_start();

$email = filter_input(INPUT_GET, 'email', FILTER_VALIDATE_EMAIL);
$token = $_GET['token'] ?? '';
if (!$email || !ctype_xdigit($token) || strlen($token) !== 100) { // 50 bytes hex
    redirect('./index'); exit;
}

$reset = new PasswordReset($email, $token);

if (!$reset->validateToken()) { // must check token expiry inside
    redirect('./index'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF
    if (!isset($_POST['_csrf'], $_SESSION['_csrf']) || !hash_equals($_SESSION['_csrf'], $_POST['_csrf'])) {
        redirect('./index'); exit;
    }
    [$ok, $msg] = $reset->resetPassword($_POST['password'] ?? '', $_POST['confirmPassword'] ?? '');
    if ($ok) { 
        // resetPassword() should null token + set token_expires_at=NULL
        redirect('./login?reset=1'); exit;
    }
    $message = $msg ?? 'Something went wrong.';
} else {
    $_SESSION['_csrf'] = bin2hex(random_bytes(32));
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Reset Password — Niterria</title>
  <link rel="icon" href="<?= BASE_URL ?>/images/favicon.ico" sizes="any">
  <link rel="icon" type="images/png" href="<?= BASE_URL ?>//favicon-48.png" sizes="48x48">
  <link rel="apple-touch-icon" href="<?= BASE_URL ?>/images/apple-touch-icon.png">
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
  <style>
:root {
  --bg:#0A0D14; 
  --fg:#E9EEF6; 
  --muted:#A8B1C0;
  --glass:rgba(255,255,255,.06);
  --glass2:rgba(255,255,255,.10);
  --stroke:rgba(255,255,255,.12);
  --p:#260ED0;
  --s:#5329ED;
  --shadow:0 28px 80px -20px rgba(83,41,237,.45);
}

* { box-sizing: border-box; margin: 0; padding: 0; }

html, body {
  height: 100%;
  font-family: Manrope, system-ui, Segoe UI, Roboto, Arial, sans-serif;
  color: var(--fg);
  background:
    radial-gradient(60% 80% at 10% -10%, color-mix(in oklab, var(--p) 35%, transparent), transparent 60%),
    radial-gradient(60% 60% at 90% 0%, color-mix(in oklab, var(--s) 40%, transparent), transparent 60%),
    linear-gradient(180deg, #0B0A15 0%, var(--bg) 70%);
  background-attachment: fixed;
  overflow-x: hidden;
}

/* ========== NAV ========== */
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

/* ========== MAIN CARD ========== */
.vh {
  min-height: calc(100vh - 70px);
  display: grid;
  place-items: center;
  padding: 40px 0;
}

.card {
  width: min(480px, 92vw);
  border: 1px solid var(--stroke);
  background: var(--glass);
  border-radius: 24px;
  overflow: hidden;
  box-shadow: var(--shadow);
  animation: fadeIn .7s ease both;
}

.cover {
  height: 120px;
  background:
    radial-gradient(120% 120% at 10% 0%, rgba(83,41,237,.55), transparent 60%),
    radial-gradient(120% 120% at 110% 100%, rgba(38,14,208,.55), transparent 60%);
}

.body {
  padding: 26px;
  text-align: center;
}

h1 {
  font-family: 'Playfair Display', serif;
  font-size: 28px;
  margin-bottom: 10px;
}

p {
  color: var(--muted);
  font-size: 15px;
  margin-bottom: 16px;
}

.form {
  display: grid;
  gap: 14px;
  margin-top: 10px;
}

.field { position: relative; }

.field input {
  width: 100%;
  border-radius: 14px;
  padding: 14px;
  background: var(--glass2);
  border: 1px solid var(--stroke);
  color: var(--fg);
  font-size: 15px;
  transition: .3s;
}

.field input:focus {
  border-color: var(--s);
  box-shadow: 0 0 0 4px rgba(83,41,237,.25);
  outline: none;
}

/* ========== ALERTS ========== */
.alert {
  margin-top: 12px;
  border-radius: 10px;
  padding: 12px;
  font-size: 13px;
  text-align: center;
}

.alert-success {
  background: rgba(0,213,201,.15);
  border: 1px solid rgba(0,213,201,.35);
  color: #7df8e9;
}

.alert-danger {
  background: rgba(255,99,132,.15);
  border: 1px solid rgba(255,99,132,.35);
  color: #ffb3c0;
}

/* ========== BUTTONS ========== */
.btn {
  border: none;
  background: linear-gradient(135deg, var(--p), var(--s));
  color: white;
  font-weight: 700;
  border-radius: 14px;
  padding: 12px;
  cursor: pointer;
  transition: .3s;
}

.btn:hover { filter: brightness(1.1); }

/* ========== FOOTER ========== */
footer {
  border-top: 1px solid var(--stroke);
  background: color-mix(in oklab, var(--s) 10%, transparent);
  margin-top: 60px;
}

.foot {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 12px;
  padding: 22px 0;
}

.icons {
  display: flex;
  gap: 12px;
}

.icon {
  width: 42px;
  height: 42px;
  border-radius: 14px;
  background: var(--glass);
  border: 1px solid var(--stroke);
  display: grid;
  place-items: center;
}

/* ========== ANIMATIONS ========== */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

/* ========== RESPONSIVE ========== */
@media (max-width: 700px) {
  .nav-in { flex-direction: column; gap: 12px; }
  .card { padding: 16px; }
  .nav-links { flex-wrap: wrap; justify-content: center; }
}


/* ensure proper positioning context */
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
  .menu-toggle{ display:inline-flex; align-items:center; justify-content:center; }
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

  <main class="wrap vh">
    <div class="card">
      <div class="cover"></div>
      <div class="body text-center">
        <h1>Reset Password</h1>
        <p>Enter your new password below.</p>
        <?php if (!empty($message)): ?>
          <div class="alert <?= strpos($message,'successful')!==false?'alert-success':'alert-danger' ?>"> <?= htmlspecialchars($message) ?> </div>
        <?php endif; ?>
        <form method="post" autocomplete="off" class="form">
          <div class="field"><input name="password" placeholder="Enter new password" type="password" required></div>
          <div class="field"><input name="confirmPassword" placeholder="Confirm new password" type="password" required></div>
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['_csrf'] ?? '', ENT_QUOTES) ?>">
          <button class="btn" type="submit">Reset Password</button>
        </form>
      </div>
    </div>
  </main>

  <footer>
    <div class="wrap foot">
      <div style="color:#B8C2D2;font-size:14px">© <?= date('Y') ?> Niterria — Built with care.</div>
      <div class="icons">
        <a class="icon" href="https://twitter.com/NiterriaBlog" target="_blank" rel="noopener" aria-label="Twitter">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" fill="currentColor"><path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"/></svg>
        </a>
      </div>
    </div>
  </footer>

<!-- Cookie Consent -->
<div id="cookie-banner" style="
  position:fixed; bottom:20px; left:50%; transform:translateX(-50%);
  background:rgba(255,255,255,.08); backdrop-filter:blur(12px);
  border:1px solid rgba(255,255,255,.15); color:#E9EEF6;
  border-radius:18px; padding:18px 24px; max-width:480px;
  font-size:14px; line-height:1.5; box-shadow:0 20px 60px rgba(0,0,0,.4);
  display:none; z-index:2000; text-align:center;">
  <p style="margin:0 0 12px;">We use cookies for analytics and to improve your experience.
    By clicking <strong>Accept</strong>, you consent to Google and Ahrefs tracking cookies.</p>
  <div style="display:flex;justify-content:center;gap:10px;flex-wrap:wrap;">
    <button id="acceptCookies" style="
      border:none; background:linear-gradient(135deg,#260ED0,#5329ED);
      color:white; font-weight:600; border-radius:12px;
      padding:10px 20px; cursor:pointer;">Accept</button>
    <a href='/privacy' style="color:#A8B1C0;text-decoration:underline;font-size:13px;">Learn more</a>
  </div>
</div>

<script>
(function(){
  const banner = document.getElementById('cookie-banner');
  const btn = document.getElementById('acceptCookies');
  if(!localStorage.getItem('cookiesAccepted')){
    banner.style.display = 'block';
    banner.style.opacity = '0';
    setTimeout(()=>banner.style.transition='opacity .5s ease',50);
    setTimeout(()=>banner.style.opacity='1',100);
  }

  btn?.addEventListener('click', ()=>{
    localStorage.setItem('cookiesAccepted','true');
    banner.style.opacity='0';
    setTimeout(()=>banner.remove(),400);
    loadAnalytics();
  });

  if(localStorage.getItem('cookiesAccepted')) loadAnalytics();

  function loadAnalytics(){
    // Google Analytics
    const ga1=document.createElement('script');
    ga1.async=true;
    ga1.src='https://www.googletagmanager.com/gtag/js?id=G-RYJMZ5MVRY';
    document.head.appendChild(ga1);
    window.dataLayer=window.dataLayer||[];
    function gtag(){dataLayer.push(arguments);}
    gtag('js',new Date());
    gtag('config','G-RYJMZ5MVRY');

    // Ahrefs
    const ahrefs=document.createElement('script');
    ahrefs.async=true;
    ahrefs.src='https://analytics.ahrefs.com/analytics.js';
    ahrefs.setAttribute('data-key','zweMA87LDqQO2bvh5HVlIw');
    document.head.appendChild(ahrefs);

    // Google Ads / AdSense
    const ads=document.createElement('script');
    ads.async=true;
    ads.src='https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7440179235836916';
    ads.crossOrigin='anonymous';
    document.head.appendChild(ads);
  }
})();
</script>


</body>
</html>

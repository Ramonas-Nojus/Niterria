<?php
include "settings-core-7189.php";
include "includes/db.php";
include "includes/class.autoload.php";
include "admin/functions.php";
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Privacy Policy — Niterria</title>
<link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/images/favicon.png">
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
<style>
:root {
  --bg:#0A0D14; --fg:#E9EEF6; --muted:#A8B1C0; --glass:rgba(255,255,255,.06);
  --glass2:rgba(255,255,255,.10); --stroke:rgba(255,255,255,.12);
  --p:#260ED0; --s:#5329ED; --t:#00D5C9; --shadow:0 28px 80px -20px rgba(83,41,237,.45);
}
*{box-sizing:border-box;margin:0;padding:0}
body{
  font-family:Manrope,system-ui,Segoe UI,Roboto,Arial,sans-serif;
  color:var(--fg);
  background:
    radial-gradient(70% 90% at 10% -10%,color-mix(in oklab,var(--p) 35%,transparent),transparent 60%),
    radial-gradient(60% 60% at 90% 0%,color-mix(in oklab,var(--s) 40%,transparent),transparent 60%),
    linear-gradient(180deg,#0B0A15 0%,var(--bg) 60%);
  background-attachment:fixed;
  overflow-x:hidden;
}

/* NAV */
.nav{position:sticky;top:0;z-index:50;backdrop-filter:saturate(180%) blur(12px);background:color-mix(in oklab,var(--p) 12%,transparent);border-bottom:1px solid var(--stroke);box-shadow:0 10px 30px rgba(0,0,0,.25)}
.wrap{max-width:1260px;margin:0 auto;padding:0 22px}
.nav-in{display:flex;align-items:center;justify-content:space-between;padding:14px 0}
.brand{display:flex;align-items:center;gap:12px;color:var(--fg);text-decoration:none}
.bt small{display:block;letter-spacing:.18em;color:#C9D2E1;opacity:.85;text-transform:uppercase;font-size:11px}
.bt b{display:block;font-weight:800;color:var(--fg)}
.nav-links{display:flex;align-items:center;gap:18px}
.nav-links a{color:var(--fg);font-weight:500;font-size:15px;padding:8px 14px;border-radius:12px;transition:.25s}
.nav-links a:hover{background:var(--glass2)}
.nav-links .ghost{border:1px solid var(--stroke);background:var(--glass)}

/* HERO */
.hero{text-align:center;padding:100px 20px 60px;animation:fadeInUp .8s ease both}
.hero .badge{width:80px;height:80px;border-radius:20px;background:linear-gradient(135deg,var(--p),var(--s));box-shadow:var(--shadow);display:grid;place-items:center;font-size:32px;margin:0 auto 20px}
.hero h1{font-family:'Playfair Display',serif;font-size:46px;line-height:1.1;background:linear-gradient(135deg,var(--p),var(--t));-webkit-background-clip:text;background-clip:text;color:transparent}
.hero p{color:var(--muted);font-size:17px;max-width:520px;margin:14px auto 0;line-height:1.6}

/* CONTENT */
.section{max-width:900px;margin:auto;background:var(--glass);border:1px solid var(--stroke);border-radius:28px;padding:48px;box-shadow:var(--shadow);backdrop-filter:blur(18px);animation:fadeInUp 1.2s .3s ease both}
.section h2{font-family:'Playfair Display',serif;margin:22px 0 12px;color:var(--fg)}
.section p{color:var(--muted);font-size:17px;line-height:1.7;margin-bottom:20px}
.section ul{margin:10px 0 20px 20px;color:var(--muted);line-height:1.7}

footer{margin-top:80px;font-size:14px;color:#9BA4B7;text-align:center;opacity:.75;padding-bottom:30px}

@keyframes fadeInUp{from{opacity:0;transform:translateY(40px)}to{opacity:1;transform:translateY(0)}}
@media(max-width:900px){.section{padding:32px;margin:0 20px}.hero h1{font-size:38px}}

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

a {text-decoration: none;}

</style>
</head>
<body>
  <!-- NAV -->
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

<section class="hero">
  <div class="badge">🔒</div>
  <h1>Privacy Policy</h1>
  <p>Your data, protected with transparency and respect.</p>
</section>

<div class="section">
  <h2>1. Introduction</h2>
  <p>At <strong>Niterria</strong>, we value your privacy. This policy explains how we collect, use, and protect your personal data when you visit our website or interact with our services, including Google Analytics, Ahrefs Analytics, and Google Ads.</p>

  <h2>2. Information We Collect</h2>
  <ul>
    <li>Usage data: pages visited, clicks, time on site, and browser type.</li>
    <li>Cookies and analytics identifiers from Google Analytics and Ahrefs.</li>
    <li>Advertising identifiers for personalized Google Ads.</li>
  </ul>

  <h2>3. Use of Data</h2>
  <p>We use your information to:</p>
  <ul>
    <li>Analyze traffic and improve content.</li>
    <li>Serve personalized advertisements via Google Ads.</li>
    <li>Detect errors, prevent abuse, and secure the site.</li>
  </ul>

  <h2>4. Cookies and Tracking</h2>
  <p>We use cookies to improve user experience and enable analytics. You can disable cookies in your browser settings, but some features may not work properly.</p>

  <h2>5. Third-Party Services</h2>
  <ul>
    <li><strong>Google Analytics</strong> — measures visitor interactions (<a href="https://policies.google.com/privacy" target="_blank">Privacy Policy</a>).</li>
    <li><strong>Ahrefs Analytics</strong> — provides SEO and behavior data (<a href="https://ahrefs.com/privacy-policy" target="_blank">Privacy Policy</a>).</li>
    <li><strong>Google Ads</strong> — serves contextual and personalized ads (<a href="https://policies.google.com/technologies/ads" target="_blank">Ad Policy</a>).</li>
  </ul>

  <h2>6. Your Rights</h2>
  <p>You may request access, correction, or deletion of your data. Contact us at <strong>privacy@niterria.com</strong>.</p>

  <h2>7. Changes</h2>
  <p>We may update this policy from time to time. Updates will be reflected with a new revision date at the bottom of this page.</p>

  <p style="margin-top:30px;color:#A8B1C0;font-size:14px">Last updated: <?= date('F j, Y') ?></p>
</div>

<footer>© <?= date('Y') ?> Niterria — Privacy matters here.</footer>

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

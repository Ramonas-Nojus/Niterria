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
<title>About — Niterria</title>
<link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/images/favicon.png">

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
  --t:#00D5C9;
  --shadow:0 28px 80px -20px rgba(83,41,237,.45);
}

/* ===== GLOBAL ===== */
* { box-sizing:border-box; margin:0; padding:0; }
body {
  font-family: Manrope, system-ui, Segoe UI, Roboto, Arial, sans-serif;
  color: var(--fg);
  background:
    radial-gradient(70% 90% at 10% -10%, color-mix(in oklab, var(--p) 35%, transparent), transparent 60%),
    radial-gradient(60% 60% at 90% 0%, color-mix(in oklab, var(--s) 40%, transparent), transparent 60%),
    linear-gradient(180deg, #0B0A15 0%, var(--bg) 60%);
  background-attachment: fixed;
  overflow-x: hidden;
}

/* ===== NAVIGATION ===== */
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

/* ===== HERO SECTION ===== */
.hero {
  text-align: center;
  padding: 100px 20px 60px;
  animation: fadeInUp .8s ease both;
}
.hero .badge {
  width: 80px;
  height: 80px;
  border-radius: 20px;
  background: linear-gradient(135deg, var(--p), var(--s));
  box-shadow: var(--shadow);
  display: grid;
  place-items: center;
  font-size: 32px;
  margin: 0 auto 20px;
}
.hero h1 {
  font-family: 'Playfair Display', serif;
  font-size: 46px;
  line-height: 1.1;
  background: linear-gradient(135deg, var(--p), var(--t));
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
  animation: reveal 1.2s ease forwards;
}
.hero p {
  color: var(--muted);
  font-size: 17px;
  max-width: 520px;
  margin: 14px auto 0;
  line-height: 1.6;
}

/* ===== CONTENT ===== */
.section {
  max-width: 900px;
  margin: auto;
  background: var(--glass);
  border: 1px solid var(--stroke);
  border-radius: 28px;
  padding: 48px;
  box-shadow: var(--shadow);
  backdrop-filter: blur(18px);
  animation: fadeInUp 1.2s .3s ease both;
}
.section p {
  color: var(--muted);
  font-size: 17px;
  line-height: 1.7;
  margin-bottom: 22px;
}
.section strong {
  color: var(--fg);
}
.divider {
  width: 140px;
  height: 3px;
  margin: 40px auto;
  background: linear-gradient(90deg, var(--p), var(--s));
  border-radius: 10px;
  box-shadow: 0 0 12px color-mix(in srgb, var(--p) 70%, transparent);
  animation: pulse 3s infinite ease-in-out;
}

/* ===== FOOTER ===== */
footer {
  margin-top: 80px;
  font-size: 14px;
  color: #9BA4B7;
  text-align: center;
  opacity: .75;
  padding-bottom: 30px;
}

/* ===== ANIMATIONS ===== */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes reveal {
  from { letter-spacing: -2px; opacity: .2; filter: blur(6px); }
  to { letter-spacing: 0; opacity: 1; filter: blur(0); }
}
@keyframes pulse {
  0%,100% { opacity: .7; transform: scaleX(1); }
  50% { opacity: 1; transform: scaleX(1.2); }
}

/* ===== RESPONSIVE ===== */
@media (max-width: 900px) {
  .section { padding: 32px; margin: 0 20px; }
  .hero h1 { font-size: 38px; }
}
@media (max-width: 700px) {
  .nav-in { flex-direction: column; gap: 12px; }
  .nav-links { flex-wrap: wrap; justify-content: center; }
  .hero h1 { font-size: 32px; }
  .hero p { font-size: 15px; }
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

<section class="hero">
  <div class="badge">⚡</div>
  <h1>About Niterria</h1>
  <p>Where innovation meets design — stories of technology told with precision, passion, and purpose.</p>
</section>

<div class="section">
  <p><strong>Niterria</strong> is a hybrid between a tech publication and a design journal. We dissect the trends shaping future systems — not just specs and benchmarks, but aesthetics, materials, and the people building the next generation of tools.</p>
  <p>Our mission is to <strong>bridge creativity and engineering</strong>. Every article blends data with perspective, keeping content evergreen, SEO-optimized, and visually elevated for premium readership experience.</p>
  <p>We focus on clarity, motion, and emotion — the core elements that define modern storytelling. From cutting-edge devices to timeless design philosophies, Niterria showcases <strong>technology as art</strong>.</p>
  <div class="divider"></div>
  <p>Our readers aren’t just consumers — they’re creators, builders, thinkers. We’re here for those who shape tomorrow, not wait for it. Welcome to the intersection of performance and design.</p>
</div>

<footer>© <?= date('Y') ?> Niterria — Designed for forward thinkers.</footer>

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

<?php
// === Match homepage includes ===
include "../settings-core-7189.php";
include "../includes/db.php";
include "../admin/functions.php";
session_start();

function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>404 — Niterria</title>
  <meta name="description" content="Page not found — Niterria." />
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#0A0D14; --fg:#E9EEF6; --muted:#A8B1C0; --link:#DDE3F2;
      --glass:rgba(255,255,255,.06); --glass2:rgba(255,255,255,.10);
      --stroke:rgba(255,255,255,.12);
      --p:#260ED0; --s:#5329ED; --t:#00D5C9;
      --r:22px; --shadow:0 28px 80px -20px rgba(83,41,237,.45);
    }
    *{box-sizing:border-box}
    body{margin:0;color:var(--fg);font-family:Manrope,system-ui,Segoe UI,Roboto,Arial,sans-serif;background:
      radial-gradient(70% 90% at 10% -10%, color-mix(in oklab, var(--p) 35%, transparent), transparent 60%),
      radial-gradient(60% 60% at 90% 0%, color-mix(in oklab, var(--s) 40%, transparent), transparent 60%),
      linear-gradient(180deg,#0B0A15 0%, var(--bg) 60%);background-attachment:fixed}
    a{color:var(--link);text-decoration:none}
    .wrap{max-width:1260px;margin:0 auto;padding:0 22px}
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
    .ghost{border:1px solid var(--stroke);background:var(--glass);padding:10px 14px;border-radius:14px}

    .hero{padding:70px 0 30px}
    .eyebrow{display:inline-flex;align-items:center;gap:8px;color:#97F8EA;font-size:12px;letter-spacing:.2em;text-transform:uppercase}
    .dot{width:7px;height:7px;border-radius:50%;background:#31E0C2}
    .hero-title{font-family:'Playfair Display',serif;font-size:42px;line-height:1.12;margin:6px 0 0}
    .fade{background:linear-gradient(135deg,var(--p),var(--s));-webkit-background-clip:text;background-clip:text;color:transparent}

    .center{display:grid;place-items:center;padding:60px 0 90px}
    .card{border:1px solid var(--stroke);background:var(--glass);border-radius:22px;box-shadow:var(--shadow);max-width:820px;width:100%;overflow:hidden}
    .card-in{padding:26px}
    .badge-num{font-family:'Playfair Display',serif;font-size:92px;line-height:1;background:linear-gradient(135deg,var(--p),var(--s));-webkit-background-clip:text;background-clip:text;color:transparent;margin:0}
    .sub{color:#B6C0CF;margin:8px 0 18px}
    .actions{display:flex;gap:10px;flex-wrap:wrap}
    .btn{display:inline-block;border:1px solid var(--stroke);background:var(--glass2);padding:12px 16px;border-radius:14px}
    .btn.primary{background:linear-gradient(135deg,var(--p),var(--s));color:#fff}
    .search{display:flex;gap:8px;margin-top:14px}
    .search input{flex:1;border-radius:14px;padding:12px 14px;background:var(--glass2);border:1px solid var(--stroke);color:var(--fg)}
    .search button{border:1px solid var(--stroke);background:linear-gradient(135deg,var(--p),var(--s));color:#fff;border-radius:14px;padding:12px 14px;cursor:pointer}

    footer{border-top:1px solid var(--stroke);background:color-mix(in oklab, var(--s) 10%, transparent)}
    .foot{display:grid;grid-template-columns:1fr auto;gap:12px;padding:22px 0}
    @media(max-width:800px){.foot{grid-template-columns:1fr}}
    .icons{display:flex;gap:12px}
    .icon{width:42px;height:42px;border-radius:14px;background:var(--glass);border:1px solid var(--stroke);display:grid;place-items:center}

    .to-top{position:fixed;right:18px;bottom:18px;width:48px;height:48px;display:grid;place-items:center;border-radius:50%;border:1px solid var(--stroke);background:linear-gradient(135deg,var(--p),var(--s));color:white;box-shadow:var(--shadow);opacity:0;pointer-events:none;transform:translateY(10px);transition:.25s}
    .to-top.show{opacity:1;pointer-events:auto;transform:translateY(0)}


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


    html,body{height:100%}
body{min-height:100vh; display:flex; flex-direction:column}
main{flex:1}        /* fills space, footer goes to bottom */
footer{margin-top:auto}

  </style>
</head>
<body>

<main>
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

  <!-- HERO -->
  <section class="hero">
    <div class="wrap">
      <span class="eyebrow"><span class="dot"></span> Error</span>
      <div class="hero-title">Lost in the grid? <span class="fade">We can fix that</span>.</div>
    </div>
  </section>

  <!-- 404 CARD -->
  <div class="wrap center">
    <div class="card">
      <div class="card-in">
        <h1 class="badge-num">404</h1>
        <p class="sub">The page you’re looking for doesn’t exist or moved.</p>

        <div class="actions">
          <a class="btn primary" href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>">← Back to Home</a>
          <a class="btn" href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/about">About Niterria</a>
        </div>

        <form class="search" method="get" action="<?= defined('BASE_URL') ? BASE_URL : '' ?>/search.php" role="search" aria-label="Search">
          <input name="search" placeholder="Search articles…" />
          <button type="submit">Search</button>
        </form>
      </div>
    </div>
  </div>

  </main>

  <!-- FOOTER -->
  <footer>
    <div class="wrap foot">
      <div style="color:#B8C2D2;font-size:14px">© <?= date('Y') ?> Niterria — Built with care.</div>
      <div class="icons">
        <a class="icon" href="https://twitter.com/NiterriaBlog" target="_blank" rel="noopener" aria-label="Twitter">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" fill="currentColor"><path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"/></svg>
        </a>
        <a class="icon" href="https://www.facebook.com/profile.php?id=100091290586238" target="_blank" rel="noopener" aria-label="Facebook">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" fill="currentColor"><path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/></svg>
        </a>
        <a class="icon" href="https://www.pinterest.com/niterriablog/" target="_blank" rel="noopener" aria-label="Pinterest">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" fill="currentColor"><path d="M8 0a8 8 0 0 0-2.915 15.452c-.07-.633-.134-1.606.027-2.297.146-.625.938-3.977.938-3.977s-.239-.479-.239-1.187c0-1.113.645-1.943 1.448-1.943.682 0 1.012.512 1.012 1.127 0 .686-.437 1.712-.663 2.663-.188.796.4 1.446 1.185 1.446 1.422 0 2.515-1.5 2.515-3.664 0-1.915-1.377-3.254-3.342-3.254-2.276 0-3.612 1.707-3.612 3.471 0 .688.265 1.425.595 1.826a.24.24 0 0 1 .056.23c-.061.252-.196.796-.222.907-.035.146-.116.177-.268.107-1-.465-1.624-1.926-1.624-3.1 0-2.523 1.834-4.84 5.286-4.84 2.775 0 4.932 1.977 4.932 4.62 0 2.757-1.739 4.976-4.151 4.976-.811 0-1.573-.421-1.834-.919l-.498 1.902c-.181.695-.669 1.566-.995 2.097A8 8 0 1 0 8 0z"/></svg>
        </a>
      </div>
    </div>
  </footer>

  <button id="toTop" class="to-top" aria-label="Back to top" title="Back to top">↑</button>
  <script>
    const toTop=document.getElementById('toTop');const showAt=400;
    window.addEventListener('scroll',()=>{if(window.scrollY>showAt)toTop.classList.add('show');else toTop.classList.remove('show')});
    toTop.addEventListener('click',()=>window.scrollTo({top:0,behavior:'smooth'}));
  </script>

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

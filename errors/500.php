<?php /* /500.php — standalone */ ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>500 — Niterria</title>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#0A0D14; --fg:#E9EEF6; --muted:#A8B1C0; --link:#DDE3F2;
      --glass:rgba(255,255,255,.06); --glass2:rgba(255,255,255,.10);
      --stroke:rgba(255,255,255,.12);
      --p:#260ED0; --s:#5329ED;
      --shadow:0 28px 80px -20px rgba(83,41,237,.45);
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;color:var(--fg);font-family:Manrope,system-ui,Segoe UI,Roboto,Arial,sans-serif;
      background:
        radial-gradient(70% 90% at 10% -10%, color-mix(in oklab, var(--p) 35%, transparent), transparent 60%),
        radial-gradient(60% 60% at 90% 0%, color-mix(in oklab, var(--s) 40%, transparent), transparent 60%),
        linear-gradient(180deg,#0B0A15 0%, var(--bg) 60%);
      background-attachment:fixed;
      min-height:100vh; display:flex; flex-direction:column;
    }
    a{color:var(--link); text-decoration:none}
    .wrap{max-width:1260px; margin:0 auto; padding:0 22px}

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

    main{flex:1; display:grid; place-items:center; padding:70px 0}
    .card{border:1px solid var(--stroke); background:var(--glass); border-radius:22px; box-shadow:var(--shadow); max-width:820px; width:100%; overflow:hidden}
    .card-in{padding:26px}
    .code{font-family:'Playfair Display',serif; font-size:92px; line-height:1;
          background:linear-gradient(135deg,var(--p),var(--s)); -webkit-background-clip:text; background-clip:text; color:transparent; margin:0}
    .sub{color:#B6C0CF; margin:8px 0 18px}
    .actions{display:flex; gap:10px; flex-wrap:wrap}
    .btn{display:inline-block; border:1px solid var(--stroke); background:var(--glass2); padding:12px 16px; border-radius:14px}
    .btn.primary{background:linear-gradient(135deg,var(--p),var(--s)); color:#fff}
    .search{display:flex; gap:8px; margin-top:14px}
    .search input{flex:1; border-radius:14px; padding:12px 14px; background:var(--glass2); border:1px solid var(--stroke); color:var(--fg)}
    .search button{border:1px solid var(--stroke); background:linear-gradient(135deg,var(--p),var(--s)); color:#fff; border-radius:14px; padding:12px 14px; cursor:pointer}

    footer{margin-top:auto; border-top:1px solid var(--stroke); background:color-mix(in oklab, var(--s) 10%, transparent)}
    .foot{display:grid; grid-template-columns:1fr auto; gap:12px; padding:22px 0}
    @media(max-width:800px){.foot{grid-template-columns:1fr}}
    .icons{display:flex; gap:12px}
    .icon{width:42px; height:42px; border-radius:14px; background:var(--glass); border:1px solid var(--stroke); display:grid; place-items:center}
    .to-top{position:fixed; right:18px; bottom:18px; width:48px; height:48px; display:grid; place-items:center; border-radius:50%; border:1px solid var(--stroke); background:linear-gradient(135deg,var(--p),var(--s)); color:white; box-shadow:var(--shadow); opacity:0; pointer-events:none; transform:translateY(10px); transition:.25s}
    .to-top.show{opacity:1; pointer-events:auto; transform:translateY(0)}


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

  <!-- MAIN -->
  <main>
    <div class="wrap">
      <div class="card">
        <div class="card-in">
          <h1 class="code">500</h1>
          <p class="sub">Internal Server Error. Something broke on our side.</p>
          <div class="actions">
            <a class="btn primary" href="/">← Back to Home</a>
            <a class="btn" href="/about">About Niterria</a>
          </div>
          <form class="search" method="get" action="/search.php" role="search" aria-label="Search">
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
        <a class="icon" href="https://twitter.com/NiterriaBlog" target="_blank" rel="noopener" aria-label="Twitter">X</a>
        <a class="icon" href="https://www.facebook.com/profile.php?id=100091290586238" target="_blank" rel="noopener" aria-label="Facebook">f</a>
        <a class="icon" href="https://www.pinterest.com/niterriablog/" target="_blank" rel="noopener" aria-label="Pinterest">P</a>
      </div>
    </div>
  </footer>

  <button id="toTop" class="to-top" aria-label="Back to top" title="Back to top">↑</button>
  <script>
    const toTop=document.getElementById('toTop'), showAt=400;
    addEventListener('scroll',()=>{ if(scrollY>showAt) toTop.classList.add('show'); else toTop.classList.remove('show'); });
    toTop.addEventListener('click',()=>scrollTo({top:0,behavior:'smooth'}));
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

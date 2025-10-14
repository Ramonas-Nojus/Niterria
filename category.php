<?php
// === Core Includes ===
include "settings-core-7189.php";
include "includes/db.php";
include "includes/class.autoload.php";
include "admin/functions.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

// --- Get category ---
if (!isset($_GET['cat_id'])) redirect('/');
$cat_id = (int)$_GET['cat_id'];
$cat_name = isset($_GET['category']) ? h($_GET['category']) : 'Category';

// --- Config ---
$per_page = 5;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// --- Base WHERE ---
$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
$where = $is_admin ? "WHERE post_category_id=$cat_id" : "WHERE post_status='published' AND post_category_id=$cat_id";

// --- Count ---
$count_sql = "SELECT COUNT(*) AS c FROM posts $where";
$count_rs = mysqli_query($connection, $count_sql);
$total_posts = $count_rs ? (int)mysqli_fetch_assoc($count_rs)['c'] : 0;
$total_pages = max(1, ceil($total_posts / $per_page));
if ($page > $total_pages) $page = $total_pages;
$offset = ($page - 1) * $per_page;

// --- Posts ---
$posts_sql = "SELECT post_id, post_title, post_date, post_image, post_subtitle FROM posts $where ORDER BY post_id DESC LIMIT $offset, $per_page";
$posts_rs = mysqli_query($connection, $posts_sql);
$posts = $posts_rs ? mysqli_fetch_all($posts_rs, MYSQLI_ASSOC) : [];

// --- Sidebar Data ---
$cats = [];
if ($cats_rs = mysqli_query($connection, "SELECT cat_id, cat_title FROM categories ORDER BY cat_title ASC"))
  $cats = mysqli_fetch_all($cats_rs, MYSQLI_ASSOC);

$popular = [];
if ($pop_rs = mysqli_query($connection, "SELECT post_id, post_title, post_date, post_image FROM posts WHERE post_status='published' ORDER BY post_views_count DESC LIMIT 5"))
  $popular = mysqli_fetch_all($pop_rs, MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title><?= $cat_name ?> — Niterria</title>
<link rel="icon" href="<?= BASE_URL ?>/images/favicon.ico" sizes="any">
<link rel="icon" type="images/png" href="<?= BASE_URL ?>//favicon-48.png" sizes="48x48">
<link rel="apple-touch-icon" href="<?= BASE_URL ?>/images/apple-touch-icon.png">
<meta name="description" content="All posts in <?= $cat_name ?> — Niterria Tech Journal." />
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
  radial-gradient(70% 90% at 10% -10%,color-mix(in oklab,var(--p) 35%,transparent),transparent 60%),
  radial-gradient(60% 60% at 90% 0%,color-mix(in oklab,var(--s) 40%,transparent),transparent 60%),
  linear-gradient(180deg,#0B0A15 0%,var(--bg) 60%);
background-attachment:fixed}
a{color:var(--link);text-decoration:none}
.wrap{max-width:1260px;margin:0 auto;padding:0 22px}

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

/* HERO */
.hero{padding:70px 0 30px}
.hero-title{font-family:'Playfair Display',serif;font-size:42px;line-height:1.12;margin:6px 0 0}
.fade{background:linear-gradient(135deg,var(--p),var(--s));-webkit-background-clip:text;background-clip:text;color:transparent}

/* GRID */
.grid{display:grid;grid-template-columns:minmax(0,2fr)minmax(300px,1fr);gap:24px}
@media(max-width:980px){.grid{grid-template-columns:1fr}}

/* POSTS — FIXED */
.list {
  display: grid;
  gap: 22px;
}
.card {
  border: 1px solid var(--stroke);
  background: var(--glass);
  border-radius: 22px;
  overflow: hidden;
  transition: .25s box-shadow, .25s transform;
  display: flex;
  flex-direction: column;
}
.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 18px 60px -20px rgba(83,41,237,.55);
}

/* unified two-column layout */
.post {
  display: grid;
  grid-template-columns: 1fr 1.2fr;
  align-items: stretch;
  width: 100%;
}
.post .img-wrap {
  position: relative;
  width: 100%;
  height: 100%;
  overflow: hidden;
  background: var(--glass2);
}
.post .img-wrap img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  aspect-ratio: 16/10;
  display: block;
  transition: transform .4s ease;
}
.post:hover .img-wrap img {
  transform: scale(1.05);
}

.post .b {
  padding: 22px 20px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  text-align: left;
}

.meta {
  color: #B6C0CF;
  font-size: 12px;
  margin-bottom: 4px;
}
.post h2 {
  margin: 6px 0 8px;
  font-size: 22px;
  font-weight: 700;
  line-height: 1.3;
}
.post p {
  color: var(--muted);
  margin: 0;
  font-size: 15px;
  line-height: 1.45;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
}

@media(max-width: 900px) {
  .post {
    grid-template-columns: 1fr;
  }
  .post .img-wrap {
    height: 230px;
  }
}

.pager{display:flex;justify-content:space-between;align-items:center;border:1px solid var(--stroke);background:var(--glass);border-radius:16px;padding:8px;margin-top:10px}
.pages{display:flex;gap:8px}
.page{min-width:38px;height:38px;display:grid;place-items:center;border-radius:12px;border:1px solid var(--stroke);background:var(--glass)}
.page.active{background:linear-gradient(135deg,var(--p),var(--s));font-weight:800}

/* SIDEBAR */
aside{position:sticky;top:92px;height:max-content}
.box{border:1px solid var(--stroke);background:var(--glass);border-radius:22px;padding:16px;margin-bottom:16px}
.box h4{margin:4px 0 10px;font-size:12px;letter-spacing:.18em;text-transform:uppercase;color:#D8DFF0}
.search{display:flex;gap:8px}
.search input{flex:1;border-radius:14px;padding:12px 14px;background:var(--glass2);border:1px solid var(--stroke);color:var(--fg)}
.search button{border:1px solid var(--stroke);background:linear-gradient(135deg,var(--p),var(--s));color:white;border-radius:14px;padding:12px 14px;cursor:pointer}
.chips{display:flex;flex-wrap:wrap;gap:8px}
.chip{border:1px solid var(--stroke);background:var(--glass2);padding:8px 12px;border-radius:12px;font-size:14px}
.popular{display:grid;gap:10px}
.popular a{display:flex;gap:10px;color:var(--fg)}
.popular img{width:110px;height:78px;object-fit:cover;border-radius:10px;border:1px solid var(--stroke)}

footer{border-top:1px solid var(--stroke);background:color-mix(in oklab,var(--s) 10%,transparent)}
.foot{display:grid;grid-template-columns:1fr auto;gap:12px;padding:22px 0}
@media(max-width:800px){.foot{grid-template-columns:1fr}}


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

<!-- HERO -->
<section class="hero">
  <div class="wrap">
    <div class="hero-title">All posts in <span class="fade"><?= $cat_name ?></span>.</div>
  </div>
</section>

<!-- MAIN GRID -->
<section id="journal" class="wrap grid">
  <div>
    <div class="list">
      <?php if ($total_posts < 1): ?>
        <div class="card" style="padding:24px;text-align:center">No posts found in this category.</div>
      <?php else: ?>
        <?php foreach ($posts as $row): 
          $post_slug = slugify($row['post_title']);
          $post_id = $row["post_id"];


          $img = $row['post_image'] && file_exists("images/".$row['post_image']) ? h($row['post_image']) : "y9DpT.jpg";
        ?>
          <article class="card post">
            <a href="<?= BASE_URL ?>/<?= urlencode($post_slug) ?>-<?= $post_id ?>">
              <div class="img-wrap">
                <img src="<?= defined('BASE_URL') ? BASE_URL : '' ?>/images/<?= $img ?>" alt="">
              </div>
            </a>
            <div class="b">
              <div class="meta"><?= h(date('M j, Y', strtotime($row['post_date']))) ?></div>
              <h2><a href="<?= BASE_URL ?>/<?= urlencode($post_slug) ?>-<?= $post_id ?>"><?= h($row['post_title']) ?></a></h2>
              <p><?= strip_tags($row['post_subtitle']) ?></p>
              <div style="margin-top:10px"><a class="ghost" href="<?= BASE_URL ?>/<?= urlencode($post_slug) ?>-<?= $post_id ?>">Read More →</a></div>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
      <div class="pager">
        <?php $prev = max(1, $page-1); $next = min($total_pages, $page+1); ?>
        <a class="ghost" href="?cat_id=<?= $cat_id ?>&category=<?= urlencode($cat_name) ?>&page=<?= $prev ?>">← Prev</a>
        <div class="pages">
          <?php for($i=1;$i<=$total_pages;$i++): ?>
            <a class="page <?= $i==$page?'active':'' ?>" href="?cat_id=<?= $cat_id ?>&category=<?= urlencode($cat_name) ?>&page=<?= $i ?>"><?= $i ?></a>
          <?php endfor; ?>
        </div>
        <a class="ghost" href="?cat_id=<?= $cat_id ?>&category=<?= urlencode($cat_name) ?>&page=<?= $next ?>">Next →</a>
      </div>
    <?php endif; ?>
  </div>

  <!-- SIDEBAR -->
  <aside>
    <!-- Search -->
    <div class="box">
      <h4>Search</h4>
      <form method="get" action="/search.php">
        <div class="search">
          <input name="search" placeholder="Find something good…" />
          <button name="submit" type="submit">Search</button>
        </div>
      </form>
    </div>

    <!-- Categories -->
    <div class="box">
      <h4>Categories</h4>
      <div class="chips">
        <?php foreach ($cats as $c): ?>
          <a class="chip" href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/category/<?= urlencode($c['cat_title']) ?>/<?= (int)$c['cat_id'] ?>"><?= h($c['cat_title']) ?></a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Popular -->
    <div class="box">
      <h4>Popular</h4>
      <div class="popular">
        <?php foreach ($popular as $pp): 
          $post_slug = slugify($pp['post_title']);
          $post_id = $pp["post_id"];

          $pimg = $pp['post_image'] && file_exists("images/".$pp['post_image']) ? h($pp['post_image']) : "y9DpT.jpg";
        ?>
          <a href="<?= BASE_URL ?>/<?= urlencode($post_slug) ?>-<?= $post_id ?>">
            <img src="<?= defined('BASE_URL') ? BASE_URL : '' ?>/images/<?= $pimg ?>" alt="">
            <div>
              <div style="font-weight:700;line-height:1.25;margin-bottom:4px"><?= h($pp['post_title']) ?></div>
              <div style="color:#AEB6C7;font-size:12px"><?= h(date('M Y', strtotime($pp['post_date']))) ?></div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </aside>
</section>


<!-- FOOTER -->
<footer>
  <div class="wrap foot">
    <div style="color:#B8C2D2;font-size:14px">© <?= date('Y') ?> Niterria — Built with care.</div>
  </div>
</footer>

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

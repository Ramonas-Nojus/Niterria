<?php
// === Keep your real backend includes ===
include "settings-core-7189.php";
include "includes/db.php";
include "admin/functions.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// --- Config ---
$per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; if ($page < 1) $page = 1;

// --- Base WHERE depending on role ---
$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
$where = $is_admin ? '' : "WHERE post_status='published'";

// --- Editor's Picks (newest 3) ---
$picks_sql = "SELECT post_id, post_title, post_date, post_image, post_subtitle FROM posts $where ORDER BY post_id DESC LIMIT 3";
$picks_rs = mysqli_query($connection, $picks_sql);
$picks = $picks_rs ? mysqli_fetch_all($picks_rs, MYSQLI_ASSOC) : [];
$picks_count = count($picks);

// --- Total count for main list (exclude picks globally so no duplicates on any page) ---
$count_sql = "SELECT COUNT(*) AS c FROM posts $where";
$count_rs = mysqli_query($connection, $count_sql);
$total_all = $count_rs ? (int)mysqli_fetch_assoc($count_rs)['c'] : 0;
$total_list = max(0, $total_all - $picks_count);
$total_pages = max(1, (int)ceil($total_list / $per_page));

// Clamp page to total pages
if ($page > $total_pages) { $page = $total_pages; }

// --- Main posts (skip picks across all pages) ---
$offset = $picks_count + ($page - 1) * $per_page; // always skip first N picks
$posts_sql = "SELECT post_id, post_title, post_date, post_image, post_subtitle FROM posts $where ORDER BY post_id DESC LIMIT $offset, $per_page";
$posts_rs = mysqli_query($connection, $posts_sql);
$posts = $posts_rs ? mysqli_fetch_all($posts_rs, MYSQLI_ASSOC) : [];

// --- Categories ---
$cats = [];
$cats_rs = mysqli_query($connection, "SELECT cat_id, cat_title FROM categories ORDER BY cat_title ASC");
if ($cats_rs) { $cats = mysqli_fetch_all($cats_rs, MYSQLI_ASSOC); }

// --- Popular (fallback to latest) ---
$popular = [];
$pop_rs = mysqli_query($connection, "SELECT post_id, post_title, post_date, post_image FROM posts $where ORDER BY post_id DESC LIMIT 5");
if ($pop_rs) { $popular = mysqli_fetch_all($pop_rs, MYSQLI_ASSOC); }

function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Niterria — Tech Journal</title>
  <meta name="description" content="Niterria — premium tech journal. Modern dark UI, glass cards, smooth glow/zoom effects." />
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
    body{margin:0; color:var(--fg); font-family:Manrope,system-ui,Segoe UI,Roboto,Arial,sans-serif; background:
      radial-gradient(70% 90% at 10% -10%, color-mix(in oklab, var(--p) 35%, transparent), transparent 60%),
      radial-gradient(60% 60% at 90% 0%, color-mix(in oklab, var(--s) 40%, transparent), transparent 60%),
      linear-gradient(180deg,#0B0A15 0%, var(--bg) 60%);
      background-attachment:fixed;
    }
    a{color:var(--link); text-decoration:none}

    /* LAYOUT */
    .wrap{max-width:1260px; margin:0 auto; padding:0 22px}

    /* NAV */
    .nav{position:sticky; top:0; z-index:30; backdrop-filter:saturate(180%) blur(12px); background:color-mix(in oklab, var(--p) 12%, transparent); border-bottom:1px solid var(--stroke)}
    .nav-in{display:flex; align-items:center; justify-content:space-between; padding:14px 0}
    .brand{display:flex; align-items:center; gap:12px; color:var(--fg)}
    .badge{width:42px; height:42px; border-radius:14px; display:grid; place-items:center; border:1px solid var(--stroke); background:linear-gradient(135deg,var(--p),var(--s)); box-shadow:var(--shadow)}
    .bt small{display:block; letter-spacing:.18em; color:#C9D2E1; opacity:.85; text-transform:uppercase; font-size:11px; line-height:1}
    .bt b{display:block; font-weight:800; line-height:1.1; color:var(--fg)}
    .nav-links{display:flex; gap:18px; align-items:center}
    .ghost{border:1px solid var(--stroke); background:var(--glass); padding:10px 14px; border-radius:14px}

    /* HERO */
    .hero{padding:70px 0 30px}
    .hero-head{display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:16px}
    .eyebrow{display:inline-flex; align-items:center; gap:8px; color:#97F8EA; font-size:12px; letter-spacing:.2em; text-transform:uppercase}
    .dot{width:7px; height:7px; border-radius:50%; background:#31E0C2}
    .hero-title{font-family:'Playfair Display', serif; font-size:42px; line-height:1.12; margin:6px 0 0}
    .fade{background:linear-gradient(135deg,var(--p),var(--s)); -webkit-background-clip:text; background-clip:text; color:transparent}

    .feat-row{display:grid; grid-template-columns:repeat(12,1fr); gap:16px}
    .tile{grid-column: span 4; position:relative; border:1px solid var(--stroke); background:var(--glass); border-radius:22px; overflow:hidden; transition:.25s transform,.25s box-shadow,.25s background; box-shadow:0 0 0 rgba(0,0,0,0)}
    .tile:hover{transform:translateY(-4px); box-shadow:0 18px 60px -20px rgba(83,41,237,.55); background:var(--glass2)}
    .img-wrap{position:relative; overflow:hidden}
    .img-wrap img{width:100%; height:100%; object-fit:cover; aspect-ratio:16/10; transform:scale(1); transition:transform .6s ease, filter .6s ease}
    .img-wrap::after{content:""; position:absolute; inset:0; background:linear-gradient(180deg, rgba(0,0,0,0) 40%, rgba(0,0,0,.35)); opacity:.0; transition:opacity .4s ease}
    .tile:hover .img-wrap img{transform:scale(1.04); filter:saturate(110%)}
    .tile:hover .img-wrap::after{opacity:1}
    .tile-in{padding:14px}
    .kicker{font-size:12px; color:#C9D2E1}
    .tile h3{margin:6px 0 0; font-size:18px}
    @media(max-width:1000px){ .feat-row{grid-template-columns:1fr} .tile{grid-column:span 12} }

    /* MAIN GRID */
    .grid{display:grid; grid-template-columns: minmax(0, 2fr) minmax(300px, 1fr); gap:24px}
    @media(max-width: 980px){ .grid{ grid-template-columns:1fr; } }

    /* POSTS */
    .list{display:grid; grid-template-columns:1fr; gap:16px}
    .card{border:1px solid var(--stroke); background:var(--glass); border-radius:22px; overflow:hidden}
    .post{display:grid; grid-template-columns:.95fr 1.05fr}
    .post .img-wrap img{aspect-ratio:4/3}
    .post .b{padding:16px}
    .post .meta{color:#B6C0CF; font-size:12px}
    .post h2{margin:8px 0 8px; font-size:22px}
    .post p{color:var(--muted)}
    @media(max-width:900px){ .post{grid-template-columns:1fr} }

    .pager{display:flex; justify-content:space-between; align-items:center; border:1px solid var(--stroke); background:var(--glass); border-radius:16px; padding:8px; margin-top:10px}
    .pages{display:flex; gap:8px}
    .page{min-width:38px; height:38px; display:grid; place-items:center; border-radius:12px; border:1px solid var(--stroke); background:var(--glass)}
    .page.active{background:linear-gradient(135deg,var(--p),var(--s)); font-weight:800}

    /* SIDEBAR (sticky) */
    aside{position: sticky; top: 92px; height: max-content}
    .box{border:1px solid var(--stroke); background:var(--glass); border-radius:22px; padding:16px; margin-bottom:16px}
    .box h4{margin:4px 0 10px; font-size:12px; letter-spacing:.18em; text-transform:uppercase; color:#D8DFF0}
    .search{display:flex;gap:8px}
    .search input{flex:1;border-radius:14px;padding:12px 14px;background:var(--glass2);border:1px solid var(--stroke);color:var(--fg)}
    .search button{border:1px solid var(--stroke);background:linear-gradient(135deg,var(--p),var(--s));color:white;border-radius:14px;padding:12px 14px;cursor:pointer}
    .chips{display:flex; flex-wrap:wrap; gap:8px}
    .chip{border:1px solid var(--stroke); background:var(--glass2); padding:8px 12px; border-radius:12px; font-size:14px}
    .popular{display:grid; gap:10px}
    .popular a{display:flex; gap:10px; color:var(--fg)}
    .popular img{width:110px; height:78px; object-fit:cover; border-radius:10px; border:1px solid var(--stroke)}

    /* FOOTER */
    footer{border-top:1px solid var(--stroke); background:color-mix(in oklab, var(--s) 10%, transparent)}
    .foot{display:grid; grid-template-columns:1fr auto; gap:12px; padding:22px 0}
    @media(max-width:800px){.foot{grid-template-columns:1fr}}
    .icons{display:flex; gap:12px}
    .icon{width:42px; height:42px; border-radius:14px; background:var(--glass); border:1px solid var(--stroke); display:grid; place-items:center}

    /* BACK TO TOP */
    .to-top{position:fixed; right:18px; bottom:18px; width:48px; height:48px; display:grid; place-items:center; border-radius:50%; border:1px solid var(--stroke); background:linear-gradient(135deg,var(--p),var(--s)); color:white; box-shadow:var(--shadow); opacity:0; pointer-events:none; transform:translateY(10px); transition:.25s}
    .to-top.show{opacity:1; pointer-events:auto; transform:translateY(0)}
  </style>
</head>
<body>
  <!-- NAV -->
<div class="nav">
    <div class="wrap nav-in">
        <!-- Brand -->
        <a class="brand" href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>">
        <div class="badge" aria-hidden="true" style="background:none; border:none; box-shadow:none; padding:0;">
            <img src="<?= defined('BASE_URL') ? BASE_URL : '' ?>/images/WhiteLogo.png"
                alt="Niterria logo"
                style="height:42px; width:auto; display:block;">
        </div>
        <div class="bt">
            <small>Niterria</small><b>Tech Journal</b>
        </div>
        </a>

        <!-- Navigation Links -->
        <div class="nav-links">
        <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>">Home</a>
        <a href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/about">About</a>

        <?php if(isLoggedIn()): ?>
            <a href="<?= BASE_URL ?>/includes/logout.php">Logout</a>
            <a href="<?= BASE_URL ?>/profile">Profile</a>

            <?php if(is_admin()): ?>
                <a href="<?= BASE_URL ?>/admin">Admin</a>
            <?php endif; ?>

        <?php else: ?>
            <a href="<?= BASE_URL ?>/registration">Register</a>
            <a href="<?= BASE_URL ?>/login" class="ghost">Login</a>
        <?php endif; ?>

        </div>
    </div>
    </div>

  <!-- HERO: Editor's Picks (3 newest) -->
  <section class="hero">
    <div class="wrap">
      <div class="hero-head">
        <div>
          <span class="eyebrow"><span class="dot"></span> Editor’s Picks</span>
          <div class="hero-title">Modern systems, <span class="fade">timeless design</span>.</div>
        </div>
      </div>
      <div class="feat-row">
        <?php if ($picks_count === 0): ?>
          <div class="tile" style="grid-column:span 12; padding:16px">No featured posts yet.</div>
        <?php else: ?>
          <?php foreach ($picks as $p): 
            $post_slug = slugify($p['post_title']);
            $post_id = $p["post_id"];


            ?>
            <a class="tile" href="<?= BASE_URL ?>/<?= urlencode($post_slug) ?>-<?= $post_id ?>">
              <div class="img-wrap">
                <img src="<?= defined('BASE_URL') ? BASE_URL : '' ?>/images/<?= $p['post_image'] ? h($p['post_image']) : 'y9DpT.jpg' ?>" alt="">
              </div>
              <div class="tile-in">
                <div class="kicker"><?= h(date('M j, Y', strtotime($p['post_date']))) ?></div>
                <h3><?= h($p['post_title']) ?></h3>
              </div>
            </a>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- MAIN GRID: Posts + Sticky Sidebar -->
  <section id="journal" class="wrap grid" style="padding: 10px 0 60px">
    <!-- POSTS -->
    <div>
      <div class="list">
        <?php if ($total_list < 1): ?>
          <div class="card" style="padding:24px; text-align:center">No posts available</div>
        <?php else: ?>
          <?php foreach ($posts as $row): 
            
            $post_slug = slugify($row['post_title']);
            $post_id = $row["post_id"];
            
            ?>
            <article class="card post">
              <a href="<?= BASE_URL ?>/<?= urlencode($post_slug) ?>-<?= $post_id ?>">
                <div class="img-wrap">
                  <img src="<?= defined('BASE_URL') ? BASE_URL : '' ?>/images/<?= $row['post_image'] ? h($row['post_image']) : 'y9DpT.jpg' ?>" alt="">
                </div>
              </a>
              <div class="b">
                <div class="meta"><?= h(date('M j, Y', strtotime($row['post_date']))) ?></div>
                <h2><a href="<?= BASE_URL ?>/<?= urlencode($post_slug) ?>-<?= $post_id ?>"><?= h($row['post_title']) ?></a></h2>
                <p><?= h($row['post_subtitle']) ?></p>
                <div style="margin-top:10px"><a class="ghost" href="<?= BASE_URL ?>/<?= urlencode($post_slug) ?>-<?= $post_id ?>"  >Read More →</a></div>
              </div>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Pagination -->
      <?php if ($total_pages > 1): ?>
      <div class="pager" style="margin-top:16px">
        <?php $prev = max(1, $page-1); $next = min($total_pages, $page+1); ?>
        <a class="ghost" href="?page=<?= $prev ?>">← Prev</a>
        <div class="pages">
          <?php for($i=1; $i <= $total_pages; $i++): ?>
            <a class="page <?= $i==$page ? 'active' : '' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
          <?php endfor; ?>
        </div>
        <a class="ghost" href="?page=<?= $next ?>">Next →</a>
      </div>
      <?php endif; ?>
    </div>

    <!-- SIDEBAR (sticky) -->
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
            ?>
            <a href="<?= BASE_URL ?>/<?= urlencode($post_slug) ?>-<?= $post_id ?>">
              <img src="<?= defined('BASE_URL') ? BASE_URL : '' ?>/images/<?= $pp['post_image'] ? h($pp['post_image']) : 'y9DpT.jpg' ?>" alt="thumb">
              <div>
                <div style="font-weight:700; line-height:1.25; margin-bottom:4px; color:var(--fg)"><?= h($pp['post_title']) ?></div>
                <div style="color:#AEB6C7; font-size:12px;">
                  <?= h(date('M Y', strtotime($pp['post_date']))) ?>
                </div>
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
      <div style="color:#B8C2D2; font-size:14px">© <?= date('Y') ?> Niterria — Built with care.</div>
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

  <!-- Back to top button -->
  <button id="toTop" class="to-top" aria-label="Back to top" title="Back to top">↑</button>
  <script>
    const toTop = document.getElementById('toTop');
    const showAt = 400;
    window.addEventListener('scroll', () => {
      if (window.scrollY > showAt) toTop.classList.add('show'); else toTop.classList.remove('show');
    });
    toTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  </script>
</body>
</html>

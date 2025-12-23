<?php
// === Required includes ===
include "settings-core-7189.php";
include "includes/db.php";
include "includes/class.autoload.php";
include "admin/functions.php";


session_start();

function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

// --- Sidebar data (Categories / Popular) ---
$cats = [];
if ($rs = mysqli_query($connection, "SELECT cat_id, cat_title FROM categories ORDER BY cat_title ASC")) {
  $cats = mysqli_fetch_all($rs, MYSQLI_ASSOC);
}
$popular = [];
$where = "WHERE post_status='published'";
if ($rs = mysqli_query($connection, "SELECT post_id, post_title, post_date, post_image FROM posts $where ORDER BY post_views_count DESC LIMIT 5")) {
  $popular = mysqli_fetch_all($rs, MYSQLI_ASSOC);
}

// --- Search ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$results = [];
$ran = false;

if ($search !== '') {
  if (class_exists('search/Search')) {
    $S = new search\Search();
    $results = $S->search($search) ?: [];
    $ran = true;
  } else {
    $like = "%{$search}%";
    $sql = "SELECT post_id, post_title, post_date, post_image, post_subtitle
            FROM posts
            WHERE post_status='published'
              AND (post_title LIKE ? OR post_subtitle LIKE ? OR post_content LIKE ?)
            ORDER BY post_id DESC
            LIMIT 100";
    if ($stmt = mysqli_prepare($connection, $sql)) {
      mysqli_stmt_bind_param($stmt, "sss", $like, $like, $like);
      mysqli_stmt_execute($stmt);
      $res = mysqli_stmt_get_result($stmt);
      if ($res) $results = mysqli_fetch_all($res, MYSQLI_ASSOC);
      mysqli_stmt_close($stmt);
      $ran = true;
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Search — Niterria</title>
  <link rel="icon" href="<?= BASE_URL ?>/images/favicon.ico" sizes="any">
  <link rel="icon" type="images/png" href="<?= BASE_URL ?>//favicon-48.png" sizes="48x48">
  <link rel="apple-touch-icon" href="<?= BASE_URL ?>/images/apple-touch-icon.png">
  <meta name="description" content="Search Niterria — premium tech/luxury posts." />
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/search.css">
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


  <!-- HEADER -->
  <header class="wrap header">
    <?php if($search !== ''): ?>
      <h1>Search results for <b>&laquo;<?= h($search) ?>&raquo;</b></h1>
    <?php else: ?>
      <h1>Search</h1>
    <?php endif; ?>
  </header>

  <!-- GRID -->
  <section class="grid wrap">
    <!-- RESULTS -->
    <div>
      <?php if($ran && empty($results)): ?>
        <div class="empty">No results found.</div>
      <?php endif; ?>

      <div class="list">
        <?php foreach ($results as $row):
          $pid  = (int)$row['post_id'];
          $title = h($row['post_title'] ?? '');
          $date  = h($row['post_date'] ?? '');
          $sub   = h($row['post_subtitle'] ?? '');
          $img   = h(($row['post_image'] ?? '') ?: 'y9DpT.jpg');
          $post_slug = slugify($title);

        ?>
          <article class="card">
            
            <a href="<?= BASE_URL ?>/<?= urlencode($post_slug) ?>-<?= $pid ?>">
              <div class="img-wrap">
                <img src="<?= defined('BASE_URL') ? BASE_URL : '' ?>/images/<?= $img ?>" alt="">
              </div>
            </a>
            <div class="b">
              <div class="meta"><?= $date ?></div>
              <h2><a href="<?= BASE_URL ?>/<?= urlencode($post_slug) ?>-<?= $pid ?>"><?= $title ?></a></h2>
              <p><?= $sub ?></p>
              <div style="margin-top:10px"><a class="ghost" href="post.php?p_id=<?= $pid ?>">Read More →</a></div>
            </div>
          </article>
        <?php endforeach; ?>

        <?php if($search === ''): ?>
          <div class="empty">Use the sidebar search to find posts.</div>
        <?php endif; ?>
      </div>
    </div>

    <!-- SIDEBAR (sticky) -->
    <aside>
      <!-- Search -->
      <div class="box">
        <h4>Search</h4>
        <form method="get" action="<?= defined('BASE_URL') ? BASE_URL : '' ?>/search">
          <div class="search">
            <input name="search" placeholder="Find something good…" value="<?= h($search) ?>" />
            <button type="submit" name="submit" value="1">Search</button>
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

            ?>
            <a href="<?= BASE_URL ?>/<?= urlencode($post_slug) ?>-<?= $pp['post_id'] ?>">
              <img src="<?= defined('BASE_URL') ? BASE_URL : '' ?>/images/<?= $pp['post_image'] ? h($pp['post_image']) : 'y9DpT.jpg' ?>" alt="thumb">
              <div>
                <div style="font-weight:700; line-height:1.25; margin-bottom:4px; color:var(--fg)"><?= h($pp['post_title']) ?></div>
                <div style="color:#AEB6C7; font-size:12px;"><?= h(date('M Y', strtotime($pp['post_date']))) ?></div>
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

<script src="<?= BASE_URL ?>/js/cookies.js"></script>


</body>
</html>

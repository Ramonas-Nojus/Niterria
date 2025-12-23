<?php
// === Core includes ===
include "settings-core-7189.php";
include "includes/db.php";
include "admin/functions.php";

session_start();

// --- Role / cache headers (HTML edge-friendly) ---
$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
$is_logged = function_exists('isLoggedIn') ? isLoggedIn() : false;
if (!$is_admin && !$is_logged) {
  // Short-lived public HTML cache (for CDNs / proxies); browsers still revalidate
  header("Cache-Control: public, s-maxage=120, max-age=0, stale-while-revalidate=60");
}

// --- Config ---
$per_page = 5;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// --- WHERE by role ---
$where = $is_admin ? '' : "WHERE post_status='published'";

// --- Picks (3 newest) ---
$picks_sql = "SELECT post_id, post_title, post_date, post_image, post_subtitle FROM posts $where ORDER BY post_id DESC LIMIT 3";
$picks_rs = mysqli_query($connection, $picks_sql);
$picks = $picks_rs ? mysqli_fetch_all($picks_rs, MYSQLI_ASSOC) : [];
$picks_count = count($picks);

// --- Total counts ---
$count_sql = "SELECT COUNT(*) AS c FROM posts $where";
$count_rs = mysqli_query($connection, $count_sql);
$total_all = $count_rs ? (int)mysqli_fetch_assoc($count_rs)['c'] : 0;
$total_list = max(0, $total_all - $picks_count);
$total_pages = max(1, (int)ceil($total_list / $per_page));
if ($page > $total_pages) $page = $total_pages;

// --- Main posts (skip picks globally) ---
$offset = $picks_count + ($page - 1) * $per_page;
$posts_sql = "SELECT post_id, post_title, post_date, post_image, post_subtitle FROM posts $where ORDER BY post_id DESC LIMIT $offset, $per_page";
$posts_rs = mysqli_query($connection, $posts_sql);
$posts = $posts_rs ? mysqli_fetch_all($posts_rs, MYSQLI_ASSOC) : [];

// --- Categories ---
$cats = [];
$cats_rs = mysqli_query($connection, "SELECT cat_id, cat_title FROM categories ORDER BY cat_title ASC");
if ($cats_rs) $cats = mysqli_fetch_all($cats_rs, MYSQLI_ASSOC);

// --- Popular ---
$popular = [];
$pop_rs = mysqli_query($connection, "SELECT post_id, post_title, post_date, post_image FROM posts $where ORDER BY post_views_count DESC, post_id DESC LIMIT 5");
if ($pop_rs) $popular = mysqli_fetch_all($pop_rs, MYSQLI_ASSOC);

// --- Helpers ---
function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
function img_exists($p){ return $p && file_exists(__DIR__."/images/".$p); }
function pick_img($p){ return img_exists($p) ? $p : 'y9DpT.jpg'; }
// Look for AVIF or WebP siblings named same base
function alt_format($filename, $ext='avif'){
  if(!$filename) return null;
  $base = pathinfo($filename, PATHINFO_FILENAME);
  $candidate = $base . '.' . $ext;
  return file_exists(__DIR__."/images/".$candidate) ? $candidate : null;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta name="description" content="Niterria — a modern tech journal for builders. Reviews, trends, systems thinking." />
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Niterria — Tech Journal</title>
  <link rel="icon" href="<?= BASE_URL ?>/images/favicon.ico" sizes="any">
  <link rel="icon" type="image/png" href="<?= BASE_URL ?>/images/favicon-48.png" sizes="48x48">
  <link rel="apple-touch-icon" href="<?= BASE_URL ?>/images/apple-touch-icon.png">
  <link rel="canonical" href="<?= BASE_URL ?>" />
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/index.css">

  <!-- DNS + font preconnect -->
  <meta http-equiv="x-dns-prefetch-control" content="on">
  <link rel="dns-prefetch" href="https://fonts.googleapis.com">
  <link rel="dns-prefetch" href="https://fonts.gstatic.com">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <!-- Non-blocking Google Fonts CSS -->
  <link rel="preload"
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600;700;800&family=Playfair+Display:wght@600;700&display=swap"
        as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600;700;800&family=Playfair+Display:wght@600;700&display=swap">
  </noscript>

  <?php
  if ($picks_count > 0) {
    $first = $picks[0];
    $img = pick_img($first['post_image']);
    $avif = alt_format($img, 'avif');
    $webp = $avif ? null : alt_format($img, 'webp'); // prefer AVIF
    $lcp_file = $avif ?: ($webp ?: $img);
    $lcp_url = BASE_URL."/images/".h($lcp_file);
    ?>
    <link rel="preload" as="image"
      href="<?= $lcp_url ?>"
      imagesrcset="<?= BASE_URL ?>/images/<?= h($lcp_file) ?> 1280w,
                   <?= BASE_URL ?>/images/<?= h($lcp_file) ?> 960w,
                   <?= BASE_URL ?>/images/<?= h($lcp_file) ?> 640w"
      imagesizes="(max-width: 1000px) 100vw, 33vw"
      fetchpriority="high">
  <?php } ?>
</head>
<body>
  <!-- NAV -->
  <div class="nav">
    <div class="wrap nav-in">
      <a class="brand" href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>">
        <div class="badge" aria-hidden="true" style="background:none;border:none;box-shadow:none;padding:0;">
          <img src="<?= defined('BASE_URL') ? BASE_URL : '' ?>/images/WhiteLogo.png"
               alt="Niterria logo" width="42"
               loading="eager" decoding="async" fetchpriority="low">
        </div>
        <div class="bt"><small>Niterria</small><b>Tech Journal</b></div>
      </a>

      <button class="menu-toggle" aria-label="Menu" aria-expanded="false" aria-controls="primary-nav">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M3 6h18v2H3zM3 11h18v2H3zM3 16h18v2H3z"/>
        </svg>
      </button>

      <div id="primary-nav" class="nav-links" role="navigation">
        <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>">Home</a>
        <a href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/about">About</a>

        <?php if($is_logged): ?>
          <a href="<?= BASE_URL ?>/includes/logout.php">Logout</a>
          <a href="<?= BASE_URL ?>/profile">Profile</a>
          <?php if($is_admin): ?><a href="<?= BASE_URL ?>/admin">Admin</a><?php endif; ?>
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
      <div class="hero-head">
        <div>
          <span class="eyebrow"><span class="dot"></span> Editor’s Picks</span>
          <h1 class="hero-title">Exploring modern <span class="fade">technology</span>.</h1>
          <p class="intro-snippet">Discover reviews, trends, and analysis on modern tech, software tools, and digital systems.</p>
        </div>
      </div>

      <div class="feat-row">
        <?php if ($picks_count === 0): ?>
          <div class="tile" style="grid-column:span 12; padding:16px">No featured posts yet.</div>
        <?php else: ?>
          <?php foreach ($picks as $i => $p):
            $post_slug = slugify($p['post_title']);
            $post_id = $p["post_id"];
            $img = pick_img($p['post_image']);
            $avif = alt_format($img, 'avif');
            $webp = $avif ? null : alt_format($img, 'webp');
            $is_first = ($i === 0);
          ?>
            <a class="tile" href="<?= BASE_URL ?>/<?= urlencode($post_slug) ?>-<?= $post_id ?>">
              <div class="img-wrap">
                <picture>
                  <?php if ($avif): ?>
                    <source srcset="<?= BASE_URL ?>/images/<?= h($avif) ?> 1280w,
                                    <?= BASE_URL ?>/images/<?= h($avif) ?> 960w,
                                    <?= BASE_URL ?>/images/<?= h($avif) ?> 640w" type="image/avif">
                  <?php elseif ($webp): ?>
                    <source srcset="<?= BASE_URL ?>/images/<?= h($webp) ?> 1280w,
                                    <?= BASE_URL ?>/images/<?= h($webp) ?> 960w,
                                    <?= BASE_URL ?>/images/<?= h($webp) ?> 640w" type="image/webp">
                  <?php endif; ?>
                  <img
                    src="<?= BASE_URL ?>/images/<?= h($img) ?>"
                    alt="<?= h($p['post_title']) ?>"
                    width="1280" height="800"
                    <?php if ($is_first): ?>fetchpriority="high" loading="eager"<?php else: ?>loading="lazy"<?php endif; ?>
                    decoding="async"
                    srcset="<?= BASE_URL ?>/images/<?= h($img) ?> 1280w,
                            <?= BASE_URL ?>/images/<?= h($img) ?> 960w,
                            <?= BASE_URL ?>/images/<?= h($img) ?> 640w"
                    sizes="(max-width: 1000px) 100vw, 33vw">
                </picture>
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

  <!-- MAIN GRID -->
  <section id="journal" class="wrap grid">
    <div>
      <div class="list">
        <?php if ($total_list < 1): ?>
          <div class="card" style="padding:24px;text-align:center">No posts found in this category.</div>
        <?php else: ?>
          <?php foreach ($posts as $row):
            $post_slug = slugify($row['post_title']);
            $post_id = $row["post_id"];
            $img = pick_img($row['post_image']);
            $avif = alt_format($img, 'avif');
            $webp = $avif ? null : alt_format($img, 'webp');
          ?>
            <article class="card post">
              <a href="<?= BASE_URL ?>/<?= urlencode($post_slug) ?>-<?= $post_id ?>">
                <div class="img-wrap">
                  <picture>
                    <?php if ($avif): ?>
                      <source srcset="<?= BASE_URL ?>/images/<?= h($avif) ?> 640w,
                                      <?= BASE_URL ?>/images/<?= h($avif) ?> 480w" type="image/avif">
                    <?php elseif ($webp): ?>
                      <source srcset="<?= BASE_URL ?>/images/<?= h($webp) ?> 640w,
                                      <?= BASE_URL ?>/images/<?= h($webp) ?> 480w" type="image/webp">
                    <?php endif; ?>
                    <img
                      src="<?= BASE_URL ?>/images/<?= h($img) ?>"
                      alt="<?= h($row['post_title']) ?>"
                      width="640" height="400"
                      loading="lazy" decoding="async"
                      srcset="<?= BASE_URL ?>/images/<?= h($img) ?> 640w,
                              <?= BASE_URL ?>/images/<?= h($img) ?> 480w"
                      sizes="(max-width: 900px) 100vw, 50vw">
                  </picture>
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

    <aside>
      <div class="box">
        <h4>Search</h4>
        <form method="get" action="/search.php">
          <div class="search">
            <input name="search" placeholder="Find something good…" />
            <button name="submit" type="submit">Search</button>
          </div>
        </form>
      </div>

      <div class="box">
        <h4>Categories</h4>
        <div class="chips">
          <?php foreach ($cats as $c): ?>
            <a class="chip" href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/category/<?= urlencode($c['cat_title']) ?>/<?= (int)$c['cat_id'] ?>"><?= h($c['cat_title']) ?></a>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="box">
        <h4>Popular</h4>
        <div class="popular">
          <?php foreach ($popular as $pp):
            $post_slug = slugify($pp['post_title']);
            $post_id = $pp["post_id"];
            $pimg = pick_img($pp['post_image']);
            $pavif = alt_format($pimg, 'avif');
            $pwebp = $pavif ? null : alt_format($pimg, 'webp');
          ?>
            <a href="<?= BASE_URL ?>/<?= urlencode($post_slug) ?>-<?= $post_id ?>">
              <picture>
                <?php if ($pavif): ?>
                  <source srcset="<?= BASE_URL ?>/images/<?= h($pavif) ?>" type="image/avif">
                <?php elseif ($pwebp): ?>
                  <source srcset="<?= BASE_URL ?>/images/<?= h($pwebp) ?>" type="image/webp">
                <?php endif; ?>
                <img
                  src="<?= BASE_URL ?>/images/<?= h($pimg) ?>"
                  alt="<?= h($pp['post_title']) ?>"
                  width="110" height="78"
                  loading="lazy" decoding="async">
              </picture>
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

  <!-- Back to top -->
  <button id="toTop" class="to-top" aria-label="Back to top" title="Back to top">↑</button>
  <script>
    const toTop = document.getElementById('toTop');
    const showAt = 400;
    addEventListener('scroll', () => {
      if (scrollY > showAt) toTop.classList.add('show'); else toTop.classList.remove('show');
    }, {passive:true});
    toTop.addEventListener('click', () => scrollTo({ top: 0, behavior: 'smooth' }));
  </script>



  <!-- Consent & ultra-deferred 3P scripts -->
  <div id="cookie-banner" style="
    position:fixed; bottom:20px; left:50%; transform:translateX(-50%);
    background:rgba(255,255,255,.08); backdrop-filter:blur(12px);
    border:1px solid rgba(255,255,255,.15); color:#E9EEF6;
    border-radius:18px; padding:18px 24px; max-width:480px;
    font-size:14px; line-height:1.5; box-shadow:0 20px 60px rgba(0,0,0,.4);
    display:none; z-index:2000; text-align:center;">
    <p style="margin:0 0 12px;">We use cookies for analytics and to improve your experience.</p>
    <div style="display:flex;justify-content:center;gap:10px;flex-wrap:wrap;">
      <button id="acceptCookies" style="border:none; background:linear-gradient(135deg,#260ED0,#5329ED); color:#fff; font-weight:600; border-radius:12px; padding:10px 20px; cursor:pointer;">Accept</button>
      <a href='/privacy' style="color:#A8B1C0;text-decoration:underline;font-size:13px;">Learn more</a>
    </div>
  </div>
  <script src="./js/index.js"></script>
  <script src="./js/cookies.js"></script>

</body>
</html>

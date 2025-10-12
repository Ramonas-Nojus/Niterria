<?php

// Core includes (backend only)
include "settings-core-7189.php";
include "includes/db.php";
include "admin/functions.php";
include "includes/class.autoload.php"; // Likes, Comments

session_start();

function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

// Like / Unlike actions via pretty URLs (?p_id=ID&like=USER or &unlike=USER)

// --- Load post ---
if(!isset($_GET['p_id'])){ header("Location: ". BASE_URL); exit; }
$the_post_id = (int)$_GET['p_id'];

// Count view
$update_statement = mysqli_prepare($connection, "UPDATE posts SET post_views_count = post_views_count + 1 WHERE post_id = ?");
mysqli_stmt_bind_param($update_statement, "i", $the_post_id);
mysqli_stmt_execute($update_statement);

// Admin can view drafts, others only published
if(isset($_SESSION['user_id']) && is_admin($_SESSION['username'])){
  $stmt = mysqli_prepare($connection, "SELECT post_title, post_date, post_image, post_content, post_subtitle FROM posts WHERE post_id = ?");
  mysqli_stmt_bind_param($stmt, "i", $the_post_id);
} else {
  $stmt = mysqli_prepare($connection, "SELECT post_title, post_date, post_image, post_content, post_subtitle FROM posts WHERE post_id = ? AND post_status = ?");
  $published = 'published';
  mysqli_stmt_bind_param($stmt, "is", $the_post_id, $published);
}
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $post_title, $post_date, $post_image, $post_content, $post_subtitle);
mysqli_stmt_store_result($stmt);
if(mysqli_stmt_num_rows($stmt) != 1){ header("Location: ./errors/404.php"); exit; }
mysqli_stmt_fetch($stmt);

$post_slug = slugify($post_title);

// --- Sidebar data (search, categories, popular) ---
$cats = [];
$cats_rs = mysqli_query($connection, "SELECT cat_id, cat_title FROM categories ORDER BY cat_title ASC");
if($cats_rs){ $cats = mysqli_fetch_all($cats_rs, MYSQLI_ASSOC); }
$popular = [];
$pop_rs = mysqli_query($connection, "SELECT post_id, post_title, post_date, post_image FROM posts WHERE post_status='published' ORDER BY post_id DESC LIMIT 5");
if($pop_rs){ $popular = mysqli_fetch_all($pop_rs, MYSQLI_ASSOC); }

// --- Comments handlers ---
$comment = new Comments();
if(isset($_POST['create_comment'])){
  if(!isLoggedIn()){ header("Location: ./login"); exit; }
  $comment_author_id = (int)($_SESSION['user_id'] ?? 0);
  $comment_email = $_SESSION['user_email'] ?? '';
  $comment_content = trim($_POST['comment_content'] ?? '');
  if(!empty($comment_content)){
    $comment->setCommentsPosts($the_post_id,$comment_author_id,$comment_email,$comment_content);
    header("Location: ". BASE_URL."/".urldecode($post_slug)."-".$the_post_id); exit;
  }
}

// Delete
if(isset($_GET['delete_comment'])){
  if(isLoggedIn()){
    $delete_comment_id = (int)$_GET['delete_comment'];
    $comment->deleteCommentsPosts($delete_comment_id);
  }
  header("Location: ". BASE_URL."/".urldecode($post_slug)."-".$the_post_id); exit;
}

// Edit (inline)
$edit = false; $edit_id = 0;
if(isset($_GET['edit'])){
  $edit = true; $edit_id = (int)$_GET['edit'];
  if(isset($_POST['edit_comment'])){
    $new_content = trim($_POST['comment_content'] ?? '');
    if(!empty($new_content)){
      $comment->editCommentsPosts($edit_id,$new_content);
    }
    header("Location: ". BASE_URL."/".urldecode($post_slug)."-".$the_post_id); exit;
  }
}

$Likes = new Likes();
if(isset($_GET['like'])){
  $post_id = (int)($_GET['p_id'] ?? 0); $user_id = (int)$_GET['like'];
  if($post_id && $user_id){ $Likes->setLikesPost($post_id,$user_id); }


  header("Location: ". BASE_URL."/".urldecode($post_slug)."-".$post_id); exit;
}
if(isset($_GET['unlike'])){
  $post_id = (int)($_GET['p_id'] ?? 0); $user_id = (int)$_GET['unlike'];
  if($post_id && $user_id){ $Likes->unlikePost($post_id,$user_id); }
  header("Location: ". BASE_URL."/".urldecode($post_slug)."-".$post_id); exit;
}


// Fetch comments
$getComments = new Comments();
$comments = $getComments->getCommetsPosts($the_post_id);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= h($post_title) ?> — Niterria</title>
  <meta name="description" content="<?= h($post_subtitle) ?>" />
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
  <style>
    :root{ --bg:#0A0D14; --fg:#E9EEF6; --muted:#A8B1C0; --link:#DDE3F2; --glass:rgba(255,255,255,.06); --glass2:rgba(255,255,255,.10); --stroke:rgba(255,255,255,.12); --p:#260ED0; --s:#5329ED; --t:#00D5C9; --r:22px; --shadow:0 28px 80px -20px rgba(83,41,237,.45); }
    *{box-sizing:border-box}
    body{margin:0; color:var(--fg); font-family:Manrope,system-ui,Segoe UI,Roboto,Arial,sans-serif; background:radial-gradient(70% 90% at 10% -10%, color-mix(in oklab, var(--p) 35%, transparent), transparent 60%), radial-gradient(60% 60% at 90% 0%, color-mix(in oklab, var(--s) 40%, transparent), transparent 60%), linear-gradient(180deg,#0B0A15 0%, var(--bg) 60%); background-attachment:fixed;}
    a{color:var(--link); text-decoration:none}
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

    /* HERO POST */
    .hero{padding:44px 0 18px}
    .post-hero{display:grid; grid-template-columns:1fr; gap:16px}
    .headline{font-family:'Playfair Display', serif; font-size:44px; line-height:1.08; margin:0}
    .sub{color:var(--muted); font-size:18px}
    .meta{color:#B6C0CF; font-size:13px}
    .hero-img{border:1px solid var(--stroke); background:var(--glass); border-radius:22px; overflow:hidden}
    .hero-img .img-wrap{position:relative}
    .hero-img img{width:100%; height:auto; display:block; aspect-ratio:16/8; object-fit:cover; transition:transform .6s ease}
    .hero-img:hover img{transform:scale(1.02)}

    /* LAYOUT GRID */
    .grid{display:grid; grid-template-columns:minmax(0,2fr) minmax(320px,1fr); gap:24px}
    @media(max-width:980px){ .grid{grid-template-columns:1fr} }

    /* ARTICLE */
    .article{border:1px solid var(--stroke); background:var(--glass); border-radius:22px; padding:22px; line-height:1.7; font-size:18px}
    .disclosure{font-size:13px; color:#b9c2d1; text-align:center; margin-bottom:10px}
    .article h2, .article h3{font-family:'Playfair Display', serif}

    /* LIKE */
    .like-row{display:flex; align-items:center; gap:12px; margin:14px 0 6px}
    .heart{display:inline-grid; place-items:center; width:44px; height:44px; border-radius:50%; border:1px solid var(--stroke); background:var(--glass); transition:transform .15s ease, box-shadow .25s}
    .heart:hover{transform:translateY(-2px)}
    .heart.liked{background:linear-gradient(135deg,#f43f5e,#fb7185); box-shadow:0 10px 30px -10px rgba(244,63,94,.7)}
    .heart svg{display:block}

    /* COMMENTS */
    .comments{margin-top:18px}
    .c-form{border:1px solid var(--stroke); background:var(--glass); border-radius:16px; padding:16px}
    textarea{width:100%; border-radius:12px; padding:12px; background:var(--glass2); border:1px solid var(--stroke); color:var(--fg)}
    .c-list{display:grid; gap:12px; margin-top:16px}
    .c-item{display:grid; grid-template-columns:auto 1fr; gap:12px; border:1px solid var(--stroke); background:var(--glass); border-radius:16px; padding:12px}
    .avatar{width:52px; height:52px; border-radius:50%; object-fit:cover; border:1px solid var(--stroke)}
    .c-head{display:flex; align-items:center; justify-content:space-between}
    .c-name{font-weight:700}
    .c-date{color:#AEB6C7; font-size:12px}
    .c-actions a{margin-left:10px}
    .edit-inp{width:100%; border-radius:10px; padding:10px; background:var(--glass2); border:1px solid var(--stroke); color:var(--fg)}

    /* SIDEBAR */
    aside{position:sticky; top:92px; height:max-content}
    .box{border:1px solid var(--stroke); background:var(--glass); border-radius:22px; padding:16px; margin-bottom:16px}
    .box h4{margin:4px 0 10px; font-size:12px; letter-spacing:.18em; text-transform:uppercase; color:#D8DFF0}
    .search{position:relative}
    .search input{width:100%; border-radius:14px; padding:12px 44px 12px 12px; background:var(--glass2); border:1px solid var(--stroke); color:var(--fg)}
    .search svg{position:absolute; right:12px; top:50%; transform:translateY(-50%); opacity:.85}
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


    .btn-glass{
  padding:10px 18px;
  border:none;
  border-radius:14px;
  font-weight:600;
  font-family:Manrope,system-ui,Segoe UI,Roboto,Arial,sans-serif;
  cursor:pointer;
  color:var(--fg);
  background:linear-gradient(135deg,rgba(255,255,255,.08),rgba(255,255,255,.04));
  backdrop-filter:blur(10px) saturate(180%);
  transition:all .25s ease;
  box-shadow:0 0 0 1px var(--stroke) inset;
}
.btn-glass:hover{
  transform:translateY(-2px);
  box-shadow:0 10px 25px -10px rgba(83,41,237,.45),0 0 0 1px var(--p) inset;
}
.btn-glass.cancel:hover{
  box-shadow:0 10px 25px -10px rgba(255,80,80,.45),0 0 0 1px #ff5050 inset;
}
.btn-glass.confirm{
  background:linear-gradient(135deg,var(--p),var(--s));
  color:#fff;
}
.btn-glass.confirm:hover{
  box-shadow:0 10px 30px -10px rgba(83,41,237,.6);
  transform:translateY(-2px) scale(1.02);
}
    .search{display:flex;gap:8px}
    .search input{flex:1;border-radius:14px;padding:12px 14px;background:var(--glass2);border:1px solid var(--stroke);color:var(--fg)}
    .search button{border:1px solid var(--stroke);background:linear-gradient(135deg,var(--p),var(--s));color:white;border-radius:14px;padding:12px 14px;cursor:pointer}
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

  <!-- HERO POST -->
  <section class="hero">
    <div class="wrap post-hero">
      <div>
        <p class="disclosure">We independently review everything we recommend. If you buy through our links, we may earn a commission.</p>
        <h1 class="headline"><?= h($post_title) ?></h1>
        <div class="meta"><?= h(date('M j, Y', strtotime($post_date))) ?></div>
      </div>
      <div class="hero-img">
        <div class="img-wrap">
          <img src="<?= (defined('BASE_URL') ? BASE_URL : '') ?>/images/<?= $post_image ? h($post_image) : 'y9DpT.jpg' ?>" alt="">
        </div>
      </div>
    </div>
  </section>

  <!-- GRID -->
  <section class="wrap grid" style="padding: 10px 0 60px">
    <!-- ARTICLE LEFT -->
    <article class="article">
      <?php if(!empty($post_subtitle)): ?><h2 style="margin-top:0"><?= h($post_subtitle) ?></h2><?php endif; ?>
      <div class="content"><?= stripslashes($post_content) ?></div>

      <!-- LIKE BUTTON ROW -->
      <div class="like-row">
  <?php if(isLoggedIn()): ?>
    <button class="heart <?= UserLikedPost($the_post_id) ? 'liked' : '' ?>" 
            data-post="<?= $the_post_id ?>" 
            data-liked="<?= UserLikedPost($the_post_id) ? '1' : '0' ?>">
      ❤️
    </button>
    <span class="like-status"><?= UserLikedPost($the_post_id) ? 'Liked' : 'Like this' ?></span>
  <?php else: ?>
    <span><a href="<?= BASE_URL ?>/login">Log in</a> to like</span>
  <?php endif; ?>
</div>


      <!-- COMMENTS -->
      <div class="comments">
        <h3 style="margin:10px 0 8px">Comments</h3>
        <div class="c-form">
          <?php if(isLoggedIn()): ?>
            <form action="" method="post">
              <textarea name="comment_content" rows="3" placeholder="Share your thoughts…"></textarea>
              <div style="margin-top:8px; display:flex; justify-content:flex-end"><button class="btn-glass confirm" style="padding:10px 14px; border-radius:12px" type="submit" name="create_comment">Submit</button></div>
            </form>
          <?php else: ?>
            <div>you need to <a href='<?= defined('BASE_URL') ? BASE_URL : '' ?>/login'>log in</a> to leave comment</div>
          <?php endif; ?>
        </div>

        <div class="c-list">
          <?php foreach($comments as $row): 
            $comment_date = $row['comment_date']; 
            $comment_id = (int)$row['comment_id']; 
            $comment_content = $row['comment_content'];
            $author_id = (int)$row['author_id'];
            $user = $getComments->getCommentAuthor($author_id);
            $img = $getComments->authorImage($author_id)['user_image'] ?? '';
          ?>
            <div class="c-item">
              <img class="avatar" src="<?= (defined('BASE_URL') ? BASE_URL : '') ?>/images/<?= $img ? h($img) : 'person-placeholder.jpg' ?>" alt="avatar">
              <div>
                <div class="c-head">
                  <div>
                    <span class="c-name"><?= h($user['username'] ?? 'User') ?></span>
                    <span class="c-date"> · <?= h($comment_date) ?></span>
                  </div>
                  <?php if(isLoggedIn() && $author_id == ($_SESSION['user_id'] ?? 0)): ?>
                    <div class="c-actions">
                        <a href="#" class="delete-comment" data-id="<?= $comment_id ?>">Delete</a>
                        <a href="#" class="edit-comment" data-id="<?= $comment_id ?>">Edit</a>
                    </div>
                  <?php endif; ?>
                </div>

                <?php if($edit && $edit_id === $comment_id): ?>
                  <form action="" method="post" style="margin-top:8px">
                    <input class="edit-inp" type="text" value="<?= h($comment_content) ?>" name="comment_content">
                    <div style="margin-top:8px; display:flex; gap:8px">
                      <button class="ghost" type="submit" name="edit_comment">Save</button>
                      <a class="ghost" style="padding:10px 14px; border-radius:12px" href="<?= BASE_URL ?>/<?= urlencode($post_slug) ?>-<?= $the_post_id ?>">Cancel</a>
                    </div>
                  </form>
                <?php else: ?>
                  <div style="margin-top:6px; color:#E5EAF3; line-height:1.6"><?= nl2br(h($comment_content)) ?></div>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </article>

    <!-- SIDEBAR RIGHT -->
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
          <?php foreach ($popular as $pp): ?>
            <a href="<?= BASE_URL ?>/<?= urlencode($post_slug) ?>-<?= $the_post_id ?>">
              <img src="<?= (defined('BASE_URL') ? BASE_URL : '') ?>/images/<?= $pp['post_image'] ? h($pp['post_image']) : 'y9DpT.jpg' ?>" alt="thumb">
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



  <!-- Back to top button -->
  <button id="toTop" class="to-top" aria-label="Back to top" title="Back to top">↑</button>


<!-- POPUP (Edit/Delete) -->
<div id="commentPopup" style="
  display:none; position:fixed; inset:0; z-index:200;
  backdrop-filter:blur(10px) saturate(180%);
  background:rgba(0,0,0,.55); place-items:center;">
  <div style="
    background:var(--glass); border:1px solid var(--stroke);
    border-radius:20px; padding:28px; width:90%; max-width:420px;
    color:var(--fg); box-shadow:var(--shadow); text-align:left;">
    <h3 id="popupTitle" style="margin-top:0; font-family:'Playfair Display',serif;"></h3>
    <textarea id="popupTextarea" rows="4" style="
      width:100%; border-radius:14px; padding:12px;
      background:var(--glass2); border:1px solid var(--stroke);
      color:var(--fg); resize:vertical; display:none;"></textarea>
    <p id="popupText" style="margin:10px 0 0; font-size:16px; color:var(--muted); display:none;"></p>

    <div class="popup-actions" style="margin-top:18px; display:flex; justify-content:flex-end; gap:12px;">
        <button id="cancelPopup" class="btn-glass cancel">Cancel</button>
        <button id="confirmPopup" class="btn-glass confirm">Confirm</button>
    </div>

  </div>
</div>



  <script>
    const toTop = document.getElementById('toTop');
    window.addEventListener('scroll', () => { if (window.scrollY > 320) toTop.classList.add('show'); else toTop.classList.remove('show'); });
    toTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

document.querySelectorAll('.heart').forEach(btn=>{
  btn.onclick=()=>{
    const liked = btn.dataset.liked === '1';
    const post = btn.dataset.post;
    fetch('../ajax.php',{
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:new URLSearchParams({
        action: liked?'unlike':'like',
        post_id: post
      })
    }).then(r=>r.json()).then(res=>{
      if(res.status==='liked'){ btn.classList.add('liked'); btn.dataset.liked='1'; btn.nextElementSibling.textContent='Liked'; }
      else if(res.status==='unliked'){ btn.classList.remove('liked'); btn.dataset.liked='0'; btn.nextElementSibling.textContent='Like this'; }
    });
  };
});

document.querySelector('form[action=""][method="post"]')?.addEventListener('submit', e=>{
  e.preventDefault();
  const content = e.target.comment_content.value.trim();
  if(!content) return;
  fetch('../ajax.php',{
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:new URLSearchParams({
      action:'comment',
      post_id:<?= $the_post_id ?>,
      comment_content: content
    })
  }).then(r=>r.json()).then(res=>{
    if(res.status==='commented'){ location.reload(); } // or append dynamically if you want fully smooth
  });
});


// DELETE COMMENT
document.querySelectorAll('.delete-comment').forEach(btn=>{
  btn.onclick=e=>{
    e.preventDefault();
    if(!confirm('Delete this comment?')) return;
    fetch('../ajax.php',{
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:new URLSearchParams({
        action:'delete_comment',
        comment_id: btn.dataset.id
      })
    }).then(r=>r.json()).then(res=>{
      if(res.status==='deleted') btn.closest('.c-item').remove();
    });
  };
});

// EDIT COMMENT
document.querySelectorAll('.edit-comment').forEach(btn=>{
  btn.onclick=e=>{
    e.preventDefault();
    const id = btn.dataset.id;
    const commentDiv = btn.closest('.c-item').querySelector('div[style]');
    const current = commentDiv.textContent.trim();
    const newContent = prompt('Edit your comment:', current);
    if(newContent === null) return;
    fetch('../ajax.php',{
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:new URLSearchParams({
        action:'edit_comment',
        comment_id: id,
        comment_content: newContent
      })
    }).then(r=>r.json()).then(res=>{
      if(res.status==='edited') commentDiv.innerHTML = newContent.replace(/\n/g,'<br>');
    });
  };
});

const popup = document.getElementById('commentPopup');
const popupTitle = document.getElementById('popupTitle');
const popupTextarea = document.getElementById('popupTextarea');
const popupText = document.getElementById('popupText');
const cancelPopup = document.getElementById('cancelPopup');
const confirmPopup = document.getElementById('confirmPopup');

let actionType = null;
let currentComment = null;
let commentId = null;

// Open EDIT popup
document.querySelectorAll('.edit-comment').forEach(btn=>{
  btn.onclick=e=>{
    e.preventDefault();
    currentComment = btn.closest('.c-item').querySelector('div[style]');
    commentId = btn.dataset.id;
    actionType = 'edit_comment';

    popupTitle.textContent = 'Edit Comment';
    popupTextarea.style.display = 'block';
    popupText.style.display = 'none';
    popupTextarea.value = currentComment.textContent.trim();
    popup.style.display = 'grid';
  };
});

// Open DELETE popup
document.querySelectorAll('.delete-comment').forEach(btn=>{
  btn.onclick=e=>{
    e.preventDefault();
    currentComment = btn.closest('.c-item');
    commentId = btn.dataset.id;
    actionType = 'delete_comment';

    popupTitle.textContent = 'Delete Comment';
    popupTextarea.style.display = 'none';
    popupText.style.display = 'block';
    popupText.textContent = 'Are you sure you want to delete this comment? This action cannot be undone.';
    popup.style.display = 'grid';
  };
});

// Cancel
cancelPopup.onclick = ()=> popup.style.display = 'none';

// Confirm
confirmPopup.onclick = ()=>{
  let bodyData = new URLSearchParams({ action: actionType, comment_id: commentId });

  if(actionType === 'edit_comment'){
    const newContent = popupTextarea.value.trim();
    if(!newContent) return;
    bodyData.append('comment_content', newContent);
  }

  fetch('../ajax.php',{
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body: bodyData
  })
  .then(r=>r.json())
  .then(res=>{
    if(res.status==='edited'){
      currentComment.innerHTML = popupTextarea.value.replace(/\n/g,'<br>');
    } else if(res.status==='deleted'){
      currentComment.remove();
    }
    popup.style.display='none';
  });
};



  </script>
</body>
</html>
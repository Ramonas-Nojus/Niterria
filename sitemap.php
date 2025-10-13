<?php
include "settings-core-7189.php";
include "includes/db.php";
include "admin/functions.php";

// Set content type
header("Content-Type: application/xml; charset=utf-8");

// Website base URL
$base_url = "https://niterria.com/";


$sql = "SELECT post_id, post_title, post_date FROM posts ORDER BY post_id DESC";
$rs = mysqli_query($connection, $sql);
$db_posts = $rs ? mysqli_fetch_all($rs, MYSQLI_ASSOC) : [];

$posts = [];

// Simulated post URLs (replace with DB query in real use)
foreach($db_posts as $post){

    $slug = slugify($post["post_title"])."-".$post["post_id"];

    $posts[] = [
        "slug" => $slug,
        "lastmod" => $post["post_date"]
    ];
}



echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Home Page -->
    <url>
        <loc><?= $base_url ?></loc>
        <lastmod><?= date('Y-m-d') ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <!-- Posts -->
    <?php foreach($posts as $post): ?>
    <url>
        <loc><?= $base_url . $post['slug'] ?></loc>
        <lastmod><?= $post['lastmod'] ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <?php endforeach; ?>
</urlset>

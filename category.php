<?php  include "includes/db.php"; ?>
 <?php  include "includes/header.php"; ?>
 <?php include "includes/class.autoload.php"; ?>
    <!-- Navigation -->
    <?php  include "includes/navigation.php"; ?>
    <!-- Page Content -->
    <div class="container">
        <div class="row">
            <!-- Blog Entries Column -->
            <div class="col-md-8">
               <?php

    if(isset($_GET['cat_id'])){
        
      $post_category_id  = $_GET['cat_id'];
      $category = $_GET['category'];

      echo "<h1>All posts with <<<b>$category</b>>>> categorie</h1><Br>";

      $posts = new Posts();

if(isset($_SESSION['username']) && is_admin($_SESSION['username'])){

    $adminPostsByCat = $posts->adminPostsByCat($post_category_id);
    foreach($adminPostsByCat as $row){
        $post_id = $row['post_id'];
        $post_title = $row['post_title'];
        $post_date = $row['post_date'];
        $post_image = $row['post_image'];
        $post_subtitle = $row['post_subtitle'];
        $post_status = $row['post_status'];
        ?>
        <h2>
                    <a href="/post/<?php echo $post_id; ?>"><?php echo $post_title ?></a>
                </h2>
                
                <img class="img-responsive" src="/images/<?php if($post_image == ""){ echo "y9DpT.jpg"; } else{echo $post_image;}?>" alt="">
                <hr>
                <p><span class="glyphicon glyphicon-time"></span> <?php echo $post_date ?></p>
                <hr>
                <p><?php echo $post_subtitle; ?></p>
                <a class="btn btn-primary" href="#">Read More <span class="glyphicon glyphicon-chevron-right"></span></a>
                <hr>
                <?php 
    }

} else {
        //  $stmt2 = mysqli_prepare($connection, "SELECT post_id, post_title, post_user, post_date, post_image, post_content FROM posts WHERE post_category_id = ? AND post_status = ? ");
        //  $published = 'published';
    $PostsByCat = $postsByCat->PostsByCat($post_category_id);
    foreach($PostsByCat as $row){
        $post_id = $row['post_id'];
        $post_title = $row['post_title'];
        $post_author = $row['post_user'];
        $post_author_id = $row['post_user_id'];
        $post_date = $row['post_date'];
        $post_image = $row['post_image'];
        $post_content = $row['post_content'];
        $post_status = $row['post_status'];
        ?>
        <h2>
                    <a href="/post/<?php echo $post_id; ?>"><?php echo $post_title ?></a>
                </h2>
                <p class="lead">
                    by <a href="author/<?php echo $post_author; ?>"><?php echo $post_author ?></a>
                </p>
                <p><span class="glyphicon glyphicon-time"></span> <?php echo $post_date ?></p>
                <hr>
                <img class="img-responsive" src="/images/<?php if($post_image == ""){ echo "y9DpT.jpg"; } else{echo $post_image;}?>" alt="">
                <hr>
                <p><?php echo $post_content ?></p>
                <a class="btn btn-primary" href="#">Read More <span class="glyphicon glyphicon-chevron-right"></span></a>
                <hr>
                <?php 
    }
} } else {
    redirect('/');
    }
?>
            </div>
            <!-- Blog Sidebar Widgets Column -->
            <?php include "includes/sidebar.php";?>
        </div>
        <!-- /.row -->
        <hr>
<?php include "includes/footer.php";?>

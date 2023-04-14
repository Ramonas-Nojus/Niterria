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
        
    $PostsByCat = $posts->PostsByCat($post_category_id);

    if(count($PostsByCat) == 0) { 
        echo "<h1>There is no posts yet</h1>"; 
    } else {

        $per_page = 5;

        if(isset($_GET['page'])) {
            $page = $_GET['page'];
        } else {
            $page = "";
        }
        if($page == "" || $page == 1) {
            $page_1 = 0;
        } else {
            $page_1 = ($page * $per_page) - $per_page;
        }
        $count  = ceil(count($PostsByCat) /$per_page);

    foreach($PostsByCat as $row){
        $post_id = $row['post_id'];
        $post_title = $row['post_title'];
        $post_date = $row['post_date'];
        $post_image = $row['post_image'];
        $post_status = $row['post_status'];
        $post_subtitle = $row['post_subtitle'];

        ?>
        <h2>
                    <a href="/post/<?php echo $post_id; ?>"><?php echo $post_title ?></a>
                </h2>
                <p><span class="glyphicon glyphicon-time"></span> <?php echo $post_date ?></p>
                <hr>
                <img class="img-responsive" src="/images/<?php if($post_image == ""){ echo "y9DpT.jpg"; } else{echo $post_image;}?>" alt="">
                <hr>
                <p><?php echo $post_subtitle ?></p>
                <a class="btn btn-primary" href="/post/<?php echo $post_id; ?>">Read More <span class="glyphicon glyphicon-chevron-right"></span></a>
                <hr>
                <?php 
    }?>

        <ul class="pager">
            <?php 
                for($i =1; $i <= $count; $i++) {
                    if($i == $page) {
                        echo "<li class='page-item'><a style='background-color: #33CBC2; color: white;' href='/profile?page={$i}'>{$i}</a></li>";
                    } else {
                        echo "<li class='page-item'><a class='page-link' href='/profile?page={$i}'>{$i}</a></li>";
                    }
                } 
            ?>
        </ul>

<?php } } else {
    redirect('/');
    }
?>
                </div>
            <?php include "includes/sidebar.php";?>
        </div>
    <hr>
<?php include "includes/footer.php";?>

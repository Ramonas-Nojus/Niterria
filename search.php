<?php
 declare(strict_types = 1);

include "includes/db.php"; ?>
 <?php  include "includes/header.php"; ?>
 <?php include "includes/class.autoload.php"; 
 
//  use search\Search;

 ?>
    <!-- Navigation -->
    
    <?php  include "includes/navigation.php"; ?>
    
    <style>

        .profilie_image {
            object-fit: cover;
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .img {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 350px;
            height: 200px;
            object-fit: cover;
            border-radius: 5px; 
        }

        .vid{
            width: 350px;
            height: 200px;
            background-color: black
        }
</style>

    <!-- Page Content -->
    <div class="container">

        <div class="row">
    
            <!-- Blog Entries Column -->
            
            <div class="col-md-8">
               
               <?php
            if(isset($_GET['submit'])){
                
            $search = $_GET['search'];

            echo "<h1>Search results for <<<b>$search</b>>></h1>";

            $Search = new search\Search;
            $src = $Search->search($search);
                
            if(empty($src)){
                echo "<h1> NO RESULT</h1>";
            } else {
               
                    foreach($src as $row){
                        $post_id = $row['post_id'];
                        $post_title = $row['post_title'];
                        $post_date = $row['post_date'];
                        $post_image = $row['post_image'];
                        $post_content = $row['post_content'];
                        $post_status = $row['post_status'];
                        $post_subtitle = $row['post_subtitle'];
?>
                <div style="padding-top: 25px;">                
                    <h1>
                        <a href="post/<?php echo $post_id; ?>"><?php echo $post_title ?></a>
                    </h1>
                
                <hr>
                
                
                <a href="/post/<?php echo $post_id; ?>">
                    <img class="img-responsive" src="/images/<?php if($post_image == ""){ echo "y9DpT.jpg"; } else{echo $post_image;}?>" alt="">
                </a>   

                <p><span class="glyphicon glyphicon-time"></span> <?php echo $post_date ?></p>

                <hr>

                <p><?php echo $post_subtitle ?></p>

                <a class="btn btn-primary" href="post.php?p_id=<?php echo $post_id; ?>">Read More <span class="glyphicon glyphicon-chevron-right"></span></a>

                <hr>

                </div>
                    
                   <?php } }  } ?>
                   </div>
            <!-- Blog Sidebar Widgets Column -->
            <?php include "includes/sidebar.php";?>
            </div>

        <!-- /.row -->
        <hr>
<?php include "includes/footer.php";?>

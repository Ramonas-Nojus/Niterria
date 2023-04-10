<?php  include "includes/db.php"; ?>
 <?php  include "includes/header.php"; ?>
 <?php  include "includes/class.autoload.php"; ?>
    <!-- Navigation -->
    <?php  include "includes/navigation.php"; ?>

<?php 

$Likes = new Likes();

if(isset($_POST['liked'])){
    $post_id = $_POST['post_id'];
    $user_id = $_POST['user_id']; 
    $Likes->setLikesPost($post_id,$user_id);
} 
if(isset($_POST['unliked'])){
    $post_id = $_POST['post_id'];
    $user_id = $_POST['user_id'];
    $Likes->unlikePost($post_id,$user_id);
} 
?>
    <style>
    .profilie_image{
        object-fit: cover;
        width: 60px;
        height: 60px;
        border-radius: 50%;
}
 </style>
    <!-- Page Content -->
    <div class="container">
        <div class="row">
            <!-- Blog Entries Column --> 
            <div class="col-md-8">
               
               <?php

    if(isset($_GET['p_id'])){
    
       $the_post_id = $_GET['p_id'];

        $update_statement = mysqli_prepare($connection, "UPDATE posts SET post_views_count = post_views_count + 1 WHERE post_id = ?");
        mysqli_stmt_bind_param($update_statement, "i", $the_post_id);
        mysqli_stmt_execute($update_statement);
     if(!$update_statement) {
        die("query failed" );
    }
    if(isset($_SESSION['user_id']) && is_admin($_SESSION['username']) ) {
         $stmt1 = mysqli_prepare($connection, "SELECT post_title, post_date, post_image, post_content, post_subtitle FROM posts WHERE post_id = ?");
    } else {
        $stmt2 = mysqli_prepare($connection , "SELECT post_title, post_date, post_image, post_content, post_subtitle FROM posts WHERE post_id = ? AND post_status = ? ");

        $published = 'published';
    }
    if(isset($stmt1)){
        mysqli_stmt_bind_param($stmt1, "i", $the_post_id);
        mysqli_stmt_execute($stmt1);
        mysqli_stmt_bind_result($stmt1, $post_title, $post_date, $post_image, $post_content, $post_subtitle);

      $stmt = $stmt1;
    }else {
        mysqli_stmt_bind_param($stmt2, "is", $the_post_id, $published);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_bind_result($stmt2, $post_title, $post_date, $post_image, $post_content, $post_subtitle);

     $stmt = $stmt2;

    }
    while(mysqli_stmt_fetch($stmt)) {
        ?>
                <!-- First Blog Post -->
                <h1>
                    <?php echo $post_title ?>
                </h1>
                <p><span class="glyphicon glyphicon-time"></span> <?php echo $post_date ?></p>
                <hr>
                <img class="img-responsive" src="/images/<?php if($post_image == ""){ echo "y9DpT.jpg"; } else{echo $post_image;}?>" alt="">
                <hr>
                <h2><p><?php echo $post_subtitle ?></p></h2>
                <p><?php echo stripslashes($post_content); ?></p>
        <?php }         
                if(isLoggedIn()){
                if(UserLikedPost($the_post_id)){  ?>
                        <div class="row">
                            <p class="pull-left"><a href="/post/<?php echo $the_post_id ?>" class="unlike"><span class="glyphicon glyphicon-thumbs-down"></span>Unlike</a></p>
                        </div>
                        <?php } else { ?>
                        <div class="row">
                            <p class="pull-left"><a href="/post/<?php echo $the_post_id ?>" class="like"><span class="glyphicon glyphicon-thumbs-up"></span>Like</a></p>
                        </div>
                         <?php }  } else { echo "<p class='pull-left'>You need to <a href='/login'>Log In</a> to leave like</p>"; } ?>
                        <div class="row">
                        <?php 
                        $Likes = new Likes();
                        $likes = $Likes->getLikesPost($the_post_id);
                        $lik = $likes['likes'];
                        echo "</br><p class='pull-right'>Likes: $lik </p>";  
                        ?>
                        </div>
                        <hr>      
<!-- Blog Comments -->
<?php 
    if(isset($_POST['create_comment'])) {

        $the_post_id = $_GET['p_id'];
        $comment_author = $_SESSION['username'];
        $comment_author_id = $_SESSION['user_id'];
        $comment_email = $_SESSION['user_email'];
        $comment_content = $_POST['comment_content'];

        if (!empty($comment_author) && !empty($comment_email) && !empty($comment_content)) {
            $setComment = new Comments();
            $setComment->setCommentsPosts($the_post_id, $comment_author,$comment_author_id,$comment_email,$comment_content);
        }
    }
?> 
                <!-- Posted Comments -->
        <!-- Comments Form -->
        <div class="well">
            <h4>Leave a Comment:</h4>
            <?php if(isLoggedIn()){ ?>
            <form action="#" method="post" role="form">
                <div class="form-group">
                    <textarea name="comment_content" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" name="create_comment" class="btn btn-primary">Submit</button>
            </form>
            <?php } else { echo "you need to <a href='/Niterria/login'>log in</a> to leave comment"; }  ?>
        </div>
        <hr>
                 <?php 

            $the_post_id = $_GET['p_id'];

            $getComments = new Comments();
            $comments = $getComments->getCommetsPosts($the_post_id);

            foreach($comments as $row){
                $comment_date = $row['comment_date']; 
                $comment_content = $row['comment_content'];
                $comment_author = $row['comment_author'];
                $author_id = $row['author_id'];
                ?>
                <!-- Comment -->
                <div class="media">
                    <a class="pull-left" href="#">
                        <img class="media-object profilie_image" width="50px" border-radius="50%" src="/images/<?php if(empty($getComments->authorImage($author_id)['user_image'])){ echo "person-placeholder.jpg"; } else { echo $getComments->authorImage($author_id)['user_image']; }
                        ?>" alt="">
                    </a>
                    <div class="media-body">
                        <h4 class="media-heading"><?php echo $comment_author;   ?>
                            <small><?php echo $comment_date;   ?></small>
                        </h4>
                        <?php echo $comment_content;   ?>
                    </div>
                </div>
           <?php } }  else {
            header("Location: /Niterria/");
            }
                ?>
            </div>
            <!-- Blog Sidebar Widgets Column -->
            <?php include "includes/sidebar.php";?>
        </div>
        <!-- /.row -->
        <hr>
<?php include "includes/footer.php";?>
<script>
            $(document).ready(function(){
                var post_id = <?php echo $the_post_id; ?>;
                var user_id = <?php echo $_SESSION['user_id']; ?>;
                // like
                $(".like").click(function(){
                   $.ajax({
                       url: "/post.php?p_id=<?php echo $the_post_id; ?>",
                       type: "post",
                       data: {
                           'liked': 1,
                           'post_id': post_id,
                           'user_id': user_id
                       }
                   });
                });
                // unlike
                $(".unlike").click(function(){
                   $.ajax({
                       url: "/post.php?p_id=<?php echo $the_post_id; ?>",
                       type: "post",
                       data: {
                           'unliked': 1,
                           'post_id': post_id,
                           'user_id': user_id
                       }
                   });
                });
            });

</script>

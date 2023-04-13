<?php  include "includes/db.php"; ?>
 <?php  include "includes/header.php"; ?>
 <?php  include "includes/class.autoload.php"; ?>
    <!-- Navigation -->
    <?php  include "includes/navigation.php"; ?>
    

<?php 

$Likes = new Likes();

if(isset($_GET['like'])){
    $post_id = $_GET['p_id'];
    $user_id = $_GET['like']; 
    $Likes->setLikesPost($post_id,$user_id);
    header("Location: /post/$post_id");
} 
if(isset($_GET['unlike'])){
    $post_id = $_GET['p_id'];
    $user_id = $_GET['unlike'];
    $Likes->unlikePost($post_id,$user_id);
    header("Location: /post/$post_id");

} 
?>
    <style>
    .profilie_image{
        object-fit: cover;
        width: 60px;
        height: 60px;
        border-radius: 50%;
}


.msg {
    font-size: 13.5px;
    text-align:center
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

    mysqli_stmt_store_result($stmt);

    if(mysqli_stmt_num_rows($stmt) != 1) { header("Location: /errors/404.php"); }

    while(mysqli_stmt_fetch($stmt)) {
        ?>
                <!-- First Blog Post -->
                <p class="msg">We independently review everything we recommend. When you buy through our links, we may earn a commission</p>
                <h1>
                    <?php echo $post_title ?>
                </h1>
                <hr>
                <img class="img-responsive" src="/images/<?php if($post_image == ""){ echo "y9DpT.jpg"; } else{echo $post_image;}?>" alt="">
                <p><span class="glyphicon glyphicon-time"></span> <?php echo $post_date ?></p>

                <hr>
                <h2><p><?php echo $post_subtitle ?></p></h2>
                <p><?php echo stripslashes($post_content); ?></p>
        <?php }         
                if(isLoggedIn()){ ?>
                <div class="row" style="margin-left; 10px; padding-right: 10px">
    
            <?php        if(UserLikedPost($the_post_id)){  ?>
                            <p class="pull-left"><a href="/post/<?php echo $the_post_id ?>/unlike/<?php echo $_SESSION['user_id']; ?>" >
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="red" class="bi bi-heart-fill" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>
                                </svg>
                            </a></p>
                        <?php } else { ?>
                            <p class="pull-left"><a href="/post/<?php echo $the_post_id ?>/like/<?php echo $_SESSION['user_id']; ?>" class="like">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                    <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                                </svg>
                            </a></p>
                         <?php }  } ?>
                         </div>

                        <hr>      

<!-- Blog Comments -->
<?php 

                            
    $comment = new Comments();


    if(isset($_POST['create_comment'])) {

        $the_post_id = $_GET['p_id'];
        $comment_author_id = $_SESSION['user_id'];
        $comment_email = $_SESSION['user_email'];
        $comment_content = $_POST['comment_content'];

        if (!empty($comment_content)) {
            $comment->setCommentsPosts($the_post_id,$comment_author_id,$comment_email,$comment_content);
            header("Location: /post/$the_post_id");
        }
    }

?> 
            <?php if(!isLoggedIn()) include "includes/sidebar.php";?>

                <!-- Posted Comments -->
        <!-- Comments Form -->
        <div class="card-body p-4 text-black" style="background-color: #F5F5F5;  border: 2px solid #BAC3D5;">
            <h4>Leave a Comment:</h4>
            <?php if(isLoggedIn()){ ?>
            <form action="" method="post" role="form">
                <div class="form-group">
                    <textarea name="comment_content" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" name="create_comment" class="btn btn-primary">Submit</button>
            </form>
            <?php } else { echo "you need to <a href='/login'>log in</a> to leave comment"; }  ?>
        </div>
        <hr>
                 <?php 

            $the_post_id = $_GET['p_id'];

            $getComments = new Comments();
            $comments = $getComments->getCommetsPosts($the_post_id);

            if(isset($_GET['delete_comment'])){
                $delete_comment_id = $_GET['delete_comment'];
                $getComments->deleteCommentsPosts($delete_comment_id);
                header("Location: /post/$the_post_id");
            }

            $edit = FALSE;

            if(isset($_GET['edit'])){
                $edit = TRUE;
                $edit_id = $_GET['edit'];
                if(isset($_POST['edit_comment'])) {
                    $comment_content = $_POST['comment_content'];
            
                    if (!empty($comment_content)) {
                        $comment->editCommentsPosts($edit_id,$comment_content);
                        header("Location: /post/$the_post_id");
                    }
                }
            }


            foreach($comments as $row){
                $comment_date = $row['comment_date']; 
                $comment_id = $row['comment_id']; 
                $comment_content = $row['comment_content'];
                $author_id = $row['author_id'];
                ?>
                <!-- Comment -->
                <div class="media" style="display: inline-block; ">
                    <a class="pull-left">
                        <img class="media-object profilie_image" width="50px" border-radius="50%" src="/images/<?php if(empty($getComments->authorImage($author_id)['user_image'])){ echo "person-placeholder.jpg"; } else { echo $getComments->authorImage($author_id)['user_image']; }
                        ?>" alt="">
                    </a>
                    <div class="media-body">
                        <h4 class="media-heading"><?php echo $getComments->getCommentAuthor($author_id)['username'];   ?>
                            <small><?php echo $comment_date;   ?></small>
                        </h4>
                        <?php if($edit && $edit_id == $comment_id){ ?>

                            <form action="" method="post">
                                <input type="text" style="height: 50px" value="<?php echo $comment_content; ?>" name="comment_content">
                                <input type="submit" name="edit_comment" class="btn btn-primary" value="edit comment">
                                <br>
                                <a href="/post/<?php echo $the_post_id ?>">cancel</a>
                            </form>

                        <?php } else { ?>
                        <?php echo $comment_content;   ?>
                        <?php } ?>

                    
                    <?php if(isLoggedIn() && $author_id == $_SESSION['user_id']){ ?>
                    <div class="dropdown-container " tabindex="-1">
                      <div class="three-dots pull-right"></div>
                        <div class="dropdown">
                            <div class="card-body p-4 text-black" style="background-color: #F5F5F5;  border: 2px solid #BAC3D5;">
                              <a href="/post/<?php echo $the_post_id ?>/delete/<?php echo $comment_id ?>"><div>Delete</div></a>
                              <a href="/post/<?php echo $the_post_id ?>/edit/<?php echo $comment_id ?>"><div>Edit</div></a>
                            </div>
                      </div>
                    </div>
                    <?php } ?>
                </div>
                </div>

           <?php } }  else {
            header("Location: /");
            }
                ?>
            </div>
            <!-- Blog Sidebar Widgets Column -->
            <?php if(isLoggedIn()) include "includes/sidebar.php";?>

        </div>
        <!-- /.row -->
        <hr>
<?php include "includes/footer.php";?>
 <script>
      document.addEventListener("DOMContentLoaded", function(event) { 
            var scrollpos = localStorage.getItem('scrollpos');
            if (scrollpos) window.scrollTo(0, scrollpos);
        });

        window.onbeforeunload = function(e) {
            localStorage.setItem('scrollpos', window.scrollY);
        };
</script>
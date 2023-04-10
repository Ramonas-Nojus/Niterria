<?php include "../includes/class.autoload.php"; ?>

 

<?php
    if(isset($_GET['p_id'])){
    
    $the_post_id =  escape($_GET['p_id']);

    }

    $query = "SELECT * FROM posts WHERE post_id = $the_post_id  ";
    $select_posts_by_id = mysqli_query($connection,$query);  

    while($row = mysqli_fetch_assoc($select_posts_by_id)) {
        $post_title         = $row['post_title'];
        $post_category_id   = $row['post_category_id'];
        $post_status        = $row['post_status'];
        $post_image         = $row['post_image'];
        $post_content       = $row['post_content'];
        $post_tags          = $row['post_tags'];
        $post_comment_count = $row['post_comment_count'];
        $post_date          = $row['post_date'];
        $post_subtitle      = $row['post_subtitle'];
        
    }

    if(isset($_POST['update_post'])) {
        
        $post_title          =  $_POST['post_title'];
        $post_category_id    =  $_POST['post_category'];
        $post_status         =  $_POST['post_status'];
        $post_image          =  $_FILES['image']['name'];
        $post_image_temp     =  $_FILES['image']['tmp_name'];
        $post_content        =  $_POST['post_content'];
        $post_tags           =  $_POST['post_tags'];
        $post_subtitle       =  $_POST['post_subtitle'];
        
        if(empty($post_image)) {
            $query = "SELECT post_image FROM posts WHERE post_id = $the_post_id ";
            $select_image = mysqli_query($connection,$query);    
            $post_image = $row = mysqli_fetch_array($select_image)['post_image'];
        }
        $post_title = mysqli_real_escape_string($connection, $post_title);

        $post = new Posts();
        $updatePost = $post->updatePost($post_title,$post_category_id,$post_image,$post_image_temp,$post_tags,$post_content,$post_date,$post_subtitle, $the_post_id, $post_status);
        echo "<p class='bg-success'>Post Updated. <a href='/post/{$the_post_id}'>View Post </a>"; if(is_admin()){ " or <a href='posts.php'>Edit More Posts</a></p>"; };
    }
?>

    <form action="" method="post" enctype="multipart/form-data">    
     
     
      <div class="form-group">
         <label for="title">Post Title</label>
          <input value="<?php echo htmlspecialchars(stripslashes($post_title)); ?>"  type="text" class="form-control" name="post_title">
      </div>

           <div class="form-group">
       <label for="categories">Categories</label>
       <select name="post_category" id="">
           
      <?php

      $query = "SELECT * FROM categories ";
      $select_categories = mysqli_query($connection,$query);
      
      confirmQuery($select_categories);

      while($row = mysqli_fetch_assoc($select_categories )) {
          $cat_id = $row['cat_id'];
          $cat_title = $row['cat_title'];

        if($cat_id == $post_category_id) {
            echo "<option selected value='{$cat_id}'>{$cat_title}</option>";

        } else {
            echo "<option value='{$cat_id}'>{$cat_title}</option>";
        }
         
      }
?>
       </select>

      </div>

        <div class="form-group">
      
       <div class="form-group">
      <select name="post_status" id="">
          
<option value='<?php echo $post_status ?>'><?php echo $post_status; ?></option>
      
      <?php
          
        if($post_status == 'published' ) {
            echo "<option value='draft'>Private</option>";
        } else {
            echo "<option value='published'>Publish</option>";
        }
        ?>
      </select>
        </div>
      
    <div class="form-group">
        <img width="100" src="/images/<?php echo $post_image; ?>" alt="" style="border: 3px solid;">
        <input style="padding-top: 5px;" type="file" name="image">
      </div>

      <div class="form-group">
         <label for="post_tags">Post Tags</label>
          <input value="<?php echo $post_tags; ?>"  type="text" class="form-control" name="post_tags">
      </div>
      
      <div class="form-group">
         <label for="post_tags">Post Subtitle</label>
          <input type="text" value="<?php echo $post_subtitle; ?>" class="form-control" name="post_subtitle">
      </div> 

      <div class="form-group">
         <label for="post_content">Post Content</label>
         <textarea  class="form-control "name="post_content" id="long_desc" cols="30" rows="10"><?php echo htmlspecialchars(stripslashes($post_content)); ?>
         </textarea>
      </div>

       <div class="form-group">
          <input class="btn btn-primary" type="submit" name="update_post" value="Update Post">
      </div>


</form>

<script>
        CKEDITOR.replace('long_desc');
    </script>
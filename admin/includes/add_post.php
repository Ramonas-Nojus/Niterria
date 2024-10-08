<?php include "../includes/class.autoload.php"; ?>
<?php
   
   if(isset($_POST['create_post'])) {
   
      $post_title        = $_POST['title'];
      $post_category_id  = $_POST['post_category'];

      $post_image        = $_FILES['image']['name'];
      $post_image_temp   = $_FILES['image']['tmp_name'];


      $post_tags         = $_POST['post_tags'];
      $post_subtitle     = $_POST['post_subtitle'];
      $post_content      = $_POST['post_content'];
      
      $setPost = new Posts();
      $addPost = $setPost->setPost($post_title,$post_category_id,$post_image,$post_image_temp,$post_tags,$post_content,$post_subtitle);

      $the_post_id = mysqli_insert_id($connection);
      echo "<p class='bg-success'>Post Created. <a href='../post/{$the_post_id}'>View Post </a>"; if(is_admin()){ " or <a href='posts.php'>Edit More Posts</a></p>"; };
   }  
?>

    <form action="" method="post" enctype="multipart/form-data">    
     
      <div class="form-group">
         <label for="title">Post Title</label>
          <input type="text" class="form-control" name="title">
      </div>

         <div class="form-group">
       <label for="category">Category</label>
       <select name="post_category" id="">
           
<?php

        $query = "SELECT * FROM categories";
        $select_categories = mysqli_query($connection,$query);
        
        confirmQuery($select_categories);


        while($row = mysqli_fetch_assoc($select_categories )) {
        $cat_id = $row['cat_id'];
        $cat_title = $row['cat_title'];
            
            
            echo "<option value='$cat_id'>{$cat_title}</option>";
         
            
        }
?>
       </select>
      </div>
    <div class="form-group">
         <label for="post_image">Post Image</label>
          <input type="file"  name="image">
      </div>

      <div class="form-group">
         <label for="post_tags">Post Tags</label>
          <input type="text" class="form-control" name="post_tags">
      </div>

      <div class="form-group">
         <label for="post_tags">Post Subtitle</label>
          <input type="text" class="form-control" name="post_subtitle">
      </div>      
      
      <div class="form-group">
         <label for="post_content">Post Content</label>
         <textarea class="form-control" name="post_content" id="long_desc" cols="30" rows="10">
         </textarea>
      </div>
   
       <div class="form-group">
          <input class="btn btn-primary" type="submit" name="create_post" value="Publish Post">
      </div>


</form>

<script>
        CKEDITOR.replace('long_desc');
    </script>
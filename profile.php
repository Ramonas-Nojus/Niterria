<?php  include "includes/header.php";  ?>
<?php  include "includes/navigation.php"; ?>
<?php include "includes/class.autoload.php"; ?>
<?php include "includes/db.php"; ?>

<style>

input[type=submit]{
  background-color: white;
  border: 3px solid black;
  border-radius: 8px;
  color: Black;
  padding: 3px 16px;
  text-decoration: none;
  margin: 4px 2px;
  cursor: pointer;

}

input[type=submit]:hover{
  background-color: grey;
}

input[type=text], select {
  width: 60%;
  display: inline-block;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

.image-upload>input {
  display: none;
}

</style>

<?php
    $edit = False;

    $user_id = $_SESSION['user_id'];

    if(isset($_GET['edit'])){
        $edit = $_GET['edit'];
    } 

    if(isset($_POST['edit_profile'])){
      $username = $_POST['username'];
      $profile_image = $_FILES['profile_image']['name'];
      $image_tmp = $_FILES['profile_image']['tmp_name'];

      if(!empty($profile_image)){
        move_uploaded_file($image_tmp, "images/$profile_image");
      } else {
        $profile_image = $_SESSION['user_image'];
      }

      if(username_exists($username)){
        echo "<p style='text-align:center'>This username alrady exists</p>";
      } else {

      $user = new Users();
      $user->editProfile($username, $profile_image, $user_id);

      $_SESSION['username'] = $username;
      $_SESSION['user_image'] = $profile_image;
      }
    }
?>

<?php 
                $post_ids = [];

                $posts = new Posts();
                
                $likedPostsIds = $posts->getLikedPostsIds($user_id);

                foreach($likedPostsIds as $x){
                    array_push($post_ids, $x['post_id']);
                }

                $post_id_list = implode(",", $post_ids);
?>

<script src="https://code.jquery.com/jquery-1.8.3.min.js"></script>
<section class="h-100">
  <div class="container py-5 h-100" >
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col col-lg-9 col-xl-9">
        <div class="card" style="border: 4px solid">
        <div class="card" >
          <form method="post" action="/profile" enctype="multipart/form-data">
            
            <div class="rounded-top text-white d-flex flex-row" style="background-color: #000; height:250px; border: 3px solid width: 100%; ">
              <div>
                <div class="image-upload" >
                  <label for="imgInp" style="float:left; display: inline-block;">
                    <img src="/images/<?php echo $_SESSION['user_image']; ?>" id="profile_img"
                      alt="Generic placeholder image" class="img-fluid img-thumbnail mt-4 mb-2"
                      style=" width: 150px; height: 150px; object-fit: cover; z-index: 0; margin: 10px; border: solid; border-color: black; cursor:pointer">
                  </label>
                  <?php  if($edit) {?>
                    <input name="profile_image" id="imgInp" type="file" valueaccept="image/*">
                  <?php } ?>
                </div>
              </div>
            <div class="ms-3" style="padding-top: 70px; margin-left: 15px;">
            <?php  if($edit) {?>
                    <input style="color: black;" type="text" value="<?php echo $_SESSION['username']; ?>" name="username">
                    <input type='submit' name="edit_profile">
                    <a href="/profile" style="color: white;">Cancel</a>
            <?php  } else {?>
              <h3 style="color: white;"><?php echo $_SESSION['username'] ?>
                <a href="/profile.php?edit=true">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" class="bi bi-pencil-fill" viewBox="0 0 16 16" style="margin-left: 10px; color: white;">
                    <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                  </svg>
                </a>
              </h3>
              <?php } ?>
            </div>
        </div>
      </form>
          <div class="card-body p-4 text-black">
            <div class="mb-5">
              <p class="p-4" style="font 20px;">Liked Posts:</p>
              <p class="p-4" style="background-color: #f8f9fa;">
                <?php echo count($likedPostsIds); ?>
              </p>
            </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
          <p class="lead fw-normal mb-0"></p>
        </div>

           <?php
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

                $likedPosts = $posts->getLikedPosts($page_1, $per_page, $post_id_list);

                $count  = ceil(count($likedPostsIds) /$per_page);

                foreach($likedPosts as $x){
                    $post_id = $x['post_id'];
                    $post_title = $x['post_title'];
                    $post_date = $x['post_date'];
                    $post_image = $x['post_image'];
                    $post_content = $x['post_content'];
                    $post_status = $x['post_status'];
                    $post_subtitle = $x['post_subtitle'];
            ?>
            <div class="d-flex justify-content-center" style="width: 100%;">
                <div class="col-md-10 mx-auto">
                <div class="post-preview">
                  <a href="/post/<?php echo $post_id; ?>">
                    <h2 class="post-title">
                      <?php echo $post_title; ?>
                    </h2>
                    <img src="images/<?php echo $post_image; ?>" style="width:100%; border: 4px solid; border-radius: 3px;">
                    <h2 class="post-subtitle">
                      <?php echo $post_subtitle ?>
                    </h2>
                  </a>
                  <p class="post-meta">Posted on <?php echo $post_date; ?></p>
                </div>
              <hr>
            </div>
            </div>
            <?php } ?>
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
            </div>
          </div>
        </div>
      </div>
    </div>
</section>
<script>
    imgInp.onchange = evt => {
        const [file] = imgInp.files
        if (file) {
            profile_img.src = URL.createObjectURL(file)
        }
    };
</script>
<?php  include("includes/footer.php") ?>


<!-- Blog Sidebar Widgets Column -->
            <div class="col-md-4">    
                <!-- Blog Search Well -->
                <div class="card-body p-4 text-white" style="  border-radius: 25px; background: linear-gradient(120deg,rgba(38, 14, 208, 1) 50%, rgba(83, 41, 237, 1) 100%); border: solid black 3px">
                    <h4>Blog Search</h4>
                    <form method="get" action="<?php echo BASE_URL; ?>/search">
                        <div class="input-group rounded search-bar search">
                            <input type="search" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="search-addon" name="search">
                            <button name="submit" class="input-group-text" type="submit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                            </button>
                        </div>
                    </form>
                    <!-- /.input-group -->
                
                
                <!-- Blog Categories Well -->
                <div class="card-body p-4 text-white" style="background: linear-gradient(120deg,rgba(38, 14, 208, 1) 50%, rgba(83, 41, 237, 1) 100%);"> 
                <?php 
                    $query = "SELECT * FROM categories";
                    $select_categories_sidebar = mysqli_query($connection,$query);         
                ?>
                 <h4>Blog Categories</h4>
                    <div class="row">
                        <div class="col-lg-12 ">
                            <ul class="list-unstyled" >
                              <?php 
                                while($row = mysqli_fetch_assoc($select_categories_sidebar )) {
                                    $cat_title = $row['cat_title'];
                                    $cat_id = $row['cat_id'];
                                    echo "<li><a style='color: white;' href='". BASE_URL ."/category/$cat_title/$cat_id'>{$cat_title}</a></li>";
                                } ?>    
                            </ul>
                        </div>
                    </div>
                    <!-- /.row -->
                </div>
            </div>
        <style>
            .sidebar {
              padding: 20px;
            }
            
            .popular-posts-list {
              list-style: none;
              padding: 0;
              margin: 0;
            }
            
            .popular-post-item {
              margin-bottom: 20px;
            }
            
            .popular-post-item a {
              display: flex;
              align-items: center;
              text-decoration: none;
              color: #333;
            }
            
            .popular-post-item img {
              width: 120px; /* Adjust thumbnail size */
              object-fit: cover;
              margin-right: 15px;
              border-radius: 5px;
            }
            
            .post-details {
              display: flex;
              flex-direction: column;
            }
            
            .post-details h4 {
              margin: 0;
              font-size: 16px;
              line-height: 1.2;
            }
            
            .post-details span {
              font-size: 12px;
              color: #666;
              margin-top: 5px;
            }
        </style>
        
            <div class="sidebar">
              <h3>Popular Posts</h3>
             
              <ul class="popular-posts-list">
                 <?php
                 
                $newObj = new Posts();
                $post = $newObj->getPopularPosts();
                foreach($post as $x){
                        $post_id = $x['post_id'];
                        $post_title = $x['post_title'];
                        $post_date = $x['post_date'];
                        $post_image = $x['post_image'];
                ?>
                  
                <li class="popular-post-item">
                  <a href="post/<?php echo $post_id; ?>">
                    <img src="<?php echo BASE_URL; ?>/images/<?php if($post_image == ""){ echo "y9DpT.jpg"; } else{echo $post_image;}?>">
                    <div class="post-details">
                      <h4><?php echo $post_title; ?></h4>
                      <span><?php echo $post_date; ?></span>
                    </div>
                  </a>
                </li>
               
                <?php } ?>
              </ul>
            </div>
        </div>
        


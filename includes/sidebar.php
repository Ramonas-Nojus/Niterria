
<!-- Blog Sidebar Widgets Column -->
            <div class="col-md-4">    
                <!-- Blog Search Well -->
                <div class="card-body p-4 text-black" style="background-color: #F5F5F5; border: 2px solid #BAC3D5;">
                    <h4>Blog Search</h4>
                    <form method="get" action="/search">
                        <div class="input-group rounded search-bar">
                            <input type="search" class="form-control rounded" placeholder="Search" aria-label="Search" aria-describedby="search-addon" name="search">
                            <button name="submit" class="input-group-text border-0" type="submit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                            </button>
                        </div>
                    </form>
                    <!-- /.input-group -->
                </div>
                
                
                <!-- Blog Categories Well -->
                <div class="card-body p-4 text-black" style="background-color: #F5F5F5;  border: 2px solid #BAC3D5; margin-top: 30px;"> 
                <?php 
                    $query = "SELECT * FROM categories";
                    $select_categories_sidebar = mysqli_query($connection,$query);         
                ?>
                 <h4>Blog Categories</h4>
                    <div class="row">
                        <div class="col-lg-12">
                            <ul class="list-unstyled">             
                              <?php 
                                while($row = mysqli_fetch_assoc($select_categories_sidebar )) {
                                    $cat_title = $row['cat_title'];
                                    $cat_id = $row['cat_id'];
                                    echo "<li><a href='/category/$cat_title/$cat_id'>{$cat_title}</a></li>";
                                } ?>    
                            </ul>
                        </div>
                    </div>
                    <!-- /.row -->
                </div>
            </div>
            
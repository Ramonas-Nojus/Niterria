<?php 

class Posts extends \DB {

    public function getPosts($page_1,$per_page){
        
        $sth  = $this->connection()->prepare("SELECT * FROM posts ORDER BY post_id DESC  LIMIT :page_1, :per_page");
        $sth->bindValue('page_1', $page_1, PDO::PARAM_INT);
        $sth->bindValue('per_page', $per_page, PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getLikedPosts($page_1, $per_page, $post_id_list){
        $sth  = $this->connection()->prepare("SELECT * FROM posts WHERE FIND_IN_SET(post_id, :post_id_list) ");
        $sth->bindValue("post_id_list", $post_id_list, PDO::PARAM_STR);
        $sth->execute();
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getLikedPostsIds($user_id){
        $sth  = $this->connection()->prepare("SELECT * FROM likes WHERE user_id = :user_id");
        $sth->bindValue('user_id', $user_id, PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function postsByCat($post_category_id){

        $sql = "SELECT * FROM posts WHERE post_category_id = :post_category_id AND post_status = 'published' ORDER BY post_id DESC";
        $sth = $this->connection()->prepare($sql);
        $sth->bindValue("post_category_id", $post_category_id, PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function setPost($post_title,$post_category_id,$post_image,$post_image_temp,$post_tags,$post_content,$post_subtitle){
        $sql = "INSERT INTO `posts` (post_category_id, post_title, post_image, post_content, post_tags, post_status, post_date, post_subtitle) ";
        $sql .= " VALUES ( :post_category_id, :post_title, :post_image, :post_content, :post_tags, 'published', now(), :post_subtitle ) "; 
        $sth = $this->connection()->prepare($sql);

        $sth->bindValue('post_title', $post_title, PDO::PARAM_STR);
        $sth->bindValue('post_category_id', $post_category_id, PDO::PARAM_INT);
        $sth->bindValue('post_subtitle', $post_subtitle, PDO::PARAM_STR);
        $sth->bindValue('post_tags', $post_tags, PDO::PARAM_STR);
        $sth->bindValue('post_content', $post_content, PDO::PARAM_STR);
        $sth->bindValue('post_image', $post_image, PDO::PARAM_STR);

        $sth->execute();

        move_uploaded_file($post_image_temp, "../images/$post_image" );


        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

    public function updatePost($post_title,$post_category_id,$post_image,$post_image_temp,$post_tags,$post_content,$post_date,$post_subtitle,$the_post_id,$post_status){

        $sth  = $this->connection()->prepare("UPDATE posts SET 
          post_title  = :post_title,
            post_category_id = :post_category_id,
            post_date   =  :post_date,
            post_status = :post_status,
            post_subtitle = :post_subtitle,
            post_tags   = :post_tags,
            post_content= :post_content,
            post_image  = :post_image
            WHERE post_id = :post_id ");

          $sth->bindValue('post_title', $post_title, PDO::PARAM_STR);
          $sth->bindValue('post_category_id', $post_category_id, PDO::PARAM_INT);
          $sth->bindValue(":post_date", $post_date, PDO::PARAM_STR);
          $sth->bindValue('post_status', $post_status, PDO::PARAM_STR);
          $sth->bindValue('post_subtitle', $post_subtitle, PDO::PARAM_STR);
          $sth->bindValue('post_tags', $post_tags, PDO::PARAM_STR);
          $sth->bindValue('post_content', $post_content, PDO::PARAM_STR);
          $sth->bindValue('post_image', $post_image, PDO::PARAM_STR);
          $sth->bindValue('post_id', $the_post_id, PDO::PARAM_INT);

        $sth->execute();
        
       move_uploaded_file($post_image_temp, "../images/$post_image" );

        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

    public function getSearch($search){
        $pattern = "%".$search."%";
        $sql = "SELECT * FROM posts WHERE post_tags LIKE ? OR post_title LIKE ? ORDER BY post_id DESC";
        $stmt = $this->connection()->prepare($sql);
        $stmt->execute([$pattern, $pattern]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
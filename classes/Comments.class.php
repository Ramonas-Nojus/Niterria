<?php 

class Comments extends Db {

    public function getCommetsPosts($post_id){
        $sql = "SELECT * FROM comments WHERE comment_post_id = ? AND comment_status = ? ";
        $sql .= "ORDER BY comment_id DESC ";
        $stmt = $this->connection()->prepare($sql);
        $stmt->execute([$post_id, 'approved' ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function authorImage($author_id){
        $sql = "SELECT user_image FROM users WHERE user_id = ? ";
        $stmt = $this->connection()->prepare($sql);
        $stmt->execute([$author_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function setCommentsPosts($the_post_id,$comment_author,$comment_author_id,$comment_email,$comment_content){
        $sql = "INSERT INTO comments (comment_post_id, comment_author, comment_email, comment_content, comment_status,comment_date,author_id)";
        $sql .= "VALUES ('{$the_post_id}' ,'{$comment_author}', '{$comment_email}', '{$comment_content }', 'approved',now(),'{$comment_author_id}')";
        $stmt = $this->connection()->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function setCommentsVideo($the_video_id,$comment_author,$comment_author_id,$comment_email,$comment_content){
        $sql = "INSERT INTO comments (comment_video_id, comment_author, comment_email, comment_content, comment_status,comment_date,author_id)";
        $sql .= "VALUES ('{$the_video_id}' ,'{$comment_author}', '{$comment_email}', '{$comment_content }', 'approved',now(),'{$comment_author_id}')";
        $stmt = $this->connection()->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCommetsVideo($video_id){
        $sql = "SELECT * FROM comments WHERE comment_video_id = ? AND comment_status = ? ";
        $sql .= "ORDER BY comment_id DESC ";
        $stmt = $this->connection()->prepare($sql);
        $stmt->execute([$video_id, 'approved' ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}
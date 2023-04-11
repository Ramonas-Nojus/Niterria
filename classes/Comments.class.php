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

    public function getCommentAuthor($author_id){
        $sql = "SELECT username FROM users WHERE user_id = :user_id ";
        $sth = $this->connection()->prepare($sql);
        $sth->bindValue("user_id", $author_id, PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetch(PDO::FETCH_ASSOC);
    }


    public function setCommentsPosts($the_post_id,$comment_author_id,$comment_email,$comment_content){
        $sql = "INSERT INTO comments (comment_post_id, comment_email, comment_content, comment_status,comment_date,author_id)";
        $sql .= "VALUES ('{$the_post_id}', '{$comment_email}', '{$comment_content }', 'approved',now(),'{$comment_author_id}')";
        $stmt = $this->connection()->query($sql);
    }

    public function deleteCommentsPosts($comment_id){
        $sql = "DELETE FROM comments WHERE comment_id = :comment_id ";
        $sth = $this->connection()->prepare($sql);
        $sth->bindValue('comment_id', $comment_id, PDO::PARAM_INT);
        $sth->execute();

    }

    public function editCommentsPosts($comment_id,$comment_content){
        $sql = "UPDATE comments SET comment_content = :comment_content WHERE comment_id = :comment_id";
        $sth = $this->connection()->prepare($sql);
        $sth->bindValue("comment_content", $comment_content, PDO::PARAM_STR);
        $sth->bindValue("comment_id", $comment_id, PDO::PARAM_INT);
        $sth->execute();
    }
}
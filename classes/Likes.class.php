<?php 


class Likes extends Db {

    public function getLikesPost($post_id){
        $sql = "SELECT likes FROM posts WHERE post_id = ?";
        $stmt = $this->connection()->prepare($sql);
        $stmt->execute([$post_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function setLikesPost($post_id,$user_id){
       
        $sql = "UPDATE posts SET likes = likes + 1 WHERE post_id = ?";
        $stmt = $this->connection()->prepare($sql);
        $stmt->execute([$post_id]);
        $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sql = "INSERT INTO likes(user_id,post_id) VALUES(?,?)";
        $stmt = $this->connection()->prepare($sql);
        $stmt->execute([$user_id,$post_id]);
    }

    public function unlikePost($post_id,$user_id){
       
        $sql = "UPDATE posts SET likes = likes - 1 WHERE post_id = ?";
        $stmt = $this->connection()->prepare($sql);
        $stmt->execute([$post_id]);

        $sql = "DELETE FROM likes WHERE post_id = ? AND user_id = ?";
        $stmt = $this->connection()->prepare($sql);
        $stmt->execute([$post_id, $user_id]);
    }


}
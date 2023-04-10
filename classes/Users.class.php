<?php 

class Users extends Db {

    public function getUser($username){

        $sql = "SELECT * FROM users WHERE username = :username ";
        $sth = $this->connection()->prepare($sql);
        $sth->bindValue("username", $username, PDO::PARAM_STR);
        $sth->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function editProfile($username, $image, $user_id){

        $sql = "UPDATE users SET username = :username, user_image = :user_image WHERE user_id = :user_id ";
        $sth = $this->connection()->prepare($sql);
        $sth->bindValue("username", $username, PDO::PARAM_STR);
        $sth->bindValue("user_image", $image, PDO::PARAM_STR);
        $sth->bindValue("user_id", $user_id, PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

}
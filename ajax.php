<?php
include "settings-core-7189.php";
include "includes/db.php";
include "admin/functions.php";
include "includes/class.autoload.php";
session_start();

header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])){
  echo json_encode(['status'=>'error','msg'=>'not_logged_in']);
  exit;
}

$Likes = new Likes();
$Comments = new Comments();

$action = $_POST['action'] ?? '';

switch($action){
  case 'like':
    $Likes->setLikesPost((int)$_POST['post_id'], (int)$_SESSION['user_id']);
    echo json_encode(['status'=>'liked']);
    break;

  case 'unlike':
    $Likes->unlikePost((int)$_POST['post_id'], (int)$_SESSION['user_id']);
    echo json_encode(['status'=>'unliked']);
    break;

  case 'comment':
    $comment_content = trim($_POST['comment_content'] ?? '');
    if($comment_content !== ''){
      $Comments->setCommentsPosts((int)$_POST['post_id'], (int)$_SESSION['user_id'], $_SESSION['user_email'], $comment_content);
      echo json_encode(['status'=>'commented']);
    } else {
      echo json_encode(['status'=>'error','msg'=>'empty_comment']);
    }
    break;

  case 'delete_comment':
    $Comments->deleteCommentsPosts((int)$_POST['comment_id']);
    echo json_encode(['status'=>'deleted']);
    break;

  case 'edit_comment':
    $new = trim($_POST['comment_content'] ?? '');
    if($new !== ''){
      $Comments->editCommentsPosts((int)$_POST['comment_id'], $new);
      echo json_encode(['status'=>'edited']);
    } else {
      echo json_encode(['status'=>'error','msg'=>'empty_edit']);
    }
    break;


  default:
    echo json_encode(['status'=>'error','msg'=>'invalid_action']);
}


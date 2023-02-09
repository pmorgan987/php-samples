<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Function_Class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DB_Class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image_Class.php');

class Announcements_Class {

  public static function addAnnouncement($post,$files) {
    $img = Announcements_Class::addAnnouncementImage($_FILES);
    $post = Function_Class::trimAll($post);
    $query = "insert into announcements set title=:title, image=:image, body=:body, date_posted=now(), active='y', display_order=:display_order";
    $params = array('title'=>$post['title'],'image'=>$img,'body'=>$post['body'],'display_order'=>$post['display_order']);
    DB_Class::query($query,$params);
    // set message
    $_SESSION['message'] = 'Announcement Added';
    Function_Class::redirect('announcements.php');
  }

  public static function editAnnouncement($post,$files) {
    $post = Function_Class::trimAll($post);
    // Look at $files and see if there is a new image...if so, need to delete the $post['existing_image'] one and add the new one
    if ($files['image']['name']) {
      unlink($_SERVER['DOCUMENT_ROOT'].$post['existing_image']);
      $img = Announcements_Class::addAnnouncementImage($_FILES);
    } else {
      $img = $post['existing_image'];
    }
    // Update announcement post
    $query = "update announcements set title=:title, image=:image, body=:body, date_posted=now(), active='y', display_order=:display_order where announcement_id=:id";
    $params = array('title'=>$post['title'],'image'=>$img,'body'=>$post['body'],'id'=>$post['announcement_id'],'display_order'=>$post['display_order']);
    DB_Class::query($query,$params);
    // set message
    $_SESSION['message'] = 'Announcement Edited';
    Function_Class::redirect('announcements.php');
  }

  public static function getActiveAnnouncements() {
    $query = "select * from announcements where active='y' order by display_order asc";
    $results = DB_Class::query($query,array());
    return $results;
  }

  public static function getActiveAnnouncementsCount() {
    $query = "select count(*) from announcements where active='y'";
    $results = DB_Class::query($query,array());
    return $results[0]['count(*)'];
  }

  public static function getAnnouncement($id) {
    $query = "select * from announcements where announcement_id=:id";
    $params = array('id'=>$id);
    $announcement = DB_Class::query($query,$params);
    return $announcement[0];
  }

  public static function addAnnouncementImage($files) {
    if ($files['image']['name']) {
      $ext = explode('.',$files['image']['name']);
      $ext = end($ext);
      $newfilename = Function_Class::generateRandomString(12);
      $newImgName = '/images/announcements/announcement_'.$newfilename.'.'.$ext;
      move_uploaded_file($files['image']['tmp_name'],$_SERVER['DOCUMENT_ROOT'].$newImgName);
      #Image_Class::resizeImage($_SERVER['DOCUMENT_ROOT'].$newImgName,$_SERVER['DOCUMENT_ROOT'].$newImgName,200,200,$ext);
      return $newImgName;
    } else {
      return '';
    }
  }

  public static function getNextAnnouncementOrder() {
    $query = "select display_order from announcements where active='y' order by display_order desc limit 1";
    $result = DB_Class::query($query,array());
    return $result[0]['display_order'];
  }

  public static function deactivateAnnouncement($id) {
    $query = "update announcements set active='n' where announcement_id=:id";
    $params = array('id'=>$id);
    $result = DB_Class::query($query,$params);
    // set message
    $_SESSION['message'] = 'Announcement Deactivated';
    Function_Class::redirect('announcements.php');
  }
}
?>

<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DB_Class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Function_Class.php');
#require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Validate_Class.php');

class Newsletter_Class {

  public static function getNewslettersCount() {
    $query = "select count(*) from newsletters";
    $params = array();
    $result = DB_Class::query($query,$params);
    return $result[0]['count(*)'];
  }

  public static function getRecentNewsletters($limit) {
    $query = "select title,body from newsletters order by newsletter_id desc limit :limit";
    $params = array('limit'=>$limit);
    $result = DB_Class::query($query,$params);
    return $result;
	}

  public static function getAllNewsletters() {
    $query = "select * from newsletters order by newsletter_id desc";
    $params = array();
    $result = DB_Class::query($query,$params);
    return $result;
  }

  public static function addNewsletter($post) {
    $post = Function_Class::trimAll($post);
    $query = "insert into newsletters set title=:title, body=:body, date_posted=:date";
    $params = array('title'=>$post['title'],'body'=>$post['body'],'date'=>$post['date_posted']);
    DB_Class::query($query,$params);
    $_SESSION['message'] = 'Newsletter Added';
    Function_Class::redirect('newsletters.php');
  }

  public static function editNewsletter($post) {
    $post = Function_Class::trimAll($post);
    $query = "update newsletters set title=:title, body=:body, date_posted=:date where newsletter_id=:newsletter_id";
    $params = array('title'=>$post['title'],'body'=>$post['body'],'date'=>$post['date_posted'],'newsletter_id'=>$post['newsletter_id']);
    DB_Class::query($query,$params);
    $_SESSION['message'] = 'Newsletter Edited';
    Function_Class::redirect('newsletters.php');
  }

  public static function deleteNewsletter($id) {
    $query = "delete from newsletters where newsletter_id=:newsletter_id";
    $params = array('newsletter_id'=>$id);
    DB_Class::query($query,$params);
    $_SESSION['message'] = 'Newsletter Deleted';
    Function_Class::redirect('newsletters.php');
  }

  public static function getNewsletterInfo($post) {
    $query = "select * from newsletters where newsletter_id=:newsletter_id";
    $params = array('newsletter_id'=>$post);
    $result = DB_Class::query($query,$params);
    $result[0]['date_posted'] = date('Y-m-d',strtotime($result[0]['date_posted']));
    return $result[0];
  }
}
?>

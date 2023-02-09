<?php
if (!isset($_SESSION)) {
session_start();
}
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DB_Class.php');

class Function_Class {


  /*********************************************************
   *                                                       *
   * Reload Page                                           *
   *                                                       *
   *********************************************************/
   public static function reloadPage() {
     header('Location: '.$_SERVER['PHP_SELF']);
     exit;
   }


  /*********************************************************
   *                                                       *
   * Redirect                                              *
   *                                                       *
   *********************************************************/
   public static function redirect($page) {
     header('Location: '.$page);
     exit;
   }


  /*********************************************************
   *                                                       *
   * Sanitize                                              *
   *                                                       *
   *********************************************************/
   public static function sanitize($string) {
      return mysql_escape_string(addslashes(trim($string)));
   }


  /*********************************************************
   *                                                       *
   * stripStuff                                            *
   *                                                       *
   *********************************************************/
   public static function stripStuff($string) {
      return stripslashes(trim($string));
   }




	 public static function buildErrors($errors,$valid) {
    if (count($errors) == 0) { // No errors, return
	    $_SESSION['error'] = '';
	  } elseif (count($errors) == 1) {
	    $_SESSION['error'] = $errors[0];
	  } elseif (count($errors) > 1) {
  	  $_SESSION['error'] = implode('<br />',$errors);
   	}
		// These are things that are valid
		$_SESSION['valid'] = $valid;
  }

	public static function buildMessages($messages) {
    if (count($messages) == 0) { // No messages, return
	    $_SESSION['message'] = '';
	  } elseif (count($messages) == 1) {
	    $_SESSION['message'] = $messages[0];
	  } elseif (count($messages) > 1) {
  	  $_SESSION['message'] = implode('<br />',$messages);
   	}
  }

	public static function trimAll($arr) {
    $titleCaseFields = array('first_name','last_name','name','students');

	  foreach ($arr as $k => $v) {
		  if (!is_array($v)) {
        if (in_array($k,$titleCaseFields)) {
          $arr[$k] = trim(ucwords(strtolower($v)));
        } else {
          $arr[$k] = trim($v);
        }
			} else {
			  // Call trimAll recursively with $v
				Function_Class::trimAll($v);
			}
		}
		return $arr;
	}

  public static function formatField($field) {
    return ucwords(strtolower($field));
  }
}
?>

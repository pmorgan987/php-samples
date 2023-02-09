<?php

class DB_Class {


  /*********************************************************
   *                                                       *
   * query                                                 *
   *                                                       *
   *********************************************************/
   public static function query($query,$params=array(),$mode='ASSOC') {
     // Database credentials
     global $dbname, $dbusername, $dbpassword;
     require_once($_SERVER['DOCUMENT_ROOT'].'/config_db.php');

		 // Try to Create new PDO object
		 try {

		   // Create $db PDO object
		   $db = new PDO('mysql:host=localhost;dbname='.$dbname, $dbusername, $dbpassword);

			 // Set error reporting
		   $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		   // Set up the query in a prepared statement
  		 $res = $db->prepare($query);

		   // Do the binding
		   foreach ($params as $key => $value) {
			   if (is_int($value)) {
				   $res->bindValue(':'.$key,$value,PDO::PARAM_INT);
				 } else {
				   $res->bindValue(':'.$key,$value,PDO::PARAM_STR);
				 }
		   }

			 // Get the info to kick back
			 if ($mode == 'ALL') {
			   $res->setFetchMode(PDO::FETCH_BOTH);
			 } else if ($mode == '') {
			   $res->setFetchMode(PDO::FETCH_NUM);
			 } else {
			   // Get just ASSOC
				 $res->setFetchMode(PDO::FETCH_ASSOC);
			 }

			 // Get the return info
			 $res->execute();
			 if (preg_match('/^select/i',$query)) {
			   $ret = $res->fetchAll();
			 } elseif (preg_match('/^insert/i',$query)) {
			   $ret = $db->lastInsertId();
			 } else {
			   $ret = true;
			 }

		   // Free the connection
       $db = null;

			 return $ret;
		 } catch (PDOException $ex) {
		   echo $ex->getMessage();
		 }
   }


  /*********************************************************
   *                                                       *
   * debug                                                 *
   *                                                       *
   *********************************************************/
   public static function debug($query,$mode='ASSOC') {
     $output = Mysql_Class::query($query,$mode);
     echo '<pre>';
     print_r($output);
     echo '</pre>';
   }
}
?>

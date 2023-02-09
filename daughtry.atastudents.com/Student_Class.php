<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DB_Class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Function_Class.php');
#require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Validate_Class.php');

class Student_Class {

  public static function getStudents($user_id) {
    $query = "select s.* from students as s,users_students as us where us.user_id=:user_id and us.student_id=s.student_id";
    $params = array('user_id'=>$user_id);
    $result = DB_Class::query($query,$params);
    return $result;
	}

  public static function getStudentsForUser($user_id,$arrange_by_keys) {
    $query = "select s.student_id,s.first_name,s.last_name from students as s, users_students as us where s.student_id=us.student_id and us.user_id=:user_id";
    $params = array('user_id'=>$user_id);
    $result = DB_Class::query($query,$params);
    if ($arrange_by_keys) {
      $resWithKeys = array();
      foreach ($result as $r) {
        $key = $r['student_id'];
        unset($r['student_id']);
        $resWithKeys[$key] = $r;
      }
      return $resWithKeys;
    } else {
      return $result;
    }
  }

  public static function getAllStudentsNames() {
    $query = "select student_id,first_name,last_name from students where active='y' order by last_name asc, first_name asc";
    $params = array();
    $result = DB_Class::query($query,$params);
    return $result;
  }

  public static function getStudentsSingleNames($user_id) {
    $query = "select s.student_id,s.first_name,s.last_name from students as s,users_students as us where us.user_id=:user_id and us.student_id=s.student_id";
    $params = array('user_id'=>$user_id);
    $result = DB_Class::query($query,$params);
    $output = array();
    foreach ($result as $res) {
      $output[$res['student_id']] = $res['first_name'].' '.$res['last_name'];
    }
    return $output;
  }

  public static function getStudentProfile($student_id) {

  }

  public static function getNewestStudents($limit) {
    $query = "select * from students where active = 'y' order by date_entered desc limit :limit";
    $params = array('limit'=>$limit);
    $result = DB_Class::query($query,$params);
    return $result;
  }

  public static function addStudent($post) {
    $post = Function_CLass::trimAll($post);
    $birthday = date('Y-m-d',strtotime($post['birthday']));
    $query = "insert into students set first_name=:first_name, last_name=:last_name,
              ata_number=:ata_number, belt_size=:belt_size,
              birthday=:birthday, date_entered=now(), active='y'";
    $params = array(
                'first_name'      => $post['first_name'],
                'last_name'       => $post['last_name'],
                'ata_number'      => $post['ata_number'],
                'belt_size'       => $post['belt_size'],
                'birthday'        => $birthday
              );
    DB_Class::query($query,$params);
    // set message
    $_SESSION['message'] = $post['first_name'].' '.$post['last_name'].' was successfully added';
    Function_Class::redirect('students.php');
  }

  public static function getActiveStudentCount() {
    $query = "select count(*) from students where active='y'";
    $result = DB_Class::query($query,array());
    return $result[0]['count(*)'];
  }

  public static function getStudent($user_id,$student_id) {
    $query = "select s.* from students as s, users_students as us where s.active='y' and s.student_id=:student_id and s.student_id=us.student_id and us.user_id=:user_id";
    $params = array('student_id'=>$student_id,'user_id'=>$user_id);
    $result = DB_Class::query($query,$params);
    if (!isset($result[0])) {
      // Error:  student does not exist or you do not have permission to view
      $_SESSION['error'] = 'This student does not exist or you do not have permission to view the profile';
      Function_Class::redirect('/');
    } else {
      $result = $result[0]; // For simplicity
      return $result;
    }
  }

  public static function deactivateStudent($student_id) {
    $query = "update students set active='n' where student_id=:student_id";
    $params = array('student_id'=>$student_id);
    DB_Class::query($query,$params);
    $messages = array('Student was deleted.');
    Function_Class::buildMessages($messages);
    Function_Class::redirect('students.php');
  }

  public static function getStudentInfo($student_id) {
    $query = "select * from students where student_id=:student_id";
    $params = array('student_id'=>$student_id);
    $result = DB_Class::query($query,$params);
    $result = $result[0];
    $result['birthday'] = date('Y-m-d',strtotime($result['birthday']));
    return $result;
  }

  public static function editStudent($post) {
    $post = Function_CLass::trimAll($post);
    $birthday = date('Y-m-d',strtotime($post['birthday']));
    $query = "update students set first_name=:first_name, last_name=:last_name,
              ata_number=:ata_number, belt_size=:belt_size,
              birthday=:birthday, active='y' where student_id=:student_id";
    $params = array(
                'first_name'      => $post['first_name'],
                'last_name'       => $post['last_name'],
                'ata_number'      => $post['ata_number'],
                'belt_size'       => $post['belt_size'],
                'birthday'        => $birthday,
                'student_id'      => $post['student_id']
              );
    DB_Class::query($query,$params);
    // set message
    $_SESSION['message'] = $post['first_name'].' '.$post['last_name'].' was successfully updated';
    Function_Class::redirect('students.php');
  }
}
?>

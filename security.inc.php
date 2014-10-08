<?php
class security {

  public static function generateSalt($string) {
  
    return hash('sha256', $string . microtime(TRUE));
  
  }

  public static function hash($string, $salt) {
  
    return hash('sha256', $salt . $string);
  
  }
  
  public static function valid($username, $password) {
  
    $db = new DB('home');
  
    $username = $db->dbClean($username);
  
    $user = $db->select('user', "username = '$username'");
    
    if (!is_array($user)) return FALSE;
    
    $user = $user[0];
  
    return self::hash($password, $user['salt']) === $user['password'];
  
  }

}
?>
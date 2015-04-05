<?php


require_once 'debug.php';
class User{
  static function getUser(){
    if(isset($_SESSION["USER"])){
      return $_SESSION["USER"];
    }
    else{
      return null;
    }
  }
  static function setUser($username, $password){
    $userdata = User::getUserData($username, $password);
    $_SESSION["USER"] = new User($username, $password, $userdata);
    return $_SESSION["USER"];
  }
  private function __construct($username, $password, $userdata = null){
    Debug::message("creating user with $username, $password");

    $this->username = $username;
    $this->password = $password;
    if($userdata == null){
      $userdata = User::getUserData($username, $password);
    }
    $this->userdata = $userdata;
    $this->storeUser();
  }
  static function getUserData($username, $password){
    $user = unserialize((file_get_contents("userdata/$username.txt")));
    if($user && isset($user->userdata)){
      return $user->userdata;
    }
    else{
      return [];
    }
  }
  static function retrieveUser($username, $password){
    if(file_exists("userdata/$username.txt")){
      return unserialize((file_get_contents("userdata/$username.txt")));
    }
  }
  function storeUser(){
    $dir = "userdata";
    if ( !file_exists($dir) ) {
      mkdir ($dir, 0744);
    }
    file_put_contents("$dir/$this->username.txt",serialize($this));
  }
}
?>

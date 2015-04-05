<?php

error_reporting(-1);
ini_set('display_errors', 'On');

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
    $userdata = getUserData($username, $password);
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
  }
  static function getUserData($username, $password){
    $user = unserialize((file_get_contents("$username.txt")));
    if($user && isset($user->userdata)){
      return $user->userdata;
    }
    else{
      return [];
    }
  }
  static function storeUserData(){
    file_put_contents("$username.txt",serialize($this));
  }
}
?>

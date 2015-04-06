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
    $_SESSION["USER"] = new User($username, $password);
    return $_SESSION["USER"];
  }
  static function unsetUser(){
    unset($_SESSION["USER"]);
  }
  private function __construct($username, $password, $userdata = null){
    Debug::message("creating user with $username, $password");

    $this->username = $username;
    $this->password = $password;
    if($userdata == null){
      $userdata = $this->getUserData();
    }
    $this->userdata = $userdata;
    $this->storeUser();
  }
  function addUserData($gpa){
    $data = $this->getUserData();
    $data[$gpa->id] = $gpa;
    $this->setUserData($data);
  }
  function removeUserData($id){
    $data = $this->getUserData();
    unset($data[$id]);
    $this->setUserData($data);
  }
  function clearUserData(){
    $this->setUserData([]);
  }
  function getUserData(){
    $user = User::retrieveUser($this->username,$this->password);
    if($user && isset($user->userdata)){
      return $user->userdata;
    }
    else{
      return [];
    }
  }
  function setUserData($data){
    $this->userdata = $data;
    $this->storeUser();
  }
  static function retrieveUser($username, $password){
    if(file_exists("userdata/$username.txt")){
      return unserialize((file_get_contents("userdata/$username.txt")));
    }
    else{
      return null;
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

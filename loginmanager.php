<?php

require_once 'debug.php';
require_once 'user.php';

class LoginManager{
  //returns user info if previous user logged in, null otherwise
  static function preUserLoginInfo(){
    Debug::message("checking for previous user's login info");
    if(isset($_POST["OLD_USERNAME"]) && isset($_POST["OLD_PASSWORD"])){
      $user = $_POST["OLD_USERNAME"]; $pass = $_POST["OLD_PASSWORD"];
      $userCheck = LoginManager::checkUser($user);
      $passCheck = LoginManager::checkPass($user, $pass);
      if($userCheck && $passCheck){
        return array("name" => $user, "pass" => $pass);
      }
    }
    return null;
  }
  static function newUserLoginInfo(){
    Debug::message("checking for new user's login info");
    if(isset($_POST["NEW_USERNAME"]) && isset($_POST["NEW_PASSWORD"])){
      $user = $_POST["NEW_USERNAME"]; $pass = $_POST["NEW_PASSWORD"];
      if((!LoginManager::checkUser($user))
          && LoginManager::validatePassword($pass)
          && LoginManager::validateUsername($user)){
        return array("name" => $_POST["NEW_USERNAME"], "pass" => $_POST["NEW_PASSWORD"]);
      }
    }
    else{
      return null;
    }
  }
  static function validateUsername($user){
    return preg_match("/[a-z0-9]{3,16}/", $user);
  }
  static function validatePassword($pass){
    return preg_match("/[a-z0-9]{3,16}/", $pass);
  }

  static function checkUser($user){
    return (file_exists("userdata/$user.txt"));
  }
  static function checkPass($username, $pass){
    if($user = User::retrieveUser($username, $pass)){
      if($user->password == $pass){
        return true;
      }
    }
    return false;
  }
  static function requestLogin(){
    //if the user hasn't logged in, force them to log in.
    $document = new DOMDocument();
    $document->loadHTMLFile("login.html");
    echo $document->saveHTML();
  }
  static function login(){
    if($user = LoginManager::preUserLoginInfo()){
      Debug::message("set previous username and password");
      User::setUser($user['name'], $user['pass']);
    }
    else if($user = LoginManager::newUserLoginInfo()){
      Debug::message("set new username and password");
      User::setUser($user['name'], $user['pass']);
    }
    if(!is_null(User::getUser())){
      return true;
    }
    else{
      LoginManager::requestLogin();
      return false;
    }
  }
  static function logout(){
    User::unsetUser();
  }
}
?>

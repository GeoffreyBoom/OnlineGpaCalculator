<?php

require_once 'debug.php';
require_once 'user.php';

class LoginManager{
  static $loginPage; 
  //returns user info if previous user logged in, null otherwise
  static function preUserLoginInfo(){
    Debug::message("checking for previous user's login info");
    if(isset($_POST["OLD_USERNAME"]) && isset($_POST["OLD_PASSWORD"])){
      $user = $_POST["OLD_USERNAME"]; $pass = $_POST["OLD_PASSWORD"];
      $userCheck = LoginManager::checkUser($user);
      $passCheck = LoginManager::checkPass($user, $pass);
      $validity = LoginManager::validatePassword($pass) && LoginManager::validateUsername($user);
      if($userCheck && $passCheck && $validity){
        return array("name" => $user, "pass" => $pass);
      }
      else if(!$userCheck || !$passCheck){
        LoginManager::new_error("Username does not exist or Password incorrect.");
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
      else{
        if(LoginManager::checkUser($user)){
          LoginManager::new_error("Username already exists");
        }
      }
    }
    else{
      return null;
    }
  }
  static function validateUsername($user){
    if($match = preg_match("/[a-z0-9]{3,16}/", $user))
      return $match;
    else{
      LoginManager::new_error("Username not valid. must be 3-16 alphanumeric characters.");
      return null;
    }
  }
  static function validatePassword($pass){
    if($match = preg_match("/[a-z0-9]{3,16}/", $pass))
      return $match;
    else{
      LoginManager::new_error("Password not valid. must be 3-16 alphanumeric characters.");
      return null;
    }
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
  static function getLoginPage(){
    if(isset(self::$loginPage)){
      return self::$loginPage;
    }
    else{
      self::$loginPage = new DOMDocument();
      self::$loginPage->loadHTMLFile("login.html");
      return self::$loginPage;
    }
  }
  static function requestLogin(){
    //if the user hasn't logged in, force them to log in.
    $document = LoginManager::getLoginPage();
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
  static function new_error($message){
    $doc = LoginManager::getLoginPage();
    $error = $doc->getElementById("login_error");
    $message = $doc->createTextNode($message);
    $error->appendChild($message);
  }
}
?>

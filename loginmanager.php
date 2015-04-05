<?php
error_reporting(-1);
ini_set('display_errors', 'On');

require_once 'debug.php';

class LoginManager{
  //returns user info if previous user logged in, null otherwise
  static function preUserLoginInfo(){
    Debug::message("checking for previous user's login info");
    if(isset($_POST["OLD_USERNAME"]) && isset($_POST["OLD_PASSWORD"])){
      return ["name" => $_POST["OLD_USERNAME"], "pass" => $_POST["OLD_PASSWORD"]];
    }
    else{
      return null;
    }
  }
  static function newUserLoginInfo(){
    Debug::message("checking for new user's login info");
    if(isset($_POST["NEW_USERNAME"]) && isset($_POST["NEW_PASSWORD"])){
      return ["name" => $_POST["NEW_USERNAME"], "pass" => $_POST["NEW_PASSWORD"]];
    }
    else{
      return null;
    }
  }
  static function requestLogin(){
    //if the user hasn't logged in, force them to log in.
    $document = new DOMDocument();
    $document->loadHTMLFile("login.html");
    echo $document->saveHTML();
  }
}
?>

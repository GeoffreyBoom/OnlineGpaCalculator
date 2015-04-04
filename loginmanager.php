<?php
error_reporting(-1);
ini_set('display_errors', 'On');
class LoginManager{
  //a function to return the current user
  static function getCurrentUser(){
    if(test::$testing) print("looking for a user<br>");
    //if the user already exists in the session data, return the user
    if(isset($_SESSION["USER"]) && $_SESSION["USER"] != null){
      if(test::$testing) print("found a user in the session data<br>");
      return $_SESSION["USER"];
    }
    else{
      if(test::$testing)print("didn't find a user in session data<br>");
      //if the user has just logged in
      if(LoginManager::catchLogin()){
        if(test::$testing) print("found login information<br>");
        return LoginManager::catchLogin();
      }
      //return null otherwise
      else{
        if(test::$testing) print("found no user data, returning null<br>");
        return null;
      }
    }
  }
  static function catchLogin(){
    if(test::$testing) print("looking for a login<br>");
    //if the user has returned from a login page
    //and is a previous user, return their data from
    //the file system
    if(isset($_POST["OLD_USERNAME"]) && isset($_POST["OLD_PASSWORD"])){
      if(test::$testing) print("user logged in with previous login<br>");
      $_SESSION["USER"] = LoginManager::getOldUser($username, $password);
      if(test::$testing) print("user found<br>");
      return $_SESSION["USER"]; 
    }
    //if the user has returned from the login page
    //and is a new user, create new data for them
    else if(isset($_POST["NEW_USERNAME"]) && isset($_POST["NEW_PASSWORD"])){
      if(test::$testing) print("user logged in with new login<br>");
      return $_SESSION["USER"] = LoginManager::createNewUser($username, $password);
    }
    else{
      if(test::$testing) print("there is no login info<br>");
      return null;
    }

  }
  static function requestLogin(){
    //if the user hasn't logged in, force them to log in.
    $document = new DOMDocument();
    $document->loadHTMLFile("login.html");
  }
}
?>

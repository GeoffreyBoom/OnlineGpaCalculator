<?php

require 'gpa.php';
require 'calculator.php';
require 'document.php';

session_start();

LoginManager::catchLogin();
function start(){
  if(test::$testing) print("starting program<br>");
  $user = LoginManager::getCurrentUser(); 
  var_dump($user);
  if(!$user){
    if(test::$testing) print("no login found, requesting new login<br>");
    LoginManager::requestLogin();
  }
  else{
    if (test::$testing) print("user found, displaying Gpa Calculator<br>");
    Database::ensureGpaArray();
    if($gpa = document::handleInput()){
      Database::addToGPA($gpa);
    }
    document::setTotal(calculator::calculateGpa());
    document::addAllGPA();
    document::displayDocument(document::loadDocument());
  }
}

class test{
  static $testing = true;
}

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
    echo $document->saveHTML();
  }
  static function getOldUser($username, $password){
    print("hello");
    $user =  User($username, $password);
    return $user;
  }
  static function getNewUser($username, $password){
    return User($username, $password);
  }
}

class User{
  function __construct($username, $password, $userdata = null){
    if(test::$testing) print("creating user with $username, $password");
    $this->username = $username;
    $this->password = $password;
    if($userdata == null){
      $userdata = User::getUserData($username, $password);
    }
    $this->userdata = $userdata;
  }
}


class Database{
  static function addToGPA($gpa){
    $_SESSION["gpa_data"][$gpa->id] = $gpa;
  }
  static function removeGpa($id){
    unset($_SESSION["gpa_data"][$id]);
  }
  static function getGpaArraySize(){
    return sizeof(Database::getGpaArray());
  }
  static function getGpaArray(){
    return $_SESSION["gpa_data"];
  }
  static function resetGpaArray(){
    $_SESSION["gpa_data"] = array();
  }
  static function ensureGpaArray(){
    if(!isset($_SESSION["gpa_data"])){
      $_SESSION["gpa_data"] = array();
    }
  }    
}




start();



?>


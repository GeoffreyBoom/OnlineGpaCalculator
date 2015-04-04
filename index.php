<?php

require 'gpa.php';
require 'calculator.php';
require 'document.php';

session_start();
function start(){
  $login = LoginManager::getCurrentUser();
  if(!$login || !$LoginManager::catchLogin()){
    LoginManager::requestLogin();
  }
  else{
    Database::ensureGpaArray();
    if($gpa = document::handleInput()){
      Database::addToGPA($gpa);
    }
    document::setTotal(calculator::calculateGpa());
    document::addAllGPA();
    document::displayDocument(document::loadDocument());
  }
}

class LoginManager{
  //a function to return the current user
  static function getCurrentUser(){
    //if the user already exists in the session data, return the user
    if(isset($_SESSION["USER"])){
      return $_SESSION["USER"];
    }
    //return null otherwise
    else{
      return null;
    }
  }
  static function catchLogin(){
    //if the user has returned from a login page
    //and is a previous user, return their data from
    //the file system
    if(isset($_POST["OLD_USERNAME"]) && isset($_POST["OLD_PASSWORD"])){
      $_SESSION["USER"] = LoginManager::getOldUser($username, $password);
      return getCurrentUser();
    }
    //if the user has returned from the login page
    //and is a new user, create new data for them
    else if(isset($_POST["NEW_USERNAME"]) && isset($_POST["NEW_PASSWORD"])){
      $_SESSION["USER"] = LoginManager::createNewUser($username, $password);
      return getCurrentUser();
    }

  }
  static function requestLogin(){
    //if the user hasn't logged in, force them to log in.
    $document = new DOMDocument();
    $document->loadHTMLFile("login.html");
    echo $document->saveHTML();
  }
  static function getOldUser($username, $password){
    return User($username, $password);
  }
  static function getNewUser($username, $password){
    return User($username, $password);
  }
}

class User{
  function __construct($username, $password, $userdata = null){
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


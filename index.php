<?php

require_once 'gpa.php';
require_once 'calculator.php';
require_once 'document.php';
require_once 'user.php';
require_once 'debug.php';
require_once 'loginmanager.php';

session_start();
function start(){
  if(isset($_GET["logout"])){
    var_dump($_GET["logout"]);
    if($_GET["logout"] == "true"){
      LoginManager::logout();
      header('Location: http://localhost/website/' );
    }
  }
  Debug::message("starting program");
  if(LoginManager::login()){
    Debug::message("user found, displaying Gpa Calculator");
    Database::ensureGpaArray();
    if($gpa = document::handleInput()){
      Database::addToGPA($gpa);
    }
    document::setTotal(calculator::calculateGpa());
    document::addAllGPA();
    document::displayDocument(document::loadDocument());
  }
}


class Database{
  static function addToGPA($gpa){
    User::getUser()->addUserData($gpa);
  }
  static function removeGpa($id){
    User::getUser()->removeUserData($id);
  }
  static function getGpaArraySize(){
    return sizeof(Database::getGpaArray());
  }
  static function getGpaArray(){
    return User::getUser()->userdata;
  }
  static function resetGpaArray(){
    User::getUser()->clearUserData();
  }
  static function ensureGpaArray(){
  }    
}
start();
?>


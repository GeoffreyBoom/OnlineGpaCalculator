<?php

require_once 'gpa.php';
require_once 'calculator.php';
require_once 'document.php';
require_once 'user.php';
require_once 'debug.php';
require_once 'login.php';

session_start();

function start(){
  if(test::$testing) print("starting program<br>");
  if(User::getUser()){
    if (test::$testing) print("user found, displaying Gpa Calculator<br>");
    Debug::message(User::getUser()->username);
  }
  else{
    loginStart();
  }
  Database::ensureGpaArray();
  if($gpa = document::handleInput()){
    Database::addToGPA($gpa);
  }
  document::setTotal(calculator::calculateGpa());
  document::addAllGPA();
  document::displayDocument(document::loadDocument());
}

class test{
  static $testing = true;
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


<?php

require 'gpa.php';
require 'calculator.php';
require 'document.php';

session_start();

function start(){
  if(test::$testing) print("starting program<br>");
  if (test::$testing) print("user found, displaying Gpa Calculator<br>");
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


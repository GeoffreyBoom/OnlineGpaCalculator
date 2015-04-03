<?php

session_start();
function start(){
  $login = LoginManager::currentUser();
  if(!$login){
    LoginManager::requireLogin();
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
  function getUser(){
    if(isset($_SESSION["USER"])){
      return $_SESSION["USER"];
    }
    else{
      return null;
    }
  }

  function requireLogin(){
    if(isset($POST["OLD_USERNAME"])){
      if(isset($POST["OLD_PASSWORD"])){
        $
      }
    }
  }
}

class User{
  function __construct($username, $password, $userdata = null){
    $this->username = $username;
    $this->password = $password;
    if($userdata == null){
      $userdata = User::getUserData($username, $password);
    }
    $this->userdata = $userdata
  }
}

class GPA{
  function __construct($course, $credit, $grade){
    $this->id = Database::getGpaArraySize();
    $this->course = $course;
    $this->credit = $credit;
    $this->grade  = $grade;
  }
  function toString(){
    return "$this->course, $this->credit, $this->grade";
  }
  function toNode(){
    $doc = document::loadDocument();
    $node = $doc->createElement("tr"); $node->setAttribute("class", "gpadata"); $node->setAttribute("id", "gpa$this->id");
    $course = $doc->createElement("td", "$this->course"); $course->setAttribute("class", "course"); 
    $credit = $doc->createElement("td", "$this->credit"); $credit->setAttribute("class", "credit"); 
    $grade  = $doc->createElement("td", "$this->grade" ); $grade ->setAttribute("class", "grade" );
    $buttonpan = $doc->createElement("td");
    $button = $doc->createElement("input"); 
    $button->setAttribute("type",  "checkbox"); $button->setAttribute("name", "checkbox");
    $button->setAttribute("value", "$this->id");   
    $buttonpan->appendChild($button);
    $node->appendChild($course); $node->appendChild($credit); $node->appendChild($grade);
    $node->appendChild($buttonpan);
    return $node;
  }
  static function verifyCourse($course){
    if(strlen($course) < 15){
      document::courseError(false);
      return $course;
    }
    else{
      document::courseError(true);
      return false;
    }
  }

  static function verifyCredit($credit){
    if( (int)$credit > 0 && (int)$credit < 10){
      document::creditError(false);
      return (int) $credit;
    }
    else{
      document::creditError(true);
      return false;
    }
  }
  
  static function verifyGrade($grade){
    $potentialGrades = "/^(([a-d]|[A-D])(\+|\-)?|F|f)$/";
    if(preg_match($potentialGrades, $grade, $matches)){
      document::gradeError(false);
      return $matches[0];
    }
    else{
      document::gradeError(true);
      return false;
    }
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

class calculator{
  static function calculateGpa(){
    $gpaArray = Database::getGpaArray();
    if(!$gpaArray){
      return 0;
    }
    $sumGrades = 0;
    $sumCredits = 0;
    foreach($gpaArray as $gpa){
      if($gpa){
        $sumGrades += $gpa->credit * calculator::getGpaByGrade($gpa->grade);
        $sumCredits+= $gpa->credit;
      }
    }
    return $sumGrades / $sumCredits;
  }
  static function getGpaByGrade($grade){
    switch($grade){
      case"a+":
      case"A+":
        return 4.3;
        break;
      case"a":
      case"A":
        return 4.0;
        break;
      case"a-":
      case"A-":
        return 3.7;
        break;   
      case"b+":
      case"B+":
        return 3.3;
        break;
      case"b":
      case"B":
        return 3.0;
        break;
      case"b-":
      case"B-":
        return 2.7;
        break;   
      case"c+":
      case"C+":
        return 2.3;
        break;
      case"c":
      case"C":
        return 2.0;
        break;
      case"c-":
      case"C-":
        return 1.7;
        break;   
      case"d+":
      case"D+":
        return 1.3;
        break;
      case"d":
      case"D":
        return 1.0;
        break;
      case"d-":
      case"D-":
        return 0.7;
        break;
      case"F":
      case"f":
        return 0;
        break;
    }
  }
}

class document{
  static $document;
  static function loadDocument(){
    if(isset(self::$document)){
      return self::$document;
    }
    else{
      self::$document = new DOMDocument();
      self::$document->loadHTMLFile("gpapage.html");
      return self::$document;
    }
  }
 
  static function addAllGPA(){
    $data = Database::getGpaArray();
    foreach($data as $gpa){
      document::addToGPA($gpa->toNode());
    }
    document::addToGPA(document::createRemoveButton());
  }
  static function createRemoveButton(){
    $doc = document::loadDocument();
    $removePanel  = $doc->createElement("tr");
    $removeAll    = $doc->createElement("td", "Remove All");
    $blank1       = $doc->createElement("td");
    $blank2       = $doc->createElement("td");
    $buttontd     = $doc->createElement("td");
    $button       = $doc->createElement("input"); 
    $button->setAttribute("type",  "checkbox"); 
    $button->setAttribute("value", "all"); $button->setAttribute("name", "checkbox");
    $buttontd->appendChild($button);
    $removePanel->appendChild($removeAll);
    $removePanel->appendChild($blank1);
    $removePanel->appendChild($blank2);
    $removePanel->appendChild($buttontd);
    return $removePanel;
  }

  static function displayDocument($document){
    echo self::$document->saveHTML();
  }
  
  static function addToGPA($gpa){
    $doc = document::loadDocument();
    $gpaTable = $doc->getElementById("gpadata");
    $gpaTable->appendChild($gpa);
  }
  static function setTotal($gpa){
    $doc = document::loadDocument();
    $gpaTotal = $doc->getElementById("gpafield");
    $gpaTotal->appendChild($doc->createElement("p", "$gpa"));
  }
  static function handleInput(){
    $gpa = null;
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if(isset($_POST["Add"])){
        $course = $_POST["course"]; $credit = $_POST["credits"]; $grade = $_POST["grade"];
        if($course = GPA::verifyCourse($course)){
          if($credit = GPA::verifyCredit($credit)){
            if($grade = GPA::verifyGrade($grade)){
              $gpa = new GPA($course, $credit, $grade);
            }
          }
        }
      }
      else if(isset($_POST["Remove"])){
        foreach($_POST as $type=>$box){
          if($type=="checkbox"){
            if($box == "all"){
              Database::resetGpaArray();
            }
            else{
              Database::removeGpa((int)$box);
            }
          }
        }
      }
    }
    if($gpa){
      Database::addToGPA($gpa);
    }
  }
  static function gradeError($display){
    //<td class="error"><img src="img/dogeintensifies.gif">hello</td>    
    if($display){
      $error = document::createError("Grades must be between A-D or F");
    }
    else{
      $error = document::loadDocument()->createElement("td");
    }
    $field = document::loadDocument()->getElementById("errorfield");
    $field->appendChild($error);
  }
  static function courseError($display){

    if($display){
      $error = document::createError("Course name is too long");
    }
    else{
      $error = document::loadDocument()->createElement("td");
    }
    $field = document::loadDocument()->getElementById("errorfield");
    $field->appendChild($error);

  }
  static function creditError($display){
    if($display){
      $error = document::createError("Credits must be between 0-9");
    }
    else{
      $error = document::loadDocument()->createElement("td");
    }
    $field = document::loadDocument()->getElementById("errorfield");
    $field->appendChild($error);
  }
  static function createError($message){
    $doc = document::loadDocument();
    $column = $doc->createElement("td");    $column->setAttribute("class", "error");
    $doge   = $doc->createElement("img");   $doge->setAttribute("src", "img/dogeintensifies.gif"); 
    $paragraph = $doc->createElement("p", $message);
    $column->appendChild($doge);
    $column->appendChild($paragraph);
    return $column;
  }
}


start();



?>


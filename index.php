<?php

function start(){
  Database::ensureGpaArray();
  if($gpa = document::handleInput()){
    Database::addToGPA($gpa);
  }
  document::addAllGPA();
  document::displayDocument(document::loadDocument());
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
    $button->setAttribute("type",  "checkbox"); 
    $button->setAttribute("value", "remove"); $button->setAttribute("name", "checkbox$this->id");
    $buttonpan->appendChild($button);
    $node->appendChild($course); $node->appendChild($credit); $node->appendChild($grade);
    $node->appendChild($buttonpan);
    return $node;
  }

}

class Database{
  static function addToGPA($gpa){
    $_SESSION["gpa_data"][] = $gpa;
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
    session_start();
    if(!isset($_SESSION["gpa_data"])){
      $_SESSION["gpa_data"] = array();
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
    document::addToGPA(document::removeAllButton());
  }
  static function removeAllButton(){
    $doc = document::loadDocument();
    $removePanel  = $doc->createElement("tr");
    $removeAll    = $doc->createElement("td", "Remove All");
    $blank1       = $doc->createElement("td");
    $blank2       = $doc->createElement("td");
    $buttontd     = $doc->createElement("td");
    $button       = $doc->createElement("input"); 
    $button->setAttribute("type",  "checkbox"); 
    $button->setAttribute("value", "remove"); $button->setAttribute("name", "checkboxALL");
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
  
  static function handleInput(){
    $gpa = null;
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if(isset($_POST["Add"])){
        $course = $_POST["course"]; $credit = $_POST["credits"]; $grade = $_POST["grade"];
        $_POST = array();
        if($course && $credit && $grade){
          $gpa = new GPA($course, $credit, $grade);
        }
      }
      else if(isset($_POST["Remove"])){
        var_dump($_POST);
        if(isset($_POST["checkboxALL"])){
          Database::resetGpaArray();
        }
      }
    }
    return $gpa;
  }
}


start();



?>
<script>
function GPA(course, credit, grade){ 
  this.toString = function(){
    return this.course + "," + this.credit + "," + this.grade
  }
  this.toNode = function(){
    var node = document.createElement("tr");
    node.id = "gpa" + this.id;
    node.className = "gpadata";

    var course = document.createElement("td");
    var credit = document.createElement("td");
    var grade  = document.createElement("td");

    var button = document.createElement("input");
    button.type="button";
    button.value="remove";
    button.name = this.id;
    button.onclick= function() {removeGpa(this.name)};
    
    course.className = "course";
    credit.className = "credit";
    grade.className  = "grade";

    course.innerHTML = this.course;
    credit.innerHTML = this.credit;
    grade.innerHTML  = this.grade;

    node.appendChild(course);
    node.appendChild(credit);
    node.appendChild(grade);
    node.appendChild(button);

    return node;
  }
  this.parse = function(gpaNode){
    var coursevar = gpaNode.getElementsByClassName("course")[0].innerHTML;
    var creditvar = parseInt(gpaNode.getElementsByClassName("credit")[0].innerHTML);
    var gradevar = gpaNode.getElementsByClassName("grade")[0].innerHTML;
    return this.populate(coursevar,creditvar,gradevar);
  }
  this.populate = function(coursevar, creditvar, gradevar){ 
    if(arguments.length == 3){
      if(!this.verifyCourse(coursevar)){
        return false;
      }
      if(!this.verifyCredit(creditvar)){
        return false;
      }
      if(!this.verifyGrade(gradevar)){
        return false;
      }
      this.course=coursevar;
      this.credit=creditvar;
      this.grade=gradevar;
      return true;
    }
  }
  this.verifyCourse = function(course){
    if(course.length < 15){
      return course;
    }
    else{
      confirm(course + ": is too long");
      return false;
    }
  }

  this.verifyCredit = function(credit){
    if(credit > 0 && credit < 10){
      return credit;
    }
    else{
      confirm(credit + ": is not valid");
      return false;
    }
  }
  
  this.verifyGrade = function(grade){
    var potentialGrades = /^(([a-d]|[A-D])(\+|\-)?|F|f)$/;
    if(potentialGrades.test(grade)){
      return potentialGrades.exec(grade);
    }
    else{
      confirm(grade + ": is not valid");
      return false;
    }
  }
  this.validate = function(){
    return (this.verifyCourse(this.course) && this.verifyCredit(this.credit) && this.verifyGrade(this.grade));
  }
  if(arguments.length == 3){
    this.id = GPA.numGPA++;
    this.populate(course,credit,grade);
  }
}

GPA.numGPA = 0;
  
//retrieves GPA data and adds it to the Data Field
function addGpaData(){
  if(gpaData = getNewGpaData()){
    insertGpaData(gpaData.toNode());
  }
}

//retrieves GPA data from the gpaForm and returns a GPA object
function getNewGpaData(){
  var form = getFormField();
  var gpaData= new GPA(
        form.course.value, 
        form.credits.value, 
        form.grade.value);
  if(!gpaData.validate()){
    alert("You choose No."); 
    return false;
  }
  form.course.value = form.credits.value = form.grade.value = "";
  return gpaData;
}

//appends a dom object representing the gpa object as a child to the DataField
function insertGpaData(gpaObject){
  var gpaData = getDataField();
  gpadata.appendChild(gpaObject);
  displayGpa();
}

//Gets current data from the Document, calculates the gpa, and displays it.
function displayGpa(){
  var currdata = (getCurrentGpaData());
  var gpa = calculateGpa(currdata);  
  getDisplayField().innerHTML = gpa.toString().slice(0,4);
}

//gets the current GPA data and returns it as an array of GPA objects
function getCurrentGpaData(){
  var gpaData = [];
  var columns = [];
  var rows = getDataField().getElementsByClassName("gpadata");
  for(var i = 0; i < rows.length; i++){
    row = rows[i];
    if (row != ""){
      var data = new GPA();
      data.parse(row)
      gpaData.push(data);
    }
  }
  return gpaData;
}

//takes in GPA objects and calculates the gpa.
function calculateGpa(gpaData){
  var gradepointsum = 0;
  var creditsum = 0;
  for(var data = 0; data < gpaData.length; data++){
    gradepointsum += gpaData[data].credit * getGradeValue(gpaData[data].grade);
    creditsum += gpaData[data].credit;
  }
  if(creditsum != 0){
    return gradepointsum / creditsum;
  }
  else{
    return 0;
  }
}

//removes all GPA data and resets the display field
function removeAll(){
  var data = getDataField().getElementsByClassName("gpadata"); 
  var num = data.length;
  for(var i = 0; i<num; i++){
    getDataField().removeChild(data[0]);
  }
  displayGpa();
}

function removeGpa(id){
  var gpaToRemove = document.getElementById("gpa" + id);
  getDataField().removeChild(gpaToRemove);
  displayGpa();
}

function getDataField(){
  return document.getElementById("gpadata");
}
function getDisplayField(){
  return document.getElementById("gpafield");
}
function getFormField(){
  return document.getElementById("gpaform");
}

function getGradeValue(grade){
  switch(grade){
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
</script>
